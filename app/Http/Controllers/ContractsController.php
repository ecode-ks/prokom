<?php

namespace App\Http\Controllers;

use App\Services\EmployeesService;
use App\Services\ContractsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContractsController extends Controller
{
    protected ContractsService $service;
    protected EmployeesService $employees;
    public function __construct(ContractsService $service, EmployeesService $employees)
    {
        $this->service = $service;
        $this->employees = $employees;
    }
    //---------------
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|integer|min:1|exists:employees,id',
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
            return redirect()->route('contracts')->with('error', __('pages.fields_invalid'))->withInput();
        } else {
            $data = $request->only(['employee_id', 'position', 'salary', 'extra_hours_price', 'weekend_hours_price', 'from_date', 'to_date', 'docs']);
            $data['company_id'] = session('company_id');
            try {
                $this->service->createContract($data);
                return redirect()->route('contracts')->with('success', __('pages.contract_created'));
            } catch (\Exception $e) {
                return redirect()->route('contracts')->with('error', __('pages.try_again'))->withInput();
            }
        }
    }
    //---------------
    public function read(Request $request)
    {
        $page = 'Contracts';
        $search = $request->query('search', '');
        $status = $request->query('status', '');
        $companyId = session('company_id');
        $role = session('role');
        $employeeId = session('employee_id') ? (int) session('employee_id') : null;

        $scopedEmployeeId = $role === 'employee' ? (int) ($employeeId ?? 0) : null;

        if ($role === 'employee') {
            $contracts = $this->service->getContracts(10, $search, $status, $companyId, $scopedEmployeeId);
            $contractStats = $this->service->getContractStatistics($search, $companyId, $scopedEmployeeId);

            return view('management.contracts')
                ->with('page', $page)
                ->with('employees', collect())
                ->with('contracts', $contracts)
                ->with('contractStats', $contractStats)
                ->with('role', $role);
        }

        $employees = $this->employees->getAllEmployees($companyId);
        if (count($employees) === 0) {
            return redirect()->route('employees')->with('error', __('pages.register_employees_first'));
        }
        $contracts = $this->service->getContracts(10, $search, $status, $companyId);
        $contractStats = $this->service->getContractStatistics($search, $companyId);
        return view('management.contracts')
            ->with('page', $page)
            ->with('employees', $employees)
            ->with('contracts', $contracts)
            ->with('contractStats', $contractStats)
            ->with('role', $role);
    }
    //---------------
    public function contractDetails(int $id)
    {
        $page = 'Contracts';
        $companyId = session('company_id');
        $role = session('role');
        $employeeId = session('employee_id') ? (int) session('employee_id') : null;

        if (!$this->service->findOrFail($id, $companyId)) {
            return redirect()->route('contracts')->with('error', __('pages.contract_not_found'));
        }

        $contractDetails = $this->service->getContractDetails($id, $companyId);

        if ($role === 'employee' && (int) ($contractDetails->employee_id ?? 0) !== (int) $employeeId) {
            return redirect()->route('contracts')->with('error', __('pages.contract_not_found'));
        }

        return view('management.contracts')
            ->with('page', $page)
            ->with('contractDetails', $contractDetails)
            ->with('role', $role);
    }
    //---------------
    public function edit(int $id)
    {
        $page = 'Contracts';
        $companyId = session('company_id');
        if (!$this->service->findOrFail($id, $companyId)) {
            return redirect()->route('contracts')->with('error', __('pages.contract_not_found'));
        } else {
            $employees = $this->employees->getAllEmployees($companyId);
            if(count($employees) === 0){
                return redirect()->route('employees')->with('error', __('pages.register_employees_first'));
            }
            $editContract = $this->service->getContractById($id, $companyId);
            return view('management.contracts')
                ->with('page', $page)
                ->with('employees', $employees)
                ->with('editContract', $editContract);
        }
    }
    //---------------
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|min:1|exists:contracts,id',
            'employee_id' => 'required|integer|min:1|exists:employees,id',
            'position' => 'required|string|min:1|max:255',
            'salary' => 'required|numeric|min:0.01',
            'extra_hours_price' => 'required|numeric|min:0.01',
            'weekend_hours_price' => 'required|numeric|min:0.01',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after:from_date',
            'docs' => 'nullable|array',
            'docs.*' => 'file'
        ]);

        if ($validator->fails()) {
            return back()->with('error', __('pages.fields_invalid'))->withInput();
        } else {
            $id = $request->id;
            $companyId = session('company_id');
            $data = $request->only(['employee_id', 'position', 'salary', 'extra_hours_price', 'weekend_hours_price', 'from_date', 'to_date']);
            if ($request->hasFile('docs')) {
                $data['docs'] = $request->file('docs');
            }
            try {
                $this->service->updateContract($id, $data, $companyId);
                return back()->with('success', __('pages.contract_updated'));
            } catch (\Exception $e) {
                return redirect()->route('contracts')->with('error', __('pages.try_again'));
            }
        }
    }
    //---------------
    public function delete(int $id)
    {
        $companyId = session('company_id');
        if (!$this->service->findOrFail($id, $companyId)) {
            return redirect()->route('contracts')->with('error', __('pages.contract_not_found'));
        } else {
            try {
                $this->service->deleteContract($id, $companyId);
                return redirect()->route('contracts')->with('success', __('pages.contract_deleted'));
            } catch (\Exception $e) {
                return redirect()->route('contracts')->with('error', __('pages.try_again'));
            }
        }
    }
}

