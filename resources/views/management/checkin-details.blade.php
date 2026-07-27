@extends('template.template')

{{-- Page Title --}}
@section('title', 'Check In Details')

{{-- Page Content --}}
@section('content')
    <small class="d-block text-uppercase"><a href="{{ route('checkins') }}" class="text-decoration-none txt-lightblue fw-semibold"><i class="fa-solid fa-circle-chevron-left"></i> All Check In Requests</a></small>
    <h3 class="fw-semibold pt-2">Check In <span class="txt-lightblue fw-semibold">Details</span></h3>
    <p class="text-muted">Review check in and check out facts, timestamps, and captured location.</p>
    <hr class="mb-4">

    <div class="card">
        <div class="card-header rounded-top-4 bg-white">
            <span><i class="fa-solid fa-circle-info txt-lightblue py-2"></i> Request Details</span>
        </div>
        <div class="card-body py-4">
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-hashtag"></i> Request ID : </span>{{ $checkinDetails->id }}</span>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-user"></i> Employee : </span>{{ $checkinDetails->employee }}</span>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-phone"></i> Phone : </span>{{ $checkinDetails->phone }}</span>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-envelope"></i> Email : </span>{{ $checkinDetails->email }}</span>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> Status : </span>
                @if(strtolower((string) $checkinDetails->status) === 'accepted')
                    <span class="badge rounded-pill text-success text-bg-success bg-opacity-25 border border-success">{{ $checkinDetails->status }}</span>
                @else
                    <span class="badge rounded-pill text-dark text-bg-warning bg-opacity-25 border border-warning">{{ $checkinDetails->status }}</span>
                @endif
            </span>

            @if(session('role') !== 'employee')
                <div class="mb-3">
                    @if(strtolower((string) $checkinDetails->status) !== 'accepted')
                        <a href="{{ route('accept.checkin', $checkinDetails->id) }}" class="btn btn-success btn-sm rounded-3 shadow-sm"><i class="fa-solid fa-check"></i> Accept</a>
                    @endif
                    <a href="{{ route('delete.checkin', $checkinDetails->id) }}" class="btn btn-danger btn-sm rounded-3 shadow-sm"><i class="fa-solid fa-trash"></i> Delete</a>
                </div>
            @endif

            <hr>

            <h6 class="fw-semibold">Check In</h6>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-regular fa-clock"></i> Time : </span>{{ $checkinDetails->check_in_time ? \Carbon\Carbon::parse($checkinDetails->check_in_time)->format('d/m/Y H:i') : '-' }}</span>
            <span class="d-block mb-2">
                <span class="text-muted"><i class="fa-solid fa-location-dot"></i> Location : </span>
                @if(!empty($checkinDetails->check_in_location))
                    <a href="https://www.google.com/maps?q={{ urlencode($checkinDetails->check_in_location) }}" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                        <i class="fa-solid fa-up-right-from-square"></i> Open in Maps
                    </a>
                @else
                    -
                @endif
            </span>
            <div class="card card-body rounded-4 mb-4">
                @if(!empty($checkinDetails->check_in_fact))
                    <img src="{{ Storage::url($checkinDetails->check_in_fact) }}" alt="Check In Fact" class="img-fluid rounded-3 border" style="max-height: 420px; object-fit: contain;">
                @else
                    <span class="text-muted">No check in fact uploaded.</span>
                @endif
            </div>

            <h6 class="fw-semibold">Check Out</h6>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-regular fa-clock"></i> Time : </span>{{ $checkinDetails->check_out_time ? \Carbon\Carbon::parse($checkinDetails->check_out_time)->format('d/m/Y H:i') : '-' }}</span>
            <span class="d-block mb-2">
                <span class="text-muted"><i class="fa-solid fa-location-dot"></i> Location : </span>
                @if(!empty($checkinDetails->check_out_location))
                    <a href="https://www.google.com/maps?q={{ urlencode($checkinDetails->check_out_location) }}" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                        <i class="fa-solid fa-up-right-from-square"></i> Open in Maps
                    </a>
                @else
                    -
                @endif
            </span>
            <div class="card card-body rounded-4">
                @if(!empty($checkinDetails->check_out_fact))
                    <img src="{{ Storage::url($checkinDetails->check_out_fact) }}" alt="Check Out Fact" class="img-fluid rounded-3 border" style="max-height: 420px; object-fit: contain;">
                @else
                    <span class="text-muted">No check out fact uploaded yet.</span>
                @endif
            </div>
        </div>
    </div>
@endsection
