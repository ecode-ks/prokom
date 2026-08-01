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
            return redirect()->route('materials.stock')->with('error', 'Të gjitha fushat duhet të plotësohen sipas rregullave.')->withInput();
        }

        $data = $request->only(['material_id', 'qty', 'type']);
        $data['company_id'] = session('company_id');
        try {
            $this->service->createMaterialStock($data);
            return redirect()->route('materials.stock')->with('success', 'Transaksioni u krijua me sukses.');
        } catch (\Exception $e) {
            return redirect()->route('materials.stock')->with('error', $e->getMessage())->withInput();
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
            return redirect()->route('materials')->with('error', 'Duhet të regjistroni materialet së pari.');
        }
        $materialsStock = $this->service->getMaterialsStock(10, $companyId, $search);
        $stockStats = $this->service->getStockStatistics($companyId);
        return view('management.materials-stock')
            ->with('page', $page)
            ->with('materials', $materials)
            ->with('materialsStock', $materialsStock)
            ->with('stockStats', $stockStats);
    }
    //---------------
    public function report()
    {
        $page = 'Stock Report';
        $companyId = (int) session('company_id');
        $stockReport = $this->service->getStockReport(10, $companyId);
        $stockStats = $this->service->getStockReportSummary($companyId);

        return view('management.materials-stock-report')
            ->with('page', $page)
            ->with('stockReport', $stockReport)
            ->with('stockStats', $stockStats);
    }
    //---------------
    public function edit(int $id)
    {
        $page = 'Materials Stock';
        $companyId = (int) session('company_id');
        if (!$this->service->findOrFail($id, $companyId)) {
            return redirect()->route('materials.stock')->with('error', 'Transaksioni nuk u gjet');
        }

        $materials = $this->service->getMaterialsFromStockReport($companyId);
        if (count($materials) === 0) {
            return redirect()->route('materials')->with('error', 'Duhet të regjistroni materialet së pari.');
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
            return back()->with('error', 'Të gjitha fushat duhet të plotësohen sipas rregullave.')->withInput();
        }

        $id = (int) $request->id;
        $data = $request->only(['material_id', 'qty', 'type']);
        $companyId = (int) session('company_id');
        try {
            $this->service->updateMaterialStock($id, $data, $companyId);
            return redirect()->route('materials.stock')->with('success', 'Transaksioni u përditësua me sukses.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }
    //---------------
    public function delete(int $id)
    {
        $companyId = (int) session('company_id');
        if (!$this->service->findOrFail($id, $companyId)) {
            return redirect()->route('materials.stock')->with('error', 'Transaksioni nuk u gjet');
        }

        try {
            $this->service->deleteMaterialStock($id, $companyId);
            return redirect()->route('materials.stock')->with('success', 'Transaksioni u fshi me sukses');
        } catch (\Exception $e) {
            return redirect()->route('materials.stock')->with('error', $e->getMessage());
        }
    }
}
