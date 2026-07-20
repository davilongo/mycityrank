<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAgencia
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_if(!$request->user()->isAgencia() && !$request->user()->isAdmin(), 403);

        return $next($request);
    }
}
