@extends('template.template')

{{-- Page Title --}}
@section('title', 'Dashboard')

{{-- Page Content --}}
@section('content')
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4" style="background: linear-gradient(135deg, rgba(13,110,253,0.09), rgba(13,110,253,0.02)); border-radius: 1rem;">
            <small class="d-block txt-lightblue fw-semibold text-uppercase">Dashboard · {{ date('d/m/Y') }}</small>
            <h3 class="fw-semibold pt-2 mb-1">Mirë se erdhët, <span class="txt-lightblue fw-semibold">{{ session('user') ?? session('company') }}</span>.</h3>
        </div>
    </div>
@endsection
