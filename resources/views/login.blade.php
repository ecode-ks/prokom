<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>WORKOM | {{ __('pages.title_login') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

        {{-- Assets --}}
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link rel="stylesheet" href="{{ asset('assets/custom.css') }}">
    </head>
    <body>
        <div class="lang-switcher-fixed">
            @php $locale = app()->getLocale(); @endphp
            <div class="dropdown lang-dropdown d-inline-block">
                <button class="btn bg-custom rounded-3 border dropdown-toggle d-inline-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false" aria-label="{{ __('pages.language') }}">
                    <span class="lang-flag-wrap">
                        @if($locale === 'sq')
                            <span class="lang-flag" aria-hidden="true"><img src="{{ asset('assets/flags/al.png') }}" alt="" width="21" height="15"></span>
                        @elseif($locale === 'en')
                            <span class="lang-flag" aria-hidden="true"><svg viewBox="0 0 21 15" xmlns="http://www.w3.org/2000/svg"><rect width="21" height="15" fill="#012169"/><path d="M0,0 L21,15 M21,0 L0,15" stroke="#fff" stroke-width="3"/><path d="M0,0 L21,15 M21,0 L0,15" stroke="#c8102e" stroke-width="2"/><path d="M10.5,0 V15 M0,7.5 H21" stroke="#fff" stroke-width="5"/><path d="M10.5,0 V15 M0,7.5 H21" stroke="#c8102e" stroke-width="3"/></svg></span>
                        @else
                            <span class="lang-flag" aria-hidden="true"><svg viewBox="0 0 21 15" xmlns="http://www.w3.org/2000/svg"><rect width="21" height="5" y="0" fill="#000"/><rect width="21" height="5" y="5" fill="#dd0000"/><rect width="21" height="5" y="10" fill="#ffce00"/></svg></span>
                        @endif
                    </span>
                    {{ strtoupper($locale) }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end lang-menu rounded-4 p-1">
                    @foreach (['sq', 'en', 'de'] as $code)
                        <li>
                            <a class="dropdown-item lang-option {{ $locale === $code ? 'is-active' : '' }}" href="{{ route('marketing.locale', $code) }}">
                                <span class="lang-flag-wrap">
                                    @if($code === 'sq')
                                        <span class="lang-flag" aria-hidden="true"><img src="{{ asset('assets/flags/al.png') }}" alt="" width="21" height="15"></span>
                                    @elseif($code === 'en')
                                        <span class="lang-flag" aria-hidden="true"><svg viewBox="0 0 21 15" xmlns="http://www.w3.org/2000/svg"><rect width="21" height="15" fill="#012169"/><path d="M0,0 L21,15 M21,0 L0,15" stroke="#fff" stroke-width="3"/><path d="M0,0 L21,15 M21,0 L0,15" stroke="#c8102e" stroke-width="2"/><path d="M10.5,0 V15 M0,7.5 H21" stroke="#fff" stroke-width="5"/><path d="M10.5,0 V15 M0,7.5 H21" stroke="#c8102e" stroke-width="3"/></svg></span>
                                    @else
                                        <span class="lang-flag" aria-hidden="true"><svg viewBox="0 0 21 15" xmlns="http://www.w3.org/2000/svg"><rect width="21" height="5" y="0" fill="#000"/><rect width="21" height="5" y="5" fill="#dd0000"/><rect width="21" height="5" y="10" fill="#ffce00"/></svg></span>
                                    @endif
                                </span>
                                <span class="lang-option-name">{{ __('pages.language_' . $code) }}</span>
                                <i class="fa-solid fa-check lang-option-check" aria-hidden="true"></i>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row min-vh-100">

                {{-- Entrance Banner --}}
                <div class="col-12 col-md-12 col-lg-6 d-none d-md-none d-lg-flex flex-column justify-content-center bg-darkblue text-light min-vh-100 p-5">
                    <div class="d-flex align-items-center">
                        <div class="me-2 rounded-3 bg-lightblue p-custom border-lightblue">
                            <i class="fa-solid fa-city"></i>
                        </div>
                        <div>
                            <span class="d-block fw-semibold">WORKOM</span>
                            <small class="d-block opacity-50">{{ __('pages.panel_subtitle') }}</small>
                        </div>
                    </div>
                    <div class="mt-4">
                        <span class="badge rounded-pill bg-success bg-opacity-10 shadow-sm text-light px-3 py-2 fw-semibold">
                            <i class="fa-regular fa-circle-dot me-2 text-success"></i>
                            {{ __('pages.trusted_by') }} <strong>10+</strong> {{ __('pages.companies_count') }}
                        </span>
                    </div>
                    <div class="mt-5">
                        <h1 class="display-4 fw-bold col-8">{{ __('pages.hero_title') }} <span class="opacity-50">{{ __('pages.hero_title_rest') }}</span></h1>
                        <p class="lead opacity-75 col-7 mt-5">{{ __('pages.hero_lead') }}</p>
                    </div>
                    <div class="mt-4">
                        <div class="d-flex justify-content-between opacity-50">
                            <small>&copy; {{ date('Y') }} WORKOM</small>
                            <small><i class="fa-solid fa-shield-alt"></i> ISO 27001</small>
                        </div>
                    </div>
                </div>

                @if ($admin == false)

                    {{-- Register Admin --}}
                <div class="col-12 col-md-12 col-lg-6 d-flex align-items-center flex-column justify-content-center bg-custom min-vh-100 overflow-y-auto p-5">
                        <div class="col-12 col-md-8 lang-page-form">
                            <span class="txt-darkblue fw-semibold text-uppercase">{{ __('pages.welcome') }}</span>
                            <h1 class="fw-bold mt-3">{{ __('pages.create_admin') }}</h1>
                            <form action="{{ route('register.admin') }}" method="post" class="mt-4" onsubmit="handleSubmit(event)">
                                @csrf
                                <div class="mb-3">
                                    <label for="user" class="form-label text-uppercase small fw-semibold">{{ __('pages.full_name') }}</label>
                                    <input type="text" class="form-control shadow-sm py-custom rounded-3" id="user" name="user" placeholder="{{ __('pages.placeholder_full_name') }}">
                                </div>
                                <div class="mb-3">
                                    <label for="username" class="form-label text-uppercase small fw-semibold">{{ __('pages.username') }}</label>
                                    <input type="text" class="form-control shadow-sm py-custom rounded-3" id="username" name="username" placeholder="{{ __('pages.placeholder_username') }}">
                                </div>
                                <div class="mb-4">
                                    <label for="password" class="form-label text-uppercase small fw-semibold">{{ __('pages.password') }}</label>
                                    <input type="password" class="form-control shadow-sm py-custom rounded-3" id="password" name="password" placeholder="{{ __('pages.placeholder_password') }}">
                                </div>
                                <button type="submit" id="submitBtn" class="btn btn-darkblue w-100 rounded-3 py-custom fw-semibold">{{ __('pages.create_account') }} <i class="fa-solid fa-angle-right"></i></button>
                            </form>
                            <div class="mt-4 text-center">
                                <a href="{{ route('login') }}" class="text-decoration-none txt-darkblue fw-semibold small"><span class="text-dark text-muted">{{ __('pages.no_account') }}</span> {{ __('pages.register_now') }}</a>
                            </div>
                            <div class="mt-4 text-center d-block d-md-none d-lg-none">
                                <span class="badge rounded-pill bg-success bg-opacity-10 shadow-sm text-success border border-success px-3 py-2 fw-semibold">
                                    <i class="fa-regular fa-circle-dot me-2 text-success"></i>
                                    {{ __('pages.trusted_by') }} <strong>10+</strong> {{ __('pages.companies_count') }}
                                </span>
                            </div>
                        </div>
                    </div>
                @else

                    {{-- Login Form --}}
                <div class="col-12 col-md-12 col-lg-6 d-flex align-items-center flex-column justify-content-center bg-custom min-vh-100 overflow-y-auto p-5">
                        <div class="col-12 col-md-8 lang-page-form">
                            <span class="txt-darkblue fw-semibold text-uppercase">{{ __('pages.welcome_back') }}</span>
                            <h1 class="fw-bold mt-3">{{ __('pages.login_heading') }}</h1>
                            <form action="{{ route('check.login') }}" method="post" class="mt-4" onsubmit="handleSubmit(event)">
                                @csrf
                                <div class="mb-3">
                                    <label for="username" class="form-label text-uppercase small fw-semibold">{{ __('pages.username') }}</label>
                                    <input type="text" class="form-control shadow-sm py-custom rounded-3" id="username" name="username" placeholder="{{ __('pages.placeholder_username') }}">
                                </div>
                                <div class="mb-4">
                                    <label for="password" class="form-label text-uppercase small fw-semibold">{{ __('pages.password') }}</label>
                                    <input type="password" class="form-control shadow-sm py-custom rounded-3" id="password" name="password" placeholder="{{ __('pages.placeholder_password') }}">
                                </div>
                                <button type="submit" class="btn btn-darkblue w-100 rounded-3 py-custom fw-semibold">{{ __('pages.login') }} <i class="fa-solid fa-angle-right"></i></button>
                            </form>
                            <div class="mt-4 text-center">
                                <a href="{{ route('register') }}" class="text-decoration-none txt-darkblue fw-semibold small"><span class="text-dark text-muted">{{ __('pages.no_account') }}</span> {{ __('pages.register_now') }}</a>
                            </div>
                            <div class="mt-4 text-center d-block d-md-none d-lg-none">
                                <span class="badge rounded-pill bg-success bg-opacity-10 shadow-sm text-success border border-success px-3 py-2 fw-semibold">
                                    <i class="fa-regular fa-circle-dot me-2 text-success"></i>
                                    {{ __('pages.trusted_by') }} <strong>10+</strong> {{ __('pages.companies_count') }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Success Alerts -->
        @if (\Session::has('success'))
            <div class="toast-container position-fixed bottom-0 end-0 p-3">
                <div id="successToast" class="toast fade show" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header bg-success border-success">
                        <strong class="me-auto text-light">{{ __('pages.toast_notice') }}</strong>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body text-dark border border-success">
                        {!! \Session::get('success') !!}
                    </div>
                </div>
            </div>
        @endif

        <!-- Error Alerts -->
        @if (\Session::has('error'))
            <div class="toast-container position-fixed bottom-0 end-0 p-3">
                <div id="errorToast" class="toast fade show" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header bg-danger border-danger">
                        <strong class="me-auto text-light">{{ __('pages.toast_notice') }}</strong>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body text-dark border border-danger">
                        {!! \Session::get('error') !!}
                    </div>
                </div>
            </div>
        @endif

        {{-- Disable submit button --}}
        <script>
            function handleSubmit(event) {
                const submitBtn = document.getElementById('submitBtn');
                submitBtn.disabled = true;
            }
        </script>
    </body>
</html>
