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

        // Run SetTenantConnection BEFORE auth (AuthenticatesRequests) AND before
        // SubstituteBindings. This way auth()->user() (and its admin_permissions)
        // and route-model binding both resolve on the correct tenant DB — not the
        // default/master connection.
        $middleware->priority([
            \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\SetTenantConnection::class,
            \Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class,
            \Illuminate\Routing\Middleware\ThrottleRequestsWithRedis::class,
            \Illuminate\Contracts\Session\Middleware\AuthenticatesSessions::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \Illuminate\Auth\Middleware\Authorize::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
