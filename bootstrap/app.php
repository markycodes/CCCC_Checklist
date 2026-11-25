<?php

use App\Http\Middleware\EnsureAdministrator;
use App\Http\Middleware\EnsureHttps;
use App\Http\Middleware\EnsureProgramHead;
use App\Http\Middleware\EnsureRegistrar;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->prependToGroup('web', EnsureHttps::class);

        $middleware->alias([
            'program-head' => EnsureProgramHead::class,
            'administrator' => EnsureAdministrator::class,
            'registrar' => EnsureRegistrar::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
