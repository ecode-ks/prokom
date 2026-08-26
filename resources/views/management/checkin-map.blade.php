@extends('template.template')

{{-- Page Title --}}
@section('title', 'Harta e Prezencës')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
<style>
    #checkin-team-map { height: 520px; width: 100%; }

    .checkin-map-panel-body {
        height: 520px;
        min-height: 520px;
        max-height: 520px;
        overflow: hidden;
    }

    .checkin-location-list {
        height: 100%;
        overflow-y: auto;
        overscroll-behavior: contain;
        scrollbar-gutter: stable;
        scroll-behavior: smooth;
    }

    .checkin-location-list::-webkit-scrollbar {
        width: 7px;
    }

    .checkin-location-list::-webkit-scrollbar-track {
        background: #f4f7fa;
    }

    .checkin-location-list::-webkit-scrollbar-thumb {
        background: rgba(7, 24, 56, 0.18);
        border-radius: 999px;
    }

    .checkin-location-list::-webkit-scrollbar-thumb:hover {
        background: rgba(7, 24, 56, 0.32);
    }

    .checkin-location-item {
        display: block;
        width: 100%;
        text-align: left;
        border: 0;
        border-bottom: 1px solid #EEF2F6;
        background: #fff;
        padding: 1rem 1.15rem;
        transition: background 0.15s ease;
    }

    .checkin-location-item:last-child {
        border-bottom: 0;
    }

    .checkin-location-item:hover,
    .checkin-location-item.is-active {
        background: rgba(13, 110, 253, 0.06);
    }

    .checkin-location-item:focus-visible {
        outline: 2px solid rgba(13, 110, 253, 0.35);
        outline-offset: -2px;
    }

    .checkin-map-status {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.32rem 0.7rem;
        border-radius: 999px;
        font-size: 0.68rem;
        font-weight: 700;
        letter-spacing: 0.02em;
        line-height: 1.2;
        flex-shrink: 0;
        white-space: nowrap;
    }

    .checkin-map-status-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .checkin-map-status.is-work {
        color: #0f6b3f;
        background: rgba(16, 185, 129, 0.12);
        border: 1px solid rgba(16, 185, 129, 0.24);
    }

    .checkin-map-status.is-work .checkin-map-status-dot {
        background: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.18);
    }

    .checkin-map-status.is-pause {
        color: #b45309;
        background: rgba(245, 158, 11, 0.12);
        border: 1px solid rgba(245, 158, 11, 0.26);
    }

    .checkin-map-status.is-pause .checkin-map-status-dot {
        background: #f59e0b;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.18);
    }

    .checkin-map-status.is-review {
        color: #475569;
        background: rgba(100, 116, 139, 0.1);
        border: 1px solid rgba(100, 116, 139, 0.2);
    }

    .checkin-map-status.is-review .checkin-map-status-dot {
        background: #64748b;
        box-shadow: 0 0 0 3px rgba(100, 116, 139, 0.15);
    }
</style>
@endpush

