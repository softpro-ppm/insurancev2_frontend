@extends('layouts.insurance')

@section('title', 'Follow Ups - Insurance Management System')

@section('content')
<div class="page active" id="followups">
    <div class="page-header">
        <h1>Follow Ups</h1>
    </div>
    <div class="page-content">
        <!-- Follow Ups Controls -->
        <div class="followups-controls">
            <div class="controls-left">
                <button class="btn btn-secondary" id="createSampleDataBtn" onclick="createSampleData()">
                    <i class="fas fa-plus"></i>
                    Create Sample Data
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

        <!-- CRM Dashboard Overview -->
        <div class="crm-dashboard">
            <div class="dashboard-section">
                <div class="followups-stats">
                    <div class="stat-card glass-effect">
                        <div class="stat-icon pending">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Pending Follow-ups</h3>
                            <p class="stat-value" id="pendingFollowupsCount">0</p>
                        </div>
                    </div>
                    <div class="stat-card glass-effect">
                        <div class="stat-icon overdue">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Overdue Follow-ups</h3>
                            <p class="stat-value" id="overdueFollowupsCount">0</p>
                        </div>
                    </div>
                    <div class="stat-card glass-effect">
                        <div class="stat-icon completed">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Completed Today</h3>
                            <p class="stat-value" id="completedTodayCount">0</p>
                        </div>
                    </div>
                    <div class="stat-card glass-effect">
                        <div class="stat-icon expiring">
                            <i class="fas fa-calendar-times"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Expiring Policies</h3>
                            <p class="stat-value" id="expiringPoliciesCount">0</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Expiring Policies Section -->
            <div class="dashboard-section">
                <h2><i class="fas fa-exclamation-circle"></i> Policies Expiring Soon</h2>
                <div class="expiring-policies-container">
                    <div class="table-wrapper compact-table">
                        <table class="compact-data-table" id="expiringPoliciesTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Customer</th>
                                    <th>Phone</th>
                                    <th>Type</th>
                                    <th>Company</th>
                                    <th>Expires</th>
                                    <th>Days</th>
                                    <th>Premium</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="expiringPoliciesTableBody">
                                <!-- Dynamic data will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Recent Follow-ups Section -->
            <div class="dashboard-section">
                <h2><i class="fas fa-history"></i> Recent Follow-ups</h2>
                <div class="recent-followups-container">
                    <div class="table-wrapper compact-table">
                        <table class="compact-data-table" id="recentFollowupsTable">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th>Last Contact</th>
                                    <th>Next</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="recentFollowupsTableBody">
                                <!-- Dynamic data will be loaded here -->
                            </tbody>
                        </table>
                    </div>
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
                        <!-- Dynamic data will be rendered by public/js/app.js -->
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

.controls-left {
    display: flex;
    align-items: center;
    gap: 12px;
}

.controls-right {
    display: flex;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
}

.filter-group {
    display: flex;
    align-items: center;
    gap: 8px;
}

.filter-group label {
    font-size: 14px;
    font-weight: 500;
    color: #374151;
    white-space: nowrap;
}

.filter-group select {
    padding: 8px 12px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 14px;
    background: white;
    min-width: 120px;
}

.search-box {
    position: relative;
    display: flex;
    align-items: center;
}

.search-box i {
    position: absolute;
    left: 12px;
    color: #6b7280;
    font-size: 14px;
}

.search-box input {
    padding: 8px 12px 8px 36px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 14px;
    width: 200px;
    background: white;
}

