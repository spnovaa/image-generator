<?php

use App\Exceptions\CaptionGeneratorException;
use App\Exceptions\GeneralDatabaseException;
use App\Exceptions\ImageGeneratorException;
use App\Exceptions\ObjectStorageException;
use App\Exceptions\RabbitMQException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web:      __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health:   '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // The /converts endpoint is API-style and not protected by sessions.
        $middleware->validateCsrfTokens(except: [
            'converts',
            'converts/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Map domain exceptions to JSON responses with sensible HTTP status codes.
        $exceptions->render(function (GeneralDatabaseException $e) {
            return response()->json(['status' => 'error', 'message' => 'Database error.'], 530);
        });
        $exceptions->render(function (RabbitMQException $e) {
            return response()->json(['status' => 'error', 'message' => 'Message broker error.'], 502);
        });
        $exceptions->render(function (ObjectStorageException $e) {
            return response()->json(['status' => 'error', 'message' => 'Object storage error.'], 502);
        });
        $exceptions->render(function (CaptionGeneratorException $e) {
            return response()->json(['status' => 'error', 'message' => 'Caption generator unavailable.'], 502);
        });
        $exceptions->render(function (ImageGeneratorException $e) {
            return response()->json(['status' => 'error', 'message' => 'Image generator unavailable.'], 502);
        });
    })
    ->create();
