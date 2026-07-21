<?php

namespace App\Http\Controllers;

use App\Services\UsersService;

class UsersController extends Controller
{
    //---------------
    protected UsersService $usersService;
    public function __construct(UsersService $usersService)
    {
        $this->usersService = $usersService;
    }
    //---------------
    public function login()
    {
        if($this->usersService->checkAdminExists()){
            $admin = true;
        }
        return view('login')
            ->with('admin', $admin ?? false);
    }
}
