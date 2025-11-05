<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <title>{{ $title ?? 'Dashboard' }} | Stexo Admin</title>
    <meta content="Responsive admin theme build on top of Bootstrap 4" name="description" />
    <meta content="Themesdesign" name="author" />
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('plugins/morris/morris.css') }}">
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/metismenu.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/icons.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">

    @stack('styles')
</head>

<body>
    <div class="header-bg">
        <header id="topnav">
            <div class="topbar-main">
                <div class="container-fluid d-flex justify-content-between align-items-center">
                    <a href="{{ route('dashboard') }}" class="logo">
                        <span class="logo-light"><i class="mdi mdi-camera-control"></i> Stexo</span>
                    </a>

                    <div class="menu-extras topbar-custom">
                        <ul class="navbar-right list-inline float-right mb-0">
                            <li class="dropdown notification-list list-inline-item">
                                <div class="dropdown nav-pro-img">
                                    <a class="dropdown-toggle nav-link nav-user" data-toggle="dropdown" href="#"
                                        role="button">
                                        <img src="{{ asset('images/logo-light.png') }}"
                                            alt="user" class="rounded-circle">
                                        <span class="d-none d-sm-inline-block fw-semibold">
                                            {{ Auth::user()->name }}
                                        </span> </a>
                                    <div class="dropdown-menu dropdown-menu-right profile-dropdown">
                                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                            <i class="mdi mdi-account-circle"></i> Profile
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="mdi mdi-power text-danger"></i> Logout
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Navbar -->
            <div class="navbar-custom">
                <div class="container-fluid">
                    <ul class="navigation-menu">
                        <li><a href="{{ route('dashboard') }}"><i class="icon-accelerator"></i> Dashboard</a></li>
                    </ul>
                </div>
            </div>
        </header>
    </div>

    <div class="wrapper">
        <div class="container-fluid mt-4">
            @yield('content')
        </div>
    </div>

    <footer class="footer text-center">
        © {{ date('Y') }} Stexo - Crafted with <i class="mdi mdi-heart text-danger"></i> by Themesdesign.
    </footer>

    <!-- JS -->
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.slimscroll.js') }}"></script>
    <script src="{{ asset('assets/js/waves.min.js') }}"></script>
    <script src="{{ asset('plugins/morris/morris.min.js') }}"></script>
    <script src="{{ asset('plugins/raphael/raphael.min.js') }}"></script>
    <script src="{{ asset('assets/pages/dashboard.init.js') }}"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>

    @stack('scripts')
</body>

</html>
