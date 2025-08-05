@extends('layouts.insurance')

@section('title', 'Renewals - Insurance Management System 2.0')

@section('content')
<div class="page" id="renewals">
    <div class="page-header">
        <h1>Renewals</h1>
        <p>Manage policy renewals and track upcoming expirations</p>
    </div>
    <div class="page-content">
        <!-- Renewals Controls -->
        <div class="renewals-controls">
            <div class="controls-left">
                <button class="add-renewal-btn" id="addRenewalBtn">
                    <i class="fas fa-plus"></i>
                    Add Renewal Reminder
                </button>
            </div>
            <div class="controls-right">
                <div class="filter-group">
                    <label for="renewalStatusFilter">Status:</label>
                    <select id="renewalStatusFilter">
                        <option value="">All Status</option>
                        <option value="Pending">Pending</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Completed">Completed</option>
                        <option value="Overdue">Overdue</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="renewalPriorityFilter">Priority:</label>
                    <select id="renewalPriorityFilter">
                        <option value="">All Priorities</option>
                        <option value="High">High</option>
                        <option value="Medium">Medium</option>
                        <option value="Low">Low</option>
                    </select>
                </div>
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search renewals..." id="renewalsSearch">
                </div>
            </div>
        </div>

        <!-- Renewals Statistics -->
        <div class="renewals-stats">
            <div class="stat-card">
                <div class="stat-icon pending">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <h3>Pending Renewals</h3>
                    <p class="stat-value" id="pendingRenewalsCount">0</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon overdue">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-content">
                    <h3>Overdue Renewals</h3>
                    <p class="stat-value" id="overdueRenewalsCount">0</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon completed">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3>Completed This Month</h3>
                    <p class="stat-value" id="completedRenewalsCount">0</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon total">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Renewals</h3>
                    <p class="stat-value" id="totalRenewalsCount">0</p>
                </div>
            </div>
        </div>

        <!-- Renewals Data Table -->
        <div class="data-table-container">
            <div class="table-header">
                <h3>Renewals Management</h3>
                <div class="table-controls">
                    <select class="rows-per-page" id="renewalsRowsPerPage">
                        <option value="10">10 rows</option>
                        <option value="25">25 rows</option>
                        <option value="50">50 rows</option>
                        <option value="100">100 rows</option>
                    </select>
                    <button class="export-btn" id="exportRenewals">
                        <i class="fas fa-download"></i>
                        Export
                    </button>
                </div>
            </div>
            <div class="table-wrapper">
                <table class="data-table" id="renewalsTable">
                    <thead>
                        <tr>
                            <th data-sort="id">Sl. No <i class="fas fa-sort"></i></th>
                            <th data-sort="policyId">Policy ID <i class="fas fa-sort"></i></th>
                            <th data-sort="customerName">Customer Name <i class="fas fa-sort"></i></th>
                            <th data-sort="policyType">Policy Type <i class="fas fa-sort"></i></th>
                            <th data-sort="expiryDate">Expiry Date <i class="fas fa-sort"></i></th>
                            <th data-sort="daysLeft">Days Left <i class="fas fa-sort"></i></th>
                            <th data-sort="status">Status <i class="fas fa-sort"></i></th>
                            <th data-sort="priority">Priority <i class="fas fa-sort"></i></th>
                            <th data-sort="assignedTo">Assigned To <i class="fas fa-sort"></i></th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="renewalsTableBody">
                        <!-- Table data will be populated by JavaScript -->
                    </tbody>
                </table>
            </div>
            <div class="table-pagination">
                <div class="pagination-info">
                    Showing <span id="renewalsStartRecord">1</span> to <span id="renewalsEndRecord">10</span> of <span id="renewalsTotalRecords">0</span> entries
                </div>
                <div class="pagination-controls">
                    <button class="pagination-btn" id="renewalsPrevPage" disabled>
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <div class="page-numbers" id="renewalsPageNumbers">
                        <!-- Page numbers will be generated by JavaScript -->
                    </div>
                    <button class="pagination-btn" id="renewalsNextPage">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
