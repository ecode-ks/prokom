<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Construction Flow | Register</title>

        {{-- Assets --}}
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link rel="stylesheet" href="{{ asset('assets/custom.css') }}">
    </head>
    <body>
        <div class="container-fluid">
            <div class="row vh-100">

                {{-- Entrance Banner --}}
                <div class="col-12 col-md-12 col-lg-6 d-none d-md-none d-lg-flex flex-column justify-content-center bg-darkblue text-light vh-100 p-5">
                    <div class="d-flex align-items-center">
                        <div class="me-2 rounded-3 bg-lightblue p-custom border-lightblue">
                            <i class="fa-solid fa-city"></i>
                        </div>
                        <div>
                            <span class="d-block fw-semibold">Construction Flow</span>
                            <small class="d-block opacity-50">Management Suite</small>
                        </div>
                    </div>
                    <div class="mt-4">
                        <span class="badge rounded-pill bg-success bg-opacity-10 shadow-sm text-light px-3 py-2 fw-semibold">
                            <i class="fa-regular fa-circle-dot me-2 text-success"></i>
                            Trusted by <strong>10+</strong> Companies
                        </span>
                    </div>
                    <div class="mt-5">
                        <h1 class="display-4 fw-bold col-8">Staff, Projects and Finances <span class="opacity-50">- in one hand.</span></h1>
                        <p class="lead opacity-75 col-7 mt-5">Employees, working hours, holidays, contracts, wages, tasks and reports. A quiet, thoughtful system.</p>
                    </div>
                    <div class="mt-4">
                        <div class="d-flex justify-content-between opacity-50">
                            <small>&copy; {{ date('Y') }} Construction Flow</small>
                            <small><i class="fa-solid fa-shield-alt"></i> ISO 27001</small>
                        </div>
                    </div>
                </div>

                {{-- Registration Form --}}
                <div class="col-12 col-md-12 col-lg-6 bg-custom vh-100 overflow-y-auto">
                    <div class="col-12 col-md-8 mx-auto py-5">
                        <span class="txt-darkblue fw-semibold text-uppercase">Welcome to C-Flow</span>
                        <h1 class="fw-bold col-12 mt-3">Create an account for your Company</h1>
                        <form action="{{ route('register.company') }}" method="post" class="mt-4" onsubmit="handleSubmit(event)">
                            @csrf
                            <div class="mb-3">
                                <label for="company" class="form-label text-uppercase small fw-semibold">Company Name</label>
                                <input type="text" class="form-control shadow-sm py-custom rounded-3" id="company" name="company" value="{{ old('company') }}" placeholder="Enter your company name">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label text-uppercase small fw-semibold">Email</label>
                                <input type="email" class="form-control shadow-sm py-custom rounded-3" id="email" name="email" value="{{ old('email') }}" placeholder="Enter your email">
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label text-uppercase small fw-semibold">Phone</label>
                                <input type="tel" class="form-control shadow-sm py-custom rounded-3" id="phone" name="phone" value="{{ old('phone') }}" placeholder="Enter your phone number">
                            </div>
                            <div class="mb-3">
                                <label for="location" class="form-label text-uppercase small fw-semibold">Location</label>
                                <input type="text" class="form-control shadow-sm py-custom rounded-3" id="location" name="location" value="{{ old('location') }}" placeholder="Enter your location">
                            </div>
                            <div class="mb-3">
                                <label for="username" class="form-label text-uppercase small fw-semibold">Username</label>
                                <input type="text" class="form-control shadow-sm py-custom rounded-3" id="username" name="username" value="{{ old('username') }}" placeholder="Enter your username">
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label text-uppercase small fw-semibold">Password</label>
                                <input type="password" class="form-control shadow-sm py-custom rounded-3" id="password" name="password" value="{{ old('password') }}" placeholder="Enter your password">
                            </div>
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label text-uppercase small fw-semibold">Confirm Password</label>
                                <input type="password" class="form-control shadow-sm py-custom rounded-3" id="password_confirmation" name="password_confirmation" value="{{ old('password_confirmation') }}" placeholder="Confirm your password">
                            </div>
                            <button type="submit" id="submitBtn" class="btn btn-darkblue w-100 rounded-3 py-custom fw-semibold">Register <i class="fa-solid fa-angle-right"></i></button>
                        </form>
                        <span class="opacity-50 small mt-4 d-block text-center">---------- OR ----------</span>
                        <div class="mt-4 text-center">
                            <a href="{{ route('login') }}" class="text-decoration-none txt-darkblue fw-semibold small"><span class="text-dark text-muted">Do you have an account?</span> Log in now!</a>
                        </div>
                        <div class="mt-4 text-center d-block d-md-none d-lg-none">
                            <span class="badge rounded-pill bg-success bg-opacity-10 shadow-sm text-success border border-success px-3 py-2 fw-semibold">
                                <i class="fa-regular fa-circle-dot me-2 text-success"></i>
                                Trusted by <strong>10+</strong> Companies
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Alerts -->
        @if (\Session::has('success'))
            <div class="toast-container position-fixed bottom-0 end-0 p-3">
                <div id="successToast" class="toast fade show" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header bg-success border-success">
                        <strong class="me-auto text-light">Success!</strong>
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
                        <strong class="me-auto text-light">Error!</strong>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body text-dark border border-danger">
                        {!! \Session::get('error') !!}
                    </div>
                </div>
            </div>
        @endif

        {{-- Disable submit button --}}
        <script>
            function handleSubmit(event) {
                const submitBtn = document.getElementById('submitBtn');
                submitBtn.disabled = true;
            }
        </script>
    </body>
</html>
