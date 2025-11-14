@extends('layouts.insurance')

@section('title', 'Follow Ups - Insurance Management System')

@section('content')
<div class="page active" id="followups">
    <div class="page-header">
        <h1>Policy Follow Ups</h1>
        <p class="text-muted">Track and manage policy renewals by month</p>
    </div>

    <!-- Dashboard Stats Cards -->
    <div class="followup-stats-grid">
        <div class="stat-card urgent-card glass-effect">
            <div class="stat-icon">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value" id="lastMonthCount">
                    <div class="stat-line">Expired: 0</div>
                </div>
                <div class="stat-label">Last Month</div>
            </div>
        </div>
        
        <div class="stat-card warning-card glass-effect">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value" id="currentMonthCount">
                    <div class="stat-line">Expired: 0</div>
                    <div class="stat-line">Expiring: 0</div>
                </div>
                <div class="stat-label">Current Month</div>
            </div>
        </div>
        
        <div class="stat-card info-card glass-effect">
            <div class="stat-icon">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value" id="nextMonthCount">
                    <div class="stat-line">Expiring: 0</div>
                </div>
                <div class="stat-label">Next Month</div>
            </div>
        </div>
        
        <div class="stat-card total-card glass-effect">
            <div class="stat-icon">
                <i class="fas fa-list-check"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value" id="totalCount">
                    <div class="stat-line">Expired: 0</div>
                    <div class="stat-line">Expiring: 0</div>
                </div>
                <div class="stat-label">Total Pending</div>
            </div>
        </div>
    </div>

    <!-- Last Month Expired Policies -->
    <div class="followup-section glass-effect">
        <div class="section-header urgent-header">
            <h2><i class="fas fa-exclamation-circle"></i> Last Month Expired</h2>
            <span class="count-badge" id="lastMonthBadge">0</span>
        </div>
        <div class="table-wrapper">
            <table class="followup-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Customer Name</th>
                        <th>Phone</th>
                        <th>Policy / Type</th>
                        <th>End Date</th>
                        <th>Days Overdue</th>
                        <th>Premium</th>
                        <th>Last Note</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="lastMonthTable">
                    <tr>
                        <td colspan="9" class="text-center loading-row">Loading...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Current Month Expiring Policies -->
    <div class="followup-section glass-effect">
        <div class="section-header warning-header">
            <h2><i class="fas fa-clock"></i> Current Month Expiring</h2>
            <span class="count-badge" id="currentMonthBadge">0</span>
        </div>
        <div class="table-wrapper">
            <table class="followup-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Customer Name</th>
                        <th>Phone</th>
                        <th>Policy / Type</th>
                        <th>End Date</th>
                        <th>Days Remaining</th>
                        <th>Premium</th>
                        <th>Last Note</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="currentMonthTable">
                    <tr>
                        <td colspan="9" class="text-center loading-row">Loading...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Next Month Expiring Policies -->
    <div class="followup-section glass-effect">
        <div class="section-header info-header">
            <h2><i class="fas fa-calendar-alt"></i> Next Month Expiring</h2>
            <span class="count-badge" id="nextMonthBadge">0</span>
        </div>
        <div class="table-wrapper">
            <table class="followup-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Customer Name</th>
                        <th>Phone</th>
                        <th>Policy / Type</th>
                        <th>End Date</th>
                        <th>Days Remaining</th>
                        <th>Premium</th>
                        <th>Last Note</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="nextMonthTable">
                    <tr>
                        <td colspan="9" class="text-center loading-row">Loading...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Quick Note Modal -->
