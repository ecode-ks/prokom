@extends('template.template')

{{-- Page Title --}}
@section('title', 'Faturë Oferte')

{{-- Page Content --}}
@section('content')
    <div class="d-print-none">
        <small class="d-block txt-lightblue fw-semibold text-uppercase"><a href="{{ route('offers') }}" class="text-decoration-none txt-lightblue fw-semibold"><i class="fa-solid fa-circle-chevron-left"></i> Kthehu Pas</a></small>
        <h3 class="fw-semibold pt-2">Faturë Oferte</h3>
        <p class="text-muted">Shikoni dhe menaxhoni faturat tuaja të ofertave.</p>
        <hr class="mb-4">
    </div>

    {{-- Offer Details --}}
    <div class="card mb-4">
        <div class="card-header rounded-top-4 bg-white justify-content-between d-flex">
            <span><i class="fa-solid fa-list-ol txt-lightblue py-2"></i> Detajet e Ofertës</span>
            <button class="btn btn-primary btn-sm d-print-none" onclick="window.print()"><i class="fa-solid fa-print"></i> Print</button>
        </div>
        <div class="card-body">
            <span class="d-block mb-3 mt-4"><i class="fa-solid fa-circle-info"></i> ONR : <strong>#{{ $offer->offer_number }}</strong></span>
            <span class="d-block mb-3"><i class="fa-regular fa-user"></i> Klienti: <strong>{{ $offer->client }}</strong></span>
            <span class="d-block mb-3"><i class="fa-solid fa-phone"></i> Telefoni: <strong>{{ $offer->phone }}</strong></span>
            <span class="d-block mb-3"><i class="fa-solid fa-location-dot"></i> Lokacioni: <strong>{{ $offer->location }}</strong></span>
            <span class="d-block mb-3"><i class="fa-solid fa-calendar"></i> Data: <strong>{{ \Carbon\Carbon::parse($offer->date)->format('d/m/Y') }}</strong></span>
            <div class="table-responsive mt-5">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="text-nowrap">Shërbimi</th>
                            <th scope="col" class="text-nowrap text-end">Çmimi (€)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($offerRows as $row)
                            <tr>
                                <td style="max-width: 300px; word-wrap: break-word;">{!! nl2br(e($row->service)) !!}</td>
                                <td class="text-nowrap text-end">€{{ number_format((float) $row->price, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card card-body mt-3 text-center">
                <span class="d-block"><i class="fa-solid fa-circle-dollar-to-slot"></i> Totali i Ofertës: <strong>€{{ number_format(collect($offerRows)->sum(function ($row) { return (float) $row->price; }), 2) }}</strong></span>
            </div>
        </div>
    </div>
@endsection
