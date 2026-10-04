<?php

use App\Engine\World\World;
use App\Http\Controllers;
use App\Http\Middleware\CheckReferral;
use App\Http\Middleware\EnsureLocation;
use App\Http\Middleware\RedirectToGame;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::get('/info/{id?}', [Controllers\InfoController::class, 'index'])->whereNumber('id')->name('info');
Route::get('/battle/log/{id}', [Controllers\BattleLogController::class, 'index'])->whereNumber('id')->name('battle.log');
Route::get('/law', [Controllers\IndexController::class, 'law'])->name('law');
Route::get('/agreement', [Controllers\IndexController::class, 'agreement'])->name('agreement');

Route::middleware([RedirectToGame::class])->group(function () {
	Route::get('/', [Controllers\IndexController::class, 'index'])->middleware([CheckReferral::class])->name('index');
	Route::get('/login', [Controllers\LoginController::class, 'index'])->name('login');
	Route::post('/login', [Controllers\LoginController::class, 'store'])->middleware('throttle:5,1')->name('login.store');
	Route::get('/register', [Controllers\RegistrationController::class, 'index'])->name('register');
	Route::post('/register', [Controllers\RegistrationController::class, 'store'])->middleware('throttle:5,1')->name('register.store');
	Route::get('/forgot-password', [Controllers\PasswordController::class, 'request'])->name('password.request');
	Route::post('/forgot-password', [Controllers\PasswordController::class, 'email'])->middleware('throttle:5,1')->name('password.email');
	Route::get('/reset-password/{token}', [Controllers\PasswordController::class, 'reset'])->name('password.reset');
	Route::post('/reset-password', [Controllers\PasswordController::class, 'update'])->middleware('throttle:5,1')->name('password.update');
	Route::get('login/social/{service}', [Controllers\LoginController::class, 'services'])->name('login.social');
	Route::get('login/callback/{service}', [Controllers\LoginController::class, 'callback']);
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
		Route::get('/world', [Controllers\WorldController::class, 'index'])->name('world');
		Route::get('/cities/{city}', [Controllers\CityController::class, 'index'])->name('city');
		Route::post('/movement', [Controllers\MovementController::class, 'store'])->name('movement');

		Route::prefix('/cities/{city}')->middleware(EnsureLocation::class)->group(function () {
			foreach (World::handlers() as $code => $definition) {
				$path = '/' . $code . ($code === 'vault' ? '/{vaultRoom}' : '');

				Route::get($path, [$definition['controller'], 'index'])
					->defaults('locationCode', $code)
					->whereNumber('vaultRoom')
					->name('city.' . $code);

				foreach ($definition['pages'] ?? [] as $pagePath => $method) {
					Route::get($path . '/' . $pagePath, [$definition['controller'], $method])
						->defaults('locationCode', $code)
						->name('city.' . $code . '.' . $method);
				}

				foreach ($definition['actions'] as $actionPath => $action) {
					$method = Str::camel($action);

					Route::post($path . '/' . $actionPath, [$definition['controller'], $method])
						->defaults('locationCode', $code)
						->whereNumber('vaultRoom')
						->name('city.' . $code . '.' . $action);
				}
			}
		});
		Route::get('/arena', [Controllers\ArenaController::class, 'index'])->name('arena');
		Route::post('/arena', [Controllers\ArenaController::class, 'store'])->name('arena.store');
		Route::get('/battle', [Controllers\BattleController::class, 'index'])->name('battle');
		Route::post('/battle', [Controllers\BattleController::class, 'store'])->name('battle.store');
	});
});
