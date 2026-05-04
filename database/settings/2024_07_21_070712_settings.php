<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
	public function up(): void
	{
		$this->migrator->add('general.usersTotal', 0);
		$this->migrator->add('general.usersOnline', 0);
	}
};
