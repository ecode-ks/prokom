<?php

namespace App\Http\Controllers;

use App\Services\CheckInService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CheckInController extends Controller
{
    protected CheckInService $service;

    public function __construct(CheckInService $service)
    {
        $this->service = $service;
    }
    //---------------
    public function read(Request $request)
    {
        $page = 'Check In';
        $search = $request->query('search', '');
        $checkIns = $this->service->getCheckIns(10, $search);

        return view('management.checkin')
            ->with('page', $page)
            ->with('checkIns', $checkIns);
    }
    //---------------
    public function map()
    {
        if (!in_array(session('role'), ['company', 'manager'], true)) {
            return redirect()->route('dashboard')->with('error', __('pages.no_access'));
        }

        $locations = $this->service->getMapLocations();

        return view('management.checkin-map')
            ->with('page', 'Check In Map')
            ->with('locations', $locations);
    }
    //---------------
    public function details(int $id)
    {
        $page = 'Check In';
        if (!$this->service->findOrFail($id)) {
            return $this->redirectAfterManage()->with('error', __('pages.checkin_not_found'));
        }

        $checkinDetails = $this->service->getCheckInById($id);

        return view('management.checkin-details')
            ->with('page', $page)
            ->with('checkinDetails', $checkinDetails);
    }
    //---------------
    public function accept(int $id)
    {
        if (!$this->service->findOrFail($id)) {
            return $this->redirectAfterManage()->with('error', __('pages.checkin_not_found'));
        }

        try {
            $this->service->acceptCheckIn($id);
            return $this->redirectAfterManage()->with('success', __('pages.checkin_accepted_msg'));
        } catch (\Exception $e) {
            return $this->redirectAfterManage()->with('error', $e->getMessage() ?: __('pages.checkin_accept_failed'));
        }
    }
    //---------------
    public function delete(int $id)
    {
        if (!$this->service->findOrFail($id)) {
            return $this->redirectAfterManage()->with('error', __('pages.checkin_not_found'));
        }

        try {
            $this->service->deleteCheckIn($id);
            return $this->redirectAfterManage()->with('success', __('pages.checkin_deleted'));
        } catch (\Exception $e) {
            return $this->redirectAfterManage()->with('error', __('pages.checkin_delete_failed'));
        }
    }
    //---------------
    private function redirectAfterManage()
    {
        if (in_array(session('role'), ['company', 'manager'], true)) {
            return redirect()->route('checkins');
        }

        return redirect()->route('dashboard');
    }
    //---------------
    public function startWork(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'check_in_location' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return redirect()->route('dashboard')->with('error', __('pages.data_invalid'));
        }

        try {
            $this->service->startWork([
                'check_in_time' => now()->format('Y-m-d H:i:s'),
                'check_in_location' => $request->input('check_in_location'),
            ]);
            return redirect()->route('dashboard')->with('success', __('pages.checkin_work_started'));
        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', __('pages.checkin_register_failed', ['message' => $e->getMessage()]));
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
            return redirect()->route('dashboard')->with('error', __('pages.data_invalid'));
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
                return redirect()->route('dashboard')->with('error', __('pages.checkin_not_found'));
            }
            return redirect()->route('dashboard')->with('success', __('pages.checkin_pause_started'));
        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', __('pages.checkin_register_failed', ['message' => $e->getMessage()]));
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
            return redirect()->route('dashboard')->with('error', __('pages.data_invalid'));
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
                return redirect()->route('dashboard')->with('error', __('pages.checkin_not_found'));
            }
            return redirect()->route('dashboard')->with('success', __('pages.checkin_pause_ended'));
        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', __('pages.checkin_register_failed', ['message' => $e->getMessage()]));
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
            return redirect()->route('dashboard')->with('error', __('pages.data_invalid'));
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
                return redirect()->route('dashboard')->with('error', __('pages.checkin_not_found'));
            }
            return redirect()->route('dashboard')->with('success', __('pages.checkin_work_ended'));
        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', __('pages.checkin_register_failed', ['message' => $e->getMessage()]));
        }
    }
}
