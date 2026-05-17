<?php

namespace App\Providers;

use App\Http\PageResponseFactory;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Inertia\ExceptionResponse;
use Inertia\Inertia;
use Inertia\ResponseFactory;

class AppServiceProvider extends ServiceProvider
{
	public function boot(): void
	{
		Model::unguard();

		Date::use(CarbonImmutable::class);

		JsonResource::withoutWrapping();

		Str::macro('sanitize', function (string $string): string {
			return htmlspecialchars($string);
		});

		Builder::macro('findOne', function ($key) {
			if (empty($key)) {
				return null;
			}

			/** @var Builder<Model> $this */
			return $this->whereKey($key)->first();
		});

		Builder::macro('findOneOrFail', function ($key) {
			if (empty($key)) {
				return null;
			}

			/** @var Builder<Model> $this */
			return $this->whereKey($key)->firstOrFail();
		});

		Gate::before(function ($user) {
			return $user->hasRole('admin') ? true : null;
		});

		if (app()->isProduction()) {
			url()->forceHttps();

			DB::prohibitDestructiveCommands();
		}

		Inertia::disableSsr(function () {
			$path = request()->path();

			if ($path == '/' || str_starts_with($path, 'info/')) {
				return false;
			}

			return true;
		});

		Inertia::handleExceptionsUsing(function (ExceptionResponse $response) {
			if ($response->response instanceof JsonResponse) {
				return $response;
			}

			if (in_array($response->statusCode(), [403, 404, 500, 503])) {
				return $response->render(
					'ErrorPage',
					array_merge([
						'status' => $response->statusCode(),
						'message' => $response->exception->getMessage(),
					], !app()->isProduction() ? ['trace' => $response->exception->getTraceAsString()] : [])
				);
			}

			return $response;
		});

		/*\DB::listen(function ($query) {
			dump($query);
		});*/
	}

	public function register()
	{
		if (!str_starts_with(request()->path(), 'api') || isset($_SERVER['LARAVEL_OCTANE'])) {
			$this->app->register(AdminPanelProvider::class);
		}

		$this->app->singleton(ResponseFactory::class, PageResponseFactory::class);
	}
}
