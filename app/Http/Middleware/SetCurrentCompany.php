<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\CurrentCompanyService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

readonly class SetCurrentCompany
{
    /**
     * @psalm-api
     */
    public function __construct(private CurrentCompanyService $currentCompanyService)
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \App\Http\Request $request */
        $this->currentCompanyService->setCompanyId($request->company()?->id);

        return $next($request);
    }
}
