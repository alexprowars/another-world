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
				'username' => 'admin',
				'email'    => 'admin@admin.com',
				'password' => 'password',
			]);

			$user->assignRole('admin');
		}
	}
}
