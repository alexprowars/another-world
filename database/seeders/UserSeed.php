<?php

namespace Database\Seeders;

use App\Services\UserService;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeed extends Seeder
{
	public function run()
	{
		if (!User::find(1)) {
			$user = UserService::creation([
				'name' 		=> 'admin',
				'email'    	=> 'admin@admin.com',
				'password' 	=> 'password',
			]);

			$user->gold = 500;
			$user->credits = 200;
			$user->rank = 100;
			$user->save();
		}
	}
}
