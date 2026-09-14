<?php

namespace App\Http\Controllers\API;

use App\Services\Api\ExtraHoursApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class ExtraHoursApiController extends Controller
{
    protected ExtraHoursApiService $service;

    public function __construct(ExtraHoursApiService $service)
    {
        $this->service = $service;
    }

    public function create(Request $request, int $companyId, int $employeeId, string $role)
    {
        if (!$this->canManage($role)) {
            return json_encode(['error' => __('pages.no_access')]);
        }

        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'employee_id' => 'required|integer',
            'hours' => 'required|numeric',
            'type' => 'required|string',
        ]);

        if ($validator->fails() || !$this->service->resolveType($request->input('type'))) {
            return json_encode(['error' => __('pages.fields_invalid')]);
        }

        $data = $request->only(['date', 'employee_id', 'hours', 'type']);
        try {
            $this->service->createExtraHour($data, $companyId);
            return json_encode(['success' => __('pages.extra_hours_created')]);
        } catch (\Exception $e) {
            return json_encode(['error' => __('pages.try_again')]);
        }
    }

    public function read(Request $request, int $companyId, int $employeeId, string $role)
    {
        if (!$this->canView($role)) {
            return json_encode(['error' => __('pages.no_access')]);
        }

        $search = $request->query('search', '');
        $scopedEmployeeId = $role === 'employee' ? (int) $employeeId : null;
        $extraHours = $this->service->getExtraHours(10, $search, $companyId, $scopedEmployeeId);

        if ($role === 'employee') {
            return json_encode([
                'extraHours' => $extraHours,
                'employees' => collect(),
                'types' => $this->service->getTypes(),
                'canManage' => false,
                'role' => $role,
            ]);
        }

        return json_encode([
            'extraHours' => $extraHours,
            'employees' => $this->service->getAllEmployees($companyId),
            'types' => $this->service->getTypes(),
            'canManage' => $this->canManage($role),
            'role' => $role,
        ]);
    }

    public function edit(int $id, int $companyId, int $employeeId, string $role)
    {
        if (!$this->canManage($role)) {
            return json_encode(['error' => __('pages.no_access')]);
        }

        if (!$this->service->findOrFail($id, $companyId)) {
            return json_encode(['error' => __('pages.extra_hours_not_found')]);
        }

        return json_encode([
            'editExtraHour' => $this->service->getExtraHourById($id, $companyId),
            'employees' => $this->service->getAllEmployees($companyId),
            'types' => $this->service->getTypes(),
            'role' => $role,
        ]);
    }

    public function update(Request $request, int $companyId, int $employeeId, string $role)
    {
        if (!$this->canManage($role)) {
            return json_encode(['error' => __('pages.no_access')]);
        }

        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:extra_hours,id',
            'date' => 'required|date',
            'employee_id' => 'required|integer',
            'hours' => 'required|numeric',
            'type' => 'required|string',
        ]);

        if ($validator->fails() || !$this->service->resolveType($request->input('type'))) {
            return json_encode(['error' => __('pages.fields_invalid')]);
        }

        $id = (int) $request->id;
        $data = $request->only(['date', 'employee_id', 'hours', 'type']);

        try {
            $this->service->updateExtraHour($id, $data, $companyId);
            return json_encode(['success' => __('pages.extra_hours_updated')]);
        } catch (\Exception $e) {
            return json_encode(['error' => __('pages.try_again')]);
        }
    }

    public function delete(int $id, int $companyId, int $employeeId, string $role)
    {
        if (!$this->canManage($role)) {
            return json_encode(['error' => __('pages.no_access')]);
        }

        if (!$this->service->findOrFail($id, $companyId)) {
            return json_encode(['error' => __('pages.extra_hours_not_found')]);
        }

        try {
            $this->service->deleteExtraHour($id, $companyId);
            return json_encode(['success' => __('pages.extra_hours_deleted')]);
        } catch (\Exception $e) {
            return json_encode(['error' => __('pages.try_again')]);
        }
    }

    private function canManage(string $role): bool
    {
        return in_array($role, ['company', 'manager', 'hr'], true);
    }

    private function canView(string $role): bool
    {
        return in_array($role, ['company', 'manager', 'hr', 'employee'], true);
    }
}
