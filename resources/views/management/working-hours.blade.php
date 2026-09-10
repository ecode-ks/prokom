@extends('template.template')

{{-- Page Title --}}
@section('title', __('app.nav_working_hours'))

{{-- Page Content --}}
@section('content')
    @php $isEmployee = ($role ?? '') === 'employee'; @endphp

    <small class="d-block txt-lightblue fw-semibold text-uppercase">{{ __('app.nav_checkin') }}</small>
    <h3 class="fw-semibold pt-2">{{ __('app.nav_working_hours') }}</h3>
    <p class="text-muted">{{ $isEmployee ? __('pages.working_hours_lead_employee') : __('pages.working_hours_lead_staff') }}</p>
    <hr class="mb-4">

    @if(!$isEmployee)
        {{-- Create Working Hour --}}
        <div class="card mb-4">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-clock txt-lightblue py-2"></i> {{ __('pages.register_working_hours') }}</span>
            </div>
            <div class="card-body">
                <form action="{{ route('create.working.hour') }}" method="post">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="date" class="form-label">{{ __('app.date') }}</label>
                            <input type="date" name="date" id="date" value="{{ old('date') }}" class="form-control shadow-sm rounded-3" placeholder="{{ __('pages.placeholder_date') }}">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="employee_id" class="form-label">{{ __('app.employee') }}</label>
                            <select name="employee_id" id="employee_id" class="form-control shadow-sm rounded-3" required>
                                <option value="" disabled {{ old('employee_id') ? '' : 'selected' }}>{{ __('pages.choose_worker') }}</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>{{ $employee->employee }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="check_in" class="form-label">{{ __('pages.check_in') }}</label>
                            <input type="datetime-local" name="check_in" id="check_in" value="{{ old('check_in') }}" class="form-control shadow-sm rounded-3" placeholder="{{ __('pages.placeholder_check_in') }}">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="check_out" class="form-label">{{ __('pages.check_out') }}</label>
                            <input type="datetime-local" name="check_out" id="check_out" value="{{ old('check_out') }}" class="form-control shadow-sm rounded-3" placeholder="{{ __('pages.placeholder_check_out') }}">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-plus"></i> {{ __('pages.register_hours') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Working Hours List --}}
    <div class="card">
        <div class="card-header rounded-top-4 bg-white">
            <span><i class="fa-solid fa-clock txt-lightblue py-2"></i> {{ __('pages.working_hours_list') }}</span>
        </div>
        <div class="card-body">
            @if($isEmployee)
                <form action="{{ route('working.hours') }}" method="get" class="row g-2 mb-3 align-items-end">
                    <div class="col-12 col-md-4">
                        <label for="from_date" class="form-label small text-muted mb-1">{{ __('app.from_date') }}</label>
                        <input type="date" name="from_date" id="from_date" value="{{ $fromDate }}" class="form-control shadow-sm rounded-3">
                    </div>
                    <div class="col-12 col-md-4">
                        <label for="to_date" class="form-label small text-muted mb-1">{{ __('pages.until') }}</label>
                        <input type="date" name="to_date" id="to_date" value="{{ $toDate }}" class="form-control shadow-sm rounded-3">
                    </div>
                    <div class="col-12 col-md-4">
                        <button type="submit" class="btn btn-darkblue shadow-sm rounded-3"><i class="fa-solid fa-filter"></i> {{ __('app.filter') }}</button>
                        @if($fromDate || $toDate)
                            <a href="{{ route('working.hours') }}" class="btn btn-outline-secondary shadow-sm rounded-3"><i class="fa-solid fa-xmark"></i> {{ __('pages.clear') }}</a>
                        @endif
                    </div>
                </form>
                @if($workingHoursTotalLabel ?? null)
                    <div class="alert alert-info d-flex justify-content-between align-items-center rounded-3">
                        <span><i class="fa-solid fa-clock"></i> {{ __('pages.hours_total') }} {{ ($fromDate || $toDate) ? __('pages.hours_total_period') : '' }}</span>
                        <strong>{{ $workingHoursTotalLabel }}</strong>
                    </div>
                @endif
            @else
                <form action="{{ route('working.hours') }}" method="get" class="input-group mb-3">
                    <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-control shadow-sm rounded-start-3" placeholder="{{ __('app.search') }}"  aria-label="Search..." aria-describedby="basic-addon2">
                    <button type="submit" class="btn btn-darkblue shadow-sm rounded-end-3" id="basic-addon2"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
            @endif
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th scope="col" class="text-nowrap">#</th>
                            <th scope="col" class="text-nowrap">{{ __('app.date') }}</th>
                            <th scope="col" class="text-nowrap">{{ __('app.employee') }}</th>
                            <th scope="col" class="text-nowrap">{{ __('pages.check_in_short') }}</th>
                            <th scope="col" class="text-nowrap">{{ __('pages.check_out_short') }}</th>
                            <th scope="col" class="text-nowrap">{{ __('pages.hours_min') }}</th>
                            @if(!$isEmployee)
                                <th scope="col" class="text-nowrap text-end">{{ __('app.operations') }}</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @if($workingHours && count($workingHours) === 0)
                            <tr>
                                <td colspan="{{ $isEmployee ? 6 : 9 }}" class="text-center"><i class="fa-regular fa-face-frown"></i> {{ __('app.no_data') }}</td>
                            </tr>
                        @elseif($workingHours)
                            @foreach ($workingHours as $workingHour)
                                <tr>
                                    <th scope="row" class="text-nowrap">{{ $workingHour->id }}</th>
                                    <td class="text-nowrap">{{ $workingHour->date ? \Carbon\Carbon::parse($workingHour->date)->format('d/m/Y') : '-' }}</td>
                                    <td class="text-nowrap">{{ $workingHour->employee }}</td>
                                    <td class="text-nowrap">{{ $workingHour->check_in ? \Carbon\Carbon::parse($workingHour->check_in)->format('d/m/Y - H:i') : '-' }}</td>
                                    <td class="text-nowrap">{{ $workingHour->check_out ? \Carbon\Carbon::parse($workingHour->check_out)->format('d/m/Y - H:i') : '-' }}</td>
                                    <td class="text-nowrap">{{ $workingHour->hours }}</td>
                                    @if(!$isEmployee)
                                        <td class="text-nowrap text-end">
                                            <button class="btn btn-outline-danger btn-sm rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $workingHour->id }}"><i class="fa-solid fa-trash"></i></button>
                                        </td>
                                    @endif
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

            @if(!$isEmployee && $workingHours && count($workingHours) !== 0)
                @foreach ($workingHours as $workingHour)
                    <div class="modal fade" id="deleteModal{{ $workingHour->id }}" tabindex="-1" aria-labelledby="deleteModal{{ $workingHour->id }}Label" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-body text-center">
                                    <p>{{ __('pages.confirm_delete_working_hour') }}</p>
                                    <div class="mt-3">
                                        <a href="{{ route('delete.working.hour', $workingHour->id) }}" class="btn btn-danger btn-sm fw-semibold"><i class="fa-solid fa-check"></i> {{ __('app.confirm') }}</a>
                                        <button class="btn btn-success btn-sm fw-semibold" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> {{ __('app.cancel') }}</button>
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
