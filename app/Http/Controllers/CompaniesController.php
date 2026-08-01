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
                $created = $this->companiesService->registerCompany($data);
                if(!$created) {
                    return back()->with('error', 'Diçka shkoi keq, ju lutemi provoni përsëri.')->withInput();
                }else {
                    return redirect()->route('login')->with('success', 'Kompania u regjistrua me sukses. Ju lutemi kyçuni.');
                }
            } catch (\Exception $e) {
                return back()->with('error', 'Regjistrimi dështoi: ' . $e->getMessage())->withInput();
            }
        }
    }
    //---------------
    public function read(Request $request)
    {
        $page = 'Companies';
        $search = $request->query('search', '');
        $companies = $this->companiesService->getCompanies(10, $search);
        $companyStats = $this->companiesService->getCompanyStatistics();
        return view('management.companies')
            ->with('page', $page)
            ->with('companies', $companies)
            ->with('companyStats', $companyStats);
    }
    //---------------
    public function details(int $id)
    {
        $page = 'Companies';
        if (!$this->companiesService->findOrFail($id)) {
            return redirect()->route('companies')->with('error', 'Kompania nuk u gjet');
        }

        $companyDetails = $this->companiesService->getCompanyById($id);
        return view('management.company-details')
            ->with('page', $page)
            ->with('companyDetails', $companyDetails);
    }
    //---------------
    public function updateStatus(int $id, string $status)
    {
        if (!$this->companiesService->findOrFail($id)) {
            return redirect()->route('companies')->with('error', 'Kompania nuk u gjet');
        }

        try {
            $updated = $this->companiesService->updateCompanyStatus($id, $status);
            if(!$updated) {
                return back()->with('error', 'Diçka shkoi keq, ju lutemi provoni përsëri.')->withInput();
            }else {
                return redirect()->route('company.details', $id)->with('success', 'Statusi i kompanisë u përditësua me sukses.');
            }
        } catch (\Exception $e) {
            return redirect()->route('company.details', $id)->with('error', 'Diçka shkoi keq, ju lutemi provoni përsëri.');
        }
    }
    //---------------
    public function delete(int $id)
    {
        if (!$this->companiesService->findOrFail($id)) {
            return redirect()->route('companies')->with('error', 'Kompania nuk u gjet');
        }

        try {
            $deleted = $this->companiesService->deleteCompany($id);
            if(!$deleted) {
                return back()->with('error', 'Diçka shkoi keq, ju lutemi provoni përsëri.')->withInput();
            }else {
                return redirect()->route('companies')->with('success', 'Kompania u fshi me sukses.');
            }
        } catch (\Exception $e) {
            return redirect()->route('companies')->with('error', 'Diçka shkoi keq, ju lutemi provoni përsëri.');
        }
    }
}
