<?php

use App\Http\Controllers;
use App\Http\Middleware\Locale;
use Illuminate\Support\Facades\Route;

Route::get('/', [Controllers\IndexController::class, 'index'])->name('index');
Route::get('/login', [Controllers\LoginController::class, 'index'])->name('login');
Route::get('login/social/{service}', [Controllers\LoginController::class, 'services'])->name('login.social');
Route::get('login/callback/{service}', [Controllers\LoginController::class, 'callback']);
Route::get('/reg', [Controllers\IndexController::class, 'reg']);
Route::get('/reminder', [Controllers\IndexController::class, 'reminder']);

Route::middleware(['auth'])->group(function () {
	Route::get('/avatar', [Controllers\AvatarController::class, 'index']);
	Route::get('/game', [Controllers\GameController::class, 'index'])->name('game');
	Route::get('/person', [Controllers\PersonController::class, 'index'])->name('person.detail');
});
