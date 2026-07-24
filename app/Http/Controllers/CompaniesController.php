<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\CompaniesService;

class CompaniesController extends Controller
{
    //---------------
    protected CompaniesService $companiesService;
    public function __construct(CompaniesService $companiesService)
    {
        $this->companiesService = $companiesService;
    }
    //---------------
    public function register()
    {
        return view('register');
    }
    //---------------
    public function registerCompany(Request $request)
    {
        $page = 'Register Company';
        $validator=Validator::make($request->all(),[
            'company'=>'required|string|min:3|max:255',
            'email'=>'required|email|min:3|max:255',
            'phone'=>'required|string|min:3|max:255',
            'location'=>'required|string|min:3|max:255',
            'username'=>'required|string|min:3|max:255',
            'password'=>'required|string|min:8|max:255',
            'password_confirmation'=>'required|string|min:8|max:255'
        ]);
        if($validator->fails()){
            return back()->with('error','Të gjitha fushat duhet të plotësohen sipas rregullave.')->withInput();
        }else{
            if($request->input('password') !== $request->input('password_confirmation')){
                return back()->with('error','Fjalëkalimi dhe Konfirmimi i fjalëkalimit nuk përputhen.')->withInput();
            }
            $data = $request->only(['company', 'email', 'phone', 'location', 'username', 'password']);
            try {
                $this->companiesService->registerCompany($data);
                return redirect()->route('login')->with('success', 'Kompania u regjistrua me sukses. Ju lutemi kyçuni.');
            } catch (\Exception $e) {
                return back()->with('error', 'Regjistrimi dështoi: ' . $e->getMessage())->withInput();
            }
        }
    }
}
