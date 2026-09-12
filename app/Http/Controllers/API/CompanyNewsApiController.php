<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\CompanyNewsService;
use Illuminate\Http\Request;

class CompanyNewsApiController extends Controller
{
    protected CompanyNewsService $service;

    public function __construct(CompanyNewsService $service)
    {
        $this->service = $service;
    }

    public function read(Request $request, int $companyId)
    {
        $search = trim((string) $request->query('search', ''));
        $companyNews = $this->service->getCompanyNews(10, $companyId, $search);

        return response()->json(['companyNews' => $companyNews]);
    }
}
