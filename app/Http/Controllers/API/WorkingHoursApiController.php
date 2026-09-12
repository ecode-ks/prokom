<?php

namespace App\Http\Controllers\API;

use App\Services\Api\WorkingHoursApiService;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WorkingHoursApiController extends Controller
{
    protected WorkingHoursApiService $service;

    public function __construct(WorkingHoursApiService $service)
    {
        $this->service = $service;
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'employee_id' => 'required|integer|exists:employees,id',
            'company_id' => 'required|integer|exists:companies,id',
            'check_in' => 'required|date|before:check_out',
            'check_out' => 'required|date|after:check_in'
        ]);

        if ($validator->fails()) {
            return json_encode(['error' => __('pages.fields_invalid')]);
        }

        $data = $request->only(['date', 'employee_id', 'check_in', 'check_out']);
        try {
            $data['hours'] = Carbon::parse($data['check_in'])->diff(Carbon::parse($data['check_out']))->format('%H:%I');
            $this->service->createWorkingHour($data, (int) $request->input('company_id'));
            return json_encode(['success' => __('pages.working_hour_created')]);
        } catch (\Exception $e) {
            return json_encode(['error' => __('pages.register_failed_reason', ['message' => $e->getMessage()])]);
        }
    }

    public function read(Request $request, int $companyId, int $employeeId, string $role)
    {
        $search = $request->query('search', '');
        $employees = $this->service->getAllEmployees($companyId);
        $fromDate = null;
        $toDate = null;
        $workingHoursTotalLabel = null;

        if ($role === 'employee' && $employeeId) {
            $fromDate = $request->query('from_date') ?: null;
            $toDate = $request->query('to_date') ?: null;
            $workingHours = $this->service->getEmployeeWorkingHours($companyId, $employeeId, $fromDate, $toDate, 10);
            $workingHoursTotalLabel = $this->service->getEmployeeWorkingHoursTotal($companyId, $employeeId, $fromDate, $toDate);
        } else {
            $workingHours = $this->service->getWorkingHours(10, $search, $companyId);
        }

        return json_encode([
            'workingHours' => $workingHours,
            'workingHoursTotalLabel' => $workingHoursTotalLabel,
            'employees' => $employees,
            'role' => $role,
            'fromDate' => $fromDate,
            'toDate' => $toDate
        ]);
    }

    public function delete(Request $request, int $id)
    {
        $companyId = (int) $request->input('company_id');
        if (!$companyId) {
            return json_encode(['error' => __('pages.fields_invalid')]);
        }

        if (!$this->service->findOrFail($id, $companyId)) {
            return json_encode(['error' => __('pages.working_hour_not_found')]);
        }

        try {
            $this->service->deleteWorkingHour($id, $companyId);
            return json_encode(['success' => __('pages.working_hour_deleted')]);
        } catch (\Exception $e) {
            return json_encode(['error' => __('pages.try_again')]);
        }
    }
}
