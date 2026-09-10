@extends('template.template')

{{-- Page Title --}}
@section('title', __('app.nav_create_offer'))

{{-- Page Content --}}
@section('content')
    <small class="d-block txt-lightblue fw-semibold text-uppercase">{{ __('app.nav_offers') }}</small>
    <h3 class="fw-semibold pt-2">{{ __('app.nav_create_offer') }}</h3>
    <p class="text-muted">{{ __('pages.offer_create_lead') }}</p>
    <hr class="mb-4">

    {{-- Start Creating Offer --}}
    @if(!session()->has('offer_client'))
        <div class="card mb-4">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-file-invoice-dollar txt-lightblue py-2"></i> {{ __('pages.offer_create_new') }}</span>
            </div>
            <div class="card-body">
                <form action="{{ route('save.offer.client.session') }}" method="post">
                    @csrf
                    <div class="row g-3">
                        @if(!session()->has('offer_client') || (($offerClient['client_id'] ?? null) !== null))
                            <div class="col-12 col-md-4 col-lg-4">
                                <label for="choose_client" class="form-label">{{ __('pages.offer_choose_client') }}</label>
                                <select name="choose_client" id="choose_client" class="form-control shadow-sm rounded-3">
                                    <option value="" disabled {{ old('choose_client') ? '' : 'selected' }}>{{ __('pages.offer_choose_client_opt') }}</option>
                                    @foreach ($clients as $client)
                                        <option value="{{ $client->id }}" {{ (string) old('choose_client') === (string) $client->id ? 'selected' : '' }}>{{ $client->client }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <span>{{ __('pages.offer_or_fill') }}</span>
                        @endif
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="client" class="form-label">{{ __('pages.offer_client') }}</label>
                            <input type="text" name="client" id="client" value="{{ old('client', $offerClient['client'] ?? '') }}" class="form-control shadow-sm rounded-3" placeholder="{{ __('pages.offer_client_ph_create') }}">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="phone" class="form-label">{{ __('app.phone') }}</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone', $offerClient['phone'] ?? '') }}" class="form-control shadow-sm rounded-3" placeholder="{{ __('pages.offer_phone_ph_create') }}">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="location" class="form-label">{{ __('pages.offer_address') }}</label>
                            <input type="text" name="location" id="location" value="{{ old('location', $offerClient['location'] ?? '') }}" class="form-control shadow-sm rounded-3" placeholder="{{ __('pages.offer_address_ph') }}">
                        </div>
                        <div class="col-12">
                            @if(session()->has('offer_client') && (($offerClient['client_id'] ?? null) !== null))
                                <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-check"></i> {{ __('pages.offer_update_client') }}</button>
                            @else
                                <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-plus"></i> {{ __('pages.offer_start') }}</button>
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
                <span><i class="fa-solid fa-list-check txt-lightblue py-2"></i> {{ __('pages.offer_fill_details') }}</span>
            </div>
            <div class="card-body">
                <form action="{{ route('save.offer.details.session') }}" method="post">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="service" class="form-label">{{ __('pages.offer_service') }}</label>
                            <input type="text" name="service" id="service" value="{{ old('service') }}" class="form-control shadow-sm rounded-3" placeholder="{{ __('pages.offer_service_ph_create') }}" autofocus>
                        </div>
                        <div class="col-12 col-md-8 col-lg-8">
                            <label for="price" class="form-label">{{ __('app.price') }}</label>
                            <input type="text" name="price" id="price" value="{{ old('price') }}" class="form-control shadow-sm rounded-3" placeholder="{{ __('pages.offer_price_ph_create') }}">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-plus"></i> {{ __('pages.offer_save_details') }}</button>
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
                <span><i class="fa-solid fa-list-ol txt-lightblue py-2"></i> {{ __('pages.offer_details') }}</span>
            </div>
            <div class="card-body">
                <span class="d-block mb-3"><i class="fa-regular fa-user"></i> {{ __('pages.offer_client') }}: <strong>{{ $offerClient['client'] }}</strong></span>
                <span class="d-block mb-3"><i class="fa-solid fa-phone"></i> {{ __('app.phone') }}: <strong>{{ $offerClient['phone'] }}</strong></span>
                <span class="d-block mb-3"><i class="fa-solid fa-location-dot"></i> {{ __('pages.offer_address') }}: <strong>{{ $offerClient['location'] }}</strong></span>

                <div class="table-responsive mt-5">
                    <table class="table table-bordered table-hover align-middle">
                        <thead>
                            <tr>
                                <th scope="col" class="text-nowrap">{{ __('pages.offer_service') }}</th>
                                <th scope="col" class="text-nowrap">{{ __('pages.offer_price_eur') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($offerDetails) == 0)
                                <tr>
                                    <td colspan="2" class="text-center"><i class="fa-regular fa-face-frown"></i> {{ __('pages.offer_no_details') }}</td>
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
                                    <td class="text-nowrap"><strong>{{ __('pages.offer_total') }}</strong></td>
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
                            <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-check"></i> {{ __('pages.offer_save') }}</button>
                        </form>
                    @endif
                    <form action="{{ route('clear.offer.session') }}" method="post">
                        @csrf
                        <button type="submit" class="btn btn-danger rounded-3 px-4 shadow-sm"><i class="fa-solid fa-trash"></i> {{ __('pages.offer_clear') }}</button>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection
