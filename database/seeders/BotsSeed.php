<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BotsSeed extends Seeder
{
	public function run()
	{
		$players = [
			[
				'name' => 'Ловкая Мона',
				'level' => 3,
				'strength' => 16,
				'dexterity' => 8,
				'agility' => 15,
				'vitality' => 18,
			],
			[
				'name' => 'Чуткая Джесси',
				'level' => 0,
				'strength' => 4,
				'dexterity' => 3,
				'agility' => 3,
				'vitality' => 4,
			],
			[
				'name' => 'Эдвард Руки Ножницы',
				'level' => 3,
				'strength' => 34,
				'dexterity' => 32,
				'agility' => 7,
				'vitality' => 30,
			],
			[
				'name' => 'Потный Гарри',
				'level' => 4,
				'strength' => 22,
				'dexterity' => 10,
				'agility' => 18,
				'vitality' => 24,
			],
			[
				'name' => 'Наивная Изольда',
				'level' => 4,
				'strength' => 42,
				'dexterity' => 40,
				'agility' => 9,
				'vitality' => 40,
			],
			[
				'name' => 'Мамочка',
				'level' => 1,
				'strength' => 7,
				'dexterity' => 4,
				'agility' => 9,
				'vitality' => 9,
			],
			[
				'name' => 'Легендарный Сноб',
				'level' => 1,
				'strength' => 10,
				'dexterity' => 12,
				'agility' => 4,
				'vitality' => 9,
			],
			[
				'name' => 'Бабушка Скорпа',
				'level' => 5,
				'strength' => 55,
				'dexterity' => 55,
				'agility' => 10,
				'vitality' => 55,
			],
			[
				'name' => 'Слепой Гудвин',
				'level' => 2,
				'strength' => 21,
				'dexterity' => 24,
				'agility' => 4,
				'vitality' => 16,
			],
			[
				'name' => 'Летучий Голандец',
				'level' => 2,
				'strength' => 11,
				'dexterity' => 7,
				'agility' => 13,
				'vitality' => 11,
			],
			[
				'name' => 'Упрямый Бруно',
				'level' => 5,
				'strength' => 28,
				'dexterity' => 12,
				'agility' => 22,
				'vitality' => 33,
			],
		];

		foreach ($players as $player) {
			User::query()->updateOrCreate([
				'name' => $player['name'],
				'rank' => 60,
				'is_clone' => false,
			], [
				'email' => 'training-' . Str::slug($player['name'], '-', 'ru') . '@bot',
				'password' => Hash::make(Str::random(10)),
				'name' => $player['name'],
				'online' => now(),
				'locale' => 'ru',
				'rank' => 60,
				'location' => 'valmir.training-arena',
				'level' => $player['level'],
				'strength' => $player['strength'],
				'dexterity' => $player['dexterity'],
				'agility' => $player['agility'],
				'vitality' => $player['vitality'],
			]);
		}
	}
}
