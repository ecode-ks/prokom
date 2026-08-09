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
        ]);

        if ($validator->fails()) {
            return redirect()->route('dashboard')->with('error', 'Të dhënat nuk janë të vlefshme. Provo përsëri.');
        }

        try {
            $ok = $this->service->startBreak(
                (int) $request->input('id'),
                ['pause_start_time' => now()->format('Y-m-d H:i:s')]
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
        ]);

        if ($validator->fails()) {
            return redirect()->route('dashboard')->with('error', 'Të dhënat nuk janë të vlefshme. Provo përsëri.');
        }

        try {
            $ok = $this->service->endBreak(
                (int) $request->input('id'),
                ['pause_end_time' => now()->format('Y-m-d H:i:s')]
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
