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
            return back()->with('error',__('pages.admin_exists'));
        }

        $validator=Validator::make($request->all(),[
            'user'=>'required|string|min:3|max:255',
            'username'=>'required|string|min:3|max:255',
            'password'=>'required|string|min:8|max:255'
        ]);
        if($validator->fails()){
            return back()->with('error',__('pages.fields_invalid'));
        }else{
            $data = $request->only(['user', 'username', 'password']);
            try {
                $this->usersService->registerAdmin($data);
                return redirect()->route('login')->with('success', __('pages.admin_created'));
            } catch (\Exception $e) {
                    return back()->with('error', __('pages.admin_create_failed'));
            }
        }
    }
    //---------------
    public function profile()
    {
        $page = 'My Profile';
        $language = session('language');
        $packages = $this->packagesService->getAllPackages($language);
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
            return back()->with('error',__('pages.fields_invalid'));
        }else{
            if ($request->new_password !== $request->new_password_confirmation) {
                return back()->with('error', __('pages.new_passwords_mismatch'))->withInput();
            }
            $username = session()->get('username');

            try {
                $created = $this->usersService->updateProfilePassword($request->old_password, $request->new_password, $username);
                if ($created) {
                    return back()->with('success', __('pages.password_updated'));
                } else {
                    return back()->with('error', __('pages.old_password_wrong'))->withInput();
                }
            } catch (\Exception $e) {
                return back()->with('error', __('pages.register_failed'))->withInput();
            }
        }
    }
    //---------------
//    public function updateCompanyLanguage(Request $request)
//    {
//        $validator = Validator::make($request->all(), [
//            'language' => 'required|in:sq,en,de',
//        ]);
//
//        if ($validator->fails()) {
//            return back()->with('error', __('pages.fields_invalid'));
//        }
//
//        $companyId = (int) session('company_id');
//        if (!$companyId) {
//            return back()->with('error', __('pages.language_update_failed'));
//        }
//
//        try {
//            $updated = $this->usersService->updateCompanyLanguage($companyId, $request->input('language'));
//            if ($updated) {
//                return back()->with('success', __('pages.language_updated'));
//            }
//            return back()->with('error', __('pages.language_update_failed'));
//        } catch (\Exception $e) {
//            return back()->with('error', __('pages.language_update_failed'));
//        }
//    }
    //---------------
    public function checkLogin(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'username'=>'required|string|min:3|max:255',
            'password'=>'required|string|min:8|max:255'
        ]);
        if($validator->fails()){
            return back()->with('error',__('pages.fields_invalid'));
        }else{
            try {
                $this->usersService->checkLogin($request->only(['username', 'password']));
                return redirect()->route('dashboard')->with('success', __('pages.login_success'));
            } catch (\Exception $e) {
                return back()->with('error', __('pages.login_failed', ['message' => $e->getMessage()]))->withInput();
            }
        }
    }
    //---------------
    public function logout()
    {
        session()->flush();
        return redirect()->route('login')
            ->with('success', __('pages.logout_success'));
    }
}
