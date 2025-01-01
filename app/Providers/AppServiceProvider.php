<?php

namespace App\Providers;

use App\Http\Controllers\GatewayController;
use App\Models\Routing;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    public function register(): void
    {
    }

    public function boot(): void
    {
        $this->addDynamicRedirects();
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
