@extends('layouts.insurance')

@section('title', 'Policies - Insurance Management System 2.0')

@section('content')
<div class="page" id="policies">
    <div class="page-header">
        <h1>Policies</h1>
        <p>Manage all insurance policies</p>
    </div>
    <div class="page-content">
        <!-- Policies Controls -->
        <div class="policies-controls">
            <div class="controls-left">
                <button class="add-policy-btn" id="addPolicyFromPoliciesBtn">
                    <i class="fas fa-plus"></i>
                    Add New Policy
                </button>
            </div>
            <div class="controls-right">
                <div class="filter-group">
                    <label for="policyTypeFilter">Policy Type:</label>
                    <select id="policyTypeFilter">
                        <option value="">All Types</option>
                        <option value="Motor">Motor</option>
                        <option value="Health">Health</option>
                        <option value="Life">Life</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="statusFilter">Status:</label>
                    <select id="statusFilter">
                        <option value="">All Status</option>
                        <option value="Active">Active</option>
                        <option value="Expired">Expired</option>
                        <option value="Pending">Pending</option>
                    </select>
                </div>
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search policies..." id="policiesSearch">
                </div>
            </div>
        </div>

        <!-- Policies Statistics -->
        <div class="policies-stats">
            <div class="stat-card">
                <div class="stat-icon active">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3>Active Policies</h3>
                    <p class="stat-value" id="activePoliciesCount">0</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon expired">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-content">
                    <h3>Expired Policies</h3>
                    <p class="stat-value" id="expiredPoliciesCount">0</p>
                </div>
            </div>
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
                <div class="stat-icon total">
                    <i class="fas fa-file-contract"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Policies</h3>
                    <p class="stat-value" id="totalPoliciesCount">0</p>
                </div>
            </div>
        </div>

        <!-- Policies Data Table -->
        <div class="data-table-container">
            <div class="table-header">
                <h3>All Policies</h3>
                <div class="table-controls">
                    <select class="rows-per-page" id="policiesRowsPerPage">
                        <option value="10">10 rows</option>
                        <option value="25">25 rows</option>
                        <option value="50">50 rows</option>
                        <option value="100">100 rows</option>
                    </select>
                    <button class="export-btn" id="exportPolicies">
                        <i class="fas fa-download"></i>
                        Export
                    </button>
                </div>
            </div>
            <div class="table-wrapper">
                <table class="data-table" id="policiesPageTable">
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
                    <tbody id="policiesPageTableBody">
                        <!-- Table data will be populated by JavaScript -->
                    </tbody>
                </table>
            </div>
            <div class="table-pagination">
                <div class="pagination-info">
                    Showing <span id="policiesStartRecord">1</span> to <span id="policiesEndRecord">10</span> of <span id="policiesTotalRecords">50</span> entries
                </div>
                <div class="pagination-controls">
                    <button class="pagination-btn" id="policiesPrevPage" disabled>
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <div class="page-numbers" id="policiesPageNumbers">
                        <!-- Page numbers will be generated by JavaScript -->
                    </div>
                    <button class="pagination-btn" id="policiesNextPage">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
                </div>
                <div class="stat-content">
                    <h3>Active Policies</h3>
                    <p class="stat-value" id="activePoliciesCount">0</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon expired">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-content">
                    <h3>Expired Policies</h3>
                    <p class="stat-value" id="expiredPoliciesCount">0</p>
                </div>
            </div>
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
                <div class="stat-icon total">
                    <i class="fas fa-file-contract"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Policies</h3>
                    <p class="stat-value" id="totalPoliciesCount">0</p>
                </div>
            </div>
        </div>

        <!-- Policies Data Table -->
        <div class="data-table-container">
            <div class="table-header">
                <h3>All Policies</h3>
                <div class="table-controls">
                    <select class="rows-per-page" id="policiesRowsPerPage">
                        <option value="10">10 rows</option>
                        <option value="25">25 rows</option>
                        <option value="50">50 rows</option>
                        <option value="100">100 rows</option>
                    </select>
                    <button class="export-btn" id="exportPolicies">
                        <i class="fas fa-download"></i>
                        Export
                    </button>
                </div>
            </div>
            <div class="table-wrapper">
                <table class="data-table" id="policiesPageTable">
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
                    <tbody id="policiesPageTableBody">
                        <!-- Table data will be populated by JavaScript -->
                    </tbody>
                </table>
            </div>
            <div class="table-pagination">
                <div class="pagination-info">
                    Showing <span id="policiesStartRecord">1</span> to <span id="policiesEndRecord">10</span> of <span id="policiesTotalRecords">50</span> entries
                </div>
                <div class="pagination-controls">
                    <button class="pagination-btn" id="policiesPrevPage" disabled>
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <div class="page-numbers" id="policiesPageNumbers">
                        <!-- Page numbers will be generated by JavaScript -->
                    </div>
                    <button class="pagination-btn" id="policiesNextPage">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
