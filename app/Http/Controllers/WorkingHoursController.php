<?php

namespace App\Http\Controllers;

use App\Services\WorkingHoursService;
use App\Services\EmployeesService;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WorkingHoursController extends Controller
{
    protected EmployeesService $employeeservice;
    protected WorkingHoursService $service;
    public function __construct(WorkingHoursService $service, EmployeesService $employeeservice)
    {
        $this->service = $service;
        $this->employeeservice = $employeeservice;
    }
    //---------------
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'employee_id' => 'required|integer|exists:employees,id',
            'check_in' => 'required|date|before:check_out',
            'check_out' => 'required|date|after:check_in'
        ]);
        if ($validator->fails()) {
            return redirect()->route('working.hours')->with('error','Të gjitha fushat duhet të plotësohen sipas rregullave.')->withInput();
        } else {
            $data = $request->only(['date', 'employee_id', 'check_in', 'check_out']);
            try {
                $data['hours'] = Carbon::parse($data['check_in'])->diff(Carbon::parse($data['check_out']))->format('%H:%I');
                $this->service->createWorkingHour($data);
                return redirect()->route('working.hours')->with('success', 'Ora e punës u regjistrua me sukses.');
            } catch (\Exception $e) {
                return redirect()->route('working.hours')->with('error', 'Regjistrimi dështoi : ' . $e->getMessage())->withInput();
            }
        }
    }
    //---------------
    public function read(Request $request)
    {
        $page = 'Working Hours';
        $search = $request->query('search', '');
        $role = session('role');
        $employeeId = session('employee_id') ? (int) session('employee_id') : null;
        $employees = $this->employeeservice->getAllEmployees();
        $workingHoursStats = $this->service->getWorkingHoursStatistics();
        $fromDate = null;
        $toDate = null;
        $workingHoursTotal = null;

        if ($role === 'employee' && $employeeId) {
            $fromDate = $request->query('from_date') ?: null;
            $toDate = $request->query('to_date') ?: null;
            $workingHours = $this->service->getEmployeeWorkingHours($employeeId, $fromDate, $toDate, 10);
            $workingHoursTotal = $this->service->getEmployeeWorkingHoursTotal($employeeId, $fromDate, $toDate);
        } else {
            $workingHours = $this->service->getWorkingHours(10, $search);
        }

        return view('management.working-hours')
            ->with('page', $page)
            ->with('workingHours', $workingHours)
            ->with('workingHoursTotal', $workingHoursTotal)
            ->with('employees', $employees)
            ->with('workingHoursStats', $workingHoursStats)
            ->with('role', $role)
            ->with('fromDate', $fromDate)
            ->with('toDate', $toDate);
    }
    //---------------
    public function delete(int $id)
    {
        if (!$this->service->findOrFail($id)) {
            return redirect()->route('working.hours')->with('error', 'Ora e punës nuk u gjet');
        } else {
            try {
                $this->service->deleteWorkingHour($id);
                return redirect()->route('working.hours')->with('success', 'Ora e punës u fshi me sukses');
            } catch (\Exception $e) {
                return redirect()->route('working.hours')->with('error', 'Diçka shkoi keq, ju lutemi provoni përsëri.');
            }
        }
    }
}
