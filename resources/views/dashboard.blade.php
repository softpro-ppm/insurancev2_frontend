@extends('layouts.insurance')

@section('title', 'Dashboard - Insurance Management System')

@section('content')
<div class="page active" id="dashboard">
    <div class="page-header">
        <h1>Dashboard</h1>
        <p>Welcome to Insurance Management System 2.0</p>
    </div>

    <!-- Dashboard Cards -->
    <div class="dashboard-cards">
        <div class="card glass-effect">
            <div class="card-icon premium">
                <i class="fas fa-rupee-sign"></i>
            </div>
            <div class="card-content">
                <h3>Premium (Current Month)</h3>
                <p class="card-value" id="monthlyPremium">₹0</p>
                <p class="card-subtitle" id="yearlyPremium">₹0 (FY)</p>
            </div>
        </div>
        <div class="card glass-effect">
            <div class="card-icon policies">
                <i class="fas fa-file-contract"></i>
            </div>
            <div class="card-content">
                <h3>Policies (Current Month)</h3>
                <p class="card-value" id="monthlyPolicies">0</p>
                <p class="card-subtitle" id="yearlyPolicies">0 (FY)</p>
            </div>
        </div>
        <div class="card glass-effect">
            <div class="card-icon renewals">
                <i class="fas fa-sync-alt"></i>
            </div>
            <div class="card-content">
                <h3>Renewals (Current Month)</h3>
                <p class="card-value" id="monthlyRenewals">0</p>
                <p class="card-subtitle" id="pendingRenewals">0 Pending</p>
            </div>
        </div>
        <div class="card glass-effect">
            <div class="card-icon revenue">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="card-content">
                <h3>Revenue (Current Month)</h3>
                <p class="card-value" id="monthlyRevenue">₹0</p>
                <p class="card-subtitle" id="yearlyRevenue">₹0 (FY)</p>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="charts-section">
        <div class="chart-container glass-effect">
            <div class="chart-header">
                <h3>Premium vs Revenue vs Policies</h3>
                <select class="chart-dropdown" id="chartPeriod">
                    <option value="fy">Financial Year</option>
                    <option value="month">Current Month</option>
                    <option value="quarter">Current Quarter</option>
                </select>
            </div>
            <canvas id="barChart"></canvas>
        </div>
        <div class="chart-container glass-effect">
            <div class="chart-header">
                <h3>Insurance Distribution</h3>
            </div>
            <canvas id="pieChart"></canvas>
        </div>
    </div>

    <!-- Data Table -->
    <div class="data-table-container glass-effect">
        <div class="table-header">
            <h3>Recent Policies</h3>
            <div class="table-controls">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search policies..." id="policySearch">
                </div>
                <select class="rows-per-page" id="rowsPerPage">
                    <option value="10">10 rows</option>
                    <option value="30">30 rows</option>
                    <option value="50">50 rows</option>
                    <option value="100">100 rows</option>
                </select>
            </div>
        </div>
        <div class="table-wrapper">
            <table class="data-table" id="policiesTable">
                <thead>
                    <tr>
                        <th data-sort="id">Sl. No <i class="fas fa-sort"></i></th>
                        <th data-sort="type">Policy Type <i class="fas fa-sort"></i></th>
                        <th data-sort="owner">Customer Name <i class="fas fa-sort"></i></th>
                        <th data-sort="phone">Phone <i class="fas fa-sort"></i></th>
                        <th data-sort="company">Insurance Company <i class="fas fa-sort"></i></th>
                        <th data-sort="endDate">End Date <i class="fas fa-sort"></i></th>
                        <th data-sort="premium">Premium <i class="fas fa-sort"></i></th>
                        <th data-sort="status">Status <i class="fas fa-sort"></i></th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="policiesTableBody">
                    <!-- Table data will be populated by JavaScript -->
                </tbody>
            </table>
        </div>
        <div class="table-pagination">
            <div class="pagination-info">
                Showing <span id="startRecord">1</span> to <span id="endRecord">10</span> of <span id="totalRecords">50</span> entries
            </div>
            <div class="pagination-controls">
                <button class="pagination-btn" id="prevPage" disabled>
                    <i class="fas fa-chevron-left"></i>
                </button>
                <div class="page-numbers" id="pageNumbers">
                    <!-- Page numbers will be generated by JavaScript -->
                </div>
                <button class="pagination-btn" id="nextPage">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Include Modals -->
@include('components.policy-modal')
@include('components.agent-modal')
@include('components.renewal-modal')
@include('components.followup-modal')
@include('components.view-policy-modal')
@include('components.bulk-notification-modal')
@include('components.schedule-notification-modal')

