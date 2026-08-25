@extends('template.template')

@section('title', 'Njoftimet')

@section('content')
    <small class="d-block txt-lightblue fw-semibold text-uppercase">Përmbledhje</small>
    <h3 class="fw-semibold pt-2">Njoftimet</h3>
    <p class="text-muted">Shiko dhe menaxho njoftimet e llogarisë suaj.</p>
    <hr class="mb-4">

    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-uppercase opacity-75"><i class="fa-solid fa-bell text-warning"></i> Të palexuara</small>
                    <h4 class="fw-semibold mb-0">{{ $unreadCount ?? 0 }}</h4>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-8 d-flex align-items-end justify-content-md-end">
            <form action="{{ route('notifications.mark.all') }}" method="post" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-darkblue rounded-3 shadow-sm">
                    <i class="fa-solid fa-check-double"></i> Shëno të gjitha si të lexuara
                </button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header rounded-top-4 bg-white">
            <span><i class="fa-regular fa-bell txt-lightblue py-2"></i> Lista e Njoftimeve</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th scope="col" class="text-nowrap">#</th>
                            <th scope="col" class="text-nowrap">Titulli</th>
                            <th scope="col" class="text-nowrap">Mesazhi</th>
                            <th scope="col" class="text-nowrap">Data</th>
                            <th scope="col" class="text-nowrap">Statusi</th>
                            <th scope="col" class="text-nowrap text-end">Veprimet</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!$notifications || count($notifications) === 0)
                            <tr>
                                <td colspan="6" class="text-center"><i class="fa-regular fa-face-frown"></i> Nuk u gjetën njoftime...</td>
                            </tr>
                        @else
                            @foreach($notifications as $notification)
                                <tr class="{{ !(int) $notification->is_read ? 'table-light' : '' }}">
                                    <th scope="row" class="text-nowrap">{{ $notification->id }}</th>
                                    <td class="text-nowrap fw-semibold">{{ $notification->title }}</td>
                                    <td class="text-nowrap">{{ $notification->message }}</td>
                                    <td class="text-nowrap">{{ $notification->created_at ? \Carbon\Carbon::parse($notification->created_at)->format('d/m/Y H:i') : '-' }}</td>
                                    <td class="text-nowrap">
                                        @if(!(int) $notification->is_read)
                                            <span class="badge rounded-pill text-dark text-bg-warning bg-opacity-25 border border-warning">E palexuar</span>
                                        @else
                                            <span class="badge rounded-pill text-success text-bg-success bg-opacity-25 border border-success">E lexuar</span>
                                        @endif
                                    </td>
                                    <td class="text-nowrap text-end">
                                        <form action="{{ route('notifications.open', $notification->id) }}" method="post" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-primary btn-sm rounded-3 shadow-sm">
                                                <i class="fa-solid fa-arrow-up-right-from-square"></i> Hap
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            @if($notifications && $notifications->hasPages())
                <div class="mt-3">
                    {{ $notifications->links('vendor.pagination.custom') }}
                </div>
            @endif
        </div>
    </div>
@endsection
