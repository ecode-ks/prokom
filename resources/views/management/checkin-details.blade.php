@extends('template.template')

{{-- Page Title --}}
@section('title', __('pages.checkin_details_title'))

{{-- Page Content --}}
@section('content')
    <small class="d-block text-uppercase"><a href="{{ route('checkins') }}" class="text-decoration-none txt-lightblue fw-semibold"><i class="fa-solid fa-circle-chevron-left"></i> {{ __('app.back') }}</a></small>
    <h3 class="fw-semibold pt-2">{{ __('pages.checkin_details_title') }}</h3>
    <p class="text-muted">{{ __('pages.checkin_details_lead') }}</p>
    <hr class="mb-4">

    <div class="card">
        <div class="card-header rounded-top-4 bg-white">
            <span><i class="fa-solid fa-circle-info txt-lightblue py-2"></i> {{ __('pages.checkin_details') }}</span>
        </div>
        <div class="card-body py-4">
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-hashtag"></i> ID : </span>{{ $checkinDetails->id }}</span>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-user"></i> {{ __('app.employee') }} : </span>{{ $checkinDetails->employee }}</span>
            @if(!empty($checkinDetails->phone))
                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-phone"></i> {{ __('app.phone') }} : </span>{{ $checkinDetails->phone }}</span>
            @endif
            @if(!empty($checkinDetails->email))
                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-envelope"></i> {{ __('app.email') }} : </span>{{ $checkinDetails->email }}</span>
            @endif
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> {{ __('app.status') }} : </span>
                @if(strtolower((string) $checkinDetails->status) === 'accepted')
                    <span class="badge rounded-pill text-success text-bg-success bg-opacity-25 border border-success">{{ __('pages.checkin_accepted') }}</span>
                @else
                    <span class="badge rounded-pill text-dark text-bg-warning bg-opacity-25 border border-warning">{{ __('pages.checkin_review') }}</span>
                @endif
            </span>

            @if(session('role') !== 'employee')
                <div class="mb-3 mt-3">
                    @if(strtolower((string) $checkinDetails->status) !== 'accepted' && !empty($checkinDetails->check_out_time))
                        <a href="{{ route('accept.checkin', $checkinDetails->id) }}" class="btn btn-success btn-sm rounded-3 shadow-sm"><i class="fa-solid fa-check"></i> {{ __('pages.checkin_accept') }}</a>
                    @endif
                    <a href="{{ route('delete.checkin', $checkinDetails->id) }}" class="btn btn-danger btn-sm rounded-3 shadow-sm"><i class="fa-solid fa-trash"></i> {{ __('pages.checkin_delete') }}</a>
                </div>
            @endif

            <hr>

            <h6 class="fw-semibold">{{ __('pages.dash_checkin') }}</h6>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-regular fa-clock"></i> {{ __('pages.checkin_time') }} : </span>{{ $checkinDetails->check_in_time ? \Carbon\Carbon::parse($checkinDetails->check_in_time)->format('d/m/Y H:i') : '-' }}</span>
            <span class="d-block mb-3">
                <span class="text-muted"><i class="fa-solid fa-location-dot"></i> {{ __('app.location') }} : </span>
                @if(!empty($checkinDetails->check_in_location))
                    <a href="https://www.google.com/maps?q={{ urlencode($checkinDetails->check_in_location) }}" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                        <i class="fa-solid fa-up-right-from-square"></i> {{ __('pages.checkin_open_maps') }}
                    </a>
                @else
                    -
                @endif
            </span>

            <h6 class="fw-semibold">{{ __('pages.checkin_pause') }}</h6>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-regular fa-clock"></i> {{ __('pages.checkin_pause_start') }} : </span>{{ $checkinDetails->pause_start_time ? \Carbon\Carbon::parse($checkinDetails->pause_start_time)->format('d/m/Y H:i') : '-' }}</span>
            <span class="d-block mb-2">
                <span class="text-muted"><i class="fa-solid fa-location-dot"></i> {{ __('pages.checkin_pause_start_loc') }} : </span>
                @if(!empty($checkinDetails->pause_start_location))
                    <a href="https://www.google.com/maps?q={{ urlencode($checkinDetails->pause_start_location) }}" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                        <i class="fa-solid fa-up-right-from-square"></i> {{ __('pages.checkin_open_maps') }}
                    </a>
                @else
                    -
                @endif
            </span>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-regular fa-clock"></i> {{ __('pages.checkin_pause_end') }} : </span>{{ $checkinDetails->pause_end_time ? \Carbon\Carbon::parse($checkinDetails->pause_end_time)->format('d/m/Y H:i') : '-' }}</span>
            <span class="d-block mb-3">
                <span class="text-muted"><i class="fa-solid fa-location-dot"></i> {{ __('pages.checkin_pause_end_loc') }} : </span>
                @if(!empty($checkinDetails->pause_end_location))
                    <a href="https://www.google.com/maps?q={{ urlencode($checkinDetails->pause_end_location) }}" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                        <i class="fa-solid fa-up-right-from-square"></i> {{ __('pages.checkin_open_maps') }}
                    </a>
                @else
                    -
                @endif
            </span>

            <h6 class="fw-semibold">{{ __('pages.checkin_checkout') }}</h6>
            <span class="d-block mb-2"><span class="text-muted"><i class="fa-regular fa-clock"></i> {{ __('pages.checkin_time') }} : </span>{{ $checkinDetails->check_out_time ? \Carbon\Carbon::parse($checkinDetails->check_out_time)->format('d/m/Y H:i') : '-' }}</span>
            <span class="d-block mb-2">
                <span class="text-muted"><i class="fa-solid fa-location-dot"></i> {{ __('app.location') }} : </span>
                @if(!empty($checkinDetails->check_out_location))
                    <a href="https://www.google.com/maps?q={{ urlencode($checkinDetails->check_out_location) }}" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                        <i class="fa-solid fa-up-right-from-square"></i> {{ __('pages.checkin_open_maps') }}
                    </a>
                @else
                    -
                @endif
            </span>
        </div>
    </div>
@endsection
