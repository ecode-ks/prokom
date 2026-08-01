@extends('template.template')

{{-- Page Title --}}
@section('title', isset($editSupplier) ? 'Përditëso Furnizuesin' : 'Furnizuesit')

{{-- Page Content --}}
@section('content')
    @if(isset($editSupplier))
        <small class="d-block txt-lightblue fw-semibold text-uppercase"><a href="{{ route('suppliers') }}" class="text-decoration-none txt-lightblue fw-semibold"><i class="fa-solid fa-circle-chevron-left"></i> Kthehu</a></small>
        <h3 class="fw-semibold pt-2">Përditëso Furnizuesin</h3>
        <p class="text-muted">Përditëso të dhënat e furnizuesit të zgjedhur.</p>
        <hr class="mb-4">
    @else
        <small class="d-block txt-lightblue fw-semibold text-uppercase">Furnizuesit</small>
        <h3 class="fw-semibold pt-2">Furnizuesit</h3>
        <p class="text-muted">Shiko, organizo dhe menaxho të dhënat e furnizuesve në një vend.</p>
        <hr class="mb-4">
    @endif

    @if(isset($editSupplier))

        {{-- Update Supplier --}}
        <div class="card mb-4">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-truck txt-lightblue py-2"></i> Përditëso Furnizuesin</span>
            </div>
            <div class="card-body">
                <form action="{{ route('update.supplier') }}" method="post">
                    <input type="hidden" name="id" value="{{ $editSupplier->id }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="supplier" class="form-label">Furnizuesi</label>
                            <input type="text" name="supplier" id="supplier" value="{{ old('supplier', $editSupplier->supplier) }}" class="form-control shadow-sm rounded-3" placeholder="Shkruaj furnizuesin...">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="phone" class="form-label">Telefoni</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone', $editSupplier->phone) }}" class="form-control shadow-sm rounded-3" placeholder="Shkruaj telefonin e furnizuesit...">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="location" class="form-label">Lokacioni</label>
                            <input type="text" name="location" id="location" value="{{ old('location', $editSupplier->location) }}" class="form-control shadow-sm rounded-3" placeholder="Shkruaj lokacionin e furnizuesit...">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-check"></i> Përditëso Furnizuesin</button>
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
                        <small class="text-uppercase opacity-75"><i class="fa-solid fa-truck text-primary"></i> Total</small>
                        <h4 class="fw-semibold mb-0">{{ $supplierStats->total_count ?? 0 }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-uppercase opacity-75"><i class="fa-solid fa-location-dot text-warning"></i> Lokacione unike</small>
                        <h4 class="fw-semibold mb-0">{{ $supplierStats->locations_count ?? 0 }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-uppercase opacity-75"><i class="fa-solid fa-phone text-success"></i> Me telefon</small>
                        <h4 class="fw-semibold mb-0">{{ $supplierStats->with_phone_count ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>

        {{-- Create Supplier --}}
        <div class="card mb-4">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-truck txt-lightblue py-2"></i> Krijo Furnizues të Ri</span>
            </div>
            <div class="card-body">
                <form action="{{ route('create.supplier') }}" method="post">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="supplier" class="form-label">Furnizuesi</label>
                            <input type="text" name="supplier" id="supplier" value="{{ old('supplier') }}" class="form-control shadow-sm rounded-3" placeholder="Shkruaj furnizuesin...">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="phone" class="form-label">Telefoni</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="form-control shadow-sm rounded-3" placeholder="Shkruaj telefonin e furnizuesit...">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="location" class="form-label">Lokacioni</label>
                            <input type="text" name="location" id="location" value="{{ old('location') }}" class="form-control shadow-sm rounded-3" placeholder="Shkruaj lokacionin e furnizuesit...">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-plus"></i> Krijo Furnizuesin</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Suppliers List --}}
        <div class="card">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-list-ol txt-lightblue py-2"></i> Lista e Furnizuesve</span>
            </div>
            <div class="card-body">
                <form action="{{ route('suppliers') }}" method="get" class="input-group mb-3">
                    <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-control shadow-sm rounded-start-3" placeholder="Kërko..." aria-label="Kërko..." aria-describedby="basic-addon2">
                    <button type="submit" class="btn btn-darkblue shadow-sm rounded-end-3" id="basic-addon2"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th scope="col" class="text-nowrap">#</th>
                                <th scope="col" class="text-nowrap">Furnizuesi</th>
                                <th scope="col" class="text-nowrap">Telefoni</th>
                                <th scope="col" class="text-nowrap">Lokacioni</th>
                                <th scope="col" class="text-nowrap text-end">Operacionet</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($suppliers) === 0)
                                <tr>
                                    <td colspan="5" class="text-center"><i class="fa-regular fa-face-frown"></i> Nuk u gjetën të dhëna...</td>
                                </tr>
                            @else
                                @foreach ($suppliers as $supplier)
                                    <tr>
                                        <th scope="row" class="text-nowrap">{{ $supplier->id }}</th>
                                        <td class="text-nowrap">{{ $supplier->supplier }}</td>
                                        <td class="text-nowrap">{{ $supplier->phone }}</td>
                                        <td class="text-nowrap">{{ $supplier->location }}</td>
                                        <td class="text-nowrap text-end">
                                            <a href="{{ route('edit.supplier', $supplier->id) }}" class="btn btn-outline-success btn-sm rounded-3 shadow-sm"><i class="fa-solid fa-pen"></i></a>
                                            <button class="btn btn-outline-danger btn-sm rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $supplier->id }}"><i class="fa-solid fa-trash"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                @if ($suppliers->hasPages())
                    <div class="mt-3">
                        {{ $suppliers->links('vendor.pagination.custom') }}
                    </div>
                @endif
            </div>
        </div>

        @if(count($suppliers) !== 0)

            <!-- Delete Modal -->
            @foreach ($suppliers as $supplier)
                <div class="modal fade" id="deleteModal{{ $supplier->id }}" tabindex="-1" aria-labelledby="deleteModal{{ $supplier->id }}Label" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-body text-center">
                                <p>A jeni i sigurt që dëshironi ta fshini këtë furnizues?</p>
                                <div class="mt-3">
                                    <a href="{{ route('delete.supplier', $supplier->id) }}" class="btn btn-danger btn-sm fw-semibold"><i class="fa-solid fa-check"></i> Konfirmo</a>
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
