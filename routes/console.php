<?php

use App\Engine\Services\GamblingHouseService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('lottery:draw', function () {
	$count = GamblingHouseService::drawDueLotteries();

	$this->info('Завершено розыгрышей: ' . $count);
});

Schedule::command('lottery:draw')->everyMinute()->withoutOverlapping();

Schedule::command('model:prune')->daily()->withoutOverlapping();

Schedule::command('auth:clear-resets')->daily()->withoutOverlapping();
