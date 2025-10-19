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
            <div class="card-header">
                <div class="card-icon premium">
                    <i class="fas fa-rupee-sign"></i>
                </div>
                <div class="card-value" id="monthlyPremium">₹0</div>
            </div>
            <div class="card-content">
                <h3>Premium (Current Month)</h3>
            </div>
        </div>
        <div class="card glass-effect">
            <div class="card-header">
                <div class="card-icon policies">
                    <i class="fas fa-file-contract"></i>
                </div>
                <div class="card-value" id="monthlyPolicies">0</div>
            </div>
            <div class="card-content">
                <h3>Policies (Current Month)</h3>
            </div>
        </div>
        <div class="card glass-effect">
            <div class="card-header">
                <div class="card-icon renewals">
                    <i class="fas fa-sync-alt"></i>
                </div>
                <div class="card-value" id="monthlyRenewals">0</div>
            </div>
            <div class="card-content">
                <h3>Renewals (Current Month)</h3>
            </div>
        </div>
        <div class="card glass-effect">
            <div class="card-header">
                <div class="card-icon revenue">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="card-value" id="monthlyRevenue">₹0</div>
            </div>
            <div class="card-content">
                <h3>Revenue (Current Month)</h3>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="charts-section">
        <div class="chart-container glass-effect" style="grid-column: 1 / -1; width: 100%">
            <div class="chart-header">
                <h3>Premium vs Revenue vs Policy</h3>
                <select class="chart-dropdown" id="chartPeriod">
                    <option value="fy">Financial Year</option>
                    <option value="month">Current Month</option>
                    <option value="quarter">Current Quarter</option>
                </select>
            </div>
            <canvas id="barChart"></canvas>
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
                        <th data-sort="vehicleNumber">Vehicle Number <i class="fas fa-sort"></i></th>
                        <th data-sort="owner">Customer Name <i class="fas fa-sort"></i></th>
                        <th data-sort="phone">Phone Number <i class="fas fa-sort"></i></th>
                        <th data-sort="vehicleType">Vehicle Type <i class="fas fa-sort"></i></th>
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

<!-- Include Modals (policy + view-policy are globally included in layout) -->
@include('components.agent-modal')
@include('components.renewal-modal')
@include('components.followup-modal')
@include('components.bulk-notification-modal')
@include('components.schedule-notification-modal')


@push('scripts')
<script>
// Dashboard page is handled by main app.js
console.log('Dashboard page loaded - functionality handled by main app.js');
</script>
@endpush

@endsection
