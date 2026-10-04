<?php

use App\Engine\Services\GamblingHouseService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('lottery:draw', function () {
	$count = GamblingHouseService::drawDueLotteries();

	$this->info('Завершено розыгрышей: ' . $count);
});

Schedule::command('lottery:draw')->everyMinute()->withoutOverlapping();
