@extends('template.template')

@section('title', __('pages.checkin_map_title'))

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
<style>
    .checkin-map-stage {
        position: relative;
        height: min(72vh, 680px);
        min-height: 440px;
        border-radius: 1.1rem;
        overflow: hidden;
        border: 1px solid rgba(7, 24, 56, 0.08);
        box-shadow: 0 16px 40px rgba(7, 24, 56, 0.08);
        background: #eef3f8;
    }

    #checkin-team-map {
        height: 100%;
        width: 100%;
    }

    .checkin-map-overlay {
        position: absolute;
        top: 1rem;
        right: 1rem;
        width: min(21.5rem, calc(100% - 1.5rem));
        max-height: calc(100% - 2rem);
        display: flex;
        flex-direction: column;
        background: rgba(255, 255, 255, 0.96);
        backdrop-filter: blur(12px);
        border-radius: 1rem;
        box-shadow: 0 18px 40px rgba(7, 24, 56, 0.16);
        z-index: 1000;
        overflow: hidden;
    }

    .checkin-map-stats {
        display: flex;
        gap: 0.4rem;
        padding: 0.75rem 0.8rem 0.45rem;
        flex-wrap: wrap;
    }

    .checkin-map-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        border-radius: 999px;
        padding: 0.28rem 0.65rem;
        font-size: 0.72rem;
        font-weight: 700;
        line-height: 1.2;
    }

    .checkin-map-chip.is-work { background: rgba(16, 185, 129, 0.12); color: #0f6b3f; }
    .checkin-map-chip.is-pause { background: rgba(245, 158, 11, 0.14); color: #b45309; }
    .checkin-map-chip.is-review { background: rgba(100, 116, 139, 0.12); color: #475569; }

    .checkin-map-search {
        padding: 0 0.8rem 0.65rem;
    }

    .checkin-map-search input {
        width: 100%;
        border: 1px solid #e8eef5;
        border-radius: 0.7rem;
        padding: 0.5rem 0.75rem;
        font-size: 0.82rem;
        outline: none;
        background: #f8fafc;
    }

    .checkin-map-search input:focus {
        border-color: rgba(0, 96, 170, 0.35);
        background: #fff;
    }

    .checkin-location-list {
        overflow-y: auto;
        overscroll-behavior: contain;
        flex: 1;
        min-height: 0;
    }

    .checkin-location-item {
        display: block;
        width: 100%;
        text-align: left;
        border: 0;
        border-bottom: 1px solid #EEF2F6;
        background: transparent;
        padding: 0.8rem 0.9rem;
        transition: background 0.15s ease;
        cursor: pointer;
    }

    .checkin-location-item:hover,
    .checkin-location-item.is-active {
        background: rgba(13, 110, 253, 0.07);
    }

    .checkin-map-avatar {
        width: 2rem;
        height: 2rem;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 0.68rem;
        font-weight: 700;
        flex-shrink: 0;
    }

    .checkin-map-avatar.is-work { background: #10b981; }
    .checkin-map-avatar.is-pause { background: #f59e0b; }
    .checkin-map-avatar.is-review { background: #64748b; }

    .checkin-map-status {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.22rem 0.55rem;
        border-radius: 999px;
        font-size: 0.65rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .checkin-map-status-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
    }

    .checkin-map-status.is-work { color: #0f6b3f; background: rgba(16, 185, 129, 0.12); }
    .checkin-map-status.is-work .checkin-map-status-dot { background: #10b981; }
    .checkin-map-status.is-pause { color: #b45309; background: rgba(245, 158, 11, 0.12); }
    .checkin-map-status.is-pause .checkin-map-status-dot { background: #f59e0b; }
    .checkin-map-status.is-review { color: #475569; background: rgba(100, 116, 139, 0.12); }
    .checkin-map-status.is-review .checkin-map-status-dot { background: #64748b; }

    .checkin-map-together {
        display: inline-flex;
        margin-top: 0.3rem;
        font-size: 0.68rem;
        font-weight: 650;
        color: #071838;
        background: rgba(7, 24, 56, 0.06);
        border-radius: 999px;
        padding: 0.15rem 0.5rem;
    }

    .checkin-map-actions {
        display: flex;
        gap: 0.4rem;
        margin-top: 0.55rem;
        flex-wrap: wrap;
    }

    .checkin-map-action {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        border-radius: 999px;
        padding: 0.28rem 0.65rem;
        font-size: 0.7rem;
        font-weight: 700;
        text-decoration: none;
        border: 1px solid #e8eef5;
        background: #fff;
        color: #071838;
        line-height: 1.2;
    }

    .checkin-map-action:hover {
        background: #f8fafc;
        color: #0060aa;
        border-color: rgba(0, 96, 170, 0.25);
    }

    .checkin-map-action.is-disabled {
        opacity: 0.45;
        pointer-events: none;
        cursor: not-allowed;
    }

    .checkin-marker-wrap { background: transparent !important; border: 0 !important; }
    .checkin-marker {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border: 2px solid #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 700;
        color: #fff;
        box-shadow: 0 6px 14px rgba(7, 24, 56, 0.28);
    }
    .checkin-marker.is-work { background: #10b981; }
    .checkin-marker.is-pause { background: #f59e0b; }
    .checkin-marker.is-review { background: #64748b; }
    .checkin-marker.is-active {
        transform: scale(1.12);
        box-shadow: 0 0 0 4px rgba(0, 96, 170, 0.28), 0 8px 16px rgba(7, 24, 56, 0.3);
    }

    .checkin-stack {
        position: relative;
        width: 52px;
        height: 38px;
    }
    .checkin-stack .checkin-marker {
        position: absolute;
        top: 2px;
        width: 32px;
        height: 32px;
        font-size: 10px;
    }
    .checkin-stack .checkin-marker:nth-child(1) { left: 0; z-index: 3; }
    .checkin-stack .checkin-marker:nth-child(2) { left: 14px; z-index: 2; }
    .checkin-stack-badge {
        position: absolute;
        right: -2px;
        top: -4px;
        min-width: 1.15rem;
        height: 1.15rem;
        padding: 0 0.22rem;
        border-radius: 999px;
        background: #e11d48;
        color: #fff;
        font-size: 0.65rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #fff;
        z-index: 5;
        box-sizing: border-box;
    }

    .checkin-popup-person {
        display: flex;
        align-items: center;
        gap: 0.45rem;
        margin-top: 0.45rem;
        font-size: 0.8rem;
        color: #071838;
    }
    .checkin-popup-person .checkin-marker {
        width: 22px;
        height: 22px;
        font-size: 8px;
        box-shadow: none;
        flex-shrink: 0;
    }

    .checkin-popup .leaflet-popup-content-wrapper {
        border-radius: 0.85rem;
        box-shadow: 0 12px 28px rgba(7, 24, 56, 0.16);
    }
    .checkin-popup .leaflet-popup-content { margin: 0.85rem 0.95rem; }
    .checkin-popup-name { font-weight: 700; color: #071838; }
    .checkin-popup-meta { font-size: 0.78rem; color: #64748b; margin-top: 0.25rem; }
    .checkin-popup-link { display: inline-block; margin-top: 0.45rem; font-size: 0.78rem; font-weight: 650; }

    .leaflet-container { font-family: inherit; }

    @media (max-width: 991.98px) {
        .checkin-map-stage {
            height: auto;
            min-height: 0;
            overflow: visible;
        }
        #checkin-team-map {
            height: 340px;
            border-radius: 1.1rem 1.1rem 0 0;
        }
        .checkin-map-overlay {
            position: relative;
            top: auto;
            right: auto;
            width: 100%;
            max-height: 280px;
            border-radius: 0 0 1.1rem 1.1rem;
            box-shadow: none;
            border-top: 1px solid #e8eef5;
        }
    }
</style>
@endpush

@section('content')
    @php
        $workCount = collect($locations)->where('status_key', 'work')->count();
        $pauseCount = collect($locations)->where('status_key', 'pause')->count();
        $reviewCount = collect($locations)->where('status_key', 'review')->count();
        $canAssignTask = in_array(session('role'), ['company', 'manager'], true);
    @endphp

    <small class="d-block text-uppercase">
        <a href="{{ route('dashboard') }}" class="text-decoration-none txt-lightblue fw-semibold">
            <i class="fa-solid fa-circle-chevron-left"></i> {{ __('pages.back') }}
        </a>
    </small>
    <h3 class="fw-semibold pt-2">{{ __('pages.checkin_map_heading') }}</h3>
    <p class="text-muted">{{ __('pages.checkin_map_lead') }}</p>
    <hr class="mb-4">

    @if(count($locations) === 0)
        <div class="card shadow-sm">
            <div class="card-body text-center text-muted py-5 px-3">
                <i class="fa-solid fa-location-dot mb-2 opacity-50 fa-lg"></i>
                <p class="mb-0">{{ __('pages.checkin_map_empty') }}</p>
            </div>
        </div>
    @else
        <div class="checkin-map-stage">
            <div id="checkin-team-map"></div>
            <aside class="checkin-map-overlay">
                <div class="checkin-map-stats">
                    <span class="checkin-map-chip is-work">{{ $workCount }} {{ __('pages.at_work') }}</span>
                    <span class="checkin-map-chip is-pause">{{ $pauseCount }} {{ __('pages.on_pause') }}</span>
                    @if($reviewCount > 0)
                        <span class="checkin-map-chip is-review">{{ $reviewCount }} {{ __('pages.pending') }}</span>
                    @endif
                </div>
                <div class="checkin-map-search">
                    <input type="search" id="checkinMapSearch" placeholder="{{ __('pages.checkin_map_search') }}" autocomplete="off">
                </div>
                <div class="checkin-location-list" id="checkinLocationList">
                    @foreach($locations as $index => $loc)
                        @php
                            $statusClass = ($loc['status_key'] ?? '') === 'pause'
                                ? 'is-pause'
                                : (($loc['status_key'] ?? '') === 'review' ? 'is-review' : 'is-work');
                            $statusLabel = $loc['status'] ?? __('pages.pending');
                            $parts = preg_split('/\s+/', trim((string) $loc['name']));
                            $initials = '';
                            foreach (array_slice($parts, 0, 2) as $part) {
                                $initials .= mb_strtoupper(mb_substr($part, 0, 1));
                            }
                            $phoneRaw = trim((string) ($loc['phone'] ?? ''));
                            $tel = $phoneRaw !== '' ? preg_replace('/[^\d+]/', '', $phoneRaw) : '';
                            $employeeId = $loc['employee_id'] ?? null;
                            $isWorker = ($loc['role'] ?? '') === 'employee';
                        @endphp
                        <div
                            class="checkin-location-item"
                            data-map-index="{{ $index }}"
                            data-search="{{ strtolower(($loc['name'] ?? '') . ' ' . ($loc['position'] ?? '') . ' ' . ($loc['project'] ?? '')) }}"
                            role="button"
                            tabindex="0"
                        >
                            <div class="d-flex align-items-start gap-2">
                                <span class="checkin-map-avatar {{ $statusClass }}">{{ $initials }}</span>
                                <div class="min-w-0 flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start gap-2">
                                        <span class="fw-semibold txt-darkblue">{{ $loc['name'] }}</span>
                                        <span class="checkin-map-status {{ $statusClass }}">
                                            <span class="checkin-map-status-dot"></span>
                                            {{ $statusLabel }}
                                        </span>
                                    </div>
                                    @if(!empty($loc['position']))
                                        <div class="small text-muted mt-1">
                                            <i class="fa-solid fa-briefcase me-1 opacity-75"></i>
                                            {{ __('pages.position') }}: {{ $loc['position'] }}
                                        </div>
                                    @endif
                                    <div class="small text-muted mt-1">
                                        <i class="fa-solid fa-diagram-project me-1 opacity-75"></i>
                                        {{ $loc['project'] ?: '-' }}
                                    </div>
                                    <div class="small text-muted">
                                        <i class="fa-solid fa-clock me-1 opacity-75"></i>
                                        {{ $loc['check_in_time'] }}
                                    </div>
                                    <span class="checkin-map-together d-none" data-together-label></span>
                                    <div class="checkin-map-actions">
                                        @if($tel)
                                            <a class="checkin-map-action" href="tel:{{ $tel }}" data-map-action>
                                                <i class="fa-solid fa-phone"></i> {{ __('pages.checkin_map_call') }}
                                            </a>
                                        @else
                                            <span class="checkin-map-action is-disabled" title="{{ __('pages.checkin_map_no_phone') }}">
                                                <i class="fa-solid fa-phone"></i> {{ __('pages.checkin_map_call') }}
                                            </span>
                                        @endif
                                        @if($canAssignTask && $employeeId && $isWorker)
                                            <a class="checkin-map-action" href="{{ route('tasks', ['assigned_to' => $employeeId]) }}#create-task" data-map-action>
                                                <i class="fa-solid fa-list-check"></i> {{ __('pages.checkin_map_assign_task') }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div id="checkinMapNoMatch" class="d-none text-center text-muted small py-3 px-3">{{ __('pages.checkin_map_no_match') }}</div>
            </aside>
        </div>
    @endif
@endsection

@if(count($locations) > 0)
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
(function () {
    const locations = @json($locations);
    const i18n = {
        project: @json(__('pages.checkin_popup_project')),
        position: @json(__('pages.position')),
        status: @json(__('pages.checkin_popup_status')),
        checkin: @json(__('pages.checkin_entry')),
        maps: @json(__('pages.checkin_open_maps')),
        together: @json(__('pages.checkin_map_together'))
    };

    function esc(value) {
        return String(value || '').replace(/[&<>"']/g, function (c) {
            return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[c];
        });
    }

    function initials(name) {
        return String(name || '')
            .trim()
            .split(/\s+/)
            .filter(Boolean)
            .slice(0, 2)
            .map(function (part) { return part.charAt(0).toUpperCase(); })
            .join('');
    }

    function placeKey(p) {
        return Number(p.lat).toFixed(5) + ',' + Number(p.lng).toFixed(5);
    }

    var groups = {};
    locations.forEach(function (p, i) {
        var key = placeKey(p);
        if (!groups[key]) groups[key] = [];
        groups[key].push(i);
    });

    var togetherTpl = i18n.together;
    document.querySelectorAll('.checkin-location-item').forEach(function (el) {
        var index = parseInt(el.getAttribute('data-map-index'), 10);
        var key = placeKey(locations[index]);
        var count = groups[key].length;
        var label = el.querySelector('[data-together-label]');
        if (count > 1 && label) {
            label.textContent = togetherTpl.replace(':count', String(count));
            label.classList.remove('d-none');
        }
    });

    var map = L.map('checkin-team-map', { zoomControl: true }).setView([locations[0].lat, locations[0].lng], 13);
    L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        maxZoom: 19,
        attribution: 'Tiles &copy; Esri'
    }).addTo(map);
    L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}', {
        maxZoom: 19,
        pane: 'overlayPane'
    }).addTo(map);

    function statusClass(p) {
        return p.status_key === 'pause' ? 'is-pause' : (p.status_key === 'review' ? 'is-review' : 'is-work');
    }

    function personPopup(p) {
        var mapsQuery = p.lat + ',' + p.lng;
        var html = ''
            + '<div class="checkin-popup-name">' + esc(p.name || '') + '</div>';
        if (p.position) {
            html += '<div class="checkin-popup-meta">' + esc(i18n.position) + ': ' + esc(p.position) + '</div>';
        }
        html += ''
            + '<div class="checkin-popup-meta">' + esc(i18n.project) + ': ' + esc(p.project || '-') + '</div>'
            + '<div class="checkin-popup-meta">' + esc(i18n.status) + ': ' + esc(p.status || '-') + '</div>'
            + '<div class="checkin-popup-meta">' + esc(i18n.checkin) + ': ' + esc(p.check_in_time || '-') + '</div>'
            + '<a class="checkin-popup-link txt-lightblue" href="https://www.google.com/maps?q=' + encodeURIComponent(mapsQuery) + '" target="_blank" rel="noopener noreferrer">' + esc(i18n.maps) + '</a>';
        return html;
    }

    function stackPopup(idxs) {
        var html = '<div class="checkin-popup-name">' + esc(togetherTpl.replace(':count', String(idxs.length))) + '</div>';
        idxs.forEach(function (index) {
            var p = locations[index];
            html += '<div class="checkin-popup-person"><span class="checkin-marker ' + statusClass(p) + '">' + esc(initials(p.name)) + '</span><span>' + esc(p.name || '') + ' · ' + esc(p.status || '') + '</span></div>';
        });
        return html;
    }

    var markers = [];
    var bounds = [];

    Object.keys(groups).forEach(function (key) {
        var idxs = groups[key];
        var origin = locations[idxs[0]];
        var originLatLng = L.latLng(origin.lat, origin.lng);
        var iconHtml;
        var iconSize;
        var iconAnchor;
        var popupHtml;

        if (idxs.length > 1) {
            var faces = idxs.slice(0, 2).map(function (index) {
                var p = locations[index];
                return '<span class="checkin-marker ' + statusClass(p) + '">' + esc(initials(p.name)) + '</span>';
            }).join('');
            iconHtml = '<div class="checkin-stack">' + faces + '<span class="checkin-stack-badge">' + idxs.length + '</span></div>';
            iconSize = [52, 38];
            iconAnchor = [26, 19];
            popupHtml = stackPopup(idxs);
        } else {
            var p = locations[idxs[0]];
            iconHtml = '<span class="checkin-marker ' + statusClass(p) + '">' + esc(initials(p.name)) + '</span>';
            iconSize = [36, 36];
            iconAnchor = [18, 18];
            popupHtml = personPopup(p);
        }

        var marker = L.marker(originLatLng, {
            icon: L.divIcon({
                className: 'checkin-marker-wrap',
                html: iconHtml,
                iconSize: iconSize,
                iconAnchor: iconAnchor,
                popupAnchor: [0, -18]
            }),
            zIndexOffset: idxs.length > 1 ? 250 : 0
        }).addTo(map).bindPopup(popupHtml, { className: 'checkin-popup' });

        idxs.forEach(function (index) {
            markers[index] = marker;
        });
        bounds.push([origin.lat, origin.lng]);
    });

    if (bounds.length > 1) {
        map.fitBounds(bounds, { padding: [56, 48] });
    }
    setTimeout(function () { map.invalidateSize(); }, 150);

    function focusLocation(index) {
        var marker = markers[index];
        if (!marker) return;
        var ll = marker.getLatLng();
        map.setView(ll, Math.max(map.getZoom(), 17), { animate: true });
        marker.openPopup();
        document.querySelectorAll('.checkin-marker').forEach(function (el) { el.classList.remove('is-active'); });
        var iconEl = marker.getElement() && marker.getElement().querySelector('.checkin-marker');
        if (iconEl) iconEl.classList.add('is-active');
        document.querySelectorAll('.checkin-location-item').forEach(function (el) {
            el.classList.toggle('is-active', parseInt(el.getAttribute('data-map-index'), 10) === index);
        });
        var activeItem = document.querySelector('.checkin-location-item[data-map-index="' + index + '"]');
        if (activeItem) activeItem.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
    }

    document.querySelectorAll('.checkin-location-item').forEach(function (el) {
        el.addEventListener('click', function (event) {
            if (event.target.closest('[data-map-action]')) return;
            focusLocation(parseInt(el.getAttribute('data-map-index'), 10));
        });
        el.addEventListener('keydown', function (event) {
            if (event.key !== 'Enter' && event.key !== ' ') return;
            if (event.target.closest('[data-map-action]')) return;
            event.preventDefault();
            focusLocation(parseInt(el.getAttribute('data-map-index'), 10));
        });
    });

    var search = document.getElementById('checkinMapSearch');
    var noMatch = document.getElementById('checkinMapNoMatch');
    if (search) {
        search.addEventListener('input', function () {
            var q = this.value.trim().toLowerCase();
            var shown = 0;
            document.querySelectorAll('.checkin-location-item').forEach(function (el) {
                var match = !q || (el.getAttribute('data-search') || '').indexOf(q) !== -1;
                el.classList.toggle('d-none', !match);
                if (match) shown++;
            });
            if (noMatch) noMatch.classList.toggle('d-none', shown > 0);
        });
    }
})();
</script>
@endpush
@endif
