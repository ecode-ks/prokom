<?php

namespace App\Http\Controllers;

use App\Services\CheckInService;

class DashboardController extends Controller
{
    protected CheckInService $checkinService;

    public function __construct(CheckInService $checkinService)
    {
        $this->checkinService = $checkinService;
    }
    //---------------
    public function dashboard()
    {
        $canCheckIn = $this->checkinService->canCheckIn();
        $checkIn = $canCheckIn ? $this->checkinService->getTodayCheckIn() : null;
        $isActive = $checkIn && empty($checkIn->check_out_time);

        return view('management.dashboard')
            ->with('page', 'Dashboard')
            ->with('canCheckIn', $canCheckIn)
            ->with('checkIn', $checkIn)
            ->with('isActive', $isActive);
    }
}
