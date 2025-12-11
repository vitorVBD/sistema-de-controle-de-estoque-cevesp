<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);
    })
    ->withSchedule(function (\Illuminate\Console\Scheduling\Schedule $schedule): void {
        $schedule->command('estoque:checar-alertas')->daily();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Sua sessão expirou. Por favor, recarregue a página e tente novamente.',
                    'reload' => true,
                    'csrf_expired' => true
                ], 419);
            }

            // Para requisições de login, redirecionar para a página de login com mensagem
            if ($request->is('login') || $request->routeIs('login')) {
                return redirect()->route('login')
                    ->withInput($request->except('password', '_token'))
                    ->withErrors(['_token' => 'Sua sessão expirou. Por favor, tente fazer login novamente.']);
            }

            return redirect()->back()
                ->withInput($request->except('password', '_token'))
                ->withErrors(['_token' => 'Sua sessão expirou. Por favor, recarregue a página e tente novamente.']);
        });
    })->create();
