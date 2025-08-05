@extends('layouts.insurance')

@section('title', 'Follow Ups - Insurance Management System 2.0')

@section('content')
<div class="page" id="followups">
    <div class="page-header">
        <h1>Follow Ups</h1>
        <p>Track customer interactions and telecaller follow-up activities</p>
    </div>
    <div class="page-content">
        <!-- Follow Ups Controls -->
        <div class="followups-controls">
            <div class="controls-left">
                <button class="add-followup-btn" id="addFollowupBtn">
                    <i class="fas fa-plus"></i>
                    Add Follow Up
                </button>
            </div>
            <div class="controls-right">
                <div class="filter-group">
                    <label for="followupStatusFilter">Status:</label>
                    <select id="followupStatusFilter">
                        <option value="">All Status</option>
                        <option value="Pending">Pending</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Completed">Completed</option>
                        <option value="No Response">No Response</option>
                        <option value="Not Interested">Not Interested</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="followupTypeFilter">Type:</label>
                    <select id="followupTypeFilter">
                        <option value="">All Types</option>
                        <option value="Renewal">Renewal</option>
                        <option value="New Policy">New Policy</option>
                        <option value="Claim">Claim</option>
                        <option value="General">General</option>
                    </select>
                </div>
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search follow-ups..." id="followupsSearch">
                </div>
            </div>
        </div>

        <!-- Follow Ups Statistics -->
        <div class="followups-stats">
            <div class="stat-card">
                <div class="stat-icon pending">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <h3>Pending Follow-ups</h3>
                    <p class="stat-value" id="pendingFollowupsCount">0</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon inprogress">
                    <i class="fas fa-phone"></i>
                </div>
                <div class="stat-content">
                    <h3>In Progress</h3>
                    <p class="stat-value" id="inProgressFollowupsCount">0</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon completed">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3>Completed Today</h3>
                    <p class="stat-value" id="completedTodayCount">0</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon total">
                    <i class="fas fa-list-alt"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Follow-ups</h3>
                    <p class="stat-value" id="totalFollowupsCount">0</p>
                </div>
            </div>
        </div>

        <!-- Follow Ups Data Table -->
        <div class="data-table-container">
            <div class="table-header">
                <h3>Follow-up Management</h3>
                <div class="table-controls">
                    <select class="rows-per-page" id="followupsRowsPerPage">
                        <option value="10">10 rows</option>
                        <option value="25">25 rows</option>
                        <option value="50">50 rows</option>
                        <option value="100">100 rows</option>
                    </select>
                    <button class="export-btn" id="exportFollowups">
                        <i class="fas fa-download"></i>
                        Export
                    </button>
                </div>
            </div>
            <div class="table-wrapper">
                <table class="data-table" id="followupsTable">
                    <thead>
                        <tr>
                            <th data-sort="id">Sl. No <i class="fas fa-sort"></i></th>
                            <th data-sort="customerName">Customer Name <i class="fas fa-sort"></i></th>
                            <th data-sort="phone">Phone Number <i class="fas fa-sort"></i></th>
                            <th data-sort="followupType">Follow-up Type <i class="fas fa-sort"></i></th>
                            <th data-sort="status">Status <i class="fas fa-sort"></i></th>
                            <th data-sort="assignedTo">Assigned To <i class="fas fa-sort"></i></th>
                            <th data-sort="lastFollowupDate">Last Follow-up <i class="fas fa-sort"></i></th>
                            <th data-sort="nextFollowupDate">Next Follow-up <i class="fas fa-sort"></i></th>
                            <th data-sort="recentNote">Recent Note <i class="fas fa-sort"></i></th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="followupsTableBody">
                        <!-- Table data will be populated by JavaScript -->
                    </tbody>
                </table>
            </div>
            <div class="table-pagination">
                <div class="pagination-info">
                    Showing <span id="followupsStartRecord">1</span> to <span id="followupsEndRecord">10</span> of <span id="followupsTotalRecords">0</span> entries
                </div>
                <div class="pagination-controls">
                    <button class="pagination-btn" id="followupsPrevPage" disabled>
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <div class="page-numbers" id="followupsPageNumbers">
                        <!-- Page numbers will be generated by JavaScript -->
                    </div>
                    <button class="pagination-btn" id="followupsNextPage">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsectionayouts.insurance')

@section('title', 'Follow Ups - Insurance Management System 2.0')

@section('content')
<div class="page" id="followups">
    <div class="page-header">
        <h1>Follow Ups</h1>
        <p>Manage customer follow-ups and track interactions</p>
    </div>
    <div class="page-content">
        <!-- Follow-ups content will be populated by JavaScript -->
        <div class="followups-controls">
            <div class="controls-left">
                <button class="add-followup-btn" id="addFollowupBtn">
                    <i class="fas fa-plus"></i>
                    Add Follow Up
                </button>
            </div>
            <div class="controls-right">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search follow-ups..." id="followupsSearch">
                </div>
            </div>
        </div>

        <div class="data-table-container">
            <div class="table-header">
                <h3>Follow Ups Management</h3>
                <div class="table-controls">
                    <select class="rows-per-page" id="followupsRowsPerPage">
                        <option value="10">10 rows</option>
                        <option value="25">25 rows</option>
                        <option value="50">50 rows</option>
                        <option value="100">100 rows</option>
                    </select>
                </div>
            </div>
            <div class="table-wrapper">
                <table class="data-table" id="followupsTable">
                    <thead>
                        <tr>
                            <th data-sort="id">Sl. No <i class="fas fa-sort"></i></th>
                            <th data-sort="customerName">Customer Name <i class="fas fa-sort"></i></th>
                            <th data-sort="type">Type <i class="fas fa-sort"></i></th>
                            <th data-sort="status">Status <i class="fas fa-sort"></i></th>
                            <th data-sort="dueDate">Due Date <i class="fas fa-sort"></i></th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="followupsTableBody">
                        <!-- Table data will be populated by JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
