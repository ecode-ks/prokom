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
        $salaries = $this->service->getSalaries(10, $search);
        $employees = $this->employeeService->getAllEmployees();

        if($employees->isEmpty()) {
            return redirect()->route('employees')->with('error', 'Ju lutem regjistroni punonjës para se të regjistroni paga.');
        }

        return view('management.salaries')
            ->with('page', $page)
            ->with('salaries', $salaries)
            ->with('employees', $employees);
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
            'to_date' => 'required|date',
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
                $calculations = $this->service->calculateSalary($fromDate, $toDate, $employeeId ?? 0);
                return view('management.calculate-salary')
                    ->with('page', 'Calculate Salary')
                    ->with('calculations', $calculations)
                    ->with('fromDate', $fromDate)
                    ->with('toDate', $toDate)
                    ->with('employeeId', $employeeId)
                    ->with('employees', $employees);
            } catch (\Exception $e) {
                return redirect()->route('calculate.salary')->with('error', 'Ndodhi një problem gjatë kalkulimit të pagës. Ju lutem provoni përsëri.'. $e->getMessage());
            }
        }
    }
}
