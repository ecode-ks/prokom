<?php

namespace App\Http\Controllers;

use App\Services\UsersService;
use App\Services\PackagesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    //---------------
    protected UsersService $usersService;
    protected PackagesService $packagesService;
    public function __construct(UsersService $usersService, PackagesService $packagesService)
    {
        $this->usersService = $usersService;
        $this->packagesService = $packagesService;
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
            try {
                $this->usersService->registerAdmin($data);
                return redirect()->route('login')->with('success', 'Përdoruesi administrator u krijua me sukses. Ju lutemi identifikohuni.');
            } catch (\Exception $e) {
                    return back()->with('error', 'Dështoi krijimi i përdoruesit administrator. Ju lutemi provoni përsëri.');
            }
        }
    }
    //---------------
    public function profile()
    {
        $page = 'My Profile';
        $packages = $this->packagesService->getAllPackages();
        return view('management.profile')
            ->with('page', $page)
            ->with('packages', $packages);
    }
    //---------------
    public function updateProfilePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required|string|min:8|max:255',
            'new_password' => 'required|string|min:8|max:255',
            'new_password_confirmation' => 'required|string|min:8|max:255'
        ]);

        if($validator->fails()){
            return back()->with('error','Të gjitha fushat duhet të plotësohen sipas rregullave.');
        }else{
            if ($request->new_password !== $request->new_password_confirmation) {
                return back()->with('error', 'Fjalëkalimi i ri dhe konfirmimi i fjalëkalimit nuk përputhen.')->withInput();
            }
            $username = session()->get('username');

            try {
                $created = $this->usersService->updateProfilePassword($request->old_password, $request->new_password, $username);
                if ($created) {
                    return back()->with('success', 'Fjalëkalimi u përditësua me sukses.');
                } else {
                    return back()->with('error', 'Fjalëkalimi i vjetër është i pasaktë.')->withInput();
                }
            } catch (\Exception $e) {
                return back()->with('error', 'Diçka shkoi keq, ju lutemi provoni përsëri.')->withInput();
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
                return redirect()->route('dashboard')->with('success', 'Mirë se vini në PROKOM! Jeni kyçur me sukses.');
            } catch (\Exception $e) {
                return back()->with('error', 'Kyçja dështoi: ' . $e->getMessage())->withInput();
            }
        }
    }
    //---------------
    public function logout()
    {
        session()->flush();
        return redirect()->route('login')
            ->with('success', 'Ju jeni shkyçur me sukses nga sistemi.');
    }
}
