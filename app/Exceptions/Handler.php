<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Exceptions\ProvidedService\RegistrationOfProvidedService;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Override;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    #[Override]
    public function register(): void
    {
        $this->renderable(fn (InUse $e) => response($e->getMessage(), 409));
        $this->renderable(fn (RegistrationOfProvidedService $e) => response()->json(['message' => $e->getMessage()], 400));
    }

    #[Override]
    protected function invalidJson($request, ValidationException $exception): JsonResponse
    {
        return response()->json([
            'errors' => $exception->errors(),
        ], $exception->status);
    }
}
