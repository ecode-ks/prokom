@extends('template.template')

@section('title', 'Detajet e Raportit të Detyrës')

@section('content')
    <small class="d-block text-uppercase"><a href="{{ route('task.reports') }}" class="text-decoration-none txt-lightblue fw-semibold"><i class="fa-solid fa-circle-chevron-left"></i> Të gjitha Raportet</a></small>
    <h3 class="fw-semibold pt-2">Detajet e <span class="txt-lightblue fw-semibold">Raportit</span></h3>
    <p class="text-muted">Pasqyra e detajuar e raportit të zgjedhur të detyrës.</p>
    <hr class="mb-4">

    <div class="card">
        <div class="card-header rounded-top-4 bg-white">
            <span><i class="fa-solid fa-circle-info txt-lightblue py-2"></i> Detajet e Raportit të Detyrës</span>
        </div>
        <div class="card-body py-4">
            @php
                $taskStatus = strtolower((string) ($reportDetails->task_status ?? ''));
                $taskStatusLabels = [
                    'new' => 'E re',
                    'working' => 'Në punë',
                    'in_progress' => 'Në punë',
                    'review' => 'Në shqyrtim',
                    'blocked' => 'Bllokuar',
                    'completed' => 'Përfunduar',
                ];
                $taskStatusLabel = $taskStatusLabels[$taskStatus] ?? ucwords(str_replace('_', ' ', $reportDetails->task_status ?? '-'));
            @endphp

            @if($reportDetails->status === 'active' || $reportDetails->status === 'new')
                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> Statusi i Raportit : </span><span class="badge rounded-pill text-primary text-bg-primary bg-opacity-25 border border-primary">Aktive</span></span>
            @elseif($reportDetails->status === 'under_review')
                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> Statusi i Raportit : </span><span class="badge rounded-pill text-dark text-bg-warning bg-opacity-25 border border-warning">Në shqyrtim</span></span>
            @elseif($reportDetails->status === 'resolved')
                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> Statusi i Raportit : </span><span class="badge rounded-pill text-success text-bg-success bg-opacity-25 border border-success">I zgjidhur</span></span>
            @else
                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> Statusi i Raportit : </span><span class="badge rounded-pill text-secondary text-bg-secondary bg-opacity-25 border border-secondary">Mbyllur</span></span>
            @endif

            <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-list-check"></i> Titulli i Detyrës : </span>{{ $reportDetails->title }}</span>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-diagram-project"></i> Projekti : </span>{{ $reportDetails->project_name }}</span>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-user-tag"></i> Punonjësi : </span>{{ $reportDetails->assigned_to_name ?? '-' }}</span>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-regular fa-circle-user"></i> Caktuar Nga : </span>{{ $reportDetails->assigned_by_name ?? '-' }}</span>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-flag"></i> Statusi i Detyrës : </span>{{ $taskStatusLabel }}</span>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-regular fa-calendar"></i> Afati i Detyrës : </span>{{ !empty($reportDetails->deadline) ? \Carbon\Carbon::parse($reportDetails->deadline)->format('d/m/Y') : '-' }}</span>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-pen"></i> Raportuar Nga : </span>{{ $reportDetails->reporter_name ?? '-' }}</span>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-regular fa-calendar"></i> Data e Raportit : </span>{{ \Carbon\Carbon::parse($reportDetails->date)->format('d/m/Y - H:i') }}</span>

            <span class="d-block mt-4 mb-3 text-muted"><i class="fa-regular fa-file-lines"></i> Përshkrimi i Detyrës</span>
            <div class="card card-body rounded-4">
                {!! nl2br(e($reportDetails->task)) !!}
            </div>

            <span class="d-block mt-4 mb-3 text-muted"><i class="fa-regular fa-file-lines"></i> Raporti</span>
            <div class="card card-body rounded-4">
                <span>{!! nl2br(e($reportDetails->report)) !!}</span>
            </div>

            @php
                $images = [];
                if (!empty($reportDetails->images)) {
                    $decodedImages = json_decode($reportDetails->images, true);
                    if (is_array($decodedImages)) {
                        $images = $decodedImages;
                    }
                }
            @endphp

            <span class="d-block mt-4 mb-3 text-muted"><i class="fa-regular fa-image"></i> Imazhet / Faktet</span>
            @if(count($images) === 0)
                <div class="card card-body rounded-4">
                    <span>Nuk ka imazhe të bashkëngjitura.</span>
                </div>
            @else
                <div class="row g-3">
                    @foreach($images as $image)
                        <div class="col-12 col-md-6 col-lg-4">
                            <a href="{{ asset('storage/' . $image) }}" target="_blank" class="text-decoration-none">
                                <img src="{{ asset('storage/' . $image) }}" alt="Imazh i raportit" class="img-fluid rounded-4 border shadow-sm">
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif

            @if(in_array($role, ['company', 'manager', 'admin'], true))
                <hr class="my-3">
                <span class="d-block mb-2"><i class="fa-solid fa-arrows-turn-to-dots"></i> Përditëso Statusin</span>
                <a href="{{ route('update.task.report.status', ['id' => $reportDetails->id, 'status' => 'active']) }}" class="btn btn-primary btn-sm rounded-4 shadow-sm m-1 {{ ($reportDetails->status === 'active' || $reportDetails->status === 'new') ? 'disabled' : '' }}"><i class="fa-solid fa-list-check"></i> Aktive</a>
                <a href="{{ route('update.task.report.status', ['id' => $reportDetails->id, 'status' => 'under_review']) }}" class="btn btn-warning btn-sm rounded-4 shadow-sm m-1 {{ $reportDetails->status === 'under_review' ? 'disabled' : '' }}"><i class="fa-solid fa-magnifying-glass"></i> Në shqyrtim</a>
                <a href="{{ route('update.task.report.status', ['id' => $reportDetails->id, 'status' => 'resolved']) }}" class="btn btn-success btn-sm rounded-4 shadow-sm m-1 {{ $reportDetails->status === 'resolved' ? 'disabled' : '' }}"><i class="fa-solid fa-check"></i> I zgjidhur</a>
                <a href="{{ route('update.task.report.status', ['id' => $reportDetails->id, 'status' => 'closed']) }}" class="btn btn-secondary btn-sm rounded-4 shadow-sm m-1 {{ $reportDetails->status === 'closed' ? 'disabled' : '' }}"><i class="fa-solid fa-lock"></i> Mbyllur</a>
            @endif
        </div>
    </div>
@endsection
