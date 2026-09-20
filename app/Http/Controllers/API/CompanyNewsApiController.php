<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\Api\CompanyNewsApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompanyNewsApiController extends Controller
{
    protected CompanyNewsApiService $service;

    public function __construct(CompanyNewsApiService $service)
    {
        $this->service = $service;
    }

    public function create(Request $request, int $companyId, int $employeeId, string $role)
    {
        if (!$this->canManage($role)) {
            return json_encode(['error' => __('pages.no_access')]);
        }

        $validator = Validator::make($request->all(), [
            'info' => 'required|string',
            'project_id' => 'nullable|integer',
            'date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return json_encode(['error' => __('pages.fields_invalid')]);
        }

        $data = $request->only(['date', 'info', 'project_id']);

        try {
            $this->service->createCompanyNews($data, $companyId);
            return json_encode(['success' => __('pages.company_news_created')]);
        } catch (\Exception $e) {
            return json_encode(['error' => __('pages.try_again')]);
        }
    }

    public function read(Request $request, int $companyId, int $employeeId, string $role)
    {
        $search = trim((string) $request->query('search', ''));

        return json_encode([
            'projects' => $this->service->getAllProjects($companyId),
            'companyNews' => $this->service->getCompanyNews(10, $companyId, $search),
            'role' => $role,
        ]);
    }

    public function edit(int $id, int $companyId, int $employeeId, string $role)
    {
        if (!$this->canManage($role)) {
            return json_encode(['error' => __('pages.no_access')]);
        }

        if (!$this->service->findOrFail($id, $companyId)) {
            return json_encode(['error' => __('pages.company_news_not_found')]);
        }

        return json_encode([
            'projects' => $this->service->getAllProjects($companyId),
            'editCompanyNews' => $this->service->getCompanyNewsById($id, $companyId),
            'role' => $role,
        ]);
    }

    public function update(Request $request, int $companyId, int $employeeId, string $role)
    {
        if (!$this->canManage($role)) {
            return json_encode(['error' => __('pages.no_access')]);
        }

        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:company_news,id',
            'info' => 'required|string',
            'project_id' => 'nullable|integer',
            'date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return json_encode(['error' => __('pages.fields_invalid')]);
        }

        $id = (int) $request->id;
        $data = $request->only(['date', 'info', 'project_id']);

        try {
            if (!$this->service->updateCompanyNews($id, $companyId, $data)) {
                return json_encode(['error' => __('pages.company_news_not_found')]);
            }

            return json_encode(['success' => __('pages.company_news_updated')]);
        } catch (\Exception $e) {
            return json_encode(['error' => __('pages.try_again')]);
        }
    }

    public function delete(int $id, int $companyId, int $employeeId, string $role)
    {
        if (!$this->canManage($role)) {
            return json_encode(['error' => __('pages.no_access')]);
        }

        if (!$this->service->findOrFail($id, $companyId)) {
            return json_encode(['error' => __('pages.company_news_not_found')]);
        }

        try {
            $this->service->deleteCompanyNews($id, $companyId);
            return json_encode(['success' => __('pages.company_news_deleted')]);
        } catch (\Exception $e) {
            return json_encode(['error' => __('pages.try_again')]);
        }
    }

    private function canManage(string $role): bool
    {
        return in_array($role, ['company', 'manager'], true);
    }
}
