@extends('template.template')

{{-- Page Title --}}
@section('title', __('pages.project_details'))

{{-- Page Content --}}
@section('content')
    <small class="d-block text-uppercase"><a href="{{ route('projects') }}" class="text-decoration-none txt-lightblue fw-semibold"><i class="fa-solid fa-circle-chevron-left"></i> {{ __('pages.all_projects') }}</a></small>
    <h3 class="fw-semibold pt-2">{{ __('pages.details_of') }} <span class="txt-lightblue fw-semibold">{{ __('pages.project_word') }}</span></h3>
    <p class="text-muted">{{ __('pages.project_details_lead') }}</p>
    <hr class="mb-4">

    {{-- Project Details --}}
    <div class="card">
        <div class="card-header rounded-top-4 bg-white">
            <span><i class="fa-solid fa-circle-info txt-lightblue py-2"></i> {{ __('pages.project_details') }}</span>
        </div>
        <div class="card-body py-4">
            @if($projectDetails->status === 'planning')
                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> {{ __('app.status') }} : </span><span class="badge rounded-pill text-dark text-bg-warning bg-opacity-25 border border-warning">{{ __('pages.status_planning') }}</span></span>
            @elseif($projectDetails->status === 'in_progress')
                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> {{ __('app.status') }} : </span><span class="badge rounded-pill text-primary text-bg-primary bg-opacity-25 border border-primary">{{ __('app.in_progress') }}</span></span>
            @elseif($projectDetails->status === 'completed')
                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> {{ __('app.status') }} : </span><span class="badge rounded-pill text-success text-bg-success bg-opacity-25 border border-success">{{ __('app.completed') }}</span></span>
            @else
                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> {{ __('app.status') }} : </span><span class="badge rounded-pill text-secondary text-bg-secondary bg-opacity-25 border border-secondary">{{ __('app.pending') }}</span></span>
            @endif
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> {{ __('pages.project_col') }} : </span>{{ $projectDetails->project_name }}</span>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-user-tag"></i> {{ __('pages.project_manager') }} : </span>{{ $projectDetails->manager_name }}</span>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-regular fa-circle-user"></i> {{ __('pages.client') }} : </span>{{ $projectDetails->client_name }}</span>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-location-dot"></i> {{ __('app.location') }} : </span>{{ $projectDetails->location }}</span>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-regular fa-calendar"></i> {{ __('pages.start_date') }} : </span>{{ \Carbon\Carbon::parse($projectDetails->start_date)->format('d/m/Y') }}</span>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-regular fa-calendar"></i> {{ __('pages.expected_end_date') }} : </span>{{ \Carbon\Carbon::parse($projectDetails->expected_end_date)->format('d/m/Y') }}</span>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-money-check-dollar"></i> {{ __('pages.budget') }} : </span>{{ number_format((float) $projectDetails->budget, 2) }} EUR</span>
            <span class="d-block mt-4 mb-3 text-muted"><i class="fa-regular fa-file-lines"></i> {{ __('app.description') }}</span>
            <div class="card card-body rounded-4">
                <span>{!! nl2br(e($projectDetails->description ?? __('pages.no_description'))) !!}</span>
            </div>
            <div class="text-end mt-3">
                <small class="text-muted"><i class="fa-solid fa-pen"></i> {{ __('pages.created_on', ['date' => \Carbon\Carbon::parse($projectDetails->created_at)->format('d/m/Y')]) }}</small>
            </div>
            <hr class="my-3">
            @if(($role ?? session('role')) !== 'manager')
            <span class="d-block mb-2"><i class="fa-solid fa-arrows-turn-to-dots"></i> {{ __('pages.update_status') }}</span>
            <a href="{{ route('update.project.status', ['id' => $projectDetails->id, 'status' => 'completed']) }}" class="btn btn-success btn-sm rounded-4 shadow-sm m-1 {{ $projectDetails->status === 'completed' ? 'disabled' : '' }}"><i class="fa-solid fa-check"></i> {{ __('app.completed') }}</a>
            <a href="{{ route('update.project.status', ['id' => $projectDetails->id, 'status' => 'in_progress']) }}" class="btn btn-primary btn-sm rounded-4 shadow-sm m-1 {{ $projectDetails->status === 'in_progress' ? 'disabled' : '' }}"><i class="fa-solid fa-clock"></i> {{ __('app.in_progress') }}</a>
            <a href="{{ route('update.project.status', ['id' => $projectDetails->id, 'status' => 'on_hold']) }}" class="btn btn-secondary btn-sm rounded-4 shadow-sm m-1 {{ $projectDetails->status === 'on_hold' ? 'disabled' : '' }}"><i class="fa-solid fa-times"></i> {{ __('app.pending') }}</a>
            <a href="{{ route('update.project.status', ['id' => $projectDetails->id, 'status' => 'planning']) }}" class="btn btn-warning btn-sm rounded-4 shadow-sm m-1 {{ $projectDetails->status === 'planning' ? 'disabled' : '' }}"><i class="fa-solid fa-list-check"></i> {{ __('pages.status_planning') }}</a>
            @endif
        </div>
    </div>
@endsection
