<?php

namespace App\Engine\Services;

use App\Exceptions\Exception;
use App\Models\Tribe;
use App\Models\User;
use Closure;
use Illuminate\Support\Facades\DB;

class TribeService
{
	public const int LEADER = 1;
	public const int RECRUITER = 5;
	public const int RECRUIT_PRICE = 200;
	public const array RANKS = [
		0 => 'Боец',
		1 => 'Глава',
		2 => 'Зам. главы',
		3 => 'Казначей',
		4 => 'Оружейник',
		5 => 'Вербовщик',
		6 => 'Командир группы',
		7 => 'Судья',
		8 => 'Леди',
		9 => 'Дипломат',
		10 => 'Журналист',
		11 => 'Шут',
	];

	public static function isLeader(User $user): bool
	{
		return $user->tribe_id !== null && $user->tribe_rank === self::LEADER;
	}

	public static function canManageMembers(User $user): bool
	{
		return $user->tribe_id !== null && in_array($user->tribe_rank, [self::LEADER, self::RECRUITER], true);
	}

	public static function canWithdraw(User $user): bool
	{
		return $user->tribe_id !== null && in_array($user->tribe_rank, [self::LEADER, 2, 3], true);
	}

	public static function manageMember(User $user, string $action, string $name, int $rank = 0): string
	{
		return self::transaction($user, function (User $account, Tribe $tribe) use ($action, $name, $rank) {
			if (!in_array($action, ['add', 'remove', 'transfer', 'rank'], true)) {
				throw new Exception('Неизвестное действие.');
			}

			if ($action === 'transfer' ? !self::isLeader($account) : !self::canManageMembers($account)) {
				throw new Exception('Нет доступа к данной функции!');
			}

			$member = User::query()->where('name', trim($name))->lockForUpdate()->first();

			if (!$member) {
				throw new Exception('Персонаж не найден!');
			}

			if ($action === 'add') {
				if ($member->tribe_id !== null) {
					throw new Exception('Персонаж уже состоит в клане!');
				}

				if ($member->rank > 99) {
					throw new Exception('Вы не можете принимать в клан должностных лиц!');
				}

				if ($member->level < 4) {
					throw new Exception('Вступать в клан могут персонажи не ниже 4 уровня!');
				}

				self::checkInquisition($member);
				$price = $tribe->members()->count() * self::RECRUIT_PRICE;

				if ($tribe->moneys < $price) {
					throw new Exception('В казне клана нет ' . $price . ' зол.!');
				}

				$member->update(['tribe_id' => $tribe->id, 'tribe_rank' => 0]);
				$tribe->decrement('moneys', $price);
				$message = 'Принят в клан персонаж ' . $member->name . ' за ' . $price . ' зол.';
				self::notify($member, 'Персонаж ' . $account->name . ' принял Вас в клан ' . $tribe->name . '.');
			} else {
				if ($member->tribe_id !== $tribe->id) {
					throw new Exception('Персонаж не состоит в вашем клане!');
				}

				if (self::isLeader($member)) {
					throw new Exception('Нельзя исключить главу клана или изменить его ранг!');
				}

				switch ($action) {
					case 'remove':
						if ($member->is($account)) {
							throw new Exception('Вы не можете исключить из клана самого себя!');
						}

						self::checkInquisition($member);
						$member->update(['tribe_id' => null, 'tribe_rank' => 0]);
						$message = 'Исключён из клана персонаж ' . $member->name . '.';
						self::notify($member, 'Персонаж ' . $account->name . ' исключил Вас из клана ' . $tribe->name . '.');
						break;
					case 'transfer':
						$account->update(['tribe_rank' => 0]);
						$member->update(['tribe_rank' => self::LEADER]);
						$message = 'Полномочия главы клана переданы персонажу ' . $member->name . '.';
						self::notify($member, 'Персонаж ' . $account->name . ' передал Вам полномочия главы клана ' . $tribe->name . '.');
						break;
					case 'rank':
						if (!array_key_exists($rank, self::RANKS) || $rank === self::LEADER) {
							throw new Exception('Выберите ранг из списка. Главу можно изменить только передачей полномочий.');
						}

						if (!self::isLeader($account) && ($member->is($account)
							|| in_array($rank, [2, 3, self::RECRUITER], true)
							|| in_array($member->tribe_rank, [2, 3, self::RECRUITER], true))) {
							throw new Exception('Изменять свой ранг и назначать или снимать управляющие ранги может только глава клана.');
						}

						$member->update(['tribe_rank' => $rank]);
						$message = 'Персонажу ' . $member->name . ' назначен ранг «' . self::RANKS[$rank] . '».';
						break;
				}
			}

			$tribe->logs()->create(['user_id' => $account->id, 'target_user_id' => $member->id, 'action' => $message]);

			return $message;
		});
	}

