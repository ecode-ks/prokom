<?php

namespace App\Http\Controllers\API;

use App\Services\CheckInService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class CheckInApiController extends Controller
{
    protected CheckInService $service;

    public function __construct(CheckInService $service)
    {
        $this->service = $service;
    }
    //---------------
    public function read(Request $request)
    {
        $search = $request->query('search', '');
        $checkIns = $this->service->getCheckIns(10, $search);

        return json_encode(['checkIns' => $checkIns]);
    }
    //---------------
    public function map()
    {
        $locations = $this->service->getMapLocations();

        return json_encode(['checkIns' => $locations]);
    }
    //---------------
    public function details(int $id)
    {
        if (!$this->service->findOrFail($id)) {
            return json_encode(['error' => __('pages.checkin_not_found')]);
        }

        $checkinDetails = $this->service->getCheckInById($id);

        return json_encode(['checkinDetails' => $checkinDetails]);
    }
    //---------------
    public function accept(int $id)
    {
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
    //---------------
    public function delete(int $id)
    {
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
    //---------------
    public function startWork(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'check_in_location' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return json_encode(['error' => __('pages.data_invalid')]);
        }

        try {
            $this->service->startWork([
                'check_in_time' => now()->format('Y-m-d H:i:s'),
                'check_in_location' => $request->input('check_in_location'),
            ]);
            return json_encode(['success' => __('pages.checkin_work_started')]);
        } catch (\Exception $e) {
            return json_encode(['error' => __('pages.checkin_register_failed', ['message' => $e->getMessage()])]);
        }
    }
    //---------------
    public function startBreak(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:check_ins,id',
            'pause_start_location' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return json_encode(['error' => __('pages.data_invalid')]);
        }

        try {
            $ok = $this->service->startBreak(
                (int) $request->input('id'),
                [
                    'pause_start_time' => now()->format('Y-m-d H:i:s'),
                    'pause_start_location' => $request->input('pause_start_location'),
                ]
            );
            if (!$ok) {
                return json_encode(['error' => __('pages.checkin_not_found')]);
            }
            return json_encode(['success' => __('pages.checkin_pause_started')]);
        } catch (\Exception $e) {
            return json_encode(['error' => __('pages.checkin_register_failed', ['message' => $e->getMessage()])]);
        }
    }
    //---------------
    public function endBreak(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:check_ins,id',
            'pause_end_location' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return json_encode(['error' => __('pages.data_invalid')]);
        }

        try {
            $ok = $this->service->endBreak(
                (int) $request->input('id'),
                [
                    'pause_end_time' => now()->format('Y-m-d H:i:s'),
                    'pause_end_location' => $request->input('pause_end_location'),
                ]
            );
            if (!$ok) {
                return json_encode(['error' => __('pages.checkin_not_found')]);
            }
            return json_encode(['success' => __('pages.checkin_pause_ended')]);
        } catch (\Exception $e) {
            return json_encode(['error' => __('pages.checkin_register_failed', ['message' => $e->getMessage()])]);
        }
    }
    //---------------
    public function endWork(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:check_ins,id',
            'check_out_location' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return json_encode(['error' => __('pages.data_invalid')]);
        }

        try {
            $ok = $this->service->endWork(
                (int) $request->input('id'),
                [
                    'check_out_time' => now()->format('Y-m-d H:i:s'),
                    'check_out_location' => $request->input('check_out_location'),
                ]
            );
            if (!$ok) {
                return json_encode(['error' => __('pages.checkin_not_found')]);
            }
            return json_encode(['success' => __('pages.checkin_work_ended')]);
        } catch (\Exception $e) {
            return json_encode(['error' => __('pages.checkin_register_failed', ['message' => $e->getMessage()])]);
        }
    }
}
