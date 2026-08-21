@extends('template.template')

{{-- Page Title --}}
@section('title', 'Dashboard')

{{-- Page Content --}}
@section('content')
    @php
        $fmt = function ($value) {
            return $value ? \Carbon\Carbon::parse($value)->format('H:i') : '--:--';
        };
        $onPause = !empty($isActive) && !empty($checkIn) && $checkIn->pause_start_time && !$checkIn->pause_end_time;
        $checkInStatus = empty($checkIn)
            ? 'Jo aktiv'
            : (empty($isActive) ? 'Përfunduar' : ($onPause ? 'Në pauzë' : 'Në punë'));
    @endphp

    {{-- Hero --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body p-4 rounded-4" style="background: linear-gradient(90deg, rgba(13,110,253,0.08), rgba(13,110,253,0.02));">
            <small class="d-block text-muted text-uppercase fw-semibold" style="letter-spacing:.04em;font-size:.72rem;">Dashboard · {{ $todayLabel }}</small>
            <h3 class="fw-semibold mb-1 mt-2">Mirë se erdhët, <span class="txt-lightblue">{{ $displayName }}</span></h3>
            <p class="text-muted mb-0">Pasqyra e ditës sipas rolit tuaj.</p>
        </div>
    </div>

    {{-- Package Alerts --}}
    @if($isCompany)
        <div class="alert alert-primary shadow-sm rounded-4 mb-4" role="alert">
            <span>Pakoja juaj e abonimit skadon me datën <strong>{{ session('expire_date') ? \Carbon\Carbon::parse(session('expire_date'))->format('d/m/Y') : '--/--/----' }}.</strong> Mund të riabonoheni <a href="{{ route('profile') }}" class="text-decoration-none txt-darkblue fw-bold">këtu</a>.</span>
        </div>
    @endif

    {{-- Quick actions --}}
    @if($showQuickActions)
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white rounded-4 py-3">
                <span class="fw-semibold"><i class="fa-solid fa-bolt txt-lightblue me-1"></i> Veprime të shpejta</span>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="dashboard-quick-actions">
                    @if(!empty($isAdmin))
                        <a href="{{ route('companies') }}" class="dashboard-quick-btn"><i class="fa-solid fa-building"></i> Kompanitë</a>
                        <a href="{{ route('packages') }}" class="dashboard-quick-btn"><i class="fa-brands fa-trello"></i> Paketat</a>
                        <a href="{{ route('payments') }}" class="dashboard-quick-btn"><i class="fa-solid fa-money-bill"></i> Pagesat</a>
                    @elseif($isCompany)
                        <a href="{{ route('projects') }}" class="dashboard-quick-btn"><i class="fa-solid fa-diagram-project"></i> Projektet</a>
                        <a href="{{ route('employees') }}" class="dashboard-quick-btn"><i class="fa-solid fa-users"></i> Punëtorët</a>
                        <a href="{{ route('offers') }}" class="dashboard-quick-btn"><i class="fa-solid fa-file-invoice-dollar"></i> Ofertat</a>
                        <a href="{{ route('financial.transactions') }}" class="dashboard-quick-btn"><i class="fa-solid fa-wallet"></i> Transaksionet</a>
                        <a href="{{ route('checkins') }}" class="dashboard-quick-btn"><i class="fa-solid fa-user-check"></i> Prezenca</a>
                    @elseif($isManager)
                        <a href="{{ route('tasks') }}" class="dashboard-quick-btn"><i class="fa-solid fa-plus"></i> Cakto detyrë</a>
                        <a href="{{ route('projects') }}" class="dashboard-quick-btn"><i class="fa-solid fa-diagram-project"></i> Projektet</a>
                        <a href="{{ route('project.reports') }}" class="dashboard-quick-btn"><i class="fa-solid fa-clipboard-list"></i> Raportet</a>
                    @elseif($isEmployee)
                        <a href="{{ route('tasks') }}" class="dashboard-quick-btn"><i class="fa-solid fa-list-check"></i> Detyrat</a>
                        <a href="{{ route('task.reports') }}" class="dashboard-quick-btn"><i class="fa-solid fa-file-lines"></i> Raportet e detyrave</a>
                        <a href="{{ route('working.hours') }}" class="dashboard-quick-btn"><i class="fa-solid fa-clock"></i> Orët e punës</a>
                    @elseif($isFinance)
                        <a href="{{ route('financial.transactions') }}" class="dashboard-quick-btn"><i class="fa-solid fa-wallet"></i> Transaksionet</a>
                        <a href="{{ route('financial.report') }}" class="dashboard-quick-btn"><i class="fa-solid fa-chart-pie"></i> Raporti financiar</a>
                        <a href="{{ route('offers') }}" class="dashboard-quick-btn"><i class="fa-solid fa-file-invoice-dollar"></i> Ofertat</a>
                        <a href="{{ route('calculate.salary') }}" class="dashboard-quick-btn"><i class="fa-solid fa-calculator"></i> Kalkulo pagat</a>
                    @elseif($isHr)
                        <a href="{{ route('employees') }}" class="dashboard-quick-btn"><i class="fa-solid fa-users"></i> Punëtorët</a>
                        <a href="{{ route('checkins') }}" class="dashboard-quick-btn"><i class="fa-solid fa-user-check"></i> Prezenca</a>
                        <a href="{{ route('vacations') }}" class="dashboard-quick-btn"><i class="fa-solid fa-calendar-days"></i> Pushimet</a>
                        <a href="{{ route('contracts') }}" class="dashboard-quick-btn"><i class="fa-solid fa-file-contract"></i> Kontratat</a>
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
                        <small class="text-muted text-uppercase d-block mb-2">Punëtorë</small>
                        <h4 class="fw-semibold mb-0">{{ number_format($companyKpis->employees) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted text-uppercase d-block mb-2">Projekte</small>
                        <h4 class="fw-semibold mb-0">{{ number_format($companyKpis->projects) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted text-uppercase d-block mb-2">Klientë</small>
                        <h4 class="fw-semibold mb-0">{{ number_format($companyKpis->clients) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted text-uppercase d-block mb-2">Oferta</small>
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
                <span class="fw-semibold"><i class="fa-regular fa-newspaper txt-lightblue me-1"></i> Lajmet e kompanisë</span>
                @if($isCompany || $isManager)
                    <a href="{{ route('company.news') }}" class="dashboard-link">Shiko të gjitha</a>
                @endif
            </div>
            <div class="card-body px-4 pb-3 pt-0">
                @if(($dashboardNews ?? collect())->isEmpty())
                    <p class="text-muted mb-0 small">Nuk ka lajme për momentin.</p>
                @else
                    @foreach($dashboardNews as $news)
                        <div class="@if(!$loop->last) border-bottom @endif py-3">
                            <small class="text-muted d-block mb-1">
                                {{ $news->date ? \Carbon\Carbon::parse($news->date)->format('d/m/Y') : '-' }}
                                @if(!empty($news->project_id))
                                    · {{ $news->project_name ?? ('Projekt #' . $news->project_id) }}
                                @endif
                            </small>
                            <div class="text-dark">{!! nl2br(e(\Illuminate\Support\Str::limit($news->info, 220))) !!}</div>
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
                <span class="fw-semibold"><i class="fa-solid fa-user-check txt-lightblue me-1"></i> Check-in</span>
                <span class="checkin-status {{ !$isActive ? 'is-idle' : ($onPause ? 'is-pause' : 'is-work') }}">{{ $checkInStatus }}</span>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="row g-2 mb-3">
                    <div class="col-6 col-md-3">
                        <div class="border rounded-3 p-3 h-100">
                            <small class="text-muted d-block">Hyrja</small>
                            <strong>{{ $fmt($checkIn->check_in_time ?? null) }}</strong>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="border rounded-3 p-3 h-100">
                            <small class="text-muted d-block">Fillimi pauzës</small>
                            <strong>{{ $fmt($checkIn->pause_start_time ?? null) }}</strong>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="border rounded-3 p-3 h-100">
                            <small class="text-muted d-block">Mbarimi pauzës</small>
                            <strong>{{ $fmt($checkIn->pause_end_time ?? null) }}</strong>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="border rounded-3 p-3 h-100">
                            <small class="text-muted d-block">Dalja</small>
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
                                <i class="fa-solid fa-play me-1"></i> Starto Punën
                            </button>
                        </form>
                    @else
                        @if(!$checkIn->pause_start_time)
                            <form action="{{ route('start.break') }}" method="post" id="startBreakForm">
                                @csrf
                                <input type="hidden" name="id" value="{{ $checkIn->id }}">
                                <input type="hidden" name="pause_start_location" id="pause_start_location">
                                <button type="submit" class="btn btn-outline-secondary rounded-3 px-4 py-2 fw-semibold">
                                    <i class="fa-solid fa-mug-saucer me-1"></i> Starto Pauzën
                                </button>
                            </form>
                        @elseif(!$checkIn->pause_end_time)
                            <form action="{{ route('end.break') }}" method="post" id="endBreakForm">
                                @csrf
                                <input type="hidden" name="id" value="{{ $checkIn->id }}">
                                <input type="hidden" name="pause_end_location" id="pause_end_location">
                                <button type="submit" class="btn btn-outline-secondary rounded-3 px-4 py-2 fw-semibold">
                                    <i class="fa-solid fa-play me-1"></i> Përfundo Pauzën
                                </button>
                            </form>
                        @endif

                        @if(!$checkIn->pause_start_time || $checkIn->pause_end_time)
                            <form action="{{ route('end.work') }}" method="post" id="endWorkForm">
                                @csrf
                                <input type="hidden" name="id" value="{{ $checkIn->id }}">
                                <input type="hidden" name="check_out_location" id="check_out_location">
                                <button type="submit" class="btn btn-darkblue rounded-3 px-4 py-2 fw-semibold">
                                    <i class="fa-solid fa-stop me-1"></i> Përfundo Punën
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
                    <h5 class="fw-semibold mb-1">Harta e prezencës</h5>
                    <p class="text-muted mb-0 small">Shiko lokacionet e punonjësve aktivë sot.</p>
                </div>
                <a href="{{ route('checkins.map') }}" class="btn btn-darkblue rounded-3 px-4">
                    <i class="fa-solid fa-map-location-dot me-1"></i> Hap hartën
                </a>
            </div>
        </div>
    @endif
@endsection

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
                    alert('Ky shfletues nuk e mbështet marrjen e lokacionit.');
                    return;
                }

                const button = form.querySelector('button');
                const original = button.innerHTML;
                button.disabled = true;
                button.innerHTML = 'Duke marrë lokacionin...';

                navigator.geolocation.getCurrentPosition(
                    function (pos) {
                        document.getElementById(locationFieldId).value = pos.coords.latitude + ',' + pos.coords.longitude;
                        form.dataset.ready = '1';
                        form.submit();
                    },
                    function () {
                        button.disabled = false;
                        button.innerHTML = original;
                        alert('Duhet të lejoni qasjen në lokacion.');
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
