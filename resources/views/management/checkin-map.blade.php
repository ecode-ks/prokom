@extends('template.template')

{{-- Page Title --}}
@section('title', 'Harta e Prezencës')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
<style>
    #checkin-team-map { height: 520px; width: 100%; }
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

    <div class="card">
        <div class="card-header rounded-top-4 bg-white d-flex justify-content-between align-items-center">
            <span><i class="fa-solid fa-map-location-dot txt-lightblue py-2"></i> Harta</span>
            <small class="text-muted">{{ count($locations) }} lokacion{{ count($locations) === 1 ? '' : 'e' }}</small>
        </div>
        <div class="card-body p-0">
            @if(count($locations) === 0)
                <div class="text-center text-muted py-5 px-3">
                    <i class="fa-solid fa-location-dot mb-2 opacity-50"></i>
                    <p class="mb-0">Nuk ka check-in aktiv me lokacion për sot.</p>
                </div>
            @else
                <div id="checkin-team-map"></div>
            @endif
        </div>
    </div>
@endsection

@if(count($locations) > 0)
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
(function () {
    const locations = @json($locations);
    const map = L.map('checkin-team-map').setView([locations[0].lat, locations[0].lng], 13);

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

        L.marker([p.lat, p.lng]).addTo(map).bindPopup(popup);
        bounds.push([p.lat, p.lng]);
    });

    if (bounds.length > 1) {
        map.fitBounds(bounds, { padding: [40, 40] });
    }
})();
</script>
@endpush
@endif
