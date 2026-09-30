<?php

namespace App\Services;

use App\Engine\Battle\Enums\BattleStatus;
use App\Engine\Magic\Spell;
use App\Engine\Magic\SpellRegistry;
use App\Exceptions\Exception;
use App\Models\Battle;
use App\Models\User;
use App\Models\UserItem;
use App\Models\UserSlot;
use Illuminate\Support\Facades\DB;

class MagicService
{
	public static function canUse(UserItem $item): bool
	{
		return in_array($item->type, [12, 13, 14], true)
			&& SpellRegistry::find($item->code) !== null;
	}

	public static function useMagic(User $user, int $itemId, string $targetName, ?int $battleId = null, ?int $round = null): string
	{
		$targetName = trim($targetName);

		if ($targetName === '') {
			throw new Exception('Укажите имя персонажа');
		}

		$targetBefore = User::query()
			->where(ctype_digit($targetName) ? 'id' : 'name', $targetName)
			->first(['id', 'battle_id']);

		if (!$targetBefore) {
			throw new Exception('Персонаж не найден');
		}

		$targetId = $targetBefore->id;
		$targetBattleId = $targetBefore->battle_id;

		return DB::transaction(function () use ($user, $itemId, $targetId, $targetBattleId, $battleId, $round) {
			// Тот же порядок блокировок, что при обработке ходов: сначала бой, затем игроки.
			$battles = Battle::query()
				->whereIn('id', array_filter([$battleId, $targetBattleId]))
				->orderBy('id')
				->lockForUpdate()
				->get()
				->keyBy('id');
			$battle = $battles->get($battleId);
			$users = User::query()
				->whereIn('id', [$user->id, $targetId])
				->orderBy('id')
				->lockForUpdate()
				->get()
				->keyBy('id');

			$caster = $users->get($user->id);
			$target = $users->get($targetId);

			if (!$caster || !$target) {
				throw new Exception('Персонаж не найден');
			}

			if ($caster->battle_id !== $battleId || $target->battle_id !== $targetBattleId) {
				throw new Exception('Состояние боя изменилось. Обновите данные');
			}

			$target->setRelation('battle', $battles->get($targetBattleId));

			if (!$caster->isFree() || $caster->prison?->isFuture() || $caster->blocked_at) {
				throw new Exception('Сейчас вы не можете использовать магию');
			}

			if ($target->isBot()) {
				throw new Exception('Использование свитков на ботов запрещено');
			}

			if (!$target->is($caster) && (!$target->online || !$target->isOnline())) {
				throw new Exception('Персонаж не в игре');
			}

			if (!$target->isFree()) {
				throw new Exception('Персонаж занят работой');
			}

			$slots = UserSlot::query()
				->whereIn('user_id', $users->keys())
				->orderBy('user_id')
				->lockForUpdate()
				->get();

			foreach ($slots as $slot) {
				$users->get($slot->user_id)->setRelation('slots', $slot);
				$slot->clearCache();
			}

			$item = $caster->items()->lockForUpdate()->find($itemId);

			if (!$item || !self::canUse($item)) {
				throw new Exception('Магический предмет не найден или заклинание не поддерживается');
			}

			$caster->calculate(false);

			if (!$target->is($caster)) {
				$target->calculate(false);
			}

			if (!InventoryService::isAllowOnset($item, $caster)) {
				throw new Exception('Предмет недоступен, исчерпан или не выполнены требования к его использованию');
			}

			$spell = SpellRegistry::find($item->code);

			if (!$spell) {
				throw new Exception('Заклинание не поддерживается');
			}

			if ($target->battle_id !== $battleId && (!$spell->canJoinBattle() || $caster->battle_id)) {
				throw new Exception('Персонажи находятся в разных боях');
			}

			if (
				!$target->is($caster) && !$target->isAdmin()
				&& !$spell->ignoresMagicProtection()
				&& $target->magic_protection?->isFuture()
			) {
				throw new Exception('Персонаж находится под защитой от магии');
			}

			if ($battleId) {
				self::validateBattle($battle, $caster, $target, $item, $spell, $round);
			}

			if ($caster->energy_now < $spell->manaCost()) {
				throw new Exception('У вас не хватает маны');
			}

			$caster->energy_now -= $spell->manaCost();
			$message = $spell->cast($caster, $target, $item);
			$caster->online = now();
			$caster->save();

			if (!$target->is($caster)) {
				$target->save();
			}

			self::consume($caster, $item);

			// После снятия экипировки или исчезновения свитка пересчитываем пределы HP и MP.
			foreach ($users as $participant) {
				$participant->fresh()->calculate();
			}

			$redirect = $target->battle_id && $target->battle_id !== $targetBattleId ? route('battle') : null;

			if ($target->prison?->isFuture()) {
				$redirect = route('map');
			}

			ChatService::sendSystemMessage($message, [$target], $redirect);

			DB::afterCommit(function () use ($slots) {
				foreach ($slots as $slot) {
					$slot->clearCache();
				}
			});

			return $message;
		}, 3);
	}

	private static function validateBattle(?Battle $battle, User $caster, User $target, UserItem $item, Spell $spell, ?int $round): void
	{
		if (!$battle || $battle->status !== BattleStatus::ACTIVE || $battle->result !== null) {
			throw new Exception('Бой уже завершён');
		}

		if ($battle->round !== $round || $battle->round_at->addSeconds($battle->timeout)->isPast()) {
			throw new Exception('Раунд изменился или время хода истекло');
		}

		$members = $battle->members()->get()->keyBy('user_id');
		$fighter = $members->get($caster->id);
		$enemy = $members->get($target->id);

		if (
			!$fighter || !$enemy
			|| $fighter->died_at || $enemy->died_at
			|| $caster->hp_now <= 0 || $target->hp_now <= 0
		) {
			throw new Exception('Использовать магию могут только живые участники боя');
		}

		if ($fighter->finished_at) {
			throw new Exception('Вы уже завершили ход');
		}

		if ($spell->isOffensive() && !$caster->is($target) && $fighter->side === $enemy->side) {
			throw new Exception('Нельзя атаковать союзника');
		}

		$slots = $caster->getSlot();

		if (!in_array($item->id, [$slots->i17, $slots->i18], true)) {
			throw new Exception('В бою можно использовать только свитки из магических слотов');
		}
	}

	private static function consume(User $caster, UserItem $item): void
	{
		$item->wearout++;

		if ($item->wearout >= $item->wearout_max) {
			$slots = $caster->getSlot();

			for ($slot = 1; $slot <= $slots::MAX_SLOTS; $slot++) {
				if ($slots->{'i' . $slot} === $item->id) {
					$slots->{'i' . $slot} = 0;
				}
			}

			$slots->save();
			$item->delete();
		} else {
			$item->save();
		}

		$caster->getSlot()->clearCache();
	}
}
