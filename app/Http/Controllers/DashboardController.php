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
        $page = 'Dashboard';
        $checkIn = $this->checkinService->getActiveCheckIn();
        return view('management.dashboard')
            ->with('page', $page)
            ->with('checkIn', $checkIn);
    }
}
