<?php

namespace Database\Seeders;

use App\Engine\Services\UserService;
use App\Models\User;
use Illuminate\Database\Seeder;

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
