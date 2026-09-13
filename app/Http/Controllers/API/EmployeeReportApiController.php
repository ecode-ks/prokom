<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\Api\EmployeeReportApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmployeeReportApiController extends Controller
{
    public function __construct(protected EmployeeReportApiService $service)
    {
    }

    public function read(Request $request, int $companyId, int $employeeId, string $role)
    {
        $validator = Validator::make([
            'company_id' => $companyId,
            'employee_id' => $employeeId,
            'role' => $role,
            'from_date' => $request->query('from_date'),
            'to_date' => $request->query('to_date'),
        ], [
            'company_id' => 'required|integer|exists:companies,id',
            'employee_id' => 'required|integer|exists:employees,id',
            'role' => 'required|in:company,hr,manager,employee,finance',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => __('pages.fields_invalid')], 422);
        }

        if (!in_array($role, ['company', 'hr'], true)) {
            $employee = $this->service->getEmployee($companyId, $employeeId);
            if (!$employee || (string) $employee->role !== $role) {
                return response()->json(['error' => __('pages.report_no_access')], 403);
            }
        }

        $report = $this->service->getReport(
            $request->query('from_date'),
            $request->query('to_date'),
            $companyId,
            $employeeId
        );

        if ($report === null) {
            return response()->json(['error' => __('pages.employee_not_found')], 404);
        }

        return response()->json($report);
    }
}
