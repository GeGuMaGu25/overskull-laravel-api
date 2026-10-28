<?php

namespace App\Exceptions;

use App\Support\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

/**
 * Converts any exception thrown inside the API into the standard JSON error format.
 */
class ApiExceptionRenderer
{
    public function __invoke(Throwable $e, Request $request): ?JsonResponse
    {
        if (! $this->shouldRender($request)) {
            return null;
        }

        return match (true) {
            $e instanceof ValidationException => ApiResponse::error(
                $e->validator->errors()->first(),
                $e->status,
                $e->errors(),
            ),
            $e instanceof AuthenticationException => ApiResponse::error('No autenticado', 401),
            $e instanceof NotFoundHttpException => ApiResponse::error('Recurso no encontrado', 404),
            $e instanceof MethodNotAllowedHttpException => ApiResponse::error('Método HTTP no permitido para esta ruta', 405),
            $e instanceof HttpExceptionInterface => ApiResponse::error(
                $e->getMessage() ?: 'Error en la solicitud',
                $e->getStatusCode(),
            ),
            default => $this->serverError($e),
        };
    }

    private function shouldRender(Request $request): bool
    {
        return $request->is('api/*') || $request->expectsJson();
    }

    private function serverError(Throwable $e): JsonResponse
    {
        $message = config('app.debug') ? $e->getMessage() : 'Error interno del servidor';

        return ApiResponse::error($message, 500);
    }
}
