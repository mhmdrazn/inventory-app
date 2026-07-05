<?php

use App\Http\Middleware\RoleMiddleware;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);
    })->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        // Unified JSON envelope for API errors: { success, message, data }.
        $exceptions->render(function (Throwable $e, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            [$status, $message, $data] = match (true) {
                $e instanceof ValidationException => [
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    'Validasi gagal.',
                    ['errors' => $e->errors()],
                ],
                $e instanceof AuthenticationException => [
                    Response::HTTP_UNAUTHORIZED,
                    'Tidak terautentikasi.',
                    null,
                ],
                $e instanceof AuthorizationException => [
                    Response::HTTP_FORBIDDEN,
                    $e->getMessage() ?: 'Akses ditolak.',
                    null,
                ],
                $e instanceof ModelNotFoundException,
                $e instanceof NotFoundHttpException => [
                    Response::HTTP_NOT_FOUND,
                    'Resource tidak ditemukan.',
                    null,
                ],
                $e instanceof HttpExceptionInterface => [
                    $e->getStatusCode(),
                    $e->getMessage() ?: Response::$statusTexts[$e->getStatusCode()] ?? 'HTTP error.',
                    null,
                ],
                default => [
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan pada server.',
                    config('app.debug') ? ['exception' => class_basename($e), 'trace' => collect($e->getTrace())->take(5)->all()] : null,
                ],
            };

            return response()->json([
                'success' => false,
                'message' => $message,
                'data' => $data,
            ], $status);
        });
    })->create();
