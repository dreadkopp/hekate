<?php

namespace App\Observers;

use App\Models\Routing;
use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class RoutingChangedObserver
{
    public function saved(Routing $routing) :void
    {
        $basePath = ltrim($routing->path,'/');
        $key = "route-lookup:$basePath";
        Cache::store('apc')->forget($key);
        Cache::forget($key);
        (new AppServiceProvider(app()))->addDynamicRedirects();
        Artisan::call('route:clear');
        Artisan::call('route:cache');
    }
}
