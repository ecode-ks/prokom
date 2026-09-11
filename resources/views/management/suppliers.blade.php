@extends('template.template')

{{-- Page Title --}}
@section('title', isset($editSupplier) ? __('pages.suppliers_update_title') : __('pages.suppliers_title'))

{{-- Page Content --}}
@section('content')
    @if(isset($editSupplier))
        <small class="d-block txt-lightblue fw-semibold text-uppercase"><a href="{{ route('suppliers') }}" class="text-decoration-none txt-lightblue fw-semibold"><i class="fa-solid fa-circle-chevron-left"></i> {{ __('pages.back') }}</a></small>
        <h3 class="fw-semibold pt-2">{{ __('pages.suppliers_update_title') }}</h3>
        <p class="text-muted">{{ __('pages.suppliers_update_lead') }}</p>
        <hr class="mb-4">
    @else
        <small class="d-block txt-lightblue fw-semibold text-uppercase">{{ __('pages.suppliers_title') }}</small>
        <h3 class="fw-semibold pt-2">{{ __('pages.suppliers_title') }}</h3>
        <p class="text-muted">{{ __('pages.suppliers_lead') }}</p>
        <hr class="mb-4">
    @endif

    @if(isset($editSupplier))

        {{-- Update Supplier --}}
        <div class="card mb-4">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-truck txt-lightblue py-2"></i> {{ __('pages.suppliers_update') }}</span>
            </div>
            <div class="card-body">
                <form action="{{ route('update.supplier') }}" method="post">
                    <input type="hidden" name="id" value="{{ $editSupplier->id }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="supplier" class="form-label">{{ __('pages.suppliers_label') }}</label>
                            <input type="text" name="supplier" id="supplier" value="{{ old('supplier', $editSupplier->supplier) }}" class="form-control shadow-sm rounded-3" placeholder="{{ __('pages.suppliers_placeholder') }}">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="phone" class="form-label">{{ __('pages.phone') }}</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone', $editSupplier->phone) }}" class="form-control shadow-sm rounded-3" placeholder="{{ __('pages.suppliers_phone_placeholder') }}">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="location" class="form-label">{{ __('pages.location') }}</label>
                            <input type="text" name="location" id="location" value="{{ old('location', $editSupplier->location) }}" class="form-control shadow-sm rounded-3" placeholder="{{ __('pages.suppliers_location_placeholder') }}">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-check"></i> {{ __('pages.suppliers_update_btn') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @else

        {{-- Create Supplier --}}
        <div class="card mb-4">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-truck txt-lightblue py-2"></i> {{ __('pages.suppliers_create') }}</span>
            </div>
            <div class="card-body">
                <form action="{{ route('create.supplier') }}" method="post">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="supplier" class="form-label">{{ __('pages.suppliers_label') }}</label>
                            <input type="text" name="supplier" id="supplier" value="{{ old('supplier') }}" class="form-control shadow-sm rounded-3" placeholder="{{ __('pages.suppliers_placeholder') }}">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="phone" class="form-label">{{ __('pages.phone') }}</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="form-control shadow-sm rounded-3" placeholder="{{ __('pages.suppliers_phone_placeholder') }}">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="location" class="form-label">{{ __('pages.location') }}</label>
                            <input type="text" name="location" id="location" value="{{ old('location') }}" class="form-control shadow-sm rounded-3" placeholder="{{ __('pages.suppliers_location_placeholder') }}">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-plus"></i> {{ __('pages.suppliers_create_btn') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Suppliers List --}}
        <div class="card">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-list-ol txt-lightblue py-2"></i> {{ __('pages.suppliers_list') }}</span>
            </div>
            <div class="card-body">
                <form action="{{ route('suppliers') }}" method="get" class="input-group mb-3">
                    <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-control shadow-sm rounded-start-3" placeholder="{{ __('pages.search') }}" aria-label="{{ __('pages.search') }}" aria-describedby="basic-addon2">
                    <button type="submit" class="btn btn-darkblue shadow-sm rounded-end-3" id="basic-addon2"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th scope="col" class="text-nowrap">#</th>
                                <th scope="col" class="text-nowrap">{{ __('pages.suppliers_label') }}</th>
                                <th scope="col" class="text-nowrap">{{ __('pages.phone') }}</th>
                                <th scope="col" class="text-nowrap">{{ __('pages.location') }}</th>
                                <th scope="col" class="text-nowrap text-end">{{ __('pages.operations') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($suppliers) === 0)
                                <tr>
                                    <td colspan="5" class="text-center"><i class="fa-regular fa-face-frown"></i> {{ __('pages.no_data') }}</td>
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
                                <p>{{ __('pages.suppliers_confirm_delete') }}</p>
                                <div class="mt-3">
                                    <a href="{{ route('delete.supplier', $supplier->id) }}" class="btn btn-danger btn-sm fw-semibold"><i class="fa-solid fa-check"></i> {{ __('pages.confirm') }}</a>
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
