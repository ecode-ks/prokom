@extends('template.template')

{{-- Page Title --}}
@section('title', isset($editClient) ? __('pages.clients_update_title') : __('pages.clients_title'))

{{-- Page Content --}}
@section('content')
    @if(isset($editClient))
        <small class="d-block txt-lightblue fw-semibold text-uppercase"><a href="{{ route('clients') }}" class="text-decoration-none txt-lightblue fw-semibold"><i class="fa-solid fa-circle-chevron-left"></i> {{ __('pages.back') }}</a></small>
        <h3 class="fw-semibold pt-2">{{ __('pages.clients_update_title') }}</h3>
        <p class="text-muted">{{ __('pages.clients_update_lead') }}</p>
        <hr class="mb-4">
    @else
        <small class="d-block txt-lightblue fw-semibold text-uppercase">{{ __('pages.clients_title') }}</small>
        <h3 class="fw-semibold pt-2">{{ __('pages.clients_title') }}</h3>
        <p class="text-muted">{{ __('pages.clients_lead') }}</p>
        <hr class="mb-4">
    @endif

    @if(isset($editClient))

        {{-- Update Client --}}
        <div class="card mb-4">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-user-tie txt-lightblue py-2"></i> {{ __('pages.clients_update') }}</span>
            </div>
            <div class="card-body">
                <form action="{{ route('update.client') }}" method="post">
                    <input type="hidden" name="id" value="{{ $editClient->id }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="client" class="form-label">{{ __('pages.clients_label') }}</label>
                            <input type="text" name="client" id="client" value="{{ old('client', $editClient->client) }}" class="form-control shadow-sm rounded-3" placeholder="{{ __('pages.clients_placeholder') }}">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="phone" class="form-label">{{ __('pages.phone') }}</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone', $editClient->phone) }}" class="form-control shadow-sm rounded-3" placeholder="{{ __('pages.clients_phone_placeholder') }}">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="location" class="form-label">{{ __('pages.location') }}</label>
                            <input type="text" name="location" id="location" value="{{ old('location', $editClient->location) }}" class="form-control shadow-sm rounded-3" placeholder="{{ __('pages.clients_location_placeholder') }}">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-check"></i> {{ __('pages.clients_update_btn') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @else

        {{-- Create Client --}}
        <div class="card mb-4">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-user-tie txt-lightblue py-2"></i> {{ __('pages.clients_create') }}</span>
            </div>
            <div class="card-body">
                <form action="{{ route('create.client') }}" method="post">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="client" class="form-label">{{ __('pages.clients_label') }}</label>
                            <input type="text" name="client" id="client" value="{{ old('client') }}" class="form-control shadow-sm rounded-3" placeholder="{{ __('pages.clients_placeholder') }}">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="phone" class="form-label">{{ __('pages.phone') }}</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="form-control shadow-sm rounded-3" placeholder="{{ __('pages.clients_phone_placeholder') }}">
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="location" class="form-label">{{ __('pages.location') }}</label>
                            <input type="text" name="location" id="location" value="{{ old('location') }}" class="form-control shadow-sm rounded-3" placeholder="{{ __('pages.clients_location_placeholder') }}">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-plus"></i> {{ __('pages.clients_create_btn') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Clients List --}}
        <div class="card">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-list-ol txt-lightblue py-2"></i> {{ __('pages.clients_list') }}</span>
            </div>
            <div class="card-body">
                <form action="{{ route('clients') }}" method="get" class="input-group mb-3">
                    <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-control shadow-sm rounded-start-3" placeholder="{{ __('pages.search') }}" aria-label="{{ __('pages.search') }}" aria-describedby="basic-addon2">
                    <button type="submit" class="btn btn-darkblue shadow-sm rounded-end-3" id="basic-addon2"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th scope="col" class="text-nowrap">#</th>
                                <th scope="col" class="text-nowrap">{{ __('pages.clients_label') }}</th>
                                <th scope="col" class="text-nowrap">{{ __('pages.phone') }}</th>
                                <th scope="col" class="text-nowrap">{{ __('pages.location') }}</th>
                                <th scope="col" class="text-nowrap text-end">{{ __('pages.operations') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($clients) === 0)
                                <tr>
                                    <td colspan="5" class="text-center"><i class="fa-regular fa-face-frown"></i> {{ __('pages.no_data') }}</td>
                                </tr>
                            @else
                                @foreach ($clients as $client)
                                    <tr>
                                        <th scope="row" class="text-nowrap">{{ $client->id }}</th>
                                        <td class="text-nowrap">{{ $client->client }}</td>
                                        <td class="text-nowrap">{{ $client->phone }}</td>
                                        <td class="text-nowrap">{{ $client->location }}</td>
                                        <td class="text-nowrap text-end">
                                            <a href="{{ route('edit.client', $client->id) }}" class="btn btn-outline-success btn-sm rounded-3 shadow-sm"><i class="fa-solid fa-pen"></i></a>
                                            <button class="btn btn-outline-danger btn-sm rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $client->id }}"><i class="fa-solid fa-trash"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                @if ($clients->hasPages())
                    <div class="mt-3">
                        {{ $clients->links('vendor.pagination.custom') }}
                    </div>
                @endif
            </div>
        </div>

        @if(count($clients) !== 0)

            <!-- Delete Modal -->
            @foreach ($clients as $client)
                <div class="modal fade" id="deleteModal{{ $client->id }}" tabindex="-1" aria-labelledby="deleteModal{{ $client->id }}Label" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-body text-center">
                                <p>{{ __('pages.clients_confirm_delete') }}</p>
                                <div class="mt-3">
                                    <a href="{{ route('delete.client', $client->id) }}" class="btn btn-danger btn-sm fw-semibold"><i class="fa-solid fa-check"></i> {{ __('pages.confirm') }}</a>
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
