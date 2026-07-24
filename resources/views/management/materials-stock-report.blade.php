@extends('template.template')

{{-- Page Title --}}
@section('title', 'Stock Report')

{{-- Page Content --}}
@section('content')
    <small class="d-block txt-lightblue fw-semibold text-uppercase">Materials</small>
    <h3 class="fw-semibold pt-2">Stock Report</h3>
    <p class="text-muted">Current stock level summary for each material.</p>
    <hr class="mb-4">

    <div class="card">
        <div class="card-header rounded-top-4 bg-white">
            <span><i class="fa-solid fa-chart-column txt-lightblue py-2"></i> Stock per Material</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th scope="col" class="text-nowrap">#</th>
                            <th scope="col" class="text-nowrap">Material</th>
                            <th scope="col" class="text-nowrap">Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($stockReport) === 0)
                            <tr>
                                <td colspan="3" class="text-center"><i class="fa-regular fa-face-frown"></i> No data found...</td>
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

            @if(method_exists($stockReport, 'hasPages') && $stockReport->hasPages())
                <div class="mt-3">
                    {{ $stockReport->links('vendor.pagination.custom') }}
                </div>
            @endif
        </div>
    </div>
@endsection
