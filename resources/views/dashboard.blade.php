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
        <div class="card">
            <div class="card-icon premium">
                <i class="fas fa-rupee-sign"></i>
            </div>
            <div class="card-content">
                <h3>Premium (Current Month)</h3>
                <p class="card-value">₹2,45,000</p>
                <p class="card-subtitle">₹12,50,000 (FY)</p>
            </div>
        </div>
        <div class="card">
            <div class="card-icon policies">
                <i class="fas fa-file-contract"></i>
            </div>
            <div class="card-content">
                <h3>Policies (Current Month)</h3>
                <p class="card-value">45</p>
                <p class="card-subtitle">280 (FY)</p>
            </div>
        </div>
        <div class="card">
            <div class="card-icon renewals">
                <i class="fas fa-sync-alt"></i>
            </div>
            <div class="card-content">
                <h3>Renewals (Current Month)</h3>
                <p class="card-value">12</p>
                <p class="card-subtitle">85 Pending</p>
            </div>
        </div>
        <div class="card">
            <div class="card-icon revenue">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="card-content">
                <h3>Revenue (Current Month)</h3>
                <p class="card-value">₹1,85,000</p>
                <p class="card-subtitle">₹9,20,000 (FY)</p>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="charts-section">
        <div class="chart-container">
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
        <div class="chart-container">
            <div class="chart-header">
                <h3>Insurance Distribution</h3>
            </div>
            <canvas id="pieChart"></canvas>
        </div>
    </div>

    <!-- Data Table -->
    <div class="data-table-container">
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
                        <th class="sortable" data-column="id">
                            Policy ID <i class="fas fa-sort"></i>
                        </th>
                        <th class="sortable" data-column="customer">
                            Customer <i class="fas fa-sort"></i>
                        </th>
                        <th class="sortable" data-column="company">
                            Company <i class="fas fa-sort"></i>
                        </th>
                        <th class="sortable" data-column="type">
                            Type <i class="fas fa-sort"></i>
                        </th>
                        <th class="sortable" data-column="premium">
                            Premium <i class="fas fa-sort"></i>
                        </th>
                        <th class="sortable" data-column="status">
                            Status <i class="fas fa-sort"></i>
                        </th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="policiesTableBody">
                    <!-- Table content will be populated by JavaScript -->
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
