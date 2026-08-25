@extends('template.template')

{{-- Page Title --}}
@section('title', 'Prezenca')

{{-- Page Content --}}
@section('content')
    <small class="d-block txt-lightblue fw-semibold text-uppercase">Prezenca</small>
    <h3 class="fw-semibold pt-2">Lista e Check-in</h3>
    <p class="text-muted">Shiko dhe prano check-in e punonjësve.</p>
    <hr class="mb-4">

    <div class="card">
        <div class="card-header rounded-top-4 bg-white">
            <span><i class="fa-solid fa-list-ol txt-lightblue py-2"></i> Lista e Prezencës</span>
        </div>
        <div class="card-body">
            <form action="{{ route('checkins') }}" method="get" class="input-group mb-3">
                <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-control shadow-sm rounded-start-3" placeholder="Kërko..." aria-label="Search..." aria-describedby="basic-addon2">
                <button type="submit" class="btn btn-darkblue shadow-sm rounded-end-3" id="basic-addon2"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th scope="col" class="text-nowrap">#</th>
                            <th scope="col" class="text-nowrap">Punëtori</th>
                            <th scope="col" class="text-nowrap">Hyrja</th>
                            <th scope="col" class="text-nowrap">Pauza</th>
                            <th scope="col" class="text-nowrap">Dalja</th>
                            <th scope="col" class="text-nowrap">Statusi</th>
                            <th scope="col" class="text-nowrap text-end">Veprimet</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($checkIns) === 0)
                            <tr>
                                <td colspan="7" class="text-center"><i class="fa-regular fa-face-frown"></i> Nuk u gjet asnjë e dhënë...</td>
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
                                            <span class="badge rounded-pill text-success text-bg-success bg-opacity-25 border border-success">Pranuar</span>
                                        @else
                                            <span class="badge rounded-pill text-dark text-bg-warning bg-opacity-25 border border-warning">Në shqyrtim</span>
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
                            <p>A jeni i sigurt që dëshironi ta fshini këtë check-in?</p>
                            <div class="mt-3">
                                <a href="{{ route('delete.checkin', $checkIn->id) }}" class="btn btn-danger btn-sm fw-semibold"><i class="fa-solid fa-check"></i> Konfirmo</a>
                                <button class="btn btn-success btn-sm fw-semibold" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> Anulo</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
@endsection
