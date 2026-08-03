@extends('template.template')

{{-- Page Title --}}
@section('title', isset($editMaterialStock) ? 'Përditëso Transaksionin e Stokut' : 'Stoku i Materialeve')

{{-- Page Content --}}
@section('content')
    @if(isset($editMaterialStock))
        <small class="d-block txt-lightblue fw-semibold text-uppercase"><a href="{{ route('materials.stock') }}" class="text-decoration-none txt-lightblue fw-semibold"><i class="fa-solid fa-circle-chevron-left"></i> Kthehu</a></small>
        <h3 class="fw-semibold pt-2">Përditëso Transaksionin e Stokut</h3>
        <p class="text-muted">Përditëso të dhënat e transaksionit të zgjedhur të stokut.</p>
        <hr class="mb-4">
    @else
        <small class="d-block txt-lightblue fw-semibold text-uppercase">Materialet</small>
        <h3 class="fw-semibold pt-2">Stoku i Materialeve</h3>
        <p class="text-muted">Monitoro nivelet e stokut dhe disponueshmërinë e materialeve.</p>
        <hr class="mb-4">
    @endif

    @if(isset($editMaterialStock))

        {{-- Update Stock Transaction --}}
        <div class="card mb-4">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-layer-group txt-lightblue py-2"></i> Përditëso Transaksionin e Stokut</span>
            </div>
            <div class="card-body">
                <form action="{{ route('update.material.stock') }}" method="post">
                    <input type="hidden" name="id" value="{{ $editMaterialStock->id }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="material_id" class="form-label">Materiali</label>
                            <select name="material_id" id="material_id" class="form-control shadow-sm rounded-3">
                                <option value="" disabled {{ old('material_id', $editMaterialStock->material_id) ? '' : 'selected' }}>-- Zgjidh Materialin --</option>
                                @foreach ($materials as $material)
                                    <option
                                        value="{{ $material->id }}"
                                        data-stock="{{ $material->current_stock }}"
                                        data-unit="{{ $material->unit }}"
                                        {{ old('material_id', $editMaterialStock->material_id) == $material->id ? 'selected' : '' }}
                                    >
                                        {{ $material->material }}
                                    </option>
                                @endforeach
                            </select>
                            <small id="current-stock-hint" class="text-muted d-block mt-1"></small>
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="qty" class="form-label">Sasia</label>
                            <input type="text" name="qty" id="qty" value="{{ old('qty', $editMaterialStock->qty) }}" class="form-control shadow-sm rounded-3" placeholder="Shkruaj sasinë...">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="type" class="form-label">Lloji</label>
                            <select name="type" id="type" class="form-control shadow-sm rounded-3">
                                <option value="" disabled {{ old('type', $editMaterialStock->type) ? '' : 'selected' }}>-- Zgjidh Llojin --</option>
                                <option value="in" {{ old('type', $editMaterialStock->type) == 'in' ? 'selected' : '' }}>HYRJE</option>
                                <option value="out" {{ old('type', $editMaterialStock->type) == 'out' ? 'selected' : '' }}>DALJE</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-check"></i> Përditëso Transaksionin</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @else

        {{-- Info Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-uppercase opacity-75"><i class="fa-solid fa-arrow-down text-success"></i> Hyrje</small>
                        <h4 class="fw-semibold mb-0">{{ number_format((float) ($stockStats->in_qty ?? 0), 2) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-uppercase opacity-75"><i class="fa-solid fa-arrow-up text-danger"></i> Dalje</small>
                        <h4 class="fw-semibold mb-0">{{ number_format((float) ($stockStats->out_qty ?? 0), 2) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-uppercase opacity-75"><i class="fa-solid fa-receipt text-primary"></i> Transaksione</small>
                        <h4 class="fw-semibold mb-0">{{ $stockStats->transaction_count ?? 0 }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-uppercase opacity-75"><i class="fa-solid fa-scale-balanced text-warning"></i> Bilanci neto</small>
                        <h4 class="fw-semibold mb-0">{{ number_format((float) ($stockStats->net_balance ?? 0), 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        {{-- Create Stock Transaction --}}
        <div class="card mb-4">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-layer-group txt-lightblue py-2"></i> Krijo Transaksion të Ri të Stokut</span>
            </div>
            <div class="card-body">
                <form action="{{ route('create.material.stock') }}" method="post">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="material_id" class="form-label">Materiali</label>
                            <select name="material_id" id="material_id" class="form-control shadow-sm rounded-3">
                                <option value="" disabled {{ old('material_id') ? '' : 'selected' }}>-- Zgjidh Materialin --</option>
                                @foreach ($materials as $material)
                                    <option
                                        value="{{ $material->id }}"
                                        data-stock="{{ $material->current_stock }}"
                                        data-unit="{{ $material->unit }}"
                                        {{ old('material_id') == $material->id ? 'selected' : '' }}
                                    >
                                        {{ $material->material }}
                                    </option>
                                @endforeach
                            </select>
                            <small id="current-stock-hint" class="text-muted d-block mt-1"></small>
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="qty" class="form-label">Sasia</label>
                            <input type="text" name="qty" id="qty" value="{{ old('qty') }}" class="form-control shadow-sm rounded-3" placeholder="Shkruaj sasinë...">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="type" class="form-label">Lloji</label>
                            <select name="type" id="type" class="form-control shadow-sm rounded-3">
                                <option value="" disabled {{ old('type') ? '' : 'selected' }}>-- Zgjidh Llojin --</option>
                                <option value="in" {{ old('type') == 'in' ? 'selected' : '' }}>HYRJE</option>
                                <option value="out" {{ old('type') == 'out' ? 'selected' : '' }}>DALJE</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-plus"></i> Krijo Transaksionin</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Materials Stock List --}}
        <div class="card">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-list-ol txt-lightblue py-2"></i> Lista e Transaksioneve të Materialeve</span>
            </div>
            <div class="card-body">
                <form action="{{ route('materials.stock') }}" method="get" class="input-group mb-3">
                    <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-control shadow-sm rounded-start-3" placeholder="Kërko..." aria-label="Kërko..." aria-describedby="basic-addon2">
                    <button type="submit" class="btn btn-darkblue shadow-sm rounded-end-3" id="basic-addon2"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th scope="col" class="text-nowrap">#</th>
                                <th scope="col" class="text-nowrap">Materiali</th>
                                <th scope="col" class="text-nowrap">Sasia (Njësitë)</th>
                                <th scope="col" class="text-nowrap">Lloji</th>
                                <th scope="col" class="text-nowrap text-end">Operacionet</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($materialsStock) === 0)
                                <tr>
                                    <td colspan="5" class="text-center"><i class="fa-regular fa-face-frown"></i> Nuk u gjetën të dhëna...</td>
                                </tr>
                            @else
                                @foreach ($materialsStock as $materialStock)
                                    <tr>
                                        <th scope="row" class="text-nowrap">{{ $materialStock->id }}</th>
                                        <td class="text-nowrap">{{ $materialStock->material }}</td>
                                        <td class="text-nowrap">{{ $materialStock->qty }} <small class="text-muted">({{ $materialStock->unit }})</small></td>
                                        <td class="text-nowrap text-uppercase fw-semibold">{{ $materialStock->type == 'in' ? 'HYRJE' : ($materialStock->type == 'out' ? 'DALJE' : $materialStock->type) }}</td>
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
                                <p>A jeni i sigurt që dëshironi ta fshini këtë transaksion të materialit?</p>
                                <div class="mt-3">
                                    <a href="{{ route('delete.material.stock', $materialStock->id) }}" class="btn btn-danger btn-sm fw-semibold"><i class="fa-solid fa-check"></i> Konfirmo</a>
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

@push('scripts')
<script>
    (function () {
        const select = document.getElementById('material_id');
        const hint = document.getElementById('current-stock-hint');
        if (!select || !hint) return;

        const updateHint = () => {
            const option = select.options[select.selectedIndex];
            if (!option || !option.value) {
                hint.textContent = '';
                return;
            }
            const stock = option.getAttribute('data-stock') || '0';
            const unit = option.getAttribute('data-unit') || '';
            hint.innerHTML = '<i class="fa-solid fa-boxes-stacked"></i> Stoku aktual: <strong>' + stock + '</strong> ' + unit;
        };

        select.addEventListener('change', updateHint);
        updateHint();
    })();
</script>
@endpush
