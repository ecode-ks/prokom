@extends('template.template')

{{-- Page Title --}}
@section('title', isset($editVacation) ? __('pages.update_vacation') : __('pages.nav_vacations'))

{{-- Page Content --}}
@section('content')
    @php
        $isEmployee = $isEmployee ?? false;
        $canManage = $canManage ?? false;
        $canApproveStatus = $canApproveStatus ?? false;
        $isCompany = $isCompany ?? false;
        $selectedType = $selectedType ?? '';
        $pendingHrCount = (int) ($vacationStats->pending_hr_count ?? 0);
        $pendingTotal = $pendingHrCount;
        $vacationTypeLabel = function ($type) {
            return match ($type) {
                'Pushim vjetor' => __('pages.vacation_annual'),
                'Pushim mjekësor' => __('pages.vacation_medical'),
                default => $type ?? '-',
            };
        };
    @endphp

    @if(isset($editVacation))
        <small class="d-block txt-lightblue fw-semibold text-uppercase"><a href="{{ route('vacations') }}" class="text-decoration-none txt-lightblue fw-semibold"><i class="fa-solid fa-circle-chevron-left"></i> {{ __('pages.back_behind') }}</a></small>
        <h3 class="fw-semibold pt-2">{{ __('pages.update_vacation') }}</h3>
        <p class="text-muted">{{ __('pages.update_vacation_lead') }}</p>
        <hr class="mb-4">
    @else
        <small class="d-block txt-lightblue fw-semibold text-uppercase">{{ $isEmployee ? __('pages.role_employee') : __('pages.nav_staff') }}</small>
        <h3 class="fw-semibold pt-2">{{ __('pages.nav_vacations') }}</h3>
        <p class="text-muted">
            @if($isEmployee)
                {{ __('pages.vacations_lead_employee') }}
            @else
                {{ __('pages.vacations_lead_staff') }}
            @endif
        </p>
        <hr class="mb-4">
    @endif


    @if(isset($editVacation))

        {{-- Update Vacation --}}
        <div class="card mb-4">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-file-contract txt-lightblue py-2"></i> {{ __('pages.update_vacation') }}</span>
            </div>
            <div class="card-body">
                <form action="{{ route('update.vacation') }}" method="post">
                    @csrf
                    <input type="hidden" name="id" value="{{ $editVacation->id }}">
                    <div class="row g-3">
                        @if($isEmployee)
                            <input type="hidden" name="employee_id" value="{{ session('employee_id') }}">
                        @else
                            <div class="col-12 col-md-3 col-lg-3">
                                <label for="employee_id" class="form-label">{{ __('pages.employee') }}</label>
                                <select name="employee_id" id="employee_id" class="form-control shadow-sm rounded-3">
                                    <option value="" disabled {{ old('employee_id', $editVacation->employee_id) ? '' : 'selected' }}>{{ __('pages.choose_employee') }}</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ old('employee_id', $editVacation->employee_id) == $employee->id ? 'selected' : '' }}>{{ $employee->employee }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="col-12 col-md-3 col-lg-3">
                            <label for="from_date" class="form-label">{{ __('pages.from_date') }}</label>
                            <input type="date" name="from_date" id="from_date" value="{{ old('from_date', $editVacation->from_date) }}" min="{{ date('Y-m-d') }}" class="form-control shadow-sm rounded-3" placeholder="...">
                        </div>
                        <div class="col-12 col-md-3 col-lg-3">
                            <label for="to_date" class="form-label">{{ __('pages.to_date') }}</label>
                            <input type="date" name="to_date" id="to_date" value="{{ old('to_date', $editVacation->to_date) }}" min="{{ date('Y-m-d') }}" class="form-control shadow-sm rounded-3" placeholder="...">
                        </div>
                        <div class="col-12 col-md-6 col-lg-3">
                            <label for="type" class="form-label">{{ __('pages.type') }}</label>
                            <select name="type" id="type" class="form-control shadow-sm rounded-3">
                                <option value="" disabled {{ old('type', $editVacation->type) ? '' : 'selected' }}>{{ __('pages.choose_type') }}</option>
                                <option value="Pushim vjetor" {{ old('type', $editVacation->type) === 'Pushim vjetor' ? 'selected' : '' }}>{{ __('pages.vacation_annual') }}</option>
                                <option value="Pushim mjekësor" {{ old('type', $editVacation->type) === 'Pushim mjekësor' ? 'selected' : '' }}>{{ __('pages.vacation_medical') }}</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="comment" class="form-label">{{ __('pages.comment') }}</label>
                            <textarea name="comment" id="comment" class="form-control shadow-sm rounded-3" rows="5" placeholder="{{ __('pages.placeholder_comment') }}">{{ old('comment', $editVacation->comment) }}</textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm">
                                <i class="fa-solid fa-check"></i> {{ __('pages.update_vacation') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @else

        @if($pendingTotal >= 3)
            <div class="alert bg-danger border-danger rounded-4 shadow-sm bg-opacity-25" role="alert">
                <span class="d-block fw-semibold"><i class="fa-solid fa-triangle-exclamation"></i> {{ __('pages.pending_requests_many', ['count' => $pendingTotal]) }}</span>
                <small class="d-block text-muted">{{ __('pages.pending_requests_lead') }}</small>
            </div>
        @elseif($pendingTotal > 0)
            <div class="alert bg-warning border-warning rounded-4 shadow-sm bg-opacity-25" role="alert">
                <span class="d-block fw-semibold"><i class="fa-solid fa-triangle-exclamation"></i> {{ trans_choice('pages.pending_alert', $pendingTotal, ['count' => $pendingTotal]) }}</span>
                <small class="d-block text-muted">{{ __('pages.pending_status_lead') }}</small>
            </div>
        @else
            <div class="alert bg-success border-success rounded-4 shadow-sm bg-opacity-25" role="alert">
                <span class="d-block fw-semibold"><i class="fa-solid fa-circle-check"></i> {{ __('pages.no_pending') }}</span>
                <small class="d-block text-muted">{{ __('pages.no_pending_lead') }}</small>
            </div>
        @endif

        {{-- Info Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-uppercase opacity-75"><i class="fa-solid fa-hourglass-half text-primary"></i> {{ __('pages.pending') }}</small>
                        <div class="d-flex align-items-center justify-content-between gap-2">
                            <h4 class="fw-semibold mb-0">{{ $pendingHrCount }}</h4>
                            <a href="{{ route('vacations', ['search' => request('search'), 'statuses' => ['Pending HR'], 'type' => $selectedType ?: null]) }}" class="btn btn-darkblue btn-sm rounded-3 shadow-sm"><i class="fa-solid fa-filter"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-uppercase opacity-75"><i class="fa-solid fa-circle-check text-success"></i> {{ __('pages.approved') }}</small>
                        <div class="d-flex align-items-center justify-content-between gap-2">
                            <h4 class="fw-semibold mb-0">{{ $vacationStats->approved_count ?? 0 }}</h4>
                            <a href="{{ route('vacations', ['search' => request('search'), 'statuses' => ['Approved'], 'type' => $selectedType ?: null]) }}" class="btn btn-darkblue btn-sm rounded-3 shadow-sm"><i class="fa-solid fa-filter"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-uppercase opacity-75"><i class="fa-solid fa-circle-xmark text-danger"></i> {{ __('pages.rejected') }}</small>
                        <div class="d-flex align-items-center justify-content-between gap-2">
                            <h4 class="fw-semibold mb-0">{{ $vacationStats->refused_count ?? 0 }}</h4>
                            <a href="{{ route('vacations', ['search' => request('search'), 'statuses' => ['Refused'], 'type' => $selectedType ?: null]) }}" class="btn btn-darkblue btn-sm rounded-3 shadow-sm"><i class="fa-solid fa-filter"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Create Vacation --}}
        <div class="card mb-4" id="vacation-form">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-plane-up txt-lightblue py-2"></i> {{ __('pages.create_request') }}</span>
            </div>
            <div class="card-body">
                <form action="{{ route('create.vacation') }}" method="post">
                    @csrf
                    <div class="row g-3">
                        @if($isEmployee)
                            <input type="hidden" name="employee_id" value="{{ session('employee_id') }}">
                        @else
                            <div class="col-12 col-md-3 col-lg-3">
                                <label for="employee_id" class="form-label">{{ __('pages.employee') }}</label>
                                <select name="employee_id" id="employee_id" class="form-control shadow-sm rounded-3">
                                    <option value="" disabled {{ old('employee_id') ? '' : 'selected' }}>{{ __('pages.choose_employee') }}</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>{{ $employee->employee }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="col-12 col-md-3 col-lg-3">
                            <label for="from_date" class="form-label">{{ __('pages.from_date') }}</label>
                            <input type="date" name="from_date" id="from_date" value="{{ old('from_date') }}" min="{{ date('Y-m-d') }}" class="form-control shadow-sm rounded-3" placeholder="...">
                        </div>
                        <div class="col-12 col-md-3 col-lg-3">
                            <label for="to_date" class="form-label">{{ __('pages.to_date') }}</label>
                            <input type="date" name="to_date" id="to_date" value="{{ old('to_date') }}" min="{{ date('Y-m-d') }}" class="form-control shadow-sm rounded-3" placeholder="...">
                        </div>
                        <div class="col-12 col-md-3 col-lg-3">
                            <label for="type" class="form-label">{{ __('pages.type') }}</label>
                            <select name="type" id="type" class="form-control shadow-sm rounded-3">
                                <option value="" disabled {{ old('type', $selectedType) ? '' : 'selected' }}>{{ __('pages.choose_type') }}</option>
                                <option value="Pushim vjetor" {{ old('type', $selectedType) === 'Pushim vjetor' ? 'selected' : '' }}>{{ __('pages.vacation_annual') }}</option>
                                <option value="Pushim mjekësor" {{ old('type', $selectedType) === 'Pushim mjekësor' ? 'selected' : '' }}>{{ __('pages.vacation_medical') }}</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="comment" class="form-label">{{ __('pages.comment') }}</label>
                            <textarea name="comment" id="comment" class="form-control shadow-sm rounded-3" rows="5" placeholder="{{ __('pages.placeholder_comment') }}">{{ old('comment') }}</textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-plus"></i> {{ __('pages.request_vacation') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Vacations List --}}
        <div class="card">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-list-ol txt-lightblue py-2"></i> {{ __('pages.vacations_list') }}</span>
            </div>
            <div class="card-body">
                <form action="{{ route('vacations') }}" method="get" class="input-group mb-3">
                    @foreach((array) request('statuses', []) as $status)
                        <input type="hidden" name="statuses[]" value="{{ $status }}">
                    @endforeach
                    @if($selectedType)
                        <input type="hidden" name="type" value="{{ $selectedType }}">
                    @endif
                    <input type="text" name="search" value="{{ request('search') }}" id="search" class="form-control shadow-sm rounded-start-3" placeholder="{{ __('pages.search') }}"  aria-label="{{ __('pages.search') }}" aria-describedby="basic-addon2">
                    <button type="submit" class="btn btn-darkblue shadow-sm rounded-end-3" id="basic-addon2"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th scope="col" class="text-nowrap">#</th>
                                <th scope="col" class="text-nowrap">{{ __('pages.request_date') }}</th>
                                @if(!$isEmployee)
                                    <th scope="col" class="text-nowrap">{{ __('pages.employee') }}</th>
                                @endif
                                <th scope="col" class="text-nowrap">{{ __('pages.type') }}</th>
                                <th scope="col" class="text-nowrap">{{ __('pages.interval') }}</th>
                                <th scope="col" class="text-nowrap">{{ __('pages.status') }}</th>
                                <th scope="col" class="text-nowrap text-end">{{ __('pages.operations') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($vacations) === 0)
                                <tr>
                                    <td colspan="{{ $isEmployee ? 6 : 7 }}" class="text-center"><i class="fa-regular fa-face-frown"></i> {{ __('pages.no_data') }}</td>
                                </tr>
                            @else
                                @foreach ($vacations as $vacation)
                                    <tr>
                                        <th scope="row" class="text-nowrap">{{ $vacation->id }}</th>
                                        <td class="text-nowrap">{{ \Carbon\Carbon::parse($vacation->date)->format('d/m/Y') }}</td>
                                        @if(!$isEmployee)
                                            <td class="text-nowrap">{{ $vacation->employee }}</td>
                                        @endif
                                        <td class="text-nowrap">{{ $vacationTypeLabel($vacation->type ?? null) }}</td>
                                        <td class="text-nowrap">{{ \Carbon\Carbon::parse($vacation->from_date)->format('d/m/Y') }} &rarr; {{ \Carbon\Carbon::parse($vacation->to_date)->format('d/m/Y') }}</td>
                                        <td class="text-nowrap">
                                            @if($vacation->status === 'Approved')
                                                <span class="badge rounded-pill text-success text-bg-success bg-opacity-25 border border-success">{{ __('pages.approved') }}</span>
                                            @elseif(in_array($vacation->status, ['Pending HR', 'Pending Manager', 'Reviewing'], true))
                                                <span class="badge rounded-pill text-primary text-bg-primary bg-opacity-25 border border-primary">{{ __('pages.pending') }}</span>
                                            @else
                                                <span class="badge rounded-pill text-danger text-bg-danger bg-opacity-25 border border-danger">{{ __('pages.rejected') }}</span>
                                            @endif
                                        </td>
                                        <td class="text-nowrap text-end">
                                            <a href="{{ route('edit.vacation', $vacation->id) }}" class="btn btn-outline-success btn-sm rounded-3 shadow-sm"><i class="fa-solid fa-pen"></i></a>
                                            <button class="btn btn-outline-danger btn-sm rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $vacation->id }}"><i class="fa-solid fa-trash"></i></button>
                                            <button class="btn btn-outline-dark btn-sm rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#commentModal{{ $vacation->id }}"><i class="fa-solid fa-comment"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                @if ($vacations->hasPages())
                    <div class="mt-3">
                        {{ $vacations->links('vendor.pagination.custom') }}
                    </div>
                @endif
            </div>
        </div>

        @if(count($vacations) !== 0)

            <!-- Delete Modal -->
            @foreach ($vacations as $vacation)
                <div class="modal fade" id="deleteModal{{ $vacation->id }}" tabindex="-1" aria-labelledby="deleteModal{{ $vacation->id }}Label" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-body text-center">
                                <p>{{ __('pages.confirm_delete_vacation') }}</p>
                                <div class="mt-3">
                                    <a href="{{ route('delete.vacation', $vacation->id) }}" class="btn btn-danger btn-sm fw-semibold"><i class="fa-solid fa-check"></i> {{ __('pages.confirm') }}</a>
                                    <button class="btn btn-success btn-sm fw-semibold" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> {{ __('pages.cancel') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Comment Modal -->
            @foreach ($vacations as $vacation)
            @php
                $currentStatus = in_array($vacation->status, ['Pending Manager', 'Reviewing'], true)
                    ? 'Pending HR'
                    : $vacation->status;
            @endphp
            <div class="modal modal-lg fade" id="commentModal{{ $vacation->id }}" tabindex="-1" aria-labelledby="commentModal{{ $vacation->id }}Label" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable">
                    <div class="modal-content rounded-4">
                        <div class="modal-header justify-content-between align-items-center px-4">
                            <h1 class="modal-title fs-5 fw-semibold" id="commentModal{{ $vacation->id }}Label">{{ __('pages.vacation_details') }} <strong>#VCID-{{ $vacation->id }}</strong></h1>
                            <button type="button" class="btn btn-darkblue btn-sm" data-bs-dismiss="modal" aria-label="{{ __('pages.close') }}"><i class="fa-solid fa-xmark"></i></button>
                        </div>
                        <div class="modal-body bg-custom px-4 py-5">

                            @if($vacation->status === 'Approved')
                                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> {{ __('pages.status') }} : </span><span class="badge rounded-pill text-success text-bg-success bg-opacity-25 border border-success">{{ __('pages.approved') }}</span></span>
                            @elseif(in_array($vacation->status, ['Pending HR', 'Pending Manager', 'Reviewing'], true))
                                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> {{ __('pages.status') }} : </span><span class="badge rounded-pill text-primary text-bg-primary bg-opacity-25 border border-primary">{{ __('pages.pending') }}</span></span>
                            @else
                                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> {{ __('pages.status') }} : </span><span class="badge rounded-pill text-danger text-bg-danger bg-opacity-25 border border-danger">{{ __('pages.rejected') }}</span></span>
                            @endif
                            <span class="d-block mb-2"><span class="text-muted"><i class="fa-regular fa-calendar"></i> {{ __('pages.interval') }} : </span>{{ \Carbon\Carbon::parse($vacation->from_date)->format('d/m/Y') }} &rarr; {{ \Carbon\Carbon::parse($vacation->to_date)->format('d/m/Y') }}</span>
                            <span class="d-block mb-2"><span class="text-muted"><i class="fa-regular fa-user"></i> {{ __('pages.employee') }} : </span>{{ $vacation->employee }}</span>
                            <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-tag"></i> {{ __('pages.type') }} : </span>{{ $vacationTypeLabel($vacation->type ?? null) }}</span>
                            <span class="d-block mt-4 mb-3 text-muted"><i class="fa-regular fa-file-lines"></i> {{ __('pages.comment') }}</span>
                            <div class="card card-body rounded-4">
                                <span>{!! nl2br(e($vacation->comment)) !!}</span>
                            </div>
                            <div class="text-end mt-3">
                                <small class="text-muted"><i class="fa-solid fa-pen"></i> {{ __('pages.requested_on', ['date' => \Carbon\Carbon::parse($vacation->date)->format('d/m/Y')]) }}</small>
                            </div>
                            @if($canManage)
                                <hr class="my-3">
                                <span class="d-block mb-2"><i class="fa-solid fa-arrows-turn-to-dots"></i> {{ __('pages.update_status') }}</span>
                                @if($canApproveStatus && in_array($currentStatus, ['Pending HR', 'Pending Manager', 'Reviewing'], true))
                                    <a href="{{ route('update.vacation.status', [$vacation->id, 'Approved']) }}" class="btn btn-success btn-sm rounded-4 shadow-sm m-1"><i class="fa-solid fa-check"></i> {{ __('pages.approve') }}</a>
                                    <a href="{{ route('update.vacation.status', [$vacation->id, 'Refused']) }}" class="btn btn-danger btn-sm rounded-4 shadow-sm m-1"><i class="fa-solid fa-times"></i> {{ __('pages.refuse') }}</a>
                                @elseif(in_array($currentStatus, ['Approved', 'Refused'], true))
                                    <small class="d-block text-muted">{{ __('pages.vacation_closed') }}</small>
                                @else
                                    <small class="d-block text-muted">{{ __('pages.no_action_phase') }}</small>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        @endif
    @endif
@endsection

@if(!isset($editVacation) && $selectedType)
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('vacation-form');
    if (form) {
        form.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
});
</script>
@endpush
@endif
