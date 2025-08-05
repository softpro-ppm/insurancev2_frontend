<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Insurance Management System 2.0')</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <!-- Remove Vite for now to avoid conflicts -->
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
    
    @stack('styles')
</head>
<body class="light-theme">
    <!-- Top Navigation Bar -->
    <nav class="top-nav">
        <div class="nav-left">
            <div class="logo">
                <i class="fas fa-shield-alt"></i>
                <span>Insurance MS 2.0</span>
            </div>
        </div>
        <div class="nav-right">
            <button class="theme-toggle" id="themeToggle">
                <i class="fas fa-moon"></i>
            </button>
            <button class="add-policy-btn" id="addPolicyBtn">
                <i class="fas fa-plus"></i>
                Add New Policy
            </button>
            <div class="profile-dropdown">
                <button class="profile-btn" id="profileBtn">
                    <i class="fas fa-user-circle"></i>
                    <span>{{ Auth::user()->name ?? 'Admin' }}</span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="dropdown-menu" id="profileDropdown">
                    <div class="dropdown-item">
                        <i class="fas fa-user"></i>
                        <span>{{ Auth::user()->name ?? 'Admin' }}</span>
                    </div>
                    <div class="dropdown-divider"></div>
                    <a href="{{ route('profile.edit') }}" class="dropdown-item">
                        <i class="fas fa-user-edit"></i>
                        <span>Profile</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="dropdown-item" style="width: 100%; text-align: left; background: none; border: none;">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Left Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" data-page="dashboard">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </li>
                    <li class="nav-item {{ request()->routeIs('policies.*') ? 'active' : '' }}" data-page="policies">
                        <i class="fas fa-file-contract"></i>
                        <span>Policies</span>
                    </li>
                    <li class="nav-item {{ request()->routeIs('renewals.*') ? 'active' : '' }}" data-page="renewals">
                        <i class="fas fa-sync-alt"></i>
                        <span>Renewals</span>
                    </li>
                    <li class="nav-item {{ request()->routeIs('followups.*') ? 'active' : '' }}" data-page="followups">
                        <i class="fas fa-bell"></i>
                        <span>Follow Ups</span>
                    </li>
                    <li class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}" data-page="reports">
                        <i class="fas fa-chart-bar"></i>
                        <span>Reports</span>
                    </li>
                    <li class="nav-item {{ request()->routeIs('agents.*') ? 'active' : '' }}" data-page="agents">
                        <i class="fas fa-users"></i>
                        <span>Agents</span>
                    </li>
                    <li class="nav-item {{ request()->routeIs('notifications.*') ? 'active' : '' }}" data-page="notifications">
                        <i class="fas fa-bell"></i>
                        <span>Notifications</span>
                    </li>
                    <li class="nav-item {{ request()->routeIs('settings.*') ? 'active' : '' }}" data-page="settings">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <main class="main-content" id="mainContent">
            @yield('content')
        </main>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/script.js') }}"></script>
    @stack('scripts')
</body>
</html>
