@extends('template.template')

{{-- Page Title --}}
@section('title', 'Dashboard')

{{-- Page Content --}}
@section('content')
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4" style="background: linear-gradient(135deg, rgba(13,110,253,0.09), rgba(13,110,253,0.02)); border-radius: 1rem;">
            <small class="d-block txt-lightblue fw-semibold text-uppercase">Dashboard · {{ date('d/m/Y') }}</small>
            <h3 class="fw-semibold pt-2 mb-1">Mirë se erdhët, <span class="txt-lightblue fw-semibold">{{ session('user') ?? session('company') }}</span>.</h3>
        </div>
    </div>

    {{-- Checkin and Checkout Activity --}}
    <div class="row g-3">
        @if(!$checkIn)
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card h-100 border-0 shadow-sm overflow-hidden">
                    <div class="card-body text-center d-flex flex-column justify-content-center py-5"
                        style="background:linear-gradient(180deg,rgba(13,110,253,.08),rgba(13,110,253,.02));">
                        <form action="{{ route('start.work') }}" method="post" id="startWorkForm">
                            @csrf
                            <input type="hidden" name="check_in_location" id="check_in_location">
                            <input type="hidden" name="check_in_time" id="check_in_time">
                            <button type="submit"
                                    class="text-dark mx-auto d-flex align-items-center justify-content-center rounded-circle bg-white shadow-sm border-0"
                                    style="width:120px;height:120px;">
                                <strong>Starto Punën</strong>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        @if($checkIn)
            @if($checkIn -> check_in_time && !$checkIn -> pause_start_time)
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm overflow-hidden">
                        <div class="card-body text-center d-flex flex-column justify-content-center py-5"
                            style="background:linear-gradient(180deg,rgba(13,110,253,.08),rgba(13,110,253,.02));">
                            <form action="{{ route('start.break') }}" method="POST" id="startBreakForm">
                                @csrf
                                <input type="hidden" name="id" id="id" value="{{ $checkIn -> id }}">
                                <input type="hidden" name="pause_start_location" id="pause_start_location">
                                <input type="hidden" name="pause_start_time" id="pause_start_time">
                                <button type="submit"
                                        class="text-dark mx-auto d-flex align-items-center justify-content-center rounded-circle bg-white shadow-sm border-0"
                                        style="width:120px;height:120px;">
                                    <strong>Starto Pauzën</strong>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

            @if($checkIn -> check_in_time && $checkIn -> pause_start_time && !$checkIn -> pause_end_time)
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm overflow-hidden">
                        <div class="card-body text-center d-flex flex-column justify-content-center py-5"
                            style="background:linear-gradient(180deg,rgba(13,110,253,.08),rgba(13,110,253,.02));">
                            <form action="{{ route('end.break') }}" method="POST" id="endBreakForm">
                                @csrf
                                <input type="hidden" name="id" id="id" value="{{ $checkIn -> id }}">
                                <input type="hidden" name="pause_end_location" id="pause_end_location">
                                <input type="hidden" name="pause_end_time" id="pause_end_time">
                                <button type="submit"
                                        class="text-dark mx-auto d-flex align-items-center justify-content-center rounded-circle bg-white shadow-sm border-0"
                                        style="width:120px;height:120px;">
                                    <strong>Përfundo Pauzën</strong>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

            @if($checkIn -> check_in_time && $checkIn -> pause_end_time && !$checkIn -> check_out_time)
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm overflow-hidden">
                        <div class="card-body text-center d-flex flex-column justify-content-center py-5"
                            style="background:linear-gradient(180deg,rgba(13,110,253,.08),rgba(13,110,253,.02));">
                            <form action="{{ route('end.work') }}" method="POST" id="endWorkForm">
                                @csrf
                                <input type="hidden" name="id" id="id" value="{{ $checkIn -> id }}">
                                <input type="hidden" name="check_out_location" id="check_out_location">
                                <input type="hidden" name="check_out_time" id="check_out_time">
                                <button type="submit"
                                        class="text-dark mx-auto d-flex align-items-center justify-content-center rounded-circle bg-white shadow-sm border-0"
                                        style="width:120px;height:120px;">
                                    <strong>Përfundo Punën</strong>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    </div>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    function getCurrentDateTime() {
        const now = new Date();

        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');

        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');

        return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
    }

    function wireLocationSubmit(formId, locationId, timeId) {

        const form = document.getElementById(formId);

        if (!form) {
            return;
        }

        form.addEventListener('submit', function (e) {

            if (form.dataset.ready === '1') {
                return;
            }

            e.preventDefault();

            if (!navigator.geolocation) {
                alert('Ky shfletues nuk e mbështet marrjen e lokacionit.');
                return;
            }

            const button = form.querySelector('button');
            const originalText = button.innerHTML;

            button.disabled = true;
            button.innerHTML = 'Duke marrë lokacionin...';

            navigator.geolocation.getCurrentPosition(

                function (position) {

                    document.getElementById(locationId).value =
                        position.coords.latitude + ',' + position.coords.longitude;

                    document.getElementById(timeId).value =
                        getCurrentDateTime();

                    button.innerHTML = 'Duke dërguar...';

                    form.dataset.ready = '1';
                    form.submit();

                },

                function () {

                    button.disabled = false;
                    button.innerHTML = originalText;

                    alert('Duhet të lejoni qasjen në lokacion.');

                },

                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }

            );

        });

    }

    wireLocationSubmit('startWorkForm', 'check_in_location', 'check_in_time');
    wireLocationSubmit('startBreakForm', 'pause_start_location', 'pause_start_time');
    wireLocationSubmit('endBreakForm', 'pause_end_location', 'pause_end_time');
    wireLocationSubmit('endWorkForm', 'check_out_location', 'check_out_time');

});
</script>
@endpush
