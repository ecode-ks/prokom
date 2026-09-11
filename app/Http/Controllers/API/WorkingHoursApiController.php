<?php

namespace App\Http\Controllers\API;

use App\Services\WorkingHoursService;
use App\Services\EmployeesService;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WorkingHoursApiController extends Controller
{
    protected EmployeesService $employeeservice;
    protected WorkingHoursService $service;
    public function __construct(WorkingHoursService $service, EmployeesService $employeeservice)
    {
        $this->service = $service;
        $this->employeeservice = $employeeservice;
    }
    //---------------
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'employee_id' => 'required|integer|exists:employees,id',
            'check_in' => 'required|date|before:check_out',
            'check_out' => 'required|date|after:check_in'
        ]);
        if ($validator->fails()) {
            return json_encode(['error' => __('pages.fields_invalid')]);
        } else {
            $data = $request->only(['date', 'employee_id', 'check_in', 'check_out']);
            try {
                $data['hours'] = Carbon::parse($data['check_in'])->diff(Carbon::parse($data['check_out']))->format('%H:%I');
                $this->service->createWorkingHour($data);
                return json_encode(['success' => __('pages.working_hour_created')]);
            } catch (\Exception $e) {
                return json_encode(['error' => __('pages.register_failed_reason', ['message' => $e->getMessage()])]);
            }
        }
    }
    //---------------
    public function read(Request $request, int $companyId, int $employeeId, string $role)
    {
        $search = $request->query('search', '');
        $employees = $this->employeeservice->getAllEmployees();
        $fromDate = null;
        $toDate = null;
        $workingHoursTotalLabel = null;

        if ($role === 'employee' && $employeeId) {
            $fromDate = $request->query('from_date') ?: null;
            $toDate = $request->query('to_date') ?: null;
            $workingHours = $this->service->getEmployeeWorkingHours($employeeId, $fromDate, $toDate, 10);
            $workingHoursTotalLabel = $this->service->getEmployeeWorkingHoursTotal($employeeId, $fromDate, $toDate);
        } else {
            $workingHours = $this->service->getWorkingHours(10, $search);
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
    //---------------
    public function delete(int $id)
    {
        if (!$this->service->findOrFail($id)) {
            return json_encode(['error' => __('pages.working_hour_not_found')]);
        } else {
            try {
                $this->service->deleteWorkingHour($id);
                return json_encode(['success' => __('pages.working_hour_deleted')]);
            } catch (\Exception $e) {
                return json_encode(['error' => __('pages.try_again')]);
            }
        }
    }
}
