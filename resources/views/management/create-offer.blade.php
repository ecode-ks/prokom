@extends('template.template')

{{-- Page Title --}}
@section('title', 'Krijo Ofertë')

{{-- Page Content --}}
@section('content')
    <small class="d-block txt-lightblue fw-semibold text-uppercase">Ofertat</small>
    <h3 class="fw-semibold pt-2">Krijo Ofertë</h3>
    <p class="text-muted">Krijo një ofertë të re për klientët tuaj.</p>
    <hr class="mb-4">

    {{-- Start Creating Offer --}}
    @if(!session()->has('offer_client'))
        <div class="card mb-4">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-file-invoice-dollar txt-lightblue py-2"></i> Krijo Ofertë të Re</span>
            </div>
            <div class="card-body">
                <form action="{{ route('save.offer.client.session') }}" method="post">
                    @csrf
                    <div class="row g-3">
                        @if(!session()->has('offer_client') || (($offerClient['client_id'] ?? null) !== null))
                            <div class="col-12 col-md-4 col-lg-4">
                                <label for="choose_client" class="form-label">Zgjidh Klientin</label>
                                <select name="choose_client" id="choose_client" class="form-control shadow-sm rounded-3">
                                    <option value="" disabled {{ old('choose_client') ? '' : 'selected' }}>-- Zgjidh Klientin --</option>
                                    @foreach ($clients as $client)
                                        <option value="{{ $client->id }}" {{ (string) old('choose_client') === (string) $client->id ? 'selected' : '' }}>{{ $client->client }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <span>--- OSE Plotëso Këto Fusha ---</span>
                        @endif
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="client" class="form-label">Klienti</label>
                            <input type="text" name="client" id="client" value="{{ old('client', $offerClient['client'] ?? '') }}" class="form-control shadow-sm rounded-3" placeholder="Shkruaj Klientin këtu...">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="phone" class="form-label">Telefoni</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone', $offerClient['phone'] ?? '') }}" class="form-control shadow-sm rounded-3" placeholder="Shkruaj Telefonin këtu...">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="location" class="form-label">Adresa</label>
                            <input type="text" name="location" id="location" value="{{ old('location', $offerClient['location'] ?? '') }}" class="form-control shadow-sm rounded-3" placeholder="Shkruaj Adresën këtu...">
                        </div>
                        <div class="col-12">
                            @if(session()->has('offer_client') && (($offerClient['client_id'] ?? null) !== null))
                                <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-check"></i> Përditëso Klientin</button>
                            @else
                                <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-plus"></i> Fillo Krijimin e Ofertës</button>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if(session()->has('offer_client'))
        {{-- Fill Offer Details --}}
        <div class="card mb-4">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-list-check txt-lightblue py-2"></i> Plotëso Detajet e Ofertës</span>
            </div>
            <div class="card-body">
                <form action="{{ route('save.offer.details.session') }}" method="post">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="service" class="form-label">Shërbimi</label>
                            <input type="text" name="service" id="service" value="{{ old('service') }}" class="form-control shadow-sm rounded-3" placeholder="Shkruaj Shërbimin këtu..." autofocus>
                        </div>
                        <div class="col-12 col-md-8 col-lg-8">
                            <label for="price" class="form-label">Çmimi</label>
                            <input type="text" name="price" id="price" value="{{ old('price') }}" class="form-control shadow-sm rounded-3" placeholder="Shkruaj Çmimin këtu...">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-plus"></i> Ruaj Detajet e Ofertës</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if(session()->has('offer_client'))

        {{-- Offer Details --}}
        <div class="card mb-4">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-list-ol txt-lightblue py-2"></i> Detajet e Ofertës</span>
            </div>
            <div class="card-body">
                <span class="d-block mb-3"><i class="fa-regular fa-user"></i> Klienti: <strong>{{ $offerClient['client'] }}</strong></span>
                <span class="d-block mb-3"><i class="fa-solid fa-phone"></i> Telefoni: <strong>{{ $offerClient['phone'] }}</strong></span>
                <span class="d-block mb-3"><i class="fa-solid fa-location-dot"></i> Adresa: <strong>{{ $offerClient['location'] }}</strong></span>

                <div class="table-responsive mt-5">
                    <table class="table table-bordered table-hover align-middle">
                        <thead>
                            <tr>
                                <th scope="col" class="text-nowrap">Shërbimi</th>
                                <th scope="col" class="text-nowrap">Çmimi (€)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($offerDetails) == 0)
                                <tr>
                                    <td colspan="2" class="text-center"><i class="fa-regular fa-face-frown"></i> Nuk ka detaje të ofertës ende...</td>
                                </tr>
                            @else
                                @foreach($offerDetails as $detail)
                                    <tr>
                                        <td class="text-nowrap">{{ $detail['service'] }}</td>
                                        <td class="text-nowrap">€{{ number_format((float) $detail['price'], 2) }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                        @if(!count($offerDetails) == 0)
                            <tfoot>
                                <tr>
                                    <td class="text-nowrap"><strong>Total</strong></td>
                                    <td class="text-nowrap"><strong>€{{ number_format(collect($offerDetails)->sum(function ($detail) { return (float) ($detail['price'] ?? 0); }), 2) }}</strong></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>

                <div class="d-flex gap-2 mt-3">
                    @if(count($offerDetails) > 0)
                        <form action="{{ route('create.offer') }}" method="post">
                            @csrf
                            <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-check"></i> Ruaj Ofertën</button>
                        </form>
                    @endif
                    <form action="{{ route('clear.offer.session') }}" method="post">
                        @csrf
                        <button type="submit" class="btn btn-danger rounded-3 px-4 shadow-sm"><i class="fa-solid fa-trash"></i> Pastro Ofertën</button>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection
