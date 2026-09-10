@extends('template.template')

{{-- Page Title --}}
@section('title', __('pages.task_details'))

{{-- Page Content --}}
@section('content')
    <small class="d-block text-uppercase"><a href="{{ route('tasks') }}" class="text-decoration-none txt-lightblue fw-semibold"><i class="fa-solid fa-circle-chevron-left"></i> {{ __('pages.all_tasks') }}</a></small>
    <h3 class="fw-semibold pt-2">{{ __('pages.details_of') }} <span class="txt-lightblue fw-semibold">{{ __('pages.task_word') }}</span></h3>
    <p class="text-muted">{{ __('pages.tasks_lead') }}</p>
    <hr class="mb-4">

    {{-- Task Details --}}
    <div class="card">
        <div class="card-header rounded-top-4 bg-white">
            <span><i class="fa-solid fa-circle-info txt-lightblue py-2"></i> {{ __('pages.task_details') }}</span>
        </div>
        <div class="card-body py-4">
            @php
                $status = strtolower((string) ($taskDetails->status ?? ''));
            @endphp

            @if($status === 'completed')
                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> {{ __('app.status') }} : </span><span class="badge rounded-pill text-success text-bg-success bg-opacity-25 border border-success">{{ __('app.completed') }}</span></span>
            @elseif($status === 'working' || $status === 'in_progress' || $status === 'in progress')
                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> {{ __('app.status') }} : </span><span class="badge rounded-pill text-primary text-bg-primary bg-opacity-25 border border-primary">{{ __('pages.status_working') }}</span></span>
            @elseif($status === 'review' || $status === 'reviewing')
                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> {{ __('app.status') }} : </span><span class="badge rounded-pill text-dark text-bg-warning bg-opacity-25 border border-warning">{{ __('pages.status_review') }}</span></span>
            @elseif($status === 'blocked' || $status === 'on_hold')
                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> {{ __('app.status') }} : </span><span class="badge rounded-pill text-danger text-bg-danger bg-opacity-25 border border-danger">{{ __('pages.status_blocked') }}</span></span>
            @else
                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> {{ __('app.status') }} : </span><span class="badge rounded-pill text-secondary text-bg-secondary bg-opacity-25 border border-secondary">{{ __('pages.status_new') }}</span></span>
            @endif

            <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-diagram-project"></i> {{ __('pages.project') }} : </span>{{ $taskDetails->project_name }}</span>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-heading"></i> {{ __('pages.title_col') }} : </span>{{ $taskDetails->title }}</span>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-user-tag"></i> {{ __('pages.employees') }} : </span>{{ $taskDetails->assigned_to_names ?? '-' }}</span>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-regular fa-circle-user"></i> {{ __('pages.assigned_by') }} : </span>{{ $taskDetails->assigned_by_name ?? '-' }}</span>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-regular fa-calendar"></i> {{ __('pages.deadline_col') }} : </span>{{ \Carbon\Carbon::parse($taskDetails->deadline)->format('d/m/Y') }}</span>
            <span class="d-block mt-4 mb-3 text-muted"><i class="fa-regular fa-file-lines"></i> {{ __('app.description') }}</span>
            <div class="card card-body rounded-4">
                <span>{!! nl2br(e($taskDetails->task)) !!}</span>
            </div>
            <div class="text-end mt-3">
                <small class="text-muted"><i class="fa-solid fa-pen"></i> {{ __('pages.assigned_on', ['date' => \Carbon\Carbon::parse($taskDetails->date)->format('d/m/Y')]) }}</small>
            </div>
            <hr class="my-3">
            <span class="d-block mb-2"><i class="fa-solid fa-arrows-turn-to-dots"></i> {{ __('pages.update_status') }}</span>

            @php
                $status = strtolower((string) ($taskDetails->status ?? ''));
                $statusButtons = [
                    'new' => ['label' => __('pages.status_new'), 'btnClass' => 'btn-secondary', 'icon' => 'fa-circle-dot'],
                    'working' => ['label' => __('pages.status_working'), 'btnClass' => 'btn-primary', 'icon' => 'fa-clock'],
                    'review' => ['label' => __('pages.status_review'), 'btnClass' => 'btn-warning', 'icon' => 'fa-list-check'],
                    'blocked' => ['label' => __('pages.status_blocked'), 'btnClass' => 'btn-danger', 'icon' => 'fa-triangle-exclamation'],
                    'completed' => ['label' => __('app.completed'), 'btnClass' => 'btn-success', 'icon' => 'fa-check'],
                ];
            @endphp

            @foreach($statusButtons as $statusValue => $statusMeta)
                <a href="{{ route('update.task.status', ['id' => $taskDetails->id, 'status' => $statusValue]) }}" class="btn {{ $statusMeta['btnClass'] }} btn-sm rounded-4 shadow-sm m-1 {{ $status === $statusValue ? 'disabled' : '' }}"><i class="fa-solid {{ $statusMeta['icon'] }}"></i> {{ $statusMeta['label'] }}</a>
            @endforeach
        </div>
    </div>
@endsection
