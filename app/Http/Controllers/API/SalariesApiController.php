<?php

namespace App\Http\Controllers\API;

use App\Services\Api\SalariesApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class SalariesApiController extends Controller
{
    protected SalariesApiService $service;

    public function __construct(SalariesApiService $service)
    {
        $this->service = $service;
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|integer|exists:employees,id',
            'company_id' => 'required|integer|exists:companies,id',
            'salary' => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return json_encode(['error' => __('pages.fields_invalid')]);
        }

        $data = $request->only(['employee_id', 'salary']);
        try {
            $this->service->createSalary($data, (int) $request->input('company_id'));
            return json_encode(['success' => __('pages.salary_created')]);
        } catch (\Exception $e) {
            return json_encode(['error' => __('pages.try_again')]);
        }
    }

    public function read(Request $request, int $companyId, int $employeeId, string $role)
    {
        $search = $request->query('search', '');
        $scopedEmployeeId = $role === 'employee' ? (int) ($employeeId ?? 0) : null;
        $salaries = $this->service->getSalaries(10, $search, $companyId, $scopedEmployeeId);

        if ($role === 'employee') {
            return json_encode([
                'salaries' => $salaries,
                'employees' => collect(),
                'role' => $role,
            ]);
        }

        $employees = $this->service->getAllEmployees($companyId);
        if ($employees->isEmpty()) {
            return json_encode(['error' => __('pages.register_employees_before_salary')]);
        }

        return json_encode([
            'salaries' => $salaries,
            'employees' => $employees,
            'role' => $role,
        ]);
    }

    public function edit(int $id, int $companyId)
    {
        if (!$this->service->findOrFail($id, $companyId)) {
            return json_encode(['error' => __('pages.salary_not_found')]);
        }

        $employees = $this->service->getAllEmployees($companyId);
        if ($employees->isEmpty()) {
            return json_encode(['error' => __('pages.register_employees_before_salary')]);
        }

        return json_encode([
            'editSalary' => $this->service->getSalaryById($id, $companyId),
            'employees' => $employees,
        ]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric|min:1|exists:salaries,id',
            'company_id' => 'required|integer|exists:companies,id',
            'employee_id' => 'required|integer|exists:employees,id',
            'salary' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return json_encode(['error' => __('pages.fields_invalid')]);
        }

        $id = (int) $request->id;
        $companyId = (int) $request->input('company_id');
        $data = $request->only(['employee_id', 'salary']);

        try {
            $this->service->updateSalary($id, $data, $companyId);
            return json_encode(['success' => __('pages.salary_updated')]);
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
            return json_encode(['error' => __('pages.salary_not_found')]);
        }

        try {
            $this->service->deleteSalary($id, $companyId);
            return json_encode(['success' => __('pages.salary_deleted')]);
        } catch (\Exception $e) {
            return json_encode(['error' => __('pages.try_again')]);
        }
    }

    public function calculateSalary(int $companyId)
    {
        $employees = $this->service->getAllEmployees($companyId);
        if ($employees->isEmpty()) {
            return json_encode(['error' => __('pages.register_employees_before_calculate')]);
        }

        return json_encode([
            'calculations' => collect(),
            'employees' => $employees,
        ]);
    }

    public function calculate(Request $request, int $companyId)
    {
        $validator = Validator::make($request->all(), [
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'employee_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return json_encode(['error' => __('pages.fields_invalid')]);
        }

        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $employeeId = $request->input('employee_id');
        $employees = $this->service->getAllEmployees($companyId);

        try {
            $employeeFilter = (int) ($employeeId ?? 0);
            $calculations = $this->service->calculateSalary($fromDate, $toDate, $employeeFilter, $companyId, 10);
            $summary = $this->service->getCalculationSummary($fromDate, $toDate, $employeeFilter, $companyId);

            return json_encode([
                'calculations' => $calculations,
                'summary' => $summary,
                'fromDate' => $fromDate,
                'toDate' => $toDate,
                'employeeId' => $employeeId,
                'employees' => $employees,
            ]);
        } catch (\Exception $e) {
            return json_encode(['error' => __('pages.calculate_failed', ['message' => $e->getMessage()])]);
        }
    }

    public function saveCalculated(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'employee_id' => 'nullable|integer',
            'company_id' => 'required|integer|exists:companies,id',
        ]);

        if ($validator->fails()) {
            return json_encode(['error' => __('pages.fields_invalid')]);
        }

        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $employeeId = (int) ($request->input('employee_id') ?? 0);
        $companyId = (int) $request->input('company_id');

        try {
            $this->service->saveCalculatedSalaries($fromDate, $toDate, $employeeId, $companyId);
            return json_encode(['success' => __('pages.salaries_saved')]);
        } catch (\Exception $e) {
            return json_encode(['error' => __('pages.salaries_save_failed', ['message' => $e->getMessage()])]);
        }
    }
}
