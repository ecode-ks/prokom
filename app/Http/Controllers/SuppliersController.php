<?php

namespace App\Http\Controllers;

use App\Services\SuppliersService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SuppliersController extends Controller
{
    protected SuppliersService $service;
    public function __construct(SuppliersService $service)
    {
        $this->service = $service;
    }
    //---------------
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'supplier' => 'required|string|min:1|max:255',
            'phone' => 'required|string|min:1|max:255',
            'location' => 'required|string|min:1|max:255',
        ]);
        if ($validator->fails()) {
            return redirect()->route('suppliers')->with('error','Të gjitha fushat duhet të plotësohen sipas rregullave.')->withInput();
        } else {
            $data = $request->only(['supplier', 'phone', 'location']);
            $data['company_id'] = session('company_id');
            try {
                $this->service->createSupplier($data);
                return redirect()->route('suppliers')->with('success', 'Furnizuesi u krijua me sukses.');
            } catch (\Exception $e) {
                return redirect()->route('suppliers')->with('error', 'Ndodhi një problem, ju lutem provoni përsëri.')->withInput();
            }
        }
    }
    //---------------
    public function read(Request $request)
    {
        $page = 'Suppliers';
        $search = $request->query('search', '');
        $companyId = session('company_id');
        $suppliers = $this->service->getSuppliers(10, $search, $companyId);
        $supplierStats = $this->service->getSupplierStatistics((int) $companyId);
        return view('management.suppliers')
            ->with('page', $page)
            ->with('suppliers', $suppliers)
            ->with('supplierStats', $supplierStats);
    }
    //---------------
    public function edit(int $id)
    {
        $page = 'Suppliers';
        $companyId = session('company_id');
        if (!$this->service->findOrFail($id, $companyId)) {
            return redirect()->route('suppliers')->with('error','Furnizuesi nuk u gjet');
        } else {
            $editSupplier = $this->service->getSupplierById($id, $companyId);
            return view('management.suppliers')
                ->with('page', $page)
                ->with('editSupplier', $editSupplier);
        }
    }
    //---------------
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric|min:1|exists:suppliers,id',
            'supplier' => 'required|string|min:1|max:255',
            'phone' => 'required|string|min:1|max:255',
            'location' => 'required|string|min:1|max:255',
        ]);

        if ($validator->fails()) {
            return back()->with('error','Të gjitha fushat duhet të plotësohen sipas rregullave.')->withInput();
        } else {
            $id = $request->id;
            $data = $request->only(['supplier', 'phone', 'location']);
            $companyId = session('company_id');
            try {
                $this->service->updateSupplier($id, $data, $companyId);
                return redirect()->route('suppliers')->with('success', 'Furnizuesi u përditësua me sukses.');
            } catch (\Exception $e) {
                return redirect()->route('suppliers')->with('error', 'Ndodhi një problem, ju lutem provoni përsëri.');
            }
        }
    }
    //---------------
    public function delete(int $id)
    {
        $companyId = session('company_id');
        if (!$this->service->findOrFail($id, $companyId)) {
            return redirect()->route('suppliers')->with('error','Furnizuesi nuk u gjet');
        } else {
            try {
                $this->service->deleteSupplier($id, $companyId);
                return redirect()->route('suppliers')->with('success','Furnizuesi u fshi me sukses');
            } catch (\Exception $e) {
                return redirect()->route('suppliers')->with('error', 'Ndodhi një problem, ju lutem provoni përsëri.');
            }
        }
    }
}
