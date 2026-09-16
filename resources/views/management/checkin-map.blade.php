@extends('template.template')

@section('title', __('pages.checkin_map_title'))

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
<style>
    .checkin-map-page {
        margin: -1.5rem;
        min-height: calc(100vh - 4.5rem);
        display: flex;
        flex-direction: column;
        background:
            radial-gradient(1200px 420px at 8% -10%, rgba(37, 99, 235, 0.08), transparent 55%),
            radial-gradient(900px 380px at 100% 0%, rgba(16, 185, 129, 0.08), transparent 50%),
            #F4F7FB;
    }

    .checkin-map-topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 0.9rem 1.15rem;
        background: rgba(255, 255, 255, 0.78);
        backdrop-filter: blur(18px);
        border-bottom: 1px solid rgba(15, 23, 42, 0.06);
        z-index: 2;
    }

    .checkin-map-topbar-left {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        min-width: 0;
    }

    .checkin-map-back {
        width: 2.35rem;
        height: 2.35rem;
        border-radius: 0.85rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #0F172A;
        color: #fff;
        text-decoration: none;
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.18);
        flex-shrink: 0;
    }
    .checkin-map-back:hover { color: #fff; background: #1E293B; }

    .checkin-map-kicker {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.68rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #2563EB;
        margin-bottom: 0.15rem;
    }
    .checkin-map-kicker-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #10B981;
        box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.18);
        animation: checkin-live 1.6s ease-in-out infinite;
    }
    @keyframes checkin-live {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(0.75); opacity: 0.55; }
    }

    .checkin-map-title {
        margin: 0;
        font-size: 1.15rem;
        font-weight: 800;
        color: #0F172A;
        letter-spacing: -0.02em;
        line-height: 1.2;
    }
    .checkin-map-lead {
        margin: 0.15rem 0 0;
        font-size: 0.8rem;
        color: #64748B;
        font-weight: 500;
    }

    .checkin-map-stage {
        position: relative;
        flex: 1;
        min-height: 520px;
        overflow: hidden;
    }

    #checkin-team-map {
        height: 100%;
        width: 100%;
        min-height: 520px;
    }

    .leaflet-control-zoom {
        border: 0 !important;
        overflow: hidden;
        border-radius: 0.9rem !important;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.14) !important;
        margin: 16px !important;
    }
    .leaflet-control-zoom a {
        width: 36px !important;
        height: 36px !important;
        line-height: 36px !important;
        color: #0F172A !important;
        border-bottom: 1px solid #EEF2F6 !important;
        font-size: 16px !important;
    }
    .leaflet-control-zoom a:last-child { border-bottom: 0 !important; }
    .leaflet-control-attribution {
        background: rgba(255,255,255,0.72) !important;
        backdrop-filter: blur(8px);
        border-radius: 0.5rem 0 0 0;
        font-size: 10px !important;
    }

    .checkin-map-overlay {
        position: absolute;
        top: 1rem;
        right: 1rem;
        width: min(23rem, calc(100% - 2rem));
        max-height: calc(100% - 2rem);
        display: flex;
        flex-direction: column;
        background: rgba(255, 255, 255, 0.78);
        backdrop-filter: blur(22px) saturate(1.3);
        border: 1px solid rgba(255, 255, 255, 0.7);
        border-radius: 1.35rem;
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.16);
        z-index: 1000;
        overflow: hidden;
    }

    .checkin-map-stats {
        display: flex;
        gap: 0.4rem;
        padding: 0.9rem 0.9rem 0.4rem;
        flex-wrap: wrap;
    }

    .checkin-map-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        border-radius: 999px;
        padding: 0.32rem 0.7rem;
        font-size: 0.7rem;
        font-weight: 800;
        line-height: 1.2;
        border: 1px solid transparent;
    }

    .checkin-map-chip.is-work { background: rgba(16, 185, 129, 0.12); color: #047857; }
    .checkin-map-chip.is-pause { background: rgba(245, 158, 11, 0.14); color: #b45309; }
    .checkin-map-chip.is-review { background: rgba(100, 116, 139, 0.12); color: #475569; }
    .checkin-map-chip.is-project { background: rgba(37, 99, 235, 0.12); color: #1D4ED8; }

    .checkin-map-search {
        padding: 0.35rem 0.9rem 0.75rem;
        position: relative;
    }
    .checkin-map-search i {
        position: absolute;
        left: 1.45rem;
        top: 50%;
        transform: translateY(-50%);
        color: #94A3B8;
        font-size: 0.78rem;
        pointer-events: none;
    }

    .checkin-map-search input {
        width: 100%;
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: 0.9rem;
        padding: 0.58rem 0.8rem 0.58rem 2.1rem;
        font-size: 0.82rem;
        outline: none;
        background: rgba(248, 250, 252, 0.92);
        color: #0F172A;
    }

    .checkin-map-search input:focus {
        border-color: rgba(37, 99, 235, 0.45);
        background: #fff;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
    }

    .checkin-map-section {
        font-size: 0.65rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #94A3B8;
        padding: 0.15rem 0.35rem 0.45rem;
    }

    .checkin-location-list {
        overflow-y: auto;
        overscroll-behavior: contain;
        flex: 1;
        min-height: 0;
        padding: 0 0.65rem 0.75rem;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .checkin-location-item {
        display: block;
        width: 100%;
        text-align: left;
        border: 1px solid rgba(15, 23, 42, 0.05);
        border-radius: 1rem;
        background: rgba(255, 255, 255, 0.72);
        padding: 0.8rem 0.85rem;
        transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
        cursor: pointer;
    }

    .checkin-location-item:hover {
        transform: translateY(-1px);
        border-color: rgba(37, 99, 235, 0.18);
        box-shadow: 0 10px 22px rgba(15, 23, 42, 0.08);
    }
    .checkin-location-item.is-active {
        background: #fff;
        border-color: rgba(37, 99, 235, 0.35);
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
    }
    .checkin-map-item-head {
        display: flex;
        align-items: center;
        gap: 0.55rem;
        width: 100%;
    }
    .checkin-map-item-head .checkin-map-item-name {
        flex: 1;
        min-width: 0;
        font-weight: 600;
        color: #071838;
        line-height: 1.25;
    }
    .checkin-map-chevron {
        color: #94A3B8;
        font-size: 0.72rem;
        transition: transform 0.18s ease;
        flex-shrink: 0;
    }
    .checkin-location-item.is-open .checkin-map-chevron {
        transform: rotate(180deg);
        color: #2563EB;
    }
    .checkin-map-item-body {
        display: none;
        padding-top: 0.65rem;
        margin-top: 0.65rem;
        margin-left: 2.55rem;
        border-top: 1px solid rgba(15, 23, 42, 0.06);
    }
    .checkin-location-item.is-open .checkin-map-item-body {
        display: block;
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
    .checkin-map-avatar.is-project {
        background: #2563EB;
        border-radius: 0.65rem;
    }
    .checkin-marker.is-project {
        width: 32px;
        height: 32px;
        background: #2563EB;
        border-radius: 50% 50% 50% 0;
        transform: rotate(-45deg);
        box-shadow: 0 6px 14px rgba(37, 99, 235, 0.4);
    }
    .checkin-marker.is-project i {
        transform: rotate(45deg);
        font-size: 12px;
    }

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

    .leaflet-tooltip.checkin-distance-label {
        background: #fff;
        border: 1px solid #2563EB;
        border-radius: 999px;
        color: #1D4ED8;
        font-weight: 700;
        font-size: 11px;
        padding: 2px 8px;
        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.16);
        white-space: nowrap;
    }
    .leaflet-tooltip.checkin-distance-label::before {
        display: none;
    }

    .checkin-map-together.is-onsite {
        color: #047857;
        background: rgba(16, 185, 129, 0.14);
    }

    .checkin-onsite {
        position: relative;
        width: 58px;
        height: 58px;
    }
    .checkin-onsite-pulse {
        position: absolute;
        inset: 4px;
        border-radius: 50%;
        border: 3px solid rgba(16, 185, 129, 0.95);
        box-shadow: 0 0 0 5px rgba(37, 99, 235, 0.22);
        animation: checkin-onsite-pulse 1.8s ease-out infinite;
    }
    .checkin-onsite .checkin-marker.is-project {
        position: absolute;
        left: 2px;
        bottom: 6px;
        z-index: 1;
    }
    .checkin-onsite .checkin-marker:not(.is-project) {
        position: absolute;
        right: 0;
        top: 0;
        z-index: 2;
        width: 30px;
        height: 30px;
        font-size: 9px;
    }
    .checkin-onsite-badge {
        position: absolute;
        left: 50%;
        bottom: -2px;
        transform: translateX(-50%);
        background: #059669;
        color: #fff;
        font-size: 9px;
        font-weight: 800;
        letter-spacing: 0.01em;
        padding: 1px 6px;
        border-radius: 999px;
        border: 2px solid #fff;
        white-space: nowrap;
        z-index: 4;
        box-shadow: 0 4px 10px rgba(5, 150, 105, 0.28);
    }
    @keyframes checkin-onsite-pulse {
        0% { transform: scale(0.92); opacity: 1; }
        100% { transform: scale(1.18); opacity: 0; }
    }

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
        border-radius: 1rem;
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.18);
        border: 1px solid rgba(15, 23, 42, 0.05);
    }
    .checkin-popup .leaflet-popup-content { margin: 0.9rem 1rem; }
    .checkin-popup-name { font-weight: 800; color: #0F172A; letter-spacing: -0.01em; }
    .checkin-popup-meta { font-size: 0.78rem; color: #64748b; margin-top: 0.28rem; }
    .checkin-popup-link { display: inline-block; margin-top: 0.5rem; font-size: 0.78rem; font-weight: 750; }

    .leaflet-container { font-family: inherit; }

    .checkin-map-empty {
        margin: 1.25rem;
        border-radius: 1.4rem;
        background: rgba(255,255,255,0.8);
        border: 1px dashed rgba(15, 23, 42, 0.12);
        text-align: center;
        padding: 4.5rem 1.5rem;
        color: #64748B;
    }
    .checkin-map-empty i {
        width: 3.2rem;
        height: 3.2rem;
        border-radius: 1rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(37, 99, 235, 0.1);
        color: #2563EB;
        margin-bottom: 0.9rem;
    }

    @media (max-width: 991.98px) {
        .checkin-map-page { min-height: calc(100vh - 5.5rem); }
        .checkin-map-topbar { padding: 0.8rem 0.9rem; }
        .checkin-map-lead { display: none; }
        .checkin-map-stage {
            min-height: 0;
            display: flex;
            flex-direction: column;
            overflow: visible;
        }
        #checkin-team-map {
            height: 52vh;
            min-height: 280px;
        }
        .checkin-map-overlay {
            position: relative;
            top: auto;
            right: auto;
            width: 100%;
            max-height: none;
            border-radius: 1.25rem 1.25rem 0 0;
            box-shadow: 0 -12px 30px rgba(15, 23, 42, 0.08);
            border: 0;
            border-top: 1px solid rgba(15, 23, 42, 0.06);
        }
        .checkin-location-list { max-height: 38vh; }
    }
</style>
@endpush

@section('content')
    @php
        $projects = $projects ?? [];
        $workCount = collect($locations)->where('status_key', 'work')->count();
        $pauseCount = collect($locations)->where('status_key', 'pause')->count();
        $reviewCount = collect($locations)->where('status_key', 'review')->count();
        $projectCount = count($projects);
        $canAssignTask = in_array(session('role'), ['company', 'manager'], true);
        $hasMapData = count($locations) > 0 || $projectCount > 0;
    @endphp

    <div class="checkin-map-page">
        <div class="checkin-map-topbar">
            <div class="checkin-map-topbar-left">
                <a href="{{ route('dashboard') }}" class="checkin-map-back" title="{{ __('pages.back') }}">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div class="min-w-0">
                    <div class="checkin-map-kicker">
                        <span class="checkin-map-kicker-dot"></span>
                        Live
                    </div>
                    <h3 class="checkin-map-title">{{ __('pages.checkin_map_heading') }}</h3>
                    <p class="checkin-map-lead">{{ __('pages.checkin_map_lead') }}</p>
                </div>
            </div>
        </div>

    @if(!$hasMapData)
        <div class="checkin-map-empty">
            <i class="fa-solid fa-location-dot"></i>
            <p class="mb-0 fw-semibold">{{ __('pages.checkin_map_empty') }}</p>
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
                    @if($projectCount > 0)
                        <span class="checkin-map-chip is-project">{{ $projectCount }} {{ __('pages.checkin_map_projects') }}</span>
                    @endif
                </div>
                <div class="checkin-map-search">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="search" id="checkinMapSearch" placeholder="{{ __('pages.checkin_map_search') }}" autocomplete="off">
                </div>
                <div class="checkin-location-list" id="checkinLocationList">
                    @if(count($locations) > 0)
                        <div class="checkin-map-section">{{ __('pages.checkin_active_employees') }}</div>
                    @endif
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
                            data-map-kind="worker"
                            data-map-index="{{ $index }}"
                            data-search="{{ strtolower(($loc['name'] ?? '') . ' ' . ($loc['position'] ?? '') . ' ' . ($loc['project'] ?? '') . ' ' . collect($loc['project_links'] ?? [])->pluck('name')->implode(' ')) }}"
                            role="button"
                            tabindex="0"
                            aria-expanded="false"
                        >
                            <div class="checkin-map-item-head">
                                <span class="checkin-map-avatar {{ $statusClass }}">{{ $initials }}</span>
                                <span class="checkin-map-item-name">{{ $loc['name'] }}</span>
                                <i class="fa-solid fa-chevron-down checkin-map-chevron"></i>
                            </div>
                            <div class="checkin-map-item-body">
                                <span class="checkin-map-status {{ $statusClass }}">
                                    <span class="checkin-map-status-dot"></span>
                                    {{ $statusLabel }}
                                </span>
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
                                @php
                                    $onsiteLinks = collect($loc['project_links'] ?? [])->where('on_site', true);
                                    $remoteLinks = collect($loc['project_links'] ?? [])->where('on_site', false);
                                @endphp
                                @if($onsiteLinks->isNotEmpty())
                                    <span class="checkin-map-together is-onsite">{{ __('pages.checkin_map_onsite') }}</span>
                                @endif
                                @foreach($remoteLinks as $link)
                                    <div class="small text-muted">
                                        <i class="fa-solid fa-ruler-horizontal me-1 opacity-75"></i>
                                        {{ __('pages.checkin_map_distance') }} · {{ $link['name'] }}: {{ $link['distance'] }}
                                    </div>
                                @endforeach
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
                    @endforeach
                    @if($projectCount > 0)
                        <div class="checkin-map-section">{{ __('pages.checkin_map_projects') }}</div>
                    @endif
                    @foreach($projects as $index => $project)
                        <div
                            class="checkin-location-item"
                            data-map-kind="project"
                            data-map-index="{{ $index }}"
                            data-search="{{ strtolower(($project['name'] ?? '') . ' ' . ($project['client_name'] ?? '') . ' ' . ($project['manager_name'] ?? '') . ' ' . ($project['location_label'] ?? '')) }}"
                            role="button"
                            tabindex="0"
                            aria-expanded="false"
                        >
                            <div class="checkin-map-item-head">
                                <span class="checkin-map-avatar is-project"><i class="fa-solid fa-building"></i></span>
                                <span class="checkin-map-item-name">{{ $project['name'] }}</span>
                                <i class="fa-solid fa-chevron-down checkin-map-chevron"></i>
                            </div>
                            <div class="checkin-map-item-body">
                                <span class="checkin-map-status is-review">{{ $project['status_label'] }}</span>
                                <div class="small text-muted mt-1">
                                    <i class="fa-solid fa-user-tie me-1 opacity-75"></i>
                                    {{ __('pages.checkin_popup_manager') }}: {{ $project['manager_name'] ?? '-' }}
                                </div>
                                <div class="small text-muted">
                                    <i class="fa-solid fa-building-user me-1 opacity-75"></i>
                                    {{ __('pages.checkin_popup_client') }}: {{ $project['client_name'] ?? '-' }}
                                </div>
                                <div class="small text-muted">
                                    <i class="fa-solid fa-location-dot me-1 opacity-75"></i>
                                    {{ $project['location_label'] }}
                                </div>
                                <div class="checkin-map-actions">
                                    @if(!empty($project['details_url']))
                                        <a class="checkin-map-action" href="{{ $project['details_url'] }}" data-map-action>
                                            <i class="fa-solid fa-eye"></i> {{ __('pages.checkin_open_project') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div id="checkinMapNoMatch" class="d-none text-center text-muted small py-3 px-3">{{ __('pages.checkin_map_no_match') }}</div>
            </aside>
        </div>
    @endif
    </div>
@endsection

@if(count($locations) > 0 || count($projects ?? []) > 0)
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
(function () {
    const locations = @json($locations);
    const projects = @json($projects);
    const i18n = {
        project: @json(__('pages.checkin_popup_project')),
        position: @json(__('pages.position')),
        status: @json(__('pages.checkin_popup_status')),
        checkin: @json(__('pages.checkin_entry')),
        maps: @json(__('pages.checkin_open_maps')),
        together: @json(__('pages.checkin_map_together')),
        client: @json(__('pages.checkin_popup_client')),
        manager: @json(__('pages.checkin_popup_manager')),
        budget: @json(__('pages.checkin_popup_budget')),
        start: @json(__('pages.checkin_popup_start')),
        end: @json(__('pages.checkin_popup_end')),
        openProject: @json(__('pages.checkin_open_project')),
        location: @json(__('pages.location')),
        distance: @json(__('pages.checkin_map_distance')),
        onsite: @json(__('pages.checkin_map_onsite')),
        onsiteShort: @json(__('pages.checkin_map_onsite_short'))
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
    document.querySelectorAll('.checkin-location-item[data-map-kind="worker"]').forEach(function (el) {
        var index = parseInt(el.getAttribute('data-map-index'), 10);
        var loc = locations[index];
        if (!loc) return;
        var key = placeKey(loc);
        var count = groups[key] ? groups[key].length : 1;
        var label = el.querySelector('[data-together-label]');
        if (count > 1 && label) {
            label.textContent = togetherTpl.replace(':count', String(count));
            label.classList.remove('d-none');
        }
    });

    var startPoint = locations[0] || projects[0] || { lat: 42.6629, lng: 21.1655 };
    var map = L.map('checkin-team-map', { zoomControl: true }).setView([startPoint.lat, startPoint.lng], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    map.createPane('project-links');
    map.getPane('project-links').style.zIndex = 350;

    locations.forEach(function (worker) {
        (worker.project_links || []).forEach(function (link) {
            if (link.on_site) return;
            L.polyline(
                [[worker.lat, worker.lng], [link.lat, link.lng]],
                {
                    pane: 'project-links',
                    color: '#2563EB',
                    weight: 2.5,
                    opacity: 0.75,
                    dashArray: '7 6'
                }
            ).addTo(map).bindTooltip(esc(link.distance), {
                permanent: true,
                direction: 'center',
                className: 'checkin-distance-label'
            });
        });
    });

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
            + '<div class="checkin-popup-meta">' + esc(i18n.project) + ': ' + esc(p.project || '-') + '</div>';
        (p.project_links || []).forEach(function (link) {
            if (link.on_site) {
                html += '<div class="checkin-popup-meta">' + esc(i18n.onsite) + ' · ' + esc(link.name) + '</div>';
                return;
            }
            html += '<div class="checkin-popup-meta">' + esc(i18n.distance) + ' · ' + esc(link.name) + ': ' + esc(link.distance) + '</div>';
        });
        html += ''
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

    function projectPopup(p) {
        var html = ''
            + '<div class="checkin-popup-name">' + esc(p.name || '') + '</div>'
            + '<div class="checkin-popup-meta">' + esc(i18n.status) + ': ' + esc(p.status_label || '-') + '</div>'
            + '<div class="checkin-popup-meta">' + esc(i18n.manager) + ': ' + esc(p.manager_name || '-') + '</div>'
            + '<div class="checkin-popup-meta">' + esc(i18n.client) + ': ' + esc(p.client_name || '-') + '</div>'
            + '<div class="checkin-popup-meta">' + esc(i18n.location) + ': ' + esc(p.location_label || '-') + '</div>';
        if (p.details_url) {
            html += '<a class="checkin-popup-link txt-lightblue" href="' + esc(p.details_url) + '">' + esc(i18n.openProject) + '</a>';
        }
        return html;
    }

    function projectIconHtml() {
        return '<span class="checkin-marker is-project"><i class="fa-solid fa-building"></i></span>';
    }

    function workerOnSite(p) {
        return (p.project_links || []).some(function (link) { return !!link.on_site; });
    }

    function firstOnSiteProject(p) {
        return (p.project_links || []).find(function (link) { return link.on_site; }) || null;
    }

    function onsitePopup(idxs) {
        var origin = locations[idxs[0]];
        var link = firstOnSiteProject(origin);
        var project = link
            ? projects.find(function (row) { return Number(row.id) === Number(link.project_id); })
            : null;
        var html = '<div class="checkin-popup-name">' + esc(i18n.onsite) + '</div>';
        if (project) {
            html += '<div class="checkin-popup-meta">' + esc(i18n.project) + ': ' + esc(project.name || '-') + '</div>'
                + '<div class="checkin-popup-meta">' + esc(i18n.status) + ': ' + esc(project.status_label || '-') + '</div>'
                + '<div class="checkin-popup-meta">' + esc(i18n.manager) + ': ' + esc(project.manager_name || '-') + '</div>'
                + '<div class="checkin-popup-meta">' + esc(i18n.client) + ': ' + esc(project.client_name || '-') + '</div>'
                + '<div class="checkin-popup-meta">' + esc(i18n.location) + ': ' + esc(project.location_label || '-') + '</div>';
            if (project.details_url) {
                html += '<a class="checkin-popup-link txt-lightblue" href="' + esc(project.details_url) + '">' + esc(i18n.openProject) + '</a>';
            }
        }
        idxs.forEach(function (index) {
            var p = locations[index];
            html += '<div class="checkin-popup-person"><span class="checkin-marker ' + statusClass(p) + '">' + esc(initials(p.name)) + '</span><span>' + esc(p.name || '') + ' · ' + esc(p.status || '') + '</span></div>';
        });
        return html;
    }

    var onsiteProjectIds = {};
    locations.forEach(function (worker, index) {
        (worker.project_links || []).forEach(function (link) {
            if (!link.on_site) return;
            if (!onsiteProjectIds[link.project_id]) onsiteProjectIds[link.project_id] = [];
            onsiteProjectIds[link.project_id].push(index);
        });
    });

    var markers = [];
    var bounds = [];

    Object.keys(groups).forEach(function (key) {
        var idxs = groups[key];
        var origin = locations[idxs[0]];
        if (!origin) return;
        var originLatLng = L.latLng(origin.lat, origin.lng);
        var iconHtml;
        var iconSize;
        var iconAnchor;
        var popupHtml;
        var onsite = idxs.some(function (index) { return workerOnSite(locations[index]); });

        if (onsite) {
            var face = locations[idxs[0]];
            iconHtml = '<div class="checkin-onsite"><span class="checkin-onsite-pulse"></span>'
                + projectIconHtml()
                + '<span class="checkin-marker ' + statusClass(face) + '">' + esc(initials(face.name)) + '</span>'
                + '<span class="checkin-onsite-badge">' + esc(i18n.onsiteShort) + '</span></div>';
            iconSize = [58, 58];
            iconAnchor = [29, 34];
            popupHtml = onsitePopup(idxs);
        } else if (idxs.length > 1) {
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
            zIndexOffset: onsite ? 400 : (idxs.length > 1 ? 250 : 0)
        }).addTo(map).bindPopup(popupHtml, { className: 'checkin-popup' });

        idxs.forEach(function (index) {
            markers[index] = marker;
        });
        bounds.push([origin.lat, origin.lng]);
    });

    var projectMarkers = [];
    projects.forEach(function (project, index) {
        var onsiteWorkers = onsiteProjectIds[project.id];
        if (onsiteWorkers && onsiteWorkers.length && markers[onsiteWorkers[0]]) {
            projectMarkers[index] = markers[onsiteWorkers[0]];
            return;
        }
        var marker = L.marker([project.lat, project.lng], {
            icon: L.divIcon({
                className: 'checkin-marker-wrap',
                html: projectIconHtml(),
                iconSize: [36, 36],
                iconAnchor: [18, 18],
                popupAnchor: [0, -18]
            }),
            zIndexOffset: 100
        }).addTo(map).bindPopup(projectPopup(project), { className: 'checkin-popup' });
        projectMarkers[index] = marker;
        bounds.push([project.lat, project.lng]);
    });

    if (bounds.length > 1) {
        map.fitBounds(bounds, { padding: [56, 48] });
    }
    setTimeout(function () { map.invalidateSize(); }, 150);

    function focusLocation(kind, index) {
        var marker = kind === 'project' ? projectMarkers[index] : markers[index];
        if (!marker) return;
        var ll = marker.getLatLng();
        map.setView(ll, Math.max(map.getZoom(), 17), { animate: true });
        marker.openPopup();
        document.querySelectorAll('.checkin-marker').forEach(function (el) { el.classList.remove('is-active'); });
        var iconEl = marker.getElement() && marker.getElement().querySelector('.checkin-marker');
        if (iconEl) iconEl.classList.add('is-active');
        document.querySelectorAll('.checkin-location-item').forEach(function (el) {
            var sameKind = el.getAttribute('data-map-kind') === kind;
            var sameIndex = parseInt(el.getAttribute('data-map-index'), 10) === index;
            el.classList.toggle('is-active', sameKind && sameIndex);
        });
        var activeItem = document.querySelector('.checkin-location-item[data-map-kind="' + kind + '"][data-map-index="' + index + '"]');
        if (activeItem) activeItem.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
    }

    function toggleListItem(el) {
        var willOpen = !el.classList.contains('is-open');
        document.querySelectorAll('.checkin-location-item').forEach(function (item) {
            var open = willOpen && item === el;
            item.classList.toggle('is-open', open);
            item.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
        focusLocation(el.getAttribute('data-map-kind') || 'worker', parseInt(el.getAttribute('data-map-index'), 10));
    }

    document.querySelectorAll('.checkin-location-item').forEach(function (el) {
        el.addEventListener('click', function (event) {
            if (event.target.closest('[data-map-action]')) return;
            toggleListItem(el);
        });
        el.addEventListener('keydown', function (event) {
            if (event.key !== 'Enter' && event.key !== ' ') return;
            if (event.target.closest('[data-map-action]')) return;
            event.preventDefault();
            toggleListItem(el);
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
