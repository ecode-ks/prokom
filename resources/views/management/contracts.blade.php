@extends('template.template')

{{-- Page Title --}}
@section('title', isset($editContract) ? 'Update Contracts' : 'Contracts')

{{-- Page Content --}}
@section('content')
    @if(isset($contractDetails))
        <small class="d-block txt-lightblue fw-semibold text-uppercase"><a href="{{ route('contracts') }}" class="text-decoration-none txt-lightblue fw-semibold"><i class="fa-solid fa-circle-chevron-left"></i> Turn Back</a></small>
        <h3 class="fw-semibold pt-2">Contract Details</h3>
        <p class="text-muted">View all information related to the selected employment contract.</p>
        <hr class="mb-4">
    @elseif(isset($editContract))
        <small class="d-block txt-lightblue fw-semibold text-uppercase"><a href="{{ route('contracts') }}" class="text-decoration-none txt-lightblue fw-semibold"><i class="fa-solid fa-circle-chevron-left"></i> Turn Back</a></small>
        <h3 class="fw-semibold pt-2">Update Contract</h3>
        <p class="text-muted">Update the details of the selected contract.</p>
        <hr class="mb-4">
    @else
        <small class="d-block txt-lightblue fw-semibold text-uppercase">HR</small>
        <h3 class="fw-semibold pt-2">Contracts</h3>
        <p class="text-muted">Employment contracts with workload, salary and term. Expiring contracts are highlighted.</p>
        <hr class="mb-4">
    @endif

    @if(isset($contractDetails))

        {{-- Contract Details --}}
        @php
            $docs = is_array($contractDetails->docs)
                ? $contractDetails->docs
                : json_decode($contractDetails->docs, true);
        @endphp
        <div class="card">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-circle-info txt-lightblue py-2"></i> Contract Details</span>
            </div>
            <div class="card-body py-4">
                @if($contractDetails->status === 'Active')
                    <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> Contract Status : </span><span class="badge rounded-pill text-success text-bg-success bg-opacity-25 border border-success">Active</span></span>
                @elseif($contractDetails->status === 'Expiring')
                    <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> Contract Status : </span><span class="badge rounded-pill text-dark text-bg-warning bg-opacity-25 border border-warning">Expiring</span></span>
                @else
                    <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> Contract Status : </span><span class="badge rounded-pill text-danger text-bg-danger bg-opacity-25 border border-danger">Expired</span></span>
                @endif
                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-user"></i> Employee : </span>{{ $contractDetails->employee }}</span>
                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-phone"></i> Phone : </span>{{ $contractDetails->phone }}</span>
                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-envelope"></i> Email : </span>{{ $contractDetails->email }}</span>
                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-briefcase"></i> Position : </span>{{ $contractDetails->position }}</span>
                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-euro-sign"></i> Salary : </span>{{ $contractDetails->salary }}€</span>
                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-clock"></i> Extra Hours Price : </span>{{ $contractDetails->extra_hours_price }}€</span>
                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-calendar-week"></i> Weekend Hours Price : </span>{{ $contractDetails->weekend_hours_price }}€</span>
                <span class="d-block mb-2"><span class="text-muted"><i class="fa-regular fa-calendar"></i> Contract Period : </span>{{ $contractDetails->from_date }} &rarr; {{ $contractDetails->to_date }}</span>
                <span class="d-block mt-4 mb-3 text-muted"><i class="fa-regular fa-file-lines"></i> Documents</span>
                <div class="card card-body rounded-4">
                    @if(!empty($docs))
                        @foreach($docs as $doc)
                            <div class="row justify-content-between align-items-center py-2">
                                <div class="col-8 col-md-6 col-lg-6">
                                    <div class="d-block text-truncate">
                                        <span><i class="fa-solid fa-file txt-lightblue me-2"></i> {{ basename($doc) }}</span>
                                    </div>
                                </div>
                                <div class="col-4 col-md-6 col-lg-6 text-end">
                                    <a href="{{ Storage::url($doc) }}" class="btn btn-darkblue btn-sm rounded-3 shadow-sm" download><i class="fa-solid fa-download"></i></a>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-muted text-center py-3">
                            No documents uploaded.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @elseif(isset($editContract))

        {{-- Update Employee --}}
        <div class="card mb-4">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-file-contract txt-lightblue py-2"></i> Update Contract</span>
            </div>
            <div class="card-body">
                <form action="{{ route('update.contract') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{ $editContract->id }}">
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label for="employee_id" class="form-label">Employee</label>
                            <select name="employee_id" id="employee_id" class="form-control shadow-sm rounded-3">
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ old('employee_id', $editContract->employee_id) == $employee->id ? 'selected' : '' }}>{{ $employee->employee }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="position" class="form-label">Position</label>
                            <input type="text" name="position" id="position" value="{{ old('position', $editContract->position) }}" class="form-control shadow-sm rounded-3">
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="salary" class="form-label">Salary</label>
                            <input type="number" step="0.01" name="salary" id="salary" value="{{ old('salary', $editContract->salary) }}" class="form-control shadow-sm rounded-3">
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="extra_hours_price" class="form-label">Extra Hours Price</label>
                            <input type="number" step="0.01" name="extra_hours_price" id="extra_hours_price" value="{{ old('extra_hours_price', $editContract->extra_hours_price) }}" class="form-control shadow-sm rounded-3">
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="weekend_hours_price" class="form-label">Weekend Hours Price</label>
                            <input type="number" step="0.01" name="weekend_hours_price" id="weekend_hours_price" value="{{ old('weekend_hours_price', $editContract->weekend_hours_price) }}" class="form-control shadow-sm rounded-3">
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="docs" class="form-label">Documents <small class="text-muted">(optional – leave empty to keep existing)</small></label>
                            <input type="file" name="docs[]" id="docs" class="form-control shadow-sm rounded-3" multiple>
                            @php
                                $existingDocs = is_array($editContract->docs ?? null)
                                    ? $editContract->docs
                                    : json_decode($editContract->docs ?? '[]', true);
                            @endphp
                            @if(!empty($existingDocs))
                                <small class="d-block text-muted mt-1">
                                    Current files:
                                    @foreach($existingDocs as $doc)
                                        {{ basename($doc) }}@if(!$loop->last), @endif
                                    @endforeach
                                </small>
                            @endif
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="from_date" class="form-label">Valid From</label>
                            <input type="date" name="from_date" id="from_date" value="{{ old('from_date', $editContract->from_date) }}" class="form-control shadow-sm rounded-3">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="to_date" class="form-label">Valid To</label>
                            <input type="date" name="to_date" id="to_date" value="{{ old('to_date', $editContract->to_date) }}" class="form-control shadow-sm rounded-3">
                        </div>
                        
                        <div class="col-12">
                            <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm">
                                <i class="fa-solid fa-check"></i> Update Contract
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @else

        @if(($contractStats->expiring_count ?? 0) > 0)
            <div class="alert bg-warning border-warning rounded-4 shadow-sm bg-opacity-25" role="alert">
                <span class="d-block fw-semibold"><i class="fa-solid fa-triangle-exclamation"></i> {{ $contractStats->expiring_count }} contract{{ ($contractStats->expiring_count ?? 0) === 1 ? '' : 's' }} currently expiring</span>
                <small class="d-block text-muted">Renewal or termination should be planned.</small>
            </div>
        @else
            <div class="alert bg-success border-success rounded-4 shadow-sm bg-opacity-25" role="alert">
                <span class="d-block fw-semibold"><i class="fa-solid fa-circle-check"></i> No contracts are currently close to expiry</span>
                <small class="d-block text-muted">Active and expired contracts are still available in the list below.</small>
            </div>
        @endif

        {{-- Info Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-uppercase opacity-75"><i class="fa-solid fa-file-signature text-success"></i> Active</small>
                        <div class="d-flex align-items-center justify-content-between gap-2">
                            <h4 class="fw-semibold mb-0">{{ $contractStats->active_count ?? 0 }}</h4>
                            <a href="{{ route('contracts', ['search' => request('search'), 'status' => 'Active']) }}" class="btn btn-darkblue btn-sm rounded-3 shadow-sm"><i class="fa-solid fa-filter"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-uppercase opacity-75"><i class="fa-solid fa-hourglass-half text-warning"></i> Expiring</small>
                        <div class="d-flex align-items-center justify-content-between gap-2">
                            <h4 class="fw-semibold mb-0">{{ $contractStats->expiring_count ?? 0 }}</h4>
                            <a href="{{ route('contracts', ['search' => request('search'), 'status' => 'Expiring']) }}" class="btn btn-darkblue btn-sm rounded-3 shadow-sm"><i class="fa-solid fa-filter"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-uppercase opacity-75"><i class="fa-solid fa-file-circle-xmark text-danger"></i> Expired</small>
                        <div class="d-flex align-items-center justify-content-between gap-2">
                            <h4 class="fw-semibold mb-0">{{ $contractStats->expired_count ?? 0 }}</h4>
                            <a href="{{ route('contracts', ['search' => request('search'), 'status' => 'Expired']) }}" class="btn btn-darkblue btn-sm rounded-3 shadow-sm"><i class="fa-solid fa-filter"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Create Contract --}}
        <div class="card mb-4">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-file-contract txt-lightblue py-2"></i> Create New Contract</span>
            </div>
            <div class="card-body">
                <form action="{{ route('create.contract') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="employee_id" class="form-label">Employee</label>
                            <select name="employee_id" id="employee_id" class="form-control shadow-sm rounded-3">
                                <option value="">-- Select Employee --</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>{{ $employee->employee }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="position" class="form-label">Position</label>
                            <input type="text" name="position" id="position" value="{{ old('position') }}" class="form-control shadow-sm rounded-3" placeholder="Enter position here...">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="salary" class="form-label">Salary</label>
                            <input type="number" step="0.01" name="salary" id="salary" value="{{ old('salary') }}" class="form-control shadow-sm rounded-3" placeholder="Enter salary here...">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="extra_hours_price" class="form-label">Extra Hours Price</label>
                            <input type="number" step="0.01" name="extra_hours_price" id="extra_hours_price" value="{{ old('extra_hours_price') }}" class="form-control shadow-sm rounded-3" placeholder="Enter extra hours price here...">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="weekend_hours_price" class="form-label">Weekend Hours Price</label>
                            <input type="number" step="0.01" name="weekend_hours_price" id="weekend_hours_price" value="{{ old('weekend_hours_price') }}" class="form-control shadow-sm rounded-3" placeholder="Enter weekend hours price here...">
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="docs" class="form-label">Documents</label>
                            <input type="file" name="docs[]" id="docs" class="form-control shadow-sm rounded-3" multiple>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="from_date" class="form-label">Valid From</label>
                            <input type="date" name="from_date" id="from_date" value="{{ old('from_date') }}" class="form-control shadow-sm rounded-3">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="to_date" class="form-label">Valid To</label>
                            <input type="date" name="to_date" id="to_date" value="{{ old('to_date') }}" class="form-control shadow-sm rounded-3">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-plus"></i> Create Contract</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Contracts List --}}
        <div class="card">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-list-ol txt-lightblue py-2"></i> Contracts List</span>
            </div>
            <div class="card-body">
                <form action="{{ route('contracts') }}" method="get" class="input-group mb-3">
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-control shadow-sm rounded-start-3" placeholder="Search..."  aria-label="Search..." aria-describedby="basic-addon2">
                    <button type="submit" class="btn btn-darkblue shadow-sm rounded-end-3" id="basic-addon2"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th scope="col" class="text-nowrap">#</th>
                                <th scope="col" class="text-nowrap">Employee</th>
                                <th scope="col" class="text-nowrap">Position</th>
                                <th scope="col" class="text-nowrap">Salary</th>
                                <th scope="col" class="text-nowrap">Phone</th>
                                <th scope="col" class="text-nowrap">Email</th>
                                <th scope="col" class="text-nowrap">Validity</th>

                                <th scope="col" class="text-nowrap">Status</th>
                                <th scope="col" class="text-nowrap text-end">Operations</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($contracts) === 0)
                                <tr>
                                    <td colspan="5" class="text-center"><i class="fa-regular fa-face-frown"></i> No data found...</td>
                                </tr>
                            @else
                                @foreach ($contracts as $contract)
                                    <tr>
                                        <th scope="row" class="text-nowrap">{{ $contract->id }}</th>
                                        <td class="text-nowrap">{{ $contract->employee }}</td>
                                        <td class="text-nowrap">{{ $contract->position }}</td>
                                        <td class="text-nowrap">{{ $contract->salary }}</td>
                                        <td class="text-nowrap">{{ $contract->phone }}</td>
                                        <td class="text-nowrap">{{ $contract->email }}</td>
                                        <td class="text-nowrap">{{ $contract->to_date }}</td>
                                        <td class="text-nowrap">
                                            @if($contract->status === 'Active')
                                                <span class="badge rounded-pill text-success text-bg-success bg-opacity-25 border border-success">Active</span>
                                            @elseif($contract->status === 'Expiring')
                                                <span class="badge rounded-pill text-dark text-bg-warning bg-opacity-25 border border-warning">Expiring</span>
                                            @else
                                                <span class="badge rounded-pill text-danger text-bg-danger bg-opacity-25 border border-danger">Expired</span>
                                            @endif
                                        </td>
                                        <td class="text-nowrap text-end">
                                            <a href="{{ route('edit.contract', $contract->id) }}" class="btn btn-outline-success btn-sm rounded-3 shadow-sm"><i class="fa-solid fa-pen"></i></a>
                                            <button class="btn btn-outline-danger btn-sm rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $contract->id }}"><i class="fa-solid fa-trash"></i></button>
                                            <a href="{{ route('contract.details', $contract->id) }}" class="btn btn-outline-dark btn-sm rounded-3 shadow-sm"><i class="fa-solid fa-eye"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                @if ($contracts->hasPages())
                    <div class="mt-3">
                        {{ $contracts->links('vendor.pagination.custom') }}
                    </div>
                @endif
            </div>
        </div>

        @if(count($contracts) !== 0)

            <!-- Delete Modal -->
            @foreach ($contracts as $contract)
                <div class="modal fade" id="deleteModal{{ $contract->id }}" tabindex="-1" aria-labelledby="deleteModal{{ $contract->id }}Label" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-body text-center">
                                <p>Confirm if you really want to delete this contract?</p>
                                <div class="mt-3">
                                    <a href="{{ route('delete.contract', $contract->id) }}" class="btn btn-danger btn-sm fw-semibold"><i class="fa-solid fa-check"></i> Confirm</a>
                                    <button class="btn btn-success btn-sm fw-semibold" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    @endif
@endsection
