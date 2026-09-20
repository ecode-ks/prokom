<?php

namespace App\Http\Controllers\API;

use App\Services\Api\VacationsApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class VacationsApiController extends Controller
{
    protected VacationsApiService $service;

    public function __construct(VacationsApiService $service)
    {
        $this->service = $service;
    }

    public function create(Request $request, int $companyId, int $employeeId, string $role)
    {
        $isEmployee = in_array(strtolower($role), ['employee', 'finance'], true);

        $validator = Validator::make($request->all(), [
            'employee_id' => $isEmployee ? 'nullable' : 'required|integer|exists:employees,id',
            'from_date' => 'required|date',
            'to_date' => 'required|date',
            'type' => 'required|string',
            'comment' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return json_encode(['error' => __('pages.fields_invalid')]);
        }

        $data = $request->only(['from_date', 'to_date', 'type', 'comment']);
        $data['company_id'] = $companyId;
        $data['employee_id'] = $isEmployee ? $employeeId : (int) $request->input('employee_id');

        try {
            $this->service->createVacation($data);
            return json_encode(['success' => __('pages.vacation_created')]);
        } catch (\Exception $e) {
            return json_encode(['error' => __('pages.try_again')]);
        }
    }

    public function read(Request $request, int $companyId, int $employeeId, string $role)
    {
        $search = $request->query('search', '');
        $statuses = array_values(array_filter((array) $request->query('statuses', [])));
        $selectedType = $this->service->resolveType($request->query('type'));
        $isEmployee = in_array(strtolower($role), ['employee', 'finance'], true);
        $scopedEmployeeId = $isEmployee ? (int) $employeeId : null;
        $employees = $isEmployee ? collect() : $this->service->getAllEmployees($companyId);

        return json_encode([
            'employees' => $employees,
            'vacations' => $this->service->getVacations(10, $search, $statuses, $companyId, $scopedEmployeeId, $selectedType),
            'vacationStats' => $this->service->getVacationStatistics($search, $companyId, $scopedEmployeeId),
            'selectedType' => $selectedType ?? '',
            'types' => $this->service->getTypes(),
            'isEmployee' => $isEmployee,
            'canSelectEmployee' => !$isEmployee,
            'role' => $role,
        ]);
    }

    public function edit(int $id, int $companyId, int $employeeId, string $role)
    {
        if (!$this->service->findOrFail($id, $companyId)) {
            return json_encode(['error' => __('pages.vacation_not_found')]);
        }

        $editVacation = $this->service->getVacationById($id, $companyId);
        $isEmployee = in_array(strtolower($role), ['employee', 'finance'], true);

        if ($isEmployee && (int) $editVacation->employee_id !== (int) $employeeId) {
            return json_encode(['error' => __('pages.vacation_no_edit')]);
        }

        return json_encode([
            'employees' => $isEmployee ? collect() : $this->service->getAllEmployees($companyId),
            'editVacation' => $editVacation,
            'types' => $this->service->getTypes(),
            'isEmployee' => $isEmployee,
            'canSelectEmployee' => !$isEmployee,
            'role' => $role,
        ]);
    }

    public function update(Request $request, int $companyId, int $employeeId, string $role)
    {
        $isEmployee = in_array(strtolower($role), ['employee', 'finance'], true);

        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:vacations,id',
            'employee_id' => $isEmployee ? 'nullable' : 'required|integer|exists:employees,id',
            'from_date' => 'required|date',
            'to_date' => 'required|date',
            'type' => 'required|string',
            'comment' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return json_encode(['error' => __('pages.fields_invalid')]);
        }

        $id = (int) $request->id;
        $existing = $this->service->getVacationById($id, $companyId);

        if (!$existing) {
            return json_encode(['error' => __('pages.vacation_not_found')]);
        }

        if ($isEmployee && (int) $existing->employee_id !== (int) $employeeId) {
            return json_encode(['error' => __('pages.vacation_no_edit')]);
        }

        $data = $request->only(['from_date', 'to_date', 'type', 'comment']);
        $data['employee_id'] = $isEmployee ? (int) $employeeId : (int) $request->input('employee_id');

        try {
            $this->service->updateVacation($id, $data, $companyId);
            return json_encode(['success' => __('pages.vacation_updated')]);
        } catch (\Exception $e) {
            return json_encode(['error' => __('pages.try_again')]);
        }
    }

    public function delete(int $id, int $companyId, int $employeeId, string $role)
    {
        if (!$this->service->findOrFail($id, $companyId)) {
            return json_encode(['error' => __('pages.vacation_not_found')]);
        }

        $vacation = $this->service->getVacationById($id, $companyId);
        $isEmployee = in_array(strtolower($role), ['employee', 'finance'], true);
        if ($isEmployee && (int) $vacation->employee_id !== (int) $employeeId) {
            return json_encode(['error' => __('pages.vacation_no_delete')]);
        }

        try {
            $this->service->deleteVacation($id, $companyId);
            return json_encode(['success' => __('pages.vacation_deleted')]);
        } catch (\Exception $e) {
            return json_encode(['error' => __('pages.try_again')]);
        }
    }

    public function updateStatus(int $id, string $status, int $companyId, int $employeeId, string $role)
    {
        $role = strtolower($role);
        $status = urldecode($status);

        if (!in_array($role, ['company', 'hr'], true)) {
            return json_encode(['error' => __('pages.vacation_no_status')]);
        }

        if (!in_array($status, ['Approved', 'Refused'], true)) {
            return json_encode(['error' => __('pages.invalid_status')]);
        }

        if (!$this->service->findOrFail($id, $companyId)) {
            return json_encode(['error' => __('pages.vacation_not_found')]);
        }

        try {
            if (!$this->service->updateVacationStatus($id, $status, $role, $companyId)) {
                return json_encode(['error' => __('pages.invalid_status')]);
            }

            return json_encode(['success' => __('pages.vacation_status_updated')]);
        } catch (\Exception $e) {
            return json_encode(['error' => __('pages.try_again')]);
        }
    }
}