@push('scripts')
<script>
    // Force hide loading overlay immediately
    (function() {
        const loadingOverlay = document.getElementById('loadingOverlay');
        if (loadingOverlay) {
            loadingOverlay.style.display = 'none';
            loadingOverlay.remove();
        }
        
        // Also hide any other loading elements
        const loadingElements = document.querySelectorAll('[id*="loading"], [class*="loading"]');
        loadingElements.forEach(el => {
            if (el.style.display !== 'none') {
                el.style.display = 'none';
            }
        });
    })();
    
    // Dashboard initialization
    document.addEventListener('DOMContentLoaded', function() {
        // Hide loading overlay
        const loadingOverlay = document.getElementById('loadingOverlay');
        if (loadingOverlay) {
            loadingOverlay.style.display = 'none';
            loadingOverlay.remove();
        }
        
        // Initialize basic dashboard functionality
        console.log('Dashboard initialized');
        
        // Load dashboard data
        loadDashboardData();
        
        // Initialize charts
        initializeCharts();
        
        // Initialize table
        loadRecentPolicies();
    });
    
    // Load dashboard statistics
    function loadDashboardData() {
        fetch('/api/dashboard/stats')
            .then(response => response.json())
            .then(data => {
                updateDashboardCards(data.stats);
                updateCharts(data);
            })
            .catch(error => {
                console.error('Error loading dashboard stats:', error);
                showNotification('Error loading dashboard data. Please refresh the page.', 'error');
            });
    }
    
    // Update dashboard cards
    function updateDashboardCards(stats) {
        document.getElementById('monthlyPremium').textContent = '₹' + stats.monthlyPremium.toLocaleString();
        document.getElementById('yearlyPremium').textContent = '₹' + stats.yearlyPremium.toLocaleString() + ' (FY)';
        document.getElementById('monthlyPolicies').textContent = stats.monthlyPolicies;
        document.getElementById('yearlyPolicies').textContent = stats.yearlyPolicies + ' (FY)';
        document.getElementById('monthlyRenewals').textContent = stats.monthlyRenewals;
        document.getElementById('pendingRenewals').textContent = stats.pendingRenewals + ' Pending';
        document.getElementById('monthlyRevenue').textContent = '₹' + stats.monthlyRevenue.toLocaleString();
        document.getElementById('yearlyRevenue').textContent = '₹' + stats.yearlyRevenue.toLocaleString() + ' (FY)';
    }
    
    // Initialize charts
    function initializeCharts() {
        if (typeof Chart === 'undefined') return;
        
        // Bar Chart
        const barCtx = document.getElementById('barChart');
        if (barCtx) {
            window.barChart = new Chart(barCtx, {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Premium',
                        data: [],
                        backgroundColor: 'rgba(79, 70, 229, 0.8)'
                    }, {
                        label: 'Revenue',
                        data: [],
                        backgroundColor: 'rgba(16, 185, 129, 0.8)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }
        
        // Pie Chart
        const pieCtx = document.getElementById('pieChart');
        if (pieCtx) {
            window.pieChart = new Chart(pieCtx, {
                type: 'pie',
                data: {
                    labels: [],
                    datasets: [{
                        data: [],
                        backgroundColor: [
                            'rgba(79, 70, 229, 0.8)',
                            'rgba(16, 185, 129, 0.8)',
                            'rgba(245, 158, 11, 0.8)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }
    }
    
    // Update charts with data
    function updateCharts(data) {
        if (window.barChart && data.chartData) {
            window.barChart.data.labels = data.chartData.map(item => item.month);
            window.barChart.data.datasets[0].data = data.chartData.map(item => item.premium);
            window.barChart.data.datasets[1].data = data.chartData.map(item => item.revenue);
            window.barChart.update();
        }
        
        if (window.pieChart && data.policyTypes) {
            window.pieChart.data.labels = Object.keys(data.policyTypes);
            window.pieChart.data.datasets[0].data = Object.values(data.policyTypes);
            window.pieChart.update();
        }
    }
    
    // Load recent policies
    function loadRecentPolicies() {
        fetch('/api/dashboard/recent-policies')
            .then(response => response.json())
            .then(data => {
                updatePoliciesTable(data.recentPolicies);
            })
            .catch(error => {
                console.error('Error loading recent policies:', error);
                showNotification('Error loading recent policies. Please refresh the page.', 'error');
            });
    }
    
    // Update policies table
    function updatePoliciesTable(policies) {
        const tableBody = document.getElementById('policiesTableBody');
        if (!tableBody) return;
        
        tableBody.innerHTML = policies.map(policy => `
            <tr>
                <td>${policy.id}</td>
                <td>${policy.policyType}</td>
                <td>${policy.customerName}</td>
                <td>${policy.phone}</td>
                <td>${policy.companyName}</td>
                <td>${policy.endDate}</td>
                <td>₹${policy.premium.toLocaleString()}</td>
                <td><span class="status-badge ${policy.status.toLowerCase()}">${policy.status}</span></td>
                <td>
                    <div class="action-buttons">
                        <button class="action-btn edit" onclick="editPolicy(${policy.id})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="action-btn delete" onclick="deletePolicy(${policy.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    }
    
    // Fallback: force hide loading after 1 second
    setTimeout(function() {
        const loadingOverlay = document.getElementById('loadingOverlay');
        if (loadingOverlay) {
            loadingOverlay.style.display = 'none';
            loadingOverlay.remove();
        }
    }, 1000);
</script>
@endpush

@endsection
