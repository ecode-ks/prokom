@extends('template.template')

{{-- Page Title --}}
@section('title', isset($editProject) ? 'Përditëso Projektin' : 'Projektet')

{{-- Page Content --}}
@section('content')
    @if(isset($editProject))
        <small class="d-block txt-lightblue fw-semibold text-uppercase"><a href="{{ route('projects') }}" class="text-decoration-none txt-lightblue fw-semibold"><i class="fa-solid fa-circle-chevron-left"></i> Kthehu mbrapa</a></small>
        <h3 class="fw-semibold pt-2">Përditëso Projektin</h3>
        <p class="text-muted">Përditëso të dhënat e projektit të zgjedhur.</p>
        <hr class="mb-4">
    @else
        <small class="d-block txt-lightblue fw-semibold text-uppercase">Projektet</small>
        <h3 class="fw-semibold pt-2">Të gjitha <span class="txt-lightblue fw-semibold">Projektet</span></h3>
        <p class="text-muted">Pasqyra e projekteve aktive, të përfunduara dhe në pritje me buxhet, menaxher dhe afate.</p>
        <hr class="mb-4">
    @endif

    @if(isset($editProject))

        {{-- Update Project --}}
        <div class="card mb-4">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-diagram-project txt-lightblue py-2"></i> Përditëso Projektin</span>
            </div>
            <div class="card-body">
                <form action="{{ route('update.project') }}" method="post">
                    @csrf
                    <input type="hidden" name="id" value="{{ $editProject->id }}">
                    <div class="row g-3">
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="project_name" class="form-label">Emri i Projektit</label>
                            <input type="text" name="project_name" id="project_name" value="{{ old('project_name', $editProject->project_name) }}" class="form-control shadow-sm rounded-3" placeholder="Shkruani emrin e projektit...">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="project_manager" class="form-label">Menaxheri i Projektit</label>
                            <select name="project_manager" id="project_manager" class="form-control shadow-sm rounded-3">
                                <option value="" disabled {{ old('project_manager', $editProject->project_manager) ? '' : 'selected' }}>-- Zgjidh Menaxherin --</option>
                                @foreach($managers as $manager)
                                    <option value="{{ $manager->id }}" {{ old('project_manager', $editProject->project_manager) == $manager->id ? 'selected' : '' }}>{{ $manager->employee }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="client_id" class="form-label">Klienti</label>
                            <select name="client_id" id="client_id" class="form-control shadow-sm rounded-3">
                                <option value="" disabled {{ old('client_id', $editProject->client_id) ? '' : 'selected' }}>-- Zgjidh Klientin --</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ old('client_id', $editProject->client_id) == $client->id ? 'selected' : '' }}>{{ $client->client }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="location" class="form-label">Lokacioni</label>
                            <input type="text" name="location" id="location" value="{{ old('location', $editProject->location) }}" class="form-control shadow-sm rounded-3" placeholder="Shkruani lokacionin...">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="start_date" class="form-label">Data e Fillimit</label>
                            <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $editProject->start_date ? \Carbon\Carbon::parse($editProject->start_date)->format('Y-m-d') : '') }}" class="form-control shadow-sm rounded-3">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="expected_end_date" class="form-label">Data e Pritur e Përfundimit</label>
                            <input type="date" name="expected_end_date" id="expected_end_date" value="{{ old('expected_end_date', $editProject->expected_end_date ? \Carbon\Carbon::parse($editProject->expected_end_date)->format('Y-m-d') : '') }}" class="form-control shadow-sm rounded-3">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="budget" class="form-label">Buxheti</label>
                            <input type="number" step="0.01" name="budget" id="budget" value="{{ old('budget', $editProject->budget) }}" class="form-control shadow-sm rounded-3" placeholder="Shkruani buxhetin...">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="status" class="form-label">Statusi</label>
                            <select name="status" id="status" class="form-control shadow-sm rounded-3">
                                @foreach(['planning' => 'Në planifikim', 'in_progress' => 'Në progres', 'completed' => 'Përfunduar', 'on_hold' => 'Në pritje'] as $value => $label)
                                    <option value="{{ $value }}" {{ old('status', $editProject->status) === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="description" class="form-label">Përshkrimi</label>
                            <textarea name="description" id="description" class="form-control shadow-sm rounded-3" rows="5" placeholder="Shkruani përshkrimin...">{{ old('description', $editProject->description) }}</textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-check"></i> Përditëso Projektin</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @else

        {{-- Info Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-uppercase opacity-75"><i class="fa-solid fa-clipboard-question text-primary"></i> Aktive</small>
                        <div class="d-flex align-items-center justify-content-between gap-2">
                            <h4 class="fw-semibold mb-0">{{ $projectStats->active_count ?? 0 }}</h4>
                            <a href="{{ route('projects', ['search' => request('search'), 'statuses' => ['planning', 'in_progress']]) }}" class="btn btn-darkblue btn-sm rounded-3 shadow-sm"><i class="fa-solid fa-filter"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-uppercase opacity-75"><i class="fa-solid fa-clipboard-check text-success"></i> Përfunduar</small>
                        <div class="d-flex align-items-center justify-content-between gap-2">
                            <h4 class="fw-semibold mb-0">{{ $projectStats->completed_count ?? 0 }}</h4>
                            <a href="{{ route('projects', ['search' => request('search'), 'statuses' => ['completed']]) }}" class="btn btn-darkblue btn-sm rounded-3 shadow-sm"><i class="fa-solid fa-filter"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-uppercase opacity-75"><i class="fa-solid fa-pause-circle text-danger"></i> Në pritje</small>
                        <div class="d-flex align-items-center justify-content-between gap-2">
                            <h4 class="fw-semibold mb-0">{{ $projectStats->on_hold_count ?? 0 }}</h4>
                            <a href="{{ route('projects', ['search' => request('search'), 'statuses' => ['on_hold']]) }}" class="btn btn-darkblue btn-sm rounded-3 shadow-sm"><i class="fa-solid fa-filter"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Create Project --}}
        <div class="card mb-4">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-regular fa-bell txt-lightblue py-2"></i> Krijo Projekt</span>
            </div>
            <div class="card-body">
                <form action="{{ route('create.project') }}" method="post">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="project_name" class="form-label">Emri i Projektit</label>
                            <input type="text" name="project_name" id="project_name" value="{{ old('project_name') }}" class="form-control shadow-sm rounded-3" placeholder="Shkruani emrin e projektit...">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="project_manager" class="form-label">Menaxheri i Projektit</label>
                            <select name="project_manager" id="project_manager" class="form-control shadow-sm rounded-3">
                                <option value="" disabled {{ old('project_manager') ? '' : 'selected' }}>-- Zgjidh Menaxherin --</option>
                                @foreach($managers as $manager)
                                    <option value="{{ $manager->id }}" {{ old('project_manager') == $manager->id ? 'selected' : '' }}>{{ $manager->employee }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="client_id" class="form-label">Klienti</label>
                            <select name="client_id" id="client_id" class="form-control shadow-sm rounded-3">
                                <option value="" disabled {{ old('client_id') ? '' : 'selected' }}>-- Zgjidh Klientin --</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>{{ $client->client }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="location" class="form-label">Lokacioni</label>
                            <input type="text" name="location" id="location" value="{{ old('location') }}" class="form-control shadow-sm rounded-3" placeholder="Shkruani lokacionin...">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="start_date" class="form-label">Data e Fillimit</label>
                            <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}" class="form-control shadow-sm rounded-3">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="expected_end_date" class="form-label">Data e Pritur e Përfundimit</label>
                            <input type="date" name="expected_end_date" id="expected_end_date" value="{{ old('expected_end_date') }}" class="form-control shadow-sm rounded-3">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="budget" class="form-label">Buxheti</label>
                            <input type="number" step="0.01" name="budget" id="budget" value="{{ old('budget') }}" class="form-control shadow-sm rounded-3" placeholder="Shkruani buxhetin...">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="status" class="form-label">Statusi</label>
                            <select name="status" id="status" class="form-control shadow-sm rounded-3">
                                <option value="" disabled {{ old('status') ? '' : 'selected' }}>-- Zgjidh Statusin --</option>
                                @foreach(['planning' => 'Në planifikim', 'in_progress' => 'Në progres', 'completed' => 'Përfunduar', 'on_hold' => 'Në pritje'] as $value => $label)
                                    <option value="{{ $value }}" {{ old('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="description" class="form-label">Përshkrimi</label>
                            <textarea name="description" id="description" class="form-control shadow-sm rounded-3" rows="5" placeholder="Shkruani përshkrimin...">{{ old('description') }}</textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-plus"></i> Krijo Projektin</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Project List --}}
        <div class="card">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-list-ol txt-lightblue py-2"></i> Lista e Projekteve</span>
            </div>
            <div class="card-body">
                <form action="{{ route('projects') }}" method="get" class="input-group mb-3">
                    @foreach((array) request('statuses', []) as $status)
                        <input type="hidden" name="statuses[]" value="{{ $status }}">
                    @endforeach
                    <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-control shadow-sm rounded-start-3" placeholder="Kërko..."  aria-label="Kërko..." aria-describedby="basic-addon2">
                    <button type="submit" class="btn btn-darkblue shadow-sm rounded-end-3" id="basic-addon2"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th scope="col" class="text-nowrap">#</th>
                                <th scope="col" class="text-nowrap">Emri i Projektit</th>
                                <th scope="col" class="text-nowrap">Menaxheri</th>
                                <th scope="col" class="text-nowrap">Klienti</th>
                                <th scope="col" class="text-nowrap">Lokacioni</th>
                                <th scope="col" class="text-nowrap">Data e Fillimit</th>
                                <th scope="col" class="text-nowrap">Përfundimi i Pritur</th>
                                <th scope="col" class="text-nowrap">Buxheti</th>
                                <th scope="col" class="text-nowrap">Statusi</th>
                                <th scope="col" class="text-nowrap text-end">Veprimet</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($projects) === 0)
                                <tr>
                                    <td colspan="10" class="text-center"><i class="fa-regular fa-face-frown"></i> Nuk u gjetën të dhëna...</td>
                                </tr>
                            @else
                                @foreach ($projects as $project)
                                    <tr>
                                        <th scope="row" class="text-nowrap">{{ $project->id }}</th>
                                        <td class="text-nowrap">{{ $project->project_name }}</td>
                                        <td class="text-nowrap">{{ $project->manager_name }}</td>
                                        <td class="text-nowrap">{{ $project->client_name }}</td>
                                        <td class="text-nowrap">{{ $project->location }}</td>
                                        <td class="text-nowrap">{{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('d/m/Y') : '-' }}</td>
                                        <td class="text-nowrap">{{ $project->expected_end_date ? \Carbon\Carbon::parse($project->expected_end_date)->format('d/m/Y') : '-' }}</td>
                                        <td class="text-nowrap">{{ number_format((float) $project->budget, 2) }}€</td>
                                        <td class="text-nowrap">
                                            @if($project->status === 'planning')
                                                <span class="badge rounded-pill text-dark text-bg-warning bg-opacity-25 border border-warning">Në planifikim</span>
                                            @elseif($project->status === 'in_progress')
                                                <span class="badge rounded-pill text-primary text-bg-primary bg-opacity-25 border border-primary">Në progres</span>
                                            @elseif($project->status === 'completed')
                                                <span class="badge rounded-pill text-success text-bg-success bg-opacity-25 border border-success">Përfunduar</span>
                                            @else
                                                <span class="badge rounded-pill text-secondary text-bg-secondary bg-opacity-25 border border-secondary">Në pritje</span>
                                            @endif
                                        </td>
                                        <td class="text-nowrap text-end">
                                            <a href="{{ route('edit.project', $project->id) }}" class="btn btn-outline-success btn-sm rounded-3 shadow-sm"><i class="fa-solid fa-pen"></i></a>
                                            <button class="btn btn-outline-danger btn-sm rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $project->id }}"><i class="fa-solid fa-trash"></i></button>
                                            <a href="{{ route('project.details', $project->id) }}" class="btn btn-outline-dark btn-sm rounded-3 shadow-sm"><i class="fa-solid fa-eye"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                @if ($projects->hasPages())
                    <div class="mt-3">
                        {{ $projects->links('vendor.pagination.custom') }}
                    </div>
                @endif
            </div>
        </div>

        @if(count($projects) !== 0)

            <!-- Delete Modal -->
            @foreach ($projects as $project)
                <div class="modal fade" id="deleteModal{{ $project->id }}" tabindex="-1" aria-labelledby="deleteModal{{ $project->id }}Label" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-body text-center">
                                <p>A jeni i sigurt që dëshironi të fshini këtë projekt?</p>
                                <div class="mt-3">
                                    <a href="{{ route('delete.project', $project->id) }}" class="btn btn-danger btn-sm fw-semibold"><i class="fa-solid fa-check"></i> Konfirmo</a>
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
