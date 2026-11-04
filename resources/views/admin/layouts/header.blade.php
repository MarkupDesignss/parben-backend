<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    @yield('title')

    <title>Paraben Corporations Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <link href="{{config('app.url')}}/assets/images/favicon.png" rel="shortcut icon" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" />
    <link rel="stylesheet" type="text/css" href="{{config('app.url')}}/assets/css/style.css" />
    <link rel="stylesheet" type="text/css" href="{{config('app.url')}}/assets/css/dashboard-style.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    @yield('styles')

   <style>
    ul.navbar-nav.sidenav-toggler {
        position: absolute;
        top: 14px;
        right: 12px;
        width: 30px;
        margin-bottom: 0;
        margin-top: 0;
    }

    .fixed-nav.sidenav-toggled ul.navbar-nav.sidenav-toggler {
        position: relative;
        top: 0;
        right: 0;
        width: 30px;
        margin-bottom: 10px;
        margin-top: 5px;
    }

    .nav-bar-logo {
        padding: 4px 4px 0px 4px;
        background-color: #fff;
        display: flex;
        justify-content: center;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    #mainNav .navbar-brand {
        width: fit-content !important;
        margin: 0;
    }

    /* ============================================
       SCROLLABLE SIDEBAR
       ============================================ */
    .navbar-sidenav {
        max-height: 100vh;
        overflow-y: auto !important;
        overflow-x: hidden !important;
        padding-bottom: 80px;
        scrollbar-width: thin;
        scrollbar-color: rgba(255, 255, 255, 0.4) transparent;
    }

    .navbar-sidenav::-webkit-scrollbar {
        width: 6px;
    }

    .navbar-sidenav::-webkit-scrollbar-track {
        background: transparent;
    }

    .navbar-sidenav::-webkit-scrollbar-thumb {
        background-color: rgba(255, 255, 255, 0.35);
        border-radius: 10px;
    }

    .navbar-sidenav::-webkit-scrollbar-thumb:hover {
        background-color: rgba(255, 255, 255, 0.6);
    }

    /* ============================================
       SIDEBAR LINKS — WHITE TEXT
       ============================================ */
    .navbar-sidenav .nav-link,
    .navbar-sidenav .nav-link:link,
    .navbar-sidenav .nav-link:visited,
    .navbar-sidenav .nav-link:focus,
    .navbar-sidenav .nav-link:hover,
    .navbar-sidenav .nav-link.active {
        color: #ffffff !important;
    }

    .navbar-sidenav .nav-link i,
    .navbar-sidenav .nav-link .nav-link-text {
        color: #ffffff !important;
    }

    /* ============================================
       CONTENT MANAGEMENT COLLAPSE
       ============================================ */
    .cm-chevron {
        transition: transform 0.25s ease;
        font-size: 11px;
        margin-left: auto;
        color: #ffffff !important;
    }

    .navbar-sidenav .nav-link[aria-expanded="true"] .cm-chevron {
        transform: rotate(180deg);
    }

    #contentManagementMenu .nav-link,
    #contentManagementMenu .nav-link:link,
    #contentManagementMenu .nav-link:visited,
    #contentManagementMenu .nav-link:focus,
    #contentManagementMenu .nav-link:hover,
    #contentManagementMenu .nav-link.active,
    #contentManagementMenu .nav-link i,
    #contentManagementMenu .nav-link .nav-link-text {
        color: #ffffff !important;
    }

    #contentManagementMenu .nav-link {
        padding-left: 42px;
        font-size: 14px;
        opacity: 0.92;
        border-radius: 8px;
        margin: 2px 8px;
        transition: all 0.2s ease;
    }

    #contentManagementMenu .nav-link:hover {
        opacity: 1;
        background: rgba(255, 255, 255, 0.12);
    }

    #contentManagementMenu .nav-link.active {
        opacity: 1;
        font-weight: 600;
        background: rgba(255, 255, 255, 0.18);
    }

    #exampleAccordion .collapse {
        background: transparent;
    }
