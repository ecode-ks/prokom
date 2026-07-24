<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    //---------------
    public function dashboard()
    {
        $page = 'Dashboard';
        return view('management.dashboard')
            ->with('page', $page);
    }
}
