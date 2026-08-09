@extends('template.template')

{{-- Page Title --}}
@section('title', 'Dashboard')

{{-- Page Content --}}
@section('content')
    <small class="d-block text-uppercase fw-semibold opacity-75" style="letter-spacing:.06em;font-size:.75rem;">Dashboard · {{ date('d/m/Y') }}</small>
    <h3 class="fw-semibold pt-2 mb-4">Mirë se erdhët, <span class="txt-lightblue">{{ session('user') ?? session('company') }}</span></h3>

    @if($canCheckIn)
        @php
            $fmt = function ($value) {
                return $value ? \Carbon\Carbon::parse($value)->format('H:i') : '--:--';
            };
            $onPause = $isActive && $checkIn && $checkIn->pause_start_time && !$checkIn->pause_end_time;
            $status = !$checkIn
                ? 'Jo aktiv'
                : (!$isActive ? 'Përfunduar' : ($onPause ? 'Në pauzë' : 'Në punë'));

            $mapLat = null;
            $mapLng = null;
            if (!empty($checkIn->check_in_location) && str_contains($checkIn->check_in_location, ',')) {
                [$mapLat, $mapLng] = array_map('trim', explode(',', $checkIn->check_in_location, 2));
                if (!is_numeric($mapLat) || !is_numeric($mapLng)) {
                    $mapLat = null;
                    $mapLng = null;
                }
            }
        @endphp

        <div class="row g-4">
            <div class="col-12 col-xl-5">
                <div class="checkin-panel h-100">
                    <div class="checkin-panel-head">
                        <div>
                            <span class="checkin-kicker">Evidenca e ditës</span>
                            <h5 class="fw-semibold mb-0">Check-in</h5>
                        </div>
                        <span class="checkin-status {{ !$isActive ? 'is-idle' : ($onPause ? 'is-pause' : 'is-work') }}">{{ $status }}</span>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-6 col-md-3 col-xl-6">
                            <div class="checkin-stat">
                                <small>Check-in</small>
                                <strong>{{ $fmt($checkIn->check_in_time ?? null) }}</strong>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 col-xl-6">
                            <div class="checkin-stat">
                                <small>Fillimi i pauzës</small>
                                <strong>{{ $fmt($checkIn->pause_start_time ?? null) }}</strong>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 col-xl-6">
                            <div class="checkin-stat">
                                <small>Mbarimi i pauzës</small>
                                <strong>{{ $fmt($checkIn->pause_end_time ?? null) }}</strong>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 col-xl-6">
                            <div class="checkin-stat">
                                <small>Check-out</small>
                                <strong>{{ $fmt($checkIn->check_out_time ?? null) }}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="checkin-actions">
                        @if(!$isActive)
                            <form action="{{ route('start.work') }}" method="post" id="startWorkForm" class="w-100">
                                @csrf
                                <input type="hidden" name="check_in_location" id="check_in_location">
                                <input type="hidden" name="check_in_time" id="check_in_time">
                                <button type="submit" class="btn btn-darkblue w-100 rounded-3 py-3 fw-semibold">
                                    <i class="fa-solid fa-play me-2"></i> Starto Punën
                                </button>
                            </form>
                        @else
                            @if(!$checkIn->pause_start_time)
                                <form action="{{ route('start.break') }}" method="post" class="flex-fill">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $checkIn->id }}">
                                    <input type="hidden" name="pause_start_time" value="{{ now()->format('Y-m-d H:i:s') }}">
                                    <button type="submit" class="btn btn-outline-secondary w-100 rounded-3 py-3 fw-semibold">
                                        <i class="fa-solid fa-mug-saucer me-2"></i> Starto Pauzën
                                    </button>
                                </form>
                            @elseif(!$checkIn->pause_end_time)
                                <form action="{{ route('end.break') }}" method="post" class="flex-fill">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $checkIn->id }}">
                                    <input type="hidden" name="pause_end_time" value="{{ now()->format('Y-m-d H:i:s') }}">
                                    <button type="submit" class="btn btn-outline-secondary w-100 rounded-3 py-3 fw-semibold">
                                        <i class="fa-solid fa-play me-2"></i> Përfundo Pauzën
                                    </button>
                                </form>
                            @endif

                            @if(!$checkIn->pause_start_time || $checkIn->pause_end_time)
                                <form action="{{ route('end.work') }}" method="post" class="flex-fill">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $checkIn->id }}">
                                    <input type="hidden" name="check_out_time" value="{{ now()->format('Y-m-d H:i:s') }}">
                                    <button type="submit" class="btn btn-darkblue w-100 rounded-3 py-3 fw-semibold">
                                        <i class="fa-solid fa-stop me-2"></i> Përfundo Punën
                                    </button>
                                </form>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-7">
                <div class="checkin-panel h-100">
                    <div class="checkin-panel-head">
                        <div>
                            <span class="checkin-kicker">Lokacioni</span>
                            <h5 class="fw-semibold mb-0">Harta</h5>
                        </div>
                        @if($mapLat !== null)
                            <a class="small text-decoration-none"
                               href="https://www.google.com/maps?q={{ $mapLat }},{{ $mapLng }}"
                               target="_blank"
                               rel="noopener noreferrer">
                                Hap në Maps <i class="fa-solid fa-arrow-up-right-from-square ms-1 opacity-50"></i>
                            </a>
                        @endif
                    </div>

                    <div class="checkin-map">
                        @if($mapLat !== null)
                            <iframe
                                title="Check-in map"
                                class="checkin-map-iframe"
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"
                                src="https://www.google.com/maps?q={{ $mapLat }},{{ $mapLng }}&z=15&output=embed">
                            </iframe>
                        @else
                            <div class="checkin-map-empty">
                                <i class="fa-solid fa-location-dot mb-2 opacity-50"></i>
                                <p class="mb-0 text-muted">Lokacioni shfaqet pasi të fillosh check-in.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
@if($canCheckIn && empty($isActive))
<script>
(function () {
    const form = document.getElementById('startWorkForm');
    if (!form) return;

    function pad(n) {
        return String(n).padStart(2, '0');
    }

    function nowString() {
        const d = new Date();
        return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate()) + ' ' +
            pad(d.getHours()) + ':' + pad(d.getMinutes()) + ':' + pad(d.getSeconds());
    }

    form.addEventListener('submit', function (e) {
        if (form.dataset.ready === '1') return;
        e.preventDefault();

        if (!navigator.geolocation) {
            alert('Ky shfletues nuk e mbështet marrjen e lokacionit.');
            return;
        }

        const button = form.querySelector('button');
        const original = button.innerHTML;
        button.disabled = true;
        button.innerHTML = 'Duke marrë lokacionin...';

        navigator.geolocation.getCurrentPosition(
            function (pos) {
                document.getElementById('check_in_location').value = pos.coords.latitude + ',' + pos.coords.longitude;
                document.getElementById('check_in_time').value = nowString();
                form.dataset.ready = '1';
                form.submit();
            },
            function () {
                button.disabled = false;
                button.innerHTML = original;
                alert('Duhet të lejoni qasjen në lokacion.');
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    });
})();
</script>
@endif
@endpush
