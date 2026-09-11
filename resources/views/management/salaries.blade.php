@extends('template.template')

{{-- Page Title --}}
@section('title', isset($editSalary) ? __('pages.update_salary') : __('pages.nav_salaries'))

{{-- Page Content --}}
@section('content')
    @php $isEmployee = ($role ?? '') === 'employee'; @endphp

    @if(isset($editSalary))
        <small class="d-block txt-lightblue fw-semibold text-uppercase"><a href="{{ route('salaries') }}" class="text-decoration-none txt-lightblue fw-semibold"><i class="fa-solid fa-circle-chevron-left"></i> {{ __('pages.back') }}</a></small>
        <h3 class="fw-semibold pt-2">{{ __('pages.update_salary') }}</h3>
        <p class="text-muted">{{ __('pages.update_salary_lead') }}</p>
        <hr class="mb-4">
    @else
        <small class="d-block txt-lightblue fw-semibold text-uppercase">{{ __('pages.nav_salaries') }}</small>
        <h3 class="fw-semibold pt-2">{{ $isEmployee ? __('pages.my_salaries') : __('pages.manage_salaries') }}</h3>
        <p class="text-muted">{{ $isEmployee ? __('pages.salaries_lead_employee') : __('pages.salaries_lead_staff') }}</p>
        <hr class="mb-4">
    @endif

    @if(isset($editSalary))

        {{-- Update Salary --}}
        <div class="card mb-4">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-layer-group txt-lightblue py-2"></i> {{ __('pages.update_salary') }}</span>
            </div>
            <div class="card-body">
                <form action="{{ route('update.salary') }}" method="post">
                    <input type="hidden" name="id" value="{{ $editSalary->id }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="employee_id" class="form-label">{{ __('pages.employee') }}</label>
                            <select name="employee_id" id="employee_id" class="form-control shadow-sm rounded-3">
                                <option value="" disabled {{ old('employee_id', $editSalary->employee_id) ? '' : 'selected' }}>{{ __('pages.choose_employee') }}</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ old('employee_id', $editSalary->employee_id) == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->employee }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="salary" class="form-label">{{ __('pages.salary') }}</label>
                            <input type="text" name="salary" id="salary" value="{{ old('salary', $editSalary->salary) }}" class="form-control shadow-sm rounded-3" placeholder="{{ __('pages.placeholder_salary_short') }}">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-check"></i> {{ __('pages.update_salary') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @else

        @if(!$isEmployee)
            {{-- Create Salary --}}
            <div class="card mb-4">
                <div class="card-header rounded-top-4 bg-white">
                    <span><i class="fa-solid fa-layer-group txt-lightblue py-2"></i> {{ __('pages.register_new_salary') }}</span>
                </div>
                <div class="card-body">
                    <form action="{{ route('create.salary') }}" method="post">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12 col-md-4 col-lg-4">
                                <label for="employee_id" class="form-label">{{ __('pages.employee') }}</label>
                                <select name="employee_id" id="employee_id" class="form-control shadow-sm rounded-3">
                                    <option value="" disabled {{ old('employee_id') ? '' : 'selected' }}>{{ __('pages.choose_employee') }}</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->employee }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-4 col-lg-4">
                                <label for="salary" class="form-label">{{ __('pages.salary') }}</label>
                                <input type="number" step="0.01" name="salary" id="salary" value="{{ old('salary') }}" class="form-control shadow-sm rounded-3" placeholder="{{ __('pages.placeholder_salary_short') }}">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-plus"></i> {{ __('pages.register_salary') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        {{-- Salaries List --}}
        <div class="card">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-list-ol txt-lightblue py-2"></i> {{ __('pages.salaries_list') }}</span>
            </div>
            <div class="card-body">
                <form action="{{ route('salaries') }}" method="get" class="input-group mb-3">
                    <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-control shadow-sm rounded-start-3" placeholder="{{ __('pages.search') }}" aria-label="{{ __('pages.search') }}" aria-describedby="basic-addon2">
                    <button type="submit" class="btn btn-darkblue shadow-sm rounded-end-3" id="basic-addon2"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th scope="col" class="text-nowrap">#</th>
                                <th scope="col" class="text-nowrap">{{ __('pages.employee') }}</th>
                                <th scope="col" class="text-nowrap">{{ __('pages.salary') }}</th>
                                <th scope="col" class="text-nowrap">{{ __('pages.date') }}</th>
                                @if(!$isEmployee)
                                    <th scope="col" class="text-nowrap text-end">{{ __('pages.operations') }}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($salaries) === 0)
                                <tr>
                                    <td colspan="{{ $isEmployee ? 4 : 5 }}" class="text-center"><i class="fa-regular fa-face-frown"></i> {{ __('pages.no_data') }}</td>
                                </tr>
                            @else
                                @foreach ($salaries as $salary)
                                    <tr>
                                        <th scope="row" class="text-nowrap">{{ $salary->id }}</th>
                                        <td class="text-nowrap">{{ $salary->employee }}</td>
                                        <td class="text-nowrap">{{ $salary->salary }}€</td>
                                        <td class="text-nowrap">{{ \Carbon\Carbon::parse($salary->date)->format('d/m/Y') }}</td>
                                        @if(!$isEmployee)
                                            <td class="text-nowrap text-end">
                                                <a href="{{ route('edit.salary', $salary->id) }}" class="btn btn-outline-success btn-sm rounded-3 shadow-sm"><i class="fa-solid fa-pen"></i></a>
                                                <button class="btn btn-outline-danger btn-sm rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $salary->id }}"><i class="fa-solid fa-trash"></i></button>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                @if ($salaries->hasPages())
                    <div class="mt-3">
                        {{ $salaries->links('vendor.pagination.custom') }}
                    </div>
                @endif
            </div>
        </div>

        @if(!$isEmployee && count($salaries) !== 0)

            <!-- Delete Modal -->
            @foreach ($salaries as $salary)
                <div class="modal fade" id="deleteModal{{ $salary->id }}" tabindex="-1" aria-labelledby="deleteModal{{ $salary->id }}Label" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-body text-center">
                                <p>{{ __('pages.confirm_delete_salary') }}</p>
                                <div class="mt-3">
                                    <a href="{{ route('delete.salary', $salary->id) }}" class="btn btn-danger btn-sm fw-semibold"><i class="fa-solid fa-check"></i> {{ __('pages.confirm') }}</a>
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
