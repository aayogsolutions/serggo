<?php

use App\Http\Middleware\AdminAuthMiddleware;
use App\Http\Middleware\ActiveBranchCheck;
use App\Http\Middleware\BranchMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->appendToGroup('Admin-auth', [
            AdminAuthMiddleware::class,
        ]);
        $middleware->appendToGroup('active_branch_check', [
            ActiveBranchCheck::class,
        ]);
        $middleware->appendToGroup('branch', [
            BranchMiddleware::class,
        ]);
       
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
