@extends('template.template')

{{-- Page Title --}}
@section('title', isset($editMaterialStock) ? 'Update Stock Transaction' : 'Materials Stock')

{{-- Page Content --}}
@section('content')
    @if(isset($editMaterialStock))
        <small class="d-block txt-lightblue fw-semibold text-uppercase"><a href="{{ route('materials.stock') }}" class="text-decoration-none txt-lightblue fw-semibold"><i class="fa-solid fa-circle-chevron-left"></i> Turn Back</a></small>
        <h3 class="fw-semibold pt-2">Update Stock Transaction</h3>
        <p class="text-muted">Update the details of the selected stock transaction.</p>
        <hr class="mb-4">
    @else
        <small class="d-block txt-lightblue fw-semibold text-uppercase">Materials</small>
        <h3 class="fw-semibold pt-2">Materials Stock</h3>
        <p class="text-muted">Monitor material stock levels and availability.</p>
        <hr class="mb-4">
    @endif

    @if(isset($editMaterialStock))

        {{-- Update Stock Transaction --}}
        <div class="card mb-4">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-layer-group txt-lightblue py-2"></i> Update Stock Transaction</span>
            </div>
            <div class="card-body">
                <form action="{{ route('update.material.stock') }}" method="post">
                    <input type="hidden" name="id" value="{{ $editMaterialStock->id }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="material_id" class="form-label">Material</label>
                            <select name="material_id" id="material_id" class="form-control shadow-sm rounded-3">
                                <option value="">-- Select Material --</option>
                                @foreach ($materials as $material)
                                    <option value="{{ $material->id }}" {{ old('material_id', $editMaterialStock->material_id) == $material->id ? 'selected' : '' }}>{{ $material->material }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="qty" class="form-label">Quantity</label>
                            <input type="text" name="qty" id="qty" value="{{ old('qty', $editMaterialStock->qty) }}" class="form-control shadow-sm rounded-3" placeholder="Enter Quantity here...">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="type" class="form-label">Type</label>
                            <select name="type" id="type" class="form-control shadow-sm rounded-3">
                                <option value="">-- Select Type --</option>
                                <option value="in" {{ old('type', $editMaterialStock->type) == 'in' ? 'selected' : '' }}>IN</option>
                                <option value="out" {{ old('type', $editMaterialStock->type) == 'out' ? 'selected' : '' }}>OUT</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-check"></i> Update Transaction</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @else

        {{-- Create Stock Transaction --}}
        <div class="card mb-4">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-layer-group txt-lightblue py-2"></i> Create New Stock Transaction</span>
            </div>
            <div class="card-body">
                <form action="{{ route('create.material.stock') }}" method="post">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="material_id" class="form-label">Material</label>
                            <select name="material_id" id="material_id" class="form-control shadow-sm rounded-3">
                                <option value="">-- Select Material --</option>
                                @foreach ($materials as $material)
                                    <option value="{{ $material->id }}" {{ old('material_id') == $material->id ? 'selected' : '' }}>{{ $material->material }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="qty" class="form-label">Quantity</label>
                            <input type="text" name="qty" id="qty" value="{{ old('qty') }}" class="form-control shadow-sm rounded-3" placeholder="Enter Quantity here...">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="type" class="form-label">Type</label>
                            <select name="type" id="type" class="form-control shadow-sm rounded-3">
                                <option value="">-- Select Type --</option>
                                <option value="in" {{ old('type') == 'in' ? 'selected' : '' }}>IN</option>
                                <option value="out" {{ old('type') == 'out' ? 'selected' : '' }}>OUT</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-plus"></i> Create Transaction</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Materials Stock List --}}
        <div class="card">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-list-ol txt-lightblue py-2"></i> Material Transactions List</span>
            </div>
            <div class="card-body">
                <form action="{{ route('materials.stock') }}" method="get" class="input-group mb-3">
                    <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-control shadow-sm rounded-start-3" placeholder="Search..."  aria-label="Search..." aria-describedby="basic-addon2">
                    <button type="submit" class="btn btn-darkblue shadow-sm rounded-end-3" id="basic-addon2"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th scope="col" class="text-nowrap">#</th>
                                <th scope="col" class="text-nowrap">Material</th>
                                <th scope="col" class="text-nowrap">Quantity (Units)</th>
                                <th scope="col" class="text-nowrap">Type</th>
                                <th scope="col" class="text-nowrap text-end">Operations</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($materialsStock) === 0)
                                <tr>
                                    <td colspan="5" class="text-center"><i class="fa-regular fa-face-frown"></i> No data found...</td>
                                </tr>
                            @else
                                @foreach ($materialsStock as $materialStock)
                                    <tr>
                                        <th scope="row" class="text-nowrap">{{ $materialStock->id }}</th>
                                        <td class="text-nowrap">{{ $materialStock->material }}</td>
                                        <td class="text-nowrap">{{ $materialStock->qty }} <small class="text-muted">({{ $materialStock->unit }})</small></td>
                                        <td class="text-nowrap text-uppercase fw-semibold">{{ $materialStock->type }}</td>
                                        <td class="text-nowrap text-end">
                                            <a href="{{ route('edit.material.stock', $materialStock->id) }}" class="btn btn-outline-success btn-sm rounded-3 shadow-sm"><i class="fa-solid fa-pen"></i></a>
                                            <button class="btn btn-outline-danger btn-sm rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $materialStock->id }}"><i class="fa-solid fa-trash"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                @if ($materialsStock->hasPages())
                    <div class="mt-3">
                        {{ $materialsStock->links('vendor.pagination.custom') }}
                    </div>
                @endif
            </div>
        </div>

        @if(count($materialsStock) !== 0)

            <!-- Delete Modal -->
            @foreach ($materialsStock as $materialStock)
                <div class="modal fade" id="deleteModal{{ $materialStock->id }}" tabindex="-1" aria-labelledby="deleteModal{{ $materialStock->id }}Label" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-body text-center">
                                <p>Confirm if you really want to delete this material transaction?</p>
                                <div class="mt-3">
                                    <a href="{{ route('delete.material.stock', $materialStock->id) }}" class="btn btn-danger btn-sm fw-semibold"><i class="fa-solid fa-check"></i> Confirm</a>
                                    <button class="btn btn-success btn-sm fw-semibold" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    @endif
@endsection
