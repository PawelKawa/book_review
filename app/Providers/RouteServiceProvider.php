<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;

class RouteServiceProvider extends ServiceProvider
{
	/**
	 * The path to the "home" route for your application.
	 *
	 * This is used by Laravel authentication to redirect users after login.
	 *
	 * @var string
	 */
	public const HOME = '/home';

	/**
	 * Define your route model bindings, pattern filters, etc.
	 */
	public function boot(): void
	{
		$this->configureRateLimiting();

		$this->routes(function () {
			Route::middleware('web')
				->group(base_path('routes/web.php'));
		});
	}

	/**
	 * Configure the rate limiters for the application.
	 */
	protected function configureRateLimiting(): void
	{
		RateLimiter::for('reviews', function (Request $request) {
			return Limit::perHour(3)->by($request->user()?->id ?: $request->ip());
		});
	}
}
