@extends('template.template')

{{-- Page Title --}}
@section('title', __('pages.profile_title'))

{{-- Page Content --}}
@section('content')
    <small class="d-block txt-lightblue fw-semibold text-uppercase">{{ __('pages.profile_account') }}</small>
    <h3 class="fw-semibold pt-2">{{ __('pages.profile_heading') }}</h3>
    <p class="text-muted">{{ __('pages.profile_lead') }}</p>
    <hr class="mb-4">

    {{-- Profile Content --}}
    <div class="row g-4 align-items-stretch mb-5">
        <div class="col-12 col-md-12 col-lg-3">
            <div class="card h-100 overflow-hidden">
                <div class="card-body text-center d-flex flex-column justify-content-center py-5" style="background: linear-gradient(180deg, rgba(13,110,253,0.08), rgba(13,110,253,0.02));">
                    <div class="mx-auto d-flex align-items-center justify-content-center rounded-circle bg-white shadow-sm mb-4" style="width: 120px; height: 120px;">
                        <i class="fa-regular fa-circle-user txt-lightblue" style="font-size: 5rem;"></i>
                    </div>
                    <h4 class="fw-semibold mb-1 text-truncate" title="{{ session('user') ?? session('company') }}">{{ session('user') ?? session('company') }}</h4>
                    <p class="text-muted mb-0 text-truncate" title="{{ session('username') }}">{{ session('username') }}</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-12 col-lg-9">
            <div class="card h-100">
                <div class="card-header rounded-top-4 bg-white border-0 pt-4 px-4">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-gear txt-lightblue"></i>
                        <span class="fw-semibold">{{ __('pages.profile_settings') }}</span>
                    </div>
                </div>
                <div class="card-body px-4 pb-4 pt-2">
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-md-6">
                            <label class="form-label">{{ __('pages.profile_full_name') }}</label>
                            <input type="text" class="form-control shadow-sm rounded-3 bg-light" value="{{ session('user') ?? session('company') }}" placeholder="{{ __('pages.profile_full_name') }}" disabled>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">{{ __('pages.username') }}</label>
                            <input type="text" class="form-control shadow-sm rounded-3 bg-light" value="{{ session('username') }}" placeholder="{{ __('pages.username') }}" disabled>
                        </div>
                    </div>
{{--                    @if(session('role') === 'company')--}}
{{--                        @php $companyLanguage = session('language', 'sq'); @endphp--}}
{{--                        <form action="{{ route('update.company.language') }}" method="post" class="mb-4">--}}
{{--                            @csrf--}}
{{--                            <label class="form-label">{{ __('pages.language') }}</label>--}}
{{--                            <div>--}}
{{--                                <div class="dropdown dropup lang-dropdown w-100">--}}
{{--                                    <button class="btn bg-custom rounded-3 border dropdown-toggle d-inline-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">--}}
{{--                                        <span class="lang-flag-wrap">--}}
{{--                                            @if($companyLanguage === 'sq')--}}
{{--                                                <span class="lang-flag" aria-hidden="true"><img src="{{ asset('assets/flags/al.png') }}" alt="" width="21" height="15"></span>--}}
{{--                                            @elseif($companyLanguage === 'en')--}}
{{--                                                <span class="lang-flag" aria-hidden="true"><svg viewBox="0 0 21 15" xmlns="http://www.w3.org/2000/svg"><rect width="21" height="15" fill="#012169"/><path d="M0,0 L21,15 M21,0 L0,15" stroke="#fff" stroke-width="3"/><path d="M0,0 L21,15 M21,0 L0,15" stroke="#c8102e" stroke-width="2"/><path d="M10.5,0 V15 M0,7.5 H21" stroke="#fff" stroke-width="5"/><path d="M10.5,0 V15 M0,7.5 H21" stroke="#c8102e" stroke-width="3"/></svg></span>--}}
{{--                                            @else--}}
{{--                                                <span class="lang-flag" aria-hidden="true"><svg viewBox="0 0 21 15" xmlns="http://www.w3.org/2000/svg"><rect width="21" height="5" y="0" fill="#000"/><rect width="21" height="5" y="5" fill="#dd0000"/><rect width="21" height="5" y="10" fill="#ffce00"/></svg></span>--}}
{{--                                            @endif--}}
{{--                                        </span>--}}
{{--                                        {{ __('pages.language_' . $companyLanguage) }}--}}
{{--                                    </button>--}}
{{--                                    <ul class="dropdown-menu lang-menu rounded-4 p-1">--}}
{{--                                        @foreach (['sq', 'en', 'de'] as $code)--}}
{{--                                            <li>--}}
{{--                                                <button type="submit" name="language" value="{{ $code }}" class="dropdown-item lang-option {{ $companyLanguage === $code ? 'is-active' : '' }}">--}}
{{--                                                    <span class="lang-flag-wrap">--}}
{{--                                                        @if($code === 'sq')--}}
{{--                                                            <span class="lang-flag" aria-hidden="true"><img src="{{ asset('assets/flags/al.png') }}" alt="" width="21" height="15"></span>--}}
{{--                                                        @elseif($code === 'en')--}}
{{--                                                            <span class="lang-flag" aria-hidden="true"><svg viewBox="0 0 21 15" xmlns="http://www.w3.org/2000/svg"><rect width="21" height="15" fill="#012169"/><path d="M0,0 L21,15 M21,0 L0,15" stroke="#fff" stroke-width="3"/><path d="M0,0 L21,15 M21,0 L0,15" stroke="#c8102e" stroke-width="2"/><path d="M10.5,0 V15 M0,7.5 H21" stroke="#fff" stroke-width="5"/><path d="M10.5,0 V15 M0,7.5 H21" stroke="#c8102e" stroke-width="3"/></svg></span>--}}
{{--                                                        @else--}}
{{--                                                            <span class="lang-flag" aria-hidden="true"><svg viewBox="0 0 21 15" xmlns="http://www.w3.org/2000/svg"><rect width="21" height="5" y="0" fill="#000"/><rect width="21" height="5" y="5" fill="#dd0000"/><rect width="21" height="5" y="10" fill="#ffce00"/></svg></span>--}}
{{--                                                        @endif--}}
{{--                                                    </span>--}}
{{--                                                    <span class="lang-option-name">{{ __('pages.language_' . $code) }}</span>--}}
{{--                                                    <i class="fa-solid fa-check lang-option-check" aria-hidden="true"></i>--}}
{{--                                                </button>--}}
{{--                                            </li>--}}
{{--                                        @endforeach--}}
{{--                                    </ul>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </form>--}}
{{--                    @endif--}}
                    <form action="{{ route('update.profile.password') }}" method="post">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <label for="old_password" class="form-label">{{ __('pages.current_password') }}</label>
                                <input type="password" name="old_password" id="old_password" class="form-control shadow-sm rounded-3" placeholder="{{ __('pages.placeholder_current_password') }}" required>
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="new_password" class="form-label">{{ __('pages.new_password') }}</label>
                                <input type="password" name="new_password" id="new_password" class="form-control shadow-sm rounded-3" placeholder="{{ __('pages.placeholder_new_password') }}" required>
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="new_password_confirmation" class="form-label">{{ __('pages.confirm_new_password') }}</label>
                                <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control shadow-sm rounded-3" placeholder="{{ __('pages.placeholder_confirm_new_password') }}" required>
                            </div>
                            <div class="col-12 pt-2">
                                <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-key"></i> {{ __('pages.update_password') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Change Package --}}
    @if(session('role') === 'company')
        @if($packages)
            <div class="alert alert-primary mb-3 rounded-4 shadow-sm" role="alert">
                <span class="fw-semibold">{{ __('pages.package_limit_alert') }}</span>
            </div>
        @endif
        <div class="card shadow-sm overflow-hidden" style="background: linear-gradient(165deg, #f8fbff 0%, #ffffff 40%, #f5f9ff 100%);">
            <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-3" style="width: 42px; height: 42px; background: rgba(13,110,253,0.1);">
                            <i class="fa-brands fa-trello txt-lightblue"></i>
                        </span>
                        <div>
                            <h5 class="mb-0 fw-semibold">{{ __('pages.change_subscription') }}</h5>
                            <small class="text-muted">{{ __('pages.choose_package_lead') }}</small>
                        </div>
                    </div>
                    <span class="badge rounded-pill text-bg-light border px-3 py-2">{{ __('pages.subscription_management') }}</span>
                </div>
            </div>
            <div class="card-body px-4 pb-4 pt-3">
                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-12">
                        <div class="rounded-4 border bg-white p-3 h-100 shadow-sm">
                            <small class="text-muted d-block mb-1">{{ __('pages.current_package') }}</small>
                            <div class="fw-semibold txt-lightblue fs-5 text-truncate" title="{{ session('package') ?? __('pages.no_active_package') }}">
                                {{ session('package') ?? __('pages.no_active_package') }}
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="rounded-4 border bg-white p-3 h-100 shadow-sm">
                            <small class="text-muted d-block mb-1">{{ __('pages.valid_until_date') }}</small>
                            <div class="fw-semibold txt-lightblue fs-5">
                                {{ session('expire_date') ? \Carbon\Carbon::parse(session('expire_date'))->format('d/m/Y') : '--/--/----' }}
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="rounded-4 border bg-white p-3 h-100 shadow-sm">
                            <small class="text-muted d-block mb-1">{{ __('pages.staff_limit') }}</small>
                            <div class="fw-semibold txt-lightblue fs-5">
                                {{ session('staff_limit') }}
                            </div>
                        </div>
                    </div>
                </div>
                @if($packages)
                    <div class="row g-4">
                        @foreach ($packages as $package)
                            <div class="col-12 col-md-6 col-xl-4">
                                <div class="card h-100 shadow-sm position-relative overflow-hidden" style="transition: transform .25s ease, box-shadow .25s ease;">
                                    <div class="position-absolute top-0 start-0 w-100" style="height: 4px; background: linear-gradient(90deg, #0d6efd, #4ea4ff);"></div>
                                    <div class="card-body d-flex flex-column p-4" style="background: linear-gradient(180deg, rgba(13,110,253,0.08), rgba(13,110,253,0.01));">
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <h6 class="fw-semibold mb-0 text-truncate pe-2" title="{{ $package->title }}">{{ $package->title }}</h6>
                                            <span class="badge rounded-pill text-bg-light border">{{ __('pages.months', ['count' => $package->months]) }}</span>
                                        </div>
                                        <div class="mb-3">
                                            <span class="h3 fw-bold mb-0 txt-lightblue">{{ $package->price }} €</span>
                                            <small class="text-muted d-block">{{ __('pages.package_total_price') }}</small>
                                        </div>
                                        <p class="text-muted mb-3">{{ __('pages.package_staff_limit') }} : {{ $package->staff_limit }}</p>
                                        <p class="text-muted mb-4" style="min-height: 72px;">{!! nl2br(e($package->description)) !!}</p>
                                        <a href="{{ route('change.package', $package->id)}}" class="btn btn-outline-primary rounded-3 mt-auto w-100">
                                            <i class="fa-solid fa-bolt me-1"></i> {{ __('pages.choose_this_package') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-light border rounded-4 mb-0">
                        {{ __('pages.no_packages') }}
                    </div>
                @endif
            </div>
        </div>
    @endif
@endsection
