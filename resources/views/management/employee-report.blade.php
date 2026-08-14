@extends('template.template')

@section('title', 'Raporti i Punëtorëve')

@section('content')
    @php
        $selectedFromDate = $fromDate ?? null;
        $selectedToDate = $toDate ?? null;
        $defaultFrom = $selectedFromDate ?? now()->format('Y-m-d');
        $defaultTo = $selectedToDate ?? now()->addDays(30)->format('Y-m-d');
        $fromLabel = $selectedFromDate ? \Carbon\Carbon::parse($selectedFromDate)->format('d/m/Y') : now()->format('d/m/Y');
        $toLabel = $selectedToDate ? \Carbon\Carbon::parse($selectedToDate)->format('d/m/Y') : now()->addDays(30)->format('d/m/Y');
        $selectedEmployee = collect($employees ?? [])->firstWhere('id', (int) ($employeeId ?? 0));

        $vacations = $reportData['vacations'] ?? null;
        $contracts = $reportData['contracts'] ?? null;
        $workingHours = $reportData['working_hours'] ?? null;
        $extraHours = $reportData['extra_hours'] ?? null;
        $extraWeekendHours = $reportData['extra_weekend_hours'] ?? null;
        $salary = $reportData['salary'] ?? [];
        $tasks = $reportData['tasks'] ?? null;

        $extraHoursValueEur = (float) ($reportData['extra_hours_value_eur'] ?? 0);
        $extraWeekendHoursValueEur = (float) ($reportData['extra_weekend_hours_value_eur'] ?? 0);

        $totalExtraHours = (float) ($extraHours->total_hours ?? 0) + (float) ($extraWeekendHours->total_hours ?? 0);
        $approvedVacationRate = (int) ($vacations->total_count ?? 0) > 0
            ? (((int) ($vacations->approved_count ?? 0) / (int) ($vacations->total_count ?? 0)) * 100)
            : 0;
        $totalTaskCount = (int) ($tasks->total_count ?? 0);
        $completedTaskCount = (int) ($tasks->completed_count ?? 0);
        $uncompletedTaskCount = (int) ($tasks->uncompleted_count ?? max(0, $totalTaskCount - $completedTaskCount));

        $salaryTotal = (float) ($salary['payroll_total'] ?? 0);
        $extraTotalEur = $extraHoursValueEur + $extraWeekendHoursValueEur;
        $grandTotalEur = $salaryTotal + $extraTotalEur;
    @endphp

    <small class="d-block txt-lightblue fw-semibold text-uppercase">Punëtorët</small>
    <h3 class="fw-semibold pt-2">Raporti i Punëtorëve</h3>
    <p class="text-muted">Filtro periudhën dhe shiko përmbledhjen e punëtorëve.</p>
    <hr class="mb-4">

    <div class="card mb-4">
        <div class="card-header rounded-4 bg-white">
            <span><i class="fa-solid fa-calendar-days txt-lightblue py-2"></i> Periudha <strong>{{ $fromLabel }} – {{ $toLabel }}</strong></span>
        </div>
        <div class="card-body">
            <form action="{{ route('generate.employee.report') }}" method="get">
                <div class="row g-3">
                    <div class="col-12 col-md-4 col-lg-4">
                        <label for="employee_id" class="form-label">Punëtori</label>
                        <select name="employee_id" id="employee_id" class="form-control shadow-sm rounded-3">
                            <option value="">-- Zgjidh Punëtorin --</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ (string) request('employee_id', $employeeId ?? '') === (string) $employee->id ? 'selected' : '' }}>
                                    {{ $employee->employee }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-4 col-lg-4">
                        <label for="from_date" class="form-label">Nga data</label>
                        <input type="date" name="from_date" id="from_date" value="{{ old('from_date', $defaultFrom) }}" class="form-control shadow-sm rounded-3">
                    </div>
                    <div class="col-12 col-md-4 col-lg-4">
                        <label for="to_date" class="form-label">Deri me</label>
                        <input type="date" name="to_date" id="to_date" value="{{ old('to_date', $defaultTo) }}" class="form-control shadow-sm rounded-3">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-file-alt"></i> Gjenero Raportin</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(!empty($reportData))
        <div class="card mb-4 border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-4 p-lg-5">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                    <div>
                        <small class="text-uppercase text-muted fw-semibold d-block mb-1">Rezultati i raportit</small>
                        <h4 class="fw-bold mb-1">{{ $selectedEmployee->employee ?? 'Përmbledhja e punëtorit' }}</h4>
                        <p class="text-muted mb-0">Periudha {{ $fromLabel }} - {{ $toLabel }}</p>
                    </div>
                    <div class="text-end">
                        <span class="badge rounded-pill text-bg-dark px-3 py-2 mb-2 text-capitalize">{{ $selectedEmployee->role ?? 'Employee' }}</span>
                        <div class="small text-muted">Panel statistikor profesional</div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="card h-100 border-0 bg-dark text-white rounded-4">
                            <div class="card-body p-4">
                                <small class="text-uppercase opacity-75 d-block">Paga Totale</small>
                                <h3 class="fw-bold mb-0 mt-1">€{{ number_format($salaryTotal, 2) }}</h3>
                                <small class="opacity-75">Për periudhën e zgjedhur</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="card h-100 border-0 bg-light rounded-4">
                            <div class="card-body p-4">
                                <small class="text-uppercase text-muted d-block">Orë pune</small>
                                <h3 class="fw-bold mb-0 mt-1">{{ number_format((float) ($workingHours->total_hours ?? 0), 2) }}</h3>
                                <small class="text-muted">Mesatarja: {{ number_format((float) ($workingHours->average_hours ?? 0), 2) }} / regjistrim</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="card h-100 border-0 bg-light rounded-4">
                            <div class="card-body p-4">
                                <small class="text-uppercase text-muted d-block">Orë shtesë totale</small>
                                <h3 class="fw-bold mb-0 mt-1">{{ number_format($totalExtraHours, 2) }}</h3>
                                <small class="text-muted">Vlera: €{{ number_format($extraTotalEur, 2) }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="card h-100 border-0 bg-light rounded-4">
                            <div class="card-body p-4">
                                <small class="text-uppercase text-muted d-block">Detyrat (Totale)</small>
                                <h3 class="fw-bold mb-0 mt-1">{{ $totalTaskCount }}</h3>
                                <small class="text-muted">Përfunduara: {{ $completedTaskCount }} | Papërfunduara: {{ $uncompletedTaskCount }}</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-12 col-xl-6">
                        <div class="card h-100 border rounded-4">
                            <div class="card-body p-4">
                                <h6 class="fw-semibold mb-3"><i class="fa-solid fa-umbrella-beach txt-lightblue me-2"></i>Pushimet</h6>
                                <div class="d-flex justify-content-between mb-2"><span>Totali</span><strong>{{ (int) ($vacations->total_count ?? 0) }}</strong></div>
                                <div class="progress mb-3" role="progressbar" aria-valuenow="{{ (int) $approvedVacationRate }}" aria-valuemin="0" aria-valuemax="100" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: {{ $approvedVacationRate }}%"></div>
                                </div>
                                <div class="small text-muted mb-3">Norma e aprovimit: {{ number_format($approvedVacationRate, 1) }}%</div>
                                <div class="row g-2 small">
                                    <div class="col-6 d-flex justify-content-between"><span>Në pritje HR</span><strong>{{ (int) ($vacations->pending_hr_count ?? 0) }}</strong></div>
                                    <div class="col-6 d-flex justify-content-between"><span>Aprovuar</span><strong class="text-success">{{ (int) ($vacations->approved_count ?? 0) }}</strong></div>
                                    <div class="col-6 d-flex justify-content-between"><span>Refuzuar</span><strong class="text-danger">{{ (int) ($vacations->refused_count ?? 0) }}</strong></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-xl-6">
                        <div class="card h-100 border rounded-4">
                            <div class="card-body p-4">
                                <h6 class="fw-semibold mb-3"><i class="fa-solid fa-file-signature txt-lightblue me-2"></i>Kontratat</h6>
                                <div class="d-flex justify-content-between mb-3"><span>Totali i kontratave</span><strong>{{ (int) ($contracts->total_count ?? 0) }}</strong></div>
                                <div class="row g-2 small">
                                    <div class="col-12 d-flex justify-content-between py-2 border-bottom"><span>Aktive</span><strong class="text-success">{{ (int) ($contracts->active_count ?? 0) }}</strong></div>
                                    <div class="col-12 d-flex justify-content-between py-2 border-bottom"><span>Afër skadimit</span><strong class="text-warning">{{ (int) ($contracts->expiring_count ?? 0) }}</strong></div>
                                    <div class="col-12 d-flex justify-content-between py-2"><span>Skaduara</span><strong class="text-danger">{{ (int) ($contracts->expired_count ?? 0) }}</strong></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-12 col-xl-6">
                        <div class="card border-0 bg-light rounded-4 h-100 shadow-sm">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="fw-semibold mb-0"><i class="fa-solid fa-list-check txt-lightblue me-2"></i>Performanca e Detyrave</h6>
                                    <span class="badge text-bg-dark rounded-pill px-3">{{ $totalTaskCount }} totale</span>
                                </div>
                                <div class="progress mb-3" role="progressbar" aria-valuenow="{{ $totalTaskCount > 0 ? round(($completedTaskCount / $totalTaskCount) * 100) : 0 }}" aria-valuemin="0" aria-valuemax="100" style="height: 10px;">
                                    <div class="progress-bar bg-success" style="width: {{ $totalTaskCount > 0 ? round(($completedTaskCount / $totalTaskCount) * 100) : 0 }}%"></div>
                                </div>
                                <div class="row g-2 small">
                                    <div class="col-6 d-flex justify-content-between"><span>Të përfunduara</span><strong class="text-success">{{ $completedTaskCount }}</strong></div>
                                    <div class="col-6 d-flex justify-content-between"><span>Të papërfunduara</span><strong class="text-danger">{{ $uncompletedTaskCount }}</strong></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-xl-6">
                        <div class="card border-0 bg-light rounded-4 h-100 shadow-sm">
                            <div class="card-body p-4">
                                <h6 class="fw-semibold mb-3"><i class="fa-solid fa-business-time txt-lightblue me-2"></i>Shpërndarja e Orëve Shtesë</h6>
                                <div class="row g-3">
                                    <div class="col-6">
                                        <small class="text-muted d-block">Jashtë orarit</small>
                                        <div class="h5 fw-bold mb-0">{{ number_format((float) ($extraHours->total_hours ?? 0), 2) }}h</div>
                                        <small class="text-muted">€{{ number_format($extraHoursValueEur, 2) }}</small>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Në vikend</small>
                                        <div class="h5 fw-bold mb-0">{{ number_format((float) ($extraWeekendHours->total_hours ?? 0), 2) }}h</div>
                                        <small class="text-muted">€{{ number_format($extraWeekendHoursValueEur, 2) }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card border-0 bg-dark text-white rounded-4">
                            <div class="card-body p-4">
                                <h6 class="text-uppercase opacity-75 mb-3">Totale Përfundimtare</h6>
                                <div class="row g-3">
                                    <div class="col-12 col-md-4">
                                        <div class="small opacity-75">Paga e llogaritur</div>
                                        <div class="h5 fw-bold mb-0">€{{ number_format($salaryTotal, 2) }}</div>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <div class="small opacity-75">Vlera e orëve shtesë + vikend</div>
                                        <div class="h5 fw-bold mb-0">€{{ number_format($extraTotalEur, 2) }}</div>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <div class="small opacity-75">Totali financiar i raportit</div>
                                        <div class="h5 fw-bold mb-0">€{{ number_format($grandTotalEur, 2) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
