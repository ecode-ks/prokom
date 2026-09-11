@extends('template.template')

{{-- Page Title --}}
@section('title', isset($editMaterialStock) ? __('pages.materials_stock_update_title') : __('pages.materials_stock_title'))

{{-- Page Content --}}
@section('content')
    @if(isset($editMaterialStock))
        <small class="d-block txt-lightblue fw-semibold text-uppercase"><a href="{{ route('materials.stock') }}" class="text-decoration-none txt-lightblue fw-semibold"><i class="fa-solid fa-circle-chevron-left"></i> {{ __('pages.back') }}</a></small>
        <h3 class="fw-semibold pt-2">{{ __('pages.materials_stock_update_title') }}</h3>
        <p class="text-muted">{{ __('pages.materials_stock_update_lead') }}</p>
        <hr class="mb-4">
    @else
        <small class="d-block txt-lightblue fw-semibold text-uppercase">{{ __('pages.materials_title') }}</small>
        <h3 class="fw-semibold pt-2">{{ __('pages.materials_stock_title') }}</h3>
        <p class="text-muted">{{ __('pages.materials_stock_lead') }}</p>
        <hr class="mb-4">
    @endif

    @if(isset($editMaterialStock))

        {{-- Update Stock Transaction --}}
        <div class="card mb-4">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-layer-group txt-lightblue py-2"></i> {{ __('pages.materials_stock_update') }}</span>
            </div>
            <div class="card-body">
                <form action="{{ route('update.material.stock') }}" method="post">
                    <input type="hidden" name="id" value="{{ $editMaterialStock->id }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="material_id" class="form-label">{{ __('pages.materials_label') }}</label>
                            <select name="material_id" id="material_id" class="form-control shadow-sm rounded-3">
                                <option value="" disabled {{ old('material_id', $editMaterialStock->material_id) ? '' : 'selected' }}>{{ __('pages.materials_stock_select_material') }}</option>
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
                            <small id="current-stock-hint" class="text-muted d-block mt-1" data-current-stock-label="{{ __('pages.materials_stock_current') }}"></small>
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="qty" class="form-label">{{ __('pages.qty') }}</label>
                            <input type="text" name="qty" id="qty" value="{{ old('qty', $editMaterialStock->qty) }}" class="form-control shadow-sm rounded-3" placeholder="{{ __('pages.materials_stock_qty_placeholder') }}">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="type" class="form-label">{{ __('pages.type') }}</label>
                            <select name="type" id="type" class="form-control shadow-sm rounded-3">
                                <option value="" disabled {{ old('type', $editMaterialStock->type) ? '' : 'selected' }}>{{ __('pages.choose_type') }}</option>
                                <option value="in" {{ old('type', $editMaterialStock->type) == 'in' ? 'selected' : '' }}>{{ __('pages.materials_stock_type_in') }}</option>
                                <option value="out" {{ old('type', $editMaterialStock->type) == 'out' ? 'selected' : '' }}>{{ __('pages.materials_stock_type_out') }}</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-check"></i> {{ __('pages.materials_stock_update_btn') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @else

        {{-- Create Stock Transaction --}}
        <div class="card mb-4">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-layer-group txt-lightblue py-2"></i> {{ __('pages.materials_stock_create') }}</span>
            </div>
            <div class="card-body">
                <form action="{{ route('create.material.stock') }}" method="post">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="material_id" class="form-label">{{ __('pages.materials_label') }}</label>
                            <select name="material_id" id="material_id" class="form-control shadow-sm rounded-3">
                                <option value="" disabled {{ old('material_id') ? '' : 'selected' }}>{{ __('pages.materials_stock_select_material') }}</option>
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
                            <small id="current-stock-hint" class="text-muted d-block mt-1" data-current-stock-label="{{ __('pages.materials_stock_current') }}"></small>
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="qty" class="form-label">{{ __('pages.qty') }}</label>
                            <input type="text" name="qty" id="qty" value="{{ old('qty') }}" class="form-control shadow-sm rounded-3" placeholder="{{ __('pages.materials_stock_qty_placeholder') }}">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="type" class="form-label">{{ __('pages.type') }}</label>
                            <select name="type" id="type" class="form-control shadow-sm rounded-3">
                                <option value="" disabled {{ old('type') ? '' : 'selected' }}>{{ __('pages.choose_type') }}</option>
                                <option value="in" {{ old('type') == 'in' ? 'selected' : '' }}>{{ __('pages.materials_stock_type_in') }}</option>
                                <option value="out" {{ old('type') == 'out' ? 'selected' : '' }}>{{ __('pages.materials_stock_type_out') }}</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-plus"></i> {{ __('pages.materials_stock_create_btn') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Materials Stock List --}}
        <div class="card">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-list-ol txt-lightblue py-2"></i> {{ __('pages.materials_stock_list') }}</span>
            </div>
            <div class="card-body">
                <form action="{{ route('materials.stock') }}" method="get" class="input-group mb-3">
                    <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-control shadow-sm rounded-start-3" placeholder="{{ __('pages.search') }}" aria-label="{{ __('pages.search') }}" aria-describedby="basic-addon2">
                    <button type="submit" class="btn btn-darkblue shadow-sm rounded-end-3" id="basic-addon2"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th scope="col" class="text-nowrap">#</th>
                                <th scope="col" class="text-nowrap">{{ __('pages.materials_label') }}</th>
                                <th scope="col" class="text-nowrap">{{ __('pages.materials_stock_qty_units') }}</th>
                                <th scope="col" class="text-nowrap">{{ __('pages.type') }}</th>
                                <th scope="col" class="text-nowrap text-end">{{ __('pages.operations') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($materialsStock) === 0)
                                <tr>
                                    <td colspan="5" class="text-center"><i class="fa-regular fa-face-frown"></i> {{ __('pages.no_data') }}</td>
                                </tr>
                            @else
                                @foreach ($materialsStock as $materialStock)
                                    <tr>
                                        <th scope="row" class="text-nowrap">{{ $materialStock->id }}</th>
                                        <td class="text-nowrap">{{ $materialStock->material }}</td>
                                        <td class="text-nowrap">{{ $materialStock->qty }} <small class="text-muted">({{ $materialStock->unit }})</small></td>
                                        <td class="text-nowrap text-uppercase fw-semibold">{{ $materialStock->type == 'in' ? __('pages.materials_stock_type_in') : ($materialStock->type == 'out' ? __('pages.materials_stock_type_out') : $materialStock->type) }}</td>
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
                                <p>{{ __('pages.materials_stock_confirm_delete') }}</p>
                                <div class="mt-3">
                                    <a href="{{ route('delete.material.stock', $materialStock->id) }}" class="btn btn-danger btn-sm fw-semibold"><i class="fa-solid fa-check"></i> {{ __('pages.confirm') }}</a>
                                    <button class="btn btn-success btn-sm fw-semibold" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> {{ __('pages.cancel') }}</button>
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
            const label = hint.getAttribute('data-current-stock-label') || '';
            hint.innerHTML = '<i class="fa-solid fa-boxes-stacked"></i> ' + label + ' <strong>' + stock + '</strong> ' + unit;
        };

        select.addEventListener('change', updateHint);
        updateHint();
    })();
</script>
@endpush
