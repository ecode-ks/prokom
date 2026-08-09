@extends('template.template')

{{-- Page Title --}}
@section('title', 'Dashboard')

{{-- Page Content --}}
@section('content')
    <small class="d-block text-uppercase fw-semibold opacity-75" style="letter-spacing:.06em;font-size:.75rem;">Dashboard · {{ date('d/m/Y') }}</small>
    <h3 class="fw-semibold pt-2 mb-4">Mirë se erdhët, <span class="txt-lightblue">{{ session('user') ?? session('company') }}</span></h3>

    @if(!empty($isManager) && $managerKpis)
        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-3">
                <div class="dashboard-kpi dashboard-kpi--navy">
                    <small><i class="fa-solid fa-diagram-project me-1 opacity-75"></i> Projektet e mia</small>
                    <strong>{{ $managerKpis->active_projects }}</strong>
                    <span>aktive</span>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="dashboard-kpi dashboard-kpi--blue">
                    <small><i class="fa-solid fa-list-check me-1 opacity-75"></i> Detyra të hapura</small>
                    <strong>{{ $managerKpis->open_tasks }}</strong>
                    <span>në projektet e mia</span>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="dashboard-kpi dashboard-kpi--warn">
                    <small><i class="fa-solid fa-triangle-exclamation me-1 opacity-75"></i> Me vonesë</small>
                    <strong>{{ $managerKpis->overdue_tasks }}</strong>
                    <span>detyra të skaduara</span>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="dashboard-kpi dashboard-kpi--teal">
                    <small><i class="fa-solid fa-calendar-week me-1 opacity-75"></i> Këtë javë</small>
                    <strong>{{ $managerKpis->due_soon_tasks }}</strong>
                    <span>afate brenda 7 ditëve</span>
                </div>
            </div>
        </div>

    @endif

    @if($canCheckIn)
        @php
            $fmt = function ($value) {
                return $value ? \Carbon\Carbon::parse($value)->format('H:i') : '--:--';
            };
            $onPause = $isActive && $checkIn && $checkIn->pause_start_time && !$checkIn->pause_end_time;
            $status = !$checkIn
                ? 'Jo aktiv'
                : (!$isActive ? 'Përfunduar' : ($onPause ? 'Në pauzë' : 'Në punë'));

            $mapLat = null;
            $mapLng = null;
            if (!empty($checkIn->check_in_location) && str_contains($checkIn->check_in_location, ',')) {
                [$mapLat, $mapLng] = array_map('trim', explode(',', $checkIn->check_in_location, 2));
                if (!is_numeric($mapLat) || !is_numeric($mapLng)) {
                    $mapLat = null;
                    $mapLng = null;
                }
            }
        @endphp

        <div class="row g-4 mb-4">
            <div class="col-12 col-xl-5">
                <div class="checkin-panel h-100">
                    <div class="checkin-panel-head">
                        <div>
                            <span class="checkin-kicker">Evidenca e ditës</span>
                            <h5 class="fw-semibold mb-0">Check-in</h5>
                        </div>
                        <span class="checkin-status {{ !$isActive ? 'is-idle' : ($onPause ? 'is-pause' : 'is-work') }}">{{ $status }}</span>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-6 col-md-3 col-xl-6">
                            <div class="checkin-stat">
                                <small>Check-in</small>
                                <strong>{{ $fmt($checkIn->check_in_time ?? null) }}</strong>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 col-xl-6">
                            <div class="checkin-stat">
                                <small>Fillimi i pauzës</small>
                                <strong>{{ $fmt($checkIn->pause_start_time ?? null) }}</strong>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 col-xl-6">
                            <div class="checkin-stat">
                                <small>Mbarimi i pauzës</small>
                                <strong>{{ $fmt($checkIn->pause_end_time ?? null) }}</strong>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 col-xl-6">
                            <div class="checkin-stat">
                                <small>Check-out</small>
                                <strong>{{ $fmt($checkIn->check_out_time ?? null) }}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="checkin-actions">
                        @if(!$isActive)
                            <form action="{{ route('start.work') }}" method="post" id="startWorkForm" class="w-100">
                                @csrf
                                <input type="hidden" name="check_in_location" id="check_in_location">
                                <button type="submit" class="btn btn-darkblue w-100 rounded-3 py-3 fw-semibold">
                                    <i class="fa-solid fa-play me-2"></i> Starto Punën
                                </button>
                            </form>
                        @else
                            @if(!$checkIn->pause_start_time)
                                <form action="{{ route('start.break') }}" method="post" class="flex-fill">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $checkIn->id }}">
                                    <button type="submit" class="btn btn-outline-secondary w-100 rounded-3 py-3 fw-semibold">
                                        <i class="fa-solid fa-mug-saucer me-2"></i> Starto Pauzën
                                    </button>
                                </form>
                            @elseif(!$checkIn->pause_end_time)
                                <form action="{{ route('end.break') }}" method="post" class="flex-fill">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $checkIn->id }}">
                                    <button type="submit" class="btn btn-outline-secondary w-100 rounded-3 py-3 fw-semibold">
                                        <i class="fa-solid fa-play me-2"></i> Përfundo Pauzën
                                    </button>
                                </form>
                            @endif

                            @if(!$checkIn->pause_start_time || $checkIn->pause_end_time)
                                <form action="{{ route('end.work') }}" method="post" id="endWorkForm" class="flex-fill">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $checkIn->id }}">
                                    <input type="hidden" name="check_out_location" id="check_out_location">
                                    <button type="submit" class="btn btn-darkblue w-100 rounded-3 py-3 fw-semibold">
                                        <i class="fa-solid fa-stop me-2"></i> Përfundo Punën
                                    </button>
                                </form>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-7">
                <div class="checkin-panel h-100">
                    <div class="checkin-panel-head">
                        <div>
                            <span class="checkin-kicker">Lokacioni</span>
                            <h5 class="fw-semibold mb-0">Harta</h5>
                        </div>
                        @if($mapLat !== null)
                            <a class="dashboard-link"
                               href="https://www.google.com/maps?q={{ $mapLat }},{{ $mapLng }}"
                               target="_blank"
                               rel="noopener noreferrer">
                                Hap në Maps <i class="fa-solid fa-arrow-up-right-from-square ms-1 opacity-50"></i>
                            </a>
                        @endif
                    </div>

                    <div class="checkin-map">
                        @if($mapLat !== null)
                            <iframe
                                title="Check-in map"
                                class="checkin-map-iframe"
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"
                                src="https://www.google.com/maps?q={{ $mapLat }},{{ $mapLng }}&z=15&output=embed">
                            </iframe>
                        @else
                            <div class="checkin-map-empty">
                                <i class="fa-solid fa-location-dot mb-2 opacity-50"></i>
                                <p class="mb-0 text-muted">Lokacioni shfaqet pasi të fillosh check-in.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(!empty($isManager) && $managerKpis)
        <div class="row g-4 mb-4">
            <div class="col-12 col-lg-6">
                <div class="checkin-panel h-100">
                    <div class="checkin-panel-head">
                        <div>
                            <span class="checkin-kicker">Projektet</span>
                            <h5 class="fw-semibold mb-0">Afatet që afrohen</h5>
                        </div>
                        <a href="{{ route('projects') }}" class="dashboard-link">Shiko të gjitha</a>
                    </div>

                    @if($managerProjects->isEmpty())
                        <p class="text-muted mb-0 small">Nuk ka projekte me afat të afërt.</p>
                    @else
                        <div class="dashboard-task-list">
                            @foreach($managerProjects as $project)
                                @php
                                    $days = \Carbon\Carbon::parse($project->expected_end_date)->startOfDay()->diffInDays(now()->startOfDay(), false);
                                    $daysLeft = (int) (-$days);
                                    $label = $daysLeft < 0
                                        ? abs($daysLeft) . ' ditë vonesë'
                                        : ($daysLeft === 0 ? 'Sot' : $daysLeft . ' ditë');
                                @endphp
                                <a href="{{ route('project.details', $project->id) }}" class="dashboard-task-item text-decoration-none">
                                    <div class="min-w-0">
                                        <strong class="d-block text-truncate">{{ $project->project_name }}</strong>
                                        <small class="text-muted">{{ $project->status }}</small>
                                    </div>
                                    <span class="small text-nowrap fw-semibold {{ $daysLeft < 0 ? 'txt-warn' : 'text-muted' }}">{{ $label }}</span>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="checkin-panel h-100">
                    <div class="checkin-panel-head">
                        <div>
                            <span class="checkin-kicker">Ekipi</span>
                            <h5 class="fw-semibold mb-0">Detyrat e projekteve</h5>
                            @if($managerTeamPresence)
                                <small class="text-muted">
                                    Prezenca sot: {{ $managerTeamPresence->checked_in_today }}/{{ $managerTeamPresence->team_size }}
                                </small>
                            @endif
                        </div>
                        <a href="{{ route('tasks') }}" class="dashboard-link">Shiko të gjitha</a>
                    </div>

                    @if($managerTeamTasks->isEmpty())
                        <p class="text-muted mb-0 small">Nuk ka detyra të hapura në projektet e tua.</p>
                    @else
                        <div class="dashboard-task-list">
                            @foreach($managerTeamTasks as $task)
                                @php
                                    $status = strtolower((string) ($task->status ?? ''));
                                    $deadlineLabel = $task->deadline
                                        ? \Carbon\Carbon::parse($task->deadline)->format('d/m/Y')
                                        : null;
                                    $isOverdue = $task->deadline
                                        && \Carbon\Carbon::parse($task->deadline)->isPast();
                                @endphp
                                <a href="{{ route('task.details', $task->id) }}" class="dashboard-task-item text-decoration-none">
                                    <div class="min-w-0">
                                        <strong class="d-block text-truncate">{{ $task->title ?: 'Pa titull' }}</strong>
                                        <small class="text-muted">{{ $task->project_name ?? '-' }}</small>
                                    </div>
                                    <div class="dashboard-task-meta">
                                        @if($deadlineLabel)
                                            <span class="{{ $isOverdue ? 'txt-warn' : 'text-muted' }} small text-nowrap fw-semibold">{{ $deadlineLabel }}</span>
                                        @endif
                                        <span class="checkin-status {{ $status === 'working' || $status === 'in_progress' ? 'is-work' : 'is-idle' }}">{{ $task->status ?: 'new' }}</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-12 col-lg-6">
                <div class="checkin-panel h-100">
                    <div class="checkin-panel-head">
                        <div>
                            <span class="checkin-kicker">Raporte</span>
                            <h5 class="fw-semibold mb-0">Raporte në pritje</h5>
                        </div>
                        <a href="{{ route('project.reports') }}" class="dashboard-link">Shiko të gjitha</a>
                    </div>

                    @if($managerPendingReports->isEmpty())
                        <p class="text-muted mb-0 small">Nuk ka raporte të reja për shqyrtim.</p>
                    @else
                        <div class="dashboard-task-list">
                            @foreach($managerPendingReports as $report)
                                <a href="{{ route('project.report.details', $report->id) }}" class="dashboard-task-item text-decoration-none">
                                    <div class="min-w-0">
                                        <strong class="d-block text-truncate">{{ $report->project_name }}</strong>
                                        <small class="text-muted">{{ $report->reporter_name ?: 'Punonjës' }}</small>
                                    </div>
                                    <span class="small text-muted text-nowrap">
                                        {{ $report->date ? \Carbon\Carbon::parse($report->date)->format('d/m H:i') : '-' }}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="checkin-panel h-100">
                    <div class="checkin-panel-head mb-3">
                        <div>
                            <span class="checkin-kicker">Veprime</span>
                            <h5 class="fw-semibold mb-0">Veprime të shpejta</h5>
                        </div>
                    </div>
                    <div class="dashboard-quick-actions">
                        <a href="{{ route('tasks') }}" class="dashboard-quick-btn">
                            <i class="fa-solid fa-plus"></i> Cakto detyrë
                        </a>
                        <a href="{{ route('projects') }}" class="dashboard-quick-btn">
                            <i class="fa-solid fa-diagram-project"></i> Projektet
                        </a>
                        <a href="{{ route('project.reports') }}" class="dashboard-quick-btn">
                            <i class="fa-solid fa-clipboard-list"></i> Raportet
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
@if($canCheckIn)
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
    wireLocationForm('endWorkForm', 'check_out_location');
})();
</script>
@endif
@endpush
