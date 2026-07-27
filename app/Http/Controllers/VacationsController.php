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
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|integer|min:1|exists:employees,id',
            'from_date' => 'required|date|after_or_equal:today',
            'to_date' => 'required|date|after:from_date',
            'type' => 'required|in:Pushim vjetor,Pushim mjekësor',
            'comment' => 'nullable|string|min:1'
        ]);
        if ($validator->fails()) {
            if ($validator->errors()->has('from_date') && strtotime($request->from_date) < strtotime('today')) {
                return redirect()->route('vacations')->with('error', 'Nuk mund të rezervoni pushim në të kaluarën.')->withInput();
            }
            return redirect()->route('vacations')->with('error','Të gjitha fusha duhet plotësuar sipas rregullave.')->withInput();
        } else {
            $data = $request->only(['employee_id', 'from_date', 'to_date', 'type', 'comment']);
            try {
                $this->service->createVacation($data);
                return redirect()->route('vacations')->with('success', 'Pushimi u krijua me sukses.');
            } catch (\Exception $e) {
                return redirect()->route('vacations')->with('error', 'Ndodhi një problem, ju lutemi provoni përsëri.'. $e)->withInput();
            }
        }
    }
    //---------------
    public function read(Request $request)
    {
        $page = 'Vacations';
        $search = $request->query('search', '');
        $companyId = session('company_id');
        $statuses = array_values(array_filter((array) $request->query('statuses', [])));
        $employees = $this->employees->getAllEmployees($companyId);
        if(count($employees) === 0){
            return redirect()->route('employees')->with('error','Ju lutemi regjistroni punëtorët e parë.');
        }
        $vacations = $this->service->getVacations(10, $search, $statuses, $companyId);
        $vacationStats = $this->service->getVacationStatistics($search, $companyId);
        return view('management.vacations')
            ->with('page', $page)
            ->with('employees', $employees)
            ->with('vacations', $vacations)
            ->with('vacationStats', $vacationStats);
    }
    //---------------
    public function edit(int $id)
    {
        $page = 'Vacations';
        $companyId = session('company_id');
        if (!$this->service->findOrFail($id, $companyId)) {
            return redirect()->route('vacations')->with('error','Vacation not found');
        } else {
            $employees = $this->employees->getAllEmployees($companyId);
            if(count($employees) === 0){
                return redirect()->route('vacations')->with('error','Ju lutemi regjistroni punëtorët e parë.');
            }
            $editVacation = $this->service->getVacationById($id, $companyId);
            return view('management.vacations')
                ->with('page', $page)
                ->with('employees', $employees)
                ->with('editVacation', $editVacation);
        }
    }
    //---------------
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|min:1|exists:vacations,id',
            'employee_id' => 'required|integer|min:1|exists:employees,id',
            'from_date' => 'required|date|after_or_equal:today',
            'to_date' => 'required|date|after:from_date',
            'type' => 'required|in:Pushim vjetor,Pushim mjekësor',
            'comment' => 'nullable|string|min:1'
        ]);

        if ($validator->fails()) {
            if ($validator->errors()->has('from_date') && strtotime($request->from_date) < strtotime('today')) {
                return back()->with('error', 'Nuk mund të rezervoni pushim në të kaluarën.')->withInput();
            }
            return back()->with('error','Të gjitha fusha duhet plotësuar sipas rregullave.')->withInput();
        } else {
            $id = $request->id;
            $data = $request->only(['employee_id', 'from_date', 'to_date', 'type', 'comment']);
            try {
                $this->service->updateVacation($id, $data);
                return back()->with('success', 'Pushimi u përditësua me sukses.');
            } catch (\Exception $e) {
                return redirect()->route('vacations')->with('error', 'Ndodhi një problem, ju lutemi provoni përsëri.');
            }
        }
    }
    //---------------
    public function delete(int $id)
    {
        $companyId = session('company_id');
        if (!$this->service->findOrFail($id, $companyId)) {
            return redirect()->route('vacations')->with('error','Pushimi nuk u gjet.');
        } else {
            try {
                $this->service->deleteVacation($id, $companyId);
                return redirect()->route('vacations')->with('success','Pushimi u fshi me sukses.');
            } catch (\Exception $e) {
                return redirect()->route('vacations')->with('error', 'Ndodhi një problem, ju lutemi provoni përsëri.');
            }
        }
    }

    //---------------
    public function updateStatus(int $id, string $status)
    {
        $companyId = session('company_id');
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

