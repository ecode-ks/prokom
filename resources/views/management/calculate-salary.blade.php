@extends('template.template')

@section('title', 'Kalkulimi i Pagave')

@section('content')
    <small class="d-block txt-lightblue fw-semibold text-uppercase">Financat</small>
    <h3 class="fw-semibold pt-2">Kalkulimi i Pagave</h3>
    <p class="text-muted">Filtro periudhën dhe shiko përmbledhjen e pagave.</p>
    <hr class="mb-4">

    @php
        $currentDateFormated = now()->format('d/m/Y');
        $toDateFormated = now()->addDays(30)->format('d/m/Y');
        $currentDate = now()->format('Y-m-d');
        $toDate = now()->addDays(30)->format('Y-m-d');
    @endphp

    <div class="card mb-4">
        <div class="card-body">
            <div class="mb-4">
                <small class="text-uppercase opacity-75 d-block mb-1">Periudha e Raportit</small>
                <h5 class="mb-0"><strong>{{ $currentDateFormated }}</strong> - <strong>{{ $toDateFormated }}</strong></h5>
            </div>
            <form action="{{ route('calculate') }}" method="get">
                <div class="row g-3">
                    <div class="col-12 col-md-4 col-lg-4">
                        <label for="from_date" class="form-label">Nga data</label>
                        <input type="date" name="from_date" value="{{ request('from_date', $currentDate) }}" class="form-control shadow-sm rounded-3" placeholder="Nga data">
                    </div>
                    <div class="col-12 col-md-4 col-lg-4">
                        <label for="to_date" class="form-label">Deri me</label>
                        <input type="date" name="to_date" value="{{ request('to_date', $toDate) }}" class="form-control shadow-sm rounded-3" placeholder="Deri me">
                    </div>
                    <div class="col-12 col-md-4 col-lg-4">
                        <label for="employee_id" class="form-label">Punëtori</label>
                        <select name="employee_id" class="form-control shadow-sm rounded-3">
                            <option value="">Të gjithë punëtorët</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->employee }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-calculator"></i> Kalkulo Pagat</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
