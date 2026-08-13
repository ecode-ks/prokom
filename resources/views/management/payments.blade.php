@extends('template.template')

{{-- Page Title --}}
@section('title', 'Pagesat e Kompanive')

{{-- Page Content --}}
@section('content')
    <small class="d-block txt-lightblue fw-semibold text-uppercase">Kompanitë</small>
    <h3 class="fw-semibold pt-2">Pagesat e Kompanive</h3>
    <p class="text-muted">Të gjitha pagesat e kompanive të regjistruara.</p>
    <hr class="mb-4">

    <div class="card">
        <div class="card-header rounded-top-4 bg-white">
            <span><i class="fa-solid fa-list-ol txt-lightblue py-2"></i> Lista e Pagesave</span>
        </div>
        <div class="card-body">
            <form action="{{ route('payments') }}" method="get" class="input-group mb-3">
                <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-control shadow-sm rounded-start-3" placeholder="Kërko..." aria-label="Search..." aria-describedby="basic-addon2">
                <button type="submit" class="btn btn-darkblue shadow-sm rounded-end-3" id="basic-addon2"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th scope="col" class="text-nowrap">Data</th>
                            <th scope="col" class="text-nowrap">Kompania</th>
                            <th scope="col" class="text-nowrap">Paketa</th>
                            <th scope="col" class="text-nowrap">Çmimi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($payments) === 0)
                            <tr>
                                <td colspan="4" class="text-center"><i class="fa-regular fa-face-frown"></i> Nuk u gjet asnjë e dhënë...</td>
                            </tr>
                        @else
                            @foreach ($payments as $payment)
                                <tr>
                                    <td class="text-nowrap">{{ \Carbon\Carbon::parse($payment->date)->format('d/m/Y') }}</td>
                                    <td class="text-nowrap">{{ $payment->company }}</td>
                                    <td class="text-nowrap">{{ $payment->package }}</td>
                                    <td class="text-nowrap">{{ number_format($payment->price, 2) }}€</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>

            @if ($payments->hasPages())
                <div class="mt-3">
                    {{ $payments->links('vendor.pagination.custom') }}
                </div>
            @endif
        </div>
    </div>
@endsection
