@extends('template.template')

{{-- Page Title --}}
@section('title', isset($editExtraHour) ? __('pages.update_extra_hours') : __('app.nav_extra_hours'))

{{-- Page Content --}}
@section('content')
    @php
        $isEmployee = ($role ?? '') === 'employee';
        $extraHourTypeLabel = function ($type) {
            return match ($type) {
                'Orët jashtë orarit' => __('pages.extra_hours_type_overtime'),
                'Orët në vikend' => __('pages.extra_hours_type_weekend'),
                default => $type,
            };
        };
    @endphp

    @if(isset($editExtraHour))
        <small class="d-block txt-lightblue fw-semibold text-uppercase"><a href="{{ route('extra.hours') }}" class="text-decoration-none txt-lightblue fw-semibold"><i class="fa-solid fa-circle-chevron-left"></i> {{ __('app.back') }}</a></small>
        <h3 class="fw-semibold pt-2">{{ __('pages.update_extra_hours') }}</h3>
        <p class="text-muted">{{ __('pages.update_extra_hours_lead') }}</p>
        <hr class="mb-4">
    @else
        <small class="d-block txt-lightblue fw-semibold text-uppercase">{{ __('app.nav_extra_hours') }}</small>
        <h3 class="fw-semibold pt-2">{{ $isEmployee ? __('pages.my_extra_hours') : __('pages.manage_extra_hours') }}</h3>
        <p class="text-muted">{{ $isEmployee ? __('pages.extra_hours_lead_employee') : __('pages.extra_hours_lead_staff') }}</p>
        <hr class="mb-4">
    @endif

    @if(isset($editExtraHour))

        {{-- Update Extra Hours --}}
        <div class="card mb-4">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-business-time txt-lightblue py-2"></i> {{ __('pages.update_extra_hours') }}</span>
            </div>
            <div class="card-body">
                <form action="{{ route('update.extra.hour') }}" method="post">
                    <input type="hidden" name="id" value="{{ $editExtraHour->id }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="employee_id" class="form-label">{{ __('app.employee') }}</label>
                            <select name="employee_id" id="employee_id" class="form-control shadow-sm rounded-3" required>
                                <option value="" disabled {{ old('employee_id', $editExtraHour->employee_id) ? '' : 'selected' }}>{{ __('pages.choose_employee') }}</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ old('employee_id', $editExtraHour->employee_id) == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->employee }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="hours" class="form-label">{{ __('app.hours') }}</label>
                            <input type="number" step="0.01" min="0.01" name="hours" id="hours" value="{{ old('hours', $editExtraHour->hours) }}" class="form-control shadow-sm rounded-3" placeholder="{{ __('pages.placeholder_hours') }}" required>
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="type" class="form-label">{{ __('pages.extra_hours_type') }}</label>
                            <select name="type" id="type" class="form-control shadow-sm rounded-3" required>
                                <option value="" disabled {{ old('type', $editExtraHour->type) ? '' : 'selected' }}>{{ __('pages.choose_extra_type') }}</option>
                                @foreach ($types as $type)
                                    <option value="{{ $type }}" {{ old('type', $editExtraHour->type) === $type ? 'selected' : '' }}>{{ $extraHourTypeLabel($type) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="date" class="form-label">{{ __('app.date') }}</label>
                            <input type="date" name="date" id="date" value="{{ old('date', $editExtraHour->date) }}" class="form-control shadow-sm rounded-3" required>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-check"></i> {{ __('pages.update_extra_hours') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @else

        @if(!$isEmployee)
            {{-- Create Extra Hours --}}
            <div class="card mb-4">
                <div class="card-header rounded-top-4 bg-white">
                    <span><i class="fa-solid fa-business-time txt-lightblue py-2"></i> {{ __('pages.register_extra_hour') }}</span>
                </div>
                <div class="card-body">
                    <form action="{{ route('create.extra.hour') }}" method="post">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12 col-md-4 col-lg-4">
                                <label for="employee_id" class="form-label">{{ __('app.employee') }}</label>
                                <select name="employee_id" id="employee_id" class="form-control shadow-sm rounded-3" required>
                                    <option value="" disabled {{ old('employee_id') ? '' : 'selected' }}>{{ __('pages.choose_employee') }}</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->employee }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-4 col-lg-4">
                                <label for="hours" class="form-label">{{ __('app.hours') }}</label>
                                <input type="number" step="0.01" min="0.01" name="hours" id="hours" value="{{ old('hours') }}" class="form-control shadow-sm rounded-3" placeholder="{{ __('pages.placeholder_hours') }}" required>
                            </div>
                            <div class="col-12 col-md-4 col-lg-4">
                                <label for="type" class="form-label">{{ __('pages.extra_hours_type') }}</label>
                                <select name="type" id="type" class="form-control shadow-sm rounded-3" required>
                                    <option value="" disabled {{ old('type') ? '' : 'selected' }}>{{ __('pages.choose_extra_type') }}</option>
                                    @foreach ($types as $type)
                                        <option value="{{ $type }}" {{ old('type') === $type ? 'selected' : '' }}>{{ $extraHourTypeLabel($type) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-4 col-lg-4">
                                <label for="date" class="form-label">{{ __('app.date') }}</label>
                                <input type="date" name="date" id="date" value="{{ old('date', date('Y-m-d')) }}" class="form-control shadow-sm rounded-3" required>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-plus"></i> {{ __('pages.register_extra_hours_btn') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        {{-- Extra Hours List --}}
        <div class="card">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-list-ol txt-lightblue py-2"></i> {{ __('pages.extra_hours_list') }}</span>
            </div>
            <div class="card-body">
                <form action="{{ route('extra.hours') }}" method="get" class="input-group mb-3">
                    <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-control shadow-sm rounded-start-3" placeholder="{{ __('app.search') }}" aria-label="{{ __('app.search') }}" aria-describedby="basic-addon2">
                    <button type="submit" class="btn btn-darkblue shadow-sm rounded-end-3" id="basic-addon2"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th scope="col" class="text-nowrap">#</th>
                                <th scope="col" class="text-nowrap">{{ __('app.employee') }}</th>
                                <th scope="col" class="text-nowrap">{{ __('app.hours') }}</th>
                                <th scope="col" class="text-nowrap">{{ __('pages.extra_hours_type') }}</th>
                                <th scope="col" class="text-nowrap">{{ __('app.date') }}</th>
                                @if(!$isEmployee)
                                    <th scope="col" class="text-nowrap text-end">{{ __('app.operations') }}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($extraHours) === 0)
                                <tr>
                                    <td colspan="{{ $isEmployee ? 5 : 6 }}" class="text-center"><i class="fa-regular fa-face-frown"></i> {{ __('app.no_data') }}</td>
                                </tr>
                            @else
                                @foreach ($extraHours as $extraHour)
                                    <tr>
                                        <th scope="row" class="text-nowrap">{{ $extraHour->id }}</th>
                                        <td class="text-nowrap">{{ $extraHour->employee }}</td>
                                        <td class="text-nowrap">{{ $extraHour->hours }}</td>
                                        <td class="text-nowrap">{{ $extraHourTypeLabel($extraHour->type) }}</td>
                                        <td class="text-nowrap">{{ $extraHour->date ? \Carbon\Carbon::parse($extraHour->date)->format('d/m/Y') : '-' }}</td>
                                        @if(!$isEmployee)
                                            <td class="text-nowrap text-end">
                                                <a href="{{ route('edit.extra.hour', $extraHour->id) }}" class="btn btn-outline-success btn-sm rounded-3 shadow-sm"><i class="fa-solid fa-pen"></i></a>
                                                <button class="btn btn-outline-danger btn-sm rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $extraHour->id }}"><i class="fa-solid fa-trash"></i></button>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                @if ($extraHours->hasPages())
                    <div class="mt-3">
                        {{ $extraHours->links('vendor.pagination.custom') }}
                    </div>
                @endif
            </div>
        </div>

        @if(!$isEmployee && count($extraHours) !== 0)

            <!-- Delete Modal -->
            @foreach ($extraHours as $extraHour)
                <div class="modal fade" id="deleteModal{{ $extraHour->id }}" tabindex="-1" aria-labelledby="deleteModal{{ $extraHour->id }}Label" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-body text-center">
                                <p>{{ __('pages.confirm_delete_extra_hours') }}</p>
                                <div class="mt-3">
                                    <a href="{{ route('delete.extra.hour', $extraHour->id) }}" class="btn btn-danger btn-sm fw-semibold"><i class="fa-solid fa-check"></i> {{ __('app.confirm') }}</a>
                                    <button class="btn btn-success btn-sm fw-semibold" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> {{ __('app.cancel') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    @endif
@endsection
