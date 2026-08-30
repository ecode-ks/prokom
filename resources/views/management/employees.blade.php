@extends('template.template')

{{-- Page Title --}}
@section('title', isset($editEmployee) ? 'Përditëso Punëtorin' : 'Punëtorët')

{{-- Page Content --}}
@section('content')
    @if(isset($editEmployee))
        <small class="d-block txt-lightblue fw-semibold text-uppercase"><a href="{{ route('employees') }}" class="text-decoration-none txt-lightblue fw-semibold"><i class="fa-solid fa-circle-chevron-left"></i> Kthehu Pas</a></small>
        <h3 class="fw-semibold pt-2">Përditëso Punëtorin</h3>
        <p class="text-muted">Përditëso detajet e punëtorit të zgjedhur.</p>
        <hr class="mb-4">
    @else
        <small class="d-block txt-lightblue fw-semibold text-uppercase">Stafi</small>
        <h3 class="fw-semibold pt-2">Punëtorët</h3>
        <p class="text-muted">Lista e të gjithë punonjësve të kompanisë. Të dhënat kryesore dhe rolet në një pamje.</p>
        <hr class="mb-4">
    @endif

    @if(isset($editEmployee))

        {{-- Update Employee --}}
        <div class="card mb-4">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-user-tie txt-lightblue py-2"></i> Përditëso Punëtorin</span>
            </div>
            <div class="card-body">
                <form action="{{ route('update.employee') }}" method="post">
                    <input type="hidden" name="id" value="{{ $editEmployee->id }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="employee" class="form-label">Emri dhe Mbiemri</label>
                            <input type="text" name="employee" id="employee" value="{{ old('employee', $editEmployee->employee) }}" class="form-control shadow-sm rounded-3" placeholder="Shkruani emrin dhe mbiemrin e punëtorit këtu...">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="phone" class="form-label">Telefoni</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone', $editEmployee->phone) }}" class="form-control shadow-sm rounded-3" placeholder="Shkruani numrin e telefonit të punëtorit këtu...">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $editEmployee->email) }}" class="form-control shadow-sm rounded-3" placeholder="Shkruani emailin e punëtorit këtu...">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="username" class="form-label">Përdoruesi</label>
                            <input type="text" name="username" id="username" value="{{ old('username', $editEmployee->username) }}" class="form-control shadow-sm rounded-3" placeholder="Shkruani përdoruesin e punëtorit këtu...">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="password" class="form-label">Fjalëkalimi (8+ Karaktere)</label>
                            <input type="password" name="password" id="password" class="form-control shadow-sm rounded-3" placeholder="Shkruani fjalëkalimin e punëtorit këtu...">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="role" class="form-label">Roli</label>
                            <select name="role" id="role" class="form-select shadow-sm rounded-3">
                                <option value="" disabled {{ old('role') ? '' : 'selected' }}>-- Zgjidh Rolin --</option>
                                <option value="manager" {{ old('role', $editEmployee->role) === 'manager' ? 'selected' : '' }}>Menaxher</option>
                                <option value="finance" {{ old('role', $editEmployee->role) === 'finance' ? 'selected' : '' }}>Financa</option>
                                <option value="hr" {{ old('role', $editEmployee->role) === 'hr' ? 'selected' : '' }}>HR</option>
                                <option value="employee" {{ old('role', $editEmployee->role) === 'employee' ? 'selected' : '' }}>Punëtor</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-check"></i> Përditëso Punëtorin</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @else

        {{-- Info Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-uppercase opacity-75"><i class="fa-solid fa-users text-dark"></i> Total</small>
                        <h4 class="fw-semibold mb-0">{{ $allEmployees ?? 0 }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-uppercase opacity-75"><i class="fa-solid fa-user-check text-success"></i> Aktiv</small>
                        <h4 class="fw-semibold mb-0">{{ $activeEmployees ?? 0 }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-uppercase opacity-75"><i class="fa-solid fa-user-minus text-danger"></i> Të bllokuar</small>
                        <h4 class="fw-semibold mb-0">{{ $blockedEmployees ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>

        {{-- Create Employee --}}
        <div class="card mb-4">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-user-plus txt-lightblue py-2"></i> Regjistro Punëtor të Ri</span>
            </div>
            <div class="card-body">
                <form action="{{ route('create.employee') }}" method="post">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="employee" class="form-label">Emri dhe Mbiemri</label>
                            <input type="text" name="employee" id="employee" value="{{ old('employee') }}" class="form-control shadow-sm rounded-3" placeholder="Shkruani emrin dhe mbiemrin e punëtorit këtu...">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="phone" class="form-label">Telefoni</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="form-control shadow-sm rounded-3" placeholder="Shkruani numrin e telefonit të punëtorit këtu...">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-control shadow-sm rounded-3" placeholder="Shkruani emailin e punëtorit këtu...">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="username" class="form-label">Përdoruesi</label>
                            <input type="text" name="username" id="username" value="{{ old('username') }}" class="form-control shadow-sm rounded-3" placeholder="Shkruani përdoruesin e punëtorit këtu...">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="password" class="form-label">Fjalëkalimi (8+ Karaktere)</label>
                            <input type="password" name="password" id="password" class="form-control shadow-sm rounded-3" placeholder="Shkruani fjalëkalimin e punëtorit këtu...">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="role" class="form-label">Roli</label>
                            <select name="role" id="role" class="form-select shadow-sm rounded-3">
                                <option value="" disabled {{ old('role') ? '' : 'selected' }}>-- Zgjidh Rolin --</option>
                                <option value="manager" {{ old('role') === 'manager' ? 'selected' : '' }}>Menaxher</option>
                                <option value="finance" {{ old('role') === 'finance' ? 'selected' : '' }}>Financa</option>
                                <option value="hr" {{ old('role') === 'hr' ? 'selected' : '' }}>HR</option>
                                <option value="employee" {{ old('role') === 'employee' ? 'selected' : '' }}>Punëtor</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-plus"></i> Regjistro Punëtorin</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Employees List --}}
        <div class="card">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-list-ol txt-lightblue py-2"></i> Lista e Punëtorëve</span>
            </div>
            <div class="card-body">
                <form action="{{ route('employees') }}" method="get" class="input-group mb-3">
                    @if(request('availability'))
                        <input type="hidden" name="availability" value="{{ request('availability') }}">
                    @endif
                    <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-control shadow-sm rounded-start-3" placeholder="Kërko..."  aria-label="Search..." aria-describedby="basic-addon2">
                    <button type="submit" class="btn btn-darkblue shadow-sm rounded-end-3" id="basic-addon2"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th scope="col" class="text-nowrap">#</th>
                                <th scope="col" class="text-nowrap">Punëtori</th>
                                <th scope="col" class="text-nowrap">Telefoni</th>
                                <th scope="col" class="text-nowrap">Email</th>
                                <th scope="col" class="text-nowrap text-end">Veprimet</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($employees) === 0)
                                <tr>
                                    <td colspan="5" class="text-center"><i class="fa-regular fa-face-frown"></i> Nuk u gjetën të dhëna...</td>
                                </tr>
                            @else
                                @foreach ($employees as $employee)
                                    <tr>
                                        <th scope="row" class="text-nowrap">{{ $employee->id }}</th>
                                        <td class="text-nowrap">{{ $employee->employee }}</td>
                                        <td class="text-nowrap">{{ $employee->phone }}</td>
                                        <td class="text-nowrap">{{ $employee->email }}</td>
                                        <td class="text-nowrap text-end">
                                            @if($employee->status === 'Active')
                                                <a href="{{ route('block.employee', $employee->id) }}" class="btn btn-danger btn-sm rounded-3 shadow-sm">Blloko</a>
                                            @else
                                                <a href="{{ route('activate.employee', $employee->id) }}" class="btn btn-success btn-sm rounded-3 shadow-sm">Aktivizo</a>
                                            @endif
                                            <a href="{{ route('edit.employee', $employee->id) }}" class="btn btn-outline-success btn-sm rounded-3 shadow-sm"><i class="fa-solid fa-pen"></i></a>
                                            <button class="btn btn-outline-danger btn-sm rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $employee->id }}"><i class="fa-solid fa-trash"></i></button>
                                            <button class="btn btn-outline-dark btn-sm rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#detailsModal{{ $employee->id }}"><i class="fa-solid fa-eye"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                @if ($employees->hasPages())
                    <div class="mt-3">
                        {{ $employees->links('vendor.pagination.custom') }}
                    </div>
                @endif
            </div>
        </div>

        @if(count($employees) !== 0)

            <!-- Delete Modal -->
            @foreach ($employees as $employee)
                <div class="modal fade" id="deleteModal{{ $employee->id }}" tabindex="-1" aria-labelledby="deleteModal{{ $employee->id }}Label" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-body text-center">
                                <p>Jeni i sigurt që dëshironi ta fshini këtë punëtor?</p>
                                <div class="mt-3">
                                    <a href="{{ route('delete.employee', $employee->id) }}" class="btn btn-danger btn-sm fw-semibold"><i class="fa-solid fa-check"></i> Konfirmo</a>
                                    <button class="btn btn-success btn-sm fw-semibold" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> Anulo</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Details Modal -->
            @foreach ($employees as $employee)
                <div class="modal modal-lg fade" id="detailsModal{{ $employee->id }}" tabindex="-1" aria-labelledby="detailsModalLabel{{ $employee->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-scrollable">
                        <div class="modal-content rounded-4">
                            <div class="modal-header justify-content-between align-items-center px-4">
                                <h1 class="modal-title fs-5 fw-semibold" id="detailsModalLabel{{ $employee->id }}">Detajet e Punëtorit <strong>#EMP-{{ $employee->id }}</strong></h1>
                                <button type="button" class="btn btn-darkblue btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
                            </div>
                            <div class="modal-body bg-custom px-4 py-5">
                                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-user"></i> Emri : </span>{{ $employee->employee }}</span>
                                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-phone"></i> Telefoni : </span>{{ $employee->phone }}</span>
                                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-envelope"></i> Email : </span>{{ $employee->email }}</span>
                                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-lock"></i> Përdoruesi : </span>{{ $employee->username }}</span>
                                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-user-tag"></i> Roli : </span>
                                    @if($employee->role === 'manager')
                                        <span class="badge rounded-pill txt-light bg-darkblue bg-opacity-25 border border-darkblue mb-4">Menaxher</span>
                                    @elseif($employee->role === 'finance')
                                        <span class="badge rounded-pill text-primary text-bg-primary bg-opacity-25 border border-primary mb-4">Financa</span>
                                    @elseif($employee->role === 'hr')
                                        <span class="badge rounded-pill text-success text-bg-success bg-opacity-25 border border-success mb-4">HR</span>
                                    @else
                                        <span class="badge rounded-pill text-secondary text-bg-secondary bg-opacity-25 border border-secondary mb-4">Punëtor</span>
                                    @endif
                                </span>
                                @if($employee->status === 'Active')
                                    <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-user"></i> Statusi : </span><span class="badge rounded-pill text-success text-bg-success bg-opacity-25 border border-success mb-4">Aktiv</span></span>
                                @else
                                    <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-user"></i> Statusi : </span><span class="badge rounded-pill text-danger text-bg-danger bg-opacity-25 border border-danger mb-4">I Bllokuar</span></span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    @endif
@endsection
