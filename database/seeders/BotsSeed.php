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
				'strength' => 20,
				'dexterity' => 10,
				'agility' => 35,
				'vitality' => 20,
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
				'strength' => 20,
				'dexterity' => 30,
				'agility' => 10,
				'vitality' => 20,
			],
			[
				'name' => 'Потный Гарри',
				'level' => 4,
				'strength' => 55,
				'dexterity' => 5,
				'agility' => 100,
				'vitality' => 30,
			],
			[
				'name' => 'Наивная Изольда',
				'level' => 4,
				'strength' => 40,
				'dexterity' => 80,
				'agility' => 5,
				'vitality' => 35,
			],
			[
				'name' => 'Мамочка',
				'level' => 1,
				'strength' => 7,
				'dexterity' => 3,
				'agility' => 8,
				'vitality' => 6,
			],
			[
				'name' => 'Легендарный Сноб',
				'level' => 1,
				'strength' => 7,
				'dexterity' => 10,
				'agility' => 3,
				'vitality' => 6,
			],
			[
				'name' => 'Бабушка Скорпа',
				'level' => 5,
				'strength' => 80,
				'dexterity' => 200,
				'agility' => 50,
				'vitality' => 50,
			],
			[
				'name' => 'Слепой Гудвин',
				'level' => 2,
				'strength' => 8,
				'dexterity' => 15,
				'agility' => 6,
				'vitality' => 9,
			],
			[
				'name' => 'Летучий Голандец',
				'level' => 2,
				'strength' => 9,
				'dexterity' => 3,
				'agility' => 20,
				'vitality' => 15,
			]
		];

		foreach ($players as $player) {
			User::create([
				'email' => Str::random(6) . '@bot',
				'password' => Hash::make(Str::random(10)),
				'name' => $player['name'],
				'online' => now(),
				'locale' => 'ru',
				'rank' => 60,
				'room' => 2,
				'level' => $player['level'],
				's_strength' => $player['strength'],
				's_dexterity' => $player['dexterity'],
				's_agility' => $player['agility'],
				's_vitality' => $player['vitality'],
			]);
		}
	}
}