</style>
</head>
<body class="fixed-nav" id="page-top">
    <div class="content-wrapper">
        <nav class="navbar navbar-expand-lg navbar-dark" id="mainNav">

            <div class="nav-bar-mob-logo">
                <a class="navbar-brand" href="{{route('admin.dashboard')}}"><img src="{{config('app.url')}}/assets/images/logo1.png" alt="" /></a>
                
            </div>

            <button class="navbar-toggler x navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav navbar-sidenav" id="exampleAccordion">

                    <div class="nav-bar-logo">
                        <!--<a class="navbar-brand" href="{{route('admin.dashboard')}}"><img src="{{config('app.url')}}/assets/images/logo1.png" style="width:120px" alt="" /></a>-->
                        <a class="navbar-brand" href="{{route('admin.dashboard')}}">
                             <img src="{{ $logo && $logo->image_url 
                                            ? $logo->image_url 
                                            : config('app.url').'/assets/images/logo1.jpg' }}"
                                 alt="logo" />
                        </a>

                        <!--<ul class="navbar-nav sidenav-toggler">-->
                        <!--    <li class="nav-item">-->
                        <!--        <a class="nav-link text-center" id="sidenavToggler">-->
                        <!--            <i><img src="{{config('app.url')}}/assets/images/menu-icon.svg" alt="" /></i>-->
                        <!--        </a>-->
                        <!--    </li>-->
                        <!--</ul>-->
                    </div>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{route('admin.dashboard')}}">
                            <i class="me-2"><img src="{{config('app.url')}}/assets/images/dashboard.svg" alt="" /></i>
                            <span class="nav-link-text">Dashboard</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.cases.*') ? 'active' : '' }}" href="{{route('admin.cases.index')}}">
                            <i class="me-2"><img src="{{config('app.url')}}/assets/images/document-error.svg" alt="" /></i>
                            <span class="nav-link-text">Router Logs</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.router-devices.*') ? 'active' : '' }}" href="{{route('admin.router-devices.index')}}">
                            <i class="me-2 fas fa-wifi"></i>
                            <span class="nav-link-text">Router Devices</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.unsupported-devices.*') ? 'active' : '' }}" href="{{route('admin.unsupported-devices.index')}}">
                            <i class="me-2 fas fa-exclamation-triangle"></i>
                            <span class="nav-link-text">
                                Unsupported Devices
                                @php $pendingUnsupported = \App\Models\UnsupportedDeviceReport::where('status','pending')->count(); @endphp
                                <!--@if($pendingUnsupported > 0)-->
                                <!--    <span class="badge bg-danger ms-1">{{ $pendingUnsupported }}</span>-->
                                <!--@endif-->
                            </span>
                        </a>
                    </li>

                    @if(auth()->user()->role == 'admin')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{route('admin.users.index')}}">
                            <i class="me-2"><img src="{{config('app.url')}}/assets/images/administrator.svg" alt="" /></i>
                            <span class="nav-link-text">User Manager</span>
                        </a>
                    </li>

                    <!--<li class="nav-item">-->
                    <!--    <a class="nav-link {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}" href="{{route('admin.pages.index')}}">-->
                    <!--        <i class="me-2"><img src="{{config('app.url')}}/assets/images/settings.svg" alt="" /></i>-->
                    <!--        <span class="nav-link-text">Pages</span>-->
                    <!--    </a>-->
                    <!--</li>-->

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.tokens.packs.*') ? 'active' : '' }}" href="{{ route('admin.tokens.packs.index') }}">
                            <i class="me-2 fas fa-coins"></i>
                            <span class="nav-link-text">Token Packs</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.tokens.transactions.*') ? 'active' : '' }}" href="{{ route('admin.tokens.transactions.index') }}">
                            <i class="me-2 fas fa-history"></i>
                            <span class="nav-link-text">Token Transactions</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.stripe.*') ? 'active' : '' }}"
                           href="{{ route('admin.stripe.edit') }}">
                            <i class="me-2 fas fa-credit-card"></i>
                            <span class="nav-link-text">
                                Stripe Settings
                                @if(\App\Services\StripeConfig::isLive())
                                    <span class="badge bg-danger ms-1">LIVE</span>
                                @endif
                            </span>
                        </a>
                    </li>

                    {{-- ============ CONTENT MANAGEMENT (Collapsible) ============ --}}
                    <!--<li class="nav-item">-->
                    <!--    <a class="nav-link d-flex align-items-center {{ request()->routeIs('admin.contents.*') ? '' : 'collapsed' }}"-->
                    <!--       data-bs-toggle="collapse"-->
                    <!--       href="#contentManagementMenu"-->
                    <!--       role="button"-->
                    <!--       aria-expanded="{{ request()->routeIs('admin.contents.*') ? 'true' : 'false' }}"-->
                    <!--       aria-controls="contentManagementMenu">-->
                    <!--        <i class="me-2 fas fa-folder-open"></i>-->
                    <!--        <span class="nav-link-text">Content Management</span>-->
                    <!--        <i class="fas fa-chevron-down cm-chevron"></i>-->
                    <!--    </a>-->

                    <!--    <div class="collapse {{ request()->routeIs('admin.contents.*') ? 'show' : '' }}"-->
                    <!--         id="contentManagementMenu">-->
                    <!--        <ul class="nav flex-column">-->
                    <!--            <li class="nav-item">-->
                    <!--                <a class="nav-link {{ request()->routeIs('admin.contents.index') && request('type') === 'logo_banner' ? 'active' : '' }}"-->
                    <!--                   href="{{ route('admin.contents.index', ['type' => 'logo_banner']) }}">-->
                    <!--                    <i class="me-2 fas fa-image"></i>-->
                    <!--                    <span class="nav-link-text">Logo &amp; Banner</span>-->
                    <!--                </a>-->
                    <!--            </li>-->
                    <!--        </ul>-->
                    <!--    </div>-->
                    <!--</li>-->
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center {{ request()->routeIs('admin.contents.*') ? '' : 'collapsed' }}"
                           data-bs-toggle="collapse"
                           href="#contentManagementMenu"
                           role="button"
                           aria-expanded="{{ request()->routeIs('admin.contents.*') ? 'true' : 'false' }}"
                           aria-controls="contentManagementMenu">
                            <i class="me-2 fas fa-folder-open"></i>
                            <span class="nav-link-text">Content Management</span>
                            <i class="fas fa-chevron-down cm-chevron"></i>
                        </a>
                    
                        <div class="collapse {{ request()->routeIs('admin.contents.*') ? 'show' : '' }}"
                             id="contentManagementMenu">
                            <ul class="nav flex-column">
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.contents.index') && request('type') === 'logo_banner' ? 'active' : '' }}"
                                       href="{{ route('admin.contents.index', ['type' => 'logo_banner']) }}">
                                        <i class="me-2 fas fa-image"></i>
                                        <span class="nav-link-text">Logo &amp; Banner</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}" href="{{route('admin.pages.index')}}">
                                        <i class="me-2"><img src="{{config('app.url')}}/assets/images/settings.svg" alt="" /></i>
                                        <span class="nav-link-text">Pages</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    {{-- ============ SUPPORT TICKETS (NEW) ============ --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.helps.*') ? 'active' : '' }}"
                           href="{{ route('admin.helps.index') }}">
                            <i class="me-2 fas fa-headset"></i>
                            <span class="nav-link-text">
                                Support Tickets
                                @php $pendingHelps = \App\Models\Help::where('status', 'pending')->count(); @endphp
                                @if($pendingHelps > 0)
                                    <span class="badge bg-danger ms-1">{{ $pendingHelps }}</span>
                                @endif
                            </span>
                        </a>
                    </li>
                    {{-- ============ /SUPPORT TICKETS ============ --}}
                    @endif

                </ul>
            </div>
        </nav>

        @php
            use Illuminate\Support\Str;

            $segment = last(request()->segments());

            $pageTitles = [
                'cases' => 'Router Logs',
                'dashboard' => 'Dashboard',
                'router-devices' => 'Router Devices',
                'users' => 'User Manager-Investigator',
                'helps' => 'Start Support Ticket',
                'contacts' => 'Contact Us',
                'pages' => 'Pages',
            ];

            $pageTitle = $pageTitles[$segment] ?? Str::headline($segment);
        @endphp

        <div class="navbar_top d-flex justify-content-between p-2 align-items-center mb-4 px-3 rounded-3 bg-white shadow-sm mx-auto">
            <h1 class="page-title m-0 fw-semibold">{{ $pageTitle }}</h1>
            <div class="nav-right">
                <ul class="navbar-nav">
                    <li class="nav-item nav-profile dropdown">
                        <a class="nav-link dropdown-toggle" id="profileDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                            <!--<div class="nav-profile-img">-->
                            <!--    <span style="display: inline-block; width: 40px; height: 40px; border-radius: 50%; background: #6c63ff; color: white; text-align: center; line-height: 40px; font-weight: 600; font-size: 18px; text-transform: uppercase;">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>-->
                            <!--    <span class="availability-status online"></span>-->
                            <!--</div>-->
                            <div class="nav-profile-text">
                                <p class="mb-1">{{auth()->user()->name}}</p>
                            </div>
                        </a>
                        <div class="dropdown-menu navbar-dropdown" aria-labelledby="profileDropdown">
                            <!--<a class="dropdown-item" href="javascript:void(0)"><i class="bi bi-person-fill me-2"></i> Profile</a>-->
                            <a class="dropdown-item" href="{{route('admin.logout')}}"><i class="bi bi-exclamation-circle me-2"></i> Log Out</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        {{-- @yield('content') --}}