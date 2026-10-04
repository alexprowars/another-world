<?php

namespace App\Engine\Services;

use App\Exceptions\Exception;
use App\Models\Item;
use App\Models\User;
use App\Models\UserItem;
use Closure;
use Illuminate\Support\Facades\DB;

class SmithyService
{
	public const int ENGRAVING_PRICE = 150;
	public const int UPGRADE_PRICE = 50;
	public const int WORK_SECONDS = 600;
	public const int WORK_STAMINA = 15;
	public const array GEM_BONUSES = ['strength', 'dexterity', 'agility', 'vitality', 'krit', 'unkrit', 'uv', 'unuv'];

	public static function available(UserItem $item): bool
	{
		return !$item->present && !$item->bank && !$item->market && !$item->pawnshop;
	}

	public static function equipment(UserItem $item): bool
	{
		return ($item->type >= 1 && $item->type <= 11) || in_array($item->type, [24, 25]);
	}

	public static function canRepair(UserItem $item): bool
	{
		return self::available($item) && (self::equipment($item) || $item->type == 18) && $item->wearout > 0;
	}

	public static function rawGem(UserItem $item): bool
	{
		return $item->type == 20 && !array_any(self::GEM_BONUSES, fn ($stat) => $item->{$stat} != 0);
	}

	public static function repairPrice(UserItem $item, bool $full = true): float
	{
		return round(0.05 * ($item->requirements['level'] ?? 0) * ($full ? $item->wearout : 1) * ($item->price_type == 1 ? 10 : 1), 2);
	}

	public static function finishWork(User $user): void
	{
		if ($user->r_type != 7 || !$user->r_date || $user->r_date->isFuture()) {
			return;
		}

		DB::transaction(function () use ($user) {
			$user->refreshForUpdate();

			if ($user->r_type == 7 && $user->r_date && !$user->r_date->isFuture()) {
				$user->update(['r_type' => null, 'r_date' => null]);
			}
		}, 3);
	}

	public static function repair(User $user, int $itemId, bool $full): string
	{
		return self::transaction($user, function () use ($user, $itemId, $full) {
			$item = self::item($user, $itemId, true);

			if (!self::canRepair($item)) {
				throw new Exception('Этот предмет не нуждается в ремонте или не подлежит починке!');
			}

			$price = self::repairPrice($item, $full);

			self::pay($user, 'gold', $price);

			$item->update(['wearout' => $full ? 0 : $item->wearout - 1]);

			$user->getSlot()->clearCache();

			LogsService::addItemLog($user, 'починил', $item->title . ' (' . $price . ' зол.)', 'кузница');

			return 'Предмет починен за ' . $price . ' зол.!';
		});
	}

	public static function engrave(User $user, int $itemId, string $text): string
	{
		$text = trim($text);

		if ($text === '' || mb_strlen($text) > 25 || !preg_match('/^[a-zа-яё0-9_.,!? -]+$/iu', $text)) {
			throw new Exception('Введите до 25 символов: русские или английские буквы, цифры, пробелы и знаки _ . , - ! ?');
		}

		return self::transaction($user, function () use ($user, $itemId, $text) {
			$item = self::item($user, $itemId);

			if (!self::equipment($item)) {
				throw new Exception('На этом предмете нельзя сделать гравировку!');
			}

			if ($item->engraving !== null && $item->engraving !== '') {
				throw new Exception('На этом предмете уже есть гравировка!');
			}

			self::pay($user, 'gold', self::ENGRAVING_PRICE);

			$item->update(['engraving' => $text]);

			LogsService::addItemLog($user, 'выгравировал', $item->title . ': ' . $text, 'кузница');

			return 'Надпись <u>' . e($text) . '</u> выгравирована за ' . self::ENGRAVING_PRICE . ' зол.';
		});
	}

	public static function upgrade(User $user, int $itemId): string
	{
		return self::transaction($user, function () use ($user, $itemId) {
			self::profession($user, 2);

			$item = self::item($user, $itemId);

			if (!self::equipment($item) || $item->wearout_max <= 20) {
				throw new Exception('Предмет не подходит для модернизации или его долговечность слишком мала!');
			}

			self::pay($user, 'credits', self::UPGRADE_PRICE);

			$item->update([
				'min' => $item->min + 1,
				'max' => $item->max + 1,
				'wearout_max' => $item->wearout_max - 20,
			]);

			LogsService::addItemLog($user, 'модернизировал', $item->title . ' (' . self::UPGRADE_PRICE . ' пл.)', 'кузница');

			$message = 'Модернизация <b>' . e($item->title) . '</b> прошла успешно. Урон: ' . $item->min . '–' . $item->max . '. Максимальная долговечность уменьшилась на 20.';

			ChatService::sendSystemMessage(
				'Модернизация ' . $item->title . ' прошла успешно. Урон: ' . $item->min . '–' . $item->max
					. '. Максимальная долговечность уменьшилась на 20.',
				[$user],
			);

			return $message;
		});
	}

