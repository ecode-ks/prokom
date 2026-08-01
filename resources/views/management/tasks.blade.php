@extends('template.template')

{{-- Page Title --}}
@section('title', isset($editTask) ? 'Përditëso Detyrën' : 'Detyrat')

{{-- Page Content --}}
@section('content')
    @if(isset($editTask))
        <small class="d-block txt-lightblue fw-semibold text-uppercase"><a href="{{ route('tasks') }}" class="text-decoration-none txt-lightblue fw-semibold"><i class="fa-solid fa-circle-chevron-left"></i> Kthehu mbrapa</a></small>
        <h3 class="fw-semibold pt-2">Përditëso Detyrën</h3>
        <p class="text-muted">Përditëso caktimin, projektin dhe afatin e detyrës së zgjedhur.</p>
        <hr class="mb-4">
    @else
        <small class="d-block txt-lightblue fw-semibold text-uppercase">Detyrat</small>
        <h3 class="fw-semibold pt-2">Të gjitha <span class="txt-lightblue fw-semibold">Detyrat</span></h3>
        <p class="text-muted">Ndjek progresin, menaxho afatet dhe mbaji detyrat në orar.</p>
        <hr class="mb-4">
    @endif

    @if(isset($editTask))

        {{-- Update Task --}}
        <div class="card mb-4">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-chart-gantt txt-lightblue py-2"></i> Përditëso Detyrën</span>
            </div>
            <div class="card-body">
                <form action="{{ route('update.task') }}" method="post">
                    @csrf
                    <input type="hidden" name="id" value="{{ $editTask->id }}">
                    <div class="row g-3">
                        <div class="col-12 col-md-6 col-lg-6">
                            <label class="form-label">Punonjësit</label>
                            @php
                                $selectedEmployees = array_map(
                                    'strval',
                                    array_filter(explode(',', old('assigned_to') ? implode(',', (array) old('assigned_to')) : (string) $editTask->assigned_to))
                                );
                                $selectedEmployeeLabels = [];
                                foreach ($employees as $employee) {
                                    if (in_array((string) $employee->id, $selectedEmployees, true)) {
                                        $selectedEmployeeLabels[] = $employee->employee;
                                    }
                                }
                                $selectedEmployeesText = count($selectedEmployeeLabels) > 0
                                    ? implode(', ', $selectedEmployeeLabels)
                                    : '-- Zgjidh Punonjësit --';
                            @endphp
                            <div class="dropdown">
                                <button
                                    class="form-control shadow-sm rounded-3 text-start d-flex justify-content-between align-items-center"
                                    type="button"
                                    id="assignedToEditDropdown"
                                    data-bs-toggle="dropdown"
                                    data-bs-auto-close="outside"
                                    aria-expanded="false"
                                >
                                    <span class="text-truncate assigned-to-label">{{ $selectedEmployeesText }}</span>
                                    <i class="fa-solid fa-chevron-down ms-2 opacity-50"></i>
                                </button>
                                <div class="dropdown-menu w-100 p-2 shadow rounded-3" aria-labelledby="assignedToEditDropdown" style="max-height: 240px; overflow-y: auto;">
                                    @foreach($employees as $employee)
                                        <label class="dropdown-item rounded-3 d-flex align-items-center gap-2 py-2 mb-1" for="assigned_to_edit_{{ $employee->id }}">
                                            <input
                                                class="form-check-input m-0 assigned-to-checkbox"
                                                type="checkbox"
                                                name="assigned_to[]"
                                                value="{{ $employee->id }}"
                                                id="assigned_to_edit_{{ $employee->id }}"
                                                data-label="{{ $employee->employee }}"
                                                {{ in_array((string) $employee->id, $selectedEmployees, true) ? 'checked' : '' }}
                                            >
                                            <span>{{ $employee->employee }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            <small class="text-muted">Zgjidh një ose më shumë punonjës.</small>
                        </div>
                        <div class="col-12 col-md-6 col-lg-6">
                            <label for="project_id" class="form-label">Projekti</label>
                            <select name="project_id" id="project_id" class="form-control shadow-sm rounded-3">
                                <option value="">-- Zgjidh Projektin --</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ old('project_id', $editTask->project_id) == $project->id ? 'selected' : '' }}>{{ $project->project_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6 col-lg-6">
                            <label for="deadline" class="form-label">Afati i Detyrës</label>
                            <input type="date" name="deadline" id="deadline" value="{{ old('deadline', $editTask->deadline) }}" class="form-control shadow-sm rounded-3">
                        </div>
                        <div class="col-12 col-md-6 col-lg-6">
                            <label for="title" class="form-label">Titulli i Detyrës</label>
                            <input type="text" name="title" id="title" value="{{ old('title', $editTask->title) }}" class="form-control shadow-sm rounded-3" placeholder="Shkruani titullin e detyrës...">
                        </div>
                        <div class="col-12">
                            <label for="task" class="form-label">Përshkrimi i Detyrës</label>
                            <textarea name="task" id="task" class="form-control shadow-sm rounded-3" placeholder="Shkruani përshkrimin e detyrës..." rows="5">{{ old('task', $editTask->task) }}</textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-check"></i> Përditëso Detyrën</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @else

        {{-- Info Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-uppercase opacity-75"><i class="fa-solid fa-list-check text-primary"></i> Total</small>
                        <h4 class="fw-semibold mb-0">{{ $taskStats->total_count ?? 0 }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-uppercase opacity-75"><i class="fa-solid fa-sparkles text-info"></i> Të reja</small>
                        <h4 class="fw-semibold mb-0">{{ $taskStats->new_count ?? 0 }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-uppercase opacity-75"><i class="fa-solid fa-spinner text-warning"></i> Në punë</small>
                        <h4 class="fw-semibold mb-0">{{ $taskStats->working_count ?? 0 }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-uppercase opacity-75"><i class="fa-solid fa-circle-check text-success"></i> Përfunduar</small>
                        <h4 class="fw-semibold mb-0">{{ $taskStats->completed_count ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>

        {{-- Create Task --}}
        <div class="card mb-4">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-chart-gantt txt-lightblue py-2"></i> Krijo Detyrë të Re</span>
            </div>
            <div class="card-body">
                <form action="{{ route('create.task') }}" method="post">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12 col-md-6 col-lg-6">
                            <label class="form-label">Punonjësit</label>
                            @php
                                $oldAssignedTo = array_map('strval', old('assigned_to', []));
                                $oldAssignedLabels = [];
                                foreach ($employees as $employee) {
                                    if (in_array((string) $employee->id, $oldAssignedTo, true)) {
                                        $oldAssignedLabels[] = $employee->employee;
                                    }
                                }
                                $oldAssignedText = count($oldAssignedLabels) > 0
                                    ? implode(', ', $oldAssignedLabels)
                                    : '-- Zgjidh Punonjësit --';
                            @endphp
                            <div class="dropdown">
                                <button
                                    class="form-control shadow-sm rounded-3 text-start d-flex justify-content-between align-items-center"
                                    type="button"
                                    id="assignedToCreateDropdown"
                                    data-bs-toggle="dropdown"
                                    data-bs-auto-close="outside"
                                    aria-expanded="false"
                                >
                                    <span class="text-truncate assigned-to-label">{{ $oldAssignedText }}</span>
                                    <i class="fa-solid fa-chevron-down ms-2 opacity-50"></i>
                                </button>
                                <div class="dropdown-menu w-100 p-2 shadow rounded-3" aria-labelledby="assignedToCreateDropdown" style="max-height: 240px; overflow-y: auto;">
                                    @foreach($employees as $employee)
                                        <label class="dropdown-item rounded-3 d-flex align-items-center gap-2 py-2 mb-1" for="assigned_to_create_{{ $employee->id }}">
                                            <input
                                                class="form-check-input m-0 assigned-to-checkbox"
                                                type="checkbox"
                                                name="assigned_to[]"
                                                value="{{ $employee->id }}"
                                                id="assigned_to_create_{{ $employee->id }}"
                                                data-label="{{ $employee->employee }}"
                                                {{ in_array((string) $employee->id, $oldAssignedTo, true) ? 'checked' : '' }}
                                            >
                                            <span>{{ $employee->employee }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            <small class="text-muted">Zgjidh një ose më shumë punonjës.</small>
                        </div>
                        <div class="col-12 col-md-6 col-lg-6">
                            <label for="project_id" class="form-label">Projekti</label>
                            <select name="project_id" id="project_id" class="form-control shadow-sm rounded-3">
                                <option value="">-- Zgjidh Projektin --</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>{{ $project->project_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6 col-lg-6">
                            <label for="deadline" class="form-label">Afati i Detyrës</label>
                            <input type="date" name="deadline" id="deadline" value="{{ old('deadline') }}" class="form-control shadow-sm rounded-3">
                        </div>
                        <div class="col-12 col-md-6 col-lg-6">
                            <label for="title" class="form-label">Titulli i Detyrës</label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}" class="form-control shadow-sm rounded-3" placeholder="Shkruani titullin e detyrës...">
                        </div>
                        <div class="col-12">
                            <label for="task" class="form-label">Përshkrimi i Detyrës</label>
                            <textarea name="task" id="task" class="form-control shadow-sm rounded-3" placeholder="Shkruani përshkrimin e detyrës..." rows="5">{{ old('task') }}</textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-plus"></i> Krijo Detyrën</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tasks List --}}
        <div class="card">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-list-ol txt-lightblue py-2"></i> Lista e Detyrave</span>
            </div>
            <div class="card-body">
                <form action="{{ route('tasks') }}" method="get" class="input-group mb-3">
                    <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-control shadow-sm rounded-start-3" placeholder="Kërko..." aria-label="Kërko..." aria-describedby="basic-addon2">
                    <button type="submit" class="btn btn-darkblue shadow-sm rounded-end-3" id="basic-addon2"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th scope="col" class="text-nowrap">#</th>
                                <th scope="col" class="text-nowrap">Data</th>
                                <th scope="col" class="text-nowrap">Punonjësit</th>
                                <th scope="col" class="text-nowrap">Caktuar Nga</th>
                                <th scope="col" class="text-nowrap">Titulli</th>
                                <th scope="col" class="text-nowrap">Projekti</th>
                                <th scope="col" class="text-nowrap">Afati</th>
                                <th scope="col" class="text-nowrap">Statusi</th>
                                <th scope="col" class="text-nowrap text-end">Veprimet</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($tasks) === 0)
                                <tr>
                                    <td colspan="9" class="text-center"><i class="fa-regular fa-face-frown"></i> Nuk u gjetën të dhëna...</td>
                                </tr>
                            @else
                                @foreach ($tasks as $task)
                                    @php
                                        $status = strtolower((string) ($task->status ?? ''));
                                        $statusLabels = [
                                            'new' => 'E re',
                                            'working' => 'Në punë',
                                            'in_progress' => 'Në punë',
                                            'in progress' => 'Në punë',
                                            'review' => 'Në shqyrtim',
                                            'reviewing' => 'Në shqyrtim',
                                            'blocked' => 'Bllokuar',
                                            'on_hold' => 'Bllokuar',
                                            'completed' => 'Përfunduar',
                                        ];
                                        $statusLabel = $statusLabels[$status] ?? ucwords(str_replace('_', ' ', $task->status ?? '-'));
                                    @endphp
                                    <tr>
                                        <th scope="row" class="text-nowrap">{{ $task->id }}</th>
                                        <td class="text-nowrap">{{ \Carbon\Carbon::parse($task->date)->format('d/m/Y') }}</td>
                                        <td class="text-nowrap">{{ $task->assigned_to_names ?? '-' }}</td>
                                        <td class="text-nowrap">{{ $task->assigned_by_name ?? '-' }}</td>
                                        <td class="text-nowrap">{{ $task->title }}</td>
                                        <td class="text-nowrap">{{ $task->project_name }}</td>
                                        <td class="text-nowrap">{{ \Carbon\Carbon::parse($task->deadline)->format('d/m/Y') }}</td>
                                        <td class="text-nowrap">
                                            @if($status === 'completed')
                                                <span class="badge rounded-pill text-success text-bg-success bg-opacity-25 border border-success">{{ $statusLabel }}</span>
                                            @elseif($status === 'working' || $status === 'in_progress' || $status === 'in progress')
                                                <span class="badge rounded-pill text-primary text-bg-primary bg-opacity-25 border border-primary">{{ $statusLabel }}</span>
                                            @elseif($status === 'review' || $status === 'reviewing')
                                                <span class="badge rounded-pill text-dark text-bg-warning bg-opacity-25 border border-warning">{{ $statusLabel }}</span>
                                            @elseif($status === 'blocked' || $status === 'on_hold')
                                                <span class="badge rounded-pill text-danger text-bg-danger bg-opacity-25 border border-danger">{{ $statusLabel }}</span>
                                            @else
                                                <span class="badge rounded-pill text-secondary text-bg-secondary bg-opacity-25 border border-secondary">{{ $statusLabel }}</span>
                                            @endif
                                        </td>
                                        <td class="text-nowrap text-end">
                                            <a href="{{ route('edit.task', $task->id) }}" class="btn btn-outline-success btn-sm rounded-3 shadow-sm"><i class="fa-solid fa-pen"></i></a>
                                            <button class="btn btn-outline-danger btn-sm rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $task->id }}"><i class="fa-solid fa-trash"></i></button>
                                            <a href="{{ route('task.details', $task->id) }}" class="btn btn-outline-dark btn-sm rounded-3 shadow-sm"><i class="fa-solid fa-eye"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                @if ($tasks->hasPages())
                    <div class="mt-3">
                        {{ $tasks->links('vendor.pagination.custom') }}
                    </div>
                @endif
            </div>
        </div>

        @if(count($tasks) !== 0)

            <!-- Delete Modal -->
            @foreach ($tasks as $task)
                <div class="modal fade" id="deleteModal{{ $task->id }}" tabindex="-1" aria-labelledby="deleteModal{{ $task->id }}Label" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-body text-center">
                                <p>A jeni i sigurt që dëshironi të fshini këtë detyrë?</p>
                                <div class="mt-3">
                                    <a href="{{ route('delete.task', $task->id) }}" class="btn btn-danger btn-sm fw-semibold"><i class="fa-solid fa-check"></i> Konfirmo</a>
                                    <button class="btn btn-success btn-sm fw-semibold" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> Anulo</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    @endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.dropdown').forEach(function (dropdown) {
        const label = dropdown.querySelector('.assigned-to-label');
        const checkboxes = dropdown.querySelectorAll('.assigned-to-checkbox');
        if (!label || checkboxes.length === 0) {
            return;
        }

        const updateLabel = function () {
            const selected = Array.from(checkboxes)
                .filter(function (checkbox) { return checkbox.checked; })
                .map(function (checkbox) { return checkbox.getAttribute('data-label'); });

            label.textContent = selected.length > 0
                ? selected.join(', ')
                : '-- Zgjidh Punonjësit --';
        };

        checkboxes.forEach(function (checkbox) {
            checkbox.addEventListener('change', updateLabel);
        });
    });
});
</script>
@endpush
