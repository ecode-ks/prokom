@extends('template.template')

{{-- Page Title --}}
@section('title', 'Ofertat')

{{-- Page Content --}}
@section('content')
    @if(isset($editOffer) && isset($editOfferRows))
        <small class="d-block txt-lightblue fw-semibold text-uppercase"><a href="{{ route('offers') }}" class="text-decoration-none txt-lightblue fw-semibold"><i class="fa-solid fa-circle-chevron-left"></i> Kthehu Pas</a></small>
        <h3 class="fw-semibold pt-2">Përditëso Ofertën</h3>
        <p class="text-muted">Përditëso informacionin e klientit dhe përmbajtjen e ofertës.</p>
        <hr class="mb-4">

        {{-- Edit Offer --}}
        <div class="card mb-4">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-file-invoice-dollar txt-lightblue py-2"></i> Përditëso Informacionin e Klientit</span>
            </div>
            <div class="card-body">
                <form action="{{ route('update.offer.client.info') }}" method="post">
                    @csrf
                    <input type="hidden" name="offer_number" value="{{ $editOffer->offer_number }}">
                    <div class="row g-3">
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="client" class="form-label">Klienti</label>
                            <input type="text" name="client" id="client" value="{{ old('client', $editOffer->client) }}" class="form-control shadow-sm rounded-3" placeholder="Shkruani emrin e klientit...">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="phone" class="form-label">Telefoni</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone', $editOffer->phone) }}" class="form-control shadow-sm rounded-3" placeholder="Shkruani numrin e telefonit...">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="location" class="form-label">Lokacioni</label>
                            <input type="text" name="location" id="location" value="{{ old('location', $editOffer->location) }}" class="form-control shadow-sm rounded-3" placeholder="Shkruani lokacionin...">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-check"></i> Ruaj Ndryshimet</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Edit Content --}}
        <div class="card mb-4">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-list-check txt-lightblue py-2"></i> Përditëso Përmbajtjen</span>
            </div>
            <div class="card-body">
                <form action="{{ route('add.offer.content') }}" method="post">
                    @csrf
                    <input type="hidden" name="offer_number" value="{{ $editOffer->offer_number }}">
                    <div class="row g-3">
                        <div class="col-12 col-md-6 col-lg-6">
                            <label for="service" class="form-label">Shërbimi</label>
                            <input type="text" name="service" id="service" value="{{ old('service') }}" class="form-control shadow-sm rounded-3" placeholder="Shkruani shërbimin këtu..." autofocus>
                        </div>
                        <div class="col-12 col-md-6 col-lg-6">
                            <label for="price" class="form-label">Çmimi</label>
                            <input type="text" name="price" id="price" value="{{ old('price') }}" class="form-control shadow-sm rounded-3" placeholder="Shkruani çmimin këtu...">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-plus"></i> Shto në Ofertë</button>
                        </div>
                    </div>
                </form>
                <div class="table-responsive mt-3">
                    <table class="table table-bordered table-hover align-middle">
                        <thead>
                            <tr>
                                <th scope="col" class="text-nowrap">Shërbimi</th>
                                <th scope="col" class="text-nowrap">Çmimi (€)</th>
                                <th scope="col" class="text-nowrap text-end">Veprimet</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($editOfferRows) === 0)
                                <tr>
                                    <td colspan="3" class="text-center"><i class="fa-regular fa-face-frown"></i> Nuk u gjetën të dhëna...</td>
                                </tr>
                            @else
                                @foreach($editOfferRows as $row)
                                    <tr>
                                        <td class="text-nowrap">{{ $row->service }}</td>
                                        <td class="text-nowrap">€{{ number_format((float) $row->price, 2) }}</td>
                                        <td class="text-nowrap text-end">
                                            <a href="{{ route('delete.offer.content', $row->id) }}" class="btn btn-outline-danger btn-sm rounded-3 shadow-sm"><i class="fa-solid fa-trash"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                        <tfoot>
                            <tr>
                                <td class="text-nowrap"><strong>Total</strong></td>
                                <td class="text-nowrap"><strong>€{{ number_format(collect($editOfferRows)->sum(function ($row) { return (float) $row->price; }), 2) }}</strong></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    @else
        <small class="d-block txt-lightblue fw-semibold text-uppercase">Ofertat</small>
        <h3 class="fw-semibold pt-2">Ofertat</h3>
        <p class="text-muted">Menaxhoni ofertat për klientët në mënyrë të shpejtë dhe efikase.</p>
        <hr class="mb-4">

        {{-- Offers List --}}
        <div class="card">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-list-ol txt-lightblue py-2"></i> Lista e Ofertave</span>
            </div>
            <div class="card-body">
                <form action="{{ route('offers') }}" method="get" class="input-group mb-3">
                    <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-control shadow-sm rounded-start-3" placeholder="Kërko..." aria-label="Kërko..." aria-describedby="basic-addon2">
                    <button type="submit" class="btn btn-darkblue shadow-sm rounded-end-3" id="basic-addon2"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th scope="col" class="text-nowrap">#ONR</th>
                                <th scope="col" class="text-nowrap">Data</th>
                                <th scope="col" class="text-nowrap">Klienti</th>
                                <th scope="col" class="text-nowrap">Statusi</th>
                                <th scope="col" class="text-nowrap text-end">Veprimet</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($offers) === 0)
                                <tr>
                                    <td colspan="5" class="text-center"><i class="fa-regular fa-face-frown"></i> Nuk u gjetën të dhëna...</td>
                                </tr>
                            @else
                                @foreach($offers as $offer)
                                    <tr>
                                        <th scope="row" class="text-nowrap">{{ $offer->offer_number }}</th>
                                        <td class="text-nowrap">{{ \Carbon\Carbon::parse($offer->date)->format('d/m/Y') }}</td>
                                        <td class="text-nowrap">{{ $offer->client }}</td>
                                        <td class="text-nowrap">
                                            @if($offer->status === 'completed')
                                                <span class="badge rounded-pill text-dark text-bg-success bg-opacity-25 border border-success">Përfunduar</span>
                                            @else
                                                <span class="badge rounded-pill text-dark text-bg-warning bg-opacity-25 border border-warning">Në pritje</span>
                                            @endif
                                        </td>
                                        <td class="text-nowrap text-end">
                                            @if($offer->status === 'completed')
                                                <a href="{{ route('update.offer.status', ['offerNumber' => $offer->offer_number, 'status' => 'pending']) }}" class="btn btn-outline-warning btn-sm rounded-3 shadow-sm"><i class="fa-solid fa-clock"></i></a>
                                            @else
                                                <a href="{{ route('update.offer.status', ['offerNumber' => $offer->offer_number, 'status' => 'completed']) }}" class="btn btn-outline-primary btn-sm rounded-3 shadow-sm"><i class="fa-solid fa-check"></i></a>
                                            @endif
                                            <a href="{{ route('edit.offer', $offer->offer_number) }}" class="btn btn-outline-success btn-sm rounded-3 shadow-sm"><i class="fa-solid fa-pen"></i></a>
                                            <button class="btn btn-outline-danger btn-sm rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#deleteOfferModal{{ $offer->id }}"><i class="fa-solid fa-trash"></i></button>
                                            <a href="{{ route('offer.invoice', $offer->offer_number) }}" class="btn btn-outline-dark btn-sm rounded-3 shadow-sm"><i class="fa-solid fa-receipt"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                @if ($offers->hasPages())
                    <div class="mt-3">
                        {{ $offers->links('vendor.pagination.custom') }}
                    </div>
                @endif

                @if(count($offers) !== 0)
                    @foreach($offers as $offer)
                        <div class="modal fade" id="deleteOfferModal{{ $offer->id }}" tabindex="-1" aria-labelledby="deleteOfferModalLabel{{ $offer->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-body text-center">
                                        <p>Konfirmoni nëse dëshironi vërtet të fshini këtë ofertë?</p>
                                        <div class="mt-3">
                                            <a href="{{ route('delete.offer', $offer->offer_number) }}" class="btn btn-danger btn-sm fw-semibold"><i class="fa-solid fa-check"></i> Konfirmo</a>
                                            <button class="btn btn-success btn-sm fw-semibold" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> Anulo</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    @endif
@endsection
