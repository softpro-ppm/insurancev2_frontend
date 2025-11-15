@extends('layouts.insurance')

@section('title', 'Renewals - Insurance Management System')

@section('content')
<div class="page active" id="renewals">
    <div class="page-header">
        <h1>Renewals</h1>
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
                    <label for="renewalTimePeriodFilter">Time Period:</label>
                    <select id="renewalTimePeriodFilter">
                        <option value="current_month">Current Month</option>
                        <option value="past_30">Past 30 Days</option>
                        <option value="past_60">Past 60 Days</option>
                        <option value="next_30">Next 30 Days</option>
                        <option value="next_60">Next 60 Days</option>
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
                    <p class="stat-value" id="pendingRenewalsCount">0</p>
                </div>
            </div>
            <div class="stat-card glass-effect">
                <div class="stat-icon completed">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3>Completed Renewals</h3>
                    <p class="stat-value" id="completedRenewalsCount">0</p>
                </div>
            </div>
            <div class="stat-card glass-effect">
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
                            <th data-sort="customerName">Customer Name <i class="fas fa-sort"></i></th>
                            <th data-sort="phone">Phone <i class="fas fa-sort"></i></th>
                            <th data-sort="vehicleType">Vehicle / Type <i class="fas fa-sort"></i></th>
                            <th data-sort="expiryDate">Expiry Date <i class="fas fa-sort"></i></th>
                            <th data-sort="daysLeft">Days Left <i class="fas fa-sort"></i></th>
                            <th data-sort="premium">Premium <i class="fas fa-sort"></i></th>
                            <th data-sort="assignedTo">Assigned To <i class="fas fa-sort"></i></th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="renewalsTableBody">
                        <!-- Rows will be populated by JavaScript -->
                    </tbody>
                </table>
            </div>
            <div class="table-pagination">
                <div class="pagination-info">
                    Showing <span id="renewalsStartRecord">0</span> to <span id="renewalsEndRecord">0</span> of <span id="renewalsTotalRecords">0</span> entries
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
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-bottom: 32px;
}

