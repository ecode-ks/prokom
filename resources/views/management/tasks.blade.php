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
                            <label for="assigned_to" class="form-label">Punonjësi</label>
                            <select name="assigned_to" id="assigned_to" class="form-control shadow-sm rounded-3">
                                <option value="">-- Zgjidh Punonjësin --</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ (string) old('assigned_to', $editTask->assigned_to) === (string) $employee->id ? 'selected' : '' }}>{{ $employee->employee }}</option>
                                @endforeach
                            </select>
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
                            <label for="assigned_to" class="form-label">Punonjësi</label>
                            <select name="assigned_to" id="assigned_to" class="form-control shadow-sm rounded-3">
                                <option value="">-- Zgjidh Punonjësin --</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ (string) old('assigned_to') === (string) $employee->id ? 'selected' : '' }}>{{ $employee->employee }}</option>
                                @endforeach
                            </select>
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
                                    <td colspan="8" class="text-center"><i class="fa-regular fa-face-frown"></i> Nuk u gjetën të dhëna...</td>
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
