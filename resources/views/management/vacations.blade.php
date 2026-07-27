@extends('template.template')

{{-- Page Title --}}
@section('title', isset($editVacation) ? 'Update Vacation' : 'Vacations')

{{-- Page Content --}}
@section('content')
    @if(isset($editVacation))
        <small class="d-block txt-lightblue fw-semibold text-uppercase"><a href="{{ route('vacations') }}" class="text-decoration-none txt-lightblue fw-semibold"><i class="fa-solid fa-circle-chevron-left"></i> Turn Back</a></small>
        <h3 class="fw-semibold pt-2">Update Vacation</h3>
        <p class="text-muted">Update the details of the selected vacation request.</p>
        <hr class="mb-4">
    @else
        <small class="d-block txt-lightblue fw-semibold text-uppercase">HR</small>
        <h3 class="fw-semibold pt-2">Vacation</h3>
        <p class="text-muted">Vacation requests from employees - submit, review and plan.</p>
        <hr class="mb-4">
    @endif


    @if(isset($editVacation))

        {{-- Update Vacation --}}
        <div class="card mb-4">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-file-contract txt-lightblue py-2"></i> Update Vacation</span>
            </div>
            <div class="card-body">
                <form action="{{ route('update.vacation') }}" method="post">
                    @csrf
                    <input type="hidden" name="id" value="{{ $editVacation->id }}">
                    <div class="row g-3">
                        <div class="col-12 col-md-3 col-lg-3">
                            <label for="employee_id" class="form-label">Employee</label>
                            <select name="employee_id" id="employee_id" class="form-control shadow-sm rounded-3">
                                <option value="">-- Select Employee --</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ old('employee_id', $editVacation->employee_id) == $employee->id ? 'selected' : '' }}>{{ $employee->employee }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-3 col-lg-3">
                            <label for="from_date" class="form-label">From Date</label>
                            <input type="date" name="from_date" id="from_date" value="{{ old('from_date', $editVacation->from_date) }}" min="{{ date('Y-m-d') }}" class="form-control shadow-sm rounded-3" placeholder="...">
                        </div>
                        <div class="col-12 col-md-3 col-lg-3">
                            <label for="to_date" class="form-label">To Date</label>
                            <input type="date" name="to_date" id="to_date" value="{{ old('to_date', $editVacation->to_date) }}" min="{{ date('Y-m-d') }}" class="form-control shadow-sm rounded-3" placeholder="...">
                        </div>
                        <div class="col-12 col-md-6 col-lg-3">
                            <label for="type" class="form-label">Type</label>
                            <select name="type" id="type" class="form-control shadow-sm rounded-3">
                                <option value="">-- Select Type --</option>
                                <option value="Pushim vjetor" {{ old('type', $editVacation->type) === 'Pushim vjetor' ? 'selected' : '' }}>Pushim vjetor</option>
                                <option value="Pushim mjekësor" {{ old('type', $editVacation->type) === 'Pushim mjekësor' ? 'selected' : '' }}>Pushim mjekësor</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="comment" class="form-label">Comment</label>
                            <textarea name="comment" id="comment" class="form-control shadow-sm rounded-3" rows="5" placeholder="Please provide a reason or comment...">{{ old('comment', $editVacation->comment) }}</textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm">
                                <i class="fa-solid fa-check"></i> Update Vacation
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @else

        @if(($vacationStats->reviewing_count ?? 0) > 0)
            <div class="alert bg-warning border-warning rounded-4 shadow-sm bg-opacity-25" role="alert">
                <span class="d-block fw-semibold"><i class="fa-solid fa-triangle-exclamation"></i> {{ $vacationStats->reviewing_count }} vacation request{{ ($vacationStats->reviewing_count ?? 0) === 1 ? '' : 's' }} pending review</span>
                <small class="d-block text-muted">Please review staffing impact before approving additional leave.</small>
            </div>
        @else
            <div class="alert bg-success border-success rounded-4 shadow-sm bg-opacity-25" role="alert">
                <span class="d-block fw-semibold"><i class="fa-solid fa-circle-check"></i> No vacation requests are waiting for review</span>
                <small class="d-block text-muted">Approved and refused requests are still available in the list below.</small>
            </div>
        @endif

        {{-- Info Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-uppercase opacity-75"><i class="fa-solid fa-hourglass-half text-warning"></i> Requested</small>
                        <div class="d-flex align-items-center justify-content-between gap-2">
                            <h4 class="fw-semibold mb-0">{{ $vacationStats->reviewing_count ?? 0 }}</h4>
                            <a href="{{ route('vacations', ['search' => request('search'), 'statuses' => ['Reviewing']]) }}" class="btn btn-darkblue btn-sm rounded-3 shadow-sm"><i class="fa-solid fa-filter"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-uppercase opacity-75"><i class="fa-solid fa-circle-check text-success"></i> Approved</small>
                        <div class="d-flex align-items-center justify-content-between gap-2">
                            <h4 class="fw-semibold mb-0">{{ $vacationStats->approved_count ?? 0 }}</h4>
                            <a href="{{ route('vacations', ['search' => request('search'), 'statuses' => ['Approved']]) }}" class="btn btn-darkblue btn-sm rounded-3 shadow-sm"><i class="fa-solid fa-filter"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-uppercase opacity-75"><i class="fa-solid fa-circle-xmark text-danger"></i> Refused</small>
                        <div class="d-flex align-items-center justify-content-between gap-2">
                            <h4 class="fw-semibold mb-0">{{ $vacationStats->refused_count ?? 0 }}</h4>
                            <a href="{{ route('vacations', ['search' => request('search'), 'statuses' => ['Refused']]) }}" class="btn btn-darkblue btn-sm rounded-3 shadow-sm"><i class="fa-solid fa-filter"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Create Vacation --}}
        <div class="card mb-4">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-plane-up txt-lightblue py-2"></i> Create New Request</span>
            </div>
            <div class="card-body">
                <form action="{{ route('create.vacation') }}" method="post">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12 col-md-3 col-lg-3">
                            <label for="employee_id" class="form-label">Employee</label>
                            <select name="employee_id" id="employee_id" class="form-control shadow-sm rounded-3">
                                <option value="">-- Select Employee --</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>{{ $employee->employee }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-3 col-lg-3">
                            <label for="from_date" class="form-label">From Date</label>
                            <input type="date" name="from_date" id="from_date" value="{{ old('from_date') }}" min="{{ date('Y-m-d') }}" class="form-control shadow-sm rounded-3" placeholder="...">
                        </div>
                        <div class="col-12 col-md-3 col-lg-3">
                            <label for="to_date" class="form-label">To Date</label>
                            <input type="date" name="to_date" id="to_date" value="{{ old('to_date') }}" min="{{ date('Y-m-d') }}" class="form-control shadow-sm rounded-3" placeholder="...">
                        </div>
                        <div class="col-12 col-md-3 col-lg-3">
                            <label for="type" class="form-label">Type</label>
                            <select name="type" id="type" class="form-control shadow-sm rounded-3">
                                <option value="">-- Select Type --</option>
                                <option value="Pushim vjetor" {{ old('type') === 'Pushim vjetor' ? 'selected' : '' }}>Pushim vjetor</option>
                                <option value="Pushim mjekësor" {{ old('type') === 'Pushim mjekësor' ? 'selected' : '' }}>Pushim mjekësor</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="comment" class="form-label">Comment</label>
                            <textarea name="comment" id="comment" class="form-control shadow-sm rounded-3" rows="5" placeholder="Please provide a reason or comment...">{{ old('comment') }}</textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-plus"></i> Request Vacation</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Vacations List --}}
        <div class="card">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-list-ol txt-lightblue py-2"></i> Vacations List</span>
            </div>
            <div class="card-body">
                <form action="{{ route('vacations') }}" method="get" class="input-group mb-3">
                    @foreach((array) request('statuses', []) as $status)
                        <input type="hidden" name="statuses[]" value="{{ $status }}">
                    @endforeach
                    <input type="text" name="search" value="{{ request('search') }}" id="search" class="form-control shadow-sm rounded-start-3" placeholder="Search..."  aria-label="Search..." aria-describedby="basic-addon2">
                    <button type="submit" class="btn btn-darkblue shadow-sm rounded-end-3" id="basic-addon2"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th scope="col" class="text-nowrap">#</th>
                                <th scope="col" class="text-nowrap">Request Date</th>
                                <th scope="col" class="text-nowrap">Employee</th>
                                <th scope="col" class="text-nowrap">Type</th>
                                <th scope="col" class="text-nowrap">Interval</th>
                                <th scope="col" class="text-nowrap">Status</th>
                                <th scope="col" class="text-nowrap text-end">Operations</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($vacations) === 0)
                                <tr>
                                    <td colspan="7" class="text-center"><i class="fa-regular fa-face-frown"></i> No data found...</td>
                                </tr>
                            @else
                                @foreach ($vacations as $vacation)
                                    <tr>
                                        <th scope="row" class="text-nowrap">{{ $vacation->id }}</th>
                                        <td class="text-nowrap">{{ \Carbon\Carbon::parse($vacation->date)->format('d/m/Y') }}</td>
                                        <td class="text-nowrap">{{ $vacation->employee }}</td>
                                        <td class="text-nowrap">{{ $vacation->type ?? '-' }}</td>
                                        <td class="text-nowrap">{{ \Carbon\Carbon::parse($vacation->from_date)->format('d/m/Y') }} &rarr; {{ \Carbon\Carbon::parse($vacation->to_date)->format('d/m/Y') }}</td>
                                        <td class="text-nowrap">
                                            @if($vacation->status === 'Approved')
                                                <span class="badge rounded-pill text-success text-bg-success bg-opacity-25 border border-success">Approved</span>
                                            @elseif($vacation->status === 'Reviewing')
                                                <span class="badge rounded-pill text-dark text-bg-warning bg-opacity-25 border border-warning">Reviewing</span>
                                            @else
                                                <span class="badge rounded-pill text-danger text-bg-danger bg-opacity-25 border border-danger">Refused</span>
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
                                <p>Confirm if you really want to delete this vacation?</p>
                                <div class="mt-3">
                                    <a href="{{ route('delete.vacation', $vacation->id) }}" class="btn btn-danger btn-sm fw-semibold"><i class="fa-solid fa-check"></i> Confirm</a>
                                    <button class="btn btn-success btn-sm fw-semibold" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Comment Modal -->
            @foreach ($vacations as $vacation)
            <div class="modal modal-lg fade" id="commentModal{{ $vacation->id }}" tabindex="-1" aria-labelledby="commentModal{{ $vacation->id }}Label" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable">
                    <div class="modal-content rounded-4">
                        <div class="modal-header justify-content-between align-items-center px-4">
                            <h1 class="modal-title fs-5 fw-semibold" id="commentModal{{ $vacation->id }}Label">Vacations Details <strong>#VCID-{{ $vacation->id }}</strong></h1>
                            <button type="button" class="btn btn-darkblue btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
                        </div>
                        <div class="modal-body bg-custom px-4 py-5">

                            @if($vacation->status === 'Approved')
                                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> Status : </span><span class="badge rounded-pill text-success text-bg-success bg-opacity-25 border border-success">Approved</span></span>
                            @elseif($vacation->status === 'Reviewing')
                                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> Status : </span><span class="badge rounded-pill text-dark text-bg-warning bg-opacity-25 border border-warning">Reviewing</span></span>
                            @else
                                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> Status : </span><span class="badge rounded-pill text-danger text-bg-danger bg-opacity-25 border border-danger">Refused</span></span>
                            @endif
                            <span class="d-block mb-2"><span class="text-muted"><i class="fa-regular fa-calendar"></i> Interval : </span>{{ \Carbon\Carbon::parse($vacation->from_date)->format('d/m/Y') }} &rarr; {{ \Carbon\Carbon::parse($vacation->to_date)->format('d/m/Y') }}</span>
                            <span class="d-block mb-2"><span class="text-muted"><i class="fa-regular fa-user"></i> Employee : </span>{{ $vacation->employee }}</span>
                            <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-tag"></i> Type : </span>{{ $vacation->type ?? '-' }}</span>
                            <span class="d-block mt-4 mb-3 text-muted"><i class="fa-regular fa-file-lines"></i> Comment</span>
                            <div class="card card-body rounded-4">
                                <span>{!! nl2br(e($vacation->comment)) !!}</span>
                            </div>
                            <div class="text-end mt-3">
                                <small class="text-muted"><i class="fa-solid fa-pen"></i> Requested on {{ \Carbon\Carbon::parse($vacation->date)->format('d/m/Y') }}</small>
                            </div>
                            <hr class="my-3">
                            <span class="d-block mb-2"><i class="fa-solid fa-arrows-turn-to-dots"></i> Update Status</span>
                            <a href="{{ route('update.vacation.status', [$vacation->id, 'Approved']) }}" class="btn btn-success btn-sm rounded-4 shadow-sm m-1"><i class="fa-solid fa-check"></i> Approve</a>
                            <a href="{{ route('update.vacation.status', [$vacation->id, 'Reviewing']) }}" class="btn btn-warning btn-sm rounded-4 shadow-sm m-1"><i class="fa-solid fa-clock"></i> Reviewing</a>
                            <a href="{{ route('update.vacation.status', [$vacation->id, 'Refused']) }}" class="btn btn-danger btn-sm rounded-4 shadow-sm m-1"><i class="fa-solid fa-times"></i> Refuse</a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        @endif
    @endif
@endsection
