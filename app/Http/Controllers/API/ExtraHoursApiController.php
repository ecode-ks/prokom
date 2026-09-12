<?php

namespace App\Http\Controllers\API;

use App\Services\Api\ExtraHoursApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;

class ExtraHoursApiController extends Controller
{
    protected ExtraHoursApiService $service;

    private const TYPES = [
        'Orët jashtë orarit',
        'Orët në vikend',
    ];

    public function __construct(ExtraHoursApiService $service)
    {
        $this->service = $service;
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'employee_id' => 'required|integer|exists:employees,id',
            'company_id' => 'required|integer|exists:companies,id',
            'hours' => 'required|numeric|min:0.01',
            'type' => ['required', 'string', Rule::in(self::TYPES)],
        ]);

        if ($validator->fails()) {
            return json_encode(['error' => __('pages.fields_invalid')]);
        }

        $data = $request->only(['date', 'employee_id', 'hours', 'type']);
        try {
            $this->service->createExtraHour($data, (int) $request->input('company_id'));
            return json_encode(['success' => __('pages.extra_hours_created')]);
        } catch (\Exception $e) {
            return json_encode(['error' => __('pages.try_again')]);
        }
    }

    public function read(Request $request, int $companyId, int $employeeId, string $role)
    {
        $search = $request->query('search', '');
        $scopedEmployeeId = $role === 'employee' ? (int) ($employeeId ?? 0) : null;
        $extraHours = $this->service->getExtraHours(10, $search, $companyId, $scopedEmployeeId);

        if ($role === 'employee') {
            return json_encode([
                'extraHours' => $extraHours,
                'employees' => collect(),
                'types' => self::TYPES,
                'role' => $role,
            ]);
        }

        $employees = $this->service->getAllEmployees($companyId);
        if ($employees->isEmpty()) {
            return json_encode(['error' => $role === 'manager' ? __('pages.no_employees') : __('pages.register_employees_before_extra')]);
        }

        return json_encode([
            'extraHours' => $extraHours,
            'employees' => $employees,
            'types' => self::TYPES,
            'role' => $role,
        ]);
    }

    public function edit(int $id, int $companyId)
    {
        if (!$this->service->findOrFail($id, $companyId)) {
            return json_encode(['error' => __('pages.extra_hours_not_found')]);
        }

        $employees = $this->service->getAllEmployees($companyId);
        if ($employees->isEmpty()) {
            return json_encode(['error' => __('pages.register_employees_before_extra')]);
        }

        return json_encode([
            'editExtraHour' => $this->service->getExtraHourById($id, $companyId),
            'employees' => $employees,
            'types' => self::TYPES,
        ]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric|min:1|exists:extra_hours,id',
            'company_id' => 'required|integer|exists:companies,id',
            'date' => 'required|date',
            'employee_id' => 'required|integer|exists:employees,id',
            'hours' => 'required|numeric|min:0.01',
            'type' => ['required', 'string', Rule::in(self::TYPES)],
        ]);

        if ($validator->fails()) {
            return json_encode(['error' => __('pages.fields_invalid')]);
        }

        $companyId = (int) $request->input('company_id');
        $id = (int) $request->id;
        $data = $request->only(['date', 'employee_id', 'hours', 'type']);

        try {
            $this->service->updateExtraHour($id, $data, $companyId);
            return json_encode(['success' => __('pages.extra_hours_updated')]);
        } catch (\Exception $e) {
            return json_encode(['error' => __('pages.try_again')]);
        }
    }

    public function delete(Request $request, int $id)
    {
        $companyId = (int) $request->input('company_id');
        if (!$companyId) {
            return json_encode(['error' => __('pages.fields_invalid')]);
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
}
