<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>WORKOM | @yield('title')</title>
        @include('partials.favicon')

        {{-- Assets --}}
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link rel="stylesheet" href="{{ asset('assets/custom.css') }}?v={{ @filemtime(public_path('assets/custom.css')) }}">
        @stack('styles')
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
                                    <small class="d-block fw-semibold">WORKOM</small>
                                    <small class="d-block opacity-50">{{ __('pages.panel_subtitle') }}</small>
                                </div>
                            </div>
                        </div>

                        {{-- List Menu --}}
                        <div>
                            <small class="d-block text-uppercase opacity-50 ms-2 mb-2 mt-4 fs-small">{{ __('pages.nav_overview') }}</small>
                            <ul class="list-group mb-4">
                                <li class="list-group-item mb-1 rounded-3 p-1" id="{{ isset($page) && $page === 'Dashboard' ? 'sidebar-active' : 'sidebar-link' }}">
                                    <a href="{{ route('dashboard') }}" class="text-decoration-none text-light">
                                        <i class="fa-solid fa-chart-column mx-2 opacity-75"></i> {{ __('pages.nav_dashboard') }}
                                    </a>
                                </li>
                                @if(session('role') && session('role') !== 'admin')
                                    <li class="list-group-item mb-1 rounded-3 p-1" id="{{ isset($page) && $page === 'Notifications' ? 'sidebar-active' : 'sidebar-link' }}">
                                        <a href="{{ route('notifications') }}" class="text-decoration-none text-light">
                                            <i class="fa-regular fa-bell mx-2 opacity-75"></i> {{ __('pages.nav_notifications') }}
                                        </a>
                                    </li>
                                @endif
                                @if(session('role') === 'admin')
                                    <li class="list-group-item mb-1 rounded-3 p-1" id="{{ isset($page) && $page === 'Packages' ? 'sidebar-active' : 'sidebar-link' }}">
                                        <a href="{{ route('packages') }}" class="text-decoration-none text-light">
                                            <i class="fa-brands fa-trello mx-2 opacity-75"></i> {{ __('pages.nav_packages') }}
                                        </a>
                                    </li>
                                    <li class="list-group-item mb-1 rounded-3 p-1" id="{{ isset($page) && $page === 'Companies' ? 'sidebar-active' : 'sidebar-link' }}">
                                        <a href="{{ route('companies') }}" class="text-decoration-none text-light">
                                            <i class="fa-solid fa-building mx-2 opacity-75"></i> {{ __('pages.nav_companies') }}
                                        </a>
                                    </li>
                                    <li class="list-group-item mb-1 rounded-3 p-1" id="{{ isset($page) && $page === 'Payments' ? 'sidebar-active' : 'sidebar-link' }}">
                                        <a href="{{ route('payments') }}" class="text-decoration-none text-light">
                                            <i class="fa-solid fa-money-bill mx-2 opacity-75"></i> {{ __('pages.nav_payments') }}
                                        </a>
                                    </li>
                                @endif
                                @if(session('role') === 'company' || session('role') === 'manager')
                                    <li class="list-group-item mb-1 rounded-3 p-1" id="{{ isset($page) && $page === 'Company News' ? 'sidebar-active' : 'sidebar-link' }}">
                                        <a href="{{ route('company.news') }}" class="text-decoration-none text-light">
                                            <i class="fa-solid fa-newspaper mx-2 opacity-75"></i> {{ __('pages.nav_company_news') }}
                                        </a>
                                    </li>
                                @endif
                            </ul>


                            @if(session('role') === 'company' || session('role') === 'manager' || session('role') === 'employee')
                                <small class="d-block text-uppercase opacity-50 ms-2 mb-2 fs-small">{{ __('pages.nav_operations') }}</small>
                                <ul class="list-group mb-4">
                                    @if(session('role') === 'company' || session('role') === 'manager')
                                        <li class="list-group-item mb-1 rounded-3 p-1" id="{{ isset($page) && $page === 'Projects' ? 'sidebar-active' : 'sidebar-link' }}">
                                            <a href="{{ route('projects') }}" class="text-decoration-none text-light">
                                                <i class="fa-solid fa-diagram-project mx-2 opacity-75"></i> {{ __('pages.nav_projects') }}
                                            </a>
                                        </li>
                                    @endif
                                    @if(session('role') === 'company' || session('role') === 'manager' || session('role') === 'employee')
                                        <li class="list-group-item mb-1 rounded-3 p-1" id="{{ isset($page) && $page === 'Tasks' ? 'sidebar-active' : 'sidebar-link' }}">
                                            <a href="{{ route('tasks') }}" class="text-decoration-none text-light">
                                                <i class="fa-solid fa-list-check mx-2 opacity-75"></i> {{ __('pages.nav_tasks') }}
                                            </a>
                                        </li>
                                    @endif

                                    @if(session('role') === 'company' || session('role') === 'manager')
                                        <li class="list-group-item mb-1 rounded-3 p-1" id="{{ isset($page) && $page === 'Project Reports' ? 'sidebar-active' : 'sidebar-link' }}">
                                            <a href="{{ route('project.reports') }}" class="text-decoration-none text-light">
                                                <i class="fa-solid fa-chart-line mx-2 opacity-75"></i> {{ __('pages.nav_project_reports') }}
                                            </a>
                                        </li>
                                    @endif
                                    @if(session('role') === 'company' || session('role') === 'manager' || session('role') === 'employee')
                                        <li class="list-group-item mb-1 rounded-3 p-1" id="{{ isset($page) && $page === 'Task Reports' ? 'sidebar-active' : 'sidebar-link' }}">
                                            <a href="{{ route('task.reports') }}" class="text-decoration-none text-light">
                                                <i class="fa-solid fa-file-lines mx-2 opacity-75"></i> {{ __('pages.nav_task_reports') }}
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            @endif

                            @if(session('role') === 'company' || session('role') === 'manager')
                                <small class="d-block text-uppercase opacity-50 ms-2 mb-2 fs-small">{{ __('pages.nav_materials') }}</small>
                                <ul class="list-group mb-4">
                                    <li class="list-group-item mb-1 rounded-3 p-1" id="{{ isset($page) && $page === 'Materials' ? 'sidebar-active' : 'sidebar-link' }}">
                                        <a href="{{ route('materials') }}" class="text-decoration-none text-light">
                                            <i class="fa-solid fa-layer-group mx-2 opacity-75"></i> {{ __('pages.nav_materials') }}
                                        </a>
                                    </li>
                                    <li class="list-group-item mb-1 rounded-3 p-1" id="{{ isset($page) && $page === 'Materials Stock' ? 'sidebar-active' : 'sidebar-link' }}">
                                        <a href="{{ route('materials.stock') }}" class="text-decoration-none text-light">
                                            <i class="fa-solid fa-boxes-stacked mx-2 opacity-75"></i> {{ __('pages.nav_materials_stock') }}
                                        </a>
                                    </li>
                                    <li class="list-group-item mb-1 rounded-3 p-1" id="{{ isset($page) && $page === 'Stock Report' ? 'sidebar-active' : 'sidebar-link' }}">
                                        <a href="{{ route('materials.stock.report') }}" class="text-decoration-none text-light">
                                            <i class="fa-solid fa-chart-pie mx-2 opacity-75"></i> {{ __('pages.nav_stock_report') }}
                                        </a>
                                    </li>
                                </ul>
                            @endif

                            @if(session('role') === 'company' || session('role') === 'manager' || session('role') === 'finance')
                                <small class="d-block text-uppercase opacity-50 ms-2 mb-2 fs-small">{{ __('pages.nav_finance') }}</small>
                                <ul class="list-group mb-4">
                                    <li class="list-group-item mb-1 rounded-3 p-1" id="{{ isset($page) && $page === 'Clients' ? 'sidebar-active' : 'sidebar-link' }}">
                                        <a href="{{ route('clients') }}" class="text-decoration-none text-light">
                                            <i class="fa-solid fa-users-viewfinder mx-2 opacity-75"></i> {{ __('pages.nav_clients') }}
                                        </a>
                                    </li>
                                    <li class="list-group-item mb-1 rounded-3 p-1" id="{{ isset($page) && $page === 'Suppliers' ? 'sidebar-active' : 'sidebar-link' }}">
                                        <a href="{{ route('suppliers') }}" class="text-decoration-none text-light">
                                            <i class="fa-solid fa-truck mx-2 opacity-75"></i> {{ __('pages.nav_suppliers') }}
                                        </a>
                                    </li>
                                    @if(session('role') === 'company' || session('role') === 'finance')
                                        <li class="list-group-item mb-1 rounded-3 p-1" id="{{ isset($page) && $page === 'Revenue & Expenses' ? 'sidebar-active' : 'sidebar-link' }}">
                                            <a href="{{ route('financial.transactions') }}" class="text-decoration-none text-light">
                                                <i class="fa-solid fa-wallet mx-2 opacity-75"></i> {{ __('pages.nav_revenue') }}
                                            </a>
                                        </li>
                                        <li class="list-group-item mb-1 rounded-3 p-1" id="{{ isset($page) && $page === 'Financial Report' ? 'sidebar-active' : 'sidebar-link' }}">
                                            <a href="{{ route('financial.report') }}" class="text-decoration-none text-light">
                                                <i class="fa-solid fa-chart-pie mx-2 opacity-75"></i> {{ __('pages.nav_financial_report') }}
                                            </a>
                                        </li>
                                        <li class="list-group-item mb-1 rounded-3 p-1" id="{{ isset($page) && $page === 'Offers' ? 'sidebar-active' : 'sidebar-link' }}">
                                            <a href="{{ route('offers') }}" class="text-decoration-none text-light">
                                                <i class="fa-solid fa-file-invoice-dollar mx-2 opacity-75"></i> {{ __('pages.nav_offers') }}
                                            </a>
                                        </li>
                                        <li class="list-group-item mb-1 rounded-3 p-1" id="{{ isset($page) && $page === 'Create Offer' ? 'sidebar-active' : 'sidebar-link' }}">
                                            <a href="{{ route('create.offer') }}" class="text-decoration-none text-light">
                                                <i class="fa-solid fa-file-invoice-dollar mx-2 opacity-75"></i> {{ __('pages.nav_create_offer') }}
                                            </a>
                                        </li>
                                        <li class="list-group-item mb-1 rounded-3 p-1" id="{{ isset($page) && $page === 'Calculate Salary' ? 'sidebar-active' : 'sidebar-link' }}">
                                            <a href="{{ route('calculate.salary') }}" class="text-decoration-none text-light">
                                                <i class="fa-solid fa-calculator mx-2 opacity-75"></i> {{ __('pages.nav_calculate_salary') }}
                                            </a>
                                        </li>
                                        <li class="list-group-item mb-1 rounded-3 p-1" id="{{ isset($page) && $page === 'Salaries' ? 'sidebar-active' : 'sidebar-link' }}">
                                            <a href="{{ route('salaries') }}" class="text-decoration-none text-light">
                                                <i class="fa-solid fa-money-bill-wave mx-2 opacity-75"></i> {{ __('pages.nav_salaries') }}
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            @endif

                            @if(session('role') === 'company' || session('role') === 'manager' || session('role') === 'hr' || session('role') === 'employee')
                                <small class="d-block text-uppercase opacity-50 ms-2 mb-2 fs-small">{{ __('pages.nav_staff') }}</small>
                            @endif
                            <ul class="list-group mb-4">
                                @if(session('role') === 'company' || session('role') === 'hr')
                                    <li class="list-group-item mb-1 rounded-3 p-1" id="{{ isset($page) && $page === 'Employees' ? 'sidebar-active' : 'sidebar-link' }}">
                                        <a href="{{ route('employees') }}" class="text-decoration-none text-light">
                                            <i class="fa-solid fa-users mx-2 opacity-75"></i> {{ __('pages.nav_employees') }}
                                        </a>
                                    </li>
                                @endif
                                @if(session('role') === 'company' || session('role') === 'manager' || session('role') === 'hr')
                                    <li class="list-group-item mb-1 rounded-3 p-1" id="{{ isset($page) && $page === 'Check In' ? 'sidebar-active' : 'sidebar-link' }}">
                                        <a href="{{ route('checkins') }}" class="text-decoration-none text-light">
                                            <i class="fa-solid fa-user-check mx-2 opacity-75"></i> {{ __('pages.nav_checkin') }}
                                        </a>
                                    </li>
                                @endif
                                @if(session('role') === 'company' || session('role') === 'manager' || session('role') === 'hr' || session('role') === 'employee')
                                    <li class="list-group-item mb-1 rounded-3 p-1" id="{{ isset($page) && $page === 'Working Hours' ? 'sidebar-active' : 'sidebar-link' }}">
                                        <a href="{{ route('working.hours') }}" class="text-decoration-none text-light">
                                            <i class="fa-solid fa-clock mx-2 opacity-75"></i> {{ __('pages.nav_working_hours') }}
                                        </a>
                                    </li>
                                @endif
                                @if(session('role') === 'company' || session('role') === 'manager' || session('role') === 'hr')
                                    <li class="list-group-item mb-1 rounded-3 p-1" id="{{ isset($page) && $page === 'Extra Hours' ? 'sidebar-active' : 'sidebar-link' }}">
                                        <a href="{{ route('extra.hours') }}" class="text-decoration-none text-light">
                                            <i class="fa-solid fa-business-time mx-2 opacity-75"></i> {{ __('pages.nav_extra_hours') }}
                                        </a>
                                    </li>
                                @endif
                                @if(session('role') === 'employee')
                                    <li class="list-group-item mb-1 rounded-3 p-1" id="{{ isset($page) && $page === 'Salaries' ? 'sidebar-active' : 'sidebar-link' }}">
                                        <a href="{{ route('salaries') }}" class="text-decoration-none text-light">
                                            <i class="fa-solid fa-money-bill-wave mx-2 opacity-75"></i> {{ __('pages.nav_salaries') }}
                                        </a>
                                    </li>
                                    <li class="list-group-item mb-1 rounded-3 p-1" id="{{ isset($page) && $page === 'Extra Hours' ? 'sidebar-active' : 'sidebar-link' }}">
                                        <a href="{{ route('extra.hours') }}" class="text-decoration-none text-light">
                                            <i class="fa-solid fa-business-time mx-2 opacity-75"></i> {{ __('pages.nav_extra_hours') }}
                                        </a>
                                    </li>
                                @endif
                                @if(session('role') === 'company' || session('role') === 'hr' || session('role') === 'employee')
                                    <li class="list-group-item mb-1 rounded-3 p-1" id="{{ isset($page) && $page === 'Contracts' ? 'sidebar-active' : 'sidebar-link' }}">
                                        <a href="{{ route('contracts') }}" class="text-decoration-none text-light">
                                            <i class="fa-solid fa-file-contract mx-2 opacity-75"></i> {{ __('pages.nav_contracts') }}
                                        </a>
                                    </li>
                                @endif
                                @if(session('role') === 'company' || session('role') === 'hr' || session('role') === 'employee' || session('role') === 'manager')
                                    <li class="list-group-item mb-1 rounded-3 p-1" id="{{ isset($page) && $page === 'Vacations' ? 'sidebar-active' : 'sidebar-link' }}">
                                        <a href="{{ route('vacations') }}" class="text-decoration-none text-light">
                                            <i class="fa-solid fa-calendar-days mx-2 opacity-75"></i> {{ __('pages.nav_vacations') }}
                                        </a>
                                    </li>
                                @endif
                                @if(session('role') === 'company' || session('role') === 'hr' || session('role') === 'employee' || session('role') === 'manager')
                                    <li class="list-group-item mb-1 rounded-3 p-1" id="{{ isset($page) && $page === 'Employee Report' ? 'sidebar-active' : 'sidebar-link' }}">
                                        <a href="{{ route('employee.report') }}" class="text-decoration-none text-light">
                                            <i class="fa-solid fa-file-alt mx-2 opacity-75"></i> {{ __('pages.nav_employee_report') }}
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
                                        @if(session('role') == 'admin')
                                            <small class="d-block opacity-50 fs-small col-12 text-truncate text-capitalize" title="{{ __('pages.role_admin') }}">{{ __('pages.role_admin') }}</small>
                                        @elseif(session('role') == 'company')
                                            <small class="d-block opacity-50 fs-small col-12 text-truncate text-capitalize" title="{{ __('pages.role_company') }}">{{ __('pages.role_company') }}</small>
                                        @elseif(session('role') == 'manager')
                                            <small class="d-block opacity-50 fs-small col-12 text-truncate text-capitalize" title="{{ __('pages.role_manager') }}">{{ __('pages.role_manager') }}</small>
                                        @elseif(session('role') == 'employee')
                                            <small class="d-block opacity-50 fs-small col-12 text-truncate text-capitalize" title="{{ __('pages.role_employee') }}">{{ __('pages.role_employee') }}</small>
                                        @elseif(session('role') == 'finance')
                                            <small class="d-block opacity-50 fs-small col-12 text-truncate text-capitalize" title="{{ __('pages.role_finance') }}">{{ __('pages.role_finance') }}</small>
                                        @elseif(session('role') == 'hr')
                                            <small class="d-block opacity-50 fs-small col-12 text-truncate text-capitalize" title="{{ __('pages.role_hr') }}">{{ __('pages.role_hr') }}</small>
                                        @endif
                                    </div>
                                    <div class="col-3">
                                        <a href="{{ route('logout') }}" class="btn btn-darkblue rounded-3 btn-sm"><i class="fa-solid fa-right-from-bracket text-danger"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </aside>

                <div class="col-sm-12 col-md-12 col-lg-10 bg-custom vh-100 overflow-y-auto overflow-x-hidden p-0">

                    {{-- Topbar --}}
                    <header class="pb-3 bg-white sticky-top py-3 px-4 d-print-none">
                        <div class="row align-items-center">
                            <div class="col-6 col-md-4 col-lg-6">
                                <div class="d-flex align-items-center gap-2">

                                    {{-- Dropdown Menu for SM Devices (synced with desktop sidebar) --}}
                                    <div class="dropdown d-lg-none">
                                        <button class="btn bg-custom rounded-3 border" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fa-solid fa-list-ul"></i> <span class="d-none d-md-inline">{{ __('pages.nav_menu') }}</span>
                                        </button>
                                        <ul class="dropdown-menu navbar-dropdown-menu rounded-3 shadow-sm p-2">
                                            <li><span class="dropdown-item-text fw-semibold">{{ __('pages.nav_overview') }}</span></li>
                                            <li><a class="dropdown-item rounded-3 mb-1 {{ isset($page) && $page === 'Dashboard' ? 'bg-darkblue text-light' : '' }}" href="{{ route('dashboard') }}"><i class="fa-solid fa-chart-column me-2 opacity-75"></i> {{ __('pages.nav_dashboard') }}</a></li>
                                            @if(session('role') && session('role') !== 'admin')
                                                <li><a class="dropdown-item rounded-3 mb-1 {{ isset($page) && $page === 'Notifications' ? 'bg-darkblue text-light' : '' }}" href="{{ route('notifications') }}"><i class="fa-regular fa-bell me-2 opacity-75"></i> {{ __('pages.nav_notifications') }}</a></li>
                                            @endif

                                            @if(session('role') === 'admin')
                                                <li><a class="dropdown-item rounded-3 mb-1 {{ isset($page) && $page === 'Packages' ? 'bg-darkblue text-light' : '' }}" href="{{ route('packages') }}"><i class="fa-brands fa-trello me-2 opacity-75"></i> {{ __('pages.nav_packages') }}</a></li>
                                                <li><a class="dropdown-item rounded-3 mb-1 {{ isset($page) && $page === 'Companies' ? 'bg-darkblue text-light' : '' }}" href="{{ route('companies') }}"><i class="fa-solid fa-building me-2 opacity-75"></i> {{ __('pages.nav_companies') }}</a></li>
                                                <li><a class="dropdown-item rounded-3 mb-1 {{ isset($page) && $page === 'Payments' ? 'bg-darkblue text-light' : '' }}" href="{{ route('payments') }}"><i class="fa-solid fa-money-bill me-2 opacity-75"></i> {{ __('pages.nav_payments') }}</a></li>
                                            @endif

                                            @if(session('role') === 'company' || session('role') === 'manager')
                                                <li><a class="dropdown-item rounded-3 mb-1 {{ isset($page) && $page === 'Company News' ? 'bg-darkblue text-light' : '' }}" href="{{ route('company.news') }}"><i class="fa-solid fa-newspaper me-2 opacity-75"></i> {{ __('pages.nav_company_news') }}</a></li>
                                            @endif

                                            @if(session('role') === 'company' || session('role') === 'manager' || session('role') === 'employee')
                                                <li><hr class="dropdown-divider"></li>
                                                <li><span class="dropdown-item-text fw-semibold">{{ __('pages.nav_operations') }}</span></li>
                                                @if(session('role') === 'company' || session('role') === 'manager')
                                                    <li><a class="dropdown-item rounded-3 mb-1 {{ isset($page) && $page === 'Projects' ? 'bg-darkblue text-light' : '' }}" href="{{ route('projects') }}"><i class="fa-solid fa-diagram-project me-2 opacity-75"></i> {{ __('pages.nav_projects') }}</a></li>
                                                @endif
                                                @if(session('role') === 'company' || session('role') === 'manager' || session('role') === 'employee')
                                                    <li><a class="dropdown-item rounded-3 mb-1 {{ isset($page) && $page === 'Tasks' ? 'bg-darkblue text-light' : '' }}" href="{{ route('tasks') }}"><i class="fa-solid fa-list-check me-2 opacity-75"></i> {{ __('pages.nav_tasks') }}</a></li>
                                                @endif
                                                @if(session('role') === 'company' || session('role') === 'manager')
                                                    <li><a class="dropdown-item rounded-3 mb-1 {{ isset($page) && $page === 'Project Reports' ? 'bg-darkblue text-light' : '' }}" href="{{ route('project.reports') }}"><i class="fa-solid fa-chart-line me-2 opacity-75"></i> {{ __('pages.nav_project_reports') }}</a></li>
                                                @endif
                                                @if(session('role') === 'company' || session('role') === 'manager' || session('role') === 'employee')
                                                    <li><a class="dropdown-item rounded-3 mb-1 {{ isset($page) && $page === 'Task Reports' ? 'bg-darkblue text-light' : '' }}" href="{{ route('task.reports') }}"><i class="fa-solid fa-file-lines me-2 opacity-75"></i> {{ __('pages.nav_task_reports') }}</a></li>
                                                @endif
                                            @endif

                                            @if(session('role') === 'company' || session('role') === 'manager')
                                                <li><hr class="dropdown-divider"></li>
                                                <li><span class="dropdown-item-text fw-semibold">{{ __('pages.nav_materials') }}</span></li>
                                                <li><a class="dropdown-item rounded-3 mb-1 {{ isset($page) && $page === 'Materials' ? 'bg-darkblue text-light' : '' }}" href="{{ route('materials') }}"><i class="fa-solid fa-layer-group me-2 opacity-75"></i> {{ __('pages.nav_materials') }}</a></li>
                                                <li><a class="dropdown-item rounded-3 mb-1 {{ isset($page) && $page === 'Materials Stock' ? 'bg-darkblue text-light' : '' }}" href="{{ route('materials.stock') }}"><i class="fa-solid fa-boxes-stacked me-2 opacity-75"></i> {{ __('pages.nav_materials_stock') }}</a></li>
                                                <li><a class="dropdown-item rounded-3 mb-1 {{ isset($page) && $page === 'Stock Report' ? 'bg-darkblue text-light' : '' }}" href="{{ route('materials.stock.report') }}"><i class="fa-solid fa-chart-pie me-2 opacity-75"></i> {{ __('pages.nav_stock_report') }}</a></li>
                                            @endif

                                            @if(session('role') === 'company' || session('role') === 'manager' || session('role') === 'finance')
                                                <li><hr class="dropdown-divider"></li>
                                                <li><span class="dropdown-item-text fw-semibold">{{ __('pages.nav_finance') }}</span></li>
                                                <li><a class="dropdown-item rounded-3 mb-1 {{ isset($page) && $page === 'Clients' ? 'bg-darkblue text-light' : '' }}" href="{{ route('clients') }}"><i class="fa-solid fa-users-viewfinder me-2 opacity-75"></i> {{ __('pages.nav_clients') }}</a></li>
                                                <li><a class="dropdown-item rounded-3 mb-1 {{ isset($page) && $page === 'Suppliers' ? 'bg-darkblue text-light' : '' }}" href="{{ route('suppliers') }}"><i class="fa-solid fa-truck me-2 opacity-75"></i> {{ __('pages.nav_suppliers') }}</a></li>
                                                @if(session('role') === 'company' || session('role') === 'finance')
                                                    <li><a class="dropdown-item rounded-3 mb-1 {{ isset($page) && $page === 'Revenue & Expenses' ? 'bg-darkblue text-light' : '' }}" href="{{ route('financial.transactions') }}"><i class="fa-solid fa-wallet me-2 opacity-75"></i> {{ __('pages.nav_revenue') }}</a></li>
                                                    <li><a class="dropdown-item rounded-3 mb-1 {{ isset($page) && $page === 'Financial Report' ? 'bg-darkblue text-light' : '' }}" href="{{ route('financial.report') }}"><i class="fa-solid fa-chart-pie me-2 opacity-75"></i> {{ __('pages.nav_financial_report') }}</a></li>
                                                    <li><a class="dropdown-item rounded-3 mb-1 {{ isset($page) && $page === 'Offers' ? 'bg-darkblue text-light' : '' }}" href="{{ route('offers') }}"><i class="fa-solid fa-file-invoice-dollar me-2 opacity-75"></i> {{ __('pages.nav_offers') }}</a></li>
                                                    <li><a class="dropdown-item rounded-3 mb-1 {{ isset($page) && $page === 'Create Offer' ? 'bg-darkblue text-light' : '' }}" href="{{ route('create.offer') }}"><i class="fa-solid fa-file-invoice-dollar me-2 opacity-75"></i> {{ __('pages.nav_create_offer') }}</a></li>
                                                    <li><a class="dropdown-item rounded-3 mb-1 {{ isset($page) && $page === 'Calculate Salary' ? 'bg-darkblue text-light' : '' }}" href="{{ route('calculate.salary') }}"><i class="fa-solid fa-calculator me-2 opacity-75"></i> {{ __('pages.nav_calculate_salary') }}</a></li>
                                                    <li><a class="dropdown-item rounded-3 mb-1 {{ isset($page) && $page === 'Salaries' ? 'bg-darkblue text-light' : '' }}" href="{{ route('salaries') }}"><i class="fa-solid fa-money-bill-wave me-2 opacity-75"></i> {{ __('pages.nav_salaries') }}</a></li>
                                                @endif
                                            @endif

                                            @if(session('role') === 'company' || session('role') === 'manager' || session('role') === 'hr' || session('role') === 'employee')
                                                <li><hr class="dropdown-divider"></li>
                                                <li><span class="dropdown-item-text fw-semibold">{{ __('pages.nav_staff') }}</span></li>
                                            @endif
                                            @if(session('role') === 'company' || session('role') === 'hr')
                                                <li><a class="dropdown-item rounded-3 mb-1 {{ isset($page) && $page === 'Employees' ? 'bg-darkblue text-light' : '' }}" href="{{ route('employees') }}"><i class="fa-solid fa-users me-2 opacity-75"></i> {{ __('pages.nav_employees') }}</a></li>
                                            @endif
                                            @if(session('role') === 'company' || session('role') === 'manager' || session('role') === 'hr')
                                                <li><a class="dropdown-item rounded-3 mb-1 {{ isset($page) && $page === 'Check In' ? 'bg-darkblue text-light' : '' }}" href="{{ route('checkins') }}"><i class="fa-solid fa-user-check me-2 opacity-75"></i> {{ __('pages.nav_checkin') }}</a></li>
                                            @endif
                                            @if(session('role') === 'company' || session('role') === 'manager' || session('role') === 'hr' || session('role') === 'employee')
                                                <li><a class="dropdown-item rounded-3 mb-1 {{ isset($page) && $page === 'Working Hours' ? 'bg-darkblue text-light' : '' }}" href="{{ route('working.hours') }}"><i class="fa-solid fa-clock me-2 opacity-75"></i> {{ __('pages.nav_working_hours') }}</a></li>
                                            @endif
                                            @if(session('role') === 'company' || session('role') === 'manager' || session('role') === 'hr')
                                                <li><a class="dropdown-item rounded-3 mb-1 {{ isset($page) && $page === 'Extra Hours' ? 'bg-darkblue text-light' : '' }}" href="{{ route('extra.hours') }}"><i class="fa-solid fa-business-time me-2 opacity-75"></i> {{ __('pages.nav_extra_hours') }}</a></li>
                                            @endif
                                            @if(session('role') === 'employee')
                                                <li><a class="dropdown-item rounded-3 mb-1 {{ isset($page) && $page === 'Salaries' ? 'bg-darkblue text-light' : '' }}" href="{{ route('salaries') }}"><i class="fa-solid fa-money-bill-wave me-2 opacity-75"></i> {{ __('pages.nav_salaries') }}</a></li>
                                                <li><a class="dropdown-item rounded-3 mb-1 {{ isset($page) && $page === 'Extra Hours' ? 'bg-darkblue text-light' : '' }}" href="{{ route('extra.hours') }}"><i class="fa-solid fa-business-time me-2 opacity-75"></i> {{ __('pages.nav_extra_hours') }}</a></li>
                                            @endif
                                            @if(session('role') === 'company' || session('role') === 'hr' || session('role') === 'employee')
                                                <li><a class="dropdown-item rounded-3 mb-1 {{ isset($page) && $page === 'Contracts' ? 'bg-darkblue text-light' : '' }}" href="{{ route('contracts') }}"><i class="fa-solid fa-file-contract me-2 opacity-75"></i> {{ __('pages.nav_contracts') }}</a></li>
                                            @endif
                                            @if(session('role') === 'company' || session('role') === 'hr' || session('role') === 'employee' || session('role') === 'manager')
                                                <li><a class="dropdown-item rounded-3 mb-1 {{ isset($page) && $page === 'Vacations' ? 'bg-darkblue text-light' : '' }}" href="{{ route('vacations') }}"><i class="fa-solid fa-calendar-days me-2 opacity-75"></i> {{ __('pages.nav_vacations') }}</a></li>
                                            @endif
                                            @if(session('role') === 'company' || session('role') === 'hr' || session('role') === 'employee' || session('role') === 'manager')
                                                <li><a class="dropdown-item rounded-3 mb-1 {{ isset($page) && $page === 'Employee Report' ? 'bg-darkblue text-light' : '' }}" href="{{ route('employee.report') }}"><i class="fa-solid fa-file-alt me-2 opacity-75"></i> {{ __('pages.nav_employee_report') }}</a></li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="d-none d-md-inline col-md-4 d-lg-none text-center">
                                <a href="{{ route('dashboard') }}" class="app-logo app-logo--header">
                                    <img src="{{ asset('images/marketing/workom_logo.png') }}" alt="WORKOM">
                                </a>
                            </div>
                            <div class="col-6 col-md-4 col-lg-6 text-end">
                                @php
                                    $profileName = session('user') ?: session('company');
                                @endphp

                                @if(session('role') && session('role') !== 'admin')
                                    <div class="dropdown d-inline-block me-2 notifications-dropdown">
                                        <button class="btn bg-custom rounded-3 border position-relative notifications-bell-btn" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" aria-label="{{ __('pages.nav_notifications') }}" title="{{ __('pages.nav_notifications') }}">
                                            <i class="fa-regular fa-bell"></i>
                                            @if(($topbarUnreadNotifications ?? 0) > 0)
                                                <span class="notifications-bell-badge">{{ $topbarUnreadNotifications > 99 ? '99+' : $topbarUnreadNotifications }}</span>
                                            @endif
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end notifications-panel shadow border-0">
                                            <div class="notifications-panel__header">
                                                <div>
                                                    <span class="notifications-panel__title">{{ __('pages.nav_notifications') }}</span>
                                                    <span class="notifications-panel__count">{{ $topbarUnreadNotifications }} {{ __('pages.notif_unread') }}</span>
                                                </div>
                                                @if(($topbarUnreadNotifications ?? 0) > 0)
                                                    <form action="{{ route('notifications.mark.all') }}" method="post">
                                                        @csrf
                                                        <button type="submit" class="notifications-panel__mark-all">{{ __('pages.notif_mark_all') }}</button>
                                                    </form>
                                                @endif
                                            </div>
                                            <div class="notifications-panel__list">
                                                @forelse($topbarNotifications ?? [] as $notification)
                                                    <form action="{{ route('notifications.open', $notification->id) }}" method="post">
                                                        @csrf
                                                        <button type="submit" class="notifications-item {{ !(int) $notification->is_read ? 'is-unread' : '' }}">
                                                            <span class="notifications-item__icon"><i class="fa-regular fa-bell"></i></span>
                                                            <span class="notifications-item__body">
                                                                <span class="notifications-item__title">{{ $notification->title }}</span>
                                                                <span class="notifications-item__message">{{ $notification->message }}</span>
                                                                <span class="notifications-item__time">{{ $notification->created_at ? \Carbon\Carbon::parse($notification->created_at)->format('d/m/Y H:i') : '' }}</span>
                                                            </span>
                                                            @if(!(int) $notification->is_read)
                                                                <span class="notifications-item__dot"></span>
                                                            @endif
                                                        </button>
                                                    </form>
                                                @empty
                                                    <div class="notifications-panel__empty">
                                                        <i class="fa-regular fa-bell-slash"></i>
                                                        <span>{{ __('pages.notif_empty') }}</span>
                                                    </div>
                                                @endforelse
                                            </div>
                                            <div class="notifications-panel__footer">
                                                <a href="{{ route('notifications') }}" class="notifications-panel__all-link">{{ __('pages.notif_view_all') }}</a>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                {{-- Profile Dropdown --}}
                                <div class="dropdown d-inline-block">
                                    <button class="btn bg-custom rounded-3 border dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="{{ $profileName }}">
                                        <i class="fa-regular fa-user"></i> <span class="d-none d-md-inline">{{ $profileName }}</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end navbar-dropdown-menu rounded-3 shadow-sm p-2">
                                        <span class="d-block px-3 fw-semibold text-wrap">{{ $profileName }}</span>
                                        <small class="d-block border-bottom pb-2 ps-3 mb-2 fs-small opacity-75 text-capitalize text-wrap">
                                            @if(session('role') == 'admin')
                                                {{ __('pages.role_admin') }}
                                            @elseif(session('role') == 'company')
                                                {{ __('pages.role_company') }}
                                            @elseif(session('role') == 'manager')
                                                {{ __('pages.role_manager') }}
                                            @elseif(session('role') == 'employee')
                                                {{ __('pages.role_employee') }}
                                            @elseif(session('role') == 'finance')
                                                {{ __('pages.role_finance') }}
                                            @elseif(session('role') == 'hr')
                                                {{ __('pages.role_hr') }}
                                            @endif
                                        </small>
                                        <li><a class="dropdown-item rounded-3 mb-1 {{ isset($page) && $page === 'My Profile' ? 'bg-darkblue text-light' : '' }}" href="{{ route('profile') }}"><i class="fa-regular fa-circle-user"></i> {{ __('pages.nav_profile') }}</a></li>
                                        <li><a class="dropdown-item rounded-3 text-danger" href="{{ route('logout') }}"><i class="fa-solid fa-right-from-bracket"></i> {{ __('pages.nav_logout') }}</a></li>
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
                        <strong class="me-auto text-light">{{ __('pages.toast_notice') }}</strong>
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
                        <strong class="me-auto text-light">{{ __('pages.toast_notice') }}</strong>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body text-dark border border-danger">
                        {!! \Session::get('error') !!}
                    </div>
                </div>
            </div>
        @endif

        {{-- Report image lightbox (Bootstrap modal header close) --}}
        <div class="modal fade" id="reportImageModal" tabindex="-1" aria-hidden="true" aria-labelledby="reportImageModalLabel">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content report-lightbox-content border-0">
                    <div class="modal-header border-0 py-2 px-3">
                        <h2 class="modal-title fs-6 text-black mb-0" id="reportImageModalLabel">{{ __('pages.report_image') }}</h2>
                        <button type="button" class="btn-close btn-close-black" data-bs-dismiss="modal" aria-label="{{ __('pages.close') }}"></button>
                    </div>
                    <div class="modal-body text-center pt-0 pb-3 px-3">
                        <img src="" alt="{{ __('pages.report_image') }}" id="reportLightboxImg" class="report-lightbox-img img-fluid rounded-3">
                    </div>
                </div>
            </div>
        </div>

        {{-- Loader JS --}}
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                const loader = document.getElementById("page-loader");
                setTimeout(() => loader.classList.add("hide"), 100);
                setTimeout(() => loader.remove(), 500);

                const reportModalEl = document.getElementById('reportImageModal');
                const reportImg = document.getElementById('reportLightboxImg');
                if (reportModalEl && reportImg && window.bootstrap) {
                    const reportModal = bootstrap.Modal.getOrCreateInstance(reportModalEl);
                    document.querySelectorAll('[data-report-lightbox]').forEach((link) => {
                        link.addEventListener('click', (e) => {
                            e.preventDefault();
                            const src = link.getAttribute('href');
                            if (!src) return;
                            reportImg.src = src;
                            reportModal.show();
                        });
                    });
                    reportModalEl.addEventListener('hidden.bs.modal', () => {
                        reportImg.src = '';
                    });
                }
            });
        </script>

        @stack('scripts')
    </body>
</html>
