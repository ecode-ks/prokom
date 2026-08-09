@extends('template.template')

{{-- Page Title --}}
@section('title', 'Orët e Punës')

{{-- Page Content --}}
@section('content')
    <small class="d-block txt-lightblue fw-semibold text-uppercase">Prezenca</small>
    <h3 class="fw-semibold pt-2">Orët e Punës</h3>
    <p class="text-muted">Lista e orëve të punës të miratuara.</p>
    <hr class="mb-4">

    @php
        $monthHours = ((float) ($workingHoursStats->month_minutes ?? 0)) / 60;
        $monthDays = (int) ($workingHoursStats->month_days_count ?? 0);
        $avgHoursPerDay = $monthDays > 0 ? $monthHours / $monthDays : 0;
    @endphp

    {{-- Info Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-uppercase opacity-75"><i class="fa-solid fa-calendar-day text-primary"></i> Regjistrime sot</small>
                    <h4 class="fw-semibold mb-0">{{ $workingHoursStats->today_count ?? 0 }}</h4>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-uppercase opacity-75"><i class="fa-solid fa-clock text-success"></i> Orë këtë muaj</small>
                    <h4 class="fw-semibold mb-0">{{ number_format($monthHours, 1) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-uppercase opacity-75"><i class="fa-solid fa-users text-info"></i> Punonjës aktivë</small>
                    <h4 class="fw-semibold mb-0">{{ $workingHoursStats->active_employees_count ?? 0 }}</h4>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-uppercase opacity-75"><i class="fa-solid fa-chart-line text-warning"></i> Mesatare orë/ditë</small>
                    <h4 class="fw-semibold mb-0">{{ number_format($avgHoursPerDay, 1) }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Create Working Hour --}}
    <div class="card mb-4">
        <div class="card-header rounded-top-4 bg-white">
            <span><i class="fa-solid fa-clock txt-lightblue py-2"></i> Regjistrimi i Orëve të Punës</span>
        </div>
        <div class="card-body">
            <form action="{{ route('create.working.hour') }}" method="post">
                @csrf
                <div class="row g-3">
                    <div class="col-12 col-md-4 col-lg-4">
                        <label for="date" class="form-label">Data</label>
                        <input type="date" name="date" id="date" value="{{ old('date') }}" class="form-control shadow-sm rounded-3" placeholder="Shkruani datën...">
                    </div>
                    <div class="col-12 col-md-4 col-lg-4">
                        <label for="employee_id" class="form-label">Punëtori</label>
                        <select name="employee_id" id="employee_id" class="form-control shadow-sm rounded-3" required>
                            <option value="" disabled {{ old('employee_id') ? '' : 'selected' }}>-- Zgjidh Punëtorin --</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>{{ $employee->employee }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-4 col-lg-4">
                        <label for="check_in" class="form-label">Koha e Hyrjes</label>
                        <input type="datetime-local" name="check_in" id="check_in" value="{{ old('check_in') }}" class="form-control shadow-sm rounded-3" placeholder="Shkruani kohën e hyrjes...">
                    </div>
                    <div class="col-12 col-md-4 col-lg-4">
                        <label for="check_out" class="form-label">Koha e Daljes</label>
                        <input type="datetime-local" name="check_out" id="check_out" value="{{ old('check_out') }}" class="form-control shadow-sm rounded-3" placeholder="Shkruani kohën e daljes nga puna...">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-plus"></i> Regjistro Orët</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Working Hours List --}}
    <div class="card">
        <div class="card-header rounded-top-4 bg-white">
            <span><i class="fa-solid fa-clock txt-lightblue py-2"></i> Lista e orëve të punës</span>
        </div>
        <div class="card-body">
            <form action="{{ route('working.hours') }}" method="get" class="input-group mb-3">
                <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-control shadow-sm rounded-start-3" placeholder="Kërko..."  aria-label="Search..." aria-describedby="basic-addon2">
                <button type="submit" class="btn btn-darkblue shadow-sm rounded-end-3" id="basic-addon2"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th scope="col" class="text-nowrap">#</th>
                            <th scope="col" class="text-nowrap">Data</th>
                            <th scope="col" class="text-nowrap">Punëtori</th>
                            <th scope="col" class="text-nowrap">Hyrja</th>
                            <th scope="col" class="text-nowrap">Lok. hyrjes</th>
                            <th scope="col" class="text-nowrap">Dalja</th>
                            <th scope="col" class="text-nowrap">Lok. daljes</th>
                            <th scope="col" class="text-nowrap">Orë:Min</th>
                            <th scope="col" class="text-nowrap text-end">Veprimet</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($workingHours && count($workingHours) === 0)
                            <tr>
                                <td colspan="9" class="text-center"><i class="fa-regular fa-face-frown"></i> Nuk u gjet asnjë e dhënë...</td>
                            </tr>
                        @elseif($workingHours)
                            @foreach ($workingHours as $workingHour)
                                <tr>
                                    <th scope="row" class="text-nowrap">{{ $workingHour->id }}</th>
                                    <td class="text-nowrap">{{ $workingHour->date ? \Carbon\Carbon::parse($workingHour->date)->format('d/m/Y') : '-' }}</td>
                                    <td class="text-nowrap">{{ $workingHour->employee }}</td>
                                    <td class="text-nowrap">{{ $workingHour->check_in ? \Carbon\Carbon::parse($workingHour->check_in)->format('d/m/Y - H:i') : '-' }}</td>
                                    <td class="text-nowrap">
                                        @if(!empty($workingHour->check_in_location))
                                            <a href="https://www.google.com/maps?q={{ urlencode($workingHour->check_in_location) }}"
                                               target="_blank"
                                               rel="noopener noreferrer"
                                               class="text-decoration-none"
                                               title="{{ $workingHour->check_in_location }}">
                                                <i class="fa-solid fa-location-dot"></i> Harta
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-nowrap">{{ $workingHour->check_out ? \Carbon\Carbon::parse($workingHour->check_out)->format('d/m/Y - H:i') : '-' }}</td>
                                    <td class="text-nowrap">
                                        @if(!empty($workingHour->check_out_location))
                                            <a href="https://www.google.com/maps?q={{ urlencode($workingHour->check_out_location) }}"
                                               target="_blank"
                                               rel="noopener noreferrer"
                                               class="text-decoration-none"
                                               title="{{ $workingHour->check_out_location }}">
                                                <i class="fa-solid fa-location-dot"></i> Harta
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-nowrap">{{ $workingHour->hours }}</td>
                                    <td class="text-nowrap text-end">
                                        <button class="btn btn-outline-danger btn-sm rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $workingHour->id }}"><i class="fa-solid fa-trash"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>

            @if($workingHours && $workingHours->hasPages())
                <div class="mt-3">
                    {{ $workingHours->links('vendor.pagination.custom') }}
                </div>
            @endif

            @if($workingHours && count($workingHours) !== 0)
                @foreach ($workingHours as $workingHour)
                    <div class="modal fade" id="deleteModal{{ $workingHour->id }}" tabindex="-1" aria-labelledby="deleteModal{{ $workingHour->id }}Label" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-body text-center">
                                    <p>Konfirmoni nëse dëshironi të fshini këtë regjistër të orëve të punës?</p>
                                    <div class="mt-3">
                                        <a href="{{ route('delete.working.hour', $workingHour->id) }}" class="btn btn-danger btn-sm fw-semibold"><i class="fa-solid fa-check"></i> Konfirmo</a>
                                        <button class="btn btn-success btn-sm fw-semibold" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> Anulo</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
@endsection