	public static function cut(User $user, int $itemId): string
	{
		return self::transaction($user, function () use ($user, $itemId) {
			self::profession($user, 3);

			$gem = self::item($user, $itemId);

			if (!self::rawGem($gem)) {
				throw new Exception('Этот камень не подлежит огранке!');
			}

			$template = Item::query()->where('code', $gem->code)->where('type', 20)->first();

			if (!$template || !array_any(self::GEM_BONUSES, fn ($stat) => $template->{$stat} != 0)) {
				throw new Exception('Характеристики огранённого камня не найдены!');
			}

			self::startWork($user, 3);

			$gem->fill($template->only([...self::GEM_BONUSES, 'hp', 'energy', 'intelligence', 'min', 'max']));
			$gem->fill([
				'price' => 30,
				'wearout' => 0,
				'about' => 'Может быть вставлен в предметы для изменения характеристик',
			]);

			$gem->save();

			LogsService::addItemLog($user, 'огранил', $gem->title, 'кузница');

			return 'Огранка началась. Работа займёт 10 минут.';
		});
	}

	public static function insert(User $user, int $gemId, int $itemId): string
	{
		return self::transaction($user, function () use ($user, $gemId, $itemId) {
			self::profession($user, 2);

			$gem = self::item($user, $gemId);
			$item = self::item($user, $itemId);

			if ($gem->type != 20 || self::rawGem($gem)) {
				throw new Exception('Для вставки нужен огранённый драгоценный камень!');
			}

			if (!self::equipment($item) || $item->mf_type) {
				throw new Exception('В этот предмет нельзя вставить камень или он уже модифицирован!');
			}

			self::startWork($user, 2);

			foreach (self::GEM_BONUSES as $stat) {
				$item->{$stat} += $gem->{$stat};
			}

			$item->fill([
				'title' => $item->title . '[МФ]',
				'price' => $item->price + 20,
				'mf_type' => 1,
			]);

			$item->save();

			$gem->delete();

			LogsService::addItemLog($user, 'вставил камень', $gem->title . ' в ' . $item->title, 'кузница');

			return 'Вставка камня началась. Работа займёт 10 минут.';
		});
	}

	private static function transaction(User $user, Closure $action): string
	{
		return DB::transaction(function () use ($user, $action) {
			$user->unsetRelation('slots')->refreshForUpdate();

			if ($user->r_type == 7 && $user->r_date && !$user->r_date->isFuture()) {
				$user->update(['r_type' => null, 'r_date' => null]);
			}

			if ($user->r_date || $user->r_type) {
				throw new Exception('Вы заняты другой работой!');
			}

			return $action();
		}, 3);
	}

	private static function item(User $user, int $itemId, bool $allowEquipped = false): UserItem
	{
		$item = UserItem::query()->whereBelongsTo($user)->lockForUpdate()->find($itemId);

		if (!$item || !self::available($item)) {
			throw new Exception('Предмет не найден или недоступен!');
		}

		if (!$allowEquipped && ($item->onset || in_array($item->id, $user->getSlot()->getItemsId()))) {
			throw new Exception('Сначала снимите предмет!');
		}

		return $item;
	}

	private static function profession(User $user, int $profession): void
	{
		if ($user->profession != $profession) {
			throw new Exception($profession == 3 ? 'Огранкой может заниматься только огранщик!' : 'Эта работа доступна только кузнецу!');
		}
	}

	/** @param 'gold'|'credits' $currency */
	private static function pay(User $user, string $currency, float $price): void
	{
		if ($user->{$currency} < $price) {
			throw new Exception('Недостаточно ' . ($currency === 'gold' ? 'золота' : 'платины') . '!');
		}

		$user->update([$currency => round($user->{$currency} - $price, 2)]);
	}

	private static function startWork(User $user, int $profession): void
	{
		if ($user->stamina_now < self::WORK_STAMINA) {
			throw new Exception('Для работы нужно не менее ' . self::WORK_STAMINA . ' единиц сил!');
		}

		$slots = $user->getSlot();
		$tool = UserItem::query()->whereBelongsTo($user)->lockForUpdate()->find($slots->i3);

		if (!$tool || !self::available($tool) || $tool->type != 18 || $tool->onset != 3
			|| ($tool->requirements['profession'] ?? 0) != $profession || $tool->wearout >= $tool->wearout_max) {
			throw new Exception('Нужно надеть исправный инструмент для своей профессии!');
		}

		$tool->wearout++;

		if ($tool->wearout >= $tool->wearout_max) {
			$slots->update(['i3' => null]);
			$tool->onset = null;
		}

		$tool->save();

		$slots->clearCache();

		$user->update([
			'stamina_now' => $user->stamina_now - self::WORK_STAMINA,
			'r_type' => 7,
			'r_date' => now()->addSeconds(self::WORK_SECONDS),
		]);
	}
}