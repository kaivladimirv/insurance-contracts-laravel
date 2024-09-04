<?php

namespace App\Providers;

use Override;
use App\Facades\Auth;
use App\Models\InsuredPerson;
use App\ReadModels\ContractFetcher;
use App\ReadModels\ContractServiceFetcher;
use App\ReadModels\InsuredPersonFetcher;
use App\ReadModels\PersonFetcher;
use App\ReadModels\ProvidedServiceFetcher;
use App\ReadModels\ServiceFetcher;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    final public const string HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    #[Override]
    public function boot(): void
    {
        RateLimiter::for('api', fn(Request $request) => Limit::perMinute(60)->by($request->user()?->id ?: $request->ip()));

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });

        Route::bind('person', fn($personId) => app(PersonFetcher::class)->getOne(Auth::company()->id, $personId));
        Route::bind('service', fn($serviceId) => app(ServiceFetcher::class)->getOne(Auth::company()->id, $serviceId));
        Route::bind('contract', fn($contractId) => app(ContractFetcher::class)->getOne(Auth::company()->id, $contractId));
        Route::bind(
            'contractService',
            fn($serviceId, $route) => app(ContractServiceFetcher::class)->getOne($route->parameter('contract_id'), $serviceId)
        );

        Route::bind('insuredPerson', function ($insuredPersonId, $route) {
            if ($route->hasParameter('contract_id')) {
                return app(InsuredPersonFetcher::class)->getOne($route->parameter('contract_id'), $insuredPersonId);
            } else {
                return InsuredPerson::query()->findOrFail($insuredPersonId);
            }
        });

        Route::bind(
            'providedService',
            fn($providedServiceId, $route) =>
                app(ProvidedServiceFetcher::class)->getOne($route->parameter('insured_person_id'), $providedServiceId)
        );
    }
}
