<?php

namespace App\Engine\Services;

use App\Engine\World\Location;
use App\Engine\World\World;
use App\Exceptions\Exception;
use App\Models\User;
use App\Models\Vault;
use Illuminate\Support\Facades\DB;

class MovementService
{
	public static function move(User $user, string $destinationCode): void
	{
		$destination = Location::fromCode($destinationCode);
		$destination->ensureExists();

		DB::transaction(function () use ($user, $destination) {
			$user->refreshForUpdate();

			abort_if($user->trashed(), 404);

			if ($user->battle_id || BattleService::getCurrentUserRequest($user)) {
				throw new Exception('Нельзя перемещаться во время боя или ожидания заявки.');
			}

			if ($user->prison?->isFuture()) {
				throw new Exception('Нельзя покинуть тюрьму до окончания срока наказания.');
			}

			if ($user->r_date || $user->r_type) {
				throw new Exception('Нельзя перемещаться, пока вы заняты.');
			}

			$source = $user->currentLocation();

			if ($source->value() === $destination->value()) {
				return;
			}

			if ($source->is('vault') && $destination->is('vault') && $source->city === $destination->city) {
				self::moveInVault($user, $source, $destination);

				return;
			}

			$definition = World::location($source->city, $source->code);

			if (!in_array($destination->value(), $definition['connections'], true)) {
				throw new Exception('Из текущей локации нет прохода в выбранное место.');
			}

			if ($source->is('vault') && $source->roomId !== $definition['entrance_id']) {
				throw new Exception('Покинуть подземелье можно только через вход.');
			}

			if ($destination->is('vault') && $user->level < 2) {
				throw new Exception('Вход в подземелье только со 2 уровня!');
			}

			$user->update(['location' => $destination->value()]);
		}, 3);
	}

	public static function finishTravel(User $user): void
	{
		if ($user->r_type != 10 || !$user->r_date?->isPast()) {
			return;
		}

		DB::transaction(function () use ($user) {
			$user->refreshForUpdate();

			if ($user->r_type != 10 || !$user->r_date?->isPast()) {
				return;
			}

			$source = $user->currentLocation();

			abort_unless($source->is('vault'), 404);

			$destination = Vault::query()->findOrFail($user->vault_destination_id);

			$user->update([
				'location' => $source->inCity('vault.' . $destination->id)->value(),
				'vault_destination_id' => null,
				'r_date' => null,
				'r_type' => null,
			]);
		}, 3);
	}

	private static function moveInVault(User $user, Location $source, Location $destination): void
	{
		$room = Vault::query()->findOrFail($source->roomId);
		$neighbors = [$room->top_id, $room->bottom_id, $room->left_id, $room->right_id];

		if (!in_array($destination->roomId, $neighbors)) {
			throw new Exception('В выбранную комнату нет прохода.');
		}

		$target = Vault::query()->findOrFail($destination->roomId);

		$user->update([
			'vault_destination_id' => $target->id,
			'r_date' => now()->addSeconds($target->time),
			'r_type' => 10,
		]);
	}
}
