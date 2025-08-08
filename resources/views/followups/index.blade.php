@extends('layouts.insurance')

@section('title', 'Follow Ups - Insurance Management System')

@section('content')
<div class="page active" id="followups">
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
            <div class="stat-card glass-effect">
                <div class="stat-icon pending">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <h3>Pending Follow-ups</h3>
                    <p class="stat-value" id="pendingFollowupsCount">12</p>
                </div>
            </div>
            <div class="stat-card glass-effect">
                <div class="stat-icon inprogress">
                    <i class="fas fa-phone"></i>
                </div>
                <div class="stat-content">
                    <h3>In Progress</h3>
                    <p class="stat-value" id="inProgressFollowupsCount">8</p>
                </div>
            </div>
            <div class="stat-card glass-effect">
                <div class="stat-icon completed">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3>Completed Today</h3>
                    <p class="stat-value" id="completedTodayCount">15</p>
                </div>
            </div>
            <div class="stat-card glass-effect">
                <div class="stat-icon total">
                    <i class="fas fa-list-alt"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Follow-ups</h3>
                    <p class="stat-value" id="totalFollowupsCount">35</p>
                </div>
            </div>
        </div>

        <!-- Follow Ups Data Table -->
        <div class="data-table-container glass-effect">
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
                        <!-- Sample data -->
                        <tr>
                            <td>1</td>
                            <td>Sarah Connor</td>
                            <td>+91-9876543210</td>
                            <td><span class="followup-type-badge renewal">Renewal</span></td>
                            <td><span class="status-badge pending">Pending</span></td>
                            <td>Alice Brown</td>
                            <td>2025-01-30</td>
                            <td>2025-02-05</td>
                            <td>Customer interested in renewal quote</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-btn edit"><i class="fas fa-edit"></i></button>
                                    <button class="action-btn"><i class="fas fa-phone"></i></button>
                                    <button class="action-btn"><i class="fas fa-comments"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Mike Johnson</td>
                            <td>+91-8765432109</td>
                            <td><span class="followup-type-badge newpolicy">New Policy</span></td>
                            <td><span class="status-badge inprogress">In Progress</span></td>
                            <td>Charlie Wilson</td>
                            <td>2025-01-31</td>
                            <td>2025-02-03</td>
                            <td>Sent policy documents for review</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-btn edit"><i class="fas fa-edit"></i></button>
                                    <button class="action-btn"><i class="fas fa-phone"></i></button>
                                    <button class="action-btn"><i class="fas fa-comments"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Emma Wilson</td>
                            <td>+91-7654321098</td>
                            <td><span class="followup-type-badge claim">Claim</span></td>
                            <td><span class="status-badge completed">Completed</span></td>
                            <td>David Smith</td>
                            <td>2025-01-29</td>
                            <td>-</td>
                            <td>Claim processed successfully</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-btn edit"><i class="fas fa-edit"></i></button>
                                    <button class="action-btn"><i class="fas fa-phone"></i></button>
                                    <button class="action-btn"><i class="fas fa-comments"></i></button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="table-pagination">
                <div class="pagination-info">
                    Showing <span id="followupsStartRecord">1</span> to <span id="followupsEndRecord">10</span> of <span id="followupsTotalRecords">35</span> entries
                </div>
                <div class="pagination-controls">
                    <button class="pagination-btn" id="followupsPrevPage" disabled>
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <div class="page-numbers" id="followupsPageNumbers">
                        <button class="page-number active">1</button>
                        <button class="page-number">2</button>
                        <button class="page-number">3</button>
                    </div>
                    <button class="pagination-btn" id="followupsNextPage">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Follow-ups specific styles */
.followups-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 16px;
}

.add-followup-btn {
    background: linear-gradient(135deg, #10B981, #059669);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.add-followup-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
}

.followups-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 32px;
}

.stat-icon.inprogress {
    background: linear-gradient(135deg, #F59E0B, #D97706);
}

/* Follow-up Type Badges */
.followup-type-badge {
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.followup-type-badge.renewal {
    background: rgba(59, 130, 246, 0.1);
    color: #3b82f6;
    border: 1px solid rgba(59, 130, 246, 0.2);
}

.followup-type-badge.newpolicy {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
    border: 1px solid rgba(16, 185, 129, 0.2);
}

.followup-type-badge.claim {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
    border: 1px solid rgba(239, 68, 68, 0.2);
}

.followup-type-badge.general {
    background: rgba(107, 114, 128, 0.1);
    color: #6b7280;
    border: 1px solid rgba(107, 114, 128, 0.2);
}

/* Status Badge for In Progress */
.status-badge.inprogress {
    background: rgba(245, 158, 11, 0.1);
    color: #F59E0B;
    border: 1px solid rgba(245, 158, 11, 0.3);
}

/* Follow-up specific styles */
.followups-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 16px;
}

.add-followup-btn {
    background: linear-gradient(135deg, #10B981, #059669);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
}

.add-followup-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
}
</style>

<!-- Include Modals -->
@include('components.followup-modal')

@push('scripts')
<script>
// Followups page is handled by main app.js
console.log('Followups page loaded - functionality handled by main app.js');
</script>
@endpush

@endsection
