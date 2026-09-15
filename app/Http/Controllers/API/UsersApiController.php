<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\Api\UsersApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UsersApiController extends Controller
{
    protected UsersApiService $usersService;

    public function __construct(UsersApiService $usersService)
    {
        $this->usersService = $usersService;
    }

    public function checkLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|min:3|max:255',
            'password' => 'required|string|min:8|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => __('pages.fields_invalid'),
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $identity = $this->usersService->checkLogin([
                'username' => $request->input('username'),
                'password' => $request->input('password'),
            ]);

            return response()->json([
                'success' => __('pages.login_success'),
                'token' => $identity['token'],
                'token_type' => 'Bearer',
                'user' => $identity['user'],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 401);
        }
    }

    /**
     * Return currently authenticated account information.
     */
    public function profile(Request $request)
    {
        $account = $request->user();

        if (!$account) {
            return response()->json([
                'error' => __('pages.unauthenticated'),
            ], 401);
        }

        return response()->json([
            'success' => true,
            'user' => $this->usersService->getAuthenticatedIdentity($account),
        ], 200);
    }

    /**
     * Logout current device/token.
     */
    public function logout(Request $request)
    {
        $account = $request->user();

        if (!$account || !$account->currentAccessToken()) {
            return response()->json([
                'error' => __('pages.unauthenticated'),
            ], 401);
        }

        $account->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => __('pages.logout_success'),
        ], 200);
    }

    /**
     * Change password for the currently authenticated account.
     */
    public function updateProfilePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required|string|min:8|max:255',
            'new_password' => 'required|string|min:8|max:255',
            'new_password_confirmation' => 'required|string|min:8|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => __('pages.fields_invalid'),
                'errors' => $validator->errors(),
            ], 422);
        }

        if ($request->new_password !== $request->new_password_confirmation) {
            return response()->json([
                'error' => __('pages.new_passwords_mismatch'),
            ], 422);
        }

        try {
            $updated = $this->usersService->updateProfilePassword(
                $request->user(),
                $request->input('old_password'),
                $request->input('new_password')
            );

            if (!$updated) {
                return response()->json([
                    'error' => __('pages.old_password_wrong'),
                ], 422);
            }

            return response()->json([
                'success' => __('pages.password_updated'),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => __('pages.register_failed'),
            ], 422);
        }
    }

    // /**
    //  * Update company language.
    //  */
    // public function updateCompanyLanguage(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'language' => 'required|in:sq,en,de',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'error' => __('pages.fields_invalid'),
    //         ], 422);
    //     }

    //     try {
    //         $updated = $this->usersService->updateCompanyLanguage(
    //             $request->user(),
    //             $request->input('language')
    //         );

    //         if (!$updated) {
    //             return response()->json([
    //                 'error' => __('pages.language_update_failed'),
    //             ], 422);
    //         }

    //         return response()->json([
    //             'success' => __('pages.language_updated'),
    //         ], 200);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'error' => __('pages.language_update_failed'),
    //         ], 422);
    //     }
    // }
}
