@extends('template.template')

{{-- Page Title --}}
@section('title', isset($editMaterial) ? 'Update Material' : 'Materials')

{{-- Page Content --}}
@section('content')
    @if(isset($editMaterial))
        <small class="d-block txt-lightblue fw-semibold text-uppercase"><a href="{{ route('materials') }}" class="text-decoration-none txt-lightblue fw-semibold"><i class="fa-solid fa-circle-chevron-left"></i> Turn Back</a></small>
        <h3 class="fw-semibold pt-2">Update Material</h3>
        <p class="text-muted">Update the details of the selected material.</p>
        <hr class="mb-4">
    @else
        <small class="d-block txt-lightblue fw-semibold text-uppercase">Materials</small>
        <h3 class="fw-semibold pt-2">Materials</h3>
        <p class="text-muted">View, create, and manage material records in one place.</p>
        <hr class="mb-4">
    @endif

    @if(isset($editMaterial))

        {{-- Update Material --}}
        <div class="card mb-4">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-layer-group txt-lightblue py-2"></i> Update Material</span>
            </div>
            <div class="card-body">
                <form action="{{ route('update.material') }}" method="post">
                    <input type="hidden" name="id" value="{{ $editMaterial->id }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="material" class="form-label">Material</label>
                            <input type="text" name="material" id="material" value="{{ old('material', $editMaterial->material) }}" class="form-control shadow-sm rounded-3" placeholder="Enter material here...">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="unit" class="form-label">Unit</label>
                            <input type="text" name="unit" id="unit" value="{{ old('unit', $editMaterial->unit) }}" class="form-control shadow-sm rounded-3" placeholder="Enter material unit here...">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-check"></i> Update Material</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @else

        {{-- Create Material --}}
        <div class="card mb-4">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-layer-group txt-lightblue py-2"></i> Create New Material</span>
            </div>
            <div class="card-body">
                <form action="{{ route('create.material') }}" method="post">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="material" class="form-label">Material</label>
                            <input type="text" name="material" id="material" value="{{ old('material') }}" class="form-control shadow-sm rounded-3" placeholder="Enter material here...">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="unit" class="form-label">Unit</label>
                            <input type="text" name="unit" id="unit" value="{{ old('unit') }}" class="form-control shadow-sm rounded-3" placeholder="Enter unit here...">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-plus"></i> Create Material</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Materials List --}}
        <div class="card">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-list-ol txt-lightblue py-2"></i> Materials List</span>
            </div>
            <div class="card-body">
                <form action="{{ route('materials') }}" method="get" class="input-group mb-3">
                    <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-control shadow-sm rounded-start-3" placeholder="Search..." aria-label="Search..." aria-describedby="basic-addon2">
                    <button type="submit" class="btn btn-darkblue shadow-sm rounded-end-3" id="basic-addon2"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th scope="col" class="text-nowrap">#</th>
                                <th scope="col" class="text-nowrap">Material</th>
                                <th scope="col" class="text-nowrap">Unit</th>
                                <th scope="col" class="text-nowrap text-end">Operations</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($materials) === 0)
                                <tr>
                                    <td colspan="4" class="text-center"><i class="fa-regular fa-face-frown"></i> No data found...</td>
                                </tr>
                            @else
                                @foreach ($materials as $material)
                                    <tr>
                                        <th scope="row" class="text-nowrap">{{ $material->id }}</th>
                                        <td class="text-nowrap">{{ $material->material }}</td>
                                        <td class="text-nowrap">{{ $material->unit }}</td>
                                        <td class="text-nowrap text-end">
                                            <a href="{{ route('edit.material', $material->id) }}" class="btn btn-outline-success btn-sm rounded-3 shadow-sm"><i class="fa-solid fa-pen"></i></a>
                                            <button class="btn btn-outline-danger btn-sm rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $material->id }}"><i class="fa-solid fa-trash"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                @if ($materials->hasPages())
                    <div class="mt-3">
                        {{ $materials->links('vendor.pagination.custom') }}
                    </div>
                @endif
            </div>
        </div>

        @if(count($materials) !== 0)

            <!-- Delete Modal -->
            @foreach ($materials as $material)
                <div class="modal fade" id="deleteModal{{ $material->id }}" tabindex="-1" aria-labelledby="deleteModal{{ $material->id }}Label" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-body text-center">
                                <p>Confirm if you really want to delete this material?</p>
                                <div class="mt-3">
                                    <a href="{{ route('delete.material', $material->id) }}" class="btn btn-danger btn-sm fw-semibold"><i class="fa-solid fa-check"></i> Confirm</a>
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
