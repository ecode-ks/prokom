@extends('template.template')

{{-- Page Title --}}
@section('title', isset($editMaterial) ? __('pages.materials_update_title') : __('pages.materials_title'))

{{-- Page Content --}}
@section('content')
    @if(isset($editMaterial))
        <small class="d-block txt-lightblue fw-semibold text-uppercase"><a href="{{ route('materials') }}" class="text-decoration-none txt-lightblue fw-semibold"><i class="fa-solid fa-circle-chevron-left"></i> {{ __('app.back') }}</a></small>
        <h3 class="fw-semibold pt-2">{{ __('pages.materials_update_title') }}</h3>
        <p class="text-muted">{{ __('pages.materials_update_lead') }}</p>
        <hr class="mb-4">
    @else
        <small class="d-block txt-lightblue fw-semibold text-uppercase">{{ __('pages.materials_title') }}</small>
        <h3 class="fw-semibold pt-2">{{ __('pages.materials_title') }}</h3>
        <p class="text-muted">{{ __('pages.materials_lead') }}</p>
        <hr class="mb-4">
    @endif

    @if(isset($editMaterial))

        {{-- Update Material --}}
        <div class="card mb-4">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-layer-group txt-lightblue py-2"></i> {{ __('pages.materials_update') }}</span>
            </div>
            <div class="card-body">
                <form action="{{ route('update.material') }}" method="post">
                    <input type="hidden" name="id" value="{{ $editMaterial->id }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="material" class="form-label">{{ __('pages.materials_label') }}</label>
                            <input type="text" name="material" id="material" value="{{ old('material', $editMaterial->material) }}" class="form-control shadow-sm rounded-3" placeholder="{{ __('pages.materials_placeholder') }}">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="unit" class="form-label">{{ __('app.unit') }}</label>
                            <input type="text" name="unit" id="unit" value="{{ old('unit', $editMaterial->unit) }}" class="form-control shadow-sm rounded-3" placeholder="{{ __('pages.materials_unit_placeholder') }}">
                            <div class="d-flex flex-wrap gap-2 mt-2" data-unit-target="unit">
                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-unit="copë">{{ __('pages.materials_unit_pcs') }}</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-unit="kg">kg</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-unit="m">m</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-unit="m²">m²</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-unit="litër">{{ __('pages.materials_unit_liter') }}</button>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-check"></i> {{ __('pages.materials_update_btn') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @else

        {{-- Create Material --}}
        <div class="card mb-4">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-layer-group txt-lightblue py-2"></i> {{ __('pages.materials_create') }}</span>
            </div>
            <div class="card-body">
                <form action="{{ route('create.material') }}" method="post">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="material" class="form-label">{{ __('pages.materials_label') }}</label>
                            <input type="text" name="material" id="material" value="{{ old('material') }}" class="form-control shadow-sm rounded-3" placeholder="{{ __('pages.materials_placeholder') }}">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="unit" class="form-label">{{ __('app.unit') }}</label>
                            <input type="text" name="unit" id="unit" value="{{ old('unit') }}" class="form-control shadow-sm rounded-3" placeholder="{{ __('pages.materials_unit_placeholder_short') }}">
                            <div class="d-flex flex-wrap gap-2 mt-2" data-unit-target="unit">
                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-unit="copë">{{ __('pages.materials_unit_pcs') }}</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-unit="kg">kg</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-unit="m">m</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-unit="m²">m²</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-unit="litër">{{ __('pages.materials_unit_liter') }}</button>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-plus"></i> {{ __('pages.materials_create_btn') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Materials List --}}
        <div class="card">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-list-ol txt-lightblue py-2"></i> {{ __('pages.materials_list') }}</span>
            </div>
            <div class="card-body">
                <form action="{{ route('materials') }}" method="get" class="input-group mb-3">
                    <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-control shadow-sm rounded-start-3" placeholder="{{ __('app.search') }}" aria-label="{{ __('app.search') }}" aria-describedby="basic-addon2">
                    <button type="submit" class="btn btn-darkblue shadow-sm rounded-end-3" id="basic-addon2"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th scope="col" class="text-nowrap">#</th>
                                <th scope="col" class="text-nowrap">{{ __('pages.materials_label') }}</th>
                                <th scope="col" class="text-nowrap">{{ __('app.unit') }}</th>
                                <th scope="col" class="text-nowrap text-end">{{ __('app.operations') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($materials) === 0)
                                <tr>
                                    <td colspan="4" class="text-center"><i class="fa-regular fa-face-frown"></i> {{ __('app.no_data') }}</td>
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
                                <p>{{ __('pages.materials_confirm_delete') }}</p>
                                <div class="mt-3">
                                    <a href="{{ route('delete.material', $material->id) }}" class="btn btn-danger btn-sm fw-semibold"><i class="fa-solid fa-check"></i> {{ __('app.confirm') }}</a>
                                    <button class="btn btn-success btn-sm fw-semibold" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> {{ __('app.cancel') }}</button>
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
    document.querySelectorAll('[data-unit-target]').forEach(function (group) {
        var input = document.getElementById(group.getAttribute('data-unit-target'));
        if (!input) return;

        group.querySelectorAll('[data-unit]').forEach(function (chip) {
            chip.addEventListener('click', function () {
                input.value = chip.getAttribute('data-unit') || '';
                group.querySelectorAll('[data-unit]').forEach(function (c) {
                    c.classList.remove('btn-darkblue');
                    c.classList.add('btn-outline-secondary');
                });
                chip.classList.remove('btn-outline-secondary');
                chip.classList.add('btn-darkblue');
                input.focus();
            });
        });
    });
</script>
@endpush
