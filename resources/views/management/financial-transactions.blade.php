@extends('template.template')

{{-- Page Title --}}
@section('title', isset($editTransaction) ? __('pages.fin_update_title') : __('app.nav_revenue'))

{{-- Page Content --}}
@section('content')
    @if(isset($editTransaction))
        <small class="d-block txt-lightblue fw-semibold text-uppercase"><a href="{{ route('financial.transactions') }}" class="text-decoration-none txt-lightblue fw-semibold"><i class="fa-solid fa-circle-chevron-left"></i> {{ __('app.back') }}</a></small>
        <h3 class="fw-semibold pt-2">{{ __('pages.fin_update_title') }}</h3>
        <p class="text-muted">{{ __('pages.fin_update_lead') }}</p>
        <hr class="mb-4">
    @else
        <small class="d-block txt-lightblue fw-semibold text-uppercase">{{ __('app.nav_finance') }}</small>
        <h3 class="fw-semibold pt-2">{{ __('app.nav_revenue') }}</h3>
        <p class="text-muted">{{ __('pages.fin_lead') }}</p>
        <hr class="mb-4">
    @endif

    @if(isset($editTransaction))

        {{-- Update Transaction --}}
        <div class="card mb-4">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-money-bill-transfer txt-lightblue py-2"></i> {{ __('pages.fin_update_title') }}</span>
            </div>
            <div class="card-body">
                <form action="{{ route('update.transaction') }}" method="post">
                    <input type="hidden" name="id" value="{{ $editTransaction->id }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="type" class="form-label">{{ __('pages.fin_type') }}</label>
                            <select name="type" id="type" class="form-control shadow-sm rounded-3">
                                <option value="" disabled {{ old('type', $editTransaction->type) ? '' : 'selected' }}>{{ __('pages.fin_choose_type') }}</option>
                                <option value="revenue" {{ old('type', $editTransaction->type) == 'revenue' ? 'selected' : '' }}>{{ __('pages.fin_revenue') }}</option>
                                <option value="expense" {{ old('type', $editTransaction->type) == 'expense' ? 'selected' : '' }}>{{ __('pages.fin_expense') }}</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="amount" class="form-label">{{ __('pages.fin_amount_label') }}</label>
                            <input type="text" name="amount" id="amount" value="{{ old('amount', $editTransaction->amount) }}" class="form-control shadow-sm rounded-3" placeholder="{{ __('pages.fin_amount_ph') }}">
                        </div>
                        <div class="col-12">
                            <label for="comment" class="form-label">{{ __('pages.fin_comment_label') }}</label>
                            <textarea name="comment" id="comment" class="form-control shadow-sm rounded-3" placeholder="{{ __('pages.fin_comment_ph') }}" rows="3">{{ old('comment', $editTransaction->comment) }}</textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-check"></i> {{ __('pages.fin_update_btn') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @else

        {{-- Info Cards --}}
        @php
            $totalRevenues = $revenues ?? 0;
            $totalExpenses = $expenses ?? 0;
        @endphp

        <div class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-uppercase opacity-75"><i class="fa-solid fa-arrow-trend-up text-success"></i> {{ __('pages.fin_revenues') }}</small>
                        <h4 class="fw-semibold mb-0 text-success">€{{ number_format($totalRevenues,2) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-uppercase opacity-75"><i class="fa-solid fa-arrow-trend-down text-danger"></i> {{ __('pages.fin_expenses') }}</small>
                        <h4 class="fw-semibold mb-0 text-danger">-€{{ number_format($totalExpenses,2) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card h-100">
                    <div class="card-body bg-darkblue text-white rounded-4">
                        <small class="text-uppercase opacity-75">{{ __('pages.fin_balance') }}</small>
                        <h4 class="fw-semibold mb-0">€{{ number_format($totalRevenues-$totalExpenses,2) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        {{-- Create Transaction --}}
        <div class="card mb-4">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-money-bill-transfer txt-lightblue py-2"></i> {{ __('pages.fin_create_title') }}</span>
            </div>
            <div class="card-body">
                <form action="{{ route('create.transaction') }}" method="post">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="type" class="form-label">{{ __('pages.fin_type') }}</label>
                            <select name="type" id="type" class="form-control shadow-sm rounded-3">
                                <option value="" disabled {{ old('type') ? '' : 'selected' }}>{{ __('pages.fin_choose_type') }}</option>
                                <option value="revenue" {{ old('type') == 'revenue' ? 'selected' : '' }}>{{ __('pages.fin_revenue') }}</option>
                                <option value="expense" {{ old('type') == 'expense' ? 'selected' : '' }}>{{ __('pages.fin_expense') }}</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="amount" class="form-label">{{ __('pages.fin_amount_label') }}</label>
                            <input type="text" name="amount" id="amount" value="{{ old('amount') }}" class="form-control shadow-sm rounded-3" placeholder="{{ __('pages.fin_amount_ph') }}">
                        </div>
                        <div class="col-12">
                            <label for="comment" class="form-label">{{ __('pages.fin_comment_label') }}</label>
                            <textarea name="comment" id="comment" class="form-control shadow-sm rounded-3" placeholder="{{ __('pages.fin_comment_ph') }}" rows="3">{{ old('comment') }}</textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-plus"></i> {{ __('pages.fin_create_btn') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Transactions List --}}
        <div class="card">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-list-ol txt-lightblue py-2"></i> {{ __('pages.fin_list') }}</span>
            </div>
            <div class="card-body">
                <form action="{{ route('financial.transactions') }}" method="get" class="input-group mb-3">
                    <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-control shadow-sm rounded-start-3" placeholder="{{ __('app.search') }}" aria-label="{{ __('app.search') }}" aria-describedby="basic-addon2">
                    <button type="submit" class="btn btn-darkblue shadow-sm rounded-end-3" id="basic-addon2"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th scope="col" class="text-nowrap">#</th>
                                <th scope="col" class="text-nowrap">{{ __('app.date') }}</th>
                                <th scope="col" class="text-nowrap">{{ __('pages.fin_type_col') }}</th>
                                <th scope="col" class="text-nowrap">{{ __('app.amount') }}</th>
                                <th scope="col" class="text-nowrap text-end">{{ __('app.operations') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($transactions) === 0)
                                <tr>
                                    <td colspan="5" class="text-center"><i class="fa-regular fa-face-frown"></i> {{ __('app.no_data') }}</td>
                                </tr>
                            @else
                                @foreach ($transactions as $transaction)
                                    <tr>
                                        <th scope="row" class="text-nowrap">{{ $transaction->id }}</th>
                                        <td class="text-nowrap">{{ \Carbon\Carbon::parse($transaction->date)->format('d/m/Y') }}</td>
                                        @if($transaction->type == 'revenue')
                                            <td class="text-nowrap"><span class="badge rounded-pill text-success text-bg-success bg-opacity-25 border border-success">{{ __('pages.fin_revenue') }}</span></td>
                                            <td class="text-nowrap text-success fw-semibold">+€{{ number_format($transaction->amount,2) }}</td>
                                        @else
                                            <td class="text-nowrap"><span class="badge rounded-pill text-danger text-bg-danger bg-opacity-25 border border-danger">{{ __('pages.fin_expense') }}</span></td>
                                            <td class="text-nowrap text-danger fw-semibold">-€{{ number_format($transaction->amount,2) }}</td>
                                        @endif
                                        <td class="text-nowrap text-end">
                                            <a href="{{ route('edit.transaction', $transaction->id) }}" class="btn btn-outline-success btn-sm rounded-3 shadow-sm"><i class="fa-solid fa-pen"></i></a>
                                            <button class="btn btn-outline-danger btn-sm rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $transaction->id }}"><i class="fa-solid fa-trash"></i></button>
                                            <button class="btn btn-outline-dark btn-sm rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#commentModal{{ $transaction->id }}"><i class="fa-solid fa-comment"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                @if ($transactions->hasPages())
                    <div class="mt-3">
                        {{ $transactions->links('vendor.pagination.custom') }}
                    </div>
                @endif
            </div>
        </div>

        @if(count($transactions) !== 0)

            <!-- Delete Modal -->
            @foreach ($transactions as $transaction)
                <div class="modal fade" id="deleteModal{{ $transaction->id }}" tabindex="-1" aria-labelledby="deleteModal{{ $transaction->id }}Label" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-body text-center">
                                <p>{{ __('app.confirm_delete') }}</p>
                                <div class="mt-3">
                                    <a href="{{ route('delete.transaction', $transaction->id) }}" class="btn btn-danger btn-sm fw-semibold"><i class="fa-solid fa-check"></i> {{ __('app.confirm') }}</a>
                                    <button class="btn btn-success btn-sm fw-semibold" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> {{ __('app.cancel') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Comment Modal -->
            @foreach ($transactions as $transaction)
                <div class="modal modal-lg fade" id="commentModal{{ $transaction->id }}" tabindex="-1" aria-labelledby="commentModal{{ $transaction->id }}Label" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-scrollable">
                        <div class="modal-content rounded-4">
                            <div class="modal-header justify-content-between align-items-center px-4">
                                <h1 class="modal-title fs-5 fw-semibold" id="commentModal{{ $transaction->id }}Label">{{ __('pages.fin_details') }} <strong>#TRID-{{ $transaction->id }}</strong></h1>
                                <button type="button" class="btn btn-darkblue btn-sm" data-bs-dismiss="modal" aria-label="{{ __('app.close') }}"><i class="fa-solid fa-xmark"></i></button>
                            </div>
                            <div class="modal-body bg-custom px-4 py-5">
                                @if($transaction->type == 'revenue')
                                    <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> {{ __('pages.fin_type_col') }} : </span><span class="badge rounded-pill text-success text-bg-success bg-opacity-25 border border-success">{{ __('pages.fin_revenue') }}</span></span>
                                @else
                                    <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> {{ __('pages.fin_type_col') }} : </span><span class="badge rounded-pill text-danger text-bg-danger bg-opacity-25 border border-danger">{{ __('pages.fin_expense') }}</span></span>
                                @endif
                                <span class="d-block mb-2"><span class="text-muted"><i class="fa-regular fa-calendar"></i> {{ __('app.date') }} : </span>{{ \Carbon\Carbon::parse($transaction->date)->format('d/m/Y') }}</span>

                                @if($transaction->type == 'revenue')
                                    <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-money-bill-wave"></i> {{ __('app.amount') }} : </span>+€{{ number_format($transaction->amount,2) }}</span>
                                @else
                                    <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-money-bill-wave"></i> {{ __('app.amount') }} : </span>-€{{ number_format($transaction->amount,2) }}</span>
                                @endif
                                <span class="d-block mt-4 mb-3 text-muted"><i class="fa-regular fa-file-lines"></i> {{ __('pages.fin_comment_label') }}</span>
                                <div class="card card-body rounded-4">
                                    <span>{!! nl2br(e($transaction->comment)) !!}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    @endif
@endsection
