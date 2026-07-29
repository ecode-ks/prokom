@extends('template.template')

@section('title', 'Project Report Details')

@section('content')
    <small class="d-block text-uppercase"><a href="{{ route('project.reports') }}" class="text-decoration-none txt-lightblue fw-semibold"><i class="fa-solid fa-circle-chevron-left"></i> All Project Reports</a></small>
    <h3 class="fw-semibold pt-2">Project Report <span class="txt-lightblue fw-semibold">Details</span></h3>
    <p class="text-muted">Detailed view of the selected project report.</p>
    <hr class="mb-4">

    <div class="card">
        <div class="card-header rounded-top-4 bg-white">
            <span><i class="fa-solid fa-circle-info txt-lightblue py-2"></i> Project Report Details</span>
        </div>
        <div class="card-body py-4">
            @if($reportDetails->status === 'active' || $reportDetails->status === 'new')
                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> Status : </span><span class="badge rounded-pill text-primary text-bg-primary bg-opacity-25 border border-primary">Active</span></span>
            @elseif($reportDetails->status === 'under_review')
                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> Status : </span><span class="badge rounded-pill text-dark text-bg-warning bg-opacity-25 border border-warning">Under Review</span></span>
            @elseif($reportDetails->status === 'resolved')
                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> Status : </span><span class="badge rounded-pill text-success text-bg-success bg-opacity-25 border border-success">Resolved</span></span>
            @else
                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> Status : </span><span class="badge rounded-pill text-secondary text-bg-secondary bg-opacity-25 border border-secondary">Closed</span></span>
            @endif

            <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-diagram-project"></i> Project : </span>{{ $reportDetails->project_name }}</span>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-regular fa-circle-user"></i> Reported By : </span>{{ $reportDetails->reporter_name ?? '-' }}</span>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-regular fa-calendar"></i> Date : </span>{{ \Carbon\Carbon::parse($reportDetails->date)->format('d/m/Y - H:i') }}</span>

            <span class="d-block mt-4 mb-3 text-muted"><i class="fa-regular fa-file-lines"></i> Report</span>
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

            <span class="d-block mt-4 mb-3 text-muted"><i class="fa-regular fa-image"></i> Images / Facts</span>
            @if(count($images) === 0)
                <div class="card card-body rounded-4">
                    <span>No images attached.</span>
                </div>
            @else
                <div class="row g-3">
                    @foreach($images as $image)
                        <div class="col-12 col-md-6 col-lg-4">
                            <a href="{{ asset('storage/' . $image) }}" target="_blank" class="text-decoration-none">
                                <img src="{{ asset('storage/' . $image) }}" alt="Report image" class="img-fluid rounded-4 border shadow-sm">
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif

            @if(in_array($role, ['company', 'manager', 'admin'], true))
                <hr class="my-3">
                <span class="d-block mb-2"><i class="fa-solid fa-arrows-turn-to-dots"></i> Update Status</span>
                <a href="{{ route('update.project.report.status', ['id' => $reportDetails->id, 'status' => 'active']) }}" class="btn btn-primary btn-sm rounded-4 shadow-sm m-1 {{ ($reportDetails->status === 'active' || $reportDetails->status === 'new') ? 'disabled' : '' }}"><i class="fa-solid fa-list-check"></i> Active</a>
                <a href="{{ route('update.project.report.status', ['id' => $reportDetails->id, 'status' => 'under_review']) }}" class="btn btn-warning btn-sm rounded-4 shadow-sm m-1 {{ $reportDetails->status === 'under_review' ? 'disabled' : '' }}"><i class="fa-solid fa-magnifying-glass"></i> Under Review</a>
                <a href="{{ route('update.project.report.status', ['id' => $reportDetails->id, 'status' => 'resolved']) }}" class="btn btn-success btn-sm rounded-4 shadow-sm m-1 {{ $reportDetails->status === 'resolved' ? 'disabled' : '' }}"><i class="fa-solid fa-check"></i> Resolved</a>
                <a href="{{ route('update.project.report.status', ['id' => $reportDetails->id, 'status' => 'closed']) }}" class="btn btn-secondary btn-sm rounded-4 shadow-sm m-1 {{ $reportDetails->status === 'closed' ? 'disabled' : '' }}"><i class="fa-solid fa-lock"></i> Closed</a>
            @endif
        </div>
    </div>
@endsection
