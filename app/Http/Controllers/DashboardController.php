<?php

namespace App\Http\Controllers;

use App\Services\CheckInService;
use App\Services\TasksService;

class DashboardController extends Controller
{
    protected CheckInService $checkinService;
    protected TasksService $tasksService;

    public function __construct(CheckInService $checkinService, TasksService $tasksService)
    {
        $this->checkinService = $checkinService;
        $this->tasksService = $tasksService;
    }
    //---------------
    public function dashboard()
    {
        $canCheckIn = $this->checkinService->canCheckIn();
        $checkIn = $canCheckIn ? $this->checkinService->getTodayCheckIn() : null;
        $isActive = $checkIn && empty($checkIn->check_out_time);

        $employeeId = (int) (session('employee_id') ?? 0);
        $companyId = (int) (session('company_id') ?? 0);
        $showMyTasks = $employeeId > 0 && session('role') !== 'company';
        $myTasks = $showMyTasks
            ? $this->tasksService->getMyOpenTasks($employeeId, $companyId, 5)
            : collect();

        return view('management.dashboard')
            ->with('page', 'Dashboard')
            ->with('canCheckIn', $canCheckIn)
            ->with('checkIn', $checkIn)
            ->with('isActive', $isActive)
            ->with('showMyTasks', $showMyTasks)
            ->with('myTasks', $myTasks);
    }
}
