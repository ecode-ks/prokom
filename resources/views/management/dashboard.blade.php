@extends('template.template')

@section('title', 'Dashboard')

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
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-4 px-4">
            <small class="d-block text-muted text-uppercase fw-semibold" style="letter-spacing:.04em;font-size:.72rem;">Dashboard · {{ $todayLabel }}</small>
            <h3 class="fw-semibold mb-1 mt-2">Mirë se erdhët, <span class="txt-lightblue">{{ $displayName }}</span></h3>
            <p class="text-muted mb-0">Pasqyra e ditës sipas rolit tuaj.</p>
        </div>
    </div>

    {{-- KPI: admin --}}
    @if(!empty($isAdmin) && $adminKpis)
        <div class="row g-3 mb-3">
            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted text-uppercase d-block mb-2">Kompanitë</small>
                        <h4 class="fw-semibold mb-0">{{ number_format($adminKpis->companies) }}</h4>
                        <small class="text-muted">aktive: {{ number_format($adminKpis->active_companies) }}</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted text-uppercase d-block mb-2">Abonim skadon së shpejti</small>
                        <h4 class="fw-semibold mb-0">{{ number_format($adminKpis->expiring_soon) }}</h4>
                        <small class="text-muted">brenda 30 ditëve</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted text-uppercase d-block mb-2">Paketat</small>
                        <h4 class="fw-semibold mb-0">{{ number_format($adminKpis->packages) }}</h4>
                        <small class="text-muted">jo aktive: {{ number_format($adminKpis->inactive_companies) }}</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted text-uppercase d-block mb-2">Të ardhura (muaji)</small>
                        <h4 class="fw-semibold mb-0">€{{ number_format($adminKpis->revenue_month, 2) }}</h4>
                        <small class="text-muted">{{ number_format($adminKpis->payments_month) }} pagesa</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 pt-3 px-4 d-flex justify-content-between align-items-center">
                        <span class="fw-semibold"><i class="fa-solid fa-building txt-lightblue me-1"></i> Kompanitë e fundit</span>
                        <a href="{{ route('companies') }}" class="dashboard-link">Shiko</a>
                    </div>
                    <div class="card-body px-4 pb-3 pt-0">
                        @if(($adminLatestCompanies ?? collect())->isEmpty())
                            <p class="text-muted mb-0 small">Nuk ka kompani.</p>
                        @else
                            @foreach($adminLatestCompanies as $company)
                                <a href="{{ route('company.details', $company->id) }}" class="dashboard-task-item text-decoration-none">
                                    <div class="min-w-0">
                                        <strong class="d-block text-truncate">{{ $company->company }}</strong>
                                        <small class="text-muted">{{ $company->package ?: 'Pa paketë' }} · {{ $company->status }}</small>
                                    </div>
                                    <span class="small text-muted text-nowrap">
                                        {{ $company->payment_expire ? \Carbon\Carbon::parse($company->payment_expire)->format('d/m/Y') : '-' }}
                                    </span>
                                </a>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 pt-3 px-4 d-flex justify-content-between align-items-center">
                        <span class="fw-semibold"><i class="fa-solid fa-hourglass-half txt-lightblue me-1"></i> Abonime që skadojnë</span>
                        <a href="{{ route('companies') }}" class="dashboard-link">Shiko</a>
                    </div>
                    <div class="card-body px-4 pb-3 pt-0">
                        @if(($adminExpiringCompanies ?? collect())->isEmpty())
                            <p class="text-muted mb-0 small">Nuk ka abonime që skadojnë së shpejti.</p>
                        @else
                            @foreach($adminExpiringCompanies as $company)
                                <a href="{{ route('company.details', $company->id) }}" class="dashboard-task-item text-decoration-none">
                                    <div class="min-w-0">
                                        <strong class="d-block text-truncate">{{ $company->company }}</strong>
                                        <small class="text-muted">{{ $company->package ?: 'Pa paketë' }}</small>
                                    </div>
                                    <span class="dashboard-deadline-label small text-nowrap fw-semibold is-soon">
                                        {{ \Carbon\Carbon::parse($company->payment_expire)->format('d/m/Y') }}
                                    </span>
                                </a>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 pt-3 px-4 d-flex justify-content-between align-items-center">
                <span class="fw-semibold"><i class="fa-solid fa-money-bill txt-lightblue me-1"></i> Pagesat e fundit</span>
                <a href="{{ route('payments') }}" class="dashboard-link">Shiko</a>
            </div>
            <div class="card-body px-4 pb-3 pt-0">
                @if(($adminLatestPayments ?? collect())->isEmpty())
                    <p class="text-muted mb-0 small">Nuk ka pagesa.</p>
                @else
                    @foreach($adminLatestPayments as $payment)
                        <div class="dashboard-task-item">
                            <div class="min-w-0">
                                <strong class="d-block text-truncate">{{ $payment->company ?: 'Kompani' }}</strong>
                                <small class="text-muted">
                                    {{ $payment->date ? \Carbon\Carbon::parse($payment->date)->format('d/m/Y') : '-' }}
                                    · {{ $payment->package }}
                                </small>
                            </div>
                            <span class="small text-nowrap fw-semibold">€{{ number_format((float) $payment->price, 2) }}</span>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    @endif

    @if($isCompany)
        <div class="alert alert-primary shadow-sm rounded-4" role="alert">
            <span>Pakoja juaj e abonimit skadon me datën <strong>{{ session('expire_date') ? \Carbon\Carbon::parse(session('expire_date'))->format('d/m/Y') : '--/--/----' }}.</strong> Mund të riabonoheni <a href="{{ route('profile') }}" class="text-decoration-none txt-darkblue fw-bold">këtu</a>.</span>
        </div>
    @endif

    {{-- Lajme --}}
    @if(in_array($role, ['company', 'manager', 'employee', 'finance', 'hr'], true))
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 pt-3 px-4 d-flex justify-content-between align-items-center">
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

    {{-- KPI: company --}}
    @if($isCompany && $companyKpis)
        <div class="row g-3 mb-3">
            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted text-uppercase d-block mb-2">Bilanci (muaji)</small>
                        <h4 class="fw-semibold mb-0">€{{ number_format($companyKpis->balance, 2) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted text-uppercase d-block mb-2">Të ardhura</small>
                        <h4 class="fw-semibold mb-0">€{{ number_format($companyKpis->revenues, 2) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted text-uppercase d-block mb-2">Shpenzime</small>
                        <h4 class="fw-semibold mb-0">€{{ number_format($companyKpis->expenses, 2) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted text-uppercase d-block mb-2">Oferta në pritje</small>
                        <h4 class="fw-semibold mb-0">{{ number_format($companyKpis->pending_offers) }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="row g-3 mb-4">
            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted text-uppercase d-block mb-2">Punonjës</small>
                        <h4 class="fw-semibold mb-0">{{ number_format($companyKpis->employees) }}</h4>
                        <small class="text-muted">aktive: {{ number_format($companyKpis->active_employees) }}</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted text-uppercase d-block mb-2">Prezenca sot</small>
                        <h4 class="fw-semibold mb-0">{{ number_format($companyKpis->checked_in_today) }}</h4>
                        <small class="text-muted">në punë: {{ number_format($companyKpis->currently_working) }}</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted text-uppercase d-block mb-2">Pushime në pritje</small>
                        <h4 class="fw-semibold mb-0">{{ number_format($companyKpis->pending_vacations) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted text-uppercase d-block mb-2">Kontrata që skadojnë</small>
                        <h4 class="fw-semibold mb-0">{{ number_format($companyKpis->expiring_contracts) }}</h4>
                        <small class="text-muted">projekte: {{ number_format($companyKpis->projects) }}</small>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- KPI: manager --}}
    @if($isManager && $managerKpis)
        <div class="row g-3 mb-4">
            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted text-uppercase d-block mb-2">Projektet e mia</small>
                        <h4 class="fw-semibold mb-0">{{ number_format($managerKpis->active_projects) }}</h4>
                        <small class="text-muted">aktive</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted text-uppercase d-block mb-2">Detyra të hapura</small>
                        <h4 class="fw-semibold mb-0">{{ number_format($managerKpis->open_tasks) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted text-uppercase d-block mb-2">Me vonesë</small>
                        <h4 class="fw-semibold mb-0">{{ number_format($managerKpis->overdue_tasks) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted text-uppercase d-block mb-2">Këtë javë</small>
                        <h4 class="fw-semibold mb-0">{{ number_format($managerKpis->due_soon_tasks) }}</h4>
                        <small class="text-muted">afate ≤ 7 ditë</small>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- KPI: employee --}}
    @if($isEmployee && $employeeKpis)
        <div class="row g-3 mb-4">
            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted text-uppercase d-block mb-2">Detyrat e mia</small>
                        <h4 class="fw-semibold mb-0">{{ number_format($employeeKpis->my_tasks) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted text-uppercase d-block mb-2">Në punë</small>
                        <h4 class="fw-semibold mb-0">{{ number_format($employeeKpis->working_tasks) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted text-uppercase d-block mb-2">Në shqyrtim</small>
                        <h4 class="fw-semibold mb-0">{{ number_format($employeeKpis->review_tasks) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted text-uppercase d-block mb-2">Raportet e mia</small>
                        <h4 class="fw-semibold mb-0">{{ number_format($employeeKpis->my_reports) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- KPI: finance --}}
    @if($isFinance && $financeKpis)
        <div class="row g-3 mb-4">
            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted text-uppercase d-block mb-2">Bilanci (muaji)</small>
                        <h4 class="fw-semibold mb-0">€{{ number_format($financeKpis->balance, 2) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted text-uppercase d-block mb-2">Të ardhurat</small>
                        <h4 class="fw-semibold mb-0">€{{ number_format($financeKpis->revenues, 2) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted text-uppercase d-block mb-2">Shpenzimet</small>
                        <h4 class="fw-semibold mb-0">€{{ number_format($financeKpis->expenses, 2) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted text-uppercase d-block mb-2">Oferta në pritje</small>
                        <h4 class="fw-semibold mb-0">{{ number_format($financeKpis->pending_offers) }}</h4>
                        <small class="text-muted">{{ number_format($financeKpis->clients) }} klientë</small>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- KPI: hr --}}
    @if($isHr && $hrKpis)
        <div class="row g-3 mb-4">
            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted text-uppercase d-block mb-2">Punonjësit</small>
                        <h4 class="fw-semibold mb-0">{{ number_format($hrKpis->employees) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted text-uppercase d-block mb-2">Aktivë</small>
                        <h4 class="fw-semibold mb-0">{{ number_format($hrKpis->active_employees) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted text-uppercase d-block mb-2">Pushime HR</small>
                        <h4 class="fw-semibold mb-0">{{ number_format($hrKpis->pending_vacations) }}</h4>
                        <small class="text-muted">në pritje</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted text-uppercase d-block mb-2">Kontrata</small>
                        <h4 class="fw-semibold mb-0">{{ number_format($hrKpis->active_contracts) }}</h4>
                        <small class="text-muted">aktive</small>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Lista: company --}}
    @if($isCompany)
        <div class="row g-3 mb-4">
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 pt-3 px-4 d-flex justify-content-between align-items-center">
                        <span class="fw-semibold"><i class="fa-solid fa-diagram-project txt-lightblue me-1"></i> Afatet e projekteve</span>
                        <a href="{{ route('projects') }}" class="dashboard-link">Shiko</a>
                    </div>
                    <div class="card-body px-4 pb-3 pt-0">
                        @if(($companyProjects ?? collect())->isEmpty())
                            <p class="text-muted mb-0 small">Nuk ka projekte me afat të afërt.</p>
                        @else
                            @foreach($companyProjects as $project)
                                @php
                                    $days = \Carbon\Carbon::parse($project->expected_end_date)->startOfDay()->diffInDays(now()->startOfDay(), false);
                                    $daysLeft = (int) (-$days);
                                    $label = $daysLeft < 0 ? abs($daysLeft) . ' ditë vonesë' : ($daysLeft === 0 ? 'Sot' : $daysLeft . ' ditë');
                                    $urgency = $daysLeft < 0 ? 'is-overdue' : ($daysLeft <= 7 ? 'is-soon' : 'is-ok');
                                @endphp
                                <a href="{{ route('project.details', $project->id) }}" class="dashboard-task-item text-decoration-none">
                                    <div class="min-w-0">
                                        <strong class="d-block text-truncate">{{ $project->project_name }}</strong>
                                        <small class="text-muted">{{ $project->status }}</small>
                                    </div>
                                    <span class="dashboard-deadline-label small text-nowrap fw-semibold {{ $urgency }}">{{ $label }}</span>
                                </a>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 pt-3 px-4 d-flex justify-content-between align-items-center">
                        <span class="fw-semibold"><i class="fa-solid fa-money-bill-transfer txt-lightblue me-1"></i> Transaksionet e fundit</span>
                        <a href="{{ route('financial.transactions') }}" class="dashboard-link">Shiko</a>
                    </div>
                    <div class="card-body px-4 pb-3 pt-0">
                        @if(($companyLatestTransactions ?? collect())->isEmpty())
                            <p class="text-muted mb-0 small">Nuk ka transaksione.</p>
                        @else
                            @foreach($companyLatestTransactions as $tx)
                                <div class="dashboard-task-item">
                                    <div class="min-w-0">
                                        <strong class="d-block text-truncate">{{ $tx->comment ?: 'Pa koment' }}</strong>
                                        <small class="text-muted">{{ $tx->date ? \Carbon\Carbon::parse($tx->date)->format('d/m/Y') : '-' }} · {{ $tx->type === 'revenue' ? 'Të ardhura' : 'Shpenzim' }}</small>
                                    </div>
                                    <span class="small text-nowrap fw-semibold {{ $tx->type === 'revenue' ? 'text-success' : 'text-danger' }}">
                                        {{ $tx->type === 'revenue' ? '+' : '-' }}€{{ number_format((float) $tx->amount, 2) }}
                                    </span>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 pt-3 px-4 d-flex justify-content-between align-items-center">
                        <span class="fw-semibold"><i class="fa-solid fa-file-invoice txt-lightblue me-1"></i> Ofertat e fundit</span>
                        <a href="{{ route('offers') }}" class="dashboard-link">Shiko</a>
                    </div>
                    <div class="card-body px-4 pb-3 pt-0">
                        @if(($companyLatestOffers ?? collect())->isEmpty())
                            <p class="text-muted mb-0 small">Nuk ka oferta.</p>
                        @else
                            @foreach($companyLatestOffers as $offer)
                                <a href="{{ route('offers') }}" class="dashboard-task-item text-decoration-none">
                                    <div class="min-w-0">
                                        <strong class="d-block text-truncate">{{ $offer->client ?: $offer->offer_number }}</strong>
                                        <small class="text-muted">{{ $offer->offer_number }} · {{ $offer->status }}</small>
                                    </div>
                                    <span class="small text-nowrap fw-semibold">€{{ number_format((float) $offer->total_price, 2) }}</span>
                                </a>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 pt-3 px-4 d-flex justify-content-between align-items-center">
                        <span class="fw-semibold"><i class="fa-solid fa-umbrella-beach txt-lightblue me-1"></i> Pushime në pritje</span>
                        <a href="{{ route('vacations') }}" class="dashboard-link">Shiko</a>
                    </div>
                    <div class="card-body px-4 pb-3 pt-0">
                        @if(($companyPendingVacations ?? collect())->isEmpty())
                            <p class="text-muted mb-0 small">Nuk ka pushime në pritje.</p>
                        @else
                            @foreach($companyPendingVacations as $vacation)
                                <a href="{{ route('vacations') }}" class="dashboard-task-item text-decoration-none">
                                    <div class="min-w-0">
                                        <strong class="d-block text-truncate">{{ $vacation->employee }}</strong>
                                        <small class="text-muted">{{ $vacation->type }} · {{ $vacation->status }}</small>
                                    </div>
                                    <span class="small text-muted text-nowrap">
                                        {{ \Carbon\Carbon::parse($vacation->from_date)->format('d/m') }}–{{ \Carbon\Carbon::parse($vacation->to_date)->format('d/m') }}
                                    </span>
                                </a>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if(($companyExpiringContracts ?? collect())->isNotEmpty())
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 pt-3 px-4 d-flex justify-content-between align-items-center">
                    <span class="fw-semibold"><i class="fa-solid fa-file-contract txt-lightblue me-1"></i> Kontrata që skadojnë</span>
                    <a href="{{ route('contracts') }}" class="dashboard-link">Shiko</a>
                </div>
                <div class="card-body px-4 pb-3 pt-0">
                    @foreach($companyExpiringContracts as $contract)
                        <a href="{{ route('contracts') }}" class="dashboard-task-item text-decoration-none">
                            <div class="min-w-0">
                                <strong class="d-block text-truncate">{{ $contract->employee }}</strong>
                                <small class="text-muted">{{ $contract->position ?: 'Pa pozicion' }}</small>
                            </div>
                            <span class="small text-nowrap fw-semibold is-soon">{{ \Carbon\Carbon::parse($contract->to_date)->format('d/m/Y') }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    @endif

    {{-- Lista: manager --}}
    @if($isManager)
        <div class="row g-3 mb-4">
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 pt-3 px-4 d-flex justify-content-between align-items-center">
                        <span class="fw-semibold"><i class="fa-solid fa-diagram-project txt-lightblue me-1"></i> Afatet e projekteve</span>
                        <a href="{{ route('projects') }}" class="dashboard-link">Shiko</a>
                    </div>
                    <div class="card-body px-4 pb-3 pt-0">
                        @if(($managerProjects ?? collect())->isEmpty())
                            <p class="text-muted mb-0 small">Nuk ka projekte me afat të afërt.</p>
                        @else
                            @foreach($managerProjects as $project)
                                @php
                                    $days = \Carbon\Carbon::parse($project->expected_end_date)->startOfDay()->diffInDays(now()->startOfDay(), false);
                                    $daysLeft = (int) (-$days);
                                    $label = $daysLeft < 0 ? abs($daysLeft) . ' ditë vonesë' : ($daysLeft === 0 ? 'Sot' : $daysLeft . ' ditë');
                                    $urgency = $daysLeft < 0 ? 'is-overdue' : ($daysLeft <= 7 ? 'is-soon' : 'is-ok');
                                @endphp
                                <a href="{{ route('project.details', $project->id) }}" class="dashboard-task-item text-decoration-none">
                                    <div class="min-w-0">
                                        <strong class="d-block text-truncate">{{ $project->project_name }}</strong>
                                        <small class="text-muted">{{ $project->status }}</small>
                                    </div>
                                    <span class="dashboard-deadline-label small text-nowrap fw-semibold {{ $urgency }}">{{ $label }}</span>
                                </a>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 pt-3 px-4 d-flex justify-content-between align-items-center">
                        <div>
                            <span class="fw-semibold d-block"><i class="fa-solid fa-list-check txt-lightblue me-1"></i> Detyrat e ekipit</span>
                            @if($managerTeamPresence)
                                <small class="text-muted">Prezenca sot: {{ $managerTeamPresence->checked_in_today }}/{{ $managerTeamPresence->team_size }}</small>
                            @endif
                        </div>
                        <a href="{{ route('tasks') }}" class="dashboard-link">Shiko</a>
                    </div>
                    <div class="card-body px-4 pb-3 pt-0">
                        @if(($managerTeamTasks ?? collect())->isEmpty())
                            <p class="text-muted mb-0 small">Nuk ka detyra të hapura.</p>
                        @else
                            @foreach($managerTeamTasks as $task)
                                <a href="{{ route('task.details', $task->id) }}" class="dashboard-task-item text-decoration-none">
                                    <div class="min-w-0">
                                        <strong class="d-block text-truncate">{{ $task->title ?: 'Pa titull' }}</strong>
                                        <small class="text-muted">{{ $task->project_name ?? '-' }}</small>
                                    </div>
                                    <span class="small text-muted text-nowrap">{{ $task->deadline ? \Carbon\Carbon::parse($task->deadline)->format('d/m/Y') : '-' }}</span>
                                </a>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if(($managerPendingReports ?? collect())->isNotEmpty())
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 pt-3 px-4 d-flex justify-content-between align-items-center">
                    <span class="fw-semibold"><i class="fa-solid fa-clipboard-list txt-lightblue me-1"></i> Raporte në pritje</span>
                    <a href="{{ route('project.reports') }}" class="dashboard-link">Shiko</a>
                </div>
                <div class="card-body px-4 pb-3 pt-0">
                    @foreach($managerPendingReports as $report)
                        <a href="{{ route('project.report.details', $report->id) }}" class="dashboard-task-item text-decoration-none">
                            <div class="min-w-0">
                                <strong class="d-block text-truncate">{{ $report->project_name }}</strong>
                                <small class="text-muted">{{ $report->reporter_name ?: 'Punonjës' }}</small>
                            </div>
                            <span class="small text-muted text-nowrap">{{ $report->date ? \Carbon\Carbon::parse($report->date)->format('d/m H:i') : '-' }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    @endif

    {{-- Lista: employee --}}
    @if($isEmployee)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 pt-3 px-4 d-flex justify-content-between align-items-center">
                <span class="fw-semibold"><i class="fa-solid fa-calendar-days txt-lightblue me-1"></i> Afate që afrohen</span>
                <a href="{{ route('tasks') }}" class="dashboard-link">Shiko të gjitha</a>
            </div>
            <div class="card-body px-4 pb-3 pt-0">
                @if(($employeeTasks ?? collect())->isEmpty())
                    <p class="text-muted mb-0 small">Nuk ka detyra të hapura.</p>
                @else
                    @foreach($employeeTasks as $task)
                        <a href="{{ route('task.details', $task->id) }}" class="dashboard-task-item text-decoration-none">
                            <div class="min-w-0">
                                <strong class="d-block text-truncate">{{ $task->title ?: 'Pa titull' }}</strong>
                                <small class="text-muted">{{ $task->project_name ?? '-' }}</small>
                            </div>
                            <span class="small text-muted text-nowrap">{{ $task->deadline ? \Carbon\Carbon::parse($task->deadline)->format('d/m/Y') : '-' }}</span>
                        </a>
                    @endforeach
                @endif
            </div>
        </div>
    @endif

    {{-- Check-in kompakt: employee / manager / finance / hr --}}
    @if(!empty($showCheckIn) && !empty($canCheckIn))
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 pt-3 px-4 d-flex justify-content-between align-items-center">
                <span class="fw-semibold"><i class="fa-solid fa-user-check txt-lightblue me-1"></i> Check-in</span>
                <span class="checkin-status {{ !$isActive ? 'is-idle' : ($onPause ? 'is-pause' : 'is-work') }}">{{ $checkInStatus }}</span>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="row g-2 mb-3">
                    <div class="col-6 col-md-3">
                        <div class="border rounded-3 p-3 h-100">
                            <small class="text-muted d-block">Check-in</small>
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
                            <small class="text-muted d-block">Check-out</small>
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

    {{-- Harta ekipi: company --}}
    @if($showTeamMap)
        <div class="card border-0 shadow-sm mb-4">
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

    {{-- Quick actions --}}
    @if($showQuickActions)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 pt-3 px-4">
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
                        <a href="{{ route('financial.transactions') }}" class="dashboard-quick-btn"><i class="fa-solid fa-wallet"></i> Transaksionet</a>
                        <a href="{{ route('checkins') }}" class="dashboard-quick-btn"><i class="fa-solid fa-user-check"></i> Prezenca</a>
                    @elseif($isManager)
                        <a href="{{ route('tasks') }}" class="dashboard-quick-btn"><i class="fa-solid fa-plus"></i> Cakto detyrë</a>
                        <a href="{{ route('projects') }}" class="dashboard-quick-btn"><i class="fa-solid fa-diagram-project"></i> Projektet</a>
                        <a href="{{ route('project.reports') }}" class="dashboard-quick-btn"><i class="fa-solid fa-clipboard-list"></i> Raportet</a>
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
