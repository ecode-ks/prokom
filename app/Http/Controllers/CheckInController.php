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
        if (session('role') !== 'company') {
            return redirect()->route('dashboard')->with('error', 'Nuk keni të drejtë për këtë faqe.');
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
            return redirect()->route('checkins')->with('error', 'Check-in nuk u gjet.');
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
            return redirect()->route('checkins')->with('error', 'Check-in nuk u gjet.');
        }

        try {
            $this->service->acceptCheckIn($id);
            return redirect()->route('checkins')->with('success', 'Check-in u pranua dhe orët e punës u regjistruan.');
        } catch (\Exception $e) {
            return redirect()->route('checkins')->with('error', $e->getMessage() ?: 'Pranimi dështoi. Provo përsëri.');
        }
    }
    //---------------
    public function delete(int $id)
    {
        if (!$this->service->findOrFail($id)) {
            return redirect()->route('checkins')->with('error', 'Check-in nuk u gjet.');
        }

        try {
            $this->service->deleteCheckIn($id);
            return redirect()->route('checkins')->with('success', 'Check-in u fshi me sukses.');
        } catch (\Exception $e) {
            return redirect()->route('checkins')->with('error', 'Fshirja dështoi. Provo përsëri.');
        }
    }
    //---------------
    public function startWork(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'check_in_location' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return redirect()->route('dashboard')->with('error', 'Të dhënat nuk janë të vlefshme. Provo përsëri.');
        }

        try {
            $this->service->startWork([
                'check_in_time' => now()->format('Y-m-d H:i:s'),
                'check_in_location' => $request->input('check_in_location'),
            ]);
            return redirect()->route('dashboard')->with('success', 'Fillimi i punës u regjistrua me sukses.');
        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', 'Regjistrimi dështoi: ' . $e->getMessage());
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
            return redirect()->route('dashboard')->with('error', 'Të dhënat nuk janë të vlefshme. Provo përsëri.');
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
                return redirect()->route('dashboard')->with('error', 'Check-in nuk u gjet.');
            }
            return redirect()->route('dashboard')->with('success', 'Fillimi i pauzës u regjistrua me sukses.');
        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', 'Regjistrimi dështoi: ' . $e->getMessage());
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
            return redirect()->route('dashboard')->with('error', 'Të dhënat nuk janë të vlefshme. Provo përsëri.');
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
                return redirect()->route('dashboard')->with('error', 'Check-in nuk u gjet.');
            }
            return redirect()->route('dashboard')->with('success', 'Përfundimi i pauzës u regjistrua me sukses.');
        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', 'Regjistrimi dështoi: ' . $e->getMessage());
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
            return redirect()->route('dashboard')->with('error', 'Të dhënat nuk janë të vlefshme. Provo përsëri.');
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
                return redirect()->route('dashboard')->with('error', 'Check-in nuk u gjet.');
            }
            return redirect()->route('dashboard')->with('success', 'Përfundimi i punës u regjistrua me sukses.');
        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', 'Regjistrimi dështoi: ' . $e->getMessage());
        }
    }
}
