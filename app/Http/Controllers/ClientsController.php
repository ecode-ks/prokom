<?php

namespace App\Http\Controllers;

use App\Services\ClientsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClientsController extends Controller
{
    protected ClientsService $service;
    public function __construct(ClientsService $service)
    {
        $this->service = $service;
    }
    //---------------
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client' => 'required|string|min:1|max:255',
            'phone' => 'required|string|min:1|max:255',
            'location' => 'required|string|min:1|max:255',
        ]);
        if ($validator->fails()) {
            return redirect()->route('clients')->with('error',__('pages.fields_invalid'))->withInput();
        } else {
            $data = $request->only(['client', 'phone', 'location']);
            $data['company_id'] = session('company_id');
            try {
                $this->service->createClient($data);
                return redirect()->route('clients')->with('success', __('pages.client_created'));
            } catch (\Exception $e) {
                return redirect()->route('clients')->with('error', __('pages.try_again'))->withInput();
            }
        }
    }
    //---------------
    public function read(Request $request)
    {
        $page = 'Clients';
        $search = $request->query('search', '');
        $companyId = session('company_id');
        $clients = $this->service->getClients(10, $companyId, $search);
        return view('management.clients')
            ->with('page', $page)
            ->with('clients', $clients);
    }
    //---------------
    public function edit(int $id)
    {
        $page = 'Clients';
        $companyId = session('company_id');
        if (!$this->service->findOrFail($id, $companyId)) {
            return redirect()->route('clients')->with('error', __('pages.client_not_found'));
        } else {
            $editClient = $this->service->getClientById($id, $companyId);
            return view('management.clients')
                ->with('page', $page)
                ->with('editClient', $editClient);
        }
    }
    //---------------
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric|min:1|exists:clients,id',
            'client' => 'required|string|min:1|max:255',
            'phone' => 'required|string|min:1|max:255',
            'location' => 'required|string|min:1|max:255',
        ]);

        if ($validator->fails()) {
            return back()->with('error',__('pages.fields_invalid'))->withInput();
        } else {
            $id = $request->id;
            $data = $request->only(['client', 'phone', 'location']);
            $companyId = session('company_id');
            try {
                $this->service->updateClient($id, $data, $companyId);
                return redirect()->route('clients')->with('success', __('pages.client_updated'));
            } catch (\Exception $e) {
                return redirect()->route('clients')->with('error', __('pages.try_again'));
            }
        }
    }
    //---------------
    public function delete(int $id)
    {
        $companyId = session('company_id');
        if (!$this->service->findOrFail($id, $companyId)) {
            return redirect()->route('clients')->with('error', __('pages.client_not_found'));
        } else {
            try {
                $this->service->deleteClient($id, $companyId);
                return redirect()->route('clients')->with('success', __('pages.client_deleted'));
            } catch (\Exception $e) {
                return redirect()->route('clients')->with('error', __('pages.try_again'));
            }
        }
    }
}
