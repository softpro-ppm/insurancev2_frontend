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
        statusFilter: document.getElementById('renewalStatusFilter'),
        priorityFilter: document.getElementById('renewalPriorityFilter'),
        stats: {
            pending: document.getElementById('pendingRenewalsCount'),
            completed: document.getElementById('completedRenewalsCount'),
            total: document.getElementById('totalRenewalsCount')
        }
    };

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
        if (Math.random() < 0.05) { // Log 5% to avoid spam
            console.log('🔍 isPolicyInTimePeriod:', {
                policyId: policy.id,
                policyDate,
                range,
                isInRange
            });
        }
        
        return isInRange;
    }

    // Check if policy has been renewed (has PolicyVersion records)
    function isPolicyRenewed(policy) {
        return policy.hasRenewal || false;
    }

    function policyTypeBadge(type) {
        const cls = (type||'').toLowerCase();
        return `<span class="policy-type-badge ${cls}">${type||''}</span>`;
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
            els.tbody.innerHTML = `<tr><td colspan="10" style="text-align:center; padding:12px;">No records found</td></tr>`;
        } else {
            const currentTimePeriod = els.timePeriodFilter.value || 'current_month';
            pageItems.forEach((row, idx) => {
                const serial = start + idx + 1;
                const isRenewed = isPolicyRenewed(row);
                const status = statusFromDaysLeft(row.daysLeft, currentTimePeriod, isRenewed);
                const pr = priorityFromDaysLeft(row.daysLeft, currentTimePeriod);
                const daysCls = row.daysLeft < 0 ? 'urgent' : (row.daysLeft <= 7 ? 'urgent' : (row.daysLeft <= 30 ? 'warning' : 'safe'));
                const daysText = row.daysLeft < 0 ? `${Math.abs(row.daysLeft)} days overdue` : `${row.daysLeft} days`;
                const assignedTo = row.agentName || '-';
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${serial}</td>
                    <td>#${(row.id || 0).toString().padStart(3, '0')}</td>
                    <td>${row.customerName}</td>
                    <td>${policyTypeBadge(row.policyType)}</td>
                    <td>${row.endDate}</td>
                    <td><span class="days-left ${daysCls}">${daysText}</span></td>
                    <td><span class="status-badge ${status.cls}">${status.label}</span></td>
                    <td><span class="priority-badge ${pr.cls}">${pr.label}</span></td>
                    <td>${assignedTo}</td>
                    <td>
                        <div class="action-buttons">
                            <button class="action-btn edit" title="Edit"><i class="fas fa-edit"></i></button>
                            <button class="action-btn" title="View"><i class="fas fa-eye"></i></button>
                            <button class="action-btn" title="Call"><i class="fas fa-phone"></i></button>
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
        const q = (els.search.value || '').toLowerCase();
        const timePeriodFilter = els.timePeriodFilter.value || 'current_month';
        const statusFilter = els.statusFilter.value; // '', 'Pending','In Progress','Completed','Overdue'
        const priorityFilter = els.priorityFilter.value; // '', 'High','Medium','Low'

        state.filtered = state.all.filter(row => {
            // First filter by time period
            const matchTimePeriod = isPolicyInTimePeriod(row, timePeriodFilter);
            if (!matchTimePeriod) return false;

            // Then filter by text search
            const matchText = !q ||
                (row.id || 0).toString().toLowerCase().includes(q) ||
                row.customerName.toLowerCase().includes(q) ||
                (row.agentName||'').toLowerCase().includes(q) ||
                (row.policyType||'').toLowerCase().includes(q);

            // Then filter by status and priority (using time period for calculation)
            const isRenewed = isPolicyRenewed(row);
            const st = statusFromDaysLeft(row.daysLeft, timePeriodFilter, isRenewed).label;
            const pr = priorityFromDaysLeft(row.daysLeft, timePeriodFilter).label;

            const matchStatus = !statusFilter || st === statusFilter;
            const matchPriority = !priorityFilter || pr === priorityFilter;

            return matchText && matchStatus && matchPriority;
        })
        // Sort by expiry date ascending
        .sort((a, b) => a.endDate.localeCompare(b.endDate));

        state.page = 1;
        updateStats(); // Update stats when filters change
        renderTable();
    }

    function updateStats() {
        const timePeriodFilter = els.timePeriodFilter.value || 'current_month';
        
        console.log('🔍 updateStats called with filter:', timePeriodFilter);
        
        // Filter policies by time period first
        const timeFilteredPolicies = state.all.filter(row => isPolicyInTimePeriod(row, timePeriodFilter));
        
        console.log('🔍 Time filtered policies:', timeFilteredPolicies.length, 'out of', state.all.length);
        
        // Calculate stats based on new logic
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
        
        console.log('🔍 Calculated counts:', { pending, completed, total });
        
        state.totals = { pending, completed, total };

        els.stats.pending.textContent = String(pending);
        els.stats.completed.textContent = String(completed);
        els.stats.total.textContent = String(total);
    }

    async function fetchPolicies() {
        const res = await fetch('/api/policies', { headers: { 'Accept': 'application/json' } });
        if (!res.ok) throw new Error('Failed to load policies');
        const data = await res.json();
        const items = (data.policies || []).map(p => {
            const endDate = p.endDate;
            const dleft = daysUntil(endDate);
            return {
                id: p.id,
                customerName: p.customerName,
                policyType: p.policyType,
                endDate: endDate,
                daysLeft: dleft,
                status: p.status,
                agentName: p.agentName || '',
                hasRenewal: p.hasRenewal || false // Add the hasRenewal field
            };
        });
        // sort ascending by expiry
        items.sort((a,b) => a.endDate.localeCompare(b.endDate));
        state.all = items;
        state.filtered = items.slice();
        updateStats();
        renderTable();
    }

    async function tryFetchCompletedFromRenewals() {
        try {
            const res = await fetch('/api/renewals', { headers: { 'Accept': 'application/json' } });
            if (!res.ok) return; // ignore
            const data = await res.json();
            const list = data.renewals || [];
            const now = new Date();
            const y = now.getFullYear();
            const m = now.getMonth();
            const completedThisMonth = list.filter(r => {
                if ((r.status||'') !== 'Completed') return false;
                const created = r.createdAt ? new Date(r.createdAt + 'T00:00:00') : null;
                return created && created.getFullYear() === y && created.getMonth() === m;
            }).length;
            state.totals.completed = completedThisMonth;
            els.stats.completed.textContent = String(completedThisMonth);
            els.stats.completed.dataset.bound = '1';
        } catch (e) {
            // ignore
        }
    }

    function bindEvents() {
        els.rowsPerPage.addEventListener('change', () => {
            state.perPage = parseInt(els.rowsPerPage.value || '10', 10);
            state.page = 1;
            renderTable();
        });
        els.prev.addEventListener('click', () => { if (state.page > 1) { state.page--; renderTable(); } });
        els.next.addEventListener('click', () => {
            const totalPages = Math.ceil(state.filtered.length / state.perPage) || 1;
            if (state.page < totalPages) { state.page++; renderTable(); }
        });
        els.search.addEventListener('input', () => applyFilters());
        els.timePeriodFilter.addEventListener('change', () => {
            applyFilters();
            updateStats();
        });
        els.statusFilter.addEventListener('change', () => applyFilters());
        els.priorityFilter.addEventListener('change', () => applyFilters());
    }

    // init
    document.addEventListener('DOMContentLoaded', async () => {
        bindEvents();
        try {
            await Promise.all([fetchPolicies(), tryFetchCompletedFromRenewals()]);
            applyFilters();
        } catch (err) {
            console.error(err);
            els.tbody.innerHTML = `<tr><td colspan="10" style="text-align:center; padding:12px; color:#EF4444;">Failed to load data</td></tr>`;
        }
    });
})();
</script>
@endpush

@endsection