{{-- Page Content --}}
@section('content')
    <small class="d-block text-uppercase">
        <a href="{{ route('dashboard') }}" class="text-decoration-none txt-lightblue fw-semibold">
            <i class="fa-solid fa-circle-chevron-left"></i> Kthehu
        </a>
    </small>
    <h3 class="fw-semibold pt-2">Harta e prezencës</h3>
    <p class="text-muted">Lokacionet e punonjësve aktivë sot.</p>
    <hr class="mb-4">

    @if(count($locations) === 0)
        <div class="card shadow-sm">
            <div class="card-body text-center text-muted py-5 px-3">
                <i class="fa-solid fa-location-dot mb-2 opacity-50 fa-lg"></i>
                <p class="mb-0">Nuk ka check-in aktiv me lokacion për sot.</p>
            </div>
        </div>
    @else
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card shadow-sm h-100">
                    <div class="card-header rounded-top-4 bg-white d-flex justify-content-between align-items-center">
                        <span><i class="fa-solid fa-map-location-dot txt-lightblue py-2"></i> Harta</span>
                        <small class="text-muted">{{ count($locations) }} lokacion{{ count($locations) === 1 ? '' : 'e' }}</small>
                    </div>
                    <div class="card-body p-0">
                        <div id="checkin-team-map"></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header rounded-top-4 bg-white d-flex justify-content-between align-items-center">
                        <span><i class="fa-solid fa-users txt-lightblue py-2"></i> Punonjësit aktivë</span>
                        <small class="text-muted">{{ count($locations) }}</small>
                    </div>
                    <div class="card-body p-0 d-flex flex-column checkin-map-panel-body">
                        <div class="checkin-location-list" id="checkinLocationList">
                            @foreach($locations as $index => $loc)
                                @php
                                    $isPause = ($loc['status'] ?? '') === 'Në pauzë';
                                    $statusClass = $isPause
                                        ? 'is-pause'
                                        : (in_array(strtolower((string) ($loc['status'] ?? '')), ['under review', 'pending'], true) ? 'is-review' : 'is-work');
                                    $statusLabel = $isPause ? 'Në pauzë' : ($statusClass === 'is-work' ? 'Në punë' : ($loc['status'] ?? 'Në pritje'));
                                    $mapsQuery = ($loc['lat'] ?? '') . ',' . ($loc['lng'] ?? '');
                                @endphp
                                <button
                                    type="button"
                                    class="checkin-location-item"
                                    data-map-index="{{ $index }}"
                                >
                                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                        <span class="fw-semibold txt-darkblue">{{ $loc['name'] }}</span>
                                        <span class="checkin-map-status {{ $statusClass }}">
                                            <span class="checkin-map-status-dot" aria-hidden="true"></span>
                                            {{ $statusLabel }}
                                        </span>
                                    </div>
                                    <div class="small text-muted mb-1">
                                        <i class="fa-solid fa-diagram-project me-1 opacity-75"></i>
                                        {{ $loc['project'] ?: '-' }}
                                    </div>
                                    <div class="small mb-1">
                                        <i class="fa-solid fa-location-dot txt-lightblue me-1"></i>
                                        <a
                                            href="https://www.google.com/maps?q={{ urlencode($mapsQuery) }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="text-decoration-none txt-lightblue fw-semibold"
                                            onclick="event.stopPropagation()"
                                        >
                                            {{ number_format($loc['lat'], 5) }}, {{ number_format($loc['lng'], 5) }}
                                        </a>
                                    </div>
                                    <div class="small text-muted">
                                        <i class="fa-solid fa-clock me-1 opacity-75"></i>
                                        Hyrja: {{ $loc['check_in_time'] }}
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@if(count($locations) > 0)
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
(function () {
    const locations = @json($locations);
    const map = L.map('checkin-team-map').setView([locations[0].lat, locations[0].lng], 13);
    const markers = [];

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    const bounds = [];

    locations.forEach(function (p) {
        const popup = ''
            + '<strong>' + p.name + '</strong><br>'
            + 'Projekti: ' + (p.project || '-') + '<br>'
            + 'Statusi: ' + (p.status || '-') + '<br>'
            + 'Hyrja: ' + (p.check_in_time || '-');

        const marker = L.marker([p.lat, p.lng]).addTo(map).bindPopup(popup);
        markers.push(marker);
        bounds.push([p.lat, p.lng]);
    });

    if (bounds.length > 1) {
        map.fitBounds(bounds, { padding: [40, 40] });
    }

    function focusLocation(index) {
        const point = locations[index];
        if (!point || !markers[index]) return;

        map.setView([point.lat, point.lng], 16, { animate: true });
        markers[index].openPopup();

        document.querySelectorAll('.checkin-location-item').forEach(function (el) {
            el.classList.toggle('is-active', parseInt(el.getAttribute('data-map-index'), 10) === index);
        });

        const activeItem = document.querySelector('.checkin-location-item[data-map-index="' + index + '"]');
        if (activeItem) {
            activeItem.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
        }
    }

    document.querySelectorAll('.checkin-location-item').forEach(function (el) {
        el.addEventListener('click', function () {
            focusLocation(parseInt(el.getAttribute('data-map-index'), 10));
        });
    });
})();
</script>
@endpush
@endif
