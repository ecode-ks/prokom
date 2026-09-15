<?php

namespace App\Http\Middleware;

use App\Models\CompaniesModel;
use App\Models\EmployeesModel;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApiPaymentExpired
{
    public function handle(Request $request, Closure $next): Response
    {
        $account = $request->user();
        $companyId = $this->resolveCompanyId($account);

        if ($companyId === null) {
            return $next($request);
        }

        $company = CompaniesModel::query()
            ->whereKey($companyId)
            ->first(['id', 'payment_expire']);

        if (!$company || empty($company->payment_expire)) {
            return $next($request);
        }

        $paymentExpired = now()->greaterThan(
            \Illuminate\Support\Carbon::parse($company->payment_expire)
        );

        if (!$paymentExpired) {
            return $next($request);
        }

        $messageKey = $account instanceof CompaniesModel
            ? 'pages.package_expired_company'
            : 'pages.package_expired_staff';
        $message = __($messageKey);

        return response()->json([
            'code' => 'payment_expired',
            'error' => $message,
            'alert' => $message,
            'payment_expire' => \Illuminate\Support\Carbon::parse($company->payment_expire)->toDateString(),
        ], 403);
    }

    private function resolveCompanyId($account): ?int
    {
        if ($account instanceof CompaniesModel) {
            return (int) $account->id;
        }

        if ($account instanceof EmployeesModel && !empty($account->company_id)) {
            return (int) $account->company_id;
        }

        return null;
    }
}
