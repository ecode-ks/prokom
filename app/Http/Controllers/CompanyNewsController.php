<?php

namespace App\Http\Controllers;

use App\Services\CompanyNewsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompanyNewsController extends Controller
{
    protected CompanyNewsService $service;
    public function __construct(CompanyNewsService $service)
    {
        $this->service = $service;
    }
    //---------------
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'info' => 'required|string|min:3',
            'project_id' => 'nullable|integer|min:1|exists:projects,id',
        ]);

        if ($validator->fails()) {
            return redirect()->route('company.news')->with('error', 'Të gjitha fushat duhet të plotësohen sipas rregullave.')->withInput();
        } else {
            $data = $request->only(['date', 'info', 'project_id']);
            if (empty($data['project_id'])) {
                $data['project_id'] = null;
            }

            try {
                $this->service->createCompanyNews($data);
                return redirect()->route('company.news')->with('success', 'Lajmi u publikua me sukses.');
            } catch (\Exception $e) {
                return redirect()->route('company.news')->with('error', 'Diçka shkoi gabim, ju lutemi provoni përsëri.')->withInput();
            }
        }
    }
    //---------------
    public function read(Request $request)
    {
        $page = 'Company News';
        $search = $request->query('search', '');
        $projects = $this->service->getAllProjects();
        $companyNews = $this->service->getCompanyNews(10, $search);

        return view('management.company-news')
            ->with('page', $page)
            ->with('projects', $projects)
            ->with('companyNews', $companyNews);
    }
    //---------------
    public function edit(int $id)
    {
        $page = 'Company News';
        if (!$this->service->findOrFail($id)) {
            return redirect()->route('company.news')->with('error', 'Lajmi nuk u gjet');
        } else {
            $projects = $this->service->getAllProjects();
            $editCompanyNews = $this->service->getCompanyNewsById($id);

            return view('management.company-news')
                ->with('page', $page)
                ->with('projects', $projects)
                ->with('editCompanyNews', $editCompanyNews);
        }
    }
    //---------------
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|min:1|exists:company_news,id',
            'info' => 'required|string|min:3',
            'project_id' => 'nullable|integer|min:1|exists:projects,id',
        ]);

        if ($validator->fails()) {
            return back()->with('error', 'Të gjitha fushat duhet të plotësohen sipas rregullave.')->withInput();
        } else {
            $id = $request->id;
            $data = $request->only(['date', 'info', 'project_id']);
            if (empty($data['project_id'])) {
                $data['project_id'] = null;
            }

            try {
                $this->service->updateCompanyNews($id, $data);
                return back()->with('success', 'Lajmi u përditësua me sukses.');
            } catch (\Exception $e) {
                return redirect()->route('company.news')->with('error', 'Diçka shkoi gabim, ju lutemi provoni përsëri.');
            }
        }
    }
    //---------------
    public function delete(int $id)
    {
        if (!$this->service->findOrFail($id)) {
            return redirect()->route('company.news')->with('error', 'Lajmi nuk u gjet');
        } else {
            try {
                $this->service->deleteCompanyNews($id);
                return redirect()->route('company.news')->with('success', 'Lajmi u fshi me sukses');
            } catch (\Exception $e) {
                return redirect()->route('company.news')->with('error', 'Diçka shkoi gabim, ju lutemi provoni përsëri.');
            }
        }
    }
}
