@extends('template.template')

@section('title', __('pages.task_report_details'))

@section('content')
    <small class="d-block text-uppercase"><a href="{{ route('task.reports') }}" class="text-decoration-none txt-lightblue fw-semibold"><i class="fa-solid fa-circle-chevron-left"></i> {{ __('pages.all_reports') }}</a></small>
    <h3 class="fw-semibold pt-2">{{ __('pages.details_of') }} <span class="txt-lightblue fw-semibold">{{ __('pages.report_word') }}</span></h3>
    <p class="text-muted">{{ __('pages.task_report_details_lead') }}</p>
    <hr class="mb-4">

    <div class="card">
        <div class="card-header rounded-top-4 bg-white">
            <span><i class="fa-solid fa-circle-info txt-lightblue py-2"></i> {{ __('pages.task_report_details') }}</span>
        </div>
        <div class="card-body py-4">
            @php
                $taskStatus = strtolower((string) ($reportDetails->task_status ?? ''));
                $taskStatusLabels = [
                    'new' => __('pages.status_new'),
                    'working' => __('pages.status_working'),
                    'in_progress' => __('pages.status_working'),
                    'review' => __('pages.status_review'),
                    'blocked' => __('pages.status_blocked'),
                    'completed' => __('pages.completed'),
                ];
                $taskStatusLabel = $taskStatusLabels[$taskStatus] ?? ucwords(str_replace('_', ' ', $reportDetails->task_status ?? '-'));
            @endphp

            @if($reportDetails->status === 'active' || $reportDetails->status === 'new')
                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> {{ __('pages.report_status') }} : </span><span class="badge rounded-pill text-primary text-bg-primary bg-opacity-25 border border-primary">{{ __('pages.status_active') }}</span></span>
            @elseif($reportDetails->status === 'under_review')
                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> {{ __('pages.report_status') }} : </span><span class="badge rounded-pill text-dark text-bg-warning bg-opacity-25 border border-warning">{{ __('pages.status_review') }}</span></span>
            @elseif($reportDetails->status === 'resolved')
                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> {{ __('pages.report_status') }} : </span><span class="badge rounded-pill text-success text-bg-success bg-opacity-25 border border-success">{{ __('pages.status_resolved') }}</span></span>
            @else
                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> {{ __('pages.report_status') }} : </span><span class="badge rounded-pill text-secondary text-bg-secondary bg-opacity-25 border border-secondary">{{ __('pages.status_closed') }}</span></span>
            @endif

            <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-list-check"></i> {{ __('pages.task_title') }} : </span>{{ $reportDetails->title }}</span>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-diagram-project"></i> {{ __('pages.project') }} : </span>{{ $reportDetails->project_name }}</span>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-user-tag"></i> {{ __('pages.employees') }} : </span>{{ $reportDetails->assigned_to_names ?? '-' }}</span>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-regular fa-circle-user"></i> {{ __('pages.assigned_by_cap') }} : </span>{{ $reportDetails->assigned_by_name ?? '-' }}</span>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-flag"></i> {{ __('pages.task_status') }} : </span>{{ $taskStatusLabel }}</span>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-regular fa-calendar"></i> {{ __('pages.task_deadline') }} : </span>{{ !empty($reportDetails->deadline) ? \Carbon\Carbon::parse($reportDetails->deadline)->format('d/m/Y') : '-' }}</span>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-pen"></i> {{ __('pages.reported_by') }} : </span>{{ $reportDetails->reporter_name ?? '-' }}</span>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-regular fa-calendar"></i> {{ __('pages.report_date') }} : </span>{{ \Carbon\Carbon::parse($reportDetails->date)->format('d/m/Y - H:i') }}</span>

            <span class="d-block mt-4 mb-3 text-muted"><i class="fa-regular fa-file-lines"></i> {{ __('pages.task_description') }}</span>
            <div class="card card-body rounded-4">
                {!! nl2br(e($reportDetails->task)) !!}
            </div>

            <span class="d-block mt-4 mb-3 text-muted"><i class="fa-regular fa-file-lines"></i> {{ __('pages.report') }}</span>
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

            <span class="d-block mt-4 mb-3 text-muted"><i class="fa-regular fa-image"></i> {{ __('pages.images_facts') }}</span>
            @if(count($images) === 0)
                <div class="card card-body rounded-4">
                    <span>{{ __('pages.no_images') }}</span>
                </div>
            @else
                <div class="report-images">
                    @foreach($images as $image)
                        <a href="{{ asset('storage/' . $image) }}" class="report-images__item" data-report-lightbox aria-label="{{ __('pages.open_image') }}">
                            <img src="{{ asset('storage/' . $image) }}" alt="{{ __('pages.report_image') }}">
                        </a>
                    @endforeach
                </div>
            @endif

            @if(in_array($role, ['company', 'manager', 'admin'], true))
                <hr class="my-3">
                <span class="d-block mb-2"><i class="fa-solid fa-arrows-turn-to-dots"></i> {{ __('pages.update_status') }}</span>
                <a href="{{ route('update.task.report.status', ['id' => $reportDetails->id, 'status' => 'active']) }}" class="btn btn-primary btn-sm rounded-4 shadow-sm m-1 {{ ($reportDetails->status === 'active' || $reportDetails->status === 'new') ? 'disabled' : '' }}"><i class="fa-solid fa-list-check"></i> {{ __('pages.status_active') }}</a>
                <a href="{{ route('update.task.report.status', ['id' => $reportDetails->id, 'status' => 'under_review']) }}" class="btn btn-warning btn-sm rounded-4 shadow-sm m-1 {{ $reportDetails->status === 'under_review' ? 'disabled' : '' }}"><i class="fa-solid fa-magnifying-glass"></i> {{ __('pages.status_review') }}</a>
                <a href="{{ route('update.task.report.status', ['id' => $reportDetails->id, 'status' => 'resolved']) }}" class="btn btn-success btn-sm rounded-4 shadow-sm m-1 {{ $reportDetails->status === 'resolved' ? 'disabled' : '' }}"><i class="fa-solid fa-check"></i> {{ __('pages.status_resolved') }}</a>
                <a href="{{ route('update.task.report.status', ['id' => $reportDetails->id, 'status' => 'closed']) }}" class="btn btn-secondary btn-sm rounded-4 shadow-sm m-1 {{ $reportDetails->status === 'closed' ? 'disabled' : '' }}"><i class="fa-solid fa-lock"></i> {{ __('pages.status_closed') }}</a>
            @endif
        </div>
    </div>
@endsection
