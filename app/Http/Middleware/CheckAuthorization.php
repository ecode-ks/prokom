<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAuthorization
{
    public function handle(Request $request, Closure $next, string ...$allowedRoles): Response
    {
        if (session('active') !== true) {
            return redirect()->route('login');
        }

        if ($allowedRoles !== [] && !in_array((string) session('role'), $allowedRoles, true)) {
            abort(404);
        }

        return $next($request);
    }
}
