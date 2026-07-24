<?php

namespace App\Http\Controllers;

use App\Services\UsersService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
    //---------------
    public function registerAdmin(Request $request)
    {
        if($this->usersService->checkAdminExists()){
            return back()->with('error','Një përdorues administrator ekziston tashmë.');
        }

        $validator=Validator::make($request->all(),[
            'user'=>'required|string|min:3|max:255',
            'username'=>'required|string|min:3|max:255',
            'password'=>'required|string|min:8|max:255'
        ]);
        if($validator->fails()){
            return back()->with('error','Të gjitha fushat duhet të plotësohen sipas rregullave.');
        }else{
            $data = $request->only(['user', 'username', 'password']);
            $created = $this->usersService->registerAdmin($data);
            if($created){
                return redirect()->route('login')->with('success', 'Përdoruesi administrator u krijua me sukses. Ju lutemi identifikohuni.');
            }else{
                return back()->with('error', 'Dështoi krijimi i përdoruesit administrator. Ju lutemi provoni përsëri.');
            }
        }
    }
    //---------------
    public function checkLogin(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'username'=>'required|string|min:3|max:255',
            'password'=>'required|string|min:8|max:255'
        ]);
        if($validator->fails()){
            return back()->with('error','Të gjitha fushat duhet të plotësohen sipas rregullave.');
        }else{
            try {
                $this->usersService->checkLogin($request->only(['username', 'password']));

                if ((string) session('role') === 'company' && session('payment_expired') === true) {
                    return redirect()->route('payment.expired')->with('error', 'Your subscription has expired. Please select a new package to continue.');
                }

                return redirect()->route('dashboard')->with('success', 'Welcome to C-Flow! You have successfully logged in.');
            } catch (\Exception $e) {
                return back()->with('error', 'Failed to log in : ' . $e->getMessage())->withInput();
            }
        }
    }
}
