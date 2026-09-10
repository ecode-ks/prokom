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
        $companyId = (int) session('company_id');

        $validator = Validator::make($request->all(), [
            'info' => 'required|string|min:3',
            'project_id' => 'nullable|integer|min:1|exists:projects,id',
        ]);

        if ($validator->fails()) {
            return redirect()->route('company.news')->with('error', __('app.fields_invalid'))->withInput();
        } else {
            $data = $request->only(['date', 'info', 'project_id']);
            if (empty($data['project_id'])) {
                $data['project_id'] = null;
            }
            $data['company_id'] = $companyId;

            try {
                $this->service->createCompanyNews($data);
                return redirect()->route('company.news')->with('success', __('pages.company_news_created'));
            } catch (\Exception $e) {
                return redirect()->route('company.news')->with('error', __('app.try_again'))->withInput();
            }
        }
    }
    //---------------
    public function read(Request $request)
    {
        $page = 'Company News';
        $search = $request->query('search', '');
        $companyId = (int) session('company_id');
        $projects = $this->service->getAllProjects($companyId);
        $companyNews = $this->service->getCompanyNews(10, $companyId, $search);

        return view('management.company-news')
            ->with('page', $page)
            ->with('projects', $projects)
            ->with('companyNews', $companyNews);
    }
    //---------------
    public function edit(int $id)
    {
        $page = 'Company News';
        $companyId = (int) session('company_id');

        if (!$this->service->findOrFail($id, $companyId)) {
            return redirect()->route('company.news')->with('error', __('pages.company_news_not_found'));
        } else {
            $projects = $this->service->getAllProjects($companyId);
            $editCompanyNews = $this->service->getCompanyNewsById($id, $companyId);

            return view('management.company-news')
                ->with('page', $page)
                ->with('projects', $projects)
                ->with('editCompanyNews', $editCompanyNews);
        }
    }
    //---------------
    public function update(Request $request)
    {
        $companyId = (int) session('company_id');

        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|min:1|exists:company_news,id',
            'info' => 'required|string|min:3',
            'project_id' => 'nullable|integer|min:1|exists:projects,id',
        ]);

        if ($validator->fails()) {
            return back()->with('error', __('app.fields_invalid'))->withInput();
        } else {
            $id = $request->id;
            $data = $request->only(['date', 'info', 'project_id']);
            if (empty($data['project_id'])) {
                $data['project_id'] = null;
            }

            try {
                if (!$this->service->updateCompanyNews($id, $companyId, $data)) {
                    return back()->with('error', __('pages.company_news_not_found'));
                }
                return back()->with('success', __('pages.company_news_updated'));
            } catch (\Exception $e) {
                return redirect()->route('company.news')->with('error', __('app.try_again'));
            }
        }
    }
    //---------------
    public function delete(int $id)
    {
        $companyId = (int) session('company_id');

        if (!$this->service->findOrFail($id, $companyId)) {
            return redirect()->route('company.news')->with('error', __('pages.company_news_not_found'));
        } else {
            try {
                $this->service->deleteCompanyNews($id, $companyId);
                return redirect()->route('company.news')->with('success', __('pages.company_news_deleted'));
            } catch (\Exception $e) {
                return redirect()->route('company.news')->with('error', __('app.try_again'));
            }
        }
    }
}
