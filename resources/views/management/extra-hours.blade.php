@extends('template.template')

{{-- Page Title --}}
@section('title', isset($editExtraHour) ? 'Përditëso Orët Shtesë' : 'Orët Shtesë')

{{-- Page Content --}}
@section('content')
    @php $isEmployee = ($role ?? '') === 'employee'; @endphp

    @if(isset($editExtraHour))
        <small class="d-block txt-lightblue fw-semibold text-uppercase"><a href="{{ route('extra.hours') }}" class="text-decoration-none txt-lightblue fw-semibold"><i class="fa-solid fa-circle-chevron-left"></i> Kthehu</a></small>
        <h3 class="fw-semibold pt-2">Përditëso Orët Shtesë</h3>
        <p class="text-muted">Përditëso të dhënat e orëve shtesë të zgjedhura.</p>
        <hr class="mb-4">
    @else
        <small class="d-block txt-lightblue fw-semibold text-uppercase">Orët Shtesë</small>
        <h3 class="fw-semibold pt-2">{{ $isEmployee ? 'Orët e Mia Shtesë' : 'Menaxho Orët Shtesë' }}</h3>
        <p class="text-muted">{{ $isEmployee ? 'Shiko orët jashtë orarit dhe orët në vikend.' : 'Monitoro dhe menaxho orët jashtë orarit dhe orët në vikend.' }}</p>
        <hr class="mb-4">
    @endif

    @if(isset($editExtraHour))

        {{-- Update Extra Hours --}}
        <div class="card mb-4">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-business-time txt-lightblue py-2"></i> Përditëso Orët Shtesë</span>
            </div>
            <div class="card-body">
                <form action="{{ route('update.extra.hour') }}" method="post">
                    <input type="hidden" name="id" value="{{ $editExtraHour->id }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="employee_id" class="form-label">Punonjësi</label>
                            <select name="employee_id" id="employee_id" class="form-control shadow-sm rounded-3" required>
                                <option value="" disabled {{ old('employee_id', $editExtraHour->employee_id) ? '' : 'selected' }}>-- Zgjidh Punonjësin --</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ old('employee_id', $editExtraHour->employee_id) == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->employee }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="hours" class="form-label">Orët</label>
                            <input type="number" step="0.01" min="0.01" name="hours" id="hours" value="{{ old('hours', $editExtraHour->hours) }}" class="form-control shadow-sm rounded-3" placeholder="Shkruaj orët..." required>
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="type" class="form-label">Tipi</label>
                            <select name="type" id="type" class="form-control shadow-sm rounded-3" required>
                                <option value="" disabled {{ old('type', $editExtraHour->type) ? '' : 'selected' }}>-- Zgjidh Tipin --</option>
                                @foreach ($types as $type)
                                    <option value="{{ $type }}" {{ old('type', $editExtraHour->type) === $type ? 'selected' : '' }}>{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="date" class="form-label">Data</label>
                            <input type="date" name="date" id="date" value="{{ old('date', $editExtraHour->date) }}" class="form-control shadow-sm rounded-3" required>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-check"></i> Përditëso Orët Shtesë</button>
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
                    <span><i class="fa-solid fa-business-time txt-lightblue py-2"></i> Regjistro Orë Shtesë</span>
                </div>
                <div class="card-body">
                    <form action="{{ route('create.extra.hour') }}" method="post">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12 col-md-4 col-lg-4">
                                <label for="employee_id" class="form-label">Punonjësi</label>
                                <select name="employee_id" id="employee_id" class="form-control shadow-sm rounded-3" required>
                                    <option value="" disabled {{ old('employee_id') ? '' : 'selected' }}>-- Zgjidh Punonjësin --</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->employee }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-4 col-lg-4">
                                <label for="hours" class="form-label">Orët</label>
                                <input type="number" step="0.01" min="0.01" name="hours" id="hours" value="{{ old('hours') }}" class="form-control shadow-sm rounded-3" placeholder="Shkruaj orët..." required>
                            </div>
                            <div class="col-12 col-md-4 col-lg-4">
                                <label for="type" class="form-label">Tipi</label>
                                <select name="type" id="type" class="form-control shadow-sm rounded-3" required>
                                    <option value="" disabled {{ old('type') ? '' : 'selected' }}>-- Zgjidh Tipin --</option>
                                    @foreach ($types as $type)
                                        <option value="{{ $type }}" {{ old('type') === $type ? 'selected' : '' }}>{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-4 col-lg-4">
                                <label for="date" class="form-label">Data</label>
                                <input type="date" name="date" id="date" value="{{ old('date', date('Y-m-d')) }}" class="form-control shadow-sm rounded-3" required>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-plus"></i> Regjistro Orët Shtesë</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        {{-- Extra Hours List --}}
        <div class="card">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-list-ol txt-lightblue py-2"></i> Lista e Orëve Shtesë</span>
            </div>
            <div class="card-body">
                <form action="{{ route('extra.hours') }}" method="get" class="input-group mb-3">
                    <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-control shadow-sm rounded-start-3" placeholder="Kërko..." aria-label="Kërko..." aria-describedby="basic-addon2">
                    <button type="submit" class="btn btn-darkblue shadow-sm rounded-end-3" id="basic-addon2"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th scope="col" class="text-nowrap">#</th>
                                <th scope="col" class="text-nowrap">Punonjësi</th>
                                <th scope="col" class="text-nowrap">Orët</th>
                                <th scope="col" class="text-nowrap">Tipi</th>
                                <th scope="col" class="text-nowrap">Data</th>
                                @if(!$isEmployee)
                                    <th scope="col" class="text-nowrap text-end">Operacionet</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($extraHours) === 0)
                                <tr>
                                    <td colspan="{{ $isEmployee ? 5 : 6 }}" class="text-center"><i class="fa-regular fa-face-frown"></i> Nuk u gjetën të dhëna...</td>
                                </tr>
                            @else
                                @foreach ($extraHours as $extraHour)
                                    <tr>
                                        <th scope="row" class="text-nowrap">{{ $extraHour->id }}</th>
                                        <td class="text-nowrap">{{ $extraHour->employee }}</td>
                                        <td class="text-nowrap">{{ $extraHour->hours }}</td>
                                        <td class="text-nowrap">{{ $extraHour->type }}</td>
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
                                <p>A jeni i sigurt që dëshironi t’i fshini këto orë shtesë?</p>
                                <div class="mt-3">
                                    <a href="{{ route('delete.extra.hour', $extraHour->id) }}" class="btn btn-danger btn-sm fw-semibold"><i class="fa-solid fa-check"></i> Konfirmo</a>
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
