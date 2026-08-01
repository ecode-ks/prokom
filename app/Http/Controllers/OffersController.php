<?php

namespace App\Http\Controllers;

use App\Services\ClientsService;
use App\Services\OffersService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OffersController extends Controller
{
    protected OffersService $service;
    protected ClientsService $clientsService;
    public function __construct(OffersService $service, ClientsService $clientsService)
    {
        $this->service = $service;
        $this->clientsService = $clientsService;
    }
    //---------------
    public function createForm()
    {
        $page = 'Create Offer';
        $companyId = session('company_id');
        $clients = $this->clientsService->getAllClients($companyId);
        $offerClient = session('offer_client');
        $offerDetails = session('offer_details', []);

        return view('management.create-offer')
            ->with('page', $page)
            ->with('clients', $clients)
            ->with('offerClient', $offerClient)
            ->with('offerDetails', $offerDetails);
    }
    //---------------
    public function saveClientSession(Request $request)
    {
        $selectedClientId = (int) $request->input('choose_client', 0);
        $companyId = session('company_id');
        if ($selectedClientId > 0) {
            $selectedClient = $this->clientsService->getClientById($selectedClientId, $companyId);
            if (!$selectedClient) {
                return redirect()->route('create.offer')->with('error', 'Klienti i zgjedhur nuk u gjet.')->withInput();
            }

            session([
                'offer_client' => [
                    'client_id' => $selectedClient->id,
                    'client' => $selectedClient->client,
                    'phone' => $selectedClient->phone,
                    'location' => $selectedClient->location,
                ],
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'client' => 'required|string|min:1|max:255',
                'phone' => 'required|string|min:1|max:255',
                'location' => 'required|string|min:1|max:255',
            ]);

            if ($validator->fails()) {
                return redirect()->route('create.offer')->with('error', 'Zgjidhni një klient ose plotësoni të dhënat e klientit saktë.')->withInput();
            }

            session([
                'offer_client' => [
                    'client_id' => null,
                    'client' => trim((string) $request->client),
                    'phone' => trim((string) $request->phone),
                    'location' => trim((string) $request->location),
                ],
            ]);
        }
        session()->forget('offer_details');
        return redirect()->route('create.offer')->with('success', 'Të dhënat e klientit u ruajtën. Vazhdoni me detajet e ofertës.');
    }
    //---------------
    public function saveDetailsSession(Request $request)
    {
        if (!session()->has('offer_client')) {
            return redirect()->route('create.offer')->with('error', 'Ju lutemi ruani së pari informacionin e klientit.');
        }

        $validator = Validator::make($request->all(), [
            'service' => 'required|string|min:1|max:255',
            'price' => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return redirect()->route('create.offer')->with('error', 'Plotësoni detajet e ofertës saktë.')->withInput();
        }

        $offerDetails = session('offer_details', []);
        $offerDetails[] = [
            'service' => trim((string) $request->service),
            'price' => (float) $request->price,
        ];

        session(['offer_details' => $offerDetails]);

        return redirect()->route('create.offer')->with('success', 'Detajet e ofertës u ruajtën. Tani mund ta ruani ofertën e plotë.');
    }
    //---------------
    public function create()
    {
        $offerClient = session('offer_client');
        $offerDetails = session('offer_details', []);

        if (!$offerClient || count($offerDetails) === 0) {
            return redirect()->route('create.offer')->with('error', 'Ju lutemi plotësoni të dy hapat para se të ruani ofertën.');
        }

        try {
            $offerNumber = $this->generateOfferNumber();
            $date = now();

            $rows = collect($offerDetails)
                ->map(function ($row) use ($offerClient, $offerNumber, $date) {
                    return [
                        'offer_number' => $offerNumber,
                        'date' => $date,
                        'client' => $offerClient['client'],
                        'phone' => $offerClient['phone'],
                        'location' => $offerClient['location'],
                        'service' => (string) ($row['service'] ?? ''),
                        'price' => (float) ($row['price'] ?? 0),
                        'status' => 'pending',
                        'company_id' => session('company_id'),
                    ];
                })
                ->values()
                ->all();

            $created = $this->service->createOffer($rows);
            if (!$created) {
                return redirect()->route('create.offer')->with('error', 'Oferta nuk mund të ruhet, ju lutemi provoni përsëri.');
            }

            session()->forget(['offer_client', 'offer_details']);

            return redirect()->route('create.offer')->with('success', 'Oferta u krijua dhe u ruajt me sukses.');
        } catch (\Exception $e) {
            return redirect()->route('create.offer')->with('error', 'Diçka shkoi keq, ju lutemi provoni përsëri.');
        }
    }
    //---------------
    public function clearSession()
    {
        session()->forget(['offer_client', 'offer_details']);

        return redirect()->route('create.offer')->with('success', 'Sesioni i ofertës u pastrua me sukses.');
    }
    //---------------
    public function read(Request $request)
    {
        $page = 'Offers';
        $search = $request->query('search', '');
        $offers = $this->service->getOffers(10, $search);
        $offerStats = $this->service->getOfferStatistics();

        return view('management.offers')
            ->with('page', $page)
            ->with('offers', $offers)
            ->with('offerStats', $offerStats);
    }
    //---------------
    public function edit(string $offerNumber)
    {
        $page = 'Offers';
        $offers = $this->service->getOffers(10, '');
        $editOfferRows = $this->service->getOfferRowsByNumber($offerNumber);

        if ($editOfferRows->count() === 0) {
            return redirect()->route('offers')->with('error', 'Oferta nuk u gjet.');
        }

        $editOffer = $editOfferRows->first();

        return view('management.offers')
            ->with('page', $page)
            ->with('offers', $offers)
            ->with('editOffer', $editOffer)
            ->with('editOfferRows', $editOfferRows);
    }
    //---------------
    public function updateClientInfo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'offer_number' => 'required|string|min:1|max:50',
            'client' => 'required|string|min:1|max:255',
            'phone' => 'required|string|min:1|max:255',
            'location' => 'required|string|min:1|max:255',
        ]);

        if ($validator->fails()) {
            return back()->with('error', 'All fields must be filled in according to the rules.')->withInput();
        }

        $offerNumber = (string) $request->offer_number;
        if (!$this->service->offerNumberExists($offerNumber)) {
            return redirect()->route('offers')->with('error', 'Oferta nuk u gjet.');
        }

        $data = $request->only(['client', 'phone', 'location']);

        try {
            $this->service->updateOfferByNumber($offerNumber, $data);
            return redirect()->route('edit.offer', $offerNumber)->with('success', 'Informacioni i klientit u përditësua me sukses.');
        } catch (\Exception $e) {
            return back()->with('error', 'Diçka shkoi keq, ju lutemi provoni përsëri.')->withInput();
        }
    }
    //---------------
    public function addContent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'offer_number' => 'required|string|min:1|max:50',
            'service' => 'required|string|min:1|max:255',
            'price' => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return back()->with('error', 'Të gjitha fushat duhet të plotësohen sipas rregullave.')->withInput();
        }

        $offerNumber = (string) $request->offer_number;
        $rows = $this->service->getOfferRowsByNumber($offerNumber);
        if ($rows->count() === 0) {
            return redirect()->route('offers')->with('error', 'Oferta nuk u gjet.');
        }

        $header = $rows->first();
        $data = [
            'offer_number' => $offerNumber,
            'date' => $header->date,
            'client' => $header->client,
            'phone' => $header->phone,
            'location' => $header->location,
            'service' => trim((string) $request->service),
            'price' => (float) $request->price,
            'status' => $header->status,
        ];

        try {
            $this->service->createOffer($data);
            return redirect()->route('edit.offer', $offerNumber)->with('success', 'Përmbajtja e ofertës u shtua me sukses.');
        } catch (\Exception $e) {
            return back()->with('error', 'Diçka shkoi keq, ju lutemi provoni përsëri.')->withInput();
        }
    }
    //---------------
    public function deleteContent(int $id)
    {
        $row = $this->service->getOfferRowById($id);
        if (!$row) {
            return redirect()->route('offers')->with('error', 'Rreshti i ofertës nuk u gjet.');
        }

        $offerNumber = $row->offer_number;
        $rows = $this->service->getOfferRowsByNumber($offerNumber);
        if ($rows->count() <= 1) {
            return redirect()->route('edit.offer', $offerNumber)->with('error', 'Të paktën një rresht përmbajtjeje është i nevojshëm.');
        }

        try {
            $this->service->deleteOfferRowById($id);
            return redirect()->route('edit.offer', $offerNumber)->with('success', 'Përmbajtja e ofertës u fshi me sukses.');
        } catch (\Exception $e) {
            return redirect()->route('edit.offer', $offerNumber)->with('error', 'Diçka shkoi keq, ju lutemi provoni përsëri.');
        }
    }
    //---------------
    public function delete(string $offerNumber)
    {
        if (!$this->service->offerNumberExists($offerNumber)) {
            return redirect()->route('offers')->with('error', 'Oferta nuk u gjet.');
        }

        try {
            $this->service->deleteOfferByNumber($offerNumber);
            return redirect()->route('offers')->with('success', 'Oferta u fshi me sukses.');
        } catch (\Exception $e) {
            return redirect()->route('offers')->with('error', 'Diçka shkoi keq, ju lutemi provoni përsëri.');
        }
    }
    //---------------
    public function invoice(string $offerNumber)
    {
        $page = 'Offers';
        $offerRows = $this->service->getOfferRowsByNumber($offerNumber);

        if ($offerRows->count() === 0) {
            return redirect()->route('offers')->with('error', 'Oferta nuk u gjet.');
        }

        $offer = $offerRows->first();

        return view('management.offer-invoice')
            ->with('page', $page)
            ->with('offer', $offer)
            ->with('offerRows', $offerRows);
    }
    //---------------
    public function updateStatus(string $offerNumber, string $status)
    {
        if (!$this->service->offerNumberExists($offerNumber)) {
            return redirect()->route('offers')->with('error', 'Oferta nuk u gjet.');
        }

        if (!in_array($status, ['pending', 'completed'], true)) {
            return redirect()->route('offers')->with('error', 'Status i zgjedhur është i pavlefshëm.');
        }

        try {
            $this->service->updateOfferByNumber($offerNumber, ['status' => $status]);
            return redirect()->route('offers')->with('success', 'Statusi i ofertës u përditësua me sukses.');
        } catch (\Exception $e) {
            return redirect()->route('offers')->with('error', 'Diçka shkoi keq, ju lutemi provoni përsëri.');
        }
    }
    //---------------
    private function generateOfferNumber(): string
    {
        do {
            $offerNumber = 'OF-' . random_int(100000, 999999);
        } while ($this->service->offerNumberExists($offerNumber));

        return $offerNumber;
    }
}
