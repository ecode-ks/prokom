@extends('template.template')

{{-- Page Title --}}
@section('title', __('pages.offer_invoice_title'))

{{-- Page Content --}}
@section('content')
    <div class="d-print-none">
        <small class="d-block txt-lightblue fw-semibold text-uppercase"><a href="{{ route('offers') }}" class="text-decoration-none txt-lightblue fw-semibold"><i class="fa-solid fa-circle-chevron-left"></i> {{ __('app.back_full') }}</a></small>
        <h3 class="fw-semibold pt-2">{{ __('pages.offer_invoice_title') }}</h3>
        <p class="text-muted">{{ __('pages.offer_invoice_lead') }}</p>
        <hr class="mb-4">
    </div>

    {{-- Offer Details --}}
    <div class="card mb-4">
        <div class="card-header rounded-top-4 bg-white justify-content-between d-flex">
            <span><i class="fa-solid fa-list-ol txt-lightblue py-2"></i> {{ __('pages.offer_details') }}</span>
            <button class="btn btn-primary btn-sm d-print-none" onclick="window.print()"><i class="fa-solid fa-print"></i> {{ __('app.print') }}</button>
        </div>
        <div class="card-body">
            <span class="d-block mb-3 mt-4"><i class="fa-solid fa-circle-info"></i> {{ __('pages.offer_onr_label') }} : <strong>#{{ $offer->offer_number }}</strong></span>
            <span class="d-block mb-3"><i class="fa-regular fa-user"></i> {{ __('pages.offer_client') }}: <strong>{{ $offer->client }}</strong></span>
            <span class="d-block mb-3"><i class="fa-solid fa-phone"></i> {{ __('app.phone') }}: <strong>{{ $offer->phone }}</strong></span>
            <span class="d-block mb-3"><i class="fa-solid fa-location-dot"></i> {{ __('app.location') }}: <strong>{{ $offer->location }}</strong></span>
            <span class="d-block mb-3"><i class="fa-solid fa-calendar"></i> {{ __('app.date') }}: <strong>{{ \Carbon\Carbon::parse($offer->date)->format('d/m/Y') }}</strong></span>
            <div class="table-responsive mt-5">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="text-nowrap">{{ __('pages.offer_service') }}</th>
                            <th scope="col" class="text-nowrap text-end">{{ __('pages.offer_price_eur') }}</th>
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
                <span class="d-block"><i class="fa-solid fa-circle-dollar-to-slot"></i> {{ __('pages.offer_total_label') }}: <strong>€{{ number_format(collect($offerRows)->sum(function ($row) { return (float) $row->price; }), 2) }}</strong></span>
            </div>
        </div>
    </div>
@endsection
