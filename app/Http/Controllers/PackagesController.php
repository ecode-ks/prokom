<?php

namespace App\Http\Controllers;

use App\Services\PackagesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PackagesController extends Controller
{
    protected PackagesService $service;
    public function __construct(PackagesService $service)
    {
        $this->service = $service;
    }
    //---------------
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|min:1|max:255',
            'price' => 'required|numeric|min:0.01',
            'months' => 'required|integer|min:1',
            'description' => 'required|string|min:1'
        ]);
        if ($validator->fails()) {
            return redirect()->route('packages')->with('error',__('pages.fields_invalid'))->withInput();
        } else {
            $data = $request->only(['title', 'price', 'months', 'description']);
            try {
                $created = $this->service->createPackage($data);
                if(!$created) {
                    return redirect()->route('packages')->with('error', __('pages.try_again'))->withInput();
                }else {
                    return redirect()->route('packages')->with('success', __('pages.package_created'));
                }
            } catch (\Exception $e) {
                return redirect()->route('packages')->with('error', __('pages.try_again'))->withInput();
            }
        }
    }
    //---------------
    public function read(Request $request)
    {
        $page = 'Packages';
        $search = $request->query('search', '');
        $packages = $this->service->getPackages(10, $search);
        $packageStats = $this->service->getPackageStatistics();
        return view('management.packages')
            ->with('page', $page)
            ->with('packages', $packages)
            ->with('packageStats', $packageStats);
    }
    //---------------
    public function edit(int $id)
    {
        $page = 'Packages';
        if (!$this->service->findOrFail($id)) {
            return redirect()->route('packages')->with('error',__('pages.package_not_found'));
        } else {
            $editPackage = $this->service->getPackageById($id);
            return view('management.packages')
                ->with('page', $page)
                ->with('editPackage', $editPackage);
        }
    }
    //---------------
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric|min:1|exists:packages,id',
            'title' => 'required|string|min:1|max:255',
            'price' => 'required|numeric|min:0.01|max:99999999.99',
            'months' => 'required|integer|min:1',
            'description' => 'required|string|min:1'
        ]);

        if ($validator->fails()) {
            return back()->with('error',__('pages.fields_invalid'))->withInput();
        } else {
            $id = $request->id;
            $data = $request->only(['title', 'price', 'months', 'description']);
            try {
                $updated = $this->service->updatePackage($id, $data);
                if(!$updated) {
                    return redirect()->back()->with('error', __('pages.packages_unchanged'));
                }else {
                    return redirect()->route('packages')->with('success', __('pages.package_updated'));
                }
            } catch (\Exception $e) {
                return redirect()->route('packages')->with('error', __('pages.try_again'));
            }
        }
    }
    //---------------
    public function delete(int $id)
    {
        if (!$this->service->findOrFail($id)) {
            return redirect()->route('packages')->with('error',__('pages.package_not_found'));
        } else {
            try {
                $deleted = $this->service->deletePackage($id);
                if(!$deleted) {
                    return redirect()->route('packages')->with('error', __('pages.try_again'));
                }else {
                    return redirect()->route('packages')->with('success', __('pages.package_deleted'));
                }
            } catch (\Exception $e) {
                return redirect()->route('packages')->with('error', __('pages.try_again'));
            }
        }
    }
    //---------------
    public function change(int $id)
    {
        if (!$this->service->findOrFail($id)) {
            return redirect()->route('profile')->with('error',__('pages.package_not_found'));
        } else {
            try {
                $changed = $this->service->changePackage($id);
                if(!$changed) {
                    return redirect()->route('profile')->with('error', __('pages.try_again'));
                }else {
                    return redirect()->route('profile')->with('success', __('pages.package_changed'));
                }
            } catch (\Exception $e) {
                return redirect()->route('profile')->with('error', __('pages.try_again_reason', ['message' => $e->getMessage()]));
            }
        }
    }
}
