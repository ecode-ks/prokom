@extends('template.template')

{{-- Page Title --}}
@section('title', 'Kompanitë')

{{-- Page Content --}}
@section('content')
    <small class="d-block txt-lightblue fw-semibold text-uppercase">Përmbledhje</small>
    <h3 class="fw-semibold pt-2">Kompanitë</h3>
    <p class="text-muted">Të gjitha kompanitë e regjistruara me paketën, abonimin dhe statusin e llogarisë.</p>
    <hr class="mb-4">

    {{-- Info Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-uppercase opacity-75"><i class="fa-solid fa-building text-primary"></i> Total</small>
                    <h4 class="fw-semibold mb-0">{{ $companyStats->total_count ?? 0 }}</h4>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-uppercase opacity-75"><i class="fa-solid fa-circle-check text-success"></i> Aktive</small>
                    <h4 class="fw-semibold mb-0">{{ $companyStats->active_count ?? 0 }}</h4>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-uppercase opacity-75"><i class="fa-solid fa-ban text-danger"></i> Jo aktive</small>
                    <h4 class="fw-semibold mb-0">{{ $companyStats->inactive_count ?? 0 }}</h4>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-uppercase opacity-75"><i class="fa-solid fa-hourglass-half text-warning"></i> Abonim skadon së shpejti</small>
                    <h4 class="fw-semibold mb-0">{{ $companyStats->expiring_soon_count ?? 0 }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header rounded-top-4 bg-white">
            <span><i class="fa-solid fa-list-ol txt-lightblue py-2"></i> Lista e Kompanive</span>
        </div>
        <div class="card-body">
            <form action="{{ route('companies') }}" method="get" class="input-group mb-3">
                <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-control shadow-sm rounded-start-3" placeholder="Kërko..." aria-label="Search..." aria-describedby="basic-addon2">
                <button type="submit" class="btn btn-darkblue shadow-sm rounded-end-3" id="basic-addon2"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th scope="col" class="text-nowrap">Kompania</th>
                            <th scope="col" class="text-nowrap">Email</th>
                            <th scope="col" class="text-nowrap">Telefoni</th>
                            <th scope="col" class="text-nowrap">Statusi</th>
                            <th scope="col" class="text-nowrap text-end">Veprimet</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($companies) === 0)
                            <tr>
                                <td colspan="5" class="text-center"><i class="fa-regular fa-face-frown"></i> Nuk u gjet asnjë e dhënë...</td>
                            </tr>
                        @else
                            @foreach ($companies as $company)
                                <tr>
                                    <td class="text-nowrap">{{ $company->company }}</td>
                                    <td class="text-nowrap">{{ $company->email }}</td>
                                    <td class="text-nowrap">{{ $company->phone }}</td>
                                    <td class="text-nowrap">
                                        @if($company->status === 'Active')
                                            <span class="badge rounded-pill text-success text-bg-success bg-opacity-25 border border-success">Aktive</span>
                                        @else
                                            <span class="badge rounded-pill text-danger text-bg-danger bg-opacity-25 border border-danger">Bllokuar</span>
                                        @endif
                                    </td>
                                    <td class="text-nowrap text-end">
                                        <a href="{{ route('company.details', $company->id) }}" class="btn btn-outline-dark btn-sm rounded-3 shadow-sm"><i class="fa-solid fa-eye"></i></a>
                                        <button class="btn btn-outline-danger btn-sm rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $company->id }}"><i class="fa-solid fa-trash"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>

            @if ($companies->hasPages())
                <div class="mt-3">
                    {{ $companies->links('vendor.pagination.custom') }}
                </div>
            @endif
        </div>
    </div>

    @if(count($companies) !== 0)
        @foreach ($companies as $company)
            <div class="modal fade" id="deleteModal{{ $company->id }}" tabindex="-1" aria-labelledby="deleteModal{{ $company->id }}Label" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-body text-center">
                            <p>Konfirmoni nëse dëshironi vërtet të fshini këtë kompani?</p>
                            <div class="mt-3">
                                <a href="{{ route('delete.company', $company->id) }}" class="btn btn-danger btn-sm fw-semibold"><i class="fa-solid fa-check"></i> Konfirmo</a>
                                <button class="btn btn-success btn-sm fw-semibold" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> Anulo</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
@endsection
