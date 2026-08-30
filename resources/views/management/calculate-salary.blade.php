@extends('template.template')

@section('title', 'Kalkulimi i Pagave')

@section('content')
    @php
        $defaultFrom = now()->format('Y-m-d');
        $defaultTo = now()->addDays(30)->format('Y-m-d');
        $selectedFrom = $fromDate ?? request('from_date', $defaultFrom);
        $selectedTo = $toDate ?? request('to_date', $defaultTo);
        $fromLabel = \Carbon\Carbon::parse($selectedFrom)->format('d/m/Y');
        $toLabel = \Carbon\Carbon::parse($selectedTo)->format('d/m/Y');
        $calculations = $calculations ?? collect();
        $summary = $summary ?? [];
        $hasResults = request()->routeIs('calculate') || request()->filled('from_date');
        $employeeCount = (int) ($summary['employee_count'] ?? $calculations->count());
        $payrollTotal = (float) ($summary['payroll_total'] ?? 0);
        $hoursTotal = (float) ($summary['hours_total'] ?? 0);
        $daysTotal = (int) ($summary['days_total'] ?? 0);
        $extraTotal = (float) ($summary['extra_total'] ?? 0);
        $fmtHours = function ($decimal) {
            $totalMinutes = (int) round(((float) $decimal) * 60);
            $hours = intdiv(abs($totalMinutes), 60);
            $minutes = abs($totalMinutes) % 60;
            return sprintf('%d:%02d', $hours, $minutes);
        };
    @endphp

    <div class="salary-calc-hero mb-4">
        <div>
            <small class="d-block txt-lightblue fw-semibold text-uppercase">Financat</small>
            <h3 class="fw-semibold pt-2 mb-1">Kalkulimi i Pagave</h3>
            <p class="text-muted mb-0">Filtro periudhën, shiko përmbledhjen sipas llojit të pagesës (mujore / orë / ditë) dhe ruaj pagat.</p>
        </div>
        @if($hasResults && $calculations->isNotEmpty())
            <form action="{{ route('save.calculated.salaries') }}" method="post" class="m-0">
                @csrf
                <input type="hidden" name="from_date" value="{{ $selectedFrom }}">
                <input type="hidden" name="to_date" value="{{ $selectedTo }}">
                <input type="hidden" name="employee_id" value="{{ request('employee_id', $employeeId ?? '') }}">
                <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Ruaj të gjitha pagat
                </button>
            </form>
        @endif
    </div>

    <div class="salary-calc-filters mb-4">
        <div class="salary-calc-filters-head">
            <div>
                <span class="checkin-kicker">Periudha</span>
                <h5 class="fw-semibold mb-0">{{ $fromLabel }} – {{ $toLabel }}</h5>
            </div>
        </div>
        <form action="{{ route('calculate') }}" method="get" class="row g-3 align-items-end">
            <div class="col-12 col-md-6 col-xl-3">
                <label for="from_date" class="form-label">Nga data</label>
                <input type="date" name="from_date" id="from_date" value="{{ request('from_date', $selectedFrom) }}" class="form-control shadow-sm rounded-3">
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <label for="to_date" class="form-label">Deri me</label>
                <input type="date" name="to_date" id="to_date" value="{{ request('to_date', $selectedTo) }}" class="form-control shadow-sm rounded-3">
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <label for="employee_id" class="form-label">Punëtori</label>
                <select name="employee_id" id="employee_id" class="form-control shadow-sm rounded-3">
                    <option value="">Të gjithë punëtorët</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ (string) request('employee_id', $employeeId ?? '') === (string) $employee->id ? 'selected' : '' }}>
                            {{ $employee->employee }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm w-100">
                    <i class="fa-solid fa-calculator me-1"></i> Kalkulo
                </button>
            </div>
        </form>
    </div>

    @if($hasResults)
        @if($calculations->isNotEmpty())
            <div class="row g-3 mb-4">
                <div class="col-6 col-lg-3">
                    <div class="dashboard-kpi dashboard-kpi--navy">
                        <small>Punonjës</small>
                        <strong>{{ $employeeCount }}</strong>
                        <span>në këtë periudhë</span>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="dashboard-kpi dashboard-kpi--blue">
                        <small>Orë / ditë pune</small>
                        <strong>{{ $fmtHours($hoursTotal) }}</strong>
                        <span>{{ $daysTotal }} ditë të regjistruara</span>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="dashboard-kpi dashboard-kpi--warn">
                        <small>Orë shtesë</small>
                        <strong>€{{ number_format($extraTotal, 2) }}</strong>
                        <span>jashtë + vikend</span>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="dashboard-kpi dashboard-kpi--teal">
                        <small>Totali i pagave</small>
                        <strong>€{{ number_format($payrollTotal, 2) }}</strong>
                        <span>për ruajtje</span>
                    </div>
                </div>
            </div>

            <div class="salary-calc-list">
                <div class="salary-calc-list-head">
                    <div>
                        <span class="checkin-kicker">Lista</span>
                        <h5 class="fw-semibold mb-0">Detajet sipas punonjësit</h5>
                    </div>
                    <span class="salary-calc-count">{{ $employeeCount }} rezultate</span>
                </div>

                @foreach($calculations as $row)
                    @php
                        $payType = $row->pay_type ?? 'monthly';
                        $basePay = (float) ($row->base_pay ?? $row->salary);
                        $rate = (float) ($row->salary ?? 0);
                        $workedDays = (int) ($row->worked_days ?? 0);
                        $overtimeHours = (float) ($row->overtime_hours ?? 0);
                        $overtimeRate = (float) ($row->extra_hours_price ?? 0);
                        $overtimeAmount = $overtimeHours * $overtimeRate;
                        $weekendHours = (float) ($row->weekend_hours ?? 0);
                        $weekendRate = (float) ($row->weekend_hours_price ?? 0);
                        $weekendAmount = $weekendHours * $weekendRate;
                        $initials = collect(preg_split('/\s+/', trim((string) $row->employee)))
                            ->filter()
                            ->take(2)
                            ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
                            ->implode('');
                        $baseEq = match ($payType) {
                            'hourly' => number_format((float) $row->working_hours, 2) . ' orë × €' . number_format($rate, 2),
                            'daily' => $workedDays . ' ditë × €' . number_format($rate, 2),
                            default => 'Paga fikse e kontratës',
                        };
                    @endphp
                    <article class="salary-calc-item">
                        <div class="salary-calc-item-main">
                            <div class="salary-calc-avatar" aria-hidden="true">{{ $initials ?: '?' }}</div>
                            <div class="min-w-0">
                                <strong class="salary-calc-name">{{ $row->employee }}</strong>
                                <div class="salary-calc-meta">
                                    <span>{{ $row->position ?: 'Pa pozitë' }}</span>
                                    <span class="salary-calc-dot"></span>
                                    <span>{{ \App\Support\PayTypes::label($payType) }}</span>
                                    <span class="salary-calc-dot"></span>
                                    <span>
                                        Kontrata {{ \Carbon\Carbon::parse($row->contract_from)->format('d/m/Y') }}
                                        – {{ \Carbon\Carbon::parse($row->contract_to)->format('d/m/Y') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="salary-calc-metrics">
                            <div class="salary-calc-metric">
                                <small>{{ \App\Support\PayTypes::rateLabel($payType) }}</small>
                                <strong>€{{ number_format($basePay, 2) }}</strong>
                                <div class="salary-calc-extra-eq">
                                    <span>{{ $baseEq }}</span>
                                </div>
                            </div>
                            <div class="salary-calc-metric">
                                <small>Orë / ditë</small>
                                <strong>{{ $fmtHours($row->working_hours) }}</strong>
                                <div class="salary-calc-extra-eq">
                                    <span>{{ $workedDays }} ditë</span>
                                </div>
                            </div>
                            <div class="salary-calc-metric salary-calc-metric--extra">
                                <small>Jashtë orarit</small>
                                <strong class="salary-calc-extra-total">€{{ number_format($overtimeAmount, 2) }}</strong>
                                <div class="salary-calc-extra-eq">
                                    <span>{{ number_format($overtimeHours) }} orë</span>
                                    <span class="salary-calc-extra-x">×</span>
                                    <span>€{{ number_format($overtimeRate, 2) }}</span>
                                </div>
                            </div>
                            <div class="salary-calc-metric salary-calc-metric--extra">
                                <small>Vikend</small>
                                <strong class="salary-calc-extra-total">€{{ number_format($weekendAmount, 2) }}</strong>
                                <div class="salary-calc-extra-eq">
                                    <span>{{ number_format($weekendHours) }} orë</span>
                                    <span class="salary-calc-extra-x">×</span>
                                    <span>€{{ number_format($weekendRate, 2) }}</span>
                                </div>
                            </div>
                            <div class="salary-calc-metric salary-calc-metric--total">
                                <small>Totali</small>
                                <strong>€{{ number_format((float) $row->total_pay, 2) }}</strong>
                            </div>
                        </div>
                    </article>
                @endforeach

                @if(method_exists($calculations, 'hasPages') && $calculations->hasPages())
                    <div class="salary-calc-pagination">
                        {{ $calculations->appends(request()->except('cursor'))->links('vendor.pagination.custom') }}
                    </div>
                @endif
            </div>
        @else
            <div class="salary-calc-empty">
                <div class="salary-calc-empty-icon">
                    <i class="fa-regular fa-folder-open"></i>
                </div>
                <h5 class="fw-semibold mb-2">Nuk u gjetën rezultate</h5>
                <p class="text-muted mb-0">Nuk ka punonjës me kontratë valide për periudhën {{ $fromLabel }} – {{ $toLabel }}.</p>
            </div>
        @endif
    @endif
@endsection
