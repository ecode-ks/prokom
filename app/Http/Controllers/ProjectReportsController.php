<?php

namespace App\Http\Controllers;

use App\Services\ProjectReportsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProjectReportsController extends Controller
{
    protected ProjectReportsService $service;

    public function __construct(ProjectReportsService $service)
    {
        $this->service = $service;
    }
    //---------------
    public function create(Request $request)
    {
        $companyId = session('company_id');
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|integer|min:1|exists:projects,id',
            'report' => 'required|string|min:1',
            'images' => 'nullable|array',
            'images.*' => 'image|max:5120',
        ]);

        if ($validator->fails()) {
            return redirect()->route('project.reports')->with('error', __('app.fields_invalid'))->withInput();
        }

        if (!$this->service->projectExists((int) $request->project_id, $companyId)) {
            return redirect()->route('project.reports')->with('error', __('pages.project_not_found'))->withInput();
        }

        $role = session('role');
        $employeeId = session('employee_id') ? (int) session('employee_id') : null;
        if ($role === 'manager' && !$this->service->getAllProjects($companyId, $employeeId)->contains('id', (int) $request->project_id)) {
            return redirect()->route('project.reports')->with('error', __('pages.report_own_projects_only'))->withInput();
        }

        $data = [
            'project_id' => (int) $request->project_id,
            'report' => $request->report,
            'images' => $request->file('images', []),
            'reported_by' => session('user_id'),
            'company_id' => $companyId,
            'status' => 'active',
        ];

        try {
            $this->service->createReport($data);
            return redirect()->route('project.reports')->with('success', __('pages.project_report_created'));
        } catch (\Exception $e) {
            return redirect()->route('project.reports')->with('error', __('app.try_again'))->withInput();
        }
    }
    //---------------
    public function read(Request $request)
    {
        $page = 'Project Reports';
        $search = trim((string) $request->query('search', ''));
        $companyId = session('company_id');
        $role = session('role');
        $employeeId = session('employee_id') ? (int) session('employee_id') : null;
        $scopedManagerId = $role === 'manager' ? (int) ($employeeId ?? 0) : null;
        $projects = $this->service->getAllProjects($companyId, $scopedManagerId);

        if (count($projects) === 0) {
            return redirect()->route('projects')->with('error', __('pages.register_projects_first'));
        }

        $reports = $this->service->getReports(10, $companyId, $search, $role, $employeeId);
        $reportStats = $this->service->getReportStatistics((int) $companyId, $role, $employeeId);

        return view('management.project-reports')
            ->with('page', $page)
            ->with('projects', $projects)
            ->with('reports', $reports)
            ->with('reportStats', $reportStats)
            ->with('role', $role);
    }
    //---------------
    public function reportDetails(int $id)
    {
        $page = 'Project Reports';
        $companyId = session('company_id');
        $role = session('role');
        $employeeId = session('employee_id') ? (int) session('employee_id') : null;

        if (!$this->service->findOrFail($id, $companyId)) {
            return redirect()->route('project.reports')->with('error', __('pages.report_not_found'));
        }

        if ($role === 'manager' && !$this->service->managerOwnsReport($id, (int) $companyId, (int) ($employeeId ?? 0))) {
            return redirect()->route('project.reports')->with('error', __('pages.report_not_found'));
        }

        $reportDetails = $this->service->getReportDetails($id, $companyId);

        return view('management.project-reports-details')
            ->with('page', $page)
            ->with('reportDetails', $reportDetails)
            ->with('role', $role);
    }
    //---------------
    public function edit(int $id)
    {
        $page = 'Project Reports';
        $companyId = session('company_id');

        if (!$this->service->findOrFail($id, $companyId)) {
            return redirect()->route('project.reports')->with('error', __('pages.report_not_found'));
        }

        $projects = $this->service->getAllProjects($companyId);
        if (count($projects) === 0) {
            return redirect()->route('projects')->with('error', __('pages.register_projects_first'));
        }

        $editReport = $this->service->getReportById($id, $companyId);

        return view('management.project-reports')
            ->with('page', $page)
            ->with('projects', $projects)
            ->with('editReport', $editReport);
    }
    //---------------
    public function update(Request $request)
    {
        $companyId = session('company_id');
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|min:1|exists:project_reports,id',
            'project_id' => 'required|integer|min:1|exists:projects,id',
            'report' => 'required|string|min:1',
            'images' => 'nullable|array',
            'images.*' => 'image|max:5120',
        ]);

        if ($validator->fails()) {
            return back()->with('error', __('app.fields_invalid'))->withInput();
        }

        $id = (int) $request->id;

        if (!$this->service->findOrFail($id, $companyId)) {
            return redirect()->route('project.reports')->with('error', __('pages.report_not_found'));
        }

        if (!$this->service->projectExists((int) $request->project_id, $companyId)) {
            return back()->with('error', __('pages.project_not_found'))->withInput();
        }

        $data = [
            'project_id' => (int) $request->project_id,
            'report' => $request->report,
        ];

        if ($request->hasFile('images')) {
            $data['images'] = $request->file('images');
        }

        try {
            if (!$this->service->updateReport($id, $data, $companyId)) {
                return back()->with('error', __('pages.report_not_updated'))->withInput();
            }

            return redirect()->route('project.reports')->with('success', __('pages.project_report_updated'));
        } catch (\Exception $e) {
            return redirect()->route('project.reports')->with('error', __('app.try_again'));
        }
    }
    //---------------
    public function delete(int $id)
    {
        $companyId = session('company_id');

        if (!$this->service->findOrFail($id, $companyId)) {
            return redirect()->route('project.reports')->with('error', __('pages.report_not_found'));
        }

        try {
            $this->service->deleteReport($id, $companyId);
            return redirect()->route('project.reports')->with('success', __('pages.project_report_deleted'));
        } catch (\Exception $e) {
            return redirect()->route('project.reports')->with('error', __('app.try_again'));
        }
    }
    //---------------
    public function updateStatus(int $id, string $status)
    {
        $companyId = session('company_id');

        if (!$this->service->findOrFail($id, $companyId)) {
            return redirect()->route('project.reports')->with('error', __('pages.report_not_found'));
        }

        try {
            if (!$this->service->updateReportStatus($id, $status, $companyId)) {
                return redirect()->route('project.report.details', $id)->with('error', __('app.invalid_status'));
            }

            return redirect()->route('project.report.details', $id)->with('success', __('pages.report_status_updated'));
        } catch (\Exception $e) {
            return redirect()->route('project.report.details', $id)->with('error', __('app.try_again'));
        }
    }
}
