@extends('template.template')

{{-- Page Title --}}
@section('title', isset($editPackage) ? 'Përditëso Paketën' : 'Paketat')

{{-- Page Content --}}
@section('content')
    @if(isset($editPackage))
        <small class="d-block txt-lightblue fw-semibold text-uppercase"><a href="{{ route('packages') }}" class="text-decoration-none txt-lightblue fw-semibold"><i class="fa-solid fa-circle-chevron-left"></i> Kthehu Pas</a></small>
        <h3 class="fw-semibold pt-2">Përditëso Paketën</h3>
        <p class="text-muted">Përditëso të dhënat e paketës së zgjedhur.</p>
        <hr class="mb-4">
    @else
        <small class="d-block txt-lightblue fw-semibold text-uppercase">Paketat</small>
        <h3 class="fw-semibold pt-2">Paketat</h3>
        <p class="text-muted">Shiko, krijo dhe menaxho paketat dhe abonimet.</p>
        <hr class="mb-4">
    @endif

    @if(isset($editPackage))

        {{-- Update Package --}}
        <div class="card mb-4">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-brands fa-trello txt-lightblue py-2"></i> Përditëso Paketën</span>
            </div>
            <div class="card-body">
                <form action="{{ route('update.package') }}" method="post">
                    <input type="hidden" name="id" value="{{ $editPackage->id }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="title" class="form-label">Titulli i Paketës</label>
                            <input type="text" name="title" id="title" value="{{ old('title', $editPackage->title) }}" class="form-control shadow-sm rounded-3" placeholder="Shkruani titullin e paketës...">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="price" class="form-label">Çmimi</label>
                            <input type="text" name="price" id="price" value="{{ old('price', $editPackage->price) }}" class="form-control shadow-sm rounded-3" placeholder="Shkruani çmimin e paketës...">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="months" class="form-label">Kohëzgjatja (Muaj)</label>
                            <input type="text" name="months" id="months" value="{{ old('months', $editPackage->months) }}" class="form-control shadow-sm rounded-3" placeholder="Shkruani kohëzgjatjen...">
                        </div>
                        <div class="col-12">
                            <label for="description" class="form-label">Përshkrimi</label>
                            <textarea name="description" id="description" class="form-control shadow-sm rounded-3" placeholder="Shkruani përshkrimin e paketës..." rows="5">{{ old('description', $editPackage->description) }}</textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-check"></i> Përditëso Paketën</button>
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
                        <small class="text-uppercase opacity-75"><i class="fa-solid fa-box text-primary"></i> Total paketa</small>
                        <h4 class="fw-semibold mb-0">{{ $packageStats->total_count ?? 0 }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-uppercase opacity-75"><i class="fa-solid fa-tag text-success"></i> Çmimi mesatar</small>
                        <h4 class="fw-semibold mb-0">€{{ number_format((float) ($packageStats->avg_price ?? 0), 2) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-uppercase opacity-75"><i class="fa-solid fa-calendar text-warning"></i> Muaj mesatarisht</small>
                        <h4 class="fw-semibold mb-0">{{ number_format((float) ($packageStats->avg_months ?? 0), 1) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        {{-- Create Package --}}
        <div class="card mb-4">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-brands fa-trello txt-lightblue py-2"></i> Krijo Paketë të Re</span>
            </div>
            <div class="card-body">
                <form action="{{ route('create.package') }}" method="post">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="title" class="form-label">Titulli i Paketës</label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}" class="form-control shadow-sm rounded-3" placeholder="Shkruani titullin e paketës...">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="price" class="form-label">Çmimi</label>
                            <input type="text" name="price" id="price" value="{{ old('price') }}" class="form-control shadow-sm rounded-3" placeholder="Shkruani çmimin e paketës...">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="months" class="form-label">Kohëzgjatja (Muaj)</label>
                            <input type="text" name="months" id="months" value="{{ old('months') }}" class="form-control shadow-sm rounded-3" placeholder="Shkruani kohëzgjatjen...">
                        </div>
                        <div class="col-12">
                            <label for="description" class="form-label">Përshkrimi</label>
                            <textarea name="description" id="description" class="form-control shadow-sm rounded-3" placeholder="Shkruani përshkrimin e paketës..." rows="5">{{ old('description') }}</textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-plus"></i> Krijo Paketën</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Packages List --}}
        <div class="card">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-list-ol txt-lightblue py-2"></i> Lista e Paketave</span>
            </div>
            <div class="card-body">
                <form action="{{ route('packages') }}" method="get" class="input-group mb-3">
                    <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-control shadow-sm rounded-start-3" placeholder="Kërko..."  aria-label="Search..." aria-describedby="basic-addon2">
                    <button type="submit" class="btn btn-darkblue shadow-sm rounded-end-3" id="basic-addon2"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th scope="col" class="text-nowrap">#</th>
                                <th scope="col" class="text-nowrap">Titulli</th>
                                <th scope="col" class="text-nowrap">Çmimi</th>
                                <th scope="col" class="text-nowrap">Kohëzgjatja</th>
                                <th scope="col" class="text-nowrap text-end">Veprimet</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($packages) === 0)
                                <tr>
                                    <td colspan="5" class="text-center"><i class="fa-regular fa-face-frown"></i> Nuk u gjet asnjë e dhënë...</td>
                                </tr>
                            @else
                                @foreach ($packages as $package)
                                    <tr>
                                        <th scope="row" class="text-nowrap">{{ $package->id }}</th>
                                        <td class="text-nowrap">{{ $package->title }}</td>
                                        <td class="text-nowrap">€{{ number_format($package->price, 2) }}</td>
                                        <td class="text-nowrap">{{ $package->months }} <small class="text-muted">(Muaj)</small></td>
                                        <td class="text-nowrap text-end">
                                            <a href="{{ route('edit.package', $package->id) }}" class="btn btn-outline-success btn-sm rounded-3 shadow-sm"><i class="fa-solid fa-pen"></i></a>
                                            <button class="btn btn-outline-danger btn-sm rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $package->id }}"><i class="fa-solid fa-trash"></i></button>
                                            <button class="btn btn-outline-dark btn-sm rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#commentModal{{ $package->id }}"><i class="fa-solid fa-eye"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                @if ($packages->hasPages())
                    <div class="mt-3">
                        {{ $packages->links('vendor.pagination.custom') }}
                    </div>
                @endif
            </div>
        </div>

        @if(count($packages) !== 0)

            <!-- Delete Modal -->
            @foreach ($packages as $package)
                <div class="modal fade" id="deleteModal{{ $package->id }}" tabindex="-1" aria-labelledby="deleteModal{{ $package->id }}Label" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-body text-center">
                                <p>Jepni konfirmimin nëse dëshironi të fshini këtë paketë?</p>
                                <div class="mt-3">
                                    <a href="{{ route('delete.package', $package->id) }}" class="btn btn-danger btn-sm fw-semibold"><i class="fa-solid fa-check"></i> Konfirmo</a>
                                    <button class="btn btn-success btn-sm fw-semibold" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> Anulo</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Comment Modal -->
            @foreach ($packages as $package)
                <div class="modal modal-lg fade" id="commentModal{{ $package->id }}" tabindex="-1" aria-labelledby="commentModalLabel{{ $package->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-scrollable">
                        <div class="modal-content rounded-4">
                            <div class="modal-header justify-content-between align-items-center px-4">
                                <h1 class="modal-title fs-5 fw-semibold" id="commentModalLabel{{ $package->id }}">Detajet e Paketës <strong>#PCK-{{ $package->id }}</strong></h1>
                                <button type="button" class="btn btn-darkblue btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
                            </div>
                            <div class="modal-body bg-custom px-4 py-5">
                                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> Paketa : </span><span>{{ $package->title }}</span></span>
                                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-tag"></i> Çmimi : </span>€{{ $package->price }}</span>
                                <span class="d-block mb-2"><span class="text-muted"><i class="fa-regular fa-calendar"></i> Kohëzgjatja (Muaj) : </span>{{ $package->months }}</span>
                                <span class="d-block mt-4 mb-3 text-muted"><i class="fa-regular fa-file-lines"></i> Përshkrimi</span>
                                <div class="card card-body rounded-4">
                                    <span>{!! nl2br(e($package->description)) !!}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    @endif
@endsection
