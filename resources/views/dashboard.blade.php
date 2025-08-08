@extends('layouts.insurance')

@section('title', 'Dashboard - Insurance Management System')

@section('content')
<div class="page active" id="dashboard">
    <div class="page-header">
        <h1>Dashboard</h1>
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
// Dashboard page is handled by main app.js
console.log('Dashboard page loaded - functionality handled by main app.js');
</script>
@endpush

@endsection
