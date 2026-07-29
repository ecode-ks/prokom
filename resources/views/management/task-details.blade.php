@extends('template.template')

{{-- Page Title --}}
@section('title', 'Detajet e Detyrës')

{{-- Page Content --}}
@section('content')
    <small class="d-block text-uppercase"><a href="{{ route('tasks') }}" class="text-decoration-none txt-lightblue fw-semibold"><i class="fa-solid fa-circle-chevron-left"></i> Të gjitha Detyrat</a></small>
    <h3 class="fw-semibold pt-2">Detajet e <span class="txt-lightblue fw-semibold">Detyrës</span></h3>
    <p class="text-muted">Ndjek progresin, menaxho afatet dhe mbaji detyrat në orar.</p>
    <hr class="mb-4">

    {{-- Task Details --}}
    <div class="card">
        <div class="card-header rounded-top-4 bg-white">
            <span><i class="fa-solid fa-circle-info txt-lightblue py-2"></i> Detajet e Detyrës</span>
        </div>
        <div class="card-body py-4">
            @php
                $status = strtolower((string) ($taskDetails->status ?? ''));
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
                $statusLabel = $statusLabels[$status] ?? ucwords(str_replace('_', ' ', $taskDetails->status ?? '-'));
            @endphp

            @if($status === 'completed')
                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> Statusi : </span><span class="badge rounded-pill text-success text-bg-success bg-opacity-25 border border-success">{{ $statusLabel }}</span></span>
            @elseif($status === 'working' || $status === 'in_progress' || $status === 'in progress')
                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> Statusi : </span><span class="badge rounded-pill text-primary text-bg-primary bg-opacity-25 border border-primary">{{ $statusLabel }}</span></span>
            @elseif($status === 'review' || $status === 'reviewing')
                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> Statusi : </span><span class="badge rounded-pill text-dark text-bg-warning bg-opacity-25 border border-warning">{{ $statusLabel }}</span></span>
            @elseif($status === 'blocked' || $status === 'on_hold')
                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> Statusi : </span><span class="badge rounded-pill text-danger text-bg-danger bg-opacity-25 border border-danger">{{ $statusLabel }}</span></span>
            @else
                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> Statusi : </span><span class="badge rounded-pill text-secondary text-bg-secondary bg-opacity-25 border border-secondary">{{ $statusLabel }}</span></span>
            @endif

            <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-diagram-project"></i> Projekti : </span>{{ $taskDetails->project_name }}</span>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-heading"></i> Titulli : </span>{{ $taskDetails->title }}</span>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-user-tag"></i> Punonjësit : </span>{{ $taskDetails->assigned_to_names ?? '-' }}</span>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-regular fa-circle-user"></i> Caktuar nga : </span>{{ $taskDetails->assigned_by_name ?? '-' }}</span>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-regular fa-calendar"></i> Afati : </span>{{ \Carbon\Carbon::parse($taskDetails->deadline)->format('d/m/Y') }}</span>
            <span class="d-block mt-4 mb-3 text-muted"><i class="fa-regular fa-file-lines"></i> Përshkrimi</span>
            <div class="card card-body rounded-4">
                <span>{!! nl2br(e($taskDetails->task)) !!}</span>
            </div>
            <div class="text-end mt-3">
                <small class="text-muted"><i class="fa-solid fa-pen"></i> Caktuar më {{ \Carbon\Carbon::parse($taskDetails->date)->format('d/m/Y') }}</small>
            </div>
            <hr class="my-3">
            <span class="d-block mb-2"><i class="fa-solid fa-arrows-turn-to-dots"></i> Përditëso Statusin</span>

            @php
                $status = strtolower((string) ($taskDetails->status ?? ''));
                $statusButtons = [
                    'new' => ['label' => 'E re', 'btnClass' => 'btn-secondary', 'icon' => 'fa-circle-dot'],
                    'working' => ['label' => 'Në punë', 'btnClass' => 'btn-primary', 'icon' => 'fa-clock'],
                    'review' => ['label' => 'Në shqyrtim', 'btnClass' => 'btn-warning', 'icon' => 'fa-list-check'],
                    'blocked' => ['label' => 'Bllokuar', 'btnClass' => 'btn-danger', 'icon' => 'fa-triangle-exclamation'],
                    'completed' => ['label' => 'Përfunduar', 'btnClass' => 'btn-success', 'icon' => 'fa-check'],
                ];
            @endphp

            @foreach($statusButtons as $statusValue => $statusMeta)
                <a href="{{ route('update.task.status', ['id' => $taskDetails->id, 'status' => $statusValue]) }}" class="btn {{ $statusMeta['btnClass'] }} btn-sm rounded-4 shadow-sm m-1 {{ $status === $statusValue ? 'disabled' : '' }}"><i class="fa-solid {{ $statusMeta['icon'] }}"></i> {{ $statusMeta['label'] }}</a>
            @endforeach
        </div>
    </div>
@endsection
