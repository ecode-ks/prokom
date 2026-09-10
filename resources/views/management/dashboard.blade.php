@extends('template.template')

{{-- Page Title --}}
@section('title', __('app.nav_dashboard'))

{{-- Page Content --}}
@section('content')
    @php
        $fmt = function ($value) {
            return $value ? \Carbon\Carbon::parse($value)->format('H:i') : '--:--';
        };
        $onPause = !empty($isActive) && !empty($checkIn) && $checkIn->pause_start_time && !$checkIn->pause_end_time;
        $checkInStatus = empty($checkIn)
            ? __('app.inactive')
            : (empty($isActive) ? __('app.completed') : ($onPause ? __('app.on_pause') : __('app.at_work')));

        $subscriptionExpireDate = session('expire_date')
            ? \Carbon\Carbon::parse(session('expire_date'))->startOfDay()
            : null;
        $showSubscriptionExpiryAlert = !empty($isCompany)
            && $subscriptionExpireDate
            && $subscriptionExpireDate->gte(now()->startOfDay())
            && $subscriptionExpireDate->lte(now()->addDays(30)->startOfDay());
    @endphp

    {{-- Hero --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body p-4 rounded-4" style="background: linear-gradient(90deg, rgba(13,110,253,0.08), rgba(13,110,253,0.02));">
            <small class="d-block text-muted text-uppercase fw-semibold" style="letter-spacing:.04em;font-size:.72rem;">{{ __('app.nav_dashboard') }} · {{ $todayLabel }}</small>
            <h3 class="fw-semibold mb-1 mt-2">{{ __('pages.dash_welcome') }}, <span class="txt-lightblue">{{ $displayName }}</span></h3>
            <p class="text-muted mb-0">{{ __('pages.dash_lead') }}</p>
        </div>
    </div>

    {{-- Package Alerts --}}
    @if($showSubscriptionExpiryAlert)
        <div
            id="subscriptionExpiryAlert"
            class="alert alert-primary alert-dismissible shadow-sm rounded-4 mb-4"
            role="alert"
            data-dismiss-key="workom-sub-expiry-{{ session('company_id') }}-{{ $subscriptionExpireDate->format('Y-m-d') }}"
        >
            <span>{!! __('pages.dash_sub_expiry', ['date' => $subscriptionExpireDate->format('d/m/Y'), 'url' => route('profile')]) !!}</span>
            <button type="button" class="btn-close" id="subscriptionExpiryDismiss" aria-label="{{ __('app.close') }}"></button>
        </div>
    @endif

    {{-- Quick actions --}}
    @if($showQuickActions)
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white rounded-4 py-3">
                <span class="fw-semibold"><i class="fa-solid fa-bolt txt-lightblue me-1"></i> {{ __('pages.dash_quick_actions') }}</span>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="dashboard-quick-actions">
                    @if(!empty($isAdmin))
                        <a href="{{ route('companies') }}" class="dashboard-quick-btn"><i class="fa-solid fa-building"></i> {{ __('app.nav_companies') }}</a>
                        <a href="{{ route('packages') }}" class="dashboard-quick-btn"><i class="fa-brands fa-trello"></i> {{ __('app.nav_packages') }}</a>
                        <a href="{{ route('payments') }}" class="dashboard-quick-btn"><i class="fa-solid fa-money-bill"></i> {{ __('app.nav_payments') }}</a>
                    @elseif($isCompany)
                        <a href="{{ route('projects') }}" class="dashboard-quick-btn"><i class="fa-solid fa-diagram-project"></i> {{ __('app.nav_projects') }}</a>
                        <a href="{{ route('employees') }}" class="dashboard-quick-btn"><i class="fa-solid fa-users"></i> {{ __('app.nav_employees') }}</a>
                        <a href="{{ route('offers') }}" class="dashboard-quick-btn"><i class="fa-solid fa-file-invoice-dollar"></i> {{ __('app.nav_offers') }}</a>
                        <a href="{{ route('financial.transactions') }}" class="dashboard-quick-btn"><i class="fa-solid fa-wallet"></i> {{ __('pages.dash_transactions') }}</a>
                        <a href="{{ route('checkins') }}" class="dashboard-quick-btn"><i class="fa-solid fa-user-check"></i> {{ __('app.nav_checkin') }}</a>
                    @elseif($isManager)
                        <a href="{{ route('tasks') }}" class="dashboard-quick-btn"><i class="fa-solid fa-plus"></i> {{ __('pages.dash_assign_task') }}</a>
                        <a href="{{ route('projects') }}" class="dashboard-quick-btn"><i class="fa-solid fa-diagram-project"></i> {{ __('app.nav_projects') }}</a>
                        <a href="{{ route('project.reports') }}" class="dashboard-quick-btn"><i class="fa-solid fa-clipboard-list"></i> {{ __('pages.dash_reports') }}</a>
                    @elseif($isEmployee)
                        <a href="{{ route('tasks') }}" class="dashboard-quick-btn"><i class="fa-solid fa-list-check"></i> {{ __('app.nav_tasks') }}</a>
                        <a href="{{ route('task.reports') }}" class="dashboard-quick-btn"><i class="fa-solid fa-file-lines"></i> {{ __('app.nav_task_reports') }}</a>
                        <a href="{{ route('working.hours') }}" class="dashboard-quick-btn"><i class="fa-solid fa-clock"></i> {{ __('app.nav_working_hours') }}</a>
                    @elseif($isFinance)
                        <a href="{{ route('financial.transactions') }}" class="dashboard-quick-btn"><i class="fa-solid fa-wallet"></i> {{ __('pages.dash_transactions') }}</a>
                        <a href="{{ route('financial.report') }}" class="dashboard-quick-btn"><i class="fa-solid fa-chart-pie"></i> {{ __('app.nav_financial_report') }}</a>
                        <a href="{{ route('offers') }}" class="dashboard-quick-btn"><i class="fa-solid fa-file-invoice-dollar"></i> {{ __('app.nav_offers') }}</a>
                        <a href="{{ route('calculate.salary') }}" class="dashboard-quick-btn"><i class="fa-solid fa-calculator"></i> {{ __('app.nav_calculate_salary') }}</a>
                    @elseif($isHr)
                        <a href="{{ route('employees') }}" class="dashboard-quick-btn"><i class="fa-solid fa-users"></i> {{ __('app.nav_employees') }}</a>
                        <a href="{{ route('checkins') }}" class="dashboard-quick-btn"><i class="fa-solid fa-user-check"></i> {{ __('app.nav_checkin') }}</a>
                        <a href="{{ route('vacations') }}" class="dashboard-quick-btn"><i class="fa-solid fa-calendar-days"></i> {{ __('app.nav_vacations') }}</a>
                        <a href="{{ route('contracts') }}" class="dashboard-quick-btn"><i class="fa-solid fa-file-contract"></i> {{ __('app.nav_contracts') }}</a>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Info Cards --}}
    @if($isCompany && $companyKpis)
        <div class="row g-3 mb-3">
            <div class="col-6 col-xl-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted text-uppercase d-block mb-2">{{ __('pages.dash_kpi_employees') }}</small>
                        <h4 class="fw-semibold mb-0">{{ number_format($companyKpis->employees) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted text-uppercase d-block mb-2">{{ __('pages.dash_kpi_projects') }}</small>
                        <h4 class="fw-semibold mb-0">{{ number_format($companyKpis->projects) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted text-uppercase d-block mb-2">{{ __('pages.dash_kpi_clients') }}</small>
                        <h4 class="fw-semibold mb-0">{{ number_format($companyKpis->clients) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted text-uppercase d-block mb-2">{{ __('pages.dash_kpi_offers') }}</small>
                        <h4 class="fw-semibold mb-0">{{ number_format($companyKpis->offers) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Company News --}}
    @if(in_array($role, ['company', 'manager', 'employee', 'finance', 'hr'], true))
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white rounded-4 py-3 d-flex justify-content-between align-items-center">
                <span class="fw-semibold"><i class="fa-regular fa-newspaper txt-lightblue me-1"></i> {{ __('pages.dash_news_title') }}</span>
                @if($isCompany || $isManager)
                    <a href="{{ route('company.news') }}" class="dashboard-link">{{ __('app.see_all') }}</a>
                @endif
            </div>
            <div class="card-body px-4 pb-3 pt-0">
                @if(($dashboardNews ?? collect())->isEmpty())
                    <p class="text-muted mb-0 small">{{ __('pages.dash_no_news') }}</p>
                @else
                    @foreach($dashboardNews as $news)
                        <div class="@if(!$loop->last) border-bottom @endif py-3">
                            <small class="text-muted d-block mb-1">
                                {{ $news->date ? \Carbon\Carbon::parse($news->date)->format('d/m/Y') : '-' }}
                                @if(!empty($news->project_id))
                                    · {{ $news->project_name ?? __('pages.dash_project_fallback', ['id' => $news->project_id]) }}
                                @endif
                            </small>
                            <div class="text-dark">{!! nl2br(e($news->info)) !!}</div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    @endif

    {{-- Check-in kompakt: employee / manager / finance / hr --}}
    @if(!empty($showCheckIn) && !empty($canCheckIn))
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white rounded-4 py-3 d-flex justify-content-between align-items-center">
                <span class="fw-semibold"><i class="fa-solid fa-user-check txt-lightblue me-1"></i> {{ __('pages.dash_checkin') }}</span>
                <span class="checkin-status {{ !$isActive ? 'is-idle' : ($onPause ? 'is-pause' : 'is-work') }}">{{ $checkInStatus }}</span>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="row g-2 mb-3">
                    <div class="col-6 col-md-3">
                        <div class="border rounded-3 p-3 h-100">
                            <small class="text-muted d-block">{{ __('pages.dash_check_in') }}</small>
                            <strong>{{ $fmt($checkIn->check_in_time ?? null) }}</strong>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="border rounded-3 p-3 h-100">
                            <small class="text-muted d-block">{{ __('pages.dash_pause_start') }}</small>
                            <strong>{{ $fmt($checkIn->pause_start_time ?? null) }}</strong>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="border rounded-3 p-3 h-100">
                            <small class="text-muted d-block">{{ __('pages.dash_pause_end') }}</small>
                            <strong>{{ $fmt($checkIn->pause_end_time ?? null) }}</strong>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="border rounded-3 p-3 h-100">
                            <small class="text-muted d-block">{{ __('pages.dash_check_out') }}</small>
                            <strong>{{ $fmt($checkIn->check_out_time ?? null) }}</strong>
                        </div>
                    </div>
                </div>

                <div class="checkin-actions">
                    @if(!$isActive)
                        <form action="{{ route('start.work') }}" method="post" id="startWorkForm" class="w-100">
                            @csrf
                            <input type="hidden" name="check_in_location" id="check_in_location">
                            <button type="submit" class="btn btn-darkblue rounded-3 px-4 py-2 fw-semibold">
                                <i class="fa-solid fa-play me-1"></i> {{ __('pages.dash_start_work') }}
                            </button>
                        </form>
                    @else
                        @if(!$checkIn->pause_start_time)
                            <form action="{{ route('start.break') }}" method="post" id="startBreakForm">
                                @csrf
                                <input type="hidden" name="id" value="{{ $checkIn->id }}">
                                <input type="hidden" name="pause_start_location" id="pause_start_location">
                                <button type="submit" class="btn btn-outline-secondary rounded-3 px-4 py-2 fw-semibold">
                                    <i class="fa-solid fa-mug-saucer me-1"></i> {{ __('pages.dash_start_pause') }}
                                </button>
                            </form>
                        @elseif(!$checkIn->pause_end_time)
                            <form action="{{ route('end.break') }}" method="post" id="endBreakForm">
                                @csrf
                                <input type="hidden" name="id" value="{{ $checkIn->id }}">
                                <input type="hidden" name="pause_end_location" id="pause_end_location">
                                <button type="submit" class="btn btn-outline-secondary rounded-3 px-4 py-2 fw-semibold">
                                    <i class="fa-solid fa-play me-1"></i> {{ __('pages.dash_end_pause') }}
                                </button>
                            </form>
                        @endif

                        @if(!$checkIn->pause_start_time || $checkIn->pause_end_time)
                            <form action="{{ route('end.work') }}" method="post" id="endWorkForm">
                                @csrf
                                <input type="hidden" name="id" value="{{ $checkIn->id }}">
                                <input type="hidden" name="check_out_location" id="check_out_location">
                                <button type="submit" class="btn btn-darkblue rounded-3 px-4 py-2 fw-semibold">
                                    <i class="fa-solid fa-stop me-1"></i> {{ __('pages.dash_end_work') }}
                                </button>
                            </form>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Team Locations --}}
    @if($showTeamMap)
        <div class="card shadow-sm mb-4">
            <div class="card-body px-4 py-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h5 class="fw-semibold mb-1">{{ __('pages.dash_map_title') }}</h5>
                    <p class="text-muted mb-0 small">{{ __('pages.dash_map_lead') }}</p>
                </div>
                <a href="{{ route('checkins.map') }}" class="btn btn-darkblue rounded-3 px-4">
                    <i class="fa-solid fa-map-location-dot me-1"></i> {{ __('pages.dash_open_map') }}
                </a>
            </div>
        </div>
    @endif
@endsection

@if(!empty($showSubscriptionExpiryAlert))
    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var alertEl = document.getElementById('subscriptionExpiryAlert');
        if (!alertEl) return;

        var key = alertEl.getAttribute('data-dismiss-key');
        if (key) {
            try {
                if (localStorage.getItem(key) === '1') {
                    alertEl.remove();
                    return;
                }
            } catch (e) {}
        }

        var dismissBtn = document.getElementById('subscriptionExpiryDismiss');
        if (!dismissBtn) return;

        dismissBtn.addEventListener('click', function () {
            if (key) {
                try { localStorage.setItem(key, '1'); } catch (e) {}
            }
            alertEl.remove();
        });
    });
    </script>
    @endpush
@endif

@if(!empty($showCheckIn) && !empty($canCheckIn))
    @push('scripts')
    <script>
    (function () {
        function wireLocationForm(formId, locationFieldId) {
            const form = document.getElementById(formId);
            if (!form) return;

            form.addEventListener('submit', function (e) {
                if (form.dataset.ready === '1') return;
                e.preventDefault();

                if (!navigator.geolocation) {
                    alert(@json(__('pages.dash_geo_unsupported')));
                    return;
                }

                const button = form.querySelector('button');
                const original = button.innerHTML;
                button.disabled = true;
                button.innerHTML = @json(__('pages.dash_getting_location'));

                navigator.geolocation.getCurrentPosition(
                    function (pos) {
                        document.getElementById(locationFieldId).value = pos.coords.latitude + ',' + pos.coords.longitude;
                        form.dataset.ready = '1';
                        form.submit();
                    },
                    function () {
                        button.disabled = false;
                        button.innerHTML = original;
                        alert(@json(__('pages.dash_geo_denied')));
                    },
                    { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                );
            });
        }

        wireLocationForm('startWorkForm', 'check_in_location');
        wireLocationForm('startBreakForm', 'pause_start_location');
        wireLocationForm('endBreakForm', 'pause_end_location');
        wireLocationForm('endWorkForm', 'check_out_location');
    })();
    </script>
    @endpush
@endif
