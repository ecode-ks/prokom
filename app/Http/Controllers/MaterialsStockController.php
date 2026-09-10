<?php

namespace App\Http\Controllers;

use App\Services\MaterialsStockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MaterialsStockController extends Controller
{
    protected MaterialsStockService $service;

    public function __construct(MaterialsStockService $service)
    {
        $this->service = $service;
    }
    //---------------
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'material_id' => 'required|integer|min:1|exists:materials,id',
            'qty' => 'required|numeric|min:0.01|max:99999999999.99',
            'type' => 'required|string|in:in,out'
        ]);
        if ($validator->fails()) {
            return redirect()->route('materials.stock')->with('error', __('app.fields_invalid'))->withInput();
        }

        $data = $request->only(['material_id', 'qty', 'type']);
        $data['company_id'] = session('company_id');
        try {
            $this->service->createMaterialStock($data);
            return redirect()->route('materials.stock')->with('success', __('pages.stock_created'));
        } catch (\Exception $e) {
            return redirect()->route('materials.stock')->with('error', __('app.try_again'))->withInput();
        }
    }
    //---------------
    public function read(Request $request)
    {
        $page = 'Materials Stock';
        $search = $request->query('search', '');
        $companyId = (int) session('company_id');
        $materials = $this->service->getMaterialsFromStockReport($companyId);
        if (count($materials) === 0) {
            return redirect()->route('materials')->with('error', __('pages.materials_register_first'));
        }
        $materialsStock = $this->service->getMaterialsStock(10, $companyId, $search);
        return view('management.materials-stock')
            ->with('page', $page)
            ->with('materials', $materials)
            ->with('materialsStock', $materialsStock);
    }
    //---------------
    public function report()
    {
        $page = 'Stock Report';
        $companyId = (int) session('company_id');
        $stockReport = $this->service->getStockReport(10, $companyId);

        return view('management.materials-stock-report')
            ->with('page', $page)
            ->with('stockReport', $stockReport);
    }
    //---------------
    public function edit(int $id)
    {
        $page = 'Materials Stock';
        $companyId = (int) session('company_id');
        if (!$this->service->findOrFail($id, $companyId)) {
            return redirect()->route('materials.stock')->with('error', __('pages.stock_not_found'));
        }

        $materials = $this->service->getMaterialsFromStockReport($companyId);
        if (count($materials) === 0) {
            return redirect()->route('materials')->with('error', __('pages.materials_register_first'));
        }
        $editMaterialStock = $this->service->getMaterialStockById($id, $companyId);
        return view('management.materials-stock')
            ->with('page', $page)
            ->with('materials', $materials)
            ->with('editMaterialStock', $editMaterialStock);
    }
    //---------------
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|min:1|exists:materials_stock,id',
            'material_id' => 'required|integer|min:1|exists:materials,id',
            'qty' => 'required|numeric|min:0.01|max:99999999999.99',
            'type' => 'required|string|in:in,out'
        ]);

        if ($validator->fails()) {
            return back()->with('error', __('app.fields_invalid'))->withInput();
        }

        $id = (int) $request->id;
        $data = $request->only(['material_id', 'qty', 'type']);
        $companyId = (int) session('company_id');
        try {
            $this->service->updateMaterialStock($id, $data, $companyId);
            return redirect()->route('materials.stock')->with('success', __('pages.stock_updated'));
        } catch (\Exception $e) {
            return back()->with('error', __('app.try_again'))->withInput();
        }
    }
    //---------------
    public function delete(int $id)
    {
        $companyId = (int) session('company_id');
        if (!$this->service->findOrFail($id, $companyId)) {
            return redirect()->route('materials.stock')->with('error', __('pages.stock_not_found'));
        }

        try {
            $this->service->deleteMaterialStock($id, $companyId);
            return redirect()->route('materials.stock')->with('success', __('pages.stock_deleted'));
        } catch (\Exception $e) {
            return redirect()->route('materials.stock')->with('error', __('app.try_again'));
        }
    }
}
