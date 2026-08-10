@extends('template.template')

{{-- Page Title --}}
@section('title', 'Detajet e Check-in')

{{-- Page Content --}}
@section('content')
    <small class="d-block text-uppercase"><a href="{{ route('checkins') }}" class="text-decoration-none txt-lightblue fw-semibold"><i class="fa-solid fa-circle-chevron-left"></i> Kthehu</a></small>
    <h3 class="fw-semibold pt-2">Detajet e Check-in</h3>
    <p class="text-muted">Shiko kohët, lokacionin dhe statusin e check-in.</p>
    <hr class="mb-4">

    <div class="card">
        <div class="card-header rounded-top-4 bg-white">
            <span><i class="fa-solid fa-circle-info txt-lightblue py-2"></i> Detajet</span>
        </div>
        <div class="card-body py-4">
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-hashtag"></i> ID : </span>{{ $checkinDetails->id }}</span>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-user"></i> Punëtori : </span>{{ $checkinDetails->employee }}</span>
            @if(!empty($checkinDetails->phone))
                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-phone"></i> Telefoni : </span>{{ $checkinDetails->phone }}</span>
            @endif
            @if(!empty($checkinDetails->email))
                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-envelope"></i> Email : </span>{{ $checkinDetails->email }}</span>
            @endif
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> Statusi : </span>
                @if(strtolower((string) $checkinDetails->status) === 'accepted')
                    <span class="badge rounded-pill text-success text-bg-success bg-opacity-25 border border-success">{{ $checkinDetails->status }}</span>
                @else
                    <span class="badge rounded-pill text-dark text-bg-warning bg-opacity-25 border border-warning">{{ $checkinDetails->status ?: 'Under Review' }}</span>
                @endif
            </span>

            @if(session('role') !== 'employee')
                <div class="mb-3 mt-3">
                    @if(strtolower((string) $checkinDetails->status) !== 'accepted')
                        <a href="{{ route('accept.checkin', $checkinDetails->id) }}" class="btn btn-success btn-sm rounded-3 shadow-sm"><i class="fa-solid fa-check"></i> Prano</a>
                    @endif
                    <a href="{{ route('delete.checkin', $checkinDetails->id) }}" class="btn btn-danger btn-sm rounded-3 shadow-sm"><i class="fa-solid fa-trash"></i> Fshi</a>
                </div>
            @endif

            <hr>

            <h6 class="fw-semibold">Check-in</h6>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-regular fa-clock"></i> Koha : </span>{{ $checkinDetails->check_in_time ? \Carbon\Carbon::parse($checkinDetails->check_in_time)->format('d/m/Y H:i') : '-' }}</span>
            <span class="d-block mb-3">
                <span class="text-muted"><i class="fa-solid fa-location-dot"></i> Lokacioni : </span>
                @if(!empty($checkinDetails->check_in_location))
                    <a href="https://www.google.com/maps?q={{ urlencode($checkinDetails->check_in_location) }}" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                        <i class="fa-solid fa-up-right-from-square"></i> Hap në Maps
                    </a>
                @else
                    -
                @endif
            </span>

            <h6 class="fw-semibold">Pauza</h6>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-regular fa-clock"></i> Fillimi : </span>{{ $checkinDetails->pause_start_time ? \Carbon\Carbon::parse($checkinDetails->pause_start_time)->format('d/m/Y H:i') : '-' }}</span>
            <span class="d-block mb-2">
                <span class="text-muted"><i class="fa-solid fa-location-dot"></i> Lok. fillimit : </span>
                @if(!empty($checkinDetails->pause_start_location))
                    <a href="https://www.google.com/maps?q={{ urlencode($checkinDetails->pause_start_location) }}" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                        <i class="fa-solid fa-up-right-from-square"></i> Hap në Maps
                    </a>
                @else
                    -
                @endif
            </span>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-regular fa-clock"></i> Mbarimi : </span>{{ $checkinDetails->pause_end_time ? \Carbon\Carbon::parse($checkinDetails->pause_end_time)->format('d/m/Y H:i') : '-' }}</span>
            <span class="d-block mb-3">
                <span class="text-muted"><i class="fa-solid fa-location-dot"></i> Lok. mbarimit : </span>
                @if(!empty($checkinDetails->pause_end_location))
                    <a href="https://www.google.com/maps?q={{ urlencode($checkinDetails->pause_end_location) }}" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                        <i class="fa-solid fa-up-right-from-square"></i> Hap në Maps
                    </a>
                @else
                    -
                @endif
            </span>

            <h6 class="fw-semibold">Check-out</h6>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-regular fa-clock"></i> Koha : </span>{{ $checkinDetails->check_out_time ? \Carbon\Carbon::parse($checkinDetails->check_out_time)->format('d/m/Y H:i') : '-' }}</span>
            <span class="d-block mb-2">
                <span class="text-muted"><i class="fa-solid fa-location-dot"></i> Lokacioni : </span>
                @if(!empty($checkinDetails->check_out_location))
                    <a href="https://www.google.com/maps?q={{ urlencode($checkinDetails->check_out_location) }}" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                        <i class="fa-solid fa-up-right-from-square"></i> Hap në Maps
                    </a>
                @else
                    -
                @endif
            </span>
        </div>
    </div>
@endsection
