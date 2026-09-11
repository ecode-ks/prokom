<?php

namespace App\Http\Controllers\API;

use App\Services\UsersService;
use App\Services\PackagesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class UsersApiController extends Controller
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
    public function updateProfilePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required|string|min:8|max:255',
            'new_password' => 'required|string|min:8|max:255',
            'new_password_confirmation' => 'required|string|min:8|max:255'
        ]);

        if($validator->fails()){
            return response()->json(['error' => __('pages.fields_invalid')], 422);
        }else{
            if ($request->new_password !== $request->new_password_confirmation) {
                return response()->json(['error' => __('pages.new_passwords_mismatch')], 422);
            }
            $username = $request->input('username');

            try {
                $created = $this->usersService->updateProfilePassword($request->old_password, $request->new_password, $username);
                if ($created) {
                    return response()->json(['success' => __('pages.password_updated')], 200);
                } else {
                    return response()->json(['error' => __('pages.old_password_wrong')], 422);
                }
            } catch (\Exception $e) {
                return response()->json(['error' => __('pages.register_failed')], 422);
            }
        }
    }
    //---------------
    public function updateCompanyLanguage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'language' => 'required|in:sq,en,de',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => __('pages.fields_invalid')], 422);
        }

        $companyId = (int) $request->input('company_id');
        if (!$companyId) {
            return response()->json(['error' => __('pages.language_update_failed')], 422);
        }

        try {
            $updated = $this->usersService->updateCompanyLanguage($companyId, $request->input('language'));
            if ($updated) {
                return response()->json(['success' => __('pages.language_updated')], 200);
            }
            return response()->json(['error' => __('pages.language_update_failed')], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => __('pages.language_update_failed')], 422);
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
            return response()->json(['error' => __('pages.fields_invalid')], 422);
        }else{
            try {
                $this->usersService->checkLogin($request->only(['username', 'password']));
                return response()->json(['success' => __('pages.login_success')], 200);
            } catch (\Exception $e) {
                return response()->json(['error' => __('pages.login_failed', ['message' => $e->getMessage()])], 401);
            }
        }
    }
}
