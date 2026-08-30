<?php

namespace App\Http\Controllers;

use App\Services\EmployeesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmployeesController extends Controller
{
    protected EmployeesService $service;
    public function __construct(EmployeesService $service)
    {
        $this->service = $service;
    }
    //---------------
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee' => 'required|string|min:1|max:255',
            'phone' => 'required|string|min:1|max:255',
            'email' => 'required|email|min:1|max:255',
            'username' => 'required|string|min:1|max:255',
            'password' => 'required|string|min:8|max:255',
            'role' => 'required|string|min:1|max:255|in:manager,finance,hr,employee'
        ]);
        if ($validator->fails()) {
            return redirect()->route('employees')->with('error','Të gjitha fushat duhet të plotësohen sipas rregullave.')->withInput();
        } else {
            $data = $request->only(['employee', 'phone', 'email', 'username', 'password', 'role']);
            try {
                $this->service->createEmployee($data);
                return redirect()->route('employees')->with('success', 'Punëtori u regjistrua me sukses.');
            } catch (\Exception $e) {
                return redirect()->route('employees')->with('error', 'Regjistrimi dështoi : ' . $e->getMessage())->withInput();
            }
        }
    }
    //---------------
    public function read(Request $request)
    {
        $page = 'Employees';
        $search = $request->query('search', '');
        $employees = $this->service->getEmployees(10, $search);
        $allEmployees = $this->service->countAllEmployees();
        $activeEmployees = $this->service->countActiveEmployees();
        $blockedEmployees = $this->service->countBlockedEmployees();
        return view('management.employees')
            ->with('page', $page)
            ->with('employees', $employees)
            ->with('allEmployees', $allEmployees)
            ->with('activeEmployees', $activeEmployees)
            ->with('blockedEmployees', $blockedEmployees);
    }
    //---------------
    public function edit(int $id)
    {
        $page = 'Employees';
        if (!$this->service->findOrFail($id)) {
            return redirect()->route('employees')->with('error','Punëtori nuk u gjet');
        } else {
            $editEmployee = $this->service->getEmployeeById($id);
            return view('management.employees')
                ->with('page', $page)
                ->with('editEmployee', $editEmployee);
        }
    }
    //---------------
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric|min:1|exists:employees,id',
            'employee' => 'required|string|min:1|max:255',
            'phone' => 'required|string|min:1|max:255',
            'email' => 'required|email|min:1|max:255',
            'password' => 'nullable|string|min:8|max:255',
            'role' => 'required|string|min:1|max:255|in:manager,finance,hr,employee'
        ]);

        if ($validator->fails()) {
            return back()->with('error','Të gjitha fushat duhet të plotësohen sipas rregullave.')->withInput();
        } else {
            $id = $request->id;
            if($request->password === null) {
                $data = $request->only(['employee', 'phone', 'email', 'role']);
            } else {
                $data = $request->only(['employee', 'phone', 'email', 'password', 'role']);
            }
            try {
                $this->service->updateEmployee($id, $data);
                return back()->with('success', 'Punëtori u përditësua me sukses.');
            } catch (\Exception $e) {
                return redirect()->route('employees')->with('error', 'Regjistrimi dështoi : ' . $e->getMessage())->withInput();
            }
        }
    }
    //---------------
    public function delete(int $id)
    {
        if (!$this->service->findOrFail($id)) {
            return redirect()->route('employees')->with('error','Punëtori nuk u gjet');
        } else {
            try {
                $this->service->deleteEmployee($id);
                return redirect()->route('employees')->with('success','Punëtori u fshi me sukses');
            } catch (\Exception $e) {
                return redirect()->route('employees')->with('error', 'Diçka shkoi keq, ju lutemi provoni përsëri.');
            }
        }
    }
    //---------------
    public function activate(int $id)
    {
        if (!$this->service->findOrFail($id)) {
            return redirect()->route('employees')->with('error','Punëtori nuk u gjet');
        } else {
            try {
                $this->service->activateEmployee($id);
                return redirect()->route('employees')->with('success','Punëtori u aktivizua me sukses');
            } catch (\Exception $e) {
                return redirect()->route('employees')->with('error', 'Diçka shkoi keq, ju lutemi provoni përsëri.');
            }
        }
    }
    //---------------
    public function block(int $id)
    {
        if (!$this->service->findOrFail($id)) {
            return redirect()->route('employees')->with('error','Punëtori nuk u gjet');
        } else {
            try {
                $this->service->blockEmployee($id);
                return redirect()->route('employees')->with('success','Punëtori u bllokua me sukses');
            } catch (\Exception $e) {
                return redirect()->route('employees')->with('error', 'Diçka shkoi keq, ju lutemi provoni përsëri.');
            }
        }
    }
}
