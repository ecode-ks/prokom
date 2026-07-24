<?php

namespace App\Http\Controllers;

use App\Services\MaterialsService;
use App\Services\MaterialsStockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MaterialsStockController extends Controller
{
    protected MaterialsStockService $service;
    protected MaterialsService $materials;
    public function __construct(MaterialsStockService $service, MaterialsService $materials)
    {
        $this->service = $service;
        $this->materials = $materials;
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
            return redirect()->route('materials.stock')->with('error','All fields must be filled in according to the rules.')->withInput();
        } else {
            $data = $request->only(['material_id', 'qty', 'type']);
            $data['company_id'] = session('company_id');
            try {
                $this->service->createMaterialStock($data);
                return redirect()->route('materials.stock')->with('success', 'Transaction created successfully.');
            } catch (\Exception $e) {
                return redirect()->route('materials.stock')->with('error', 'Something went wrong, please try again.')->withInput();
            }
        }
    }
    //---------------
    public function read(Request $request)
    {
        $page = 'Materials Stock';
        $search = $request->query('search', '');
        $companyId = session('company_id');
        $materials = $this->materials->getAllMaterials($companyId);
        if(count($materials) === 0){
            return redirect()->route('materials')->with('error','You need to register materials first.');
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
        $companyId = session('company_id');
        $stockReport = $this->service->getStockReport(10, $companyId);

        return view('management.materials-stock-report')
            ->with('page', $page)
            ->with('stockReport', $stockReport);
    }
    //---------------
    public function edit(int $id)
    {
        $page = 'Materials Stock';
        $companyId = session('company_id');
        if (!$this->service->findOrFail($id, $companyId)) {
            return redirect()->route('materials.stock')->with('error','Transaction not found');
        } else {
            $materials = $this->materials->getAllMaterials($companyId);
            if(count($materials) === 0){
                return redirect()->route('materials')->with('error','You need to register materials first.');
            }
            $editMaterialStock = $this->service->getMaterialStockById($id, $companyId);
            return view('management.materials-stock')
                ->with('page', $page)
                ->with('materials', $materials)
                ->with('editMaterialStock', $editMaterialStock);
        }
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
            return back()->with('error','All fields must be filled in according to the rules.')->withInput();
        } else {
            $id = $request->id;
            $data = $request->only(['material_id', 'qty', 'type']);
            $companyId = session('company_id');
            try {
                $this->service->updateMaterialStock($id, $data, $companyId);
                return back()->with('success', 'Transaction updated successfully.');
            } catch (\Exception $e) {
                return redirect()->route('materials.stock')->with('error', 'Something went wrong, please try again.');
            }
        }
    }
    //---------------
    public function delete(int $id)
    {
        $companyId = session('company_id');
        if (!$this->service->findOrFail($id, $companyId)) {
            return redirect()->route('materials.stock')->with('error','Transaction not found');
        } else {
            try {
                $this->service->deleteMaterialStock($id, $companyId);
                return redirect()->route('materials.stock')->with('success','Transaction deleted successfully');
            } catch (\Exception $e) {
                return redirect()->route('materials.stock')->with('error', 'Something went wrong, please try again.');
            }
        }
    }
}