@media (max-width: 768px) {
    .renewals-stats {
        grid-template-columns: 1fr;
    }
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

.policy-type-badge.default {
    background: rgba(107, 114, 128, 0.12);
    color: #374151;
}

.vehicle-subtext {
    font-size: 11px;
    color: #666;
    margin-top: 2px;
}

.dark-theme .vehicle-subtext {
    color: #CBD5F5;
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
// Dynamically load renewals from Policies API and render table + stats
(function() {
    const state = {
        all: [],
        filtered: [],
        page: 1,
        perPage: 10,
        sort: {
            column: 'expiryDate', // default sort by expiry
            direction: 'asc'
        },
        totals: {
            pending: 0,
            completed: 0,
            total: 0
        }
    };

    const els = {
        tbody: document.getElementById('renewalsTableBody'),
        rowsPerPage: document.getElementById('renewalsRowsPerPage'),
        prev: document.getElementById('renewalsPrevPage'),
        next: document.getElementById('renewalsNextPage'),
        pages: document.getElementById('renewalsPageNumbers'),
        startRec: document.getElementById('renewalsStartRecord'),
        endRec: document.getElementById('renewalsEndRecord'),
        totalRecs: document.getElementById('renewalsTotalRecords'),
        search: document.getElementById('renewalsSearch'),
        timePeriodFilter: document.getElementById('renewalTimePeriodFilter'),
        exportBtn: document.getElementById('exportRenewals'),
        statusFilter: null,
        priorityFilter: null,
        stats: {
            pending: document.getElementById('pendingRenewalsCount'),
            completed: document.getElementById('completedRenewalsCount'),
            total: document.getElementById('totalRenewalsCount')
        }
    };

    if (els.tbody) {
        els.tbody.addEventListener('click', handleActionButtonClick);
    }

    function handleActionButtonClick(event) {
        const btn = event.target.closest('button[data-action]');
        if (!btn) return;
        event.preventDefault();
        event.stopPropagation();
        const action = btn.dataset.action;
        const policyId = Number(btn.dataset.policyId || 0);
        const phone = btn.dataset.phone || '';

        if (action === 'edit') {
            if (policyId && typeof window.editPolicy === 'function') {
                window.editPolicy(policyId);
            } else {
                notify('Unable to open edit modal for this policy.', 'error');
            }
        } else if (action === 'view') {
            if (policyId) {
                if (typeof window.viewPolicy === 'function') {
                    window.viewPolicy(policyId);
                } else {
                    window.location.href = `/policies/${policyId}/view`;
                }
            } else {
                notify('Unable to open policy view.', 'error');
            }
        } else if (action === 'call') {
            if (phone) {
                window.location.href = `tel:${phone}`;
            } else {
                notify('Phone number not available for this policy.', 'info');
            }
        }
    }

    function parseDate(dateStr) {
        // dateStr expected format YYYY-MM-DD
        return new Date(dateStr + 'T00:00:00');
    }

    function daysUntil(dateStr) {
        const today = new Date();
        const d = parseDate(dateStr);
        // normalize to midnight
        const msPerDay = 24*60*60*1000;
        const diff = Math.floor((d.setHours(0,0,0,0) - new Date(today.getFullYear(), today.getMonth(), today.getDate()).getTime()) / msPerDay);
        return diff; // negative means overdue
    }

    function getTimePeriodRange(timePeriod) {
        const today = new Date();
        const currentYear = today.getFullYear();
        const currentMonth = today.getMonth(); // 0-based month
        
        console.log('🔍 getTimePeriodRange called with:', timePeriod);
        console.log('🔍 Current date info:', { today: today.toISOString().split('T')[0], currentYear, currentMonth });
        
        switch (timePeriod) {
            case 'current_month':
                // Fix: Use proper date calculation for current month
                const year = currentYear;
                const month = currentMonth + 1; // Convert to 1-based month
                const startDate = `${year}-${month.toString().padStart(2, '0')}-01`;
                const endDate = `${year}-${month.toString().padStart(2, '0')}-${new Date(year, currentMonth + 1, 0).getDate().toString().padStart(2, '0')}`;
                const range = { start: startDate, end: endDate };
                console.log('🔍 getTimePeriodRange current_month:', range);
                return range;
            case 'past_30':
                const past30 = new Date(today);
                past30.setDate(today.getDate() - 30);
                return {
                    start: past30.toISOString().split('T')[0],
                    end: today.toISOString().split('T')[0]
                };
            case 'past_60':
                const past60 = new Date(today);
                past60.setDate(today.getDate() - 60);
                return {
                    start: past60.toISOString().split('T')[0],
                    end: today.toISOString().split('T')[0]
                };
            case 'next_30':
                const next30 = new Date(today);
                next30.setDate(today.getDate() + 30);
                return {
                    start: today.toISOString().split('T')[0],
                    end: next30.toISOString().split('T')[0]
                };
            case 'next_60':
                const next60 = new Date(today);
                next60.setDate(today.getDate() + 60);
                return {
                    start: today.toISOString().split('T')[0],
                    end: next60.toISOString().split('T')[0]
                };
        }
    }

    function isPolicyInTimePeriod(policy, timePeriod) {
        const range = getTimePeriodRange(timePeriod);
        const policyDate = policy.endDate;
        const isInRange = policyDate >= range.start && policyDate <= range.end;
        
        // Debug first few policies
        if (Math.random() < 0.1) { // Log 10% to avoid spam
            console.log('🔍 isPolicyInTimePeriod:', {
                policyId: policy.id,
                policyDate,
                range,
                isInRange,
                comparison: `${policyDate} >= ${range.start} && ${policyDate} <= ${range.end}`
            });
        }
        
        return isInRange;
    }

    // Check if policy has been renewed (has PolicyVersion records)
    function isPolicyRenewed(policy) {
        const result = policy.hasRenewal || false;
        if (Math.random() < 0.1) { // Log 10% to avoid spam
            console.log('🔍 isPolicyRenewed:', {
                policyId: policy.id,
                hasRenewal: policy.hasRenewal,
                result
            });
        }
        return result;
    }

    function escapeHtml(unsafe) {
        return unsafe
             .replace(/&/g, "&amp;")
             .replace(/</g, "&lt;")
             .replace(/>/g, "&gt;")
             .replace(/"/g, "&quot;")
             .replace(/'/g, "&#039;");
    }

    function vehicleTypeBadge(displayLabel, policyType) {
        const base = (policyType || '').toLowerCase();
        const cls = base === 'motor' ? 'motor' : (base === 'health' ? 'health' : (base === 'life' ? 'life' : 'default'));
        return `<span class="policy-type-badge ${cls}">${escapeHtml(displayLabel || '-')}</span>`;
    }

    function statusFromDaysLeft(n, timePeriod, isRenewed) {
        // If policy has been renewed, it's completed
        if (isRenewed) {
            return { label: 'Renewed', cls: 'completed' };
        }
        
        // For past periods, all non-renewed policies are overdue
        if (timePeriod === 'past_30' || timePeriod === 'past_60') {
            return { label: 'Overdue', cls: 'overdue' };
        }
        
        // For current month and future periods, calculate based on days left
        if (n < 0) return { label: 'Overdue', cls: 'overdue' };
        if (n <= 7) return { label: 'Pending', cls: 'pending' };
        if (n <= 30) return { label: 'In Progress', cls: 'inprogress' };
        return { label: 'Pending', cls: 'pending' };
    }

    function priorityFromDaysLeft(n, timePeriod) {
        // For past periods, all policies are high priority (overdue)
        if (timePeriod === 'past_30' || timePeriod === 'past_60') {
            return { label: 'High', cls: 'high' };
        }
        
        // For current month and future periods, calculate based on days left
        if (n <= 7) return { label: 'High', cls: 'high' };
        if (n <= 30) return { label: 'Medium', cls: 'medium' };
        return { label: 'Low', cls: 'low' };
    }

    function renderTable() {
        const start = (state.page - 1) * state.perPage;
        const end = start + state.perPage;
        const pageItems = state.filtered.slice(start, end);
        els.tbody.innerHTML = '';

        if (pageItems.length === 0) {
            els.tbody.innerHTML = `<tr><td colspan="9" style="text-align:center; padding:12px;">No records found</td></tr>`;
        } else {
            const currentTimePeriod = els.timePeriodFilter.value || 'current_month';
            pageItems.forEach((row, idx) => {
                const serial = start + idx + 1;
                const daysCls = row.daysLeft < 0 ? 'urgent' : (row.daysLeft <= 7 ? 'urgent' : (row.daysLeft <= 30 ? 'warning' : 'safe'));
                const daysText = row.daysLeft < 0 ? `${Math.abs(row.daysLeft)} days overdue` : `${row.daysLeft} days`;
                const assignedTo = row.agentName || '-';
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${serial}</td>
                    <td>${row.customerName}</td>
                    <td>${row.phone || '-'}</td>
                    <td>
                        ${vehicleTypeBadge(row.vehicleDisplay || row.policyType, row.policyType)}
                        ${row.vehicleNumber ? `<div class="vehicle-subtext">${row.vehicleNumber}</div>` : ''}
                    </td>
                    <td>${row.endDate}</td>
                    <td><span class="days-left ${daysCls}">${daysText}</span></td>
                    <td>₹${Number(row.premium || 0).toLocaleString('en-IN', { minimumFractionDigits: 0, maximumFractionDigits: 2 })}</td>
                    <td>${assignedTo}</td>
                    <td>
                        <div class="action-buttons">
                            <button class="action-btn edit" data-action="edit" data-policy-id="${row.id}" title="Edit"><i class="fas fa-edit"></i></button>
                            <button class="action-btn view" data-action="view" data-policy-id="${row.id}" title="View"><i class="fas fa-eye"></i></button>
                            <button class="action-btn call" data-action="call" data-phone="${row.phone || ''}" title="Call"><i class="fas fa-phone"></i></button>
                        </div>
                    </td>`;
                els.tbody.appendChild(tr);
            });
        }

        // Update pagination info
        const total = state.filtered.length;
        const showingStart = total === 0 ? 0 : start + 1;
        const showingEnd = Math.min(end, total);
        els.startRec.textContent = String(showingStart);
        els.endRec.textContent = String(showingEnd);
        els.totalRecs.textContent = String(total);

        renderPagination(total);
    }

    function exportFilteredToCSV() {
        const rows = state.filtered.slice();
        if (!rows.length) {
            alert('No records to export');
            return;
        }
        const headers = ['Sl. No', 'Customer Name', 'Phone', 'Vehicle / Type', 'Expiry Date', 'Days Left', 'Premium', 'Assigned To'];
        const csv = [headers.join(',')];
        rows.forEach((row, idx) => {
            const daysText = row.daysLeft < 0 ? `${Math.abs(row.daysLeft)} days overdue` : `${row.daysLeft} days`;
            const line = [
                idx + 1,
                (row.customerName||'').replace(/,/g,' '),
                (row.phone||'').replace(/,/g,' '),
                (row.vehicleDisplay||row.policyType||'').replace(/,/g,' '),
                row.endDate || '',
                daysText,
                Number(row.premium || 0).toFixed(2),
                (row.agentName||'').replace(/,/g,' ')
            ];
            csv.push(line.join(','));
        });
        const blob = new Blob([csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = `renewals_export_${new Date().toISOString().split('T')[0]}.csv`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function getSortValue(row, column) {
        switch (column) {
            case 'id':
            case 'policyId':
                return Number(row.id) || 0;
            case 'customerName':
                return (row.customerName || '').toLowerCase();
            case 'phone':
                return (row.phone || '').toLowerCase();
            case 'vehicleType':
                return (row.vehicleDisplay || row.policyType || '').toLowerCase();
            case 'expiryDate':
                return row.endDate || '';
            case 'daysLeft':
                return Number(row.daysLeft) || 0;
            case 'premium':
                return Number(row.premium) || 0;
            case 'assignedTo':
                return (row.agentName || '').toLowerCase();
            default:
                return row.endDate || '';
        }
    }

    function sortFiltered() {
        const col = state.sort.column;
        const dir = state.sort.direction === 'asc' ? 1 : -1;
        state.filtered.sort((a, b) => {
            const av = getSortValue(a, col);
            const bv = getSortValue(b, col);
            if (av < bv) return -1 * dir;
            if (av > bv) return 1 * dir;
            return 0;
        });
    }

    function renderPagination(total) {
        const totalPages = Math.ceil(total / state.perPage) || 1;
        els.pages.innerHTML = '';
        for (let i = 1; i <= totalPages; i++) {
            const btn = document.createElement('button');
            btn.className = 'page-number' + (i === state.page ? ' active' : '');
            btn.textContent = String(i);
            btn.addEventListener('click', () => { state.page = i; renderTable(); });
            els.pages.appendChild(btn);
        }
        els.prev.disabled = state.page <= 1;
        els.next.disabled = state.page >= totalPages;
    }

    function applyFilters() {
        console.log('🔍 applyFilters called');
        
        const q = (els.search.value || '').toLowerCase();
        const timePeriodFilter = els.timePeriodFilter.value || 'current_month';
        
        console.log('🔍 Filter values:', { q, timePeriodFilter });
        
        state.filtered = state.all.filter(row => {
            const matchTimePeriod = isPolicyInTimePeriod(row, timePeriodFilter);
            if (!matchTimePeriod) return false;
            const matchText = !q || (row.searchIndex || '').includes(q);
            return matchText;
        });
        
        // Expose filtered array for export in global scope (used by app.js export)
        window.renewalsV2Filtered = state.filtered.slice();
        
        // Apply current sort
        sortFiltered();
        
        console.log('🔍 Filtered results:', state.filtered.length, 'out of', state.all.length);
        
        state.page = 1;
        updateStats();
        renderTable();
    }

    async function updateStats() {
        const timePeriodFilter = els.timePeriodFilter.value || 'current_month';
        
        console.log('🔍 updateStats called with filter:', timePeriodFilter);
        console.log('🔍 Total policies in state.all:', state.all.length);

        // For the current month, use server-side summary based on latest period end-date
        if (timePeriodFilter === 'current_month') {
            try {
                const res = await fetch('/api/renewals/summary?time_period=current_month', {
                    headers: { 'Accept': 'application/json' }
                });
                
                if (res.ok) {
                    const payload = await res.json();
                    const totals = payload.totals || {};
                    const pending = Number(totals.pending ?? 0);
                    const completed = Number(totals.completed ?? 0);
                    const total = Number(totals.total ?? (pending + completed));

                    console.log('🔍 Server summary for current month:', { pending, completed, total, payload });

                    state.totals = { pending, completed, total };
                    els.stats.pending.textContent = String(pending);
                    els.stats.completed.textContent = String(completed);
                    els.stats.total.textContent = String(total);

                    console.log('🔍 DOM updated from server summary. Current values:', {
                        pending: els.stats.pending.textContent,
                        completed: els.stats.completed.textContent,
                        total: els.stats.total.textContent
                    });

                    return;
                } else {
                    console.warn('🔍 Failed to fetch server renewals summary, falling back to client logic. Status:', res.status);
                }
            } catch (err) {
                console.error('🔍 Error fetching server renewals summary, falling back to client logic:', err);
            }
        }
        
        // Fallback / non-current-month logic: compute from client-side dataset
        // Filter policies by time period first
        const timeFilteredPolicies = state.all.filter(row => isPolicyInTimePeriod(row, timePeriodFilter));
        
        console.log('🔍 Time filtered policies (client fallback):', timeFilteredPolicies.length, 'out of', state.all.length);
        console.log('🔍 Sample filtered policies:', timeFilteredPolicies.slice(0, 3));
        
        // Calculate stats based on simple hasRenewal flag
        let pending = 0, completed = 0;
        
        timeFilteredPolicies.forEach(policy => {
            const isRenewed = isPolicyRenewed(policy);
            if (isRenewed) {
                completed++;
            } else {
                pending++; // All non-renewed policies are pending (overdue or expiring soon)
            }
        });
        
        const total = timeFilteredPolicies.length;
        
        console.log('🔍 Final calculated counts (client fallback):', { pending, completed, total });
        
        state.totals = { pending, completed, total };

        els.stats.pending.textContent = String(pending);
        els.stats.completed.textContent = String(completed);
        els.stats.total.textContent = String(total);
    }

    async function fetchPolicies() {
        const res = await fetch('/api/policies', { headers: { 'Accept': 'application/json' } });
        if (!res.ok) throw new Error('Failed to load policies');
        const data = await res.json();
        
        console.log('🔍 Raw API data sample:', data.policies.slice(0, 2));
        
        const items = (data.policies || []).map(p => {
            const customerName = p.customerName || '';
            const endDate = p.endDate || '';
            const dleft = daysUntil(endDate);
            const policyType = p.policyType || p.policy_type || '';
            const phone = p.phone || '';
            const vehicleTypeRaw = p.vehicleType || p.vehicle_type || '';
            let vehicleDisplay = '';
            if ((policyType || '').toLowerCase() === 'motor') {
                vehicleDisplay = vehicleTypeRaw || 'Motor';
            } else if (policyType) {
                vehicleDisplay = policyType;
            } else {
                vehicleDisplay = 'Policy';
            }
            vehicleDisplay = vehicleDisplay.toUpperCase();
            const vehicleNumber = p.vehicleNumber || p.vehicle_number || '';
            const agentName = p.agentName || '';
            const premium = p.premium || 0;
            const searchIndex = [
                customerName,
                phone,
                policyType,
                vehicleTypeRaw,
                vehicleDisplay,
                vehicleNumber,
                agentName,
                endDate
            ].filter(Boolean).join(' ').toLowerCase();
            return {
                id: p.id,
                customerName,
                phone,
                policyType,
                vehicleNumber,
                vehicleDisplay,
                endDate,
                daysLeft: dleft,
                premium,
                agentName,
                hasRenewal: p.hasRenewal || false,
                searchIndex
            };
        });
        
        console.log('🔍 Mapped items sample:', items.slice(0, 2));
        
        // sort ascending by expiry
        items.sort((a,b) => a.endDate.localeCompare(b.endDate));
        state.all = items;
        state.filtered = items.slice();
        
        console.log('🔍 State updated. Total policies:', state.all.length);
        console.log('🔍 Policies with hasRenewal=true:', state.all.filter(p => p.hasRenewal).length);
        
        updateStats();
        renderTable();
    }

    function bindEvents() {
        console.log('🔍 bindEvents called');
        
        els.rowsPerPage.addEventListener('change', () => {
            state.perPage = parseInt(els.rowsPerPage.value || '10', 10);
            state.page = 1;
            // Re-apply current filters to keep dataset constrained to the selected time period
            applyFilters();
        });
        els.prev.addEventListener('click', () => { if (state.page > 1) { state.page--; renderTable(); } });
        els.next.addEventListener('click', () => {
            const totalPages = Math.ceil(state.filtered.length / state.perPage) || 1;
            if (state.page < totalPages) { state.page++; renderTable(); }
        });
        els.search.addEventListener('input', () => {
            console.log('🔍 Search input changed');
            applyFilters();
        });
        if (els.exportBtn) {
            els.exportBtn.addEventListener('click', () => {
                // Prefer local exporter to avoid legacy global side-effects
                exportFilteredToCSV();
            });
        }
        // Table sorting (toggle asc/desc)
        document.querySelectorAll('#renewalsTable thead th[data-sort]').forEach(th => {
            th.addEventListener('click', () => {
                const col = th.getAttribute('data-sort');
                if (!col) return;
                if (state.sort.column === col) {
                    state.sort.direction = state.sort.direction === 'asc' ? 'desc' : 'asc';
                } else {
                    state.sort.column = col;
                    state.sort.direction = 'asc';
                }
                sortFiltered();
                state.page = 1;
                renderTable();
            });
        });
        els.timePeriodFilter.addEventListener('change', () => {
            console.log('🔍 Time period filter changed to:', els.timePeriodFilter.value);
            applyFilters();
            updateStats();
        });
        // Removed status & priority filters
    }

    // init
    document.addEventListener('DOMContentLoaded', async () => {
        console.log('🔍 DOMContentLoaded - Initializing renewals page');
        console.log('🔍 Current path:', window.location.pathname);
        console.log('🔍 Using new policy-based logic (not old renewals table)');
        // Signal v2 mode to global scripts to avoid legacy bindings
        window.RENEWALS_V2 = true;

        bindEvents();
        try {
            await fetchPolicies();
            console.log('🔍 fetchPolicies completed');
            applyFilters();
            console.log('🔍 applyFilters completed');

            // Add a small delay to ensure no conflicts from global app.js
            setTimeout(() => {
                console.log('🔍 VERIFICATION: Final counts after initialization:');
                console.log('🔍 Pending:', els.stats.pending.textContent);
                console.log('🔍 Completed:', els.stats.completed.textContent);
                console.log('🔍 Total:', els.stats.total.textContent);
            }, 1000);
        } catch (err) {
            console.error('🔍 Error in initialization:', err);
            els.tbody.innerHTML = `<tr><td colspan="9" style="text-align:center; padding:12px; color:#EF4444;">Failed to load data</td></tr>`;
        }
    });
})();
</script>
@endpush

@endsection
