<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPaymentExpired
{
    public function handle(Request $request, Closure $next): Response
    {
        if (session('active') !== true) {
            return $next($request);
        }

        if (session('role') === 'admin' || session('payment_expired') !== true) {
            return $next($request);
        }

        $role = (string) session('role');

        if ($role !== 'company') {
            session()->flush();

            return redirect()
                ->route('login')
                ->with('error', 'Paketa e kompanisë ka skaduar. Qasja është e bllokuar. Kontaktoni kompaninë për të rinovuar paketën.');
        }

        $allowedRouteNames = [
            'profile',
            'update.profile.password',
            'update.company.language',
            'change.package',
            'logout',
        ];

        if ($request->routeIs(...$allowedRouteNames)) {
            return $next($request);
        }

        return redirect()
            ->route('profile')
            ->with('error', 'Paketa juaj ka skaduar. Aktivizoni një paketë për të vazhduar.');
    }
}