.search-box input:focus {
    outline: none;
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
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

.stat-icon.overdue {
    background: linear-gradient(135deg, #EF4444, #DC2626);
}

.stat-icon.expiring {
    background: linear-gradient(135deg, #8B5CF6, #7C3AED);
}

/* CRM Dashboard Styles */
.crm-dashboard {
    margin-bottom: 32px;
}

.dashboard-section {
    margin-bottom: 32px;
}

.dashboard-section h2 {
    color: #1F2937;
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.dashboard-section h2 i {
    color: #6366F1;
}

.expiring-policies-container,
.recent-followups-container {
    background: rgba(255, 255, 255, 0.8);
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

/* Status badges for expiring policies */
.status-badge.urgent {
    background: rgba(239, 68, 68, 0.1);
    color: #EF4444;
    border: 1px solid rgba(239, 68, 68, 0.3);
}

.status-badge.warning {
    background: rgba(245, 158, 11, 0.1);
    color: #F59E0B;
    border: 1px solid rgba(245, 158, 11, 0.3);
}

.status-badge.normal {
    background: rgba(34, 197, 94, 0.1);
    color: #22C55E;
    border: 1px solid rgba(34, 197, 94, 0.3);
}

/* Quick action buttons */
.quick-action-btn {
    padding: 6px 12px;
    border: none;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    margin: 2px;
}

.quick-action-btn.call {
    background: rgba(34, 197, 94, 0.1);
    color: #22C55E;
    border: 1px solid rgba(34, 197, 94, 0.3);
}

.quick-action-btn.call:hover {
    background: #22C55E;
    color: white;
}

.quick-action-btn.email {
    background: rgba(59, 130, 246, 0.1);
    color: #3B82F6;
    border: 1px solid rgba(59, 130, 246, 0.3);
}

.quick-action-btn.email:hover {
    background: #3B82F6;
    color: white;
}

.quick-action-btn.followup {
    background: rgba(139, 92, 246, 0.1);
    color: #8B5CF6;
    border: 1px solid rgba(139, 92, 246, 0.3);
}

.quick-action-btn.followup:hover {
    background: #8B5CF6;
    color: white;
}

/* Compact Table Styles */
.compact-table {
    font-size: 13px;
}

.compact-data-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
    line-height: 1.3;
}

.compact-data-table th {
    background: #f8f9fa;
    padding: 8px 6px;
    text-align: left;
    font-weight: 600;
    font-size: 12px;
    color: #374151;
    border-bottom: 2px solid #e5e7eb;
    white-space: nowrap;
}

.compact-data-table td {
    padding: 6px;
    border-bottom: 1px solid #e5e7eb;
    vertical-align: middle;
    white-space: nowrap;
}

.compact-data-table tr:hover {
    background-color: #f9fafb;
}

.compact-data-table .quick-action-btn {
    padding: 4px 8px;
    font-size: 11px;
    margin: 1px;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .followups-stats {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .followups-controls {
        flex-direction: column;
        align-items: stretch;
        gap: 16px;
    }
    
    .controls-left {
        justify-content: center;
    }
    
    .controls-right {
        flex-direction: column;
        align-items: stretch;
        gap: 12px;
    }
    
    .filter-group {
        flex-direction: column;
        align-items: flex-start;
        gap: 4px;
    }
    
    .search-box input {
        width: 100%;
    }
    
    .followups-stats {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    
    .compact-data-table {
        font-size: 11px;
    }
    
    .compact-data-table th,
    .compact-data-table td {
        padding: 4px 3px;
    }
    
    .compact-data-table .quick-action-btn {
        padding: 2px 4px;
        font-size: 10px;
    }
    
    .dashboard-section h2 {
        font-size: 1.25rem;
    }
    
    .modal-small {
        width: 95%;
        max-width: none;
    }
    
    .customer-details {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 480px) {
    .page-content {
        padding: 16px;
    }
    
    .followups-stats {
        gap: 12px;
    }
    
    .stat-card {
        padding: 16px;
    }
    
    .stat-content h3 {
        font-size: 14px;
    }
    
    .stat-value {
        font-size: 24px;
    }
    
    .compact-data-table {
        font-size: 10px;
    }
    
    .compact-data-table th,
    .compact-data-table td {
        padding: 3px 2px;
    }
}

/* Simple Follow-up Modal Styles */
.modal-small {
    max-width: 600px;
    width: 90%;
}

.customer-info-section {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 20px;
}

.customer-info-section h3 {
    margin: 0 0 12px 0;
    color: #374151;
    font-size: 14px;
    font-weight: 600;
}

.customer-details {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
}

.detail-row {
    display: flex;
    align-items: center;
    font-size: 13px;
}

.detail-row .label {
    font-weight: 600;
    color: #6b7280;
    margin-right: 8px;
    min-width: 80px;
}

.detail-row .value {
    color: #374151;
    font-weight: 500;
}

.followup-form-section {
    margin-bottom: 20px;
}

.followup-form-section h3 {
    margin: 0 0 16px 0;
    color: #374151;
    font-size: 14px;
    font-weight: 600;
}

.recent-followups-section {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 16px;
    margin-top: 20px;
}

.recent-followups-section h3 {
    margin: 0 0 12px 0;
    color: #374151;
    font-size: 14px;
    font-weight: 600;
}

.recent-followups-list {
    max-height: 200px;
    overflow-y: auto;
}

.followup-item {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    padding: 12px;
    margin-bottom: 8px;
    font-size: 13px;
}

.followup-item:last-child {
    margin-bottom: 0;
}

.followup-date {
    color: #6b7280;
    font-size: 12px;
    margin-bottom: 4px;
}

.followup-notes {
    color: #374151;
    margin-bottom: 4px;
}

.followup-status {
    display: inline-block;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 500;
}

.followup-status.willcome { background: #d1fae5; color: #065f46; }
.followup-status.sold { background: #dbeafe; color: #1e40af; }
.followup-status.closed { background: #fef3c7; color: #92400e; }
.followup-status.nametransfered { background: #e0e7ff; color: #3730a3; }
.followup-status.notanswered { background: #fee2e2; color: #dc2626; }
.followup-status.wrongnumber { background: #f3f4f6; color: #374151; }
.followup-status.notworking { background: #f3f4f6; color: #374151; }

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

<!-- Simple Follow-up Notes Modal -->
<div class="modal" id="simpleFollowupModal">
    <div class="modal-content modal-small">
        <div class="modal-header">
            <h2><i class="fas fa-sticky-note"></i> Add Follow-up Notes</h2>
            <button class="modal-close" id="closeSimpleFollowupModal">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="simpleFollowupForm">
            <div class="modal-body">
                <!-- Customer Info Display -->
                <div class="customer-info-section">
                    <h3><i class="fas fa-user"></i> Customer Information</h3>
                    <div class="customer-details">
                        <div class="detail-row">
                            <span class="label">Name:</span>
                            <span class="value" id="modalCustomerName">-</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Phone:</span>
                            <span class="value" id="modalCustomerPhone">-</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Email:</span>
                            <span class="value" id="modalCustomerEmail">-</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Policy Type:</span>
                            <span class="value" id="modalPolicyType">-</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Company:</span>
                            <span class="value" id="modalCompanyName">-</span>
                        </div>
                    </div>
                </div>

                <!-- Follow-up Form -->
                <div class="followup-form-section">
                    <h3><i class="fas fa-edit"></i> Follow-up Details</h3>
                    <div class="form-group">
                        <label for="followupNotes">Customer Notes *</label>
                        <textarea id="followupNotes" name="notes" rows="3" placeholder="What did the customer say? Any important information..." required></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="followupStatus">Status *</label>
                            <select id="followupStatus" name="status" required>
                                <option value="">Select Status</option>
                                <option value="Will Come">Will Come</option>
                                <option value="Sold">Sold</option>
                                <option value="Closed">Closed</option>
                                <option value="Name Transfered">Name Transfered</option>
                                <option value="Not Answered">Not Answered</option>
                                <option value="Wrong Number">Wrong Number</option>
                                <option value="Not Working">Not Working</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="nextFollowupDate">Next Follow-up Date</label>
                            <input type="date" id="nextFollowupDate" name="nextFollowupDate">
                        </div>
                    </div>
                </div>

                <!-- Recent Follow-ups -->
                <div class="recent-followups-section" id="recentFollowupsSection" style="display: none;">
                    <h3><i class="fas fa-history"></i> Recent Follow-ups</h3>
                    <div id="recentFollowupsList" class="recent-followups-list">
                        <!-- Recent follow-ups will be loaded here -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="cancelSimpleFollowup">Cancel</button>
                <button type="submit" class="btn btn-primary" id="saveSimpleFollowup">Save Follow-up</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// CRM Dashboard functionality
document.addEventListener('DOMContentLoaded', function() {
    checkDataStatus();
    loadCrmDashboard();
    initializeSimpleFollowupModal();
});

// Check data status for production safety
async function checkDataStatus() {
    try {
        const response = await fetch('/api/followups/check-data');
        const data = await response.json();
        
        console.log('Data status:', data);
        
        // Show/hide sample data button based on data availability
        const sampleDataBtn = document.getElementById('createSampleDataBtn');
        if (sampleDataBtn) {
            if (data.needsSampleData) {
                sampleDataBtn.style.display = 'block';
                sampleDataBtn.innerHTML = '<i class="fas fa-plus"></i> Create Sample Data (No Data Found)';
            } else {
                sampleDataBtn.style.display = 'none';
            }
        }
        
    } catch (error) {
        console.error('Error checking data status:', error);
    }
}

// Initialize simple follow-up modal
function initializeSimpleFollowupModal() {
    const modal = document.getElementById('simpleFollowupModal');
    const closeBtn = document.getElementById('closeSimpleFollowupModal');
    const cancelBtn = document.getElementById('cancelSimpleFollowup');
    const form = document.getElementById('simpleFollowupForm');

    // Close modal events
    closeBtn.addEventListener('click', () => modal.style.display = 'none');
    cancelBtn.addEventListener('click', () => modal.style.display = 'none');
    
    // Close on outside click
    modal.addEventListener('click', (e) => {
        if (e.target === modal) modal.style.display = 'none';
    });

    // Form submission
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        await saveSimpleFollowup();
    });
}

// Open simple follow-up modal
function openFollowupModal(policyId, customerName, phone, email, policyType, companyName) {
    const modal = document.getElementById('simpleFollowupModal');
    
    // Populate customer info
    document.getElementById('modalCustomerName').textContent = customerName;
    document.getElementById('modalCustomerPhone').textContent = phone;
    document.getElementById('modalCustomerEmail').textContent = email || 'Not provided';
    document.getElementById('modalPolicyType').textContent = policyType;
    document.getElementById('modalCompanyName').textContent = companyName;
    
    // Store policy ID for form submission
    document.getElementById('simpleFollowupForm').dataset.policyId = policyId;
    
    // Load recent follow-ups for this customer
    loadRecentFollowupsForCustomer(phone);
    
    // Show modal
    modal.style.display = 'flex';
}

// Load recent follow-ups for a specific customer
async function loadRecentFollowupsForCustomer(phone) {
    try {
        const response = await fetch(`/api/followups/customer/${phone}`);
        if (response.ok) {
            const data = await response.json();
            displayRecentFollowups(data.followups);
        }
    } catch (error) {
        console.error('Error loading recent follow-ups:', error);
    }
}

// Display recent follow-ups in modal
function displayRecentFollowups(followups) {
    const container = document.getElementById('recentFollowupsList');
    const section = document.getElementById('recentFollowupsSection');
    
    if (followups && followups.length > 0) {
        container.innerHTML = '';
        followups.forEach(followup => {
            const item = document.createElement('div');
            item.className = 'followup-item';
            item.innerHTML = `
                <div class="followup-date">${followup.created_at}</div>
                <div class="followup-notes">${followup.notes}</div>
                <span class="followup-status ${followup.status.toLowerCase().replace(' ', '')}">${followup.status}</span>
            `;
            container.appendChild(item);
        });
        section.style.display = 'block';
    } else {
        section.style.display = 'none';
    }
}

// Save simple follow-up
async function saveSimpleFollowup() {
    const form = document.getElementById('simpleFollowupForm');
    const policyId = form.dataset.policyId;
    const formData = new FormData(form);
    
    const data = {
        policyId: policyId,
        customerName: document.getElementById('modalCustomerName').textContent,
        phone: document.getElementById('modalCustomerPhone').textContent,
        email: document.getElementById('modalCustomerEmail').textContent,
        notes: formData.get('notes'),
        status: formData.get('status'),
        nextFollowupDate: formData.get('nextFollowupDate')
    };
    
    try {
        const response = await fetch('/api/followups/save-simple', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (response.ok) {
            alert('Follow-up saved successfully!');
            document.getElementById('simpleFollowupModal').style.display = 'none';
            form.reset();
            loadCrmDashboard(); // Refresh the dashboard
        } else {
            alert('Error saving follow-up: ' + result.message);
        }
    } catch (error) {
        console.error('Error saving follow-up:', error);
        alert('Error saving follow-up');
    }
}

async function loadCrmDashboard() {
    try {
        console.log('Loading CRM dashboard...');
        const response = await fetch('/api/followups/crm-dashboard');
        const data = await response.json();
        
        console.log('Dashboard data received:', data);
        
        // Update statistics
        document.getElementById('pendingFollowupsCount').textContent = data.stats.pendingFollowups;
        document.getElementById('overdueFollowupsCount').textContent = data.stats.overdueFollowups;
        document.getElementById('completedTodayCount').textContent = data.stats.completedToday;
        document.getElementById('expiringPoliciesCount').textContent = data.stats.expiringPolicies;
        
        console.log('Expiring policies:', data.expiringPolicies);
        console.log('Recent followups:', data.recentFollowups);
        
        // Load expiring policies
        loadExpiringPolicies(data.expiringPolicies);
        
        // Load recent followups
        loadRecentFollowups(data.recentFollowups);
        
    } catch (error) {
        console.error('Error loading CRM dashboard:', error);
        alert('Error loading dashboard data: ' + error.message);
    }
}

function loadExpiringPolicies(policies) {
    const tbody = document.getElementById('expiringPoliciesTableBody');
    tbody.innerHTML = '';
    
    if (policies.length === 0) {
        tbody.innerHTML = '<tr><td colspan="10" class="text-center text-gray-500 py-8">No expiring policies found</td></tr>';
        return;
    }
    
    policies.forEach((policy, index) => {
        const row = document.createElement('tr');
        const expiryDate = new Date(policy.endDate).toLocaleDateString('en-GB', { 
            day: '2-digit', 
            month: 'short' 
        });
        const premium = parseFloat(policy.premium).toLocaleString('en-IN');
        const daysLeft = Math.round(policy.daysUntilExpiry); // Round to whole number
        
        row.innerHTML = `
            <td>${index + 1}</td>
            <td>${policy.customerName}</td>
            <td>${policy.phone}</td>
            <td>${policy.policyType}</td>
            <td>${policy.companyName}</td>
            <td>${expiryDate}</td>
            <td><strong>${daysLeft}</strong></td>
            <td>₹${premium}</td>
            <td><span class="status-badge ${policy.status.toLowerCase()}">${policy.status}</span></td>
            <td>
                <button class="quick-action-btn call" onclick="callClient('${policy.phone}')" title="Call">
                    <i class="fas fa-phone"></i>
                </button>
                <button class="quick-action-btn email" onclick="sendEmailToClient(${policy.id}, '${policy.status}')" title="Email">
                    <i class="fas fa-envelope"></i>
                </button>
                <button class="quick-action-btn followup" onclick="openFollowupModal(${policy.id}, '${policy.customerName}', '${policy.phone}', '${policy.email || ''}', '${policy.policyType}', '${policy.companyName}')" title="Add Follow-up Notes">
                    <i class="fas fa-plus"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

function loadRecentFollowups(followups) {
    const tbody = document.getElementById('recentFollowupsTableBody');
    tbody.innerHTML = '';
    
    if (followups.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-gray-500 py-8">No recent follow-ups found</td></tr>';
        return;
    }
    
    followups.forEach(followup => {
        const row = document.createElement('tr');
        const lastContact = new Date(followup.lastContact).toLocaleDateString('en-GB', { 
            day: '2-digit', 
            month: 'short' 
        });
        const nextFollowup = followup.nextFollowup === 'Not scheduled' ? 'Not scheduled' : 
            new Date(followup.nextFollowup).toLocaleDateString('en-GB', { 
                day: '2-digit', 
                month: 'short' 
            });
        
        row.innerHTML = `
            <td>${followup.customerName}</td>
            <td>${followup.phone}</td>
            <td><span class="status-badge ${followup.status.toLowerCase().replace(' ', '')}">${followup.status}</span></td>
            <td>${lastContact}</td>
            <td>${nextFollowup}</td>
            <td>
                <button class="quick-action-btn call" onclick="callClient('${followup.phone}')" title="Call">
                    <i class="fas fa-phone"></i>
                </button>
                <button class="quick-action-btn email" onclick="sendEmailToClient(${followup.id}, 'reminder')" title="Email">
                    <i class="fas fa-envelope"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

// Quick action functions
function callClient(phone) {
    window.open(`tel:${phone}`, '_self');
}

function emailClient(email) {
    if (email) {
        window.open(`mailto:${email}`, '_self');
    } else {
        alert('No email address available for this client');
    }
}

async function sendEmailToClient(policyId, emailType = 'reminder') {
    try {
        const response = await fetch(`/api/followups/send-email/${policyId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                emailType: emailType
            })
        });
        
        const result = await response.json();
        
        if (response.ok) {
            alert('Email sent successfully!');
        } else {
            alert('Error sending email: ' + result.error);
        }
    } catch (error) {
        console.error('Error sending email:', error);
        alert('Error sending email');
    }
}

async function createFollowupFromPolicy(policyId) {
    try {
        const response = await fetch(`/api/followups/create-from-policy/${policyId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const result = await response.json();
        
        if (response.ok) {
            alert('Follow-up created successfully!');
            loadCrmDashboard(); // Refresh the dashboard
        } else {
            alert('Error creating follow-up: ' + result.message);
        }
    } catch (error) {
        console.error('Error creating follow-up:', error);
        alert('Error creating follow-up');
    }
}

// Create sample data function
async function createSampleData() {
    try {
        const response = await fetch('/api/followups/create-sample-policies', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const result = await response.json();
        
        if (response.ok) {
            alert('Sample data created successfully! ' + result.count + ' policies added.');
            // Force refresh the dashboard
            setTimeout(() => {
                loadCrmDashboard();
            }, 1000);
        } else {
            alert('Error creating sample data: ' + (result.message || 'Unknown error'));
        }
    } catch (error) {
        console.error('Error creating sample data:', error);
        alert('Error creating sample data: ' + error.message);
    }
}

// Followups page is handled by main app.js
console.log('Followups page loaded - CRM functionality added');
</script>
@endpush

@endsection
