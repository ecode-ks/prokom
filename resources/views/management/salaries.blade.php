@extends('template.template')

{{-- Page Title --}}
@section('title', isset($editSalary) ? 'Përditëso Pagën' : 'Pagat')

{{-- Page Content --}}
@section('content')
    @if(isset($editSalary))
        <small class="d-block txt-lightblue fw-semibold text-uppercase"><a href="{{ route('salaries') }}" class="text-decoration-none txt-lightblue fw-semibold"><i class="fa-solid fa-circle-chevron-left"></i> Kthehu</a></small>
        <h3 class="fw-semibold pt-2">Përditëso Pagën</h3>
        <p class="text-muted">Përditëso të dhënat e pagës së zgjedhur.</p>
        <hr class="mb-4">
    @else
        <small class="d-block txt-lightblue fw-semibold text-uppercase">Pagat</small>
        <h3 class="fw-semibold pt-2">Menaxho Pagat</h3>
        <p class="text-muted">Monitoro dhe menaxho pagat e punonjësve.</p>
        <hr class="mb-4">
    @endif

    @if(isset($editSalary))

        {{-- Update Salary --}}
        <div class="card mb-4">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-layer-group txt-lightblue py-2"></i> Përditëso Pagën</span>
            </div>
            <div class="card-body">
                <form action="{{ route('update.salary') }}" method="post">
                    <input type="hidden" name="id" value="{{ $editSalary->id }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="employee_id" class="form-label">Punonjësi</label>
                            <select name="employee_id" id="employee_id" class="form-control shadow-sm rounded-3">
                                <option value="" disabled {{ old('employee_id', $editSalary->employee_id) ? '' : 'selected' }}>-- Zgjidh Punonjësin --</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ old('employee_id', $editSalary->employee_id) == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->employee }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="salary" class="form-label">Paga</label>
                            <input type="text" name="salary" id="salary" value="{{ old('salary', $editSalary->salary) }}" class="form-control shadow-sm rounded-3" placeholder="Shkruaj pagën...">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-check"></i> Përditëso Pagën</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @else

        {{-- Create Salary --}}
        <div class="card mb-4">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-layer-group txt-lightblue py-2"></i> Regjistro Pagë të Re</span>
            </div>
            <div class="card-body">
                <form action="{{ route('create.salary') }}" method="post">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="employee_id" class="form-label">Punonjësi</label>
                            <select name="employee_id" id="employee_id" class="form-control shadow-sm rounded-3">
                                <option value="" disabled {{ old('employee_id') ? '' : 'selected' }}>-- Zgjidh Punonjësin --</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->employee }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="salary" class="form-label">Paga</label>
                            <input type="number" step="0.01" name="salary" id="salary" value="{{ old('salary') }}" class="form-control shadow-sm rounded-3" placeholder="Shkruaj pagën...">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-plus"></i> Regjistro Pagën</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Salaries List --}}
        <div class="card">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-list-ol txt-lightblue py-2"></i> Lista e Pagave</span>
            </div>
            <div class="card-body">
                <form action="{{ route('salaries') }}" method="get" class="input-group mb-3">
                    <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-control shadow-sm rounded-start-3" placeholder="Kërko..." aria-label="Kërko..." aria-describedby="basic-addon2">
                    <button type="submit" class="btn btn-darkblue shadow-sm rounded-end-3" id="basic-addon2"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th scope="col" class="text-nowrap">#</th>
                                <th scope="col" class="text-nowrap">Data</th>
                                <th scope="col" class="text-nowrap">Punonjësi</th>
                                <th scope="col" class="text-nowrap">Paga</th>
                                <th scope="col" class="text-nowrap text-end">Operacionet</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($salaries) === 0)
                                <tr>
                                    <td colspan="5" class="text-center"><i class="fa-regular fa-face-frown"></i> Nuk u gjetën të dhëna...</td>
                                </tr>
                            @else
                                @foreach ($salaries as $salary)
                                    <tr>
                                        <th scope="row" class="text-nowrap">{{ $salary->id }}</th>
                                        <td class="text-nowrap">{{ \Carbon\Carbon::parse($salary->date)->format('d/m/Y') }}</td>
                                        <td class="text-nowrap">{{ $salary->employee }}</td>
                                        <td class="text-nowrap">{{ $salary->salary }}€</td>
                                        <td class="text-nowrap text-end">
                                            <a href="{{ route('edit.salary', $salary->id) }}" class="btn btn-outline-success btn-sm rounded-3 shadow-sm"><i class="fa-solid fa-pen"></i></a>
                                            <button class="btn btn-outline-danger btn-sm rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $salary->id }}"><i class="fa-solid fa-trash"></i></button>
                                        </td>
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

        @if(count($salaries) !== 0)

            <!-- Delete Modal -->
            @foreach ($salaries as $salary)
                <div class="modal fade" id="deleteModal{{ $salary->id }}" tabindex="-1" aria-labelledby="deleteModal{{ $salary->id }}Label" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-body text-center">
                                <p>A jeni i sigurt që dëshironi ta fshini këtë pagë?</p>
                                <div class="mt-3">
                                    <a href="{{ route('delete.salary', $salary->id) }}" class="btn btn-danger btn-sm fw-semibold"><i class="fa-solid fa-check"></i> Konfirmo</a>
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
