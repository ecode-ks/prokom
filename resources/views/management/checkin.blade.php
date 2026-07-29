@extends('template.template')

{{-- Page Title --}}
@section('title', 'Hyrje / Dalje nga Puna')

{{-- Page Content --}}
@section('content')
    <small class="d-block txt-lightblue fw-semibold text-uppercase">Prezenca</small>
    <h3 class="fw-semibold pt-2">Hyrje / Dalje nga Puna</h3>
    <p class="text-muted">Paraqisni kërkesën për hyrje ose dalje nga puna.</p>
    <hr class="mb-4">
    <div class="row g-4">

        {{-- Create Check In --}}
        <div class="card mb-4">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-brands fa-trello txt-lightblue py-2"></i> Regjistrimi i Hyrjes në Punë</span>
            </div>
            <div class="card-body">
                <form action="{{ route('create.checkin') }}" method="post">
                    @csrf
                    <div class="row g-3">
                        <input type="hidden" name="check_in_location" id="check_in_location" value="">
                        <input type="hidden" name="check_out_location" id="check_out_location" value="">
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="employee_id" class="form-label">Punëtori</label>
                            <select name="employee_id" id="employee_id" class="form-control shadow-sm rounded-3" required>
                                <option value="">-- Zgjidh Punëtorin --</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>{{ $employee->employee }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="check_in_time" class="form-label">Koha e Hyrjes</label>
                            <input type="text" name="check_in_time" id="check_in_time" value="{{ old('check_in_time') }}" class="form-control shadow-sm rounded-3" placeholder="Shkruani kohën e hyrjes...">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="pause_start_time" class="form-label">Koha e Fillimit të Pushimit</label>
                            <input type="text" name="pause_start_time" id="pause_start_time" value="{{ old('pause_start_time') }}" class="form-control shadow-sm rounded-3" placeholder="Shkruani kohën e fillimit të pushimit...">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="pause_end_time" class="form-label">Koha e Mbarimit të Pushimit</label>
                            <input type="text" name="pause_end_time" id="pause_end_time" value="{{ old('pause_end_time') }}" class="form-control shadow-sm rounded-3" placeholder="Shkruani kohën e mbarimit të pushimit...">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="check_out_time" class="form-label">Koha e Daljes nga Puna</label>
                            <input type="text" name="check_out_time" id="check_out_time" value="{{ old('check_out_time') }}" class="form-control shadow-sm rounded-3" placeholder="Shkruani kohën e daljes nga puna...">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-plus"></i> Regjistro Hyrje / Dalje</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Check Ins List --}}
        <div class="card">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-list-ol txt-lightblue py-2"></i> Lista e Prezencës</span>
            </div>
            <div class="card-body">
                <form action="{{ route('checkins') }}" method="get" class="input-group mb-3">
                    <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-control shadow-sm rounded-start-3" placeholder="Kërko..."  aria-label="Search..." aria-describedby="basic-addon2">
                    <button type="submit" class="btn btn-darkblue shadow-sm rounded-end-3" id="basic-addon2"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th scope="col" class="text-nowrap">#</th>
                                <th scope="col" class="text-nowrap">Punëtori</th>
                                <th scope="col" class="text-nowrap">Koha e Hyrjes</th>
                                <th scope="col" class="text-nowrap">Koha e Pushimit</th>
                                <th scope="col" class="text-nowrap">Koha e Daljes</th>
                                <th scope="col" class="text-nowrap text-end">Veprimet</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($checkIns) === 0)
                                <tr>
                                    <td colspan="6" class="text-center"><i class="fa-regular fa-face-frown"></i> Nuk u gjet asnjë e dhënë...</td>
                                </tr>
                            @else
                                @foreach ($checkIns as $checkIn)
                                    <tr>
                                        <th scope="row" class="text-nowrap">{{ $checkIn->id }}</th>
                                        <td class="text-nowrap">{{ $checkIn->employee->employee }}</td>
                                        <td class="text-nowrap">{{ $checkIn->check_in_time }}</td>
                                        <td class="text-nowrap">{{ $checkIn->pause_start_time }} - {{ $checkIn->pause_end_time }}</td>
                                        <td class="text-nowrap">{{ $checkIn->check_out_time }}</td>
                                        <td class="text-nowrap text-end">
                                            <a href="{{ route('edit.checkin', $checkIn->id) }}" class="btn btn-outline-success btn-sm rounded-3 shadow-sm"><i class="fa-solid fa-pen"></i></a>
                                            <button class="btn btn-outline-danger btn-sm rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $checkIn->id }}"><i class="fa-solid fa-trash"></i></button>
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
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const checkInForm = document.querySelector('form[action="{{ route('create.checkin') }}"]');
            const checkOutForm = document.querySelector('form[action="{{ route('create.checkin') }}"]');

            const wireLocationSubmit = function (form, locationFieldName) {
                if (!form) {
                    return;
                }

                form.addEventListener('submit', function (event) {
                    if (form.dataset.readyToSubmit === '1') {
                        return;
                    }

                    event.preventDefault();

                    const submitButton = form.querySelector('button[type="submit"]');
                    const originalButtonHtml = submitButton ? submitButton.innerHTML : '';
                    const locationInput = form.querySelector('input[name="' + locationFieldName + '"]');

                    if (!locationInput) {
                        alert('Location field is missing. Please refresh and try again.');
                        return;
                    }

                    const setSubmittingState = function (isSubmitting, label) {
                        if (!submitButton) {
                            return;
                        }

                        submitButton.disabled = isSubmitting;
                        submitButton.innerHTML = isSubmitting ? label : originalButtonHtml;
                    };

                    if (!navigator.geolocation) {
                        setSubmittingState(false, '');
                        alert('Geolocation is not supported in this browser. Request not submitted.');
                        return;
                    }

                    locationInput.value = '';
                    setSubmittingState(true, 'Requesting location...');

                    navigator.geolocation.getCurrentPosition(
                        function (position) {
                            locationInput.value = position.coords.latitude + ',' + position.coords.longitude;
                            setSubmittingState(true, 'Submitting...');
                            form.dataset.readyToSubmit = '1';
                            form.submit();
                        },
                        function () {
                            setSubmittingState(false, '');
                            alert('Location permission is required for this request. If you deny access, data will not be submitted. Please allow location and try again.');
                        },
                        {
                            enableHighAccuracy: true,
                            timeout: 10000,
                            maximumAge: 0,
                        }
                    );
                });
            };

            wireLocationSubmit(checkInForm, 'check_in_location');
            wireLocationSubmit(checkOutForm, 'check_out_location');
        });
    </script>
@endpush
