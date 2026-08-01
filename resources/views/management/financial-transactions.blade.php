@extends('template.template')

{{-- Page Title --}}
@section('title', isset($editTransaction) ? 'Përditëso Transaksionin' : 'Të Ardhurat & Shpenzimet')

{{-- Page Content --}}
@section('content')
    @if(isset($editTransaction))
        <small class="d-block txt-lightblue fw-semibold text-uppercase"><a href="{{ route('financial.transactions') }}" class="text-decoration-none txt-lightblue fw-semibold"><i class="fa-solid fa-circle-chevron-left"></i> Kthehu</a></small>
        <h3 class="fw-semibold pt-2">Përditëso Transaksionin</h3>
        <p class="text-muted">Përditëso të dhënat e transaksionit të zgjedhur.</p>
        <hr class="mb-4">
    @else
        <small class="d-block txt-lightblue fw-semibold text-uppercase">Financat · {{ now()->translatedFormat('F Y') }}</small>
        <h3 class="fw-semibold pt-2">Të Ardhurat & Shpenzimet</h3>
        <p class="text-muted">Ndjek dhe menaxho të ardhurat dhe shpenzimet e kompanisë.</p>
        <hr class="mb-4">
    @endif

    @if(isset($editTransaction))

        {{-- Update Transaction --}}
        <div class="card mb-4">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-money-bill-transfer txt-lightblue py-2"></i> Përditëso Transaksionin</span>
            </div>
            <div class="card-body">
                <form action="{{ route('update.transaction') }}" method="post">
                    <input type="hidden" name="id" value="{{ $editTransaction->id }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="type" class="form-label">Lloji i Transaksionit</label>
                            <select name="type" id="type" class="form-control shadow-sm rounded-3">
                                <option value="">-- Zgjidh Llojin --</option>
                                <option value="revenue" {{ old('type', $editTransaction->type) == 'revenue' ? 'selected' : '' }}>Të ardhura</option>
                                <option value="expense" {{ old('type', $editTransaction->type) == 'expense' ? 'selected' : '' }}>Shpenzim</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="amount" class="form-label">Shuma e Transaksionit</label>
                            <input type="text" name="amount" id="amount" value="{{ old('amount', $editTransaction->amount) }}" class="form-control shadow-sm rounded-3" placeholder="Shkruaj shumën e transaksionit...">
                        </div>
                        <div class="col-12">
                            <label for="comment" class="form-label">Komenti i Transaksionit</label>
                            <textarea name="comment" id="comment" class="form-control shadow-sm rounded-3" placeholder="Shkruaj komentin..." rows="3">{{ old('comment', $editTransaction->comment) }}</textarea>
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
        @php
            $totalRevenues = $revenues ?? 0;
            $totalExpenses = $expenses ?? 0;
        @endphp

        <div class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-uppercase opacity-75"><i class="fa-solid fa-arrow-trend-up text-success"></i> Të Ardhurat</small>
                        <h4 class="fw-semibold mb-0 text-success">€{{ number_format($totalRevenues,2) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-uppercase opacity-75"><i class="fa-solid fa-arrow-trend-down text-danger"></i> Shpenzimet</small>
                        <h4 class="fw-semibold mb-0 text-danger">-€{{ number_format($totalExpenses,2) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card h-100">
                    <div class="card-body bg-darkblue text-white rounded-4">
                        <small class="text-uppercase opacity-75">Bilanci</small>
                        <h4 class="fw-semibold mb-0">€{{ number_format($totalRevenues-$totalExpenses,2) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        {{-- Create Transaction --}}
        <div class="card mb-4">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-money-bill-transfer txt-lightblue py-2"></i> Krijo Transaksion të Ri</span>
            </div>
            <div class="card-body">
                <form action="{{ route('create.transaction') }}" method="post">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="type" class="form-label">Lloji i Transaksionit</label>
                            <select name="type" id="type" class="form-control shadow-sm rounded-3">
                                <option value="">-- Zgjidh Llojin --</option>
                                <option value="revenue" {{ old('type') == 'revenue' ? 'selected' : '' }}>Të ardhura</option>
                                <option value="expense" {{ old('type') == 'expense' ? 'selected' : '' }}>Shpenzim</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="amount" class="form-label">Shuma e Transaksionit</label>
                            <input type="text" name="amount" id="amount" value="{{ old('amount') }}" class="form-control shadow-sm rounded-3" placeholder="Shkruaj shumën e transaksionit...">
                        </div>
                        <div class="col-12">
                            <label for="comment" class="form-label">Komenti i Transaksionit</label>
                            <textarea name="comment" id="comment" class="form-control shadow-sm rounded-3" placeholder="Shkruaj komentin..." rows="3">{{ old('comment') }}</textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-plus"></i> Krijo Transaksionin</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Transactions List --}}
        <div class="card">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-list-ol txt-lightblue py-2"></i> Lista e Transaksioneve</span>
            </div>
            <div class="card-body">
                <form action="{{ route('financial.transactions') }}" method="get" class="input-group mb-3">
                    <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-control shadow-sm rounded-start-3" placeholder="Kërko..." aria-label="Kërko..." aria-describedby="basic-addon2">
                    <button type="submit" class="btn btn-darkblue shadow-sm rounded-end-3" id="basic-addon2"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th scope="col" class="text-nowrap">#</th>
                                <th scope="col" class="text-nowrap">Data</th>
                                <th scope="col" class="text-nowrap">Lloji</th>
                                <th scope="col" class="text-nowrap">Shuma</th>
                                <th scope="col" class="text-nowrap text-end">Operacionet</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($transactions) === 0)
                                <tr>
                                    <td colspan="5" class="text-center"><i class="fa-regular fa-face-frown"></i> Nuk u gjetën të dhëna...</td>
                                </tr>
                            @else
                                @foreach ($transactions as $transaction)
                                    <tr>
                                        <th scope="row" class="text-nowrap">{{ $transaction->id }}</th>
                                        <td class="text-nowrap">{{ \Carbon\Carbon::parse($transaction->date)->format('d/m/Y') }}</td>
                                        @if($transaction->type == 'revenue')
                                            <td class="text-nowrap"><span class="badge rounded-pill text-success text-bg-success bg-opacity-25 border border-success">Të ardhura</span></td>
                                            <td class="text-nowrap text-success fw-semibold">+€{{ number_format($transaction->amount,2) }}</td>
                                        @else
                                            <td class="text-nowrap"><span class="badge rounded-pill text-danger text-bg-danger bg-opacity-25 border border-danger">Shpenzim</span></td>
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
                                <p>A jeni i sigurt që dëshironi ta fshini këtë transaksion?</p>
                                <div class="mt-3">
                                    <a href="{{ route('delete.transaction', $transaction->id) }}" class="btn btn-danger btn-sm fw-semibold"><i class="fa-solid fa-check"></i> Konfirmo</a>
                                    <button class="btn btn-success btn-sm fw-semibold" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> Anulo</button>
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
                                <h1 class="modal-title fs-5 fw-semibold" id="commentModal{{ $transaction->id }}Label">Detajet e Transaksionit <strong>#TRID-{{ $transaction->id }}</strong></h1>
                                <button type="button" class="btn btn-darkblue btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
                            </div>
                            <div class="modal-body bg-custom px-4 py-5">
                                @if($transaction->type == 'revenue')
                                    <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> Lloji : </span><span class="badge rounded-pill text-success text-bg-success bg-opacity-25 border border-success">Të ardhura</span></span>
                                @else
                                    <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-circle-info"></i> Lloji : </span><span class="badge rounded-pill text-danger text-bg-danger bg-opacity-25 border border-danger">Shpenzim</span></span>
                                @endif
                                <span class="d-block mb-2"><span class="text-muted"><i class="fa-regular fa-calendar"></i> Data : </span>{{ \Carbon\Carbon::parse($transaction->date)->format('d/m/Y') }}</span>

                                @if($transaction->type == 'revenue')
                                    <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-money-bill-wave"></i> Shuma : </span>+€{{ number_format($transaction->amount,2) }}</span>
                                @else
                                    <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-money-bill-wave"></i> Shuma : </span>-€{{ number_format($transaction->amount,2) }}</span>
                                @endif
                                <span class="d-block mt-4 mb-3 text-muted"><i class="fa-regular fa-file-lines"></i> Komenti i Transaksionit</span>
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
