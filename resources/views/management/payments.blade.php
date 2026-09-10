@extends('template.template')

{{-- Page Title --}}
@section('title', __('pages.payments_title'))

{{-- Page Content --}}
@section('content')
    <small class="d-block txt-lightblue fw-semibold text-uppercase">{{ __('app.nav_companies') }}</small>
    <h3 class="fw-semibold pt-2">{{ __('pages.payments_title') }}</h3>
    <p class="text-muted">{{ __('pages.payments_lead') }}</p>
    <hr class="mb-4">

    <div class="card">
        <div class="card-header rounded-top-4 bg-white">
            <span><i class="fa-solid fa-list-ol txt-lightblue py-2"></i> {{ __('pages.payments_list') }}</span>
        </div>
        <div class="card-body">
            <form action="{{ route('payments') }}" method="get" class="input-group mb-3">
                <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-control shadow-sm rounded-start-3" placeholder="{{ __('app.search') }}" aria-label="{{ __('app.search') }}" aria-describedby="basic-addon2">
                <button type="submit" class="btn btn-darkblue shadow-sm rounded-end-3" id="basic-addon2"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th scope="col" class="text-nowrap">{{ __('app.date') }}</th>
                            <th scope="col" class="text-nowrap">{{ __('pages.companies_company') }}</th>
                            <th scope="col" class="text-nowrap">{{ __('pages.payments_package') }}</th>
                            <th scope="col" class="text-nowrap">{{ __('app.price') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($payments) === 0)
                            <tr>
                                <td colspan="4" class="text-center"><i class="fa-regular fa-face-frown"></i> {{ __('app.no_data') }}</td>
                            </tr>
                        @else
                            @foreach ($payments as $payment)
                                <tr>
                                    <td class="text-nowrap">{{ \Carbon\Carbon::parse($payment->date)->format('d/m/Y') }}</td>
                                    <td class="text-nowrap">{{ $payment->company }}</td>
                                    <td class="text-nowrap">{{ $payment->package }}</td>
                                    <td class="text-nowrap">{{ number_format($payment->price, 2) }}€</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>

            @if ($payments->hasPages())
                <div class="mt-3">
                    {{ $payments->links('vendor.pagination.custom') }}
                </div>
            @endif
        </div>
    </div>
@endsection
