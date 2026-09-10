@extends('template.template')

{{-- Page Title --}}
@section('title', __('app.nav_stock_report'))

{{-- Page Content --}}
@section('content')
    <small class="d-block txt-lightblue fw-semibold text-uppercase">{{ __('app.nav_materials') }}</small>
    <h3 class="fw-semibold pt-2">{{ __('app.nav_stock_report') }}</h3>
    <p class="text-muted">{{ __('pages.stock_report_lead') }}</p>
    <hr class="mb-4">

    <div class="card">
        <div class="card-header rounded-top-4 bg-white">
            <span><i class="fa-solid fa-chart-column txt-lightblue py-2"></i> {{ __('pages.stock_by_material') }}</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th scope="col" class="text-nowrap">#</th>
                            <th scope="col" class="text-nowrap">{{ __('pages.stock_material') }}</th>
                            <th scope="col" class="text-nowrap">{{ __('app.qty') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($stockReport) === 0)
                            <tr>
                                <td colspan="3" class="text-center"><i class="fa-regular fa-face-frown"></i> {{ __('app.no_data') }}</td>
                            </tr>
                        @else
                            @foreach($stockReport as $row)
                                <tr>
                                    <th scope="row" class="text-nowrap">{{ $row->id }}</th>
                                    <td class="text-nowrap">{{ $row->material }}</td>
                                    @if((float) $row->qty < 0)
                                        <td class="text-nowrap text-danger fw-semibold">{{ rtrim(rtrim(number_format((float) $row->qty, 2, '.', ''), '0'), '.') }} <small class="text-muted">({{ $row->unit }})</small></td>
                                    @else
                                        <td class="text-nowrap">{{ rtrim(rtrim(number_format((float) $row->qty, 2, '.', ''), '0'), '.') }} <small class="text-muted">({{ $row->unit }})</small></td>
                                    @endif
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            @if ($stockReport->hasPages())
                <div class="mt-3">
                    {{ $stockReport->links('vendor.pagination.custom') }}
                </div>
            @endif
        </div>
    </div>
@endsection
