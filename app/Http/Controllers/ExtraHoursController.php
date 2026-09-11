<?php

namespace App\Http\Controllers;

use App\Services\ExtraHoursService;
use App\Services\EmployeesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ExtraHoursController extends Controller
{
    protected ExtraHoursService $service;
    protected EmployeesService $employeeService;

    private const TYPES = [
        'Orët jashtë orarit',
        'Orët në vikend',
    ];

    public function __construct(ExtraHoursService $service, EmployeesService $employeeService)
    {
        $this->service = $service;
        $this->employeeService = $employeeService;
    }
    //---------------
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'employee_id' => 'required|integer|exists:employees,id',
            'hours' => 'required|numeric|min:0.01',
            'type' => ['required', 'string', Rule::in(self::TYPES)],
        ]);
        if ($validator->fails()) {
            return redirect()->route('extra.hours')->with('error', __('pages.fields_invalid'))->withInput();
        } else {
            $data = $request->only(['date', 'employee_id', 'hours', 'type']);
            try {
                $this->service->createExtraHour($data);
                return redirect()->route('extra.hours')->with('success', __('pages.extra_hours_created'));
            } catch (\Exception $e) {
                return redirect()->route('extra.hours')->with('error', __('pages.try_again'))->withInput();
            }
        }
    }
    //---------------
    public function read(Request $request)
    {
        $page = 'Extra Hours';
        $search = $request->query('search', '');
        $role = session('role');
        $employeeId = session('employee_id') ? (int) session('employee_id') : null;

        $scopedEmployeeId = $role === 'employee' ? (int) ($employeeId ?? 0) : null;
        $extraHours = $this->service->getExtraHours(10, $search, $scopedEmployeeId);

        if ($role === 'employee') {
            return view('management.extra-hours')
                ->with('page', $page)
                ->with('extraHours', $extraHours)
                ->with('employees', collect())
                ->with('types', self::TYPES)
                ->with('role', $role);
        }
        $employees = $this->employeeService->getAllEmployees();

        if ($employees->isEmpty()) {
            if (session('role') === 'manager') {
                return redirect()->route('dashboard')->with('error', __('pages.no_employees'));
            }
            return redirect()->route('employees')->with('error', __('pages.register_employees_before_extra'));
        }

        return view('management.extra-hours')
            ->with('page', $page)
            ->with('extraHours', $extraHours)
            ->with('employees', $employees)
            ->with('types', self::TYPES)
            ->with('role', $role);
    }
    //---------------
    public function edit(int $id)
    {
        $page = 'Extra Hours';
        if (!$this->service->findOrFail($id)) {
            return redirect()->route('extra.hours')->with('error', __('pages.extra_hours_not_found'));
        } else {
            $editExtraHour = $this->service->getExtraHourById($id);
            $employees = $this->employeeService->getAllEmployees();

            if ($employees->isEmpty()) {
                if (session('role') === 'manager') {
                    return redirect()->route('dashboard')->with('error', __('pages.no_employees'));
                }
                return redirect()->route('employees')->with('error', __('pages.register_employees_before_extra'));
            }

            return view('management.extra-hours')
                ->with('page', $page)
                ->with('editExtraHour', $editExtraHour)
                ->with('employees', $employees)
                ->with('types', self::TYPES)
                ->with('role', session('role'));
        }
    }
    //---------------
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric|min:1|exists:extra_hours,id',
            'date' => 'required|date',
            'employee_id' => 'required|integer|exists:employees,id',
            'hours' => 'required|numeric|min:0.01',
            'type' => ['required', 'string', Rule::in(self::TYPES)],
        ]);

        if ($validator->fails()) {
            return back()->with('error', __('pages.fields_invalid'))->withInput();
        } else {
            $id = $request->id;
            $data = $request->only(['date', 'employee_id', 'hours', 'type']);
            try {
                $this->service->updateExtraHour($id, $data);
                return redirect()->route('extra.hours')->with('success', __('pages.extra_hours_updated'));
            } catch (\Exception $e) {
                return redirect()->route('extra.hours')->with('error', __('pages.try_again'));
            }
        }
    }
    //---------------
    public function delete(int $id)
    {
        if (!$this->service->findOrFail($id)) {
            return redirect()->route('extra.hours')->with('error', __('pages.extra_hours_not_found'));
        } else {
            try {
                $this->service->deleteExtraHour($id);
                return redirect()->route('extra.hours')->with('success', __('pages.extra_hours_deleted'));
            } catch (\Exception $e) {
                return redirect()->route('extra.hours')->with('error', __('pages.try_again'));
            }
        }
    }
}
