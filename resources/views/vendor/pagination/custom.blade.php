@if ($paginator->hasPages())
    <nav class="container">
        <ul class="pagination flex-wrap justify-content-center">

            {{-- Previous Cursor Link --}}
            @if ($paginator->previousPageUrl())
                <li class="page-item me-1">
                    <a class="page-link rounded-4"
                       href="{{ $paginator->appends(request()->only('search'))->previousPageUrl() }}">
                        <i class="fa-solid fa-angle-left txt-darkblue"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled me-1">
                    <span class="page-link rounded-4">
                        <i class="fa-solid fa-angle-left txt-darkblue"></i>
                    </span>
                </li>
            @endif

            {{-- Next Cursor Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item ms-1">
                    <a class="page-link rounded-4"
                       href="{{ $paginator->appends(request()->only('search'))->nextPageUrl() }}">
                        <i class="fa fa-angle-right txt-darkblue"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled ms-1">
                    <span class="page-link rounded-4">
                        <i class="fa fa-angle-right txt-darkblue"></i>
                    </span>
                </li>
            @endif

        </ul>
    </nav>
@endif
