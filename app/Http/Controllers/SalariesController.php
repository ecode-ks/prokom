<?php

namespace App\Http\Controllers;

use App\Services\SalariesService;
use App\Services\EmployeesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SalariesController extends Controller
{
    protected SalariesService $service;
    protected EmployeesService $employeeService;
    public function __construct(SalariesService $service, EmployeesService $employeeService)
    {
        $this->service = $service;
        $this->employeeService = $employeeService;
    }
    //---------------
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|integer|exists:employees,id',
            'salary' => 'required|numeric|min:0.01',
        ]);
        if ($validator->fails()) {
            return redirect()->route('salaries')->with('error','Të gjitha fushat duhet të plotësohen sipas rregullave.')->withInput();
        } else {
            $data = $request->only(['employee_id', 'salary']);
            try {
                $this->service->createSalary($data);
                return redirect()->route('salaries')->with('success', 'Paga u regjistrua me sukses.');
            } catch (\Exception $e) {
                return redirect()->route('salaries')->with('error', 'Ndodhi një problem, ju lutem provoni përsëri.')->withInput();
            }
        }
    }
    //---------------
    public function read(Request $request)
    {
        $page = 'Salaries';
        $search = $request->query('search', '');
        $role = session('role');
        $employeeId = session('employee_id') ? (int) session('employee_id') : null;

        $scopedEmployeeId = $role === 'employee' ? (int) ($employeeId ?? 0) : null;
        $salaries = $this->service->getSalaries(10, $search, $scopedEmployeeId);

        if ($role === 'employee') {
            return view('management.salaries')
                ->with('page', $page)
                ->with('salaries', $salaries)
                ->with('employees', collect())
                ->with('role', $role);
        }
        $employees = $this->employeeService->getAllEmployees();

        if ($employees->isEmpty()) {
            return redirect()->route('employees')->with('error', 'Ju lutem regjistroni punonjës para se të regjistroni paga.');
        }

        return view('management.salaries')
            ->with('page', $page)
            ->with('salaries', $salaries)
            ->with('employees', $employees)
            ->with('role', $role);
    }
    //---------------
    public function edit(int $id)
    {
        $page = 'Salaries';
        if (!$this->service->findOrFail($id)) {
            return redirect()->route('salaries')->with('error','Paga nuk u gjet');
        } else {
            $editSalary = $this->service->getSalaryById($id);
            $employees = $this->employeeService->getAllEmployees();

            if($employees->isEmpty()) {
                return redirect()->route('employees')->with('error', 'Ju lutem regjistroni punonjës para se të regjistroni paga.');
            }

            return view('management.salaries')
                ->with('page', $page)
                ->with('editSalary', $editSalary)
                ->with('employees', $employees);
        }
    }
    //---------------
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric|min:1|exists:salaries,id',
            'employee_id' => 'required|integer|exists:employees,id',
            'salary' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return back()->with('error','Të gjitha fushat duhet të plotësohen sipas rregullave.')->withInput();
        } else {
            $id = $request->id;
            $data = $request->only(['employee_id', 'salary']);
            try {
                $this->service->updateSalary($id, $data);
                return redirect()->route('salaries')->with('success', 'Paga u përditësua me sukses.');
            } catch (\Exception $e) {
                return redirect()->route('salaries')->with('error', 'Ndodhi një problem, ju lutem provoni përsëri.');
            }
        }
    }
    //---------------
    public function delete(int $id)
    {
        if (!$this->service->findOrFail($id)) {
            return redirect()->route('salaries')->with('error','Paga nuk u gjet');
        } else {
            try {
                $this->service->deleteSalary($id);
                return redirect()->route('salaries')->with('success','Paga u fshi me sukses');
            } catch (\Exception $e) {
                return redirect()->route('salaries')->with('error', 'Ndodhi një problem, ju lutem provoni përsëri.');
            }
        }
    }
    //---------------
    public function calculateSalary()
    {
        $page = 'Calculate Salary';
        $employees = $this->employeeService->getAllEmployees();
        if($employees->isEmpty()) {
            return redirect()->route('calculate.salary')->with('error', 'Ju lutem regjistroni punonjës para se të kalkuloni paga.');
        }
        return view('management.calculate-salary')
            ->with('page', $page)
            ->with('calculations', collect())
            ->with('employees', $employees);
    }
    //---------------
    public function calculate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'employee_id' => 'nullable|integer',
        ]);
        if ($validator->fails()) {
            return redirect()->route('calculate.salary')->with('error','Të gjitha fushat duhet të plotësohen sipas rregullave.')->withInput();
        } else {
            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');
            $employeeId = $request->input('employee_id');
            $employees = $this->employeeService->getAllEmployees();

            try {
                $employeeFilter = (int) ($employeeId ?? 0);
                $calculations = $this->service->calculateSalary($fromDate, $toDate, $employeeFilter, 10);
                $summary = $this->service->getCalculationSummary($fromDate, $toDate, $employeeFilter);
                return view('management.calculate-salary')
                    ->with('page', 'Calculate Salary')
                    ->with('calculations', $calculations)
                    ->with('summary', $summary)
                    ->with('fromDate', $fromDate)
                    ->with('toDate', $toDate)
                    ->with('employeeId', $employeeId)
                    ->with('employees', $employees);
            } catch (\Exception $e) {
                return redirect()->route('calculate.salary')->with('error', 'Ndodhi një problem gjatë kalkulimit të pagës. Ju lutem provoni përsëri.'. $e->getMessage());
            }
        }
    }
    //---------------
    public function saveCalculated(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'employee_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return redirect()->route('calculate.salary')->with('error', 'Të gjitha fushat duhet të plotësohen sipas rregullave.')->withInput();
        }

        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $employeeId = (int) ($request->input('employee_id') ?? 0);

        try {
            $this->service->saveCalculatedSalaries($fromDate, $toDate, $employeeId);
            return redirect()->route('salaries')->with('success', 'Pagat u ruajtën me sukses dhe u regjistruan si shpenzim.');
        } catch (\Exception $e) {
            return redirect()
                ->route('calculate', [
                    'from_date' => $fromDate,
                    'to_date' => $toDate,
                    'employee_id' => $employeeId ?: null,
                ])
                ->with('error', 'Ndodhi një problem gjatë ruajtjes së pagave. ' . $e->getMessage());
        }
    }
}
