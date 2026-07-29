@extends('template.template')

{{-- Page Title --}}
@section('title', 'Detajet e Projektit')

{{-- Page Content --}}
@section('content')
    <small class="d-block text-uppercase"><a href="{{ route('projects') }}" class="text-decoration-none txt-lightblue fw-semibold"><i class="fa-solid fa-circle-chevron-left"></i> Të gjitha Projektet</a></small>
    <h3 class="fw-semibold pt-2">Detajet e <span class="txt-lightblue fw-semibold">Projektit</span></h3>
    <p class="text-muted">Pasqyra e projektit me buxhet, ekip dhe progres.</p>
    <hr class="mb-4">

    {{-- Project Details --}}
    <div class="card">
        <div class="card-header rounded-top-4 bg-white">
            <span><i class="fa-solid fa-circle-info txt-lightblue py-2"></i> Detajet e Projektit</span>
        </div>
        <div class="card-body py-4">
            @if($projectDetails->status === 'planning')
                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> Statusi : </span><span class="badge rounded-pill text-dark text-bg-warning bg-opacity-25 border border-warning">Në planifikim</span></span>
            @elseif($projectDetails->status === 'in_progress')
                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> Statusi : </span><span class="badge rounded-pill text-primary text-bg-primary bg-opacity-25 border border-primary">Në progres</span></span>
            @elseif($projectDetails->status === 'completed')
                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> Statusi : </span><span class="badge rounded-pill text-success text-bg-success bg-opacity-25 border border-success">Përfunduar</span></span>
            @else
                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> Statusi : </span><span class="badge rounded-pill text-secondary text-bg-secondary bg-opacity-25 border border-secondary">Në pritje</span></span>
            @endif
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> Projekti : </span>{{ $projectDetails->project_name }}</span>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-user-tag"></i> Menaxheri i Projektit : </span>{{ $projectDetails->manager_name }}</span>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-regular fa-circle-user"></i> Klienti : </span>{{ $projectDetails->client_name }}</span>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-location-dot"></i> Lokacioni : </span>{{ $projectDetails->location }}</span>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-regular fa-calendar"></i> Data e Fillimit : </span>{{ \Carbon\Carbon::parse($projectDetails->start_date)->format('d/m/Y') }}</span>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-regular fa-calendar"></i> Data e Pritur e Përfundimit : </span>{{ \Carbon\Carbon::parse($projectDetails->expected_end_date)->format('d/m/Y') }}</span>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-money-check-dollar"></i> Buxheti : </span>{{ number_format((float) $projectDetails->budget, 2) }} EUR</span>
            <span class="d-block mt-4 mb-3 text-muted"><i class="fa-regular fa-file-lines"></i> Përshkrimi</span>
            <div class="card card-body rounded-4">
                <span>{!! nl2br(e($projectDetails->description ?? 'Nuk ka përshkrim.')) !!}</span>
            </div>
            <div class="text-end mt-3">
                <small class="text-muted"><i class="fa-solid fa-pen"></i> Krijuar më {{ \Carbon\Carbon::parse($projectDetails->created_at)->format('d/m/Y') }}</small>
            </div>
            <hr class="my-3">
            <span class="d-block mb-2"><i class="fa-solid fa-arrows-turn-to-dots"></i> Përditëso Statusin</span>
            <a href="{{ route('update.project.status', ['id' => $projectDetails->id, 'status' => 'completed']) }}" class="btn btn-success btn-sm rounded-4 shadow-sm m-1 {{ $projectDetails->status === 'completed' ? 'disabled' : '' }}"><i class="fa-solid fa-check"></i> Përfunduar</a>
            <a href="{{ route('update.project.status', ['id' => $projectDetails->id, 'status' => 'in_progress']) }}" class="btn btn-primary btn-sm rounded-4 shadow-sm m-1 {{ $projectDetails->status === 'in_progress' ? 'disabled' : '' }}"><i class="fa-solid fa-clock"></i> Në progres</a>
            <a href="{{ route('update.project.status', ['id' => $projectDetails->id, 'status' => 'on_hold']) }}" class="btn btn-secondary btn-sm rounded-4 shadow-sm m-1 {{ $projectDetails->status === 'on_hold' ? 'disabled' : '' }}"><i class="fa-solid fa-times"></i> Në pritje</a>
            <a href="{{ route('update.project.status', ['id' => $projectDetails->id, 'status' => 'planning']) }}" class="btn btn-warning btn-sm rounded-4 shadow-sm m-1 {{ $projectDetails->status === 'planning' ? 'disabled' : '' }}"><i class="fa-solid fa-list-check"></i> Në planifikim</a>
        </div>
    </div>
@endsection
