@extends('template.template')

@section('title', isset($editReport) ? 'Përditëso Raportin e Detyrës' : 'Raportet e Detyrave')

@section('content')
    @if(isset($editReport))
        <small class="d-block txt-lightblue fw-semibold text-uppercase"><a href="{{ route('task.reports') }}" class="text-decoration-none txt-lightblue fw-semibold"><i class="fa-solid fa-circle-chevron-left"></i> Kthehu mbrapa</a></small>
        <h3 class="fw-semibold pt-2">Përditëso Raportin e Detyrës</h3>
        <p class="text-muted">Përditëso të dhënat e raportit të zgjedhur.</p>
        <hr class="mb-4">
    @else
        <small class="d-block txt-lightblue fw-semibold text-uppercase">Detyrat</small>
        <h3 class="fw-semibold pt-2">Raportet e <span class="txt-lightblue fw-semibold">Detyrave</span></h3>
        <p class="text-muted">Krijo dhe ndiq raportet e ekzekutimit të detyrave.</p>
        <hr class="mb-4">
    @endif

    @if(isset($editReport))

        <div class="card mb-4">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-file-lines txt-lightblue py-2"></i> Përditëso Raportin e Detyrës</span>
            </div>
            <div class="card-body">
                <form action="{{ route('update.task.report') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{ $editReport->id }}">
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label for="task_id" class="form-label">Detyra</label>
                            <select name="task_id" id="task_id" class="form-control shadow-sm rounded-3" required>
                                <option value="">-- Zgjidh Detyrën --</option>
                                @foreach($tasks as $task)
                                    <option value="{{ $task->id }}" {{ old('task_id', $editReport->task_id) == $task->id ? 'selected' : '' }}>
                                        #{{ $task->id }} - {{ \Illuminate\Support\Str::limit($task->title, 45) }} ({{ $task->project_name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-8">
                            <label for="images" class="form-label">Imazhet / Faktet <small class="text-muted">(opsionale – lëreni bosh për të ruajtur ekzistueset)</small></label>
                            <input type="file" name="images[]" id="images" class="form-control shadow-sm rounded-3" multiple accept="image/*">
                            @php
                                $existingImages = json_decode($editReport->images ?? '[]', true);
                            @endphp
                            @if(!empty($existingImages) && is_array($existingImages))
                                <small class="d-block text-muted mt-1">{{ count($existingImages) }} imazh(e) ekzistuese</small>
                            @endif
                        </div>
                        <div class="col-12">
                            <label for="report" class="form-label">Raporti</label>
                            <textarea name="report" id="report" class="form-control shadow-sm rounded-3" rows="5" placeholder="Shkruani detajet e raportit..." required>{{ old('report', $editReport->report) }}</textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-check"></i> Përditëso Raportin</button>
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
                        <small class="text-uppercase opacity-75"><i class="fa-solid fa-list-check text-primary"></i> Aktive</small>
                        <h4 class="fw-semibold mb-0">{{ $reportStats->active_count ?? 0 }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-uppercase opacity-75"><i class="fa-solid fa-magnifying-glass text-warning"></i> Në shqyrtim</small>
                        <h4 class="fw-semibold mb-0">{{ $reportStats->under_review_count ?? 0 }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-uppercase opacity-75"><i class="fa-solid fa-check text-success"></i> Të zgjidhura</small>
                        <h4 class="fw-semibold mb-0">{{ $reportStats->resolved_count ?? 0 }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-uppercase opacity-75"><i class="fa-solid fa-lock text-secondary"></i> Mbyllura</small>
                        <h4 class="fw-semibold mb-0">{{ $reportStats->closed_count ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-file-lines txt-lightblue py-2"></i> Krijo Raport Detyre</span>
            </div>
            <div class="card-body">
                <form action="{{ route('create.task.report') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label for="task_id" class="form-label">Detyra</label>
                            <select name="task_id" id="task_id" class="form-control shadow-sm rounded-3" required>
                                <option value="">-- Zgjidh Detyrën --</option>
                                @foreach($tasks as $task)
                                    <option value="{{ $task->id }}" {{ old('task_id') == $task->id ? 'selected' : '' }}>
                                        #{{ $task->id }} - {{ \Illuminate\Support\Str::limit($task->title, 45) }} ({{ $task->project_name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-8">
                            <label for="images" class="form-label">Imazhet / Faktet (opsionale)</label>
                            <input type="file" name="images[]" id="images" class="form-control shadow-sm rounded-3" multiple accept="image/*">
                        </div>
                        <div class="col-12">
                            <label for="report" class="form-label">Raporti</label>
                            <textarea name="report" id="report" class="form-control shadow-sm rounded-3" rows="5" placeholder="Shkruani detajet e raportit..." required>{{ old('report') }}</textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-plus"></i> Krijo Raportin</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-list-ol txt-lightblue py-2"></i> Lista e Raporteve të Detyrave</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th scope="col" class="text-nowrap">#</th>
                                <th scope="col" class="text-nowrap">Data</th>
                                <th scope="col" class="text-nowrap">Titulli i Detyrës</th>
                                <th scope="col" class="text-nowrap">Projekti</th>
                                <th scope="col" class="text-nowrap">Punonjësit</th>
                                <th scope="col" class="text-nowrap">Raportuar Nga</th>
                                <th scope="col" class="text-nowrap">Statusi</th>
                                <th scope="col" class="text-nowrap text-end">Veprimet</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($reports) === 0)
                                <tr>
                                    <td colspan="8" class="text-center"><i class="fa-regular fa-face-frown"></i> Nuk u gjetën të dhëna...</td>
                                </tr>
                            @else
                                @foreach($reports as $report)
                                    <tr>
                                        <th scope="row" class="text-nowrap">{{ $report->id }}</th>
                                        <td class="text-nowrap">{{ \Carbon\Carbon::parse($report->date)->format('d/m/Y') }}</td>
                                        <td class="text-truncate" style="max-width: 260px;">{{ $report->title }}</td>
                                        <td class="text-nowrap">{{ $report->project_name }}</td>
                                        <td class="text-nowrap">{{ $report->assigned_to_names ?? '-' }}</td>
                                        <td class="text-nowrap">{{ $report->reporter_name ?? '-' }}</td>
                                        <td class="text-nowrap">
                                            @if($report->status === 'active' || $report->status === 'new')
                                                <span class="badge rounded-pill text-primary text-bg-primary bg-opacity-25 border border-primary">Aktive</span>
                                            @elseif($report->status === 'under_review')
                                                <span class="badge rounded-pill text-dark text-bg-warning bg-opacity-25 border border-warning">Në shqyrtim</span>
                                            @elseif($report->status === 'resolved')
                                                <span class="badge rounded-pill text-success text-bg-success bg-opacity-25 border border-success">I zgjidhur</span>
                                            @else
                                                <span class="badge rounded-pill text-secondary text-bg-secondary bg-opacity-25 border border-secondary">Mbyllur</span>
                                            @endif
                                        </td>
                                        <td class="text-nowrap text-end">
                                            <a href="{{ route('edit.task.report', $report->id) }}" class="btn btn-outline-success btn-sm rounded-3 shadow-sm"><i class="fa-solid fa-pen"></i></a>
                                            <button class="btn btn-outline-danger btn-sm rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $report->id }}"><i class="fa-solid fa-trash"></i></button>
                                            <a href="{{ route('task.report.details', $report->id) }}" class="btn btn-outline-dark btn-sm rounded-3 shadow-sm"><i class="fa-solid fa-eye"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>

                @if(method_exists($reports, 'hasPages') && $reports->hasPages())
                    <div class="mt-3">
                        {{ $reports->links('vendor.pagination.custom') }}
                    </div>
                @endif
            </div>
        </div>

        @if(count($reports) !== 0)
            @foreach($reports as $report)
                <div class="modal fade" id="deleteModal{{ $report->id }}" tabindex="-1" aria-labelledby="deleteModal{{ $report->id }}Label" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-body text-center">
                                <p>A jeni i sigurt që dëshironi të fshini këtë raport?</p>
                                <div class="mt-3">
                                    <a href="{{ route('delete.task.report', $report->id) }}" class="btn btn-danger btn-sm fw-semibold"><i class="fa-solid fa-check"></i> Konfirmo</a>
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
