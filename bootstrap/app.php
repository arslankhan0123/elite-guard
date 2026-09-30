<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        then: function () {
            \Illuminate\Support\Facades\Route::middleware('web')
                ->group(base_path('routes/master.php'));
        },
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\SetTenantConnection::class,
        ]);
        $middleware->api(prepend: [
            \App\Http\Middleware\SetTenantConnectionApi::class,
        ]);
        $middleware->alias([
            'superadmin'   => \App\Http\Middleware\IsSuperAdmin::class,
            'master.admin' => \App\Http\Middleware\IsMasterAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
