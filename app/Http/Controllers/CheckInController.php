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
    public function startWork(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'check_in_time' => 'required|date',
            'check_in_location' => 'required|string|max:255'
        ]);
        if ($validator->fails()) {
            return redirect()->route('dashboard')->with('error','Të dhënat nuk janë të vlefshme. Provo përsëri.');
        } else {
            $data = $request->only(['check_in_time', 'check_in_location']);
            try {
                $this->service->startWork($data);
                return redirect()->route('dashboard')->with('success', 'Fillimi i punës u regjistrua me sukses.');
            } catch (\Exception $e) {
                return redirect()->route('dashboard')->with('error', 'Regjistrimi dështoi : ' . $e->getMessage())->withInput();
            }
        }
    }
    //---------------
    public function startBreak(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:check_ins,id',
            'pause_start_time' => 'required|date',
            'pause_start_location' => 'required|string|max:255'
        ]);
        if ($validator->fails()) {
            return redirect()->route('dashboard')->with('error','Të dhënat nuk janë të vlefshme. Provo përsëri.');
        } else {
            $id = $request->input('id');
            $data = $request->only(['pause_start_time', 'pause_start_location']);
            try {
                $this->service->startBreak($id, $data);
                return redirect()->route('dashboard')->with('success', 'Fillimi i pauzës u regjistrua me sukses.');
            } catch (\Exception $e) {
                return redirect()->route('dashboard')->with('error', 'Regjistrimi dështoi : ' . $e->getMessage())->withInput();
            }
        }
    }
    //---------------
    public function endBreak(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:check_ins,id',
            'pause_end_time' => 'required|date',
            'pause_end_location' => 'required|string|max:255'
        ]);
        if ($validator->fails()) {
            return redirect()->route('dashboard')->with('error','Të dhënat nuk janë të vlefshme. Provo përsëri.');
        } else {
            $id = $request->input('id');
            $data = $request->only(['pause_end_time', 'pause_end_location']);
            try {
                $this->service->endBreak($id, $data);
                return redirect()->route('dashboard')->with('success', 'Përfundimi i pauzës u regjistrua me sukses.');
            } catch (\Exception $e) {
                return redirect()->route('dashboard')->with('error', 'Regjistrimi dështoi : ' . $e->getMessage())->withInput();
            }
        }
    }
    //---------------
    public function endWork(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:check_ins,id',
            'check_out_time' => 'required|date',
            'check_out_location' => 'required|string|max:255'
        ]);
        if ($validator->fails()) {
            return redirect()->route('dashboard')->with('error','Të dhënat nuk janë të vlefshme. Provo përsëri.');
        } else {
            $id = $request->input('id');
            $data = $request->only(['check_out_time', 'check_out_location']);
            try {
                $this->service->endWork($id, $data);
                return redirect()->route('dashboard')->with('success', 'Përfundimi i punës u regjistrua me sukses.');
            } catch (\Exception $e) {
                return redirect()->route('dashboard')->with('error', 'Regjistrimi dështoi : ' . $e->getMessage())->withInput();
            }
        }
    }
    //---------------
    // public function read(Request $request)
    // {
    //     $page = 'Check In';
    //     $employees = $this->service->getAllEmployees();

    //     if (count($employees) === 0) {
    //         return redirect()->route('employees')->with('error', 'Ju lutem regjistroni punëtorët para se të përdorni këtë funksionalitet.');
    //     }

    //     $search = $request->query('search', '');
    //     $checkins = $this->service->getCheckIns(10, $search);
    //     return view('management.checkin')
    //         ->with('page', $page)
    //         ->with('employees', $employees)
    //         ->with('checkins', $checkins);
    // }
}
