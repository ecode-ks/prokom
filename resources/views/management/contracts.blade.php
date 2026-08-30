@extends('template.template')

{{-- Page Title --}}
@section('title', isset($editContract) ? 'Përditëso Kontratën' : (isset($contractDetails) ? 'Detajet e Kontratës' : 'Kontratat'))

{{-- Page Content --}}
@section('content')
    @php $isEmployee = ($role ?? '') === 'employee'; @endphp

    @if(isset($contractDetails))
        <small class="d-block txt-lightblue fw-semibold text-uppercase"><a href="{{ route('contracts') }}" class="text-decoration-none txt-lightblue fw-semibold"><i class="fa-solid fa-circle-chevron-left"></i> Kthehu mbrapa</a></small>
        <h3 class="fw-semibold pt-2">Detajet e Kontratës</h3>
        <p class="text-muted">Shiko të gjitha informacionet e kontratës së zgjedhur të punës.</p>
        <hr class="mb-4">
    @elseif(isset($editContract))
        <small class="d-block txt-lightblue fw-semibold text-uppercase"><a href="{{ route('contracts') }}" class="text-decoration-none txt-lightblue fw-semibold"><i class="fa-solid fa-circle-chevron-left"></i> Kthehu mbrapa</a></small>
        <h3 class="fw-semibold pt-2">Përditëso Kontratën</h3>
        <p class="text-muted">Përditëso të dhënat e kontratës së zgjedhur.</p>
        <hr class="mb-4">
    @else
        <small class="d-block txt-lightblue fw-semibold text-uppercase">{{ $isEmployee ? 'Kontratat' : 'HR' }}</small>
        <h3 class="fw-semibold pt-2">{{ $isEmployee ? 'Kontratat e Mia' : 'Kontratat' }}</h3>
        <p class="text-muted">{{ $isEmployee ? 'Shiko kontratat e tua të punës.' : 'Kontratat e punës me pagë mujore, me orë ose me ditë, orë shtesë dhe afat. Kontratat që skadojnë theksohen.' }}</p>
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
                <span><i class="fa-solid fa-circle-info txt-lightblue py-2"></i> Detajet e Kontratës</span>
            </div>
            <div class="card-body py-4">
                @if($contractDetails->status === 'Active')
                    <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> Statusi i Kontratës : </span><span class="badge rounded-pill text-success text-bg-success bg-opacity-25 border border-success">Aktive</span></span>
                @elseif($contractDetails->status === 'Expiring')
                    <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> Statusi i Kontratës : </span><span class="badge rounded-pill text-dark text-bg-warning bg-opacity-25 border border-warning">Duke skaduar</span></span>
                @else
                    <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> Statusi i Kontratës : </span><span class="badge rounded-pill text-danger text-bg-danger bg-opacity-25 border border-danger">Skaduar</span></span>
                @endif
                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-user"></i> Punonjësi : </span>{{ $contractDetails->employee }}</span>
                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-phone"></i> Telefoni : </span>{{ $contractDetails->phone }}</span>
                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-envelope"></i> Email : </span>{{ $contractDetails->email }}</span>
                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-briefcase"></i> Pozita : </span>{{ $contractDetails->position }}</span>
                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-coins"></i> Lloji i pagesës : </span>{{ \App\Support\PayTypes::label($contractDetails->pay_type ?? 'monthly') }}</span>
                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-euro-sign"></i> {{ \App\Support\PayTypes::rateLabel($contractDetails->pay_type ?? 'monthly') }} : </span>{{ $contractDetails->salary }}€</span>
                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-clock"></i> Çmimi i Orëve Shtesë : </span>{{ $contractDetails->extra_hours_price }}€</span>
                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-calendar-week"></i> Çmimi i Orëve të Fundjavës : </span>{{ $contractDetails->weekend_hours_price }}€</span>
                <span class="d-block mb-2"><span class="text-muted"><i class="fa-regular fa-calendar"></i> Periudha e Kontratës : </span>{{ Carbon\Carbon::parse($contractDetails->from_date)->format('d/m/Y') }} &rarr; {{ Carbon\Carbon::parse($contractDetails->to_date)->format('d/m/Y') }}</span>
                <span class="d-block mt-4 mb-3 text-muted"><i class="fa-regular fa-file-lines"></i> Dokumentet</span>
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
                            Nuk ka dokumente të ngarkuara.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @elseif(isset($editContract))

        {{-- Update Contract --}}
        <div class="card mb-4">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-file-contract txt-lightblue py-2"></i> Përditëso Kontratën</span>
            </div>
            <div class="card-body">
                <form action="{{ route('update.contract') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{ $editContract->id }}">
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label for="employee_id" class="form-label">Punonjësi</label>
                            <select name="employee_id" id="employee_id" class="form-control shadow-sm rounded-3">
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ old('employee_id', $editContract->employee_id) == $employee->id ? 'selected' : '' }}>{{ $employee->employee }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="position" class="form-label">Pozita</label>
                            <input type="text" name="position" id="position" value="{{ old('position', $editContract->position) }}" class="form-control shadow-sm rounded-3">
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="pay_type" class="form-label">Lloji i pagesës</label>
                            <select name="pay_type" id="pay_type" class="form-control shadow-sm rounded-3">
                                @foreach(\App\Support\PayTypes::options() as $value => $label)
                                    <option value="{{ $value }}" {{ old('pay_type', $editContract->pay_type ?? 'monthly') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="salary" class="form-label" id="salary_label">{{ \App\Support\PayTypes::rateLabel(old('pay_type', $editContract->pay_type ?? 'monthly')) }}</label>
                            <input type="number" step="0.01" name="salary" id="salary" value="{{ old('salary', $editContract->salary) }}" class="form-control shadow-sm rounded-3">
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="extra_hours_price" class="form-label">Çmimi i Orëve Shtesë</label>
                            <input type="number" step="0.01" name="extra_hours_price" id="extra_hours_price" value="{{ old('extra_hours_price', $editContract->extra_hours_price) }}" class="form-control shadow-sm rounded-3">
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="weekend_hours_price" class="form-label">Çmimi i Orëve të Fundjavës</label>
                            <input type="number" step="0.01" name="weekend_hours_price" id="weekend_hours_price" value="{{ old('weekend_hours_price', $editContract->weekend_hours_price) }}" class="form-control shadow-sm rounded-3">
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="docs" class="form-label">Dokumentet <small class="text-muted">(opsionale)</small></label>
                            <input type="file" name="docs[]" id="docs" class="form-control shadow-sm rounded-3" multiple>
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="from_date" class="form-label">Valide Nga</label>
                            <input type="date" name="from_date" id="from_date" value="{{ old('from_date', $editContract->from_date) }}" class="form-control shadow-sm rounded-3">
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="to_date" class="form-label">Valide Deri</label>
                            <input type="date" name="to_date" id="to_date" value="{{ old('to_date', $editContract->to_date) }}" class="form-control shadow-sm rounded-3">
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm">
                                <i class="fa-solid fa-check"></i> Përditëso Kontratën
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @else

        @if(!$isEmployee)
            @if(($contractStats->expiring_count ?? 0) > 0)
                <div class="alert bg-warning border-warning rounded-4 shadow-sm bg-opacity-25" role="alert">
                    <span class="d-block fw-semibold"><i class="fa-solid fa-triangle-exclamation"></i> {{ $contractStats->expiring_count }} kontrat{{ ($contractStats->expiring_count ?? 0) === 1 ? 'ë' : 'a' }} duke skaduar</span>
                    <small class="d-block text-muted">Duhet planifikuar rinovimi ose përfundimi.</small>
                </div>
            @else
                <div class="alert bg-success border-success rounded-4 shadow-sm bg-opacity-25" role="alert">
                    <span class="d-block fw-semibold"><i class="fa-solid fa-circle-check"></i> Nuk ka kontrata afër skadimit</span>
                    <small class="d-block text-muted">Kontratat aktive dhe të skaduara janë ende të disponueshme në listën më poshtë.</small>
                </div>
            @endif

            {{-- Info Cards --}}
            <div class="row g-3 mb-4">
                <div class="col-12 col-md-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <small class="text-uppercase opacity-75"><i class="fa-solid fa-file-signature text-success"></i> Aktive</small>
                            <div class="d-flex align-items-center justify-content-between gap-2">
                                <h4 class="fw-semibold mb-0">{{ $contractStats->active_count ?? 0 }}</h4>
                                <a href="{{ route('contracts', ['search' => request('search'), 'status' => 'Active']) }}" class="btn btn-darkblue btn-sm rounded-3 shadow-sm"><i class="fa-solid fa-filter"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <small class="text-uppercase opacity-75"><i class="fa-solid fa-hourglass-half text-warning"></i> Duke skaduar</small>
                            <div class="d-flex align-items-center justify-content-between gap-2">
                                <h4 class="fw-semibold mb-0">{{ $contractStats->expiring_count ?? 0 }}</h4>
                                <a href="{{ route('contracts', ['search' => request('search'), 'status' => 'Expiring']) }}" class="btn btn-darkblue btn-sm rounded-3 shadow-sm"><i class="fa-solid fa-filter"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <small class="text-uppercase opacity-75"><i class="fa-solid fa-file-circle-xmark text-danger"></i> Skaduar</small>
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
                    <span><i class="fa-solid fa-file-contract txt-lightblue py-2"></i> Krijo Kontratë të Re</span>
                </div>
                <div class="card-body">
                    <form action="{{ route('create.contract') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12 col-md-4 col-lg-4">
                                <label for="employee_id" class="form-label">Punonjësi</label>
                                <select name="employee_id" id="employee_id" class="form-control shadow-sm rounded-3">
                                    <option value="" disabled {{ old('employee_id') ? '' : 'selected' }}>-- Zgjidh Punonjësin --</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>{{ $employee->employee }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-4 col-lg-4">
                                <label for="position" class="form-label">Pozita</label>
                                <input type="text" name="position" id="position" value="{{ old('position') }}" class="form-control shadow-sm rounded-3" placeholder="Shkruani pozitën këtu...">
                            </div>
                            <div class="col-12 col-md-4 col-lg-4">
                                <label for="pay_type" class="form-label">Lloji i pagesës</label>
                                <select name="pay_type" id="pay_type" class="form-control shadow-sm rounded-3">
                                    @foreach(\App\Support\PayTypes::options() as $value => $label)
                                        <option value="{{ $value }}" {{ old('pay_type', 'monthly') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-4 col-lg-4">
                                <label for="salary" class="form-label" id="salary_label">{{ \App\Support\PayTypes::rateLabel(old('pay_type', 'monthly')) }}</label>
                                <input type="number" step="0.01" name="salary" id="salary" value="{{ old('salary') }}" class="form-control shadow-sm rounded-3" placeholder="Shkruani shumën...">
                            </div>
                            <div class="col-12 col-md-4 col-lg-4">
                                <label for="extra_hours_price" class="form-label">Çmimi i Orëve Shtesë</label>
                                <input type="number" step="0.01" name="extra_hours_price" id="extra_hours_price" value="{{ old('extra_hours_price') }}" class="form-control shadow-sm rounded-3" placeholder="Shkruani çmimin e orëve shtesë...">
                            </div>
                            <div class="col-12 col-md-4 col-lg-4">
                                <label for="weekend_hours_price" class="form-label">Çmimi i Orëve të Fundjavës</label>
                                <input type="number" step="0.01" name="weekend_hours_price" id="weekend_hours_price" value="{{ old('weekend_hours_price') }}" class="form-control shadow-sm rounded-3" placeholder="Shkruani çmimin e orëve të fundjavës...">
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="docs" class="form-label">Dokumentet</label>
                                <input type="file" name="docs[]" id="docs" class="form-control shadow-sm rounded-3" multiple>
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="from_date" class="form-label">Valide Nga</label>
                                <input type="date" name="from_date" id="from_date" value="{{ old('from_date') }}" class="form-control shadow-sm rounded-3">
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="to_date" class="form-label">Valide Deri</label>
                                <input type="date" name="to_date" id="to_date" value="{{ old('to_date') }}" class="form-control shadow-sm rounded-3">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-plus"></i> Krijo Kontratën</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        {{-- Contracts List --}}
        <div class="card">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-list-ol txt-lightblue py-2"></i> Lista e Kontratave</span>
            </div>
            <div class="card-body">
                <form action="{{ route('contracts') }}" method="get" class="input-group mb-3">
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-control shadow-sm rounded-start-3" placeholder="Kërko..."  aria-label="Kërko..." aria-describedby="basic-addon2">
                    <button type="submit" class="btn btn-darkblue shadow-sm rounded-end-3" id="basic-addon2"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th scope="col" class="text-nowrap">#</th>
                                <th scope="col" class="text-nowrap">Punonjësi</th>
                                <th scope="col" class="text-nowrap">Pozita</th>
                                <th scope="col" class="text-nowrap">Telefoni</th>
                                <th scope="col" class="text-nowrap">Vlefshmëria</th>
                                <th scope="col" class="text-nowrap">Statusi</th>
                                <th scope="col" class="text-nowrap text-end">Veprimet</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($contracts) === 0)
                                <tr>
                                    <td colspan="9" class="text-center"><i class="fa-regular fa-face-frown"></i> Nuk u gjetën të dhëna...</td>
                                </tr>
                            @else
                                @foreach ($contracts as $contract)
                                    <tr>
                                        <th scope="row" class="text-nowrap">{{ $contract->id }}</th>
                                        <td class="text-nowrap">{{ $contract->employee }}</td>
                                        <td class="text-nowrap">{{ $contract->position }}</td>
                                        <td class="text-nowrap">{{ $contract->phone }}</td>
                                        <td class="text-nowrap">{{ Carbon\Carbon::parse($contract->to_date)->format('d/m/Y') }}</td>
                                        <td class="text-nowrap">
                                            @if($contract->status === 'Active')
                                                <span class="badge rounded-pill text-success text-bg-success bg-opacity-25 border border-success">Aktive</span>
                                            @elseif($contract->status === 'Expiring')
                                                <span class="badge rounded-pill text-dark text-bg-warning bg-opacity-25 border border-warning">Duke skaduar</span>
                                            @else
                                                <span class="badge rounded-pill text-danger text-bg-danger bg-opacity-25 border border-danger">Skaduar</span>
                                            @endif
                                        </td>
                                        <td class="text-nowrap text-end">
                                            @if(!$isEmployee)
                                                <a href="{{ route('edit.contract', $contract->id) }}" class="btn btn-outline-success btn-sm rounded-3 shadow-sm"><i class="fa-solid fa-pen"></i></a>
                                                <button class="btn btn-outline-danger btn-sm rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $contract->id }}"><i class="fa-solid fa-trash"></i></button>
                                            @endif
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

        @if(!$isEmployee && count($contracts) !== 0)

            <!-- Delete Modal -->
            @foreach ($contracts as $contract)
                <div class="modal fade" id="deleteModal{{ $contract->id }}" tabindex="-1" aria-labelledby="deleteModal{{ $contract->id }}Label" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-body text-center">
                                <p>A jeni i sigurt që dëshironi të fshini këtë kontratë?</p>
                                <div class="mt-3">
                                    <a href="{{ route('delete.contract', $contract->id) }}" class="btn btn-danger btn-sm fw-semibold"><i class="fa-solid fa-check"></i> Konfirmo</a>
                                    <button class="btn btn-success btn-sm fw-semibold" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> Anulo</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    @endif
@endsection

@push('scripts')
<script>
    (function () {
        const rateLabels = {
            monthly: @json(\App\Support\PayTypes::rateLabel('monthly')),
            hourly: @json(\App\Support\PayTypes::rateLabel('hourly')),
            daily: @json(\App\Support\PayTypes::rateLabel('daily')),
        };

        document.querySelectorAll('#pay_type').forEach((select) => {
            const form = select.closest('form');
            const label = form ? form.querySelector('#salary_label') : null;
            if (!label) return;

            const sync = () => {
                label.textContent = rateLabels[select.value] || rateLabels.monthly;
            };

            select.addEventListener('change', sync);
            sync();
        });
    })();
</script>
@endpush
