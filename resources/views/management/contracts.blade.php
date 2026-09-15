@extends('template.template')

{{-- Page Title --}}
@section('title', isset($editContract) ? __('pages.update_contract') : (isset($contractDetails) ? __('pages.contract_details') : __('pages.nav_contracts')))

{{-- Page Content --}}
@section('content')
    @php $isEmployee = ($role ?? '') === 'employee'; @endphp

    @if(isset($contractDetails))
        <small class="d-block txt-lightblue fw-semibold text-uppercase"><a href="{{ route('contracts') }}" class="text-decoration-none txt-lightblue fw-semibold"><i class="fa-solid fa-circle-chevron-left"></i> {{ __('pages.back_behind') }}</a></small>
        <h3 class="fw-semibold pt-2">{{ __('pages.contract_details') }}</h3>
        <p class="text-muted">{{ __('pages.contract_details_lead') }}</p>
        <hr class="mb-4">
    @elseif(isset($editContract))
        <small class="d-block txt-lightblue fw-semibold text-uppercase"><a href="{{ route('contracts') }}" class="text-decoration-none txt-lightblue fw-semibold"><i class="fa-solid fa-circle-chevron-left"></i> {{ __('pages.back_behind') }}</a></small>
        <h3 class="fw-semibold pt-2">{{ __('pages.update_contract') }}</h3>
        <p class="text-muted">{{ __('pages.update_contract_lead') }}</p>
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
                <span><i class="fa-solid fa-circle-info txt-lightblue py-2"></i> {{ __('pages.contract_details') }}</span>
            </div>
            <div class="card-body py-4">
                @if($contractDetails->status === 'Active')
                    <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> {{ __('pages.contract_status') }} : </span><span class="badge rounded-pill text-success text-bg-success bg-opacity-25 border border-success">{{ __('pages.contract_active') }}</span></span>
                @elseif($contractDetails->status === 'Expiring')
                    <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> {{ __('pages.contract_status') }} : </span><span class="badge rounded-pill text-dark text-bg-warning bg-opacity-25 border border-warning">{{ __('pages.contract_expiring') }}</span></span>
                @else
                    <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> {{ __('pages.contract_status') }} : </span><span class="badge rounded-pill text-danger text-bg-danger bg-opacity-25 border border-danger">{{ __('pages.contract_expired') }}</span></span>
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
                            {{ __('pages.no_documents') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @elseif(isset($editContract))

        {{-- Update Contract --}}
        <div class="card mb-4">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-file-contract txt-lightblue py-2"></i> {{ __('pages.update_contract') }}</span>
            </div>
            <div class="card-body">
                <form action="{{ route('update.contract') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{ $editContract->id }}">
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label for="employee_id" class="form-label">{{ __('pages.employee') }}</label>
                            <select name="employee_id" id="employee_id" class="form-control shadow-sm rounded-3">
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ old('employee_id', $editContract->employee_id) == $employee->id ? 'selected' : '' }}>{{ $employee->employee }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="position" class="form-label">{{ __('pages.position') }}</label>
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
                            <label for="extra_hours_price" class="form-label">{{ __('pages.extra_hours_price') }}</label>
                            <input type="number" step="0.01" name="extra_hours_price" id="extra_hours_price" value="{{ old('extra_hours_price', $editContract->extra_hours_price) }}" class="form-control shadow-sm rounded-3">
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="weekend_hours_price" class="form-label">{{ __('pages.weekend_hours_price') }}</label>
                            <input type="number" step="0.01" name="weekend_hours_price" id="weekend_hours_price" value="{{ old('weekend_hours_price', $editContract->weekend_hours_price) }}" class="form-control shadow-sm rounded-3">
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="docs" class="form-label">{{ __('pages.documents') }} <small class="text-muted">({{ __('pages.optional') }})</small></label>
                            <input type="file" name="docs[]" id="docs" class="form-control shadow-sm rounded-3" multiple>
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="from_date" class="form-label">{{ __('pages.valid_from') }}</label>
                            <input type="date" name="from_date" id="from_date" value="{{ old('from_date', $editContract->from_date) }}" class="form-control shadow-sm rounded-3">
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="to_date" class="form-label">{{ __('pages.valid_until') }}</label>
                            <input type="date" name="to_date" id="to_date" value="{{ old('to_date', $editContract->to_date) }}" class="form-control shadow-sm rounded-3">
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm">
                                <i class="fa-solid fa-check"></i> {{ __('pages.update_contract') }}
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
                    <span class="d-block fw-semibold"><i class="fa-solid fa-triangle-exclamation"></i> {{ trans_choice('pages.expiring_alert', $contractStats->expiring_count ?? 0, ['count' => $contractStats->expiring_count ?? 0]) }}</span>
                    <small class="d-block text-muted">{{ __('pages.expiring_plan') }}</small>
                </div>
            @else
                <div class="alert bg-success border-success rounded-4 shadow-sm bg-opacity-25" role="alert">
                    <span class="d-block fw-semibold"><i class="fa-solid fa-circle-check"></i> {{ __('pages.no_expiring') }}</span>
                    <small class="d-block text-muted">{{ __('pages.no_expiring_lead') }}</small>
                </div>
            @endif

            {{-- Info Cards --}}
            <div class="row g-3 mb-4">
                <div class="col-12 col-md-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <small class="text-uppercase opacity-75"><i class="fa-solid fa-file-signature text-success"></i> {{ __('pages.contract_active') }}</small>
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
                            <small class="text-uppercase opacity-75"><i class="fa-solid fa-hourglass-half text-warning"></i> {{ __('pages.contract_expiring') }}</small>
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
                            <small class="text-uppercase opacity-75"><i class="fa-solid fa-file-circle-xmark text-danger"></i> {{ __('pages.contract_expired') }}</small>
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
                    <span><i class="fa-solid fa-file-contract txt-lightblue py-2"></i> {{ __('pages.create_contract') }}</span>
                </div>
                <div class="card-body">
                    <form action="{{ route('create.contract') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12 col-md-4 col-lg-4">
                                <label for="employee_id" class="form-label">{{ __('pages.employee') }}</label>
                                <select name="employee_id" id="employee_id" class="form-control shadow-sm rounded-3">
                                    <option value="" disabled {{ old('employee_id') ? '' : 'selected' }}>{{ __('pages.choose_employee') }}</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>{{ $employee->employee }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-4 col-lg-4">
                                <label for="position" class="form-label">{{ __('pages.position') }}</label>
                                <input type="text" name="position" id="position" value="{{ old('position') }}" class="form-control shadow-sm rounded-3" placeholder="{{ __('pages.placeholder_position') }}">
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
                                <label for="salary" class="form-label" id="salary_label">{{ \App\Support\PayTypes::rateLabel(old('pay_type', 'monthly')) }}</label>
                                <input type="number" step="0.01" name="salary" id="salary" value="{{ old('salary') }}" class="form-control shadow-sm rounded-3" placeholder="Shkruani shumën...">
                            </div>
                            <div class="col-12 col-md-4 col-lg-4">
                                <label for="extra_hours_price" class="form-label">{{ __('pages.extra_hours_price') }}</label>
                                <input type="number" step="0.01" name="extra_hours_price" id="extra_hours_price" value="{{ old('extra_hours_price') }}" class="form-control shadow-sm rounded-3" placeholder="{{ __('pages.placeholder_extra_hours_price') }}">
                            </div>
                            <div class="col-12 col-md-4 col-lg-4">
                                <label for="weekend_hours_price" class="form-label">{{ __('pages.weekend_hours_price') }}</label>
                                <input type="number" step="0.01" name="weekend_hours_price" id="weekend_hours_price" value="{{ old('weekend_hours_price') }}" class="form-control shadow-sm rounded-3" placeholder="{{ __('pages.placeholder_weekend_hours_price') }}">
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="docs" class="form-label">{{ __('pages.documents') }}</label>
                                <input type="file" name="docs[]" id="docs" class="form-control shadow-sm rounded-3" multiple>
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="from_date" class="form-label">{{ __('pages.valid_from') }}</label>
                                <input type="date" name="from_date" id="from_date" value="{{ old('from_date') }}" class="form-control shadow-sm rounded-3">
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="to_date" class="form-label">{{ __('pages.valid_until') }}</label>
                                <input type="date" name="to_date" id="to_date" value="{{ old('to_date') }}" class="form-control shadow-sm rounded-3">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-plus"></i> {{ __('pages.create_contract_btn') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        {{-- Contracts List --}}
        <div class="card">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-list-ol txt-lightblue py-2"></i> {{ __('pages.contracts_list') }}</span>
            </div>
            <div class="card-body">
                <form action="{{ route('contracts') }}" method="get" class="input-group mb-3">
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-control shadow-sm rounded-start-3" placeholder="{{ __('pages.search') }}"  aria-label="{{ __('pages.search') }}" aria-describedby="basic-addon2">
                    <button type="submit" class="btn btn-darkblue shadow-sm rounded-end-3" id="basic-addon2"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th scope="col" class="text-nowrap">#</th>
                                <th scope="col" class="text-nowrap">{{ __('pages.employee') }}</th>
                                <th scope="col" class="text-nowrap">{{ __('pages.position') }}</th>
                                <th scope="col" class="text-nowrap">{{ __('pages.phone') }}</th>
                                <th scope="col" class="text-nowrap">{{ __('pages.validity') }}</th>
                                <th scope="col" class="text-nowrap">{{ __('pages.status') }}</th>
                                <th scope="col" class="text-nowrap text-end">{{ __('pages.operations') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($contracts) === 0)
                                <tr>
                                    <td colspan="9" class="text-center"><i class="fa-regular fa-face-frown"></i> {{ __('pages.no_data') }}</td>
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
                                                <span class="badge rounded-pill text-success text-bg-success bg-opacity-25 border border-success">{{ __('pages.contract_active') }}</span>
                                            @elseif($contract->status === 'Expiring')
                                                <span class="badge rounded-pill text-dark text-bg-warning bg-opacity-25 border border-warning">{{ __('pages.contract_expiring') }}</span>
                                            @else
                                                <span class="badge rounded-pill text-danger text-bg-danger bg-opacity-25 border border-danger">{{ __('pages.contract_expired') }}</span>
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
                                <p>{{ __('pages.confirm_delete_contract') }}</p>
                                <div class="mt-3">
                                    <a href="{{ route('delete.contract', $contract->id) }}" class="btn btn-danger btn-sm fw-semibold"><i class="fa-solid fa-check"></i> {{ __('pages.confirm') }}</a>
                                    <button class="btn btn-success btn-sm fw-semibold" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> {{ __('pages.cancel') }}</button>
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