<div class="modal" id="quickNoteModal">
    <div class="modal-content modal-medium">
        <div class="modal-header">
            <h2><i class="fas fa-sticky-note"></i> Add Follow-up Note</h2>
            <button class="modal-close" id="closeNoteModal">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="customer-info-box">
                <h3>Customer Information</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="label">Name:</span>
                        <span class="value" id="noteCustomerName">-</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Phone:</span>
                        <span class="value" id="noteCustomerPhone">-</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Policy Type:</span>
                        <span class="value" id="notePolicyType">-</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Company:</span>
                        <span class="value" id="noteCompanyName">-</span>
                    </div>
                </div>
            </div>

            <form id="quickNoteForm">
                <input type="hidden" id="notePolicyId">
                <input type="hidden" id="noteEmail">
                
                <div class="form-group">
                    <label for="noteText">Customer Notes *</label>
                    <textarea id="noteText" name="note" rows="4" placeholder="What did the customer say? Add any important details..." required></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="noteStatus">Status *</label>
                        <select id="noteStatus" name="status" required>
                            <option value="">Select Status</option>
                            <option value="Interested">Interested</option>
                            <option value="Will Call Back">Will Call Back</option>
                            <option value="Renewed">Renewed</option>
                            <option value="Not Interested">Not Interested</option>
                            <option value="Wrong Number">Wrong Number</option>
                            <option value="Not Answered">Not Answered</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="noteNextDate">Next Follow-up Date</label>
                        <input type="date" id="noteNextDate" name="nextFollowupDate">
                    </div>
                </div>
            </form>
        </div>

        <div class="previous-notes-box" id="previousNotesBox">
            <h3><i class="fas fa-history"></i> Previous Notes</h3>
            <div class="previous-notes-list" id="previousNotesList">
                <p class="text-muted">No previous notes yet.</p>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" id="cancelNoteBtn">Cancel</button>
            <button type="button" class="btn btn-primary" id="saveNoteBtn">
                <i class="fas fa-save"></i> Save Note
            </button>
        </div>
    </div>
</div>

<style>
/* Follow-ups Page Styles */
.page-header p.text-muted {
    color: #6b7280;
    font-size: 14px;
    margin-top: 8px;
}

.followup-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 32px;
}

.stat-card {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 24px;
    border-radius: 12px;
    border: 2px solid transparent;
}

.stat-card .stat-icon {
    width: 56px;
    height: 56px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
}

