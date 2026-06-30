<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Globálne middleware
        $middleware->append(\App\Http\Middleware\EnsureBuildAssets::class);
        
        // Aliasy middleware
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'prevent.impersonation.admin' => \App\Http\Middleware\PreventImpersonationAdminAccess::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Custom exception handling pre JSON responses
        $exceptions->render(function (Throwable $e, $request) {
            // Pre AJAX/JSON požiadavky vratime vždy JSON response
            if ($request->expectsJson() || $request->isXmlHttpRequest()) {
                return response()->json([
                    'success' => false,
                    'message' => app()->environment('production') 
                        ? 'Nastala neočakávaná chyba. Skúste to neskôr.' 
                        : $e->getMessage(),
                    'error' => app()->environment('production') ? null : [
                        'type' => get_class($e),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                    ]
                ], 500);
            }
        });
    })
    ->withSchedule(function (Schedule $schedule) {
        // Kontrola expirovaných inzerátov každé 4 hodiny
        $schedule->command('ads:expire --force')
            ->everyFourHours()
            ->withoutOverlapping()
            ->runInBackground()
            ->emailOutputOnFailure(config('mail.admin_email', 'admin@erotikon.sk'));

        // Kontrola expirovaných inzerátov aj o polnoci (pre istotu)
        $schedule->command('ads:expire --force')
            ->dailyAt('00:15')
            ->withoutOverlapping()
            ->runInBackground()
            ->emailOutputOnFailure(config('mail.admin_email', 'admin@erotikon.sk'));
    })->create();
