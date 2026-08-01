<?php

namespace App\Http\Controllers;

use App\Services\TaskReportsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskReportsController extends Controller
{
    protected TaskReportsService $service;

    public function __construct(TaskReportsService $service)
    {
        $this->service = $service;
    }
    //---------------
    public function create(Request $request)
    {
        $companyId = session('company_id');
        $role = session('role');
        $employeeId = session('employee_id') ?  session('employee_id') : null;

        $validator = Validator::make($request->all(), [
            'task_id' => 'required|integer|min:1|exists:tasks,id',
            'report' => 'required|string|min:1',
            'images' => 'nullable|array',
            'images.*' => 'image|max:5120',
        ]);

        if ($validator->fails()) {
            return redirect()->route('task.reports')->with('error', 'Të gjitha fusha duhet plotësuar sipas rregullave.')->withInput();
        }

        $data = [
            'task_id' => $request->task_id,
            'report' => trim( $request->report),
            'images' => $request->hasFile('images') ? $request->file('images') : [],
            'reported_by' => $employeeId,
            'company_id' => $companyId,
            'status' => 'active',
            'date' => now(),
        ];

        if (!$this->service->createReport($data, $role, $employeeId, $companyId)) {
            return redirect()->route('task.reports')->with('error', 'Nuk mund të raportoni për këtë detyrë.')->withInput();
        }

        return redirect()->route('task.reports')->with('success', 'Raporti i detyrës u krijua me sukses.');
    }
    //---------------
    public function read()
    {
        $page = 'Task Reports';
        $companyId = session('company_id');
        $role = session('role');
        $employeeId = session('employee_id') ? session('employee_id') : null;

        $tasks = $this->service->getAvailableTasks($companyId, $role, $employeeId);

        if (count($tasks) === 0) {
            return redirect()->route('tasks')->with('error', 'Ju lutemi regjistroni së pari detyrat.');
        }

        $reports = $this->service->getReports(10, $companyId, $role, $employeeId);
        $reportStats = $this->service->getReportStatistics((int) $companyId, $role, $employeeId ? (int) $employeeId : null);

        return view('management.task-reports')
            ->with('page', $page)
            ->with('tasks', $tasks)
            ->with('reports', $reports)
            ->with('role', $role)
            ->with('reportStats', $reportStats);
    }
    //---------------
    public function details(int $id)
    {
        $page = 'Task Reports';
        $companyId = session('company_id');
        $role = session('role');

        if (!$this->service->findOrFail($id, $companyId)) {
            return redirect()->route('task.reports')->with('error', 'Raporti nuk u gjet.');
        }

        $reportDetails = $this->service->getReportDetails($id, $companyId);

        return view('management.task-reports-details')
            ->with('page', $page)
            ->with('reportDetails', $reportDetails)
            ->with('role', $role);
    }
    //---------------
    public function edit(int $id)
    {
        $page = 'Task Reports';
        $companyId = session('company_id');
        $role = session('role');
        $employeeId = session('employee_id') ? session('employee_id') : null;

        if (!$this->service->findOrFail($id, $companyId)) {
            return redirect()->route('task.reports')->with('error', 'Raporti nuk u gjet.');
        }

        $tasks = $this->service->getAvailableTasks($companyId, $role, $employeeId);
        $editReport = $this->service->getReportById($id, $companyId);

        return view('management.task-reports')
            ->with('page', $page)
            ->with('tasks', $tasks)
            ->with('editReport', $editReport)
            ->with('role', $role);
    }
    //---------------
    public function update(Request $request)
    {
        $companyId = session('company_id');
        $role = session('role');
        $employeeId = session('employee_id') ? session('employee_id') : null;

        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|min:1|exists:task_reports,id',
            'task_id' => 'required|integer|min:1|exists:tasks,id',
            'report' => 'required|string|min:1',
            'images' => 'nullable|array',
            'images.*' => 'image|max:5120',
        ]);

        if ($validator->fails()) {
            return back()->with('error', 'Të gjitha fusha duhet plotësuar sipas rregullave.')->withInput();
        }

        $id = $request->id;

        if (!$this->service->findOrFail($id, $companyId)) {
            return redirect()->route('task.reports')->with('error', 'Raporti nuk u gjet.');
        }

        $data = [
            'task_id' => $request->task_id,
            'report' => trim( $request->report),
        ];

        if ($request->hasFile('images')) {
            $data['images'] = $request->file('images');
        }

        try {
            if (!$this->service->updateReport($id, $data, $role, $employeeId, $companyId)) {
                return back()->with('error', 'Raporti nuk u gjet ose nuk u përditësua.')->withInput();
            }

            return redirect()->route('task.reports')->with('success', 'Raporti i detyrës u përditësua me sukses.');
        } catch (\Exception $e) {
            return redirect()->route('task.reports')->with('error', 'Ndodhi një problem, ju lutemi provoni përsëri.');
        }
    }
    //---------------
    public function delete(int $id)
    {
        $companyId = session('company_id');

        if (!$this->service->findOrFail($id, $companyId)) {
            return redirect()->route('task.reports')->with('error', 'Raporti nuk u gjet.');
        }

        try {
            $this->service->deleteReport($id, $companyId);
            return redirect()->route('task.reports')->with('success', 'Raporti i detyrës u fshi me sukses.');
        } catch (\Exception $e) {
            return redirect()->route('task.reports')->with('error', 'Ndodhi një problem, ju lutemi provoni përsëri.');
        }
    }
    //---------------
    public function updateStatus(int $id, string $status)
    {
        $companyId = session('company_id');

        if (!$this->service->findOrFail($id, $companyId)) {
            return redirect()->route('task.reports')->with('error', 'Raporti nuk u gjet.');
        }

        try {
            if (!$this->service->updateReportStatus($id, $status, $companyId)) {
                return redirect()->route('task.report.details', $id)->with('error', 'Kërkesa për përditësim statusi nuk është e vlefshme.');
            }

            return redirect()->route('task.report.details', $id)->with('success', 'Statusi i raportit u përditësua me sukses.');
        } catch (\Exception $e) {
            return redirect()->route('task.report.details', $id)->with('error', 'Ndodhi një problem, ju lutemi provoni përsëri.');
        }
    }
}
