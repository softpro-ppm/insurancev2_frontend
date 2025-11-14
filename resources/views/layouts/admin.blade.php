<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SoftPro Insurance Management')</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">
    <link rel="icon" type="image/png" href="{{ asset('images/softpro-logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/softpro-logo.png') }}">
    
    <!-- Preconnect for faster loading -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://code.jquery.com">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    
    <!-- Styles - Load immediately -->
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}?v=2.1">
    <link rel="stylesheet" href="{{ asset('css/laravel-overrides.css') }}?v=2.1">
    
    <!-- Fonts and Icons - Load asynchronously -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" media="print" onload="this.media='all'">
    
    <!-- Scripts - Defer for better performance -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js" defer></script>
    
    @stack('styles')
</head>
<body class="light-theme">
    <!-- Top Navigation Bar -->
    <nav class="top-nav">
        <div class="nav-left">
            <!-- brand moved to sidebar -->
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
                        <span>Admin</span>
                    </div>
                    <div class="dropdown-item">
                        <i class="fas fa-user-tie"></i>
                        <span>Agent</span>
                    </div>
                    <div class="dropdown-item">
                        <i class="fas fa-user-shield"></i>
                        <span>Reception</span>
                    </div>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="dropdown-item" style="background: none; border: none; width: 100%; text-align: left;">
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
                <div class="logo">
                    <img src="{{ asset('images/softpro-logo.png') }}" alt="SoftPro" class="logo-image">
                    <span class="logo-text">SoftPro</span>
                </div>
                <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" data-page="dashboard">
                        <a href="{{ route('dashboard') }}">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('policies*') ? 'active' : '' }}" data-page="policies">
                        <a href="{{ route('policies.index') }}">
                            <i class="fas fa-file-contract"></i>
                            <span>Policies</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('renewals*') ? 'active' : '' }}" data-page="renewals">
                        <a href="{{ route('renewals.index') }}">
                            <i class="fas fa-sync-alt"></i>
                            <span>Renewals</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('followups*') ? 'active' : '' }}" data-page="followups">
                        <a href="{{ route('followups.index') }}">
                            <i class="fas fa-bell"></i>
                            <span>Follow Ups</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('reports*') ? 'active' : '' }}" data-page="reports">
                        <a href="{{ route('reports.index') }}">
                            <i class="fas fa-chart-bar"></i>
                            <span>Reports</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('agents*') ? 'active' : '' }}" data-page="agents">
                        <a href="{{ route('agents.index') }}">
                            <i class="fas fa-users"></i>
                            <span>Agents</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('notifications*') ? 'active' : '' }}" data-page="notifications">
                        <a href="{{ route('notifications.index') }}">
                            <i class="fas fa-bell"></i>
                            <span>Notifications</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('settings*') ? 'active' : '' }}" data-page="settings">
                        <a href="{{ route('settings.index') }}">
                            <i class="fas fa-cog"></i>
                            <span>Settings</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            @yield('content')
        </main>
    </div>

    <!-- JavaScript -->
    <script src="{{ asset('js/script.js') }}"></script>
    @stack('scripts')
</body>
</html>
