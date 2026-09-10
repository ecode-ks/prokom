@extends('template.template')

{{-- Page Title --}}
@section('title', __('app.nav_checkin'))

{{-- Page Content --}}
@section('content')
    <small class="d-block txt-lightblue fw-semibold text-uppercase">{{ __('app.nav_checkin') }}</small>
    <h3 class="fw-semibold pt-2">{{ __('pages.checkin_heading') }}</h3>
    <p class="text-muted">{{ __('pages.checkin_lead') }}</p>
    <hr class="mb-4">

    <div class="card">
        <div class="card-header rounded-top-4 bg-white">
            <span><i class="fa-solid fa-list-ol txt-lightblue py-2"></i> {{ __('pages.checkin_list') }}</span>
        </div>
        <div class="card-body">
            <form action="{{ route('checkins') }}" method="get" class="input-group mb-3">
                <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-control shadow-sm rounded-start-3" placeholder="{{ __('app.search') }}" aria-label="{{ __('app.search') }}" aria-describedby="basic-addon2">
                <button type="submit" class="btn btn-darkblue shadow-sm rounded-end-3" id="basic-addon2"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th scope="col" class="text-nowrap">#</th>
                            <th scope="col" class="text-nowrap">{{ __('app.employee') }}</th>
                            <th scope="col" class="text-nowrap">{{ __('pages.checkin_in') }}</th>
                            <th scope="col" class="text-nowrap">{{ __('pages.checkin_pause') }}</th>
                            <th scope="col" class="text-nowrap">{{ __('pages.checkin_out') }}</th>
                            <th scope="col" class="text-nowrap">{{ __('app.status') }}</th>
                            <th scope="col" class="text-nowrap text-end">{{ __('app.operations') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($checkIns) === 0)
                            <tr>
                                <td colspan="7" class="text-center"><i class="fa-regular fa-face-frown"></i> {{ __('app.no_data') }}</td>
                            </tr>
                        @else
                            @foreach ($checkIns as $checkIn)
                                @php
                                    $isAccepted = strtolower((string) ($checkIn->status ?? '')) === 'accepted';
                                @endphp
                                <tr>
                                    <th scope="row" class="text-nowrap">{{ $checkIn->id }}</th>
                                    <td class="text-nowrap">{{ $checkIn->employee }}</td>
                                    <td class="text-nowrap">{{ $checkIn->check_in_time ? \Carbon\Carbon::parse($checkIn->check_in_time)->format('d/m/Y H:i') : '-' }}</td>
                                    <td class="text-nowrap">
                                        {{ $checkIn->pause_start_time ? \Carbon\Carbon::parse($checkIn->pause_start_time)->format('H:i') : '--:--' }}
                                        -
                                        {{ $checkIn->pause_end_time ? \Carbon\Carbon::parse($checkIn->pause_end_time)->format('H:i') : '--:--' }}
                                    </td>
                                    <td class="text-nowrap">{{ $checkIn->check_out_time ? \Carbon\Carbon::parse($checkIn->check_out_time)->format('d/m/Y H:i') : '-' }}</td>
                                    <td class="text-nowrap">
                                        @if($isAccepted)
                                            <span class="badge rounded-pill text-success text-bg-success bg-opacity-25 border border-success">{{ __('pages.checkin_accepted') }}</span>
                                        @else
                                            <span class="badge rounded-pill text-dark text-bg-warning bg-opacity-25 border border-warning">{{ __('pages.checkin_review') }}</span>
                                        @endif
                                    </td>
                                    <td class="text-nowrap text-end">
                                        @if(!$isAccepted && !empty($checkIn->check_out_time))
                                            <a href="{{ route('accept.checkin', $checkIn->id) }}" class="btn btn-outline-success btn-sm rounded-3 shadow-sm"><i class="fa-solid fa-check"></i></a>
                                        @endif
                                        <button class="btn btn-outline-danger btn-sm rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $checkIn->id }}"><i class="fa-solid fa-trash"></i></button>
                                        <a href="{{ route('checkin.details', $checkIn->id) }}" class="btn btn-outline-dark btn-sm rounded-3 shadow-sm"><i class="fa-solid fa-eye"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            @if ($checkIns->hasPages())
                <div class="mt-3">
                    {{ $checkIns->links('vendor.pagination.custom') }}
                </div>
            @endif
        </div>
    </div>

    @if(count($checkIns) !== 0)
        @foreach ($checkIns as $checkIn)
            <div class="modal fade" id="deleteModal{{ $checkIn->id }}" tabindex="-1" aria-labelledby="deleteModal{{ $checkIn->id }}Label" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-body text-center">
                            <p>{{ __('pages.checkin_delete_confirm') }}</p>
                            <div class="mt-3">
                                <a href="{{ route('delete.checkin', $checkIn->id) }}" class="btn btn-danger btn-sm fw-semibold"><i class="fa-solid fa-check"></i> {{ __('app.confirm') }}</a>
                                <button class="btn btn-success btn-sm fw-semibold" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> {{ __('app.cancel') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
@endsection
