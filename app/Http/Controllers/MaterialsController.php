<?php

namespace App\Http\Controllers;

use App\Services\MaterialsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MaterialsController extends Controller
{
    protected MaterialsService $service;
    public function __construct(MaterialsService $service)
    {
        $this->service = $service;
    }
    //---------------
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'material' => 'required|string|min:1|max:255',
            'unit' => 'required|string|min:1|max:255',
        ]);
        if ($validator->fails()) {
            return redirect()->route('materials')->with('error','All fields must be filled in according to the rules.')->withInput();
        } else {
            $data = $request->only(['material', 'unit']);
            $data['company_id'] = session('company_id');
            try {
                $this->service->createMaterial($data);
                return redirect()->route('materials')->with('success', 'Material created successfully.');
            } catch (\Exception $e) {
                return redirect()->route('materials')->with('error', 'Something went wrong, please try again.')->withInput();
            }
        }
    }
    //---------------
    public function read(Request $request)
    {
        $page = 'Materials';
        $search = $request->query('search', '');
        $companyId = session('company_id');
        $materials = $this->service->getMaterials(10, $companyId, $search);
        return view('management.materials')
            ->with('page', $page)
            ->with('materials', $materials);
    }
    //---------------
    public function edit(int $id)
    {
        $page = 'Materials';
        $companyId = session('company_id');
        if (!$this->service->findOrFail($id, $companyId)) {
            return redirect()->route('materials')->with('error','Material not found');
        } else {
            $editMaterial = $this->service->getMaterialById($id, $companyId);
            return view('management.materials')
                ->with('page', $page)
                ->with('editMaterial', $editMaterial);
        }
    }
    //---------------
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric|min:1|exists:materials,id',
            'material' => 'required|string|min:1|max:255',
            'unit' => 'required|string|min:1|max:255',
        ]);

        if ($validator->fails()) {
            return back()->with('error','All fields must be filled in according to the rules.')->withInput();
        } else {
            $id = $request->id;
            $data = $request->only(['material', 'unit']);
            $companyId = session('company_id');
            try {
                $this->service->updateMaterial($id, $data, $companyId);
                return redirect()->route('materials')->with('success', 'Material updated successfully.');
            } catch (\Exception $e) {
                return redirect()->route('materials')->with('error', 'Something went wrong, please try again.');
            }
        }
    }
    //---------------
    public function delete(int $id)
    {
        $companyId = session('company_id');
        if (!$this->service->findOrFail($id, $companyId)) {
            return redirect()->route('materials')->with('error','Material not found');
        } else {
            try {
                $this->service->deleteMaterial($id, $companyId);
                return redirect()->route('materials')->with('success','Material deleted successfully');
            } catch (\Exception $e) {
                return redirect()->route('materials')->with('error', 'Something went wrong, please try again.');
            }
        }
    }
}