.urgent-card { border-color: rgba(239, 68, 68, 0.3); }
.urgent-card .stat-icon { background: linear-gradient(135deg, #EF4444, #DC2626); }

.warning-card { border-color: rgba(245, 158, 11, 0.3); }
.warning-card .stat-icon { background: linear-gradient(135deg, #F59E0B, #D97706); }

.info-card { border-color: rgba(59, 130, 246, 0.3); }
.info-card .stat-icon { background: linear-gradient(135deg, #3B82F6, #2563EB); }

.total-card { border-color: rgba(99, 102, 241, 0.3); }
.total-card .stat-icon { background: linear-gradient(135deg, #6366F1, #4F46E5); }

.stat-content {
    flex: 1;
}

.stat-value {
    font-size: 32px;
    font-weight: 700;
    color: #111827;
    line-height: 1.2;
}

.dark-theme .stat-value {
    color: #F1F5F9;
}

/* Two-line stat display */
.stat-line {
    font-size: 20px;
    font-weight: 600;
    color: #111827;
    line-height: 1.5;
    margin: 2px 0;
}

.dark-theme .stat-line {
    color: #F1F5F9;
}

.stat-label {
    font-size: 14px;
    color: #6b7280;
    margin-top: 4px;
}

/* Follow-up Sections */
.followup-section {
    margin-bottom: 32px;
    border-radius: 12px;
    overflow: hidden;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 24px;
    border-bottom: 2px solid;
}

.section-header h2 {
    font-size: 18px;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.urgent-header {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(220, 38, 38, 0.05));
    border-bottom-color: #EF4444;
    color: #991B1B;
}

.warning-header {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(217, 119, 6, 0.05));
    border-bottom-color: #F59E0B;
    color: #92400E;
}

.info-header {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(37, 99, 235, 0.05));
    border-bottom-color: #3B82F6;
    color: #1E40AF;
}

.count-badge {
    background: rgba(255, 255, 255, 0.9);
    color: #111827;
    padding: 6px 16px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 16px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

/* Tables */
.table-wrapper {
    overflow-x: auto;
}

.followup-table {
    width: 100%;
    border-collapse: collapse;
}

.followup-table thead th {
    background: #f8f9fa;
    padding: 12px;
    text-align: left;
    font-weight: 600;
    font-size: 13px;
    color: #374151;
    border-bottom: 2px solid #e5e7eb;
    white-space: nowrap;
}

.dark-theme .followup-table thead th {
    background: #1F2937;
    color: #F1F5F9;
}

.followup-table tbody td {
    padding: 12px;
    border-bottom: 1px solid #e5e7eb;
    font-size: 14px;
    vertical-align: middle;
}

.dark-theme .followup-table tbody td {
    border-bottom-color: rgba(255, 255, 255, 0.1);
}

.followup-table tbody tr:hover {
    background-color: #f9fafb;
}

.dark-theme .followup-table tbody tr:hover {
    background-color: rgba(255, 255, 255, 0.05);
}

.loading-row {
    padding: 40px !important;
    color: #6b7280;
    font-style: italic;
}

.no-data-row {
    padding: 40px !important;
    color: #6b7280;
    text-align: center;
}

/* Status Badges */
.status-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    white-space: nowrap;
}

.status-expired {
    background: rgba(239, 68, 68, 0.1);
    color: #DC2626;
}

.status-expiring {
    background: rgba(245, 158, 11, 0.1);
    color: #D97706;
}

.status-upcoming {
    background: rgba(59, 130, 246, 0.1);
    color: #2563EB;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 8px;
}

.action-btn {
    padding: 6px 12px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 13px;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.btn-call {
    background: rgba(34, 197, 94, 0.1);
    color: #16A34A;
    border: 1px solid rgba(34, 197, 94, 0.3);
}

.btn-call:hover {
    background: #16A34A;
    color: white;
}

.btn-note {
    background: rgba(99, 102, 241, 0.1);
    color: #4F46E5;
    border: 1px solid rgba(99, 102, 241, 0.3);
}

.btn-note:hover {
    background: #4F46E5;
    color: white;
}

/* Last Note Display */
.last-note {
    max-width: 200px;
    font-size: 12px;
    color: #6b7280;
}

.last-note-text {
    display: block;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.last-note-date {
    font-size: 11px;
    color: #9ca3af;
    margin-top: 2px;
}

.last-note-status {
    display: inline-block;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 10px;
    font-weight: 600;
    margin-top: 4px;
    background: rgba(99, 102, 241, 0.1);
    color: #4F46E5;
}

/* Quick Note Modal */
.modal-medium {
    max-width: 600px;
}

.customer-info-box {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 20px;
}

.dark-theme .customer-info-box {
    background: #1F2937;
}

.customer-info-box h3 {
    margin: 0 0 12px 0;
    font-size: 14px;
    font-weight: 600;
    color: #374151;
}

.dark-theme .customer-info-box h3 {
    color: #F1F5F9;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
}

.info-item {
    display: flex;
    gap: 8px;
    font-size: 13px;
}

.info-item .label {
    font-weight: 600;
    color: #6b7280;
}

.info-item .value {
    color: #111827;
    font-weight: 500;
}

.dark-theme .info-item .value {
    color: #F1F5F9;
}

/* Policy Type Badges */
.policy-type-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
    background: #E0E7FF;
    color: #3730A3;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.policy-type-badge.motor {
    background: rgba(59, 130, 246, 0.12);
    color: #1D4ED8;
}

.policy-type-badge.health {
    background: rgba(16, 185, 129, 0.12);
    color: #047857;
}

.policy-type-badge.life {
    background: rgba(245, 158, 11, 0.12);
    color: #B45309;
}

.policy-type-badge.default {
    background: rgba(107, 114, 128, 0.12);
    color: #374151;
}

.text-muted {
    color: #6b7280;
    font-size: 13px;
}

.dark-theme .text-muted {
    color: #94A3B8;
}

.previous-notes-box {
    margin-top: 24px;
    border-top: 1px solid rgba(107, 114, 128, 0.2);
    padding: 16px 18px 0 18px;
    background: #f9fafb;
    border-radius: 10px;
}

.previous-notes-box h3 {
    font-size: 14px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 12px;
}

.dark-theme .previous-notes-box {
    background: #1F2937;
    border-color: rgba(148, 163, 184, 0.3);
}

.dark-theme .previous-notes-box h3 {
    color: #E2E8F0;
}

.previous-notes-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
    max-height: 220px;
    overflow-y: auto;
}

.previous-note-item {
    background: rgba(59, 130, 246, 0.05);
    border: 1px solid rgba(59, 130, 246, 0.1);
    border-radius: 10px;
    padding: 12px 14px;
}

.previous-note-item .note-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 6px;
    font-size: 12px;
    color: #6b7280;
}

.previous-note-item .note-status {
    background: rgba(59, 130, 246, 0.15);
    color: #1D4ED8;
    padding: 2px 8px;
    border-radius: 999px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.previous-note-item .note-text {
    font-size: 13px;
    color: #1f2937;
}

.dark-theme .previous-note-item {
    background: rgba(79, 70, 229, 0.12);
    border-color: rgba(79, 70, 229, 0.2);
}

.dark-theme .previous-note-item .note-meta {
    color: #CBD5F5;
}

.dark-theme .previous-note-item .note-text {
    color: #E5E7EB;
}

/* Responsive Design */
@media (max-width: 768px) {
    .followup-stats-grid {
        grid-template-columns: 1fr;
    }
    
    .section-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }
    
    .followup-table {
        font-size: 12px;
    }
    
    .followup-table thead th,
    .followup-table tbody td {
        padding: 8px;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .info-grid {
        grid-template-columns: 1fr;
    }
}
</style>

@push('scripts')
<script>
(function() {
    // Utility helpers
    const notify = (message, type = 'info') => {
        if (typeof window !== 'undefined' && typeof window.showNotification === 'function') {
            window.showNotification(message, type);
        } else {
            const bgColors = {
                success: '#10B981',
                error: '#EF4444',
                info: '#3B82F6'
            };
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${bgColors[type] || bgColors.info};
                color: white;
                padding: 16px 24px;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                z-index: 10000;
                font-weight: 600;
            `;
            notification.textContent = message;
            document.body.appendChild(notification);
            setTimeout(() => notification.remove(), 3000);
        }
    };

    const escapeHtml = (str = '') => String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');

    const formatPolicyTypeCell = (policy) => {
        const baseType = (policy.policyType || '').toLowerCase();
        const label = escapeHtml(policy.displayType || policy.policyType || '');
        const badgeClass = baseType || 'default';
        return `<span class="policy-type-badge ${badgeClass}">${label || '-'}</span>`;
    };

    const formatLastNote = (note) => {
        if (!note) return '<span class="text-muted">No notes yet</span>';
        const fullText = escapeHtml(note.note || '');
        const words = (note.note || '').trim().split(/\s+/);
        let preview = words.slice(0, 2).join(' ');
        if (words.length > 2) {
            preview += '...';
        }
        return `<div class="last-note" title="${fullText}">
            <span class="last-note-text">${escapeHtml(preview)}</span>
            <div class="last-note-date">${escapeHtml(note.date || '')}</div>
            <span class="last-note-status">${escapeHtml(note.status || '')}</span>
        </div>`;
    };

    const renderTable = (tableId, policies, category) => {
        const tbody = document.getElementById(tableId);
        if (!tbody) return;

        if (!policies.length) {
            tbody.innerHTML = '<tr><td colspan="9" class="no-data-row">No policies found in this category</td></tr>';
            return;
        }

        tbody.innerHTML = '';
        policies.forEach((policy, index) => {
            const row = document.createElement('tr');
            const daysText = policy.daysUntilExpiry < 0
                ? `${Math.abs(policy.daysUntilExpiry)} days ago`
                : `${policy.daysUntilExpiry} days`;

            const lastNoteHtml = policy.lastNote
                ? formatLastNote(policy.lastNote)
                : '<span class="text-muted">No notes yet</span>';

            const safeCustomerName = (policy.customerName || '').replace(/'/g, "\\'");
            const safePhone = (policy.phone || '').replace(/'/g, "\\'");
            const safeEmail = (policy.email || '').replace(/'/g, "\\'");
            const safeDisplayType = (policy.displayType || policy.policyType || '').replace(/'/g, "\\'");
            const safeRawType = (policy.policyType || '').replace(/'/g, "\\'");
            const safeCompany = (policy.companyName || '').replace(/'/g, "\\'");

            row.innerHTML = `
                <td>${index + 1}</td>
                <td><strong>${escapeHtml(policy.customerName)}</strong></td>
                <td>${escapeHtml(policy.phone)}</td>
                <td>${formatPolicyTypeCell(policy)}</td>
                <td>${escapeHtml(policy.endDate)}</td>
                <td><span class="status-badge status-${category}">${daysText}</span></td>
                <td>₹${parseFloat(policy.premium).toLocaleString('en-IN')}</td>
                <td>${lastNoteHtml}</td>
                <td>
                    <div class="action-buttons">
                        <button class="action-btn btn-call" onclick="window.followupsCallCustomer('${safePhone}')">
                            <i class="fas fa-phone"></i> Call
                        </button>
                        <button class="action-btn btn-note" onclick="window.followupsOpenQuickNote(${policy.id}, '${safeCustomerName}', '${safePhone}', '${safeEmail}', '${safeDisplayType}', '${safeCompany}', '${safeRawType}')">
                            <i class="fas fa-sticky-note"></i> Note
                        </button>
                    </div>
                </td>
            `;
            tbody.appendChild(row);
        });
    };

    const callCustomer = (phone) => {
        window.location.href = `tel:${phone}`;
    };

    const openQuickNote = (policyId, customerName, phone, email, displayType, companyName, rawPolicyType) => {
        const form = document.getElementById('quickNoteForm');
        if (form) form.reset();

        document.getElementById('notePolicyId').value = policyId;
        document.getElementById('noteCustomerName').textContent = customerName;
        document.getElementById('noteCustomerPhone').textContent = phone;
        document.getElementById('noteEmail').value = email;
        document.getElementById('notePolicyType').textContent = displayType || rawPolicyType || '-';
        document.getElementById('noteCompanyName').textContent = companyName || '-';

        const previousNotesList = document.getElementById('previousNotesList');
        previousNotesList.innerHTML = '<p class="text-muted">Loading previous notes...</p>';

        document.getElementById('quickNoteModal').classList.add('show');
        loadPreviousNotes(phone);
    };

    const closeQuickNoteModal = () => {
        document.getElementById('quickNoteModal').classList.remove('show');
    };

    const loadPreviousNotes = async (phone) => {
        const list = document.getElementById('previousNotesList');
        if (!phone) {
            list.innerHTML = '<p class="text-muted">Phone number not available.</p>';
            return;
        }
        try {
            const response = await fetch(`/api/followups/customer/${encodeURIComponent(phone)}`);
            if (!response.ok) throw new Error(`Failed to load previous notes (status ${response.status})`);
            const data = await response.json();
            const notes = data.followups || [];
            if (!notes.length) {
                list.innerHTML = '<p class="text-muted">No previous notes yet.</p>';
                return;
            }
            list.innerHTML = notes.map(note => `
                <div class="previous-note-item">
                    <div class="note-meta">
                        <span>${escapeHtml(note.created_at || '')}</span>
                        <span class="note-status">${escapeHtml(note.status || '')}</span>
                    </div>
                    <div class="note-text">${escapeHtml(note.notes || '')}</div>
                </div>
            `).join('');
        } catch (error) {
            console.error('Failed to load previous notes:', error);
            list.innerHTML = '<p class="text-muted">Unable to load previous notes.</p>';
        }
    };

    const saveQuickNote = async () => {
        console.log('🔍 saveQuickNote called');
        const policyId = document.getElementById('notePolicyId').value;
        const customerName = document.getElementById('noteCustomerName').textContent;
        const phone = document.getElementById('noteCustomerPhone').textContent;
        const email = document.getElementById('noteEmail').value;
        const note = document.getElementById('noteText').value;
        const status = document.getElementById('noteStatus').value;
        const nextDate = document.getElementById('noteNextDate').value;

        console.log('🔍 Form data:', { policyId, customerName, phone, note, status, nextDate });

        if (!note || !status) {
            notify('Please fill in all required fields', 'error');
            return;
        }

        try {
            console.log('🔍 Sending request to /api/followups/save-note');
            const response = await fetch('/api/followups/save-note', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json'
                },
                body: JSON.stringify({
                    policyId,
                    customerName,
                    phone,
                    email,
                    note,
                    status,
                    nextFollowupDate: nextDate
                })
            });

            console.log('🔍 Response status:', response.status);
            const data = await response.json();
            console.log('🔍 Response data:', data);

            if (response.ok && data.success) {
                console.log('✅ Note saved successfully');
                notify('Note saved successfully!', 'success');
                closeQuickNoteModal();
                setTimeout(() => {
                    console.log('🔄 Reloading dashboard...');
                    loadDashboard();
                }, 500);
            } else {
                console.error('❌ Save failed:', data);
                notify(data.message || 'Failed to save note', 'error');
            }
        } catch (error) {
            console.error('❌ Error saving note:', error);
            notify('Error saving note: ' + error.message, 'error');
        }
    };

    const loadDashboard = async () => {
        try {
            console.log('🔍 Loading follow-ups dashboard...');
            console.log('🔍 Calling API: /api/followups/dashboard');

            const response = await fetch('/api/followups/dashboard', {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });

            console.log('🔍 Response status:', response.status);
            console.log('🔍 Response ok:', response.ok);

            if (response.status === 401 || response.status === 419) {
                console.error('❌ Authentication error:', response.status);
                notify('Please login to view follow-ups', 'error');
                setTimeout(() => {
                    window.location.href = '/login';
                }, 2000);
                return;
            }

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            console.log('🔍 API Response:', data);

            if (data.debug) {
                console.log('🔍 Debug Info:', data.debug);
            }

            if (data.success) {
                document.getElementById('lastMonthCount').innerHTML = `
                    <div class="stat-line">Expired: ${data.stats.lastMonth.expired}</div>
                `;
                document.getElementById('currentMonthCount').innerHTML = `
                    <div class="stat-line">Expired: ${data.stats.currentMonth.expired}</div>
                    <div class="stat-line">Expiring: ${data.stats.currentMonth.expiring}</div>
                `;
                document.getElementById('nextMonthCount').innerHTML = `
                    <div class="stat-line">Expiring: ${data.stats.nextMonth.expiring}</div>
                `;
                document.getElementById('totalCount').innerHTML = `
                    <div class="stat-line">Expired: ${data.stats.total.expired}</div>
                    <div class="stat-line">Expiring: ${data.stats.total.expiring}</div>
                `;

                document.getElementById('lastMonthBadge').textContent = data.stats.lastMonth.total;
                document.getElementById('currentMonthBadge').textContent = data.stats.currentMonth.total;
                document.getElementById('nextMonthBadge').textContent = data.stats.nextMonth.total;

                renderTable('lastMonthTable', data.data.lastMonth, 'expired');
                renderTable('currentMonthTable', data.data.currentMonth, 'expiring');
                renderTable('nextMonthTable', data.data.nextMonth, 'upcoming');

                if (data.stats.total === 0 && data.debug && data.debug.totalPolicies === 0) {
                    console.warn('No active policies found in database');
                    notify('No active policies found. Please add some policies first.', 'info');
                } else if (data.stats.total === 0) {
                    console.warn('No policies found in the selected date ranges');
                    notify('No policies expiring in the selected months', 'info');
                }
            } else {
                console.error('API returned error:', data.error);
                notify('Error: ' + (data.error || 'Failed to load data'), 'error');
            }
        } catch (error) {
            console.error('Error loading follow-ups dashboard:', error);
            notify('Failed to load follow-ups data: ' + error.message, 'error');
        }
    };

    document.addEventListener('DOMContentLoaded', function() {
        loadDashboard();

        const addPolicyBtn = document.getElementById('addPolicyBtn');
        if (addPolicyBtn) {
            addPolicyBtn.addEventListener('click', function(event) {
                event.preventDefault();
                event.stopPropagation();
                const tryOpenPolicyModal = () => {
                    if (typeof window.openPolicyModal === 'function') {
                        window.openPolicyModal();
                    } else if (typeof openPolicyModal === 'function') {
                        openPolicyModal();
                    } else {
                        console.warn('openPolicyModal not available yet, retrying in 100ms');
                        setTimeout(tryOpenPolicyModal, 100);
                    }
                };
                tryOpenPolicyModal();
            }, { once: false });
        }

        document.getElementById('closeNoteModal').addEventListener('click', closeQuickNoteModal);
        document.getElementById('cancelNoteBtn').addEventListener('click', closeQuickNoteModal);
        document.getElementById('saveNoteBtn').addEventListener('click', saveQuickNote);
        document.getElementById('quickNoteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeQuickNoteModal();
            }
        });
    });

    // Expose functions needed by inline handlers
    window.followupsOpenQuickNote = openQuickNote;
    window.followupsCallCustomer = callCustomer;
})();
</script>
@endpush

@endsection
