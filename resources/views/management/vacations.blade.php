@extends('template.template')

{{-- Page Title --}}
@section('title', isset($editVacation) ? 'Përditëso Pushimin' : 'Pushimet')

{{-- Page Content --}}
@section('content')
    @php
        $isEmployee = $isEmployee ?? false;
        $canManage = $canManage ?? false;
        $canApproveManager = $canApproveManager ?? false;
        $canApproveHr = $canApproveHr ?? false;
        $isCompany = $isCompany ?? false;
        $selectedType = $selectedType ?? '';
        $pendingManagerCount = (int) ($vacationStats->pending_manager_count ?? 0);
        $pendingHrCount = (int) ($vacationStats->pending_hr_count ?? 0);
        $pendingTotal = $pendingManagerCount + $pendingHrCount;
    @endphp

    @if(isset($editVacation))
        <small class="d-block txt-lightblue fw-semibold text-uppercase"><a href="{{ route('vacations') }}" class="text-decoration-none txt-lightblue fw-semibold"><i class="fa-solid fa-circle-chevron-left"></i> Kthehu mbrapa</a></small>
        <h3 class="fw-semibold pt-2">Përditëso Pushimin</h3>
        <p class="text-muted">Përditëso të dhënat e kërkesës së zgjedhur për pushim.</p>
        <hr class="mb-4">
    @else
        <small class="d-block txt-lightblue fw-semibold text-uppercase">{{ $isEmployee ? 'Punonjësi' : 'Stafi' }}</small>
        <h3 class="fw-semibold pt-2">Pushimet</h3>
        <p class="text-muted">
            @if($isEmployee)
                Kërko pushim dhe ndiq statusin e kërkesave tua. Aprovimi bëhet fillimisht nga menaxheri, pastaj nga HR.
            @else
                Kërkesat për pushim: fillimisht menaxheri, pastaj HR.
            @endif
        </p>
        <hr class="mb-4">
    @endif


    @if(isset($editVacation))

        {{-- Update Vacation --}}
        <div class="card mb-4">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-file-contract txt-lightblue py-2"></i> Përditëso Pushimin</span>
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
                                <label for="employee_id" class="form-label">Punonjësi</label>
                                <select name="employee_id" id="employee_id" class="form-control shadow-sm rounded-3">
                                    <option value="" disabled {{ old('employee_id', $editVacation->employee_id) ? '' : 'selected' }}>-- Zgjidh Punonjësin --</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ old('employee_id', $editVacation->employee_id) == $employee->id ? 'selected' : '' }}>{{ $employee->employee }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="col-12 col-md-3 col-lg-3">
                            <label for="from_date" class="form-label">Nga Data</label>
                            <input type="date" name="from_date" id="from_date" value="{{ old('from_date', $editVacation->from_date) }}" min="{{ date('Y-m-d') }}" class="form-control shadow-sm rounded-3" placeholder="...">
                        </div>
                        <div class="col-12 col-md-3 col-lg-3">
                            <label for="to_date" class="form-label">Deri në Datën</label>
                            <input type="date" name="to_date" id="to_date" value="{{ old('to_date', $editVacation->to_date) }}" min="{{ date('Y-m-d') }}" class="form-control shadow-sm rounded-3" placeholder="...">
                        </div>
                        <div class="col-12 col-md-6 col-lg-3">
                            <label for="type" class="form-label">Lloji</label>
                            <select name="type" id="type" class="form-control shadow-sm rounded-3">
                                <option value="" disabled {{ old('type', $editVacation->type) ? '' : 'selected' }}>-- Zgjidh Llojin --</option>
                                <option value="Pushim vjetor" {{ old('type', $editVacation->type) === 'Pushim vjetor' ? 'selected' : '' }}>Pushim vjetor</option>
                                <option value="Pushim mjekësor" {{ old('type', $editVacation->type) === 'Pushim mjekësor' ? 'selected' : '' }}>Pushim mjekësor</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="comment" class="form-label">Komenti</label>
                            <textarea name="comment" id="comment" class="form-control shadow-sm rounded-3" rows="5" placeholder="Shkruani arsyen ose komentin...">{{ old('comment', $editVacation->comment) }}</textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm">
                                <i class="fa-solid fa-check"></i> Përditëso Pushimin
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @else

        @if($pendingTotal > 0)
            <div class="alert bg-warning border-warning rounded-4 shadow-sm bg-opacity-25" role="alert">
                <span class="d-block fw-semibold"><i class="fa-solid fa-triangle-exclamation"></i> {{ $pendingTotal }} kërkes{{ $pendingTotal === 1 ? 'ë' : 'a' }} në pritje ({{ $pendingManagerCount }} menaxher · {{ $pendingHrCount }} HR)</span>
                <small class="d-block text-muted">Aprovimi bëhet fillimisht nga menaxheri, pastaj nga HR.</small>
            </div>
        @else
            <div class="alert bg-success border-success rounded-4 shadow-sm bg-opacity-25" role="alert">
                <span class="d-block fw-semibold"><i class="fa-solid fa-circle-check"></i> Nuk ka kërkesa pushimi në pritje</span>
                <small class="d-block text-muted">Kërkesat e aprovuara dhe të refuzuara janë ende të disponueshme në listën më poshtë.</small>
            </div>
        @endif

        {{-- Info Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-uppercase opacity-75"><i class="fa-solid fa-user-tie text-warning"></i> Në pritje të menaxherit</small>
                        <div class="d-flex align-items-center justify-content-between gap-2">
                            <h4 class="fw-semibold mb-0">{{ $pendingManagerCount }}</h4>
                            <a href="{{ route('vacations', ['search' => request('search'), 'statuses' => ['Pending Manager'], 'type' => $selectedType ?: null]) }}" class="btn btn-darkblue btn-sm rounded-3 shadow-sm"><i class="fa-solid fa-filter"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-uppercase opacity-75"><i class="fa-solid fa-building-user text-primary"></i> Në pritje të HR</small>
                        <div class="d-flex align-items-center justify-content-between gap-2">
                            <h4 class="fw-semibold mb-0">{{ $pendingHrCount }}</h4>
                            <a href="{{ route('vacations', ['search' => request('search'), 'statuses' => ['Pending HR'], 'type' => $selectedType ?: null]) }}" class="btn btn-darkblue btn-sm rounded-3 shadow-sm"><i class="fa-solid fa-filter"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-uppercase opacity-75"><i class="fa-solid fa-circle-check text-success"></i> Aprovuar</small>
                        <div class="d-flex align-items-center justify-content-between gap-2">
                            <h4 class="fw-semibold mb-0">{{ $vacationStats->approved_count ?? 0 }}</h4>
                            <a href="{{ route('vacations', ['search' => request('search'), 'statuses' => ['Approved'], 'type' => $selectedType ?: null]) }}" class="btn btn-darkblue btn-sm rounded-3 shadow-sm"><i class="fa-solid fa-filter"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-uppercase opacity-75"><i class="fa-solid fa-circle-xmark text-danger"></i> Refuzuar</small>
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
                <span><i class="fa-solid fa-plane-up txt-lightblue py-2"></i> Krijo Kërkesë të Re</span>
            </div>
            <div class="card-body">
                <form action="{{ route('create.vacation') }}" method="post">
                    @csrf
                    <div class="row g-3">
                        @if($isEmployee)
                            <input type="hidden" name="employee_id" value="{{ session('employee_id') }}">
                        @else
                            <div class="col-12 col-md-3 col-lg-3">
                                <label for="employee_id" class="form-label">Punonjësi</label>
                                <select name="employee_id" id="employee_id" class="form-control shadow-sm rounded-3">
                                    <option value="" disabled {{ old('employee_id') ? '' : 'selected' }}>-- Zgjidh Punonjësin --</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>{{ $employee->employee }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="col-12 col-md-3 col-lg-3">
                            <label for="from_date" class="form-label">Nga Data</label>
                            <input type="date" name="from_date" id="from_date" value="{{ old('from_date') }}" min="{{ date('Y-m-d') }}" class="form-control shadow-sm rounded-3" placeholder="...">
                        </div>
                        <div class="col-12 col-md-3 col-lg-3">
                            <label for="to_date" class="form-label">Deri në Datën</label>
                            <input type="date" name="to_date" id="to_date" value="{{ old('to_date') }}" min="{{ date('Y-m-d') }}" class="form-control shadow-sm rounded-3" placeholder="...">
                        </div>
                        <div class="col-12 col-md-3 col-lg-3">
                            <label for="type" class="form-label">Lloji</label>
                            <select name="type" id="type" class="form-control shadow-sm rounded-3">
                                <option value="" disabled {{ old('type', $selectedType) ? '' : 'selected' }}>-- Zgjidh Llojin --</option>
                                <option value="Pushim vjetor" {{ old('type', $selectedType) === 'Pushim vjetor' ? 'selected' : '' }}>Pushim vjetor</option>
                                <option value="Pushim mjekësor" {{ old('type', $selectedType) === 'Pushim mjekësor' ? 'selected' : '' }}>Pushim mjekësor</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="comment" class="form-label">Komenti</label>
                            <textarea name="comment" id="comment" class="form-control shadow-sm rounded-3" rows="5" placeholder="Shkruani arsyen ose komentin...">{{ old('comment') }}</textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-plus"></i> Kërko Pushim</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Vacations List --}}
        <div class="card">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-list-ol txt-lightblue py-2"></i> Lista e Pushimeve</span>
            </div>
            <div class="card-body">
                <form action="{{ route('vacations') }}" method="get" class="input-group mb-3">
                    @foreach((array) request('statuses', []) as $status)
                        <input type="hidden" name="statuses[]" value="{{ $status }}">
                    @endforeach
                    @if($selectedType)
                        <input type="hidden" name="type" value="{{ $selectedType }}">
                    @endif
                    <input type="text" name="search" value="{{ request('search') }}" id="search" class="form-control shadow-sm rounded-start-3" placeholder="Kërko..."  aria-label="Kërko..." aria-describedby="basic-addon2">
                    <button type="submit" class="btn btn-darkblue shadow-sm rounded-end-3" id="basic-addon2"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th scope="col" class="text-nowrap">#</th>
                                <th scope="col" class="text-nowrap">Data e Kërkesës</th>
                                @if(!$isEmployee)
                                    <th scope="col" class="text-nowrap">Punonjësi</th>
                                @endif
                                <th scope="col" class="text-nowrap">Lloji</th>
                                <th scope="col" class="text-nowrap">Intervali</th>
                                <th scope="col" class="text-nowrap">Statusi</th>
                                <th scope="col" class="text-nowrap text-end">Veprimet</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($vacations) === 0)
                                <tr>
                                    <td colspan="{{ $isEmployee ? 6 : 7 }}" class="text-center"><i class="fa-regular fa-face-frown"></i> Nuk u gjetën të dhëna...</td>
                                </tr>
                            @else
                                @foreach ($vacations as $vacation)
                                    <tr>
                                        <th scope="row" class="text-nowrap">{{ $vacation->id }}</th>
                                        <td class="text-nowrap">{{ \Carbon\Carbon::parse($vacation->date)->format('d/m/Y') }}</td>
                                        @if(!$isEmployee)
                                            <td class="text-nowrap">{{ $vacation->employee }}</td>
                                        @endif
                                        <td class="text-nowrap">{{ $vacation->type ?? '-' }}</td>
                                        <td class="text-nowrap">{{ \Carbon\Carbon::parse($vacation->from_date)->format('d/m/Y') }} &rarr; {{ \Carbon\Carbon::parse($vacation->to_date)->format('d/m/Y') }}</td>
                                        <td class="text-nowrap">
                                            @if($vacation->status === 'Approved')
                                                <span class="badge rounded-pill text-success text-bg-success bg-opacity-25 border border-success">Aprovuar</span>
                                            @elseif($vacation->status === 'Pending HR')
                                                <span class="badge rounded-pill text-primary text-bg-primary bg-opacity-25 border border-primary">Në pritje të HR</span>
                                            @elseif($vacation->status === 'Pending Manager' || $vacation->status === 'Reviewing')
                                                <span class="badge rounded-pill text-dark text-bg-warning bg-opacity-25 border border-warning">Në pritje të menaxherit</span>
                                            @else
                                                <span class="badge rounded-pill text-danger text-bg-danger bg-opacity-25 border border-danger">Refuzuar</span>
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
                                <p>A jeni i sigurt që dëshironi të fshini këtë pushim?</p>
                                <div class="mt-3">
                                    <a href="{{ route('delete.vacation', $vacation->id) }}" class="btn btn-danger btn-sm fw-semibold"><i class="fa-solid fa-check"></i> Konfirmo</a>
                                    <button class="btn btn-success btn-sm fw-semibold" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> Anulo</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Comment Modal -->
            @foreach ($vacations as $vacation)
            @php
                $currentStatus = ($vacation->status === 'Reviewing') ? 'Pending Manager' : $vacation->status;
            @endphp
            <div class="modal modal-lg fade" id="commentModal{{ $vacation->id }}" tabindex="-1" aria-labelledby="commentModal{{ $vacation->id }}Label" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable">
                    <div class="modal-content rounded-4">
                        <div class="modal-header justify-content-between align-items-center px-4">
                            <h1 class="modal-title fs-5 fw-semibold" id="commentModal{{ $vacation->id }}Label">Detajet e Pushimit <strong>#VCID-{{ $vacation->id }}</strong></h1>
                            <button type="button" class="btn btn-darkblue btn-sm" data-bs-dismiss="modal" aria-label="Mbyll"><i class="fa-solid fa-xmark"></i></button>
                        </div>
                        <div class="modal-body bg-custom px-4 py-5">

                            @if($vacation->status === 'Approved')
                                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> Statusi : </span><span class="badge rounded-pill text-success text-bg-success bg-opacity-25 border border-success">Aprovuar</span></span>
                            @elseif($vacation->status === 'Pending HR')
                                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> Statusi : </span><span class="badge rounded-pill text-primary text-bg-primary bg-opacity-25 border border-primary">Në pritje të HR</span></span>
                            @elseif($vacation->status === 'Pending Manager' || $vacation->status === 'Reviewing')
                                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> Statusi : </span><span class="badge rounded-pill text-dark text-bg-warning bg-opacity-25 border border-warning">Në pritje të menaxherit</span></span>
                            @else
                                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> Statusi : </span><span class="badge rounded-pill text-danger text-bg-danger bg-opacity-25 border border-danger">Refuzuar</span></span>
                            @endif
                            <span class="d-block mb-2"><span class="text-muted"><i class="fa-regular fa-calendar"></i> Intervali : </span>{{ \Carbon\Carbon::parse($vacation->from_date)->format('d/m/Y') }} &rarr; {{ \Carbon\Carbon::parse($vacation->to_date)->format('d/m/Y') }}</span>
                            <span class="d-block mb-2"><span class="text-muted"><i class="fa-regular fa-user"></i> Punonjësi : </span>{{ $vacation->employee }}</span>
                            <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-tag"></i> Lloji : </span>{{ $vacation->type ?? '-' }}</span>
                            <span class="d-block mt-4 mb-3 text-muted"><i class="fa-regular fa-file-lines"></i> Komenti</span>
                            <div class="card card-body rounded-4">
                                <span>{!! nl2br(e($vacation->comment)) !!}</span>
                            </div>
                            <div class="text-end mt-3">
                                <small class="text-muted"><i class="fa-solid fa-pen"></i> Kërkuar më {{ \Carbon\Carbon::parse($vacation->date)->format('d/m/Y') }}</small>
                            </div>
                            @if($canManage)
                                <hr class="my-3">
                                <span class="d-block mb-2"><i class="fa-solid fa-arrows-turn-to-dots"></i> Përditëso Statusin</span>
                                @if($canApproveManager && $currentStatus === 'Pending Manager')
                                    <a href="{{ route('update.vacation.status', [$vacation->id, 'Pending HR']) }}" class="btn btn-success btn-sm rounded-4 shadow-sm m-1"><i class="fa-solid fa-check"></i> Aprovo (Menaxher)</a>
                                    @if($isCompany)
                                        <a href="{{ route('update.vacation.status', [$vacation->id, 'Approved']) }}" class="btn btn-darkblue btn-sm rounded-4 shadow-sm m-1"><i class="fa-solid fa-check-double"></i> Aprovo direkt</a>
                                    @endif
                                    <a href="{{ route('update.vacation.status', [$vacation->id, 'Refused']) }}" class="btn btn-danger btn-sm rounded-4 shadow-sm m-1"><i class="fa-solid fa-times"></i> Refuzo</a>
                                @elseif($canApproveHr && $currentStatus === 'Pending HR')
                                    <a href="{{ route('update.vacation.status', [$vacation->id, 'Approved']) }}" class="btn btn-success btn-sm rounded-4 shadow-sm m-1"><i class="fa-solid fa-check"></i> Aprovo (HR)</a>
                                    <a href="{{ route('update.vacation.status', [$vacation->id, 'Refused']) }}" class="btn btn-danger btn-sm rounded-4 shadow-sm m-1"><i class="fa-solid fa-times"></i> Refuzo</a>
                                @elseif(in_array($currentStatus, ['Approved', 'Refused'], true))
                                    <small class="d-block text-muted">Ky pushim është i mbyllur.</small>
                                @else
                                    <small class="d-block text-muted">Nuk keni veprim për këtë fazë.</small>
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
