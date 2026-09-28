<?php

use App\Http\Controllers;
use App\Http\Middleware\CheckReferral;
use App\Http\Middleware\RedirectToGame;
use Illuminate\Support\Facades\Route;

Route::get('/info/{id?}', [Controllers\InfoController::class, 'index'])->whereNumber('id')->name('info');

Route::middleware([RedirectToGame::class])->group(function () {
	Route::get('/', [Controllers\IndexController::class, 'index'])->middleware([CheckReferral::class])->name('index');
	Route::get('/login', [Controllers\LoginController::class, 'index'])->name('login');
	Route::get('login/social/{service}', [Controllers\LoginController::class, 'services'])->name('login.social');
	Route::get('login/callback/{service}', [Controllers\LoginController::class, 'callback']);
	Route::get('/reg', [Controllers\IndexController::class, 'reg']);
	Route::get('/reminder', [Controllers\IndexController::class, 'reminder']);
});

Route::middleware(['auth'])->group(function () {
	Route::post('/logout', [Controllers\LoginController::class, 'logout'])->name('logout');

	Route::get('/library', [Controllers\LibraryController::class, 'index'])->name('library');

	Route::get('/chat/last', [Controllers\ChatController::class, 'last']);
	Route::post('/chat/send', [Controllers\ChatController::class, 'send']);
	Route::get('/chat/online', [Controllers\ChatController::class, 'online']);
	Route::post('/magic', [Controllers\MagicController::class, 'store'])->name('magic.store');

	Route::middleware(['game'])->group(function () {
		Route::get('/tribe', [Controllers\TribeController::class, 'index'])->name('tribe');
		Route::post('/tribe', [Controllers\TribeController::class, 'store'])->name('tribe.store');
		Route::get('/transfers', [Controllers\TransfersController::class, 'index'])->name('transfers');
		Route::post('/transfers', [Controllers\TransfersController::class, 'store'])->name('transfers.store');
		Route::get('/pay', [Controllers\PayController::class, 'index'])->name('pay');
		Route::get('/avatar', [Controllers\AvatarController::class, 'index']);
		Route::get('/person', [Controllers\PersonController::class, 'index'])->name('person.detail');
		Route::match(['get', 'post'], '/person/updates', [Controllers\PersonController::class, 'updates'])->name('person.updates');
		Route::match(['get', 'post'], '/person/abilities', [Controllers\PersonController::class, 'abilities'])->name('person.abilities');
		Route::match(['get', 'post'], '/person/avatar', [Controllers\AvatarController::class, 'index'])->name('person.avatar');
		Route::get('/person/inventory', [Controllers\PersonController::class, 'inventory'])->name('person.inventory');
		Route::post('/person/inventory/drop', [Controllers\PersonController::class, 'drop'])->name('person.inventory.drop');
		Route::match(['get', 'post'], '/person/inventory/sets', [Controllers\PersonController::class, 'sets'])->name('person.inventory.sets');
		Route::match(['get', 'post'], '/person/friends', [Controllers\PersonController::class, 'friends'])->name('person.friends');
		Route::match(['get', 'post'], '/person/settings', [Controllers\PersonController::class, 'settings'])->name('person.settings');
		Route::match(['get', 'post'], '/map', [Controllers\MapController::class, 'index'])->name('map');
		Route::get('/map/change/{room}', [Controllers\MapController::class, 'change']);
		Route::match(['get', 'post'], '/battle', [Controllers\BattleController::class, 'index'])->name('battle');
	});
});
