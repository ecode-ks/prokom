<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>PROKOM | @yield('title')</title>

        {{-- Assets --}}
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link rel="stylesheet" href="{{ asset('assets/custom.css') }}">
    </head>
    <body>
        {{-- Load spinner --}}
        <div id="page-loader" class="d-flex justify-content-center align-items-center position-fixed top-0 start-0 w-100 h-100 bg-white" style="z-index:99999;">
            <div class="spinner-border txt-darkblue" style="width:4rem; height:4rem;" role="status"></div>
        </div>

        <div class="container-fluid">
            <div class="row">

                {{-- Sidebar --}}
                <aside class="col-sm-12 col-md-12 col-lg-2 d-none d-md-none d-lg-block bg-darkblue text-light vh-100 overflow-auto m-0 d-print-none">
                    <div class="d-flex flex-column vh-100">
                        <div class="sticky-top bg-darkblue pt-3 pb-2" style="border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
                            <div class="d-flex align-items-center mb-2">
                                <div class="me-2 rounded-3 bg-lightblue p-2 border-lightblue">
                                    <i class="fa-solid fa-city"></i>
                                </div>
                                <div>
                                    <small class="d-block fw-semibold">PROKOM</small>
                                    <small class="d-block opacity-50">Paneli Menaxhimit</small>
                                </div>
                            </div>
                        </div>

                        {{-- List Menu --}}
                        <div>
                            <small class="d-block text-uppercase opacity-50 ms-2 mb-2 mt-4 fs-small">Përmbledhje</small>
                            <ul class="list-group mb-4">
                                <li class="list-group-item mb-1 rounded-3 p-1" id="{{ isset($page) && $page === 'Dashboard' ? 'sidebar-active' : 'sidebar-link' }}">
                                    <a href="{{ route('dashboard') }}" class="text-decoration-none text-light">
                                        <i class="fa-solid fa-chart-column mx-2 opacity-75"></i> Dashboard
                                    </a>
                                </li>
                                @if(session('role') === 'admin')
                                    <li class="list-group-item mb-1 rounded-3 p-1" id="{{ isset($page) && $page === 'Packages' ? 'sidebar-active' : 'sidebar-link' }}">
                                        <a href="{{ route('packages') }}" class="text-decoration-none text-light">
                                            <i class="fa-brands fa-trello mx-2 opacity-75"></i> Paketat
                                        </a>
                                    </li>
                                    <li class="list-group-item mb-1 rounded-3 p-1" id="{{ isset($page) && $page === 'Companies' ? 'sidebar-active' : 'sidebar-link' }}">
                                        <a href="{{ route('companies') }}" class="text-decoration-none text-light">
                                            <i class="fa-solid fa-building mx-2 opacity-75"></i> Kompanitë
                                        </a>
                                    </li>
                                @endif
                                @if(session('role') === 'company' || session('role') === 'manager')
                                    <li class="list-group-item mb-1 rounded-3 p-1" id="{{ isset($page) && $page === 'Company News' ? 'sidebar-active' : 'sidebar-link' }}">
                                        <a href="{{ route('company.news') }}" class="text-decoration-none text-light">
                                            <i class="fa-solid fa-newspaper mx-2 opacity-75"></i> Lajme të Kompanisë
                                        </a>
                                    </li>
                                @endif
                            </ul>


                            @if(session('role') === 'company' || session('role') === 'manager' || session('role') === 'employee')
                                <small class="d-block text-uppercase opacity-50 ms-2 mb-2 fs-small">Operations</small>
                                <ul class="list-group mb-4">
                                    @if(session('role') === 'company' || session('role') === 'manager')
                                        <li class="list-group-item mb-1 rounded-3 p-1" id="{{ isset($page) && $page === 'Projects' ? 'sidebar-active' : 'sidebar-link' }}">
                                            <a href="{{ route('projects') }}" class="text-decoration-none text-light">
                                                <i class="fa-solid fa-diagram-project mx-2 opacity-75"></i> Projektet
                                            </a>
                                        </li>
                                    @endif
                                    @if(session('role') === 'company' || session('role') === 'manager' || session('role') === 'employee')
                                        <li class="list-group-item mb-1 rounded-3 p-1" id="{{ isset($page) && $page === 'Tasks' ? 'sidebar-active' : 'sidebar-link' }}">
                                            <a href="" class="text-decoration-none text-light">
                                                <i class="fa-solid fa-list-check mx-2 opacity-75"></i> Tasks
                                            </a>
                                        </li>
                                    @endif

                                    @if(session('role') === 'company' || session('role') === 'manager')
                                        <li class="list-group-item mb-1 rounded-3 p-1" id="{{ isset($page) && $page === 'Project Reports' ? 'sidebar-active' : 'sidebar-link' }}">
                                            <a href="{{ route('project.reports') }}" class="text-decoration-none text-light">
                                                <i class="fa-solid fa-chart-line mx-2 opacity-75"></i> Raportet e Projekteve
                                            </a>
                                        </li>
                                    @endif
                                    @if(session('role') === 'company' || session('role') === 'manager' || session('role') === 'employee')
                                        <li class="list-group-item mb-1 rounded-3 p-1" id="{{ isset($page) && $page === 'Task Reports' ? 'sidebar-active' : 'sidebar-link' }}">
                                            <a href="" class="text-decoration-none text-light">
                                                <i class="fa-solid fa-file-lines mx-2 opacity-75"></i> Task Reports
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            @endif

                            @if(session('role') === 'company' || session('role') === 'manager')
                                <small class="d-block text-uppercase opacity-50 ms-2 mb-2 fs-small">Materialet</small>
                                <ul class="list-group mb-4">
                                    <li class="list-group-item mb-1 rounded-3 p-1" id="{{ isset($page) && $page === 'Materials' ? 'sidebar-active' : 'sidebar-link' }}">
                                        <a href="{{ route('materials') }}" class="text-decoration-none text-light">
                                            <i class="fa-solid fa-layer-group mx-2 opacity-75"></i> Materialet
                                        </a>
                                    </li>
                                    <li class="list-group-item mb-1 rounded-3 p-1" id="{{ isset($page) && $page === 'Materials Stock' ? 'sidebar-active' : 'sidebar-link' }}">
                                        <a href="{{ route('materials.stock') }}" class="text-decoration-none text-light">
                                            <i class="fa-solid fa-boxes-stacked mx-2 opacity-75"></i> Stoku i Materialeve
                                        </a>
                                    </li>
                                    <li class="list-group-item mb-1 rounded-3 p-1" id="{{ isset($page) && $page === 'Stock Report' ? 'sidebar-active' : 'sidebar-link' }}">
                                        <a href="{{ route('materials.stock.report') }}" class="text-decoration-none text-light">
                                            <i class="fa-solid fa-chart-pie mx-2 opacity-75"></i> Raporti i Stokut
                                        </a>
                                    </li>
                                </ul>
                            @endif

                            @if(session('role') === 'company' || session('role') === 'manager' || session('role') === 'finance')
                                <small class="d-block text-uppercase opacity-50 ms-2 mb-2 fs-small">Financat</small>
                                <ul class="list-group mb-4">
                                    <li class="list-group-item mb-1 rounded-3 p-1" id="{{ isset($page) && $page === 'Clients' ? 'sidebar-active' : 'sidebar-link' }}">
                                        <a href="{{ route('clients') }}" class="text-decoration-none text-light">
                                            <i class="fa-solid fa-users-viewfinder mx-2 opacity-75"></i> Klientët
                                        </a>
                                    </li>
                                    <li class="list-group-item mb-1 rounded-3 p-1" id="{{ isset($page) && $page === 'Suppliers' ? 'sidebar-active' : 'sidebar-link' }}">
                                        <a href="{{ route('suppliers') }}" class="text-decoration-none text-light">
                                            <i class="fa-solid fa-truck mx-2 opacity-75"></i> Furnizuesit
                                        </a>
                                    </li>
                                    @if(session('role') === 'company' || session('role') === 'finance')
                                        <li class="list-group-item mb-1 rounded-3 p-1" id="{{ isset($page) && $page === 'Revenue & Expenses' ? 'sidebar-active' : 'sidebar-link' }}">
                                            <a href="{{ route('financial.transactions') }}" class="text-decoration-none text-light">
                                                <i class="fa-solid fa-wallet mx-2 opacity-75"></i> Fitimet & Shpenzimet
                                            </a>
                                        </li>
                                        <li class="list-group-item mb-1 rounded-3 p-1" id="{{ isset($page) && $page === 'Financial Report' ? 'sidebar-active' : 'sidebar-link' }}">
                                            <a href="{{ route('financial.report') }}" class="text-decoration-none text-light">
                                                <i class="fa-solid fa-chart-pie mx-2 opacity-75"></i> Raporti Financiar
                                            </a>
                                        </li>
                                        <li class="list-group-item mb-1 rounded-3 p-1" id="{{ isset($page) && $page === 'Offers' ? 'sidebar-active' : 'sidebar-link' }}">
                                            <a href="{{ route('offers') }}" class="text-decoration-none text-light">
                                                <i class="fa-solid fa-file-invoice-dollar mx-2 opacity-75"></i> Ofertat
                                            </a>
                                        </li>
                                        <li class="list-group-item mb-1 rounded-3 p-1" id="{{ isset($page) && $page === 'Create Offer' ? 'sidebar-active' : 'sidebar-link' }}">
                                            <a href="{{ route('create.offer') }}" class="text-decoration-none text-light">
                                                <i class="fa-solid fa-file-invoice-dollar mx-2 opacity-75"></i> Krijo Ofertë
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            @endif

                            @if(session('role') === 'company' || session('role') === 'manager' || session('role') === 'hr')
                                <small class="d-block text-uppercase opacity-50 ms-2 mb-2 fs-small">Stafi</small>
                            @endif
                            <ul class="list-group mb-4">
                                @if(session('role') === 'company' || session('role') === 'manager' || session('role') === 'hr')
                                    <li class="list-group-item mb-1 rounded-3 p-1" id="{{ isset($page) && $page === 'Employees' ? 'sidebar-active' : 'sidebar-link' }}">
                                        <a href="{{ route('employees') }}" class="text-decoration-none text-light">
                                            <i class="fa-solid fa-users mx-2 opacity-75"></i> Punëtorët
                                        </a>
                                    </li>
                                @endif
                                @if(session('role') === 'company' || session('role') === 'manager' || session('role') === 'hr')
                                    <li class="list-group-item mb-1 rounded-3 p-1" id="{{ isset($page) && $page === 'Check In' ? 'sidebar-active' : 'sidebar-link' }}">
                                        <a href="" class="text-decoration-none text-light">
                                            <i class="fa-solid fa-user-check mx-2 opacity-75"></i> Check In
                                        </a>
                                    </li>
                                @endif
                                @if(session('role') === 'company' || session('role') === 'manager' || session('role') === 'hr' || session('role') === 'employee')
                                    <li class="list-group-item mb-1 rounded-3 p-1" id="{{ isset($page) && $page === 'Working Hours' ? 'sidebar-active' : 'sidebar-link' }}">
                                        <a href="" class="text-decoration-none text-light">
                                            <i class="fa-solid fa-clock mx-2 opacity-75"></i> Working Hours
                                        </a>
                                    </li>
                                @endif
                                @if(session('role') === 'company' || session('role') === 'hr')
                                    <li class="list-group-item mb-1 rounded-3 p-1" id="{{ isset($page) && $page === 'Contracts' ? 'sidebar-active' : 'sidebar-link' }}">
                                        <a href="{{ route('contracts') }}" class="text-decoration-none text-light">
                                            <i class="fa-solid fa-file-contract mx-2 opacity-75"></i> Kontratat
                                        </a>
                                    </li>
                                    <li class="list-group-item mb-1 rounded-3 p-1" id="{{ isset($page) && $page === 'Vacations' ? 'sidebar-active' : 'sidebar-link' }}">
                                        <a href="{{ route('vacations') }}" class="text-decoration-none text-light">
                                            <i class="fa-solid fa-calendar-days mx-2 opacity-75"></i> Pushimet
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>

                        {{-- User Profile --}}
                        @php
                            $profileName = session('user') ?: session('company');
                        @endphp
                        <div class="sticky-bottom bg-darkblue pt-2 pb-2 mt-auto" style="border-top: 1px solid rgba(255, 255, 255, 0.1);">
                            <div class="bg-lightblue border-lightblue rounded-3 p-2">
                                <div class="row align-items-center">
                                    <div class="col-9">
                                        <small class="d-block col-12 text-truncate" title="{{ $profileName }}">{{ $profileName }}</small>
                                        <small class="d-block opacity-50 fs-small col-12 text-truncate text-capitalize" title="{{ session('role') }}">{{ session('role') }}</small>
                                    </div>
                                    <div class="col-3">
                                        <a href="{{ route('logout') }}" class="btn btn-darkblue rounded-3 btn-sm"><i class="fa-solid fa-right-from-bracket text-danger"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </aside>

                <div class="col-sm-12 col-md-12 col-lg-10 bg-custom vh-100 overflow-auto p-0">

                    {{-- Topbar --}}
                    <header class="pb-3 bg-white sticky-top py-3 px-4 d-print-none">
                        <div class="row align-items-center">
                            <div class="col-4 col-lg-6">
                                <div class="d-flex align-items-center gap-2">

                                    {{-- Dropdown Menu for SM Devices --}}
                                    <div class="dropdown d-lg-none">
                                        <button class="btn bg-custom rounded-3 border" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fa-solid fa-list-ul"></i></i> <span class="d-none d-md-inline">Menu</span>
                                        </button>
                                        <ul class="dropdown-menu rounded-3 shadow-sm p-2" style="min-width: 260px; max-height: 70vh; overflow-y: auto;">
                                            <li><span class="dropdown-item-text fw-semibold">Përmbledhje</span></li>
                                            <li><a class="dropdown-item rounded-3 mb-1 {{ isset($page) && $page === 'Dashboard' ? 'bg-darkblue text-light' : '' }}" href="{{ route('dashboard') }}"><i class="fa-solid fa-chart-column me-2 opacity-75"></i> Dashboard</a></li>

                                            @if(session('role') === 'admin')
                                                <li><a class="dropdown-item rounded-3 mb-1 {{ isset($page) && $page === 'Packages' ? 'bg-darkblue text-light' : '' }}" href="{{ route('packages') }}"><i class="fa-brands fa-trello me-2 opacity-75"></i> Paketat</a></li>
                                                <li><a class="dropdown-item rounded-3 mb-1 {{ isset($page) && $page === 'Companies' ? 'bg-darkblue text-light' : '' }}" href="{{ route('companies') }}"><i class="fa-solid fa-building me-2 opacity-75"></i> Kompanitë</a></li>
                                            @endif

                                            @if(session('role') === 'company' || session('role') === 'manager')
                                                <li><a class="dropdown-item rounded-3 mb-1 {{ isset($page) && $page === 'Company News' ? 'bg-darkblue text-light' : '' }}" href="{{ route('company.news') }}"><i class="fa-solid fa-newspaper me-2 opacity-75"></i> Lajme të Kompanisë</a></li>
                                            @endif

                                            @if(session('role') === 'company' || session('role') === 'manager' || session('role') === 'employee')
                                                <li><hr class="dropdown-divider"></li>
                                                <li><span class="dropdown-item-text fw-semibold">Operations</span></li>
                                                @if(session('role') === 'company' || session('role') === 'manager')
                                                    <li><a class="dropdown-item rounded-3 mb-1 {{ isset($page) && $page === 'Projects' ? 'bg-darkblue text-light' : '' }}" href="{{ route('projects') }}"><i class="fa-solid fa-diagram-project me-2 opacity-75"></i> Projektet</a></li>
                                                    <li><a class="dropdown-item rounded-3 mb-1 {{ isset($page) && $page === 'Tasks' ? 'bg-darkblue text-light' : '' }}" href=""><i class="fa-solid fa-list-check me-2 opacity-75"></i> Tasks</a></li>
                                                @endif
                                                @if(session('role') === 'company' || session('role') === 'manager')
                                                    <li><a class="dropdown-item rounded-3 mb-1 {{ isset($page) && $page === 'Project Reports' ? 'bg-darkblue text-light' : '' }}" href="{{ route('project.reports') }}"><i class="fa-solid fa-chart-line me-2 opacity-75"></i> Raportet e Projekteve</a></li>
                                                @endif
                                                @if(session('role') === 'company' || session('role') === 'manager' || session('role') === 'employee')
                                                    <li><a class="dropdown-item rounded-3 mb-1 {{ isset($page) && $page === 'Task Reports' ? 'bg-darkblue text-light' : '' }}" href=""><i class="fa-solid fa-file-lines me-2 opacity-75"></i> Task Reports</a></li>
                                                @endif
                                            @endif

                                            @if(session('role') === 'company' || session('role') === 'manager')
                                                <li><hr class="dropdown-divider"></li>
                                                <li><span class="dropdown-item-text fw-semibold">Materialet</span></li>
                                                <li><a class="dropdown-item rounded-3 mb-1 {{ isset($page) && $page === 'Materials' ? 'bg-darkblue text-light' : '' }}" href=""><i class="fa-solid fa-layer-group me-2 opacity-75"></i> Materialet</a></li>
                                                <li><a class="dropdown-item rounded-3 mb-1 {{ isset($page) && $page === 'Materials Stock' ? 'bg-darkblue text-light' : '' }}" href=""><i class="fa-solid fa-boxes-stacked me-2 opacity-75"></i> Stoku i Materialeve</a></li>
                                                <li><a class="dropdown-item rounded-3 mb-1 {{ isset($page) && $page === 'Stock Report' ? 'bg-darkblue text-light' : '' }}" href="{{ route('materials.stock.report') }}"><i class="fa-solid fa-chart-column me-2 opacity-75"></i> Raporti i Stokut</a></li>
                                            @endif

                                            @if(session('role') === 'company' || session('role') === 'manager' || session('role') === 'finance')
                                                <li><hr class="dropdown-divider"></li>
                                                <li><span class="dropdown-item-text fw-semibold">Financat</span></li>
                                                <li><a class="dropdown-item rounded-3 mb-1 {{ isset($page) && $page === 'Clients' ? 'bg-darkblue text-light' : '' }}" href="{{ route('clients') }}"><i class="fa-solid fa-users-viewfinder me-2 opacity-75"></i> Klientët</a></li>
                                                <li><a class="dropdown-item rounded-3 mb-1 {{ isset($page) && $page === 'Suppliers' ? 'bg-darkblue text-light' : '' }}" href="{{ route('suppliers') }}"><i class="fa-solid fa-truck me-2 opacity-75"></i> Furnizuesit</a></li>
                                                @if(session('role') === 'company' || session('role') === 'finance')
                                                    <li><a class="dropdown-item rounded-3 mb-1 {{ isset($page) && $page === 'Revenue & Expenses' ? 'bg-darkblue text-light' : '' }}" href=""><i class="fa-solid fa-wallet me-2 opacity-75"></i> Fitimet & Shpenzimet</a></li>
                                                    <li><a class="dropdown-item rounded-3 mb-1 {{ isset($page) && $page === 'Financial Report' ? 'bg-darkblue text-light' : '' }}" href=""><i class="fa-solid fa-chart-pie me-2 opacity-75"></i> Financial Report</a></li>
                                                    <li><a class="dropdown-item rounded-3 mb-1 {{ isset($page) && $page === 'Offers' ? 'bg-darkblue text-light' : '' }}" href="{{ route('offers') }}"><i class="fa-solid fa-file-invoice-dollar me-2 opacity-75"></i> Ofertat</a></li>
                                                    <li><a class="dropdown-item rounded-3 mb-1 {{ isset($page) && $page === 'Create Offer' ? 'bg-darkblue text-light' : '' }}" href="{{ route('create.offer') }}"><i class="fa-solid fa-file-invoice-dollar me-2 opacity-75"></i> Krijo Ofertë</a></li>
                                                @endif
                                            @endif

                                            <li><hr class="dropdown-divider"></li>
                                            @if(session('role') === 'company' || session('role') === 'manager' || session('role') === 'hr')
                                                <li><span class="dropdown-item-text fw-semibold">Stafi</span></li>
                                            @endif
                                            @if(session('role') === 'company' || session('role') === 'manager' || session('role') === 'hr')
                                                <li><a class="dropdown-item rounded-3 mb-1 {{ isset($page) && $page === 'Employees' ? 'bg-darkblue text-light' : '' }}" href="{{ route('employees') }}"><i class="fa-solid fa-users me-2 opacity-75"></i> Punëtorët</a></li>
                                                <li><a class="dropdown-item rounded-3 mb-1 {{ isset($page) && $page === 'Check In' ? 'bg-darkblue text-light' : '' }}" href=""><i class="fa-solid fa-user-check me-2 opacity-75"></i> Check In</a></li>
                                            @endif
                                            @if(session('role') === 'company' || session('role') === 'manager' || session('role') === 'hr')
                                                <li><a class="dropdown-item rounded-3 mb-1 {{ isset($page) && $page === 'Working Hours' ? 'bg-darkblue text-light' : '' }}" href=""><i class="fa-solid fa-clock me-2 opacity-75"></i> Working Hours</a></li>
                                                <li><a class="dropdown-item rounded-3 mb-1 {{ isset($page) && $page === 'Contracts' ? 'bg-darkblue text-light' : '' }}" href="{{ route('contracts') }}"><i class="fa-solid fa-file-contract me-2 opacity-75"></i> Kontratat</a></li>
                                                <li><a class="dropdown-item rounded-3 mb-1 {{ isset($page) && $page === 'Vacations' ? 'bg-darkblue text-light' : '' }}" href="{{ route('vacations') }}"><i class="fa-solid fa-calendar-days me-2 opacity-75"></i> Pushimet</a></li>
                                            @endif
                                        </ul>
                                    </div>

                                    {{-- Change Project Dropdown --}}
                                    @if(session('role') === 'company' || session('role') === 'manager')
                                        <div class="dropdown">
                                            <button class="btn bg-custom rounded-3 border text-truncate" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="{{ $selectedProjectName ?? 'Zgjidh Projektin' }}" style="max-width: 250px;">
                                                <i class="fa-regular fa-building"></i> <span class="d-none d-md-inline">{{ $selectedProjectName ?? 'Zgjidh Projektin' }} <i class="fa-solid fa-up-down ms-1" style="font-size: 10px;"></i></span>
                                            </button>
                                            <ul class="dropdown-menu rounded-3 shadow-sm p-2">
                                                <span class="d-block border-bottom pb-2 ps-3 mb-2 fw-semibold">Projektet</span>
                                                @if(isset($topbarProjects) && count($topbarProjects) > 0)
                                                    @foreach($topbarProjects as $project)
                                                        <li>
                                                            <a class="dropdown-item rounded-3 d-flex align-items-center gap-2 mb-1" href="{{ route('set.project.context', $project->id) }}">
                                                                @if((int) ($selectedProjectId ?? 0) === (int) $project->id)
                                                                    <i class="fa-solid fa-circle" style="font-size: 8px;"></i>
                                                                @else
                                                                    <i class="fa-regular fa-circle" style="font-size: 8px;"></i>
                                                                @endif
                                                                {{ $project->project_name }}
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                @else
                                                    <li><span class="dropdown-item text-muted">Nuk u gjetën projekte</span></li>
                                                @endif
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-4 d-lg-none text-center fw-semibold" style="letter-spacing: 0.5px;">PROKOM</div>
                            <div class="col-4 col-lg-6 text-end">
                                @php
                                    $profileName = session('user') ?: session('company');
                                @endphp

                                {{-- Profile Dropdown --}}
                                <div class="dropdown-end">
                                    <button class="btn bg-custom rounded-3 border dropdown-toggle text-truncate" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="{{ $profileName }}" style="max-width: 200px;">
                                        <i class="fa-regular fa-user"></i> <span class="d-none d-md-inline">{{ $profileName }}</span>
                                    </button>
                                    <ul class="dropdown-menu rounded-3 shadow-sm p-2">
                                        <span class="d-block px-3 fw-semibold">{{ $profileName }}</span>
                                        <small class="d-block border-bottom pb-2 ps-3 mb-2 fs-small opacity-75 text-capitalize">{{ session('role') }}</small>
                                        <li><a class="dropdown-item rounded-3 mb-1 {{ isset($page) && $page === 'My Profile' ? 'bg-darkblue text-light' : '' }}" href="{{ route('profile') }}"><i class="fa-regular fa-circle-user"></i> Profili im</a></li>
                                        <li><a class="dropdown-item rounded-3 text-danger" href="{{ route('logout') }}"><i class="fa-solid fa-right-from-bracket"></i> Shkyçu</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </header>

                    {{-- Content --}}
                    <div class="p-4">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Alerts -->
        @if (\Session::has('success'))
            <div class="toast-container position-fixed bottom-0 end-0 p-3">
                <div id="successToast" class="toast fade show" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header bg-success border-success">
                        <strong class="me-auto text-light">Njoftim!</strong>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body text-dark border border-success">
                        {!! \Session::get('success') !!}
                    </div>
                </div>
            </div>
        @endif

        <!-- Error Alerts -->
        @if (\Session::has('error'))
            <div class="toast-container position-fixed bottom-0 end-0 p-3">
                <div id="errorToast" class="toast fade show" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header bg-danger border-danger">
                        <strong class="me-auto text-light">Njoftim!</strong>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body text-dark border border-danger">
                        {!! \Session::get('error') !!}
                    </div>
                </div>
            </div>
        @endif

        {{-- Loader JS --}}
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                const loader = document.getElementById("page-loader");
                setTimeout(() => loader.classList.add("hide"), 100);
                setTimeout(() => loader.remove(), 500);
            });
        </script>

        @stack('scripts')
    </body>
</html>
