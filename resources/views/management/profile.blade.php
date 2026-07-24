@extends('template.template')

@section('title', 'Profili im')

@section('content')
    <small class="d-block txt-lightblue fw-semibold text-uppercase">Llogaria</small>
    <h3 class="fw-semibold pt-2">Profili Im</h3>
    <p class="text-muted">Shikoni të dhënat e llogarisë dhe ndryshoni vetëm fjalëkalimin tuaj.</p>
    <hr class="mb-4">

    <div class="row g-4 align-items-stretch">
        <div class="col-12 col-md-12 col-lg-3">
            <div class="card h-100 border-0 shadow-sm overflow-hidden">
                <div class="card-body text-center d-flex flex-column justify-content-center py-5" style="background: linear-gradient(180deg, rgba(13,110,253,0.08), rgba(13,110,253,0.02));">
                    <div class="mx-auto d-flex align-items-center justify-content-center rounded-circle bg-white shadow-sm mb-4" style="width: 120px; height: 120px;">
                        <i class="fa-regular fa-circle-user txt-lightblue" style="font-size: 5rem;"></i>
                    </div>
                    <h4 class="fw-semibold mb-1 text-truncate" title="{{ session('user') ?? session('company') }}">{{ session('user') ?? session('company') }}</h4>
                    <p class="text-muted mb-0 text-truncate" title="{{ session('username') }}">{{ session('username') }}</p>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-12 col-lg-9">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header rounded-top-4 bg-white border-0 pt-4 px-4">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-key txt-lightblue"></i>
                        <span class="fw-semibold">Ndrysho Fjalëkalimin</span>
                    </div>
                </div>
                <div class="card-body px-4 pb-4 pt-2">
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-md-6">
                            <label class="form-label">Emri dhe Mbiemri</label>
                            <input type="text" class="form-control shadow-sm rounded-3 bg-light" value="{{ session('user') }}" placeholder="Emri dhe Mbiemri" disabled>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Përdoruesi</label>
                            <input type="text" class="form-control shadow-sm rounded-3 bg-light" value="{{ session('username') }}" placeholder="Përdoruesi" disabled>
                        </div>
                    </div>

                    <form action="{{ route('update.profile.password') }}" method="post">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <label for="old_password" class="form-label">Fjalëkalimi Aktual</label>
                                <input type="password" name="old_password" id="old_password" class="form-control shadow-sm rounded-3" placeholder="Shkruani fjalëkalimin aktual" required>
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="new_password" class="form-label">Fjalëkalimi i Ri</label>
                                <input type="password" name="new_password" id="new_password" class="form-control shadow-sm rounded-3" placeholder="Shkruani fjalëkalimin e ri" required>
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="new_password_confirmation" class="form-label">Konfirmo Fjalëkalimin e Ri</label>
                                <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control shadow-sm rounded-3" placeholder="Përsërit fjalëkalimin e ri" required>
                            </div>
                            <div class="col-12 pt-2">
                                <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-key"></i> Përditëso Fjalëkalimin</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
