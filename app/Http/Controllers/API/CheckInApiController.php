<?php

namespace App\Http\Controllers\API;

use App\Models\EmployeesModel;
use App\Services\Api\CheckInApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class CheckInApiController extends Controller
{
    protected CheckInApiService $service;

    public function __construct(CheckInApiService $service)
    {
        $this->service = $service;
    }

    public function read(Request $request, int $companyId, int $employeeId, string $role)
    {
        if (!$this->canList($role)) {
            return json_encode(['error' => __('pages.no_access')]);
        }

        $search = $request->query('search', '');
        $checkIns = $this->service->getCheckIns(10, $search, $companyId);

        return json_encode([
            'checkIns' => $checkIns,
            'role' => $role,
        ]);
    }

    /**
     * Return the authenticated employee's latest unfinished check-in.
     */
    public function active(Request $request)
    {
        $account = $request->user();

        if (!$account instanceof EmployeesModel || empty($account->company_id)) {
            return response()->json([
                'success' => true,
                'has_active_checkin' => false,
                'checkin' => null,
            ]);
        }

        $checkin = $this->service->getActiveCheckIn(
            (int) $account->id,
            (int) $account->company_id
        );

        return response()->json([
            'success' => true,
            'has_active_checkin' => $checkin !== null,
            'checkin' => $checkin,
        ]);
    }

    public function map(Request $request, int $companyId, int $employeeId, string $role)
    {
        if (!$this->canList($role)) {
            return json_encode(['error' => __('pages.no_access')]);
        }

        $normalizedRole = strtolower($role);
        $managerId = $normalizedRole === 'manager' ? $employeeId : null;
        $projectId = $managerId && $request->filled('project_id') ? (int) $request->query('project_id') : null;

        if ($normalizedRole === 'manager' && $employeeId <= 0) {
            return json_encode([
                'checkIns' => [],
                'projects' => [],
                'role' => $role,
            ]);
        }

        $projects = $this->service->getMapProjects($companyId, $managerId ?: null);
        $locations = $this->service->attachMapProjectLinks(
            $this->service->getMapLocations($companyId, $managerId ?: null, $projectId),
            $projects,
            $companyId,
            $managerId ?: null
        );

        return json_encode([
            'checkIns' => $locations,
            'projects' => $projects,
            'role' => $role,
        ]);
    }

    public function details(int $id, int $companyId, int $employeeId, string $role)
    {
        if (!$this->canManage($role)) {
            return json_encode(['error' => __('pages.no_access')]);
        }

        if (!$this->service->findOrFail($id, $companyId)) {
            return json_encode(['error' => __('pages.checkin_not_found')]);
        }

        return json_encode([
            'checkinDetails' => $this->service->getCheckInById($id, $companyId),
            'role' => $role,
        ]);
    }

    public function accept(int $id, int $companyId, string $reporter, int $employeeId, string $role)
    {
        if (!$this->canManage($role)) {
            return json_encode(['error' => __('pages.no_access')]);
        }

        if (!$this->service->findOrFail($id, $companyId)) {
            return json_encode(['error' => __('pages.checkin_not_found')]);
        }

        try {
            $this->service->acceptCheckIn($id, $reporter, $companyId);
            return json_encode(['success' => __('pages.checkin_accepted_msg')]);
        } catch (\Exception $e) {
            return json_encode(['error' => $e->getMessage() ?: __('pages.checkin_accept_failed')]);
        }
    }

    public function delete(int $id, int $companyId, int $employeeId, string $role)
    {
        if (!$this->canManage($role)) {
            return json_encode(['error' => __('pages.no_access')]);
        }

        if (!$this->service->findOrFail($id, $companyId)) {
            return json_encode(['error' => __('pages.checkin_not_found')]);
        }

        try {
            $this->service->deleteCheckIn($id, $companyId);
            return json_encode(['success' => __('pages.checkin_deleted')]);
        } catch (\Exception $e) {
            return json_encode(['error' => __('pages.checkin_delete_failed')]);
        }
    }

    public function startWork(Request $request, int $companyId, int $employeeId, string $role)
    {
        if (!$this->canCheckIn($role)) {
            return json_encode(['error' => __('pages.no_access')]);
        }

        $validator = Validator::make($request->all(), [
            'check_in_location' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return json_encode(['error' => __('pages.data_invalid')]);
        }

        try {
            $this->bindActor($request, $companyId, $employeeId, $role);
            $this->service->startWork([
                'check_in_time' => now()->format('Y-m-d H:i:s'),
                'check_in_location' => $request->input('check_in_location'),
            ]);
            return json_encode(['success' => __('pages.checkin_work_started')]);
        } catch (\Exception $e) {
            return json_encode(['error' => __('pages.checkin_register_failed', ['message' => $e->getMessage()])]);
        }
    }

    public function startBreak(Request $request, int $companyId, int $employeeId, string $role)
    {
        if (!$this->canCheckIn($role)) {
            return json_encode(['error' => __('pages.no_access')]);
        }

        $validator = Validator::make($request->all(), [
            'pause_start_location' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return json_encode(['error' => __('pages.data_invalid')]);
        }

        try {
            $this->bindActor($request, $companyId, $employeeId, $role);
            $ok = $this->service->startBreak([
                'pause_start_time' => now()->format('Y-m-d H:i:s'),
                'pause_start_location' => $request->input('pause_start_location'),
            ]);
            if (!$ok) {
                return json_encode(['error' => __('pages.checkin_not_found')]);
            }
            return json_encode(['success' => __('pages.checkin_pause_started')]);
        } catch (\Exception $e) {
            return json_encode(['error' => __('pages.checkin_register_failed', ['message' => $e->getMessage()])]);
        }
    }

    public function endBreak(Request $request, int $companyId, int $employeeId, string $role)
    {
        if (!$this->canCheckIn($role)) {
            return json_encode(['error' => __('pages.no_access')]);
        }

        $validator = Validator::make($request->all(), [
            'pause_end_location' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return json_encode(['error' => __('pages.data_invalid')]);
        }

        try {
            $this->bindActor($request, $companyId, $employeeId, $role);
            $ok = $this->service->endBreak([
                'pause_end_time' => now()->format('Y-m-d H:i:s'),
                'pause_end_location' => $request->input('pause_end_location'),
            ]);
            if (!$ok) {
                return json_encode(['error' => __('pages.checkin_not_found')]);
            }
            return json_encode(['success' => __('pages.checkin_pause_ended')]);
        } catch (\Exception $e) {
            return json_encode(['error' => __('pages.checkin_register_failed', ['message' => $e->getMessage()])]);
        }
    }

    public function endWork(Request $request, int $companyId, int $employeeId, string $role)
    {
        if (!$this->canCheckIn($role)) {
            return json_encode(['error' => __('pages.no_access')]);
        }

        $validator = Validator::make($request->all(), [
            'check_out_location' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return json_encode(['error' => __('pages.data_invalid')]);
        }

        try {
            $this->bindActor($request, $companyId, $employeeId, $role);
            $ok = $this->service->endWork([
                'check_out_time' => now()->format('Y-m-d H:i:s'),
                'check_out_location' => $request->input('check_out_location'),
            ]);
            if (!$ok) {
                return json_encode(['error' => __('pages.checkin_not_found')]);
            }
            return json_encode(['success' => __('pages.checkin_work_ended')]);
        } catch (\Exception $e) {
            return json_encode(['error' => __('pages.checkin_register_failed', ['message' => $e->getMessage()])]);
        }
    }

    private function bindActor(Request $request, int $companyId, int $employeeId, string $role): void
    {
        $this->service->bindActor(
            $request->input('username'),
            $employeeId ?: ($request->filled('employee_id') ? (int) $request->input('employee_id') : null),
            $companyId ?: ($request->filled('company_id') ? (int) $request->input('company_id') : null),
            $role ?: $request->input('role')
        );
    }

    private function canList(string $role): bool
    {
        return in_array($role, ['company', 'manager', 'hr'], true);
    }

    private function canManage(string $role): bool
    {
        return in_array($role, ['company', 'manager', 'hr'], true);
    }

    private function canCheckIn(string $role): bool
    {
        return in_array($role, ['company', 'manager', 'hr', 'finance', 'employee'], true);
    }
}