	public static function leave(User $user): string
	{
		return self::transaction($user, function (User $account, Tribe $tribe) {
			HealerService::ensureAvailable($account);

			if (self::isLeader($account)) {
				throw new Exception('Глава клана не может покинуть его таким способом.');
			}

			self::checkInquisition($account);

			$price = config('game.healer.leave_tribe_price');

			if ($account->gold < $price) {
				throw new Exception('Для выхода из клана нужно ' . $price . ' зол.');
			}

			$account->update([
				'tribe_id' => null,
				'tribe_rank' => 0,
				'gold' => $account->gold - $price,
			]);

			$tribe->logs()->create([
				'user_id' => $account->id,
				'target_user_id' => $account->id,
				'action' => 'Персонаж ' . $account->name . ' покинул клан через знахаря.',
			]);

			return 'Вы покинули клан «' . $tribe->name . '» за ' . $price . ' зол.';
		});
	}

	public static function transferGold(User $user, float $amount, bool $withdraw): string
	{
		if (!is_finite($amount) || $amount <= 0 || $amount > 9999999999.99 || round($amount, 2) != $amount) {
			throw new Exception('Укажите положительную сумму с точностью до сотых.');
		}

		return self::transaction($user, function (User $account, Tribe $tribe) use ($amount, $withdraw) {
			if ($withdraw && !self::canWithdraw($account)) {
				throw new Exception('Снимать золото могут только глава, заместитель и казначей.');
			}

			if (($withdraw ? $tribe->moneys : $account->gold) < $amount) {
				throw new Exception($withdraw ? 'В казне недостаточно золота!' : 'У вас недостаточно золота!');
			}

			$balance = round($tribe->moneys + ($withdraw ? -$amount : $amount), 2);
			$gold = round($account->gold + ($withdraw ? $amount : -$amount), 2);

			if ($balance > 9999999999.99) {
				throw new Exception('Превышен лимит казны клана.');
			}

			if ($gold > 9999999999.99) {
				throw new Exception('Превышен лимит золота персонажа.');
			}

			$account->update(['gold' => $gold]);
			$tribe->update(['moneys' => $balance]);
			$message = ($withdraw ? 'Взял из казны ' : 'Перечислил в казну ') . $amount . ' зол.';
			$tribe->logs()->create(['user_id' => $account->id, 'action' => $message]);

			return $withdraw ? 'Вы сняли из казны ' . $amount . ' зол.' : 'Вы перечислили в казну ' . $amount . ' зол.';
		});
	}

	/** @param array{about: ?string, laws: ?string, url: ?string} $data */
	public static function updateInfo(User $user, array $data): string
	{
		return self::transaction($user, function (User $account, Tribe $tribe) use ($data) {
			if (!self::isLeader($account)) {
				throw new Exception('Редактировать клан может только глава.');
			}

			$tribe->update(['about' => $data['about'], 'laws' => $data['laws'], 'url' => $data['url']]);
			$tribe->logs()->create(['user_id' => $account->id, 'action' => 'Обновил информацию о клане.']);

			return 'Информация о клане сохранена.';
		});
	}

	/** @param Closure(User, Tribe): string $action */
	private static function transaction(User $user, Closure $action): string
	{
		return DB::transaction(function () use ($user, $action) {
			$tribe = Tribe::query()->lockForUpdate()->find($user->tribe_id);
			$account = User::query()->lockForUpdate()->findOrFail($user->id);

			if (!$tribe || $account->tribe_id !== $tribe->id) {
				throw new Exception('Вы не состоите в этом клане!');
			}

			return $action($account, $tribe);
		}, 3);
	}

	private static function checkInquisition(User $user): void
	{
		if (!$user->inquisitor_check || $user->inquisitor_check->isPast()) {
			throw new Exception('Персонаж не проходил проверку у инквизиторов или срок проверки истёк!');
		}
	}

	private static function notify(User $recipient, string $text): void
	{
		ChatService::sendSystemMessage($text, [$recipient]);
	}
}
