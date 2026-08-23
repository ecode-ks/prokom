@extends('template.template')

{{-- Page Title --}}
@section('title', 'Profili im')

{{-- Page Content --}}
@section('content')
    <small class="d-block txt-lightblue fw-semibold text-uppercase">Llogaria</small>
    <h3 class="fw-semibold pt-2">Profili Im</h3>
    <p class="text-muted">Shikoni të dhënat e llogarisë dhe ndryshoni vetëm fjalëkalimin tuaj.</p>
    <hr class="mb-4">

    {{-- Profile Content --}}
    <div class="row g-4 align-items-stretch">
        <div class="col-12 col-md-12 col-lg-3">
            <div class="card h-100 overflow-hidden">
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
            <div class="card h-100">
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
                            <input type="text" class="form-control shadow-sm rounded-3 bg-light" value="{{ session('user') ?? session('company') }}" placeholder="Emri dhe Mbiemri" disabled>
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

    {{-- Change Package --}}
    @if(session('role') === 'company')
        <div class="card mt-5 border-0 shadow-sm overflow-hidden" style="background: linear-gradient(165deg, #f8fbff 0%, #ffffff 40%, #f5f9ff 100%);">
            <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-3" style="width: 42px; height: 42px; background: rgba(13,110,253,0.1);">
                            <i class="fa-brands fa-trello txt-lightblue"></i>
                        </span>
                        <div>
                            <h5 class="mb-0 fw-semibold">Ndrysho Paketën e Abonimit</h5>
                            <small class="text-muted">Zgjidhni paketën që i përshtatet më mirë kompanisë suaj</small>
                        </div>
                    </div>
                    <span class="badge rounded-pill text-bg-light border px-3 py-2">Menaxhimi i Abonimit</span>
                </div>
            </div>
            <div class="card-body px-4 pb-4 pt-3">
                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-6">
                        <div class="rounded-4 border bg-white p-3 h-100 shadow-sm">
                            <small class="text-muted d-block mb-1">Paketa aktuale</small>
                            <div class="fw-semibold txt-lightblue fs-5 text-truncate" title="{{ session('package') ?? 'Nuk ka pako aktive' }}">
                                {{ session('package') ?? 'Nuk ka pako aktive' }}
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="rounded-4 border bg-white p-3 h-100 shadow-sm">
                            <small class="text-muted d-block mb-1">Valide deri më</small>
                            <div class="fw-semibold txt-lightblue fs-5">
                                {{ session('expire_date') ? \Carbon\Carbon::parse(session('expire_date'))->format('d/m/Y') : '--/--/----' }}
                            </div>
                        </div>
                    </div>
                </div>
                @if($packages)
                    <div class="row g-4">
                        @foreach ($packages as $package)
                            <div class="col-12 col-md-6 col-xl-4">
                                <div class="card h-100 border-0 shadow-sm position-relative overflow-hidden" style="transition: transform .25s ease, box-shadow .25s ease;">
                                    <div class="position-absolute top-0 start-0 w-100" style="height: 4px; background: linear-gradient(90deg, #0d6efd, #4ea4ff);"></div>
                                    <div class="card-body d-flex flex-column p-4" style="background: linear-gradient(180deg, rgba(13,110,253,0.08), rgba(13,110,253,0.01));">
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <h6 class="fw-semibold mb-0 text-truncate pe-2" title="{{ $package->title }}">{{ $package->title }}</h6>
                                            <span class="badge rounded-pill text-bg-light border">{{ $package->months }} Muaj</span>
                                        </div>
                                        <div class="mb-3">
                                            <span class="h3 fw-bold mb-0 txt-lightblue">{{ $package->price }} €</span>
                                            <small class="text-muted d-block">çmimi total i paketës</small>
                                        </div>
                                        <p class="text-muted mb-4" style="min-height: 72px;">{!! nl2br(e($package->description)) !!}</p>
                                        <a href="{{ route('change.package', $package->id)}}" class="btn btn-outline-primary rounded-3 mt-auto w-100">
                                            <i class="fa-solid fa-bolt me-1"></i> Zgjidh Këtë Paketë
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-light border rounded-4 mb-0">
                        Momentalisht nuk ka pako të disponueshme.
                    </div>
                @endif
            </div>
        </div>
    @endif
@endsection
