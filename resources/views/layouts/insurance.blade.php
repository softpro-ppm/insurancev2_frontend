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
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ time() }}">
    
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
            console.log('Layout script loaded');
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
                    <span>{{ Auth::check() && Auth::user() ? (Auth::user()->name === 'Test' ? 'Admin' : (Auth::user()->name ?? 'Admin')) : 'Admin' }}</span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="dropdown-menu" id="profileDropdown">
                    <div class="dropdown-item">
                        <i class="fas fa-user"></i>
                        <span>{{ Auth::check() && Auth::user() ? (Auth::user()->name === 'Test' ? 'Admin' : (Auth::user()->name ?? 'Admin')) : 'Admin' }}</span>
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
                <div class="logo">
                    <i class="fas fa-shield-alt"></i>
                    <span>Insurance MS 2.0</span>
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

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}?v={{ uniqid() }}&cache_bust={{ time() }}&force_reload={{ microtime(true) }}&data_fix={{ time() }}&api_fix={{ time() }}&dashboard_fix={{ time() }}&policies_fix={{ time() }}&js_fix={{ time() }}&zero_fix={{ time() }}&count_fix={{ time() }}&dates_fix={{ time() }}&empty_string_fix={{ time() }}&table_layout_fix={{ time() }}&policy_update_fix={{ time() }}&policy_history_fix={{ time() }}&table_simplify_fix={{ time() }}&policies_data_fix={{ time() }}&debug_fix={{ time() }}"></script>
    
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
