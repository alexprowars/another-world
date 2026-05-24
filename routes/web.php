<?php

use App\Http\Controllers;
use App\Http\Middleware\CheckReferral;
use App\Http\Middleware\RedirectToGame;
use Illuminate\Support\Facades\Route;

Route::middleware([RedirectToGame::class])->group(function () {
	Route::get('/', [Controllers\IndexController::class, 'index'])->middleware([CheckReferral::class])->name('index');
	Route::get('/login', [Controllers\LoginController::class, 'index'])->name('login');
	Route::get('login/social/{service}', [Controllers\LoginController::class, 'services'])->name('login.social');
	Route::get('login/callback/{service}', [Controllers\LoginController::class, 'callback']);
	Route::get('/reg', [Controllers\IndexController::class, 'reg']);
	Route::get('/reminder', [Controllers\IndexController::class, 'reminder']);
});

Route::middleware(['auth'])->group(function () {
	Route::get('/chat/last', [Controllers\ChatController::class, 'last']);
	Route::post('/chat/send', [Controllers\ChatController::class, 'send']);
	Route::get('/chat/online', [Controllers\ChatController::class, 'online']);

	Route::middleware(['game'])->group(function () {
		Route::get('/avatar', [Controllers\AvatarController::class, 'index']);
		Route::get('/person', [Controllers\PersonController::class, 'index'])->name('person.detail');
		Route::match(['get', 'post'], '/person/updates', [Controllers\PersonController::class, 'updates'])->name('person.updates');
		Route::match(['get', 'post'], '/person/abilities', [Controllers\PersonController::class, 'abilities'])->name('person.abilities');
		Route::match(['get', 'post'], '/person/avatar', [Controllers\AvatarController::class, 'index'])->name('person.avatar');
		Route::get('/person/inventory', [Controllers\PersonController::class, 'inventory'])->name('person.inventory');
		Route::match(['get', 'post'], '/map', [Controllers\MapController::class, 'index'])->name('map');
		Route::get('/map/change/{room}', [Controllers\MapController::class, 'change']);
		Route::get('/battle', [Controllers\BattleController::class, 'index'])->name('battle');
	});
});
