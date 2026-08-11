<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class ReportsRepository
{
    //---------------
    public function generateEmployeeReport(string $fromDate, string $toDate, int $employeeId, int $companyId)
    {
        return DB::table('employees')
            ->where('id', $employeeId)
            ->where('company_id', $companyId)
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->orderByDesc('id')
            ->cursorPaginate(15);
    }
}
