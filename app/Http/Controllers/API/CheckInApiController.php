<?php

namespace App\Http\Controllers\API;

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

    private function bindFromRequest(Request $request): void
    {
        $this->service->bindActor(
            $request->input('username'),
            $request->filled('employee_id') ? (int) $request->input('employee_id') : null,
            $request->filled('company_id') ? (int) $request->input('company_id') : null,
            $request->input('role')
        );
    }

    public function read(Request $request)
    {
        try {
            $this->bindFromRequest($request);
        } catch (\Exception $e) {
            return json_encode(['error' => $e->getMessage()]);
        }

        $search = $request->query('search', '');
        $checkIns = $this->service->getCheckIns(10, $search);

        return json_encode(['checkIns' => $checkIns]);
    }

    public function map(Request $request)
    {
        try {
            $this->bindFromRequest($request);
        } catch (\Exception $e) {
            return json_encode(['error' => $e->getMessage()]);
        }

        $locations = $this->service->getMapLocations();

        return json_encode(['checkIns' => $locations]);
    }

    public function details(Request $request, int $id)
    {
        try {
            $this->bindFromRequest($request);
        } catch (\Exception $e) {
            return json_encode(['error' => $e->getMessage()]);
        }

        if (!$this->service->findOrFail($id)) {
            return json_encode(['error' => __('pages.checkin_not_found')]);
        }

        $checkinDetails = $this->service->getCheckInById($id);

        return json_encode(['checkinDetails' => $checkinDetails]);
    }

    public function accept(Request $request, int $id)
    {
        try {
            $this->bindFromRequest($request);
        } catch (\Exception $e) {
            return json_encode(['error' => $e->getMessage()]);
        }

        if (!$this->service->findOrFail($id)) {
            return json_encode(['error' => __('pages.checkin_not_found')]);
        }

        try {
            $this->service->acceptCheckIn($id);
            return json_encode(['success' => __('pages.checkin_accepted_msg')]);
        } catch (\Exception $e) {
            return json_encode(['error' => $e->getMessage() ?: __('pages.checkin_accept_failed')]);
        }
    }

    public function delete(Request $request, int $id)
    {
        try {
            $this->bindFromRequest($request);
        } catch (\Exception $e) {
            return json_encode(['error' => $e->getMessage()]);
        }

        if (!$this->service->findOrFail($id)) {
            return json_encode(['error' => __('pages.checkin_not_found')]);
        }

        try {
            $this->service->deleteCheckIn($id);
            return json_encode(['success' => __('pages.checkin_deleted')]);
        } catch (\Exception $e) {
            return json_encode(['error' => __('pages.checkin_delete_failed')]);
        }
    }

    public function startWork(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'check_in_location' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return json_encode(['error' => __('pages.data_invalid')]);
        }

        try {
            $this->bindFromRequest($request);
            $this->service->startWork([
                'check_in_time' => now()->format('Y-m-d H:i:s'),
                'check_in_location' => $request->input('check_in_location'),
            ]);
            return json_encode(['success' => __('pages.checkin_work_started')]);
        } catch (\Exception $e) {
            return json_encode(['error' => __('pages.checkin_register_failed', ['message' => $e->getMessage()])]);
        }
    }

    public function startBreak(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pause_start_location' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return json_encode(['error' => __('pages.data_invalid')]);
        }

        try {
            $this->bindFromRequest($request);
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

    public function endBreak(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pause_end_location' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return json_encode(['error' => __('pages.data_invalid')]);
        }

        try {
            $this->bindFromRequest($request);
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

    public function endWork(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'check_out_location' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return json_encode(['error' => __('pages.data_invalid')]);
        }

        try {
            $this->bindFromRequest($request);
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
}
