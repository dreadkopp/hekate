<?php

namespace App\Providers;

use App\Http\Controllers\GatewayController;
use App\Models\AccessToken;
use App\Models\Client;
use App\Models\Routing;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        // https://wearnhardt.com/2020/09/improving-laravel-sanctum-personal-access-token-performance/
        Sanctum::usePersonalAccessTokenModel(AccessToken::class);
    }

    public function boot(): void
    {
        $this->addDynamicRedirects();

        Relation::requireMorphMap();
        Relation::enforceMorphMap([
            'kerberos-user' => User::class,
            'kerberos-client' => Client::class
        ]);
    }

    protected function addDynamicRedirects(): void
    {
        // to migrate, route service provider will be booted.... and will not be able to access Redirection model
        try {
            if (!Schema::hasTable((new Routing())->getTable())) {
                return;
            }
        } catch (QueryException) {
            // no database connection (yet)
            return;
        }


        $routes = Route::getRoutes();

        Routing::all()->each(function (Routing $routing) use ($routes): void {
            $routes->add(Route::any($routing->path.'/{path?}',[GatewayController::class, 'proxy']))
                ->name('proxy.to.'.$routing->path);

        });
    }
}
