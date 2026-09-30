<?php

namespace App\Engine\Map;

use App\Exceptions\Exception;
use App\Models\User;
use App\Models\Vault as VaultRoom;
use App\Services\ChatService;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class Vault
{
	private const array DIRECTIONS = ['top', 'bottom', 'left', 'right'];

	private const array GEMS = [
		'alexandrit' => 'Александрит',
		'almaz' => 'Алмаз',
		'amazonit' => 'Амазонит',
		'biruza' => 'Бирюза',
		'pirit' => 'Пирит',
		'opal' => 'Опал',
		'rubin' => 'Рубин',
		'sapfir' => 'Сапфир',
	];

	public function __invoke()
	{
		$user = auth()->user();

		if (in_array($user->r_type, [8, 10]) && $user->r_date?->isPast()) {
			DB::transaction(function () use ($user) {
				$this->lockUser($user);
				$this->finishWork($user);
			});
		}

		$action = null;

		foreach (['heal', 'dig', 'unwork', 'go'] as $key) {
			if (request()->has($key)) {
				$action = $key;
				break;
			}
		}

		if ($action) {
			try {
				$message = DB::transaction(function () use ($user, $action) {
					$this->lockUser($user);

					$room = VaultRoom::query()->findOrFail($user->room);

					return match ($action) {
						'heal' => $this->heal($user, $room),
						'dig' => $this->dig($user),
						'unwork' => $this->cancel($user),
						'go' => $this->move($user, $room, request()->input('go')),
					};
				});

				if ($message) {
					flash($message);
				}
			} catch (Exception $e) {
				flash($e->getMessage());
			}

			return to_route('map');
		}

		$room = VaultRoom::query()
			->findOrFail($user->room);

		$neighbors = VaultRoom::query()
			->whereIn('id', array_map(fn($direction) => $room->{$direction . '_id'}, self::DIRECTIONS))
			->get(['id', 'title'])
			->keyBy('id');

		$directions = [];

		foreach (self::DIRECTIONS as $direction) {
			$directions[$direction] = $neighbors->get($room->{$direction . '_id'})?->only(['id', 'title']);
		}

		return Inertia::render('Map/Vault', [
			'vault' => $room->only(['id', 'title', 'text']),
			'directions' => $directions,
			'destination' => $user->r_type == 10
				? VaultRoom::query()->find($user->vault_destination_id)?->title
				: null,
			'canHeal' => $room->id != 200 && (!$room->heal_at || $room->heal_at->isPast()),
			'captcha' => !$user->r_date && !$user->r_type ? $this->captcha() : null,
		]);
	}

	private function lockUser(User $user): void
	{
		$user->unsetRelation('slots')->refreshForUpdate();

		abort_if($user->trashed(), 404);
		abort_unless($user->room >= 200 && $user->room <= 370, 404);
	}

	private function finishWork(User $user): void
	{
		if (!$user->r_date || $user->r_date->isFuture()) {
			return;
		}

		if ($user->r_type == 10) {
			$destination = VaultRoom::query()
				->findOrFail($user->vault_destination_id);

			$user->update([
				'room' => $destination->id,
				'vault_destination_id' => null,
				'r_date' => null,
				'r_type' => null,
			]);
		} elseif ($user->r_type == 8) {
			$gem = ($user->profession == 5 ? random_int(2, 7) : random_int(0, 9)) == 5;
			$code = $gem ? array_rand(self::GEMS) : 'ruda';

			$user->items()->create([
				'code' => $code,
				'title' => $gem ? self::GEMS[$code] : 'Руда',
				'price' => $gem ? 15 : 6,
				'type' => $gem ? 20 : 19,
				'wearout_max' => 1,
				'about' => $gem ? 'Неограненный камень' : 'Руда',
			]);

			$user->update([
				'r_date' => null,
				'r_type' => null,
			]);

			$message = $gem
				? 'Поздравляем! Вы добыли драгоценный камень в кол-ве <b><u>1 ед</u></b>!'
				: 'Вы добыли руду в кол-ве <b><u>1 ед</u></b>!';

			DB::afterCommit(fn() => ChatService::sendSystemMessage($user, '', $message));
		}
	}

	private function ensureFree(User $user): void
	{
		if ($user->r_date || $user->r_type) {
			throw new Exception('Вы заняты какой-то работой!');
		}
	}

	private function heal(User $user, VaultRoom $room): string
	{
		$this->ensureFree($user);

		if ($room->id == 200) {
			throw new Exception('Здесь колодец пустой!');
		}

		$room->refreshForUpdate();

		if ($room->heal_at && !$room->heal_at->isPast()) {
			throw new Exception('Кто-то оказался быстрее и выпил всю энергию из Колодца Жизни!');
		}

		if ($user->hp_now >= $user->hp_max) {
			throw new Exception('Вы не нуждаетесь в лечении!');
		}

		$room->update(['heal_at' => now()->addHour()]);
		$user->update(['hp_now' => $user->hp_max]);

		return 'Ваш уровень жизни полностью восстановлен!';
	}

	private function dig(User $user): string
	{
		$this->ensureFree($user);

		$expected = session()->pull('vault.captcha');
		$answer = request()->input('captcha');

		if (!is_string($expected) || !is_string($answer) || !hash_equals($expected, $answer)) {
			throw new Exception('Неправильный ввод цифр.');
		}

		$slots = $user->getSlot();

		$tool = $user->items()
			->whereKey($slots->i3)
			->where('code', 'kirka')
			->where('type', 18)
			->lockForUpdate()
			->first();

		if (!$tool || $tool->wearout >= $tool->wearout_max) {
			throw new Exception('Без исправной кирки добывать руду нельзя!');
		}

		if ($user->stamina_now < 15) {
			throw new Exception('Недостаточно сил для добычи. Восстановите запас сил в боях.');
		}

		$tool->wearout++;

		if ($tool->wearout >= $tool->wearout_max) {
			$slots->update(['i3' => null]);
			$tool->onset = null;
		}

		$tool->save();

		$slots->clearCache();

		$user->update([
			'r_date' => now()->addSeconds($user->profession == 5 ? 1125 : 1500),
			'r_type' => 8,
			'stamina_now' => $user->stamina_now - 15,
		]);

		return 'Добыча руды началась.';
	}

	private function cancel(User $user): string
	{
		if ($user->r_type != 8 || !$user->r_date) {
			throw new Exception('Вы не заняты никакой работой в шахте!');
		}

		$user->update([
			'r_date' => null,
			'r_type' => null,
		]);

		return 'Вы успешно отменили добычу руды.';
	}

	private function move(User $user, VaultRoom $room, mixed $direction): null
	{
		$this->ensureFree($user);

		if (!in_array($direction, self::DIRECTIONS, true)) {
			throw new Exception('Неизвестное направление.');
		}

		$destination = VaultRoom::query()
			->find($room->{$direction . '_id'});

		if (!$destination) {
			throw new Exception('В этом направлении нет прохода.');
		}

		$user->update([
			'vault_destination_id' => $destination->id,
			'r_date' => now()->addSeconds($destination->time),
			'r_type' => 10,
		]);

		return null;
	}

	private function captcha(): string
	{
		$code = (string) random_int(10000, 99999);

		session()->put('vault.captcha', $code);

		$image = imagecreatetruecolor(140, 48);
		$background = imagecolorallocate($image, 241, 245, 249);
		$noise = imagecolorallocate($image, 148, 163, 184);
		$foreground = imagecolorallocate($image, 51, 65, 85);
		imagefill($image, 0, 0, $background);

		for ($i = 0; $i < 8; $i++) {
			imageline($image, random_int(0, 139), random_int(0, 47), random_int(0, 139), random_int(0, 47), $noise);
		}

		foreach (str_split($code) as $i => $digit) {
			imagestring($image, 5, 15 + $i * 25, random_int(10, 24), $digit, $foreground);
		}

		ob_start();
		imagepng($image);
		$png = ob_get_clean();

		return 'data:image/png;base64,' . base64_encode($png);
	}
}
