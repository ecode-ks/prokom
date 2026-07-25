@extends('template.template')

@section('title', 'Raporti Financiar')

@section('content')
    <small class="d-block txt-lightblue fw-semibold text-uppercase">Financat</small>
    <h3 class="fw-semibold pt-2">Raporti Financiar</h3>
    <p class="text-muted">Filtro transaksionet sipas periudhës dhe shiko përmbledhjen e performancës financiare.</p>
    <hr class="mb-4">

    @php
        $summary = $report['summary'] ?? [];
        $transactions = $report['transactions'] ?? null;
        $revenues = $summary['revenues'] ?? 0;
        $expenses = $summary['expenses'] ?? 0;
        $balance = $summary['balance'] ?? 0;
        $transactionCount = $summary['transaction_count'] ?? 0;
        $averageTransaction = $summary['average_transaction'] ?? 0;
        $fromLabel = $fromDate ? \Carbon\Carbon::parse($fromDate)->format('d/m/Y') : '-';
        $toLabel = $toDate ? \Carbon\Carbon::parse($toDate)->format('d/m/Y') : '-';
    @endphp

    <div class="card mb-4">
        <div class="card-body p-4">
            <div class="row align-items-center g-3">
                <div class="col-12 col-lg-8">
                    <small class="text-uppercase opacity-75 d-block mb-1">Periudha e Raportit</small>
                    <h5 class="fw-semibold mb-0">{{ $fromLabel }} deri {{ $toLabel }}</h5>
                </div>
                <div class="col-12 col-lg-4 text-lg-end">
                    <form action="{{ route('financial.report') }}" method="get" class="row g-2 justify-content-end">
                        <div class="col-12 col-md-6 col-lg-5">
                            <input type="date" name="from_date" value="{{ request('from_date', $fromDate) }}" class="form-control shadow-sm rounded-3" placeholder="Nga data">
                        </div>
                        <div class="col-12 col-md-6 col-lg-5">
                            <input type="date" name="to_date" value="{{ request('to_date', $toDate) }}" class="form-control shadow-sm rounded-3" placeholder="Deri data">
                        </div>
                        <div class="col-12 col-lg-2 d-grid">
                            <button type="submit" class="btn btn-darkblue rounded-3 shadow-sm"><i class="fa-solid fa-filter"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-uppercase opacity-75"><i class="fa-solid fa-arrow-trend-up text-success"></i> Të Ardhurat</small>
                    <h4 class="fw-semibold mb-0 text-success">€{{ number_format($revenues, 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-uppercase opacity-75"><i class="fa-solid fa-arrow-trend-down text-danger"></i> Shpenzimet</small>
                    <h4 class="fw-semibold mb-0 text-danger">-€{{ number_format($expenses, 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body bg-darkblue text-white rounded-4">
                    <small class="text-uppercase opacity-75 d-block">Bilanci Neto</small>
                    <h4 class="fw-semibold mb-0">€{{ number_format($balance, 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-uppercase opacity-75"><i class="fa-solid fa-receipt"></i> Transaksionet</small>
                    <h4 class="fw-semibold mb-0">{{ number_format($transactionCount) }}</h4>
                    <small class="text-muted">Mes. €{{ number_format($averageTransaction, 2) }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body p-4">
            <div class="row g-3 align-items-center">
                <div class="col-12 col-lg-4">
                    <div class="p-3 rounded-4 bg-light">
                        <small class="text-uppercase opacity-75 d-block">Të Ardhurat vs Shpenzimet</small>
                        @php
                            $totalFlow = max($revenues + $expenses, 1);
                            $revenuePercent = ($revenues / $totalFlow) * 100;
                            $expensePercent = ($expenses / $totalFlow) * 100;
                        @endphp
                        <div class="mt-3">
                            <div class="d-flex justify-content-between mb-1">
                                <small>Të ardhura</small>
                                <small>{{ number_format($revenuePercent, 1) }}%</small>
                            </div>
                            <div class="progress mb-3" style="height: 10px;">
                                <div class="progress-bar bg-success" style="width: {{ $revenuePercent }}%"></div>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <small>Shpenzim</small>
                                <small>{{ number_format($expensePercent, 1) }}%</small>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-danger" style="width: {{ $expensePercent }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-8">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
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
                                @if(!$transactions || count($transactions) === 0)
                                    <tr>
                                        <td colspan="5" class="text-center py-4"><i class="fa-regular fa-face-frown"></i> Nuk u gjetën të dhëna...</td>
                                    </tr>
                                @else
                                    @foreach ($transactions as $transaction)
                                        <tr>
                                            <th scope="row" class="text-nowrap">{{ $transaction->id }}</th>
                                            <td class="text-nowrap">{{ \Carbon\Carbon::parse($transaction->date)->format('d/m/Y') }}</td>
                                            <td class="text-nowrap">
                                                @if($transaction->type === 'revenue')
                                                    <span class="badge rounded-pill text-success text-bg-success bg-opacity-25 border border-success">Të ardhura</span>
                                                @else
                                                    <span class="badge rounded-pill text-danger text-bg-danger bg-opacity-25 border border-danger">Shpenzim</span>
                                                @endif
                                            </td>
                                            <td class="text-nowrap fw-semibold {{ $transaction->type === 'revenue' ? 'text-success' : 'text-danger' }}">
                                                {{ $transaction->type === 'revenue' ? '+' : '-' }}€{{ number_format($transaction->amount, 2) }}
                                            </td>
                                            <td class="text-nowrap text-end">
                                                <button class="btn btn-outline-dark btn-sm rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#commentModal{{ $transaction->id }}"><i class="fa-solid fa-comment"></i></button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>

                    @if($transactions && $transactions->hasPages())
                        <div class="mt-3">
                            {{ $transactions->appends(request()->query())->links('vendor.pagination.custom') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($transactions && count($transactions) !== 0)
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
@endsection
