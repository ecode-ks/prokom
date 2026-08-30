@extends('template.template')

@section('title', 'Detajet e Raportit të Projektit')

@section('content')
    <small class="d-block text-uppercase"><a href="{{ route('project.reports') }}" class="text-decoration-none txt-lightblue fw-semibold"><i class="fa-solid fa-circle-chevron-left"></i> Të gjitha Raportet</a></small>
    <h3 class="fw-semibold pt-2">Detajet e <span class="txt-lightblue fw-semibold">Raportit</span></h3>
    <p class="text-muted">Pasqyra e detajuar e raportit të zgjedhur të projektit.</p>
    <hr class="mb-4">

    <div class="card">
        <div class="card-header rounded-top-4 bg-white">
            <span><i class="fa-solid fa-circle-info txt-lightblue py-2"></i> Detajet e Raportit të Projektit</span>
        </div>
        <div class="card-body py-4">
            @php
                $projectStatus = strtolower((string) ($reportDetails->project_status ?? ''));
                $projectStatusLabels = [
                    'planning' => 'Në planifikim',
                    'in_progress' => 'Në progres',
                    'completed' => 'Përfunduar',
                    'on_hold' => 'Në pritje',
                ];
                $projectStatusLabel = $projectStatusLabels[$projectStatus] ?? ucwords(str_replace('_', ' ', $reportDetails->project_status ?? '-'));
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

            <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-diagram-project"></i> Projekti : </span>{{ $reportDetails->project_name }}</span>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-user-tag"></i> Menaxheri : </span>{{ $reportDetails->manager_name ?? '-' }}</span>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-regular fa-circle-user"></i> Klienti : </span>{{ $reportDetails->client_name ?? '-' }}</span>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-location-dot"></i> Lokacioni : </span>{{ $reportDetails->location ?? '-' }}</span>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-flag"></i> Statusi i Projektit : </span>{{ $projectStatusLabel }}</span>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-money-check-dollar"></i> Buxheti : </span>{{ $reportDetails->budget !== null ? number_format((float) $reportDetails->budget, 2) . '€' : '-' }}</span>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-regular fa-calendar"></i> Data e Fillimit : </span>{{ !empty($reportDetails->start_date) ? \Carbon\Carbon::parse($reportDetails->start_date)->format('d/m/Y') : '-' }}</span>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-regular fa-calendar"></i> Përfundimi i Pritur : </span>{{ !empty($reportDetails->expected_end_date) ? \Carbon\Carbon::parse($reportDetails->expected_end_date)->format('d/m/Y') : '-' }}</span>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-pen"></i> Raportuar Nga : </span>{{ $reportDetails->reporter_name ?? '-' }}</span>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-regular fa-calendar"></i> Data e Raportit : </span>{{ \Carbon\Carbon::parse($reportDetails->date)->format('d/m/Y - H:i') }}</span>

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
                <div class="report-images">
                    @foreach($images as $image)
                        <a href="{{ asset('storage/' . $image) }}" class="report-images__item" data-report-lightbox aria-label="Hap imazhin">
                            <img src="{{ asset('storage/' . $image) }}" alt="Imazh i raportit">
                        </a>
                    @endforeach
                </div>
            @endif

            @if(in_array($role, ['company', 'manager', 'admin'], true))
                <hr class="my-3">
                <span class="d-block mb-2"><i class="fa-solid fa-arrows-turn-to-dots"></i> Përditëso Statusin</span>
                <a href="{{ route('update.project.report.status', ['id' => $reportDetails->id, 'status' => 'active']) }}" class="btn btn-primary btn-sm rounded-4 shadow-sm m-1 {{ ($reportDetails->status === 'active' || $reportDetails->status === 'new') ? 'disabled' : '' }}"><i class="fa-solid fa-list-check"></i> Aktive</a>
                <a href="{{ route('update.project.report.status', ['id' => $reportDetails->id, 'status' => 'under_review']) }}" class="btn btn-warning btn-sm rounded-4 shadow-sm m-1 {{ $reportDetails->status === 'under_review' ? 'disabled' : '' }}"><i class="fa-solid fa-magnifying-glass"></i> Në shqyrtim</a>
                <a href="{{ route('update.project.report.status', ['id' => $reportDetails->id, 'status' => 'resolved']) }}" class="btn btn-success btn-sm rounded-4 shadow-sm m-1 {{ $reportDetails->status === 'resolved' ? 'disabled' : '' }}"><i class="fa-solid fa-check"></i> I zgjidhur</a>
                <a href="{{ route('update.project.report.status', ['id' => $reportDetails->id, 'status' => 'closed']) }}" class="btn btn-secondary btn-sm rounded-4 shadow-sm m-1 {{ $reportDetails->status === 'closed' ? 'disabled' : '' }}"><i class="fa-solid fa-lock"></i> Mbyllur</a>
            @endif
        </div>
    </div>
@endsection
