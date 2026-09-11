@extends('template.template')

{{-- Page Title --}}
@section('title', __('pages.company_details_title'))

{{-- Page Content --}}
@section('content')
    <small class="d-block txt-lightblue fw-semibold text-uppercase"><a href="{{ route('companies') }}" class="text-decoration-none txt-lightblue fw-semibold"><i class="fa-solid fa-circle-chevron-left"></i> {{ __('pages.back_full') }}</a></small>
    <h3 class="fw-semibold pt-2">{{ __('pages.company_details_title') }}</h3>
    <p class="text-muted">{{ __('pages.company_details_lead') }}</p>
    <hr class="mb-4">

    <div class="card shadow-sm mb-4">
        <div class="card-body p-4" style="background: linear-gradient(135deg, rgba(13,110,253,0.08), rgba(13,110,253,0.02)); border-radius: 1rem;">
            <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                <div>
                    <small class="text-uppercase opacity-75 d-block"><i class="fa-solid fa-building me-1"></i> {{ __('pages.company_details_profile') }}</small>
                    <h4 class="fw-semibold mb-1">{{ $companyDetails->company }}</h4>
                    <small class="text-muted">ID #{{ $companyDetails->id }} · {{ $companyDetails->email }}</small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    @if($companyDetails->status === 'Active')
                        <span class="badge rounded-pill text-success text-bg-success bg-opacity-25 border border-success px-3 py-2">{{ __('pages.active') }}</span>
                    @else
                        <span class="badge rounded-pill text-danger text-bg-danger bg-opacity-25 border border-danger px-3 py-2">{{ __('pages.blocked') }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12 col-xl-8">
            <div class="card shadow-sm h-100">
                <div class="card-header rounded-top-4 bg-white">
                    <span><i class="fa-solid fa-circle-info txt-lightblue py-2"></i> {{ __('pages.company_details_info') }}</span>
                </div>
                <div class="card-body py-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <span class="d-block text-muted"><i class="fa-solid fa-building"></i> {{ __('pages.companies_company') }}</span>
                            <span class="fw-semibold">{{ $companyDetails->company }}</span>
                        </div>
                        <div class="col-12 col-md-6">
                            <span class="d-block text-muted"><i class="fa-solid fa-envelope"></i> {{ __('pages.email') }}</span>
                            <span class="fw-semibold">{{ $companyDetails->email }}</span>
                        </div>
                        <div class="col-12 col-md-6">
                            <span class="d-block text-muted"><i class="fa-solid fa-phone"></i> {{ __('pages.phone') }}</span>
                            <span class="fw-semibold">{{ $companyDetails->phone }}</span>
                        </div>
                        <div class="col-12 col-md-6">
                            <span class="d-block text-muted"><i class="fa-solid fa-location-dot"></i> {{ __('pages.location') }}</span>
                            <span class="fw-semibold">{{ $companyDetails->location }}</span>
                        </div>
                        <div class="col-12 col-md-6">
                            <span class="d-block text-muted"><i class="fa-solid fa-user"></i> {{ __('pages.username') }}</span>
                            <span class="fw-semibold">{{ $companyDetails->username }}</span>
                        </div>
                        <div class="col-12 col-md-6">
                            <span class="d-block text-muted"><i class="fa-solid fa-box-open"></i> {{ __('pages.company_details_package_id') }}</span>
                            <span class="fw-semibold">{{ $companyDetails->title ?? '-' }}</span>
                        </div>
                        <div class="col-12 col-md-6">
                            <span class="d-block text-muted"><i class="fa-regular fa-calendar"></i> {{ __('pages.company_details_payment_expire') }}</span>
                            <span class="fw-semibold">{{ $companyDetails->payment_expire ? \Carbon\Carbon::parse($companyDetails->payment_expire)->format('d/m/Y') : '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-4">
            <div class="card shadow-sm h-100">
                <div class="card-header rounded-top-4 bg-white">
                    <span><i class="fa-solid fa-shield-halved txt-lightblue py-2"></i> {{ __('pages.company_details_account_control') }}</span>
                </div>
                <div class="card-body py-4">
                    <small class="text-muted d-block mb-2">{{ __('pages.company_details_current_status') }}</small>
                    @if($companyDetails->status === 'Active')
                        <span class="badge rounded-pill text-success text-bg-success bg-opacity-25 border border-success mb-4">{{ __('pages.active') }}</span>
                    @else
                        <span class="badge rounded-pill text-danger text-bg-danger bg-opacity-25 border border-danger mb-4">{{ __('pages.blocked') }}</span>
                    @endif

                    <div class="d-grid gap-2">
                        @if($companyDetails->status === 'Active')
                            <button type="button" class="btn btn-success rounded-3 shadow-sm" disabled><i class="fa-solid fa-check"></i> {{ __('pages.company_details_activate') }}</button>
                            <a href="{{ route('update.company.status', [$companyDetails->id, 'Blocked']) }}" class="btn btn-danger rounded-3 shadow-sm"><i class="fa-solid fa-ban"></i> {{ __('pages.company_details_block') }}</a>
                        @else
                            <a href="{{ route('update.company.status', [$companyDetails->id, 'Active']) }}" class="btn btn-success rounded-3 shadow-sm"><i class="fa-solid fa-check"></i> {{ __('pages.company_details_activate') }}</a>
                            <button type="button" class="btn btn-danger rounded-3 shadow-sm" disabled><i class="fa-solid fa-ban"></i> {{ __('pages.company_details_block') }}</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
