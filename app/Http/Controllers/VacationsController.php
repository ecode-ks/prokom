<?php

namespace App\Http\Controllers;

use App\Services\EmployeesService;
use App\Services\VacationsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VacationsController extends Controller
{
    protected VacationsService $service;
    protected EmployeesService $employees;

    public function __construct(VacationsService $service, EmployeesService $employees)
    {
        $this->service = $service;
        $this->employees = $employees;
    }
    //---------------
    public function create(Request $request)
    {
        $role = session('role');
        $isEmployee = $role === 'employee';

        $validator = Validator::make($request->all(), [
            'employee_id' => $isEmployee ? 'nullable|integer' : 'required|integer|min:1|exists:employees,id',
            'from_date' => 'required|date|after_or_equal:today',
            'to_date' => 'required|date|after:from_date',
            'type' => 'required|in:Pushim vjetor,Pushim mjekësor',
            'comment' => 'nullable|string|min:1'
        ]);

        if ($validator->fails()) {
            if ($validator->errors()->has('from_date') && strtotime($request->from_date) < strtotime('today')) {
                return redirect()->route('vacations')->with('error', 'Nuk mund të rezervoni pushim në të kaluarën.')->withInput();
            }
            return redirect()->route('vacations')->with('error', 'Të gjitha fusha duhet plotësuar sipas rregullave.')->withInput();
        }

        $data = $request->only(['employee_id', 'from_date', 'to_date', 'type', 'comment']);
        $data['company_id'] = session('company_id');

        if ($isEmployee) {
            $data['employee_id'] = (int) session('employee_id');
        }

        try {
            $this->service->createVacation($data);
            return redirect()->route('vacations')->with('success', 'Pushimi u krijua me sukses.');
        } catch (\Exception $e) {
            return redirect()->route('vacations')->with('error', 'Ndodhi një problem, ju lutemi provoni përsëri.')->withInput();
        }
    }
    //---------------
    public function read(Request $request)
    {
        $page = 'Vacations';
        $search = $request->query('search', '');
        $companyId = (int) session('company_id');
        $role = session('role');
        $isEmployee = $role === 'employee';
        $statuses = array_values(array_filter((array) $request->query('statuses', [])));
        $selectedType = $request->query('type', old('type', ''));
        if (!in_array($selectedType, [VacationsService::TYPE_ANNUAL, VacationsService::TYPE_MEDICAL], true)) {
            $selectedType = '';
        }

        $employeeId = null;
        $employees = collect();

        if ($isEmployee) {
            $employeeId = (int) session('employee_id');
            if ($employeeId <= 0) {
                return redirect()->route('dashboard')->with('error', 'Punonjësi nuk u gjet.');
            }
        } else {
            $employees = $this->employees->getAllEmployees();
            if (count($employees) === 0) {
                return redirect()->route('employees')->with('error', 'Ju lutemi regjistroni së pari punonjësit.');
            }
        }

        $vacations = $this->service->getVacations(10, $search, $statuses, $companyId, $employeeId, null);
        $vacationStats = $this->service->getVacationStatistics($search, $companyId, $employeeId);
        $canManage = in_array($role, ['company', 'hr'], true);

        return view('management.vacations')
            ->with('page', $page)
            ->with('employees', $employees)
            ->with('vacations', $vacations)
            ->with('vacationStats', $vacationStats)
            ->with('selectedType', $selectedType)
            ->with('isEmployee', $isEmployee)
            ->with('canManage', $canManage);
    }
    //---------------
    public function edit(int $id)
    {
        $page = 'Vacations';
        $companyId = (int) session('company_id');
        $role = session('role');
        $isEmployee = $role === 'employee';

        if (!$this->service->findOrFail($id, $companyId)) {
            return redirect()->route('vacations')->with('error', 'Pushimi nuk u gjet.');
        }

        $editVacation = $this->service->getVacationById($id, $companyId);

        if ($isEmployee && (int) $editVacation->employee_id !== (int) session('employee_id')) {
            return redirect()->route('vacations')->with('error', 'Nuk keni të drejtë të ndryshoni këtë pushim.');
        }

        $employees = $isEmployee ? collect() : $this->employees->getAllEmployees();
        if (!$isEmployee && count($employees) === 0) {
            return redirect()->route('vacations')->with('error', 'Ju lutemi regjistroni së pari punonjësit.');
        }

        return view('management.vacations')
            ->with('page', $page)
            ->with('employees', $employees)
            ->with('editVacation', $editVacation)
            ->with('isEmployee', $isEmployee)
            ->with('canManage', in_array($role, ['company', 'hr'], true))
            ->with('selectedType', $editVacation->type ?? '');
    }
    //---------------
    public function update(Request $request)
    {
        $role = session('role');
        $isEmployee = $role === 'employee';

        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|min:1|exists:vacations,id',
            'employee_id' => $isEmployee ? 'nullable|integer' : 'required|integer|min:1|exists:employees,id',
            'from_date' => 'required|date|after_or_equal:today',
            'to_date' => 'required|date|after:from_date',
            'type' => 'required|in:Pushim vjetor,Pushim mjekësor',
            'comment' => 'nullable|string|min:1'
        ]);

        if ($validator->fails()) {
            if ($validator->errors()->has('from_date') && strtotime($request->from_date) < strtotime('today')) {
                return back()->with('error', 'Nuk mund të rezervoni pushim në të kaluarën.')->withInput();
            }
            return back()->with('error', 'Të gjitha fusha duhet plotësuar sipas rregullave.')->withInput();
        }

        $id = (int) $request->id;
        $companyId = (int) session('company_id');
        $existing = $this->service->getVacationById($id, $companyId);

        if (!$existing) {
            return redirect()->route('vacations')->with('error', 'Pushimi nuk u gjet.');
        }

        if ($isEmployee && (int) $existing->employee_id !== (int) session('employee_id')) {
            return redirect()->route('vacations')->with('error', 'Nuk keni të drejtë të ndryshoni këtë pushim.');
        }

        $data = $request->only(['employee_id', 'from_date', 'to_date', 'type', 'comment']);
        if ($isEmployee) {
            $data['employee_id'] = (int) session('employee_id');
        }

        try {
            $this->service->updateVacation($id, $data, $companyId);
            return redirect()->route('vacations')->with('success', 'Pushimi u përditësua me sukses.');
        } catch (\Exception $e) {
            return redirect()->route('vacations')->with('error', 'Ndodhi një problem, ju lutemi provoni përsëri.');
        }
    }
    //---------------
    public function delete(int $id)
    {
        $companyId = (int) session('company_id');
        $role = session('role');

        if (!$this->service->findOrFail($id, $companyId)) {
            return redirect()->route('vacations')->with('error', 'Pushimi nuk u gjet.');
        }

        $vacation = $this->service->getVacationById($id, $companyId);
        if ($role === 'employee' && (int) $vacation->employee_id !== (int) session('employee_id')) {
            return redirect()->route('vacations')->with('error', 'Nuk keni të drejtë të fshini këtë pushim.');
        }

        try {
            $this->service->deleteVacation($id, $companyId);
            return redirect()->route('vacations')->with('success', 'Pushimi u fshi me sukses.');
        } catch (\Exception $e) {
            return redirect()->route('vacations')->with('error', 'Ndodhi një problem, ju lutemi provoni përsëri.');
        }
    }
    //---------------
    public function updateStatus(int $id, string $status)
    {
        $companyId = (int) session('company_id');
        $role = session('role');

        if (!in_array($role, ['company', 'hr'], true)) {
            return redirect()->route('vacations')->with('error', 'Nuk keni të drejtë të ndryshoni statusin.');
        }

        if (!$this->service->findOrFail($id, $companyId)) {
            return redirect()->route('vacations')->with('error', 'Pushimi nuk u gjet.');
        }

        try {
            if (!$this->service->updateVacationStatus($id, $status, $companyId)) {
                return back()->with('error', 'Kërkesa përditësimi statusi nuk është e vlefshme.');
            }

            return back()->with('success', 'Statusi i pushimit u përditësua me sukses.');
        } catch (\Exception $e) {
            return back()->with('error', 'Ndodhi një problem, ju lutemi provoni përsëri.');
        }
    }
}
