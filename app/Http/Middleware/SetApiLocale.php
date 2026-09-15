<?php

namespace App\Http\Middleware;

use App\Models\CompaniesModel;
use App\Models\EmployeesModel;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetApiLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        app()->setLocale($this->resolveLanguage($request->user()));

        return $next($request);
    }

    private function resolveLanguage($account): string
    {
        $companyId = null;

        if ($account instanceof CompaniesModel) {
            $companyId = (int) $account->id;
        } elseif ($account instanceof EmployeesModel) {
            $companyId = (int) $account->company_id;
        }

        if ($companyId <= 0) {
            return 'sq';
        }

        $language = CompaniesModel::query()
            ->whereKey($companyId)
            ->value('language');

        return in_array($language, ['sq', 'en', 'de'], true)
            ? $language
            : 'sq';
    }
}
