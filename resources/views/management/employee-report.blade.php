@extends('template.template')

@section('title', __('pages.employee_report_title'))

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

    <small class="d-block txt-lightblue fw-semibold text-uppercase">{{ __('pages.nav_employees') }}</small>
    <h3 class="fw-semibold pt-2">{{ ($canSelectEmployee ?? false) ? __('pages.employee_report_title') : __('pages.my_report') }}</h3>
    <p class="text-muted">{{ ($canSelectEmployee ?? false) ? __('pages.report_lead_staff') : __('pages.report_lead_self') }}</p>
    <hr class="mb-4">

    <div class="card mb-4">
        <div class="card-header rounded-4 bg-white">
            <span><i class="fa-solid fa-calendar-days txt-lightblue py-2"></i> {{ __('pages.period') }} <strong>{{ $fromLabel }} – {{ $toLabel }}</strong></span>
        </div>
        <div class="card-body">
            <form action="{{ route('generate.employee.report') }}" method="get">
                <div class="row g-3">
                    <div class="col-12 col-md-4 col-lg-4">
                        <label for="employee_id" class="form-label">{{ __('pages.employee') }}</label>
                        @if($canSelectEmployee ?? false)
                            <select name="employee_id" id="employee_id" class="form-control shadow-sm rounded-3">
                                <option value="">{{ __('pages.choose_worker') }}</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ (string) request('employee_id', $employeeId ?? '') === (string) $employee->id ? 'selected' : '' }}>
                                        {{ $employee->employee }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            @php $selfEmployee = collect($employees ?? [])->first(); @endphp
                            <input type="hidden" name="employee_id" value="{{ $selfEmployee->id ?? session('employee_id') }}">
                            <input type="text" class="form-control shadow-sm rounded-3" value="{{ $selfEmployee->employee ?? '' }}" readonly>
                        @endif
                    </div>

                    <div class="col-12 col-md-4 col-lg-4">
                        <label for="from_date" class="form-label">{{ __('pages.from_date') }}</label>
                        <input type="date" name="from_date" id="from_date" value="{{ old('from_date', $defaultFrom) }}" class="form-control shadow-sm rounded-3">
                    </div>
                    <div class="col-12 col-md-4 col-lg-4">
                        <label for="to_date" class="form-label">{{ __('pages.until') }}</label>
                        <input type="date" name="to_date" id="to_date" value="{{ old('to_date', $defaultTo) }}" class="form-control shadow-sm rounded-3">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-file-alt"></i> {{ __('pages.generate_report') }}</button>
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
                        <small class="text-uppercase text-muted fw-semibold d-block mb-1">{{ __('pages.report_result') }}</small>
                        <h4 class="fw-bold mb-1">{{ $selectedEmployee->employee ?? __('pages.employee_summary') }}</h4>
                        <p class="text-muted mb-0">{{ __('pages.period_range', ['from' => $fromLabel, 'to' => $toLabel]) }}</p>
                    </div>
                    <div class="text-end">
                        <span class="badge rounded-pill text-bg-dark px-3 py-2 mb-2 text-capitalize">
                            @if($selectedEmployee->role == 'admin')
                                {{ __('pages.role_admin') }}
                            @elseif($selectedEmployee->role == 'company')
                                {{ __('pages.role_company') }}
                            @elseif($selectedEmployee->role == 'manager')
                                {{ __('pages.role_manager') }}
                            @elseif($selectedEmployee->role == 'employee')
                                {{ __('pages.role_employee') }}
                            @elseif($selectedEmployee->role == 'finance')
                                {{ __('pages.role_finance') }}
                            @elseif($selectedEmployee->role == 'hr')
                                {{ __('pages.role_hr') }}
                            @endif
                        </span>
                        <div class="small text-muted">{{ __('pages.professional_panel') }}</div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="card h-100 border-0 bg-dark text-white rounded-4">
                            <div class="card-body p-4">
                                <small class="text-uppercase opacity-75 d-block">{{ __('pages.total_salary') }}</small>
                                <h3 class="fw-bold mb-0 mt-1">€{{ number_format($salaryTotal, 2) }}</h3>
                                <small class="opacity-75">{{ __('pages.for_selected_period') }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="card h-100 border-0 bg-light rounded-4">
                            <div class="card-body p-4">
                                <small class="text-uppercase text-muted d-block">{{ __('pages.work_hours') }}</small>
                                <h3 class="fw-bold mb-0 mt-1">{{ number_format((float) ($workingHours->total_hours ?? 0), 2) }}</h3>
                                <small class="text-muted">{{ __('pages.avg_per_record', ['avg' => number_format((float) ($workingHours->average_hours ?? 0), 2)]) }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="card h-100 border-0 bg-light rounded-4">
                            <div class="card-body p-4">
                                <small class="text-uppercase text-muted d-block">{{ __('pages.total_extra_hours') }}</small>
                                <h3 class="fw-bold mb-0 mt-1">{{ number_format($totalExtraHours, 2) }}</h3>
                                <small class="text-muted">{{ __('pages.value_eur', ['amount' => number_format($extraTotalEur, 2)]) }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="card h-100 border-0 bg-light rounded-4">
                            <div class="card-body p-4">
                                <small class="text-uppercase text-muted d-block">{{ __('pages.tasks_total') }}</small>
                                <h3 class="fw-bold mb-0 mt-1">{{ $totalTaskCount }}</h3>
                                <small class="text-muted">{{ __('pages.completed_uncompleted', ['done' => $completedTaskCount, 'open' => $uncompletedTaskCount]) }}</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-12 col-xl-6">
                        <div class="card h-100 border rounded-4">
                            <div class="card-body p-4">
                                <h6 class="fw-semibold mb-3"><i class="fa-solid fa-umbrella-beach txt-lightblue me-2"></i>{{ __('pages.vacations') }}</h6>
                                <div class="d-flex justify-content-between mb-2"><span>{{ __('pages.total_label') }}</span><strong>{{ (int) ($vacations->total_count ?? 0) }}</strong></div>
                                <div class="progress mb-3" role="progressbar" aria-valuenow="{{ (int) $approvedVacationRate }}" aria-valuemin="0" aria-valuemax="100" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: {{ $approvedVacationRate }}%"></div>
                                </div>
                                <div class="small text-muted mb-3">{{ __('pages.approval_rate', ['rate' => number_format($approvedVacationRate, 1)]) }}</div>
                                <div class="row g-2 small">
                                    <div class="col-6 d-flex justify-content-between"><span>{{ __('pages.pending_hr') }}</span><strong>{{ (int) ($vacations->pending_hr_count ?? 0) }}</strong></div>
                                    <div class="col-6 d-flex justify-content-between"><span>{{ __('pages.approved') }}</span><strong class="text-success">{{ (int) ($vacations->approved_count ?? 0) }}</strong></div>
                                    <div class="col-6 d-flex justify-content-between"><span>{{ __('pages.rejected') }}</span><strong class="text-danger">{{ (int) ($vacations->refused_count ?? 0) }}</strong></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-xl-6">
                        <div class="card h-100 border rounded-4">
                            <div class="card-body p-4">
                                <h6 class="fw-semibold mb-3"><i class="fa-solid fa-file-signature txt-lightblue me-2"></i>{{ __('pages.contracts') }}</h6>
                                <div class="d-flex justify-content-between mb-3"><span>{{ __('pages.total_contracts') }}</span><strong>{{ (int) ($contracts->total_count ?? 0) }}</strong></div>
                                <div class="row g-2 small">
                                    <div class="col-12 d-flex justify-content-between py-2 border-bottom"><span>{{ __('pages.contract_active') }}</span><strong class="text-success">{{ (int) ($contracts->active_count ?? 0) }}</strong></div>
                                    <div class="col-12 d-flex justify-content-between py-2 border-bottom"><span>{{ __('pages.near_expiry') }}</span><strong class="text-warning">{{ (int) ($contracts->expiring_count ?? 0) }}</strong></div>
                                    <div class="col-12 d-flex justify-content-between py-2"><span>{{ __('pages.expired_plural') }}</span><strong class="text-danger">{{ (int) ($contracts->expired_count ?? 0) }}</strong></div>
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
                                    <h6 class="fw-semibold mb-0"><i class="fa-solid fa-list-check txt-lightblue me-2"></i>{{ __('pages.task_performance') }}</h6>
                                    <span class="badge text-bg-dark rounded-pill px-3">{{ __('pages.total_badge', ['count' => $totalTaskCount]) }}</span>
                                </div>
                                <div class="progress mb-3" role="progressbar" aria-valuenow="{{ $totalTaskCount > 0 ? round(($completedTaskCount / $totalTaskCount) * 100) : 0 }}" aria-valuemin="0" aria-valuemax="100" style="height: 10px;">
                                    <div class="progress-bar bg-success" style="width: {{ $totalTaskCount > 0 ? round(($completedTaskCount / $totalTaskCount) * 100) : 0 }}%"></div>
                                </div>
                                <div class="row g-2 small">
                                    <div class="col-6 d-flex justify-content-between"><span>{{ __('pages.completed_plural') }}</span><strong class="text-success">{{ $completedTaskCount }}</strong></div>
                                    <div class="col-6 d-flex justify-content-between"><span>{{ __('pages.uncompleted_plural') }}</span><strong class="text-danger">{{ $uncompletedTaskCount }}</strong></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-xl-6">
                        <div class="card border-0 bg-light rounded-4 h-100 shadow-sm">
                            <div class="card-body p-4">
                                <h6 class="fw-semibold mb-3"><i class="fa-solid fa-business-time txt-lightblue me-2"></i>{{ __('pages.extra_hours_distribution') }}</h6>
                                <div class="row g-3">
                                    <div class="col-6">
                                        <small class="text-muted d-block">{{ __('pages.overtime') }}</small>
                                        <div class="h5 fw-bold mb-0">{{ number_format((float) ($extraHours->total_hours ?? 0), 2) }}h</div>
                                        <small class="text-muted">€{{ number_format($extraHoursValueEur, 2) }}</small>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">{{ __('pages.weekend_at') }}</small>
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
                                <h6 class="text-uppercase opacity-75 mb-3">{{ __('pages.grand_totals') }}</h6>
                                <div class="row g-3">
                                    <div class="col-12 col-md-4">
                                        <div class="small opacity-75">{{ __('pages.calculated_salary') }}</div>
                                        <div class="h5 fw-bold mb-0">€{{ number_format($salaryTotal, 2) }}</div>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <div class="small opacity-75">{{ __('pages.extra_weekend_value') }}</div>
                                        <div class="h5 fw-bold mb-0">€{{ number_format($extraTotalEur, 2) }}</div>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <div class="small opacity-75">{{ __('pages.financial_total') }}</div>
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
