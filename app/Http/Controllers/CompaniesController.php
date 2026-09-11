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
            'password_confirmation'=>'required|string|min:8|max:255',
            'language'=>'required|in:sq,en,de',
        ]);
        if($validator->fails()){
            return back()->with('error',__('pages.fields_invalid'))->withInput();
        }else{
            if($request->input('password') !== $request->input('password_confirmation')){
                return back()->with('error',__('pages.passwords_mismatch'))->withInput();
            }
            $data = $request->only(['company', 'email', 'phone', 'location', 'username', 'password', 'language']);
            try {
                $created = $this->companiesService->registerCompany($data);
                if(!$created) {
                    return back()->with('error', __('pages.register_failed'))->withInput();
                }else {
                    return redirect()->route('login')->with('success', __('pages.register_success'));
                }
            } catch (\Exception $e) {
                return back()->with('error', __('pages.register_failed_reason', ['message' => $e->getMessage()]))->withInput();
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
            return redirect()->route('companies')->with('error', __('pages.company_not_found'));
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
            return redirect()->route('companies')->with('error', __('pages.company_not_found'));
        }

        try {
            $updated = $this->companiesService->updateCompanyStatus($id, $status);
            if(!$updated) {
                return back()->with('error', __('pages.try_again'))->withInput();
            }else {
                return redirect()->route('company.details', $id)->with('success', __('pages.company_status_updated'));
            }
        } catch (\Exception $e) {
            return redirect()->route('company.details', $id)->with('error', __('pages.try_again'));
        }
    }
    //---------------
    public function delete(int $id)
    {
        if (!$this->companiesService->findOrFail($id)) {
            return redirect()->route('companies')->with('error', __('pages.company_not_found'));
        }

        try {
            $deleted = $this->companiesService->deleteCompany($id);
            if(!$deleted) {
                return back()->with('error', __('pages.try_again'))->withInput();
            }else {
                return redirect()->route('companies')->with('success', __('pages.company_deleted'));
            }
        } catch (\Exception $e) {
            return redirect()->route('companies')->with('error', __('pages.try_again'));
        }
    }
    //---------------
    public function payments(Request $request)
    {
        $page = 'Payments';
        $search = $request->query('search', '');
        $payments = $this->companiesService->getPayments(10, $search);
        return view('management.payments')
            ->with('page', $page)
            ->with('payments', $payments);
    }
}
