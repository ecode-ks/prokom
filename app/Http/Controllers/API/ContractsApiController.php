<?php

namespace App\Http\Controllers\API;

use App\Services\Api\ContractsApiService;
use App\Support\PayTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class ContractsApiController extends Controller
{
    protected ContractsApiService $service;

    public function __construct(ContractsApiService $service)
    {
        $this->service = $service;
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|integer|min:1|exists:employees,id',
            'company_id' => 'required|integer|exists:companies,id',
            'position' => 'required|string|min:1|max:255',
            'salary' => 'required|numeric|min:0.01',
            'extra_hours_price' => 'required|numeric|min:0.00',
            'weekend_hours_price' => 'required|numeric|min:0.00',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after:from_date',
            'docs' => 'required|array|min:1',
            'docs.*' => 'file|max:51200',
        ]);

        if ($validator->fails()) {
            return json_encode(['error' => __('pages.fields_invalid')]);
        }

        $data = $request->only(['employee_id', 'position', 'salary', 'extra_hours_price', 'weekend_hours_price', 'from_date', 'to_date', 'docs']);
        $data['company_id'] = (int) $request->input('company_id');

        try {
            $this->service->createContract($data);
            return json_encode(['success' => __('pages.contract_created')]);
        } catch (\Exception $e) {
            return json_encode(['error' => __('pages.try_again')]);
        }
    }

    public function read(Request $request, int $companyId, int $employeeId, string $role)
    {
        $search = $request->query('search', '');
        $status = $request->query('status', '');
        $scopedEmployeeId = $role === 'employee' ? (int) ($employeeId ?? 0) : null;

        if ($role === 'employee') {
            return json_encode([
                'employees' => collect(),
                'contracts' => $this->service->getContracts(10, $search, $status, $companyId, $scopedEmployeeId),
                'contractStats' => $this->service->getContractStatistics($search, $companyId, $scopedEmployeeId),
                'role' => $role,
            ]);
        }

        $employees = $this->service->getAllEmployees($companyId);
        if (count($employees) === 0) {
            return json_encode(['error' => __('pages.register_employees_first')]);
        }

        return json_encode([
            'employees' => $employees,
            'contracts' => $this->service->getContracts(10, $search, $status, $companyId),
            'contractStats' => $this->service->getContractStatistics($search, $companyId),
            'role' => $role,
        ]);
    }

    public function contractDetails(int $id, int $companyId, int $employeeId, string $role)
    {
        if (!$this->service->findOrFail($id, $companyId)) {
            return json_encode(['error' => __('pages.contract_not_found')]);
        }

        $contractDetails = $this->service->getContractDetails($id, $companyId);

        if ($role === 'employee' && (int) ($contractDetails->employee_id ?? 0) !== (int) $employeeId) {
            return json_encode(['error' => __('pages.contract_not_found')]);
        }

        $payType = $contractDetails->pay_type ?? PayTypes::MONTHLY;
        $docs = $contractDetails->docs ?? [];
        if (is_string($docs)) {
            $docs = json_decode($docs, true) ?: [];
        }

        $contractDetails->pay_type = $payType;
        $contractDetails->pay_type_label = PayTypes::label($payType);
        $contractDetails->rate_label = PayTypes::rateLabel($payType);
        $contractDetails->docs = collect(is_array($docs) ? $docs : [])
            ->filter()
            ->map(fn ($doc) => [
                'path' => $doc,
                'name' => basename((string) $doc),
            ])
            ->values()
            ->all();

        return json_encode([
            'contractDetails' => $contractDetails,
            'role' => $role,
        ]);
    }

    public function edit(int $id, int $companyId)
    {
        if (!$this->service->findOrFail($id, $companyId)) {
            return json_encode(['error' => __('pages.contract_not_found')]);
        }

        $employees = $this->service->getAllEmployees($companyId);
        if (count($employees) === 0) {
            return json_encode(['error' => __('pages.register_employees_first')]);
        }

        return json_encode([
            'employees' => $employees,
            'editContract' => $this->service->getContractById($id, $companyId),
        ]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|min:1|exists:contracts,id',
            'company_id' => 'required|integer|exists:companies,id',
            'employee_id' => 'required|integer|min:1|exists:employees,id',
            'position' => 'required|string|min:1|max:255',
            'salary' => 'required|numeric|min:0.01',
            'extra_hours_price' => 'required|numeric|min:0.01',
            'weekend_hours_price' => 'required|numeric|min:0.01',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after:from_date',
            'docs' => 'nullable|array',
            'docs.*' => 'file',
        ]);

        if ($validator->fails()) {
            return json_encode(['error' => __('pages.fields_invalid')]);
        }

        $id = (int) $request->id;
        $companyId = (int) $request->input('company_id');
        $data = $request->only(['employee_id', 'position', 'salary', 'extra_hours_price', 'weekend_hours_price', 'from_date', 'to_date']);
        if ($request->hasFile('docs')) {
            $data['docs'] = $request->file('docs');
        }

        try {
            $this->service->updateContract($id, $data, $companyId);
            return json_encode(['success' => __('pages.contract_updated')]);
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
            return json_encode(['error' => __('pages.contract_not_found')]);
        }

        try {
            $this->service->deleteContract($id, $companyId);
            return json_encode(['success' => __('pages.contract_deleted')]);
        } catch (\Exception $e) {
            return json_encode(['error' => __('pages.try_again')]);
        }
    }
}
