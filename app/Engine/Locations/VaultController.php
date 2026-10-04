<?php

namespace App\Engine\Locations;

use App\Engine\Services\ChatService;
use App\Engine\World\World;
use App\Exceptions\Exception;
use App\Models\User;
use App\Models\Vault as VaultRoom;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class VaultController extends LocationController
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

	public function index()
	{
		$user = auth()->user();

		if ($user->r_type == 8 && $user->r_date?->isPast()) {
			DB::transaction(function () use ($user) {
				$this->lockUser($user);
				$this->finishWork($user);
			});
		}

		$room = VaultRoom::query()
			->findOrFail($user->currentLocation()->roomId);
		$entranceId = World::location($user->currentLocation()->city, 'vault')['entrance_id'];

		$neighbors = VaultRoom::query()
			->whereIn('id', array_map(fn($direction) => $room->{$direction . '_id'}, self::DIRECTIONS))
			->get(['id', 'title'])
			->keyBy('id');

		$directions = [];

		foreach (self::DIRECTIONS as $direction) {
			$neighbor = $neighbors->get($room->{$direction . '_id'});

			$directions[$direction] = $neighbor ? [
				'id' => $neighbor->id,
				'title' => $neighbor->title,
				'location' => $user->currentLocation()->inCity('vault.' . $neighbor->id)->value(),
			] : null;
		}

		return Inertia::render('Map/Vault', [
			'vault' => $room->only(['id', 'title', 'text']),
			'is_entrance' => $room->id === $entranceId,
			'directions' => $directions,
			'destination' => $user->r_type == 10
				? VaultRoom::query()->find($user->vault_destination_id)?->title
				: null,
			'canHeal' => $room->id !== $entranceId && (!$room->heal_at || $room->heal_at->isPast()),
			'captcha' => !$user->r_date && !$user->r_type ? $this->captcha() : null,
		]);
	}

	public function store()
	{
		$user = $this->user;
		$action = request()->route('locationAction');

		try {
			$message = DB::transaction(function () use ($user, $action) {
				$this->lockUser($user);

				$room = VaultRoom::query()->findOrFail($user->currentLocation()->roomId);

				return match ($action) {
					'heal' => $this->heal($user, $room),
					'dig' => $this->dig($user),
					'unwork' => $this->cancel($user),
					default => throw new Exception('Неизвестное действие в подземелье.'),
				};
			});

			flash($message);
		} catch (Exception $e) {
			flash($e->getMessage());
		}

		return $this->redirectToLocation();
	}

	private function lockUser(User $user): void
	{
		$user->unsetRelation('slots')->refreshForUpdate();

		abort_if($user->trashed(), 404);
		abort_unless($user->currentLocation()->is('vault'), 404);
	}

	private function finishWork(User $user): void
	{
		if (!$user->r_date || $user->r_date->isFuture()) {
			return;
		}

		if ($user->r_type == 8) {
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
				? 'Поздравляем! Вы добыли драгоценный камень в кол-ве 1 ед!'
				: 'Вы добыли руду в кол-ве 1 ед!';

			ChatService::sendSystemMessage($message, [$user]);
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

		if ($room->id === World::location($user->currentLocation()->city, 'vault')['entrance_id']) {
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
