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
    
    <!-- Preconnect to external resources for faster loading -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://code.jquery.com">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    
    <!-- Styles - Load immediately for rendering -->
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}?v=2.6">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v=2.6">
    
    <!-- Fonts - Load asynchronously -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" media="print" onload="this.media='all'">
    
    <!-- Scripts - Defer to not block page rendering -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js" defer></script>
    
    @stack('styles')
</head>
<body class="light-theme">
    <!-- Top Navigation Bar -->
    <nav class="top-nav">
        <div class="nav-left">
            <!-- Mobile Menu Toggle Button -->
            <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Toggle menu">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        <div class="nav-right">
            <div class="nav-datetime" id="navDateTime">
                <i class="fas fa-clock"></i>
                <span id="currentDateTime">Loading...</span>
            </div>
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

    <!-- Mobile Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
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
                    <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <a href="{{ route('dashboard') }}">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('policies.*') ? 'active' : '' }}">
                        <a href="{{ route('policies.index') }}">
                            <i class="fas fa-file-contract"></i>
                            <span>Policies</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('renewals.*') ? 'active' : '' }}">
                        <a href="{{ route('renewals.index') }}">
                            <i class="fas fa-sync-alt"></i>
                            <span>Renewals</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('followups.*') ? 'active' : '' }}">
                        <a href="{{ route('followups.index') }}">
                            <i class="fas fa-bell"></i>
                            <span>Follow Ups</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <a href="{{ route('reports.index') }}">
                            <i class="fas fa-chart-bar"></i>
                            <span>Reports</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('business-analytics.*') ? 'active' : '' }}">
                        <a href="{{ route('business-analytics.index') }}">
                            <i class="fas fa-briefcase"></i>
                            <span>My Business</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('agents.*') ? 'active' : '' }}">
                        <a href="{{ route('agents.index') }}">
                            <i class="fas fa-users"></i>
                            <span>Agents</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                        <a href="{{ route('notifications.index') }}">
                            <i class="fas fa-bell"></i>
                            <span>Notifications</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                        <a href="{{ route('settings.index') }}">
                            <i class="fas fa-cog"></i>
                            <span>Settings</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <main class="main-content" id="mainContent">
            @yield('content')
        </main>
    </div>

    <!-- Global Modals - Available on all pages -->
    @include('components.policy-modal')
    @include('components.renew-policy-modal')
    @include('components.view-policy-modal')
    
    <!-- Policy History Modal - Available on all pages -->
    <div class="modal" id="policyHistoryModal" style="display: none !important; position: fixed !important; z-index: 99999 !important; left: 0 !important; top: 0 !important; width: 100% !important; height: 100% !important; background-color: rgba(0,0,0,0.8) !important;">
        <div class="modal-content" style="max-width: 1200px !important; width: 90% !important; margin: 2% auto !important; background: white !important; border-radius: 12px !important; box-shadow: 0 4px 20px rgba(0,0,0,0.5) !important; position: relative !important;">
            <div class="modal-header" style="padding: 20px !important; border-bottom: 1px solid #eee !important; display: flex !important; justify-content: space-between !important; align-items: center !important;">
                <h2 style="margin: 0 !important; color: #1f2937 !important;">Policy History</h2>
                <span class="close" onclick="closePolicyHistoryModal()" style="font-size: 28px !important; font-weight: bold !important; cursor: pointer !important; color: #666 !important; line-height: 1 !important;">&times;</span>
            </div>
            <div class="modal-body" style="padding: 20px !important; max-height: 70vh !important; overflow-y: auto !important;">
                <div id="policyHistoryContent">
                    <div class="loading" style="text-align: center; padding: 40px; color: #666;">Loading policy history...</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts - Load with defer for better performance -->
    <script src="{{ asset('js/app.js') }}?v=2.10" defer></script>
    
    @stack('scripts')
</body>
</html>
