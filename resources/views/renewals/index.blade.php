@extends('layouts.insurance')

@section('title', 'Renewals - Insurance Management System')

@section('content')
<div class="page active" id="renewals">
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
            <div class="stat-card glass-effect">
                <div class="stat-icon pending">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <h3>Pending Renewals</h3>
                    <p class="stat-value" id="pendingRenewalsCount">15</p>
                </div>
            </div>
            <div class="stat-card glass-effect">
                <div class="stat-icon overdue">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-content">
                    <h3>Overdue Renewals</h3>
                    <p class="stat-value" id="overdueRenewalsCount">3</p>
                </div>
            </div>
            <div class="stat-card glass-effect">
                <div class="stat-icon completed">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3>Completed This Month</h3>
                    <p class="stat-value" id="completedRenewalsCount">28</p>
                </div>
            </div>
            <div class="stat-card glass-effect">
                <div class="stat-icon total">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Renewals</h3>
                    <p class="stat-value" id="totalRenewalsCount">46</p>
                </div>
            </div>
        </div>

        <!-- Renewals Data Table -->
        <div class="data-table-container glass-effect">
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
                        <!-- Sample data -->
                        <tr>
                            <td>1</td>
                            <td>POL001</td>
                            <td>John Doe</td>
                            <td><span class="policy-type-badge motor">Motor</span></td>
                            <td>2025-02-15</td>
                            <td><span class="days-left urgent">5 days</span></td>
                            <td><span class="status-badge pending">Pending</span></td>
                            <td><span class="priority-badge high">High</span></td>
                            <td>Alice Brown</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-btn edit"><i class="fas fa-edit"></i></button>
                                    <button class="action-btn"><i class="fas fa-eye"></i></button>
                                    <button class="action-btn"><i class="fas fa-phone"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>POL002</td>
                            <td>Jane Smith</td>
                            <td><span class="policy-type-badge health">Health</span></td>
                            <td>2025-03-01</td>
                            <td><span class="days-left warning">20 days</span></td>
                            <td><span class="status-badge pending">In Progress</span></td>
                            <td><span class="priority-badge medium">Medium</span></td>
                            <td>Charlie Wilson</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-btn edit"><i class="fas fa-edit"></i></button>
                                    <button class="action-btn"><i class="fas fa-eye"></i></button>
                                    <button class="action-btn"><i class="fas fa-phone"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>POL003</td>
                            <td>Bob Johnson</td>
                            <td><span class="policy-type-badge life">Life</span></td>
                            <td>2025-01-31</td>
                            <td><span class="days-left urgent">-2 days</span></td>
                            <td><span class="status-badge expired">Overdue</span></td>
                            <td><span class="priority-badge high">High</span></td>
                            <td>Alice Brown</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-btn edit"><i class="fas fa-edit"></i></button>
                                    <button class="action-btn"><i class="fas fa-eye"></i></button>
                                    <button class="action-btn"><i class="fas fa-phone"></i></button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="table-pagination">
                <div class="pagination-info">
                    Showing <span id="renewalsStartRecord">1</span> to <span id="renewalsEndRecord">10</span> of <span id="renewalsTotalRecords">46</span> entries
                </div>
                <div class="pagination-controls">
                    <button class="pagination-btn" id="renewalsPrevPage" disabled>
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <div class="page-numbers" id="renewalsPageNumbers">
                        <button class="page-number active">1</button>
                        <button class="page-number">2</button>
                        <button class="page-number">3</button>
                    </div>
                    <button class="pagination-btn" id="renewalsNextPage">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Renewal specific styles */
.renewals-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 16px;
}

.add-renewal-btn {
    background: linear-gradient(135deg, #4F46E5, #7C3AED);
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
    box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
}

.add-renewal-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(79, 70, 229, 0.4);
}

.renewals-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 32px;
}

.stat-icon.overdue {
    background: linear-gradient(135deg, #EF4444, #DC2626);
}

.stat-icon.completed {
    background: linear-gradient(135deg, #10B981, #059669);
}

/* Priority Badges */
.priority-badge {
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.priority-badge.high {
    background: rgba(239, 68, 68, 0.1);
    color: #EF4444;
    border: 1px solid rgba(239, 68, 68, 0.3);
}

.priority-badge.medium {
    background: rgba(245, 158, 11, 0.1);
    color: #F59E0B;
    border: 1px solid rgba(245, 158, 11, 0.3);
}

.priority-badge.low {
    background: rgba(16, 185, 129, 0.1);
    color: #10B981;
    border: 1px solid rgba(16, 185, 129, 0.3);
}

/* Days Left Indicators */
.days-left {
    font-weight: 600;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
}

.days-left.urgent {
    background: rgba(239, 68, 68, 0.1);
    color: #EF4444;
}

.days-left.warning {
    background: rgba(245, 158, 11, 0.1);
    color: #F59E0B;
}

.days-left.safe {
    background: rgba(16, 185, 129, 0.1);
    color: #10B981;
}

/* Policy Type Badges */
.policy-type-badge {
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.policy-type-badge.motor {
    background: rgba(59, 130, 246, 0.1);
    color: #3b82f6;
    border: 1px solid rgba(59, 130, 246, 0.2);
}

.policy-type-badge.health {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
    border: 1px solid rgba(16, 185, 129, 0.2);
}

.policy-type-badge.life {
    background: rgba(245, 158, 11, 0.1);
    color: #f59e0b;
    border: 1px solid rgba(245, 158, 11, 0.2);
}

/* View Modal Styles */
.detail-section {
    margin-bottom: 24px;
}

.detail-section h3 {
    color: #374151;
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.detail-section h3 i {
    color: #6B7280;
}

.detail-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin-bottom: 12px;
}

.detail-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.detail-item.full-width {
    grid-column: 1 / -1;
}

.detail-item label {
    font-size: 12px;
    font-weight: 600;
    color: #6B7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.detail-item span {
    font-size: 14px;
    color: #111827;
    font-weight: 500;
}

.detail-item span span {
    display: inline-block;
}

/* Status Badges */
.status-badge {
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-badge.pending {
    background: rgba(245, 158, 11, 0.1);
    color: #F59E0B;
    border: 1px solid rgba(245, 158, 11, 0.3);
}

.status-badge.inprogress {
    background: rgba(59, 130, 246, 0.1);
    color: #3B82F6;
    border: 1px solid rgba(59, 130, 246, 0.3);
}

.status-badge.completed {
    background: rgba(16, 185, 129, 0.1);
    color: #10B981;
    border: 1px solid rgba(16, 185, 129, 0.3);
}

.status-badge.overdue {
    background: rgba(239, 68, 68, 0.1);
    color: #EF4444;
    border: 1px solid rgba(239, 68, 68, 0.3);
}

/* Renewals specific styles */
.renewals-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 16px;
}

.add-renewal-btn {
    background: linear-gradient(135deg, #4F46E5, #6366F1);
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

.add-renewal-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(79, 70, 229, 0.4);
}
</style>

<!-- Include Modals -->
@include('components.renewal-modal')

@push('scripts')
<script>
// Renewals page is handled by main app.js
console.log('Renewals page loaded - functionality handled by main app.js');
</script>
@endpush

@endsection
