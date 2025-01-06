<?php

use App\Console\Commands\ClearExpiredUserTokens;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->shouldRenderJsonWhen(fn() => true);
    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('app:clear-expired-user-tokens')->daily();
    })
    ->create();
