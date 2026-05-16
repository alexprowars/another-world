<?php

use App\Http\Controllers;
use App\Http\Middleware\RedirectToGame;
use Illuminate\Support\Facades\Route;

Route::middleware([RedirectToGame::class])->group(function () {
	Route::get('/', [Controllers\IndexController::class, 'index'])->name('index');
	Route::get('/login', [Controllers\LoginController::class, 'index'])->name('login');
	Route::get('login/social/{service}', [Controllers\LoginController::class, 'services'])->name('login.social');
	Route::get('login/callback/{service}', [Controllers\LoginController::class, 'callback']);
	Route::get('/reg', [Controllers\IndexController::class, 'reg']);
	Route::get('/reminder', [Controllers\IndexController::class, 'reminder']);
});

Route::middleware(['auth', 'game'])->group(function () {
	Route::get('/avatar', [Controllers\AvatarController::class, 'index']);
	Route::get('/person', [Controllers\PersonController::class, 'index'])->name('person.detail');
	Route::get('/person/updates', [Controllers\PersonController::class, 'updates'])->name('person.updates');
	Route::get('/person/inventory', [Controllers\PersonController::class, 'inventory'])->name('person.inventory');
	Route::get('/chat/last', [Controllers\ChatController::class, 'last']);
	Route::post('/chat/send', [Controllers\ChatController::class, 'send']);
	Route::get('/chat/online', [Controllers\ChatController::class, 'online']);
	Route::get('/map', [Controllers\MapController::class, 'index'])->name('map');
	Route::get('/battle', [Controllers\BattleController::class, 'index'])->name('battle');

	Route::get('/map/city/street/{id}', [Controllers\Map\City\StreetController::class, 'index'])->name('map.city.street');
	Route::get('/map/city/shop', [Controllers\Map\City\ShopController::class, 'index'])->name('map.city.shop');
	Route::match(['get', 'post'], '/map/arena/training', [Controllers\Map\City\TrainingController::class, 'index'])->name('map.arena.training');
});
