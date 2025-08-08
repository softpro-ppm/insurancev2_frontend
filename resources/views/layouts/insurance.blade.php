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
    
    <!-- Force hide loading overlay -->
    <style>
        #loadingOverlay {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            pointer-events: none !important;
        }
        
        .loading-spinner {
            display: none !important;
        }
        
        /* Ensure main content is visible */
        .main-container {
            opacity: 1 !important;
            visibility: visible !important;
        }
    </style>
    
    @stack('styles')
</head>
<body class="light-theme">
    <!-- Immediate fix for loading overlay -->
    <script>
        // Immediately hide loading overlay
        document.addEventListener('DOMContentLoaded', function() {
            const loadingOverlay = document.getElementById('loadingOverlay');
            if (loadingOverlay) {
                loadingOverlay.style.display = 'none';
                loadingOverlay.remove();
            }
        });
        
        // Also hide on window load
        window.addEventListener('load', function() {
            const loadingOverlay = document.getElementById('loadingOverlay');
            if (loadingOverlay) {
                loadingOverlay.style.display = 'none';
                loadingOverlay.remove();
            }
        });
        
        // Force hide immediately
        setTimeout(function() {
            const loadingOverlay = document.getElementById('loadingOverlay');
            if (loadingOverlay) {
                loadingOverlay.style.display = 'none';
                loadingOverlay.remove();
            }
        }, 100);
    </script>
    
    <!-- Loading Overlay -->
    <div id="loadingOverlay" style="display: none !important;">
        <div class="loading-spinner"></div>
    </div>
    <!-- Top Navigation Bar -->
    <nav class="top-nav">
        <div class="nav-left"></div>
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
                <div class="sidebar-brand">
                    <i class="fas fa-shield-alt"></i>
                    <span>Insurance MS 2.0</span>
                </div>
                <button class="sidebar-toggle" id="sidebarToggle" title="Toggle menu">
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
    @include('components.view-policy-modal')

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    
    <!-- Fix loading overlay issue -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Hide loading overlay after page loads
            const loadingOverlay = document.getElementById('loadingOverlay');
            if (loadingOverlay) {
                loadingOverlay.style.display = 'none';
            }
            
            // Initialize basic functionality if main scripts fail
            setTimeout(function() {
                if (loadingOverlay && loadingOverlay.style.display !== 'none') {
                    loadingOverlay.style.display = 'none';
                }
            }, 3000); // Force hide after 3 seconds
        });
        
        // Fallback: hide loading overlay on window load
        window.addEventListener('load', function() {
            const loadingOverlay = document.getElementById('loadingOverlay');
            if (loadingOverlay) {
                loadingOverlay.style.display = 'none';
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>
