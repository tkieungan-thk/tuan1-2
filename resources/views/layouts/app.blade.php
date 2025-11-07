<!DOCTYPE html>
<html lang="en">

@include('layouts.partials.header')

<body>

    <div class="header-bg">
        <header id="topnav">
            <div class="topbar-main">
                <div class="container-fluid">
                    <div>
                        <a href="index.html" class="logo">
                            <span class="logo-light">
                                <i class="mdi mdi-camera-control"></i> Stexo
                            </span>
                        </a>
                    </div>
                    <div class="menu-extras topbar-custom navbar p-0">
                        <ul class="list-inline d-none d-lg-block mb-0">
                            <li class="hide-phone app-search float-left">
                                <form role="search" class="app-search">
                                    <div class="form-group mb-0">
                                        <input type="text" class="form-control" placeholder="Search..">
                                        <button type="submit"><i class="fa fa-search"></i></button>
                                    </div>
                                </form>
                            </li>
                        </ul>

                        <ul class="navbar-right ml-auto list-inline float-right mb-0">
                            <!-- language-->
                            <li class="dropdown notification-list list-inline-item d-none d-md-inline-block">
                                <a class="nav-link dropdown-toggle arrow-none waves-effect" data-toggle="dropdown"
                                    href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                    <img src="assets/images/flags/us_flag.jpg" class="mr-2" height="12"
                                        alt="" />
                                    English <span class="mdi mdi-chevron-down"></span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-animated language-switch">
                                    <a class="dropdown-item" href="#"><img
                                            src="assets/images/flags/french_flag.jpg" alt=""
                                            height="16" /><span> French </span></a>
                                    <a class="dropdown-item" href="#"><img
                                            src="assets/images/flags/spain_flag.jpg" alt=""
                                            height="16" /><span> Spanish </span></a>
                                    <a class="dropdown-item" href="#"><img
                                            src="assets/images/flags/russia_flag.jpg" alt=""
                                            height="16" /><span> Russian </span></a>
                                    <a class="dropdown-item" href="#"><img
                                            src="assets/images/flags/germany_flag.jpg" alt=""
                                            height="16" /><span> German </span></a>
                                    <a class="dropdown-item" href="#"><img
                                            src="assets/images/flags/italy_flag.jpg" alt=""
                                            height="16" /><span> Italian </span></a>
                                </div>
                            </li>

                            <!-- full screen -->
                            <li class="dropdown notification-list list-inline-item d-none d-md-inline-block">
                                <a class="nav-link waves-effect" href="#" id="btn-fullscreen">
                                    <i class="mdi mdi-arrow-expand-all noti-icon"></i>
                                </a>
                            </li>

                            <li class="dropdown notification-list list-inline-item">
                                <div class="dropdown notification-list nav-pro-img">

                                    <a class="dropdown-toggle nav-link arrow-none nav-user" data-toggle="dropdown"
                                        href="#" role="button" aria-haspopup="false" aria-expanded="false">

                                        <img src="{{ asset('images/logo-light.png') }}" alt="user"
                                            class="rounded-circle" height="36">
                                        @auth
                                            <span class="ml-2 text-white">{{ Auth::user()->name }}</span>
                                        @endauth
                                    </a>

                                    <div class="dropdown-menu dropdown-menu-right profile-dropdown ">
                                        <!-- item-->
                                        <a class="dropdown-item" href="{{ route('password.edit') }}"><i
                                                class="mdi mdi-account-circle"></i>
                                            Profile</a>
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

                            <li class="menu-item dropdown notification-list list-inline-item">
                                <!-- Mobile menu toggle-->
                                <a class="navbar-toggle nav-link">
                                    <div class="lines">
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                    </div>
                                </a>
                                <!-- End mobile menu toggle-->
                            </li>

                        </ul>

                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>

            @include('layouts.partials.navbar')

        </header>
    </div>
    <div class="wrapper">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert" id="alert-success">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        @yield('content');
    </div>

    @include('layouts.partials.footer')

    @include('layouts.partials.script')

</body>

</html>
