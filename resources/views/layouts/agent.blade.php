<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'SoftPro Agent Dashboard')</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">
    <link rel="icon" type="image/png" href="{{ asset('images/softpro-logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/softpro-logo.png') }}">
    
    <!-- Preconnect for faster loading -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    
    <!-- Styles - Load immediately -->
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}?v=2.1">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v=2.1">
    
    <!-- Fonts - Load asynchronously -->
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" media="print" onload="this.media='all'">
    
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
        
        /* Profile dropdown styles */
        .profile-dropdown {
            position: relative;
        }
        
        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            min-width: 200px;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.2s ease;
        }
        
        .dropdown-menu.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        
        .dropdown-item {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            color: #374151;
            text-decoration: none;
            transition: background-color 0.2s;
        }
        
        .dropdown-item:hover {
            background-color: #f3f4f6;
        }
        
        .dropdown-item i {
            margin-right: 8px;
            width: 16px;
        }
        
        .dropdown-divider {
            height: 1px;
            background-color: #e5e7eb;
            margin: 4px 0;
        }
    </style>
    
    <!-- Inline dropdown functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Agent layout script loaded');
            const profileBtn = document.getElementById('profileBtn');
            const dropdown = document.getElementById('profileDropdown');
            
            console.log('Profile button found:', !!profileBtn);
            console.log('Dropdown found:', !!dropdown);
            
            if (profileBtn && dropdown) {
                profileBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    console.log('Profile button clicked');
                    dropdown.classList.toggle('show');
                    console.log('Dropdown toggled, has show class:', dropdown.classList.contains('show'));
                });
                
                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!profileBtn.contains(e.target) && !dropdown.contains(e.target)) {
                        dropdown.classList.remove('show');
                    }
                });
            } else {
                console.log('Profile button or dropdown not found');
            }
        });
    </script>
    
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
        <div class="nav-left">
            <!-- Logo removed from top bar -->
        </div>
        <div class="nav-right">
            <div class="profile-dropdown">
                <button class="profile-btn" id="profileBtn">
                    <i class="fas fa-user-circle"></i>
                    <span>{{ Auth::guard('agent')->user()->name ?? 'Agent' }}</span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="dropdown-menu" id="profileDropdown">
                    <div class="dropdown-item">
                        <i class="fas fa-user"></i>
                        <span>{{ Auth::guard('agent')->user()->name ?? 'Agent' }}</span>
                    </div>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('agent.logout') }}" style="display: inline;">
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
                    <li class="nav-item {{ request()->routeIs('agent.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('agent.dashboard') }}">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('agent.policies') ? 'active' : '' }}">
                        <a href="{{ route('agent.policies') }}">
                            <i class="fas fa-file-contract"></i>
                            <span>My Policies</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('agent.renewals') ? 'active' : '' }}">
                        <a href="{{ route('agent.renewals') }}">
                            <i class="fas fa-sync-alt"></i>
                            <span>Renewals</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('agent.followups') ? 'active' : '' }}">
                        <a href="{{ route('agent.followups') }}">
                            <i class="fas fa-bell"></i>
                            <span>Follow Ups</span>
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

    <!-- Scripts - Load with defer -->
    <script src="{{ asset('js/app.js') }}?v=2.9" defer></script>
    
    @stack('scripts')
</body>
</html>
