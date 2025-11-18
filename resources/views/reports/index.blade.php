@extends('layouts.insurance')

@section('title', 'Reports & Analytics - Insurance Management System')

@section('content')
<script>
    // Ensure global app.js skips legacy reports init BEFORE it loads
    window.REPORTS_V2 = true;
</script>

<div class="page active" id="reports">
    <div class="page-header">
        <h1>Reports & Analytics</h1>
    </div>
    <div class="page-content">
        <!-- Reports Controls -->
        <div class="reports-controls">
            <div class="controls-left">
                <div class="date-range-picker">
                    <label for="reportStartDate">From:</label>
                    <input type="date" id="reportStartDate">
                    <label for="reportEndDate">To:</label>
                    <input type="date" id="reportEndDate">
                </div>
            </div>
            <div class="controls-right">
                <select id="businessTypeFilter" class="business-type-filter">
                    <option value="all">All</option>
                    <option value="Self">Self</option>
                    <!-- Agent options will be populated dynamically -->
                </select>
                <button class="export-report-btn" id="exportReportBtn">
                    <i class="fas fa-download"></i>
                    Export
                </button>
            </div>
        </div>

        <!-- Key Performance Indicators -->
        <div class="kpi-section">
            <h3>Key Performance Indicators</h3>
            <div class="kpi-grid">
                <div class="kpi-card glass-effect">
                    <div class="kpi-icon premium">
                        <i class="fas fa-rupee-sign"></i>
                    </div>
                    <div class="kpi-content">
                        <h4>Total Premium</h4>
                        <p class="kpi-value" id="totalPremiumKPI">₹0</p>
                        <span class="kpi-change" id="premiumChange">0%</span>
                    </div>
                </div>
                <div class="kpi-card glass-effect">
                    <div class="kpi-icon revenue">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="kpi-content">
                        <h4>Total Revenue</h4>
                        <p class="kpi-value" id="totalRevenueKPI">₹0</p>
                        <span class="kpi-change" id="revenueChange">0%</span>
                    </div>
                </div>
                <div class="kpi-card glass-effect">
                    <div class="kpi-icon policies">
                        <i class="fas fa-file-contract"></i>
                    </div>
                    <div class="kpi-content">
                        <h4>Active Policies</h4>
                        <p class="kpi-value" id="activePoliciesKPI">0</p>
                        <span class="kpi-change" id="policiesChange">0%</span>
                    </div>
                </div>
            </div>
        </div>


        <!-- Reports Tabs -->
        <div class="reports-tabs">
            <button class="tab-btn active" data-tab="policies">Policies</button>
            <button class="tab-btn" data-tab="renewals">Renewals</button>
            <button class="tab-btn" data-tab="agents">Agents</button>
        </div>

        <!-- Reports Tables -->
        <div class="reports-tables">
            <!-- Policies Table -->
            <div class="data-table-container glass-effect" id="policiesTable">
                <div class="table-header">
                    <h3>Policies Report</h3>
                    <div class="table-controls">
                        <input type="text" id="policiesSearch" placeholder="Search policies...">
                        <select class="rows-per-page" id="policiesRowsPerPage">
                            <option value="10" selected>10 rows</option>
                            <option value="25">25 rows</option>
                            <option value="50">50 rows</option>
                            <option value="100">100 rows</option>
                        </select>
                    </div>
                </div>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Sl. No</th>
                                <th data-sort="customerName">Customer Name</th>
                                <th data-sort="policyType">Policy Type</th>
                                <th data-sort="vehicleNumber">Vehicle Number</th>
                                <th data-sort="companyName">Company</th>
                                <th data-sort="premium">Premium</th>
                                <th data-sort="status">Status</th>
                                <th data-sort="startDate">Start Date</th>
                            </tr>
                        </thead>
                        <tbody id="policiesTableBody">
                            <!-- Data will be populated here -->
                        </tbody>
                    </table>
                </div>
                <div class="table-pagination">
                    <div class="pagination-info">
                        Showing <span id="policiesStartRecord">1</span> to <span id="policiesEndRecord">10</span> of <span id="policiesTotalRecords">0</span> entries
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

            <!-- Renewals Table -->
            <div class="data-table-container glass-effect" id="renewalsTable" style="display: none;">
                <div class="table-header">
                    <h3>Pending Renewals</h3>
                    <div class="table-controls">
                        <input type="text" id="renewalsSearch" placeholder="Search pending renewals...">
                        <select class="rows-per-page" id="renewalsRowsPerPage">
                            <option value="10" selected>10 rows</option>
                            <option value="25">25 rows</option>
                            <option value="50">50 rows</option>
                            <option value="100">100 rows</option>
                        </select>
                    </div>
                </div>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Sl. No</th>
                                <th data-sort="customerName">Customer Name</th>
                                <th data-sort="phone">Phone</th>
                                <th data-sort="policyType">Policy Type</th>
                                <th data-sort="companyName">Company</th>
                                <th data-sort="endDate">End Date</th>
                                <th data-sort="daysLeft">Days Left</th>
                                <th data-sort="premium">Premium</th>
                                <th data-sort="status">Status</th>
                            </tr>
                        </thead>
                        <tbody id="renewalsTableBody">
                            <!-- Data will be populated here -->
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

            <!-- Agents Table -->
            <div class="data-table-container glass-effect" id="agentsTable" style="display: none;">
                <div class="table-header">
                    <h3>Agents Report</h3>
                    <div class="table-controls">
                        <input type="text" id="agentsSearch" placeholder="Search agents...">
                        <select class="rows-per-page" id="agentsRowsPerPage">
                            <option value="10" selected>10 rows</option>
                            <option value="25">25 rows</option>
                            <option value="50">50 rows</option>
                            <option value="100">100 rows</option>
                        </select>
                    </div>
                </div>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Sl. No</th>
                                <th data-sort="name">Business Type</th>
                                <th data-sort="phone">Phone</th>
                                <th data-sort="policies">Policies Sold</th>
                                <th data-sort="totalPremium">Total Premium</th>
                                <th data-sort="performance">Performance</th>
                            </tr>
                        </thead>
                        <tbody id="agentsTableBody">
                            <!-- Data will be populated here -->
                        </tbody>
                    </table>
                </div>
                <div class="table-pagination">
                    <div class="pagination-info">
                        Showing <span id="agentsStartRecord">1</span> to <span id="agentsEndRecord">10</span> of <span id="agentsTotalRecords">0</span> entries
                    </div>
                    <div class="pagination-controls">
                        <button class="pagination-btn" id="agentsPrevPage" disabled>
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <div class="page-numbers" id="agentsPageNumbers">
                            <!-- Page numbers will be generated by JavaScript -->
                        </div>
                        <button class="pagination-btn" id="agentsNextPage">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Reports Page Styles - matching global theme */

.controls-left {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.date-range-picker {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.date-range-picker label {
    font-weight: 600;
    color: #374151;
}

.date-range-picker input {
    padding: 0.5rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    background: white;
}

.controls-right {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.business-type-filter {
    padding: 0.75rem 1rem;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    background: white;
    font-weight: 500;
    color: #374151;
    cursor: pointer;
    min-width: 150px;
    transition: all 0.3s ease;
}

.business-type-filter:hover {
    border-color: #4F46E5;
}

.business-type-filter:focus {
    outline: none;
    border-color: #4F46E5;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

.dark-theme .business-type-filter {
    background: rgba(30, 41, 59, 0.8);
    border-color: rgba(255, 255, 255, 0.1);
    color: #F1F5F9;
}

.dark-theme .business-type-filter:hover {
    border-color: #6366F1;
}

.filter-group {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.filter-group label {
    font-weight: 600;
    color: #374151;
}

.filter-group select {
    padding: 0.5rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    background: white;
}

.generate-report-btn, .export-report-btn {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
}

.generate-report-btn {
    background: #8b5cf6;
    color: white;
}

.generate-report-btn:hover {
    background: #7c3aed;
    transform: translateY(-2px);
}

.export-report-btn {
    background: #10b981;
    color: white;
}

.export-report-btn:hover {
    background: #059669;
    transform: translateY(-2px);
}

.kpi-section {
    margin-bottom: 2rem;
}

.kpi-section h3 {
    margin-bottom: 1rem;
    color: #374151;
    font-size: 1.25rem;
    font-weight: 700;
}

.kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.kpi-card {
    padding: 1.5rem;
    border-radius: 12px;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.kpi-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.kpi-icon.premium { background: linear-gradient(135deg, #f59e0b, #d97706); }
.kpi-icon.revenue { background: linear-gradient(135deg, #10b981, #059669); }
.kpi-icon.policies { background: linear-gradient(135deg, #3b82f6, #2563eb); }

.kpi-content h4 {
    margin: 0 0 0.5rem 0;
    color: #6b7280;
    font-size: 0.875rem;
    font-weight: 600;
}

.kpi-value {
    margin: 0 0 0.25rem 0;
    font-size: 1.875rem;
    font-weight: 700;
    color: #111827;
}

.kpi-change {
    font-size: 0.875rem;
    font-weight: 600;
}

.kpi-change.positive { color: #10b981; }
.kpi-change.negative { color: #ef4444; }

.charts-section {
    margin-bottom: 2rem;
}

.chart-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
}

.chart-container {
    padding: 1.5rem;
    border-radius: 12px;
}

.chart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.chart-header h3 {
    margin: 0;
    color: #374151;
    font-size: 1.125rem;
    font-weight: 600;
}

.chart-controls select {
    padding: 0.5rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    background: white;
}

.chart-body {
    height: 300px;
    position: relative;
}

.reports-tabs {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
    border-bottom: 2px solid #e5e7eb;
}

.tab-btn {
    padding: 0.75rem 1.5rem;
    border: none;
    background: transparent;
    color: #6b7280;
    font-weight: 600;
    cursor: pointer;
    border-bottom: 2px solid transparent;
    transition: all 0.3s ease;
}

.tab-btn.active {
    color: #8b5cf6;
    border-bottom-color: #8b5cf6;
}

.tab-btn:hover {
    color: #8b5cf6;
}

.reports-tables {
    margin-bottom: 2rem;
}

/* Tables use global theme styles from styles.css */
.report-table {
    background: rgba(255, 255, 255, 0.7);
    border-radius: 16px;
    padding: 1.5rem;
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

.dark-theme .report-table {
    background: rgba(30, 41, 59, 0.7);
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
}

.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 1rem 1.5rem;
    border-radius: 12px;
    color: white;
    font-weight: 600;
    z-index: 10000;
    animation: slideIn 0.3s ease;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    display: flex;
    align-items: center;
    gap: 0.75rem;
    backdrop-filter: blur(10px);
}

.notification::before {
    font-family: 'Font Awesome 6 Free';
    font-weight: 900;
    font-size: 1.25rem;
}

.notification-success { 
    background: linear-gradient(135deg, #10b981, #059669);
}

.notification-success::before {
    content: '\f058'; /* fa-check-circle */
}

.notification-error { 
    background: linear-gradient(135deg, #ef4444, #dc2626);
}

.notification-error::before {
    content: '\f071'; /* fa-exclamation-triangle */
}

.notification-info { 
    background: linear-gradient(135deg, #4F46E5, #7C3AED);
}

.notification-info::before {
    content: '\f05a'; /* fa-info-circle */
}

@keyframes slideIn {
    from { 
        transform: translateX(400px); 
        opacity: 0; 
    }
    to { 
        transform: translateX(0); 
        opacity: 1; 
    }
}

.loading-overlay,
#loadingOverlay {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    background: rgba(255, 255, 255, 0.9) !important;
    backdrop-filter: blur(10px) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    z-index: 12000 !important;
}

.dark-theme .loading-overlay,
.dark-theme #loadingOverlay {
    background: rgba(15, 23, 42, 0.9) !important;
}

.loading-content {
    padding: 2rem;
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    text-align: center;
}

.dark-theme .loading-content {
    background: rgba(30, 41, 59, 0.95);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.loading-spinner {
    width: 50px;
    height: 50px;
    border: 4px solid rgba(79, 70, 229, 0.3);
    border-top: 4px solid #4F46E5;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 1rem;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Ensure top navigation (Admin profile dropdown) stays clickable above any reports overlays */
.top-nav {
    position: fixed !important;
    z-index: 11000 !important;
    overflow: visible !important;
}

.top-nav .nav-right {
    overflow: visible !important;
    position: relative;
    z-index: 11001 !important;
}

.top-nav .profile-dropdown {
    overflow: visible !important;
    position: relative;
    z-index: 11002 !important;
}

.top-nav .dropdown-menu {
    z-index: 11003 !important;
}

/* Responsive Design */
@media (max-width: 768px) {
    .reports-controls {
        flex-direction: column;
        align-items: stretch;
    }
    
    .controls-left,
    .controls-right {
        width: 100%;
    }
    
    .date-range-picker {
        flex-direction: column;
        align-items: stretch !important;
    }
    
    .kpi-grid {
        grid-template-columns: 1fr !important;
    }
    
    .reports-tabs {
        flex-direction: column;
    }
    
    .tab-btn {
        width: 100%;
    }
}
</style>

<script>
(function() {
// Reports page scoped variables (avoid clashing with global app.js variables)
let allPolicies = [];
let allRenewals = [];
let allAgents = [];
let currentReportData = {};

// Pagination state
let paginationState = {
    policies: { currentPage: 1, rowsPerPage: 10 },
    renewals: { currentPage: 1, rowsPerPage: 10 },
    agents: { currentPage: 1, rowsPerPage: 10 }
};

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Reports page initialized');
    
    // Set default dates: 01-04-2025 to current date
    const endDate = new Date();
    const startDate = new Date('2025-04-01');
    
    document.getElementById('reportStartDate').value = startDate.toISOString().split('T')[0];
    document.getElementById('reportEndDate').value = endDate.toISOString().split('T')[0];
    
    // Bind event listeners
    bindEventListeners();
    
    // Load initial data
    loadAllData();
});

function bindEventListeners() {
    // Export Report button
    const exportBtn = document.getElementById('exportReportBtn');
    if (exportBtn) {
        exportBtn.addEventListener('click', exportReport);
    }
    
    // Tab switching
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            switchTab(this.getAttribute('data-tab'));
        });
    });
    
    // Date range changes
    const startDateInput = document.getElementById('reportStartDate');
    const endDateInput = document.getElementById('reportEndDate');
    if (startDateInput) {
        startDateInput.addEventListener('change', loadAllData);
    }
    if (endDateInput) {
        endDateInput.addEventListener('change', loadAllData);
    }
    
    // Business type filter change
    const businessTypeFilter = document.getElementById('businessTypeFilter');
    if (businessTypeFilter) {
        businessTypeFilter.addEventListener('change', loadAllData);
    }
    
    // Pagination controls for each table - setup after a small delay to ensure DOM is ready
    setTimeout(() => {
        setupPaginationControls('policies');
        setupPaginationControls('renewals');
        setupPaginationControls('agents');
    }, 100);
}

function setupPaginationControls(tableType) {
    // Remove existing event listeners by cloning and replacing elements
    const rowsSelect = document.getElementById(tableType + 'RowsPerPage');
    if (rowsSelect) {
        // Clone and replace to remove old listeners
        const newRowsSelect = rowsSelect.cloneNode(true);
        rowsSelect.parentNode.replaceChild(newRowsSelect, rowsSelect);
        
        newRowsSelect.addEventListener('change', function() {
            console.log(`📊 ${tableType} rows per page changed to:`, this.value);
            paginationState[tableType].rowsPerPage = parseInt(this.value);
            paginationState[tableType].currentPage = 1;
            renderTableWithPagination(tableType);
        });
    }
    
    // Previous page button
    const prevBtn = document.getElementById(tableType + 'PrevPage');
    if (prevBtn) {
        // Remove old listener
        const newPrevBtn = prevBtn.cloneNode(true);
        prevBtn.parentNode.replaceChild(newPrevBtn, prevBtn);
        
        newPrevBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (paginationState[tableType].currentPage > 1) {
                paginationState[tableType].currentPage--;
                console.log(`📄 ${tableType} previous page:`, paginationState[tableType].currentPage);
                renderTableWithPagination(tableType);
            }
        });
    }
    
    // Next page button
    const nextBtn = document.getElementById(tableType + 'NextPage');
    if (nextBtn) {
        // Remove old listener
        const newNextBtn = nextBtn.cloneNode(true);
        nextBtn.parentNode.replaceChild(newNextBtn, nextBtn);
        
        newNextBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const totalPages = Math.ceil(getFilteredData(tableType).length / paginationState[tableType].rowsPerPage);
            if (paginationState[tableType].currentPage < totalPages) {
                paginationState[tableType].currentPage++;
                console.log(`📄 ${tableType} next page:`, paginationState[tableType].currentPage);
                renderTableWithPagination(tableType);
            }
        });
    }
    
    // Search input - use debounce for better performance
    const searchInput = document.getElementById(tableType + 'Search');
    if (searchInput) {
        // Clone and replace to remove old listeners
        const newSearchInput = searchInput.cloneNode(true);
        newSearchInput.value = searchInput.value; // Preserve value
        searchInput.parentNode.replaceChild(newSearchInput, searchInput);
        
        let searchTimeout;
        newSearchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                console.log(`🔍 ${tableType} search:`, this.value);
                paginationState[tableType].currentPage = 1;
                renderTableWithPagination(tableType);
            }, 300); // Debounce 300ms
        });
    }
}

function getFilteredData(tableType) {
    let data = [];
    const searchInput = document.getElementById(tableType + 'Search');
    const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';
    
    switch(tableType) {
        case 'policies':
            data = currentReportData.policies || [];
            break;
        case 'renewals':
            data = currentReportData.renewals || [];
            break;
        case 'agents':
            data = currentReportData.agents || [];
            break;
    }
    
    // Apply search filter
    if (searchTerm) {
        data = data.filter(item => {
            // Search across all string/number fields
            return Object.values(item).some(val => {
                if (val === null || val === undefined) return false;
                const strVal = String(val).toLowerCase();
                return strVal.includes(searchTerm);
            });
        });
    }
    
    console.log(`🔍 ${tableType} filtered data:`, data.length, 'items (search term:', searchTerm, ')');
    return data;
}

async function loadAllData() {
    console.log('📊 Loading all reports data...');
    showLoading();
    
    try {
        const startDate = document.getElementById('reportStartDate').value;
        const endDate = document.getElementById('reportEndDate').value;
        
        // Get filter value BEFORE repopulating dropdown
        const filterSelect = document.getElementById('businessTypeFilter');
        const businessTypeFilter = filterSelect ? filterSelect.value : 'all';
        
        console.log('📅 Date range:', startDate, 'to', endDate);
        console.log('🔍 Business type filter:', businessTypeFilter);
        
        // Fetch all data in parallel
        const [policiesRes, agentsRes] = await Promise.all([
            fetch('/api/policies').then(r => r.json()),
            fetch('/api/agents').then(r => r.json())
        ]);
        
        allPolicies = policiesRes.policies || [];
        allRenewals = []; // We'll use policies for pending renewals
        allAgents = agentsRes.agents || [];
        
        // Populate business type filter dropdown with agents (preserving current selection)
        populateBusinessTypeFilter(businessTypeFilter);
        
        console.log('📊 Data loaded:', {
            policies: allPolicies.length,
            renewals: allRenewals.length,
            agents: allAgents.length
        });
        
        // Filter data by date range and business type
        const filteredData = filterDataByDateRange(startDate, endDate, businessTypeFilter);
        
        console.log('📊 Filtered data:', {
            policies: filteredData.policies.length,
            renewals: filteredData.renewals.length,
            agents: filteredData.agents.length
        });
        
        // Update UI
        updateKPIs(filteredData);
        renderTables(filteredData);
        
        // Small delay to ensure UI updates
        setTimeout(() => {
            hideLoading();
        }, 100);
        
    } catch (error) {
        console.error('❌ Error loading data:', error);
        console.error('Error details:', error.stack);
        showNotification('Failed to load data: ' + error.message, 'error');
        hideLoading();
    }
}

function populateBusinessTypeFilter(preserveValue = null) {
    const filterSelect = document.getElementById('businessTypeFilter');
    if (!filterSelect) return;
    
    // Get current value to preserve
    const currentValue = preserveValue !== null ? preserveValue : filterSelect.value;
    
    // Keep "All" and "Self" options, remove old agent options
    filterSelect.innerHTML = '<option value="all">All</option><option value="Self">Self</option>';
    
    // Add agent options
    (allAgents || []).forEach(agent => {
        if (agent && agent.name) {
            const option = document.createElement('option');
            option.value = agent.name;
            option.textContent = agent.name;
            filterSelect.appendChild(option);
        }
    });
    
    // Restore previous selection if it still exists
    if (currentValue && Array.from(filterSelect.options).some(opt => opt.value === currentValue)) {
        filterSelect.value = currentValue;
    } else {
        // If the preserved value doesn't exist, default to "all"
        filterSelect.value = 'all';
    }
}

function filterDataByDateRange(startDate, endDate, businessTypeFilter = 'all') {
    const start = new Date(startDate);
    start.setHours(0, 0, 0, 0);
    const end = new Date(endDate);
    end.setHours(23, 59, 59, 999);
    
    console.log('=== FILTER DEBUG ===');
    console.log('Date Range:', start.toLocaleDateString(), 'to', end.toLocaleDateString());
    console.log('Business Type Filter:', businessTypeFilter);
    console.log('Total policies available:', allPolicies.length);
    
    // Debug: Show first 3 policies with their dates
    if (allPolicies.length > 0) {
        console.log('Sample policy dates (first 3):');
        allPolicies.slice(0, 3).forEach(p => {
            console.log('Policy:', p.customerName, {
                created_at: p.created_at,
                startDate: p.startDate,
                endDate: p.endDate,
                businessType: p.businessType || p.business_type,
                agentName: p.agentName || p.agent_name
            });
        });
    }
    
    // Filter policies by created_at date (when policy was added to system) and business type/agent
    const filteredPolicies = allPolicies.filter(policy => {
        // Date filter: Use created_at (when policy was added) as the primary filter
        const createdDate = policy.created_at ? new Date(policy.created_at) : null;
        
        let isInDateRange = false;
        if (!createdDate) {
            // If no created_at, try startDate as fallback
            const startDate = policy.startDate ? new Date(policy.startDate) : null;
            if (!startDate) {
                console.log('❌ Policy has no dates:', policy.customerName);
                return false;
            }
            isInDateRange = startDate >= start && startDate <= end;
        } else {
            isInDateRange = createdDate >= start && createdDate <= end;
        }
        
        if (!isInDateRange) {
            return false;
        }
        
        // Business type/agent filter
        if (businessTypeFilter === 'all') {
            return true;
        }
        
        // Normalize field names - check multiple possible field names
        const policyBusinessType = (policy.businessType || policy.business_type || '').toString().trim();
        const policyAgentName = (policy.agentName || policy.agent_name || '').toString().trim();
        
        if (businessTypeFilter === 'Self') {
            return policyBusinessType === 'Self';
        } else {
            // Filter by agent name
            return policyAgentName === businessTypeFilter;
        }
    });
    
    console.log('=== FILTERED RESULT ===');
    console.log('Showing', filteredPolicies.length, 'policies out of', allPolicies.length);
    
    // Filter policies that are expiring (pending renewals) in the date range and business type/agent
    const filteredRenewals = allPolicies.filter(policy => {
        if (!policy.endDate) return false;
        const endDate = new Date(policy.endDate);
        const isInDateRange = endDate >= start && endDate <= end;
        
        if (!isInDateRange) {
            return false;
        }
        
        // Apply business type/agent filter
        if (businessTypeFilter === 'all') {
            return true;
        }
        
        const policyBusinessType = policy.businessType || policy.business_type || '';
        const policyAgentName = policy.agentName || policy.agent_name || '';
        
        if (businessTypeFilter === 'Self') {
            return policyBusinessType === 'Self';
        } else {
            return policyAgentName === businessTypeFilter;
        }
    });
    
    console.log('=== RENEWALS FILTER DEBUG ===');
    console.log('Total policies:', allPolicies.length);
    console.log('Policies with endDate:', allPolicies.filter(p => p.endDate).length);
    console.log('Filtered renewals:', filteredRenewals.length);
    if (filteredRenewals.length > 0) {
        console.log('Sample renewal policy:', filteredRenewals[0]);
    }
    
    // Filter agents who have policies in the date range and add "Self" business
    // Only show agents/self that match the filter (or all if filter is "all")
    const agentMap = new Map();
    
    if (businessTypeFilter === 'all') {
        // Show all agents and Self
        allAgents.forEach(agent => {
            const agentPolicies = filteredPolicies.filter(p => 
                p.agentName === agent.name || p.agent_name === agent.name
            );
            agentMap.set(agent.name, {
                name: agent.name,
                phone: agent.phone || agent.contact || '',
                policies: agentPolicies.length,
                totalPremium: agentPolicies.reduce((sum, p) => sum + (parseFloat(p.premium) || 0), 0)
            });
        });
        
        // Add "Self" business
        const selfPolicies = filteredPolicies.filter(p => 
            p.businessType === 'Self' || p.business_type === 'Self'
        );
        if (selfPolicies.length > 0) {
            agentMap.set('Self', {
                name: 'Self',
                phone: '-',
                policies: selfPolicies.length,
                totalPremium: selfPolicies.reduce((sum, p) => sum + (parseFloat(p.premium) || 0), 0)
            });
        }
    } else if (businessTypeFilter === 'Self') {
        // Only show Self
        const selfPolicies = filteredPolicies.filter(p => 
            p.businessType === 'Self' || p.business_type === 'Self'
        );
        if (selfPolicies.length > 0) {
            agentMap.set('Self', {
                name: 'Self',
                phone: '-',
                policies: selfPolicies.length,
                totalPremium: selfPolicies.reduce((sum, p) => sum + (parseFloat(p.premium) || 0), 0)
            });
        }
    } else {
        // Show only the selected agent
        const agentPolicies = filteredPolicies.filter(p => 
            (p.agentName === businessTypeFilter || p.agent_name === businessTypeFilter)
        );
        const agent = allAgents.find(a => a.name === businessTypeFilter);
        if (agentPolicies.length > 0 && agent) {
            agentMap.set(agent.name, {
                name: agent.name,
                phone: agent.phone || agent.contact || '',
                policies: agentPolicies.length,
                totalPremium: agentPolicies.reduce((sum, p) => sum + (parseFloat(p.premium) || 0), 0)
            });
        }
    }
    
    // Convert to array and calculate performance
    const filteredAgents = Array.from(agentMap.values()).map(agent => {
        // Calculate performance as: (agent's policies / total policies) * 100
        const performance = filteredPolicies.length > 0 
            ? ((agent.policies / filteredPolicies.length) * 100).toFixed(2) + '%'
            : '0.00%';
        
        return {
            ...agent,
            performance: performance
        };
    });
    
    return {
        policies: filteredPolicies,
        renewals: filteredRenewals,
        agents: filteredAgents
    };
}

function updateKPIs(data) {
    console.log('📊 Updating KPIs with data:', data);
    
    // Calculate totals
    const totalPremium = data.policies.reduce((sum, policy) => sum + (parseFloat(policy.premium) || 0), 0);
    const totalRevenue = data.policies.reduce((sum, policy) => sum + (parseFloat(policy.revenue) || 0), 0);
    const activePolicies = data.policies.length;
    
    // Update KPI values
    document.getElementById('totalPremiumKPI').textContent = `₹${totalPremium.toLocaleString()}`;
    document.getElementById('totalRevenueKPI').textContent = `₹${totalRevenue.toLocaleString()}`;
    document.getElementById('activePoliciesKPI').textContent = activePolicies.toString();
    
    // Calculate percentage changes (mock data for now)
    const premiumChange = Math.random() * 20 - 10; // -10% to +10%
    const revenueChange = Math.random() * 20 - 10;
    const policiesChange = Math.random() * 20 - 10;
    
    updateKPIChange('premiumChange', premiumChange);
    updateKPIChange('revenueChange', revenueChange);
    updateKPIChange('policiesChange', policiesChange);
}

function updateKPIChange(elementId, change) {
    const element = document.getElementById(elementId);
    const isPositive = change >= 0;
    element.textContent = `${isPositive ? '+' : ''}${change.toFixed(1)}%`;
    element.className = `kpi-change ${isPositive ? 'positive' : 'negative'}`;
}

function renderCharts() {
    console.log('📊 Charts disabled for reports page');
    // Charts removed as per user request
}

function renderTables(data) {
    console.log('📊 Rendering tables with data:', data);
    
    // Store current data for export
    currentReportData = data;
    
    // Reset pagination for all tables
    paginationState.policies.currentPage = 1;
    paginationState.renewals.currentPage = 1;
    paginationState.agents.currentPage = 1;
    
    // Render each table with pagination
    renderTableWithPagination('policies');
    renderTableWithPagination('renewals');
    renderTableWithPagination('agents');
}

function renderTableWithPagination(tableType) {
    const data = getFilteredData(tableType);
    const { currentPage, rowsPerPage } = paginationState[tableType];
    
    // Calculate pagination
    const totalItems = data.length;
    const totalPages = Math.ceil(totalItems / rowsPerPage);
    const startIndex = (currentPage - 1) * rowsPerPage;
    const endIndex = startIndex + rowsPerPage;
    const pageData = data.slice(startIndex, endIndex);
    
    // Render based on table type
    switch(tableType) {
        case 'policies':
            renderPoliciesTable(pageData);
            break;
        case 'renewals':
            renderRenewalsTable(pageData);
            break;
        case 'agents':
            renderAgentsTable(pageData);
            break;
    }
    
    // Update pagination UI
    updatePaginationUI(tableType, totalItems, totalPages, startIndex + 1, Math.min(endIndex, totalItems));
}

function updatePaginationUI(tableType, totalItems, totalPages, start, end) {
    // Update pagination info with new IDs matching Policies/Renewals pages
    const startRecord = document.getElementById(tableType + 'StartRecord');
    const endRecord = document.getElementById(tableType + 'EndRecord');
    const totalRecords = document.getElementById(tableType + 'TotalRecords');
    
    if (startRecord) startRecord.textContent = start;
    if (endRecord) endRecord.textContent = end;
    if (totalRecords) totalRecords.textContent = totalItems;
    
    // Update prev/next buttons
    const prevBtn = document.getElementById(tableType + 'PrevPage');
    const nextBtn = document.getElementById(tableType + 'NextPage');
    
    if (prevBtn) {
        prevBtn.disabled = paginationState[tableType].currentPage === 1;
    }
    if (nextBtn) {
        nextBtn.disabled = paginationState[tableType].currentPage >= totalPages;
    }
    
    // Update page numbers - generate clickable page numbers like Policies page
    const pageNumbersElement = document.getElementById(tableType + 'PageNumbers');
    if (pageNumbersElement) {
        const currentPage = paginationState[tableType].currentPage;
        let pageNumbersHtml = '';
        
        if (totalPages === 0) {
            pageNumbersHtml = '<span class="page-number" style="padding: 4px 8px; margin: 0 2px;">0</span>';
        } else if (totalPages <= 7) {
            // Show all pages if 7 or fewer
            for (let i = 1; i <= totalPages; i++) {
                const activeClass = i === currentPage ? 'active' : '';
                const style = i === currentPage 
                    ? 'background: #4f46e5; color: white; cursor: pointer; padding: 4px 8px; margin: 0 2px; border-radius: 4px; display: inline-block;'
                    : 'cursor: pointer; padding: 4px 8px; margin: 0 2px; border-radius: 4px; display: inline-block;';
                pageNumbersHtml += `<span class="page-number ${activeClass}" onclick="setPage('${tableType}', ${i})" style="${style}">${i}</span>`;
            }
        } else {
            // Show max 5 page numbers at a time with ellipsis
            const maxVisible = 5;
            let startPage = Math.max(1, currentPage - Math.floor(maxVisible / 2));
            let endPage = Math.min(totalPages, startPage + maxVisible - 1);
            
            if (endPage - startPage < maxVisible - 1) {
                startPage = Math.max(1, endPage - maxVisible + 1);
            }
            
            // Previous ellipsis
            if (startPage > 1) {
                pageNumbersHtml += `<span class="page-number" onclick="setPage('${tableType}', 1)" style="cursor: pointer; padding: 4px 8px; margin: 0 2px; border-radius: 4px; display: inline-block;">1</span>`;
                if (startPage > 2) {
                    pageNumbersHtml += `<span class="page-ellipsis" style="padding: 4px 8px; margin: 0 2px; display: inline-block;">...</span>`;
                }
            }
            
            // Page numbers
            for (let i = startPage; i <= endPage; i++) {
                const activeClass = i === currentPage ? 'active' : '';
                const style = i === currentPage 
                    ? 'background: #4f46e5; color: white; cursor: pointer; padding: 4px 8px; margin: 0 2px; border-radius: 4px; display: inline-block;'
                    : 'cursor: pointer; padding: 4px 8px; margin: 0 2px; border-radius: 4px; display: inline-block;';
                pageNumbersHtml += `<span class="page-number ${activeClass}" onclick="setPage('${tableType}', ${i})" style="${style}">${i}</span>`;
            }
            
            // Next ellipsis
            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    pageNumbersHtml += `<span class="page-ellipsis" style="padding: 4px 8px; margin: 0 2px; display: inline-block;">...</span>`;
                }
                pageNumbersHtml += `<span class="page-number" onclick="setPage('${tableType}', ${totalPages})" style="cursor: pointer; padding: 4px 8px; margin: 0 2px; border-radius: 4px; display: inline-block;">${totalPages}</span>`;
            }
        }
        
        pageNumbersElement.innerHTML = pageNumbersHtml;
    }
}

// Make setPage accessible globally for onclick handlers
window.setPage = function(tableType, page) {
    console.log(`📄 ${tableType} set page to:`, page);
    paginationState[tableType].currentPage = parseInt(page);
    renderTableWithPagination(tableType);
};

function renderPoliciesTable(policies) {
    const tbody = document.getElementById('policiesTableBody');
    if (!tbody) return;
    
    tbody.innerHTML = '';
    
    // Get current pagination state to calculate starting serial number
    const { currentPage, rowsPerPage } = paginationState.policies;
    const startSerialNumber = (currentPage - 1) * rowsPerPage + 1;
    
    policies.forEach((policy, index) => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${startSerialNumber + index}</td>
            <td>${escapeHtml(policy.customerName || '')}</td>
            <td>${escapeHtml(policy.policyType || '')}</td>
            <td>${escapeHtml(policy.vehicleNumber || '')}</td>
            <td>${escapeHtml(policy.companyName || '')}</td>
            <td>₹${parseFloat(policy.premium || 0).toLocaleString()}</td>
            <td>${escapeHtml(policy.status || '')}</td>
            <td>${formatDate(policy.startDate)}</td>
        `;
        tbody.appendChild(row);
    });
}

function renderRenewalsTable(policies) {
    const tbody = document.getElementById('renewalsTableBody');
    if (!tbody) return;
    
    tbody.innerHTML = '';
    
    // Get current pagination state to calculate starting serial number
    const { currentPage, rowsPerPage } = paginationState.renewals;
    const startSerialNumber = (currentPage - 1) * rowsPerPage + 1;
    
    policies.forEach((policy, index) => {
        const endDate = policy.endDate ? new Date(policy.endDate) : null;
        const daysLeft = endDate ? Math.ceil((endDate - new Date()) / (1000 * 60 * 60 * 24)) : '';
        
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${startSerialNumber + index}</td>
            <td>${escapeHtml(policy.customerName || '')}</td>
            <td>${escapeHtml(policy.phone || '')}</td>
            <td>${escapeHtml(policy.policyType || '')}</td>
            <td>${escapeHtml(policy.companyName || '')}</td>
            <td>${formatDate(policy.endDate)}</td>
            <td>${daysLeft}</td>
            <td>₹${formatCurrency(policy.premium || 0)}</td>
            <td>${escapeHtml(policy.status || '')}</td>
        `;
        tbody.appendChild(row);
    });
}

function renderAgentsTable(agents) {
    const tbody = document.getElementById('agentsTableBody');
    if (!tbody) return;
    
    tbody.innerHTML = '';
    
    // Get current pagination state to calculate starting serial number
    const { currentPage, rowsPerPage } = paginationState.agents;
    const startSerialNumber = (currentPage - 1) * rowsPerPage + 1;
    
    console.log('Rendering agents:', agents);
    
    agents.forEach((agent, index) => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${startSerialNumber + index}</td>
            <td><strong>${escapeHtml(agent.name || '')}</strong></td>
            <td>${escapeHtml(agent.phone || '-')}</td>
            <td>${agent.policies || 0}</td>
            <td>₹${parseFloat(agent.totalPremium || 0).toLocaleString()}</td>
            <td><span class="status-badge status-active">${agent.performance || '0.00%'}</span></td>
        `;
        tbody.appendChild(row);
    });
}

function switchTab(tabName) {
    // Update tab buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    document.querySelector(`[data-tab="${tabName}"]`).classList.add('active');
    
    // Show/hide tables - use the correct selector
    document.querySelectorAll('.data-table-container').forEach(table => {
        table.style.display = 'none';
    });
    document.getElementById(tabName + 'Table').style.display = 'block';
}

function exportReport() {
    console.log('🔄 Export Report clicked');
    
    try {
        const activeTabBtn = document.querySelector('.tab-btn.active');
        const tab = activeTabBtn ? activeTabBtn.getAttribute('data-tab') : 'policies';
        
        console.log('📊 Exporting tab:', tab);
        console.log('📊 Current data:', currentReportData);
        
        let csv = '';
        let filename = '';
        
        switch(tab) {
            case 'policies':
                csv = generatePoliciesCSV(currentReportData.policies || []);
                filename = 'policies_report';
                break;
            case 'renewals':
                csv = generateRenewalsCSV(currentReportData.renewals || []);
                filename = 'renewals_report';
                break;
            case 'agents':
                csv = generateAgentsCSV(currentReportData.agents || []);
                filename = 'agents_report';
                break;
            default:
                throw new Error('Unknown tab type');
        }
        
        if (!csv) {
            showNotification('No data to export', 'error');
            return;
        }
        
        // Download CSV
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = `${filename}_${new Date().toISOString().split('T')[0]}.csv`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        showNotification('Report exported successfully!', 'success');
        
    } catch (error) {
        console.error('❌ Export error:', error);
        showNotification('Export failed: ' + error.message, 'error');
    }
}

function generatePoliciesCSV(policies) {
    const headers = ['Sl. No', 'Customer Name', 'Phone', 'Policy Type', 'Vehicle Number', 'Insurance Company', 'Premium', 'Revenue', 'Status', 'Business Type', 'Agent Name', 'Start Date', 'End Date'];
    const rows = policies.map((policy, index) => [
        index + 1,
        policy.customerName || '',
        policy.phone || '',
        policy.policyType || '',
        policy.vehicleNumber || '',
        policy.companyName || '',
        parseFloat(policy.premium || 0),
        parseFloat(policy.revenue || 0),
        policy.status || '',
        policy.businessType || policy.business_type || '',
        policy.agentName || policy.agent_name || '',
        formatDate(policy.startDate),
        formatDate(policy.endDate)
    ]);
    
    return generateCSV(headers, rows);
}

function generateRenewalsCSV(renewals) {
    const headers = ['Sl. No', 'Customer Name', 'Due Date', 'Days Left', 'Status', 'Priority', 'Assigned To'];
    const rows = renewals.map((renewal, index) => {
        const dueDate = renewal.dueDate ? new Date(renewal.dueDate) : null;
        const daysLeft = dueDate ? Math.ceil((dueDate - new Date()) / (1000 * 60 * 60 * 24)) : '';
        const priority = daysLeft === '' ? '' : (daysLeft <= 7 ? 'High' : daysLeft <= 14 ? 'Medium' : 'Low');
        
        return [
            index + 1,
            renewal.customerName || '',
            formatDate(renewal.dueDate),
            daysLeft,
            renewal.status || '',
            priority,
            renewal.agentName || ''
        ];
    });
    
    return generateCSV(headers, rows);
}

function generateAgentsCSV(agents) {
    const headers = ['Sl. No', 'Business Type', 'Phone', 'Policies Sold', 'Total Premium', 'Performance'];
    const rows = agents.map((agent, index) => [
        index + 1,
        agent.name || '',
        agent.phone || agent.contact || '',
        agent.policies || 0,
        parseFloat(agent.totalPremium || 0),
        agent.performance || '0.00%'
    ]);
    
    return generateCSV(headers, rows);
}

function generateCSV(headers, rows) {
    const escapeValue = (value) => {
        const str = String(value || '');
        return `"${str.replace(/"/g, '""')}"`;
    };
    
    const csvRows = [
        headers.map(escapeValue).join(','),
        ...rows.map(row => row.map(escapeValue).join(','))
    ];
    
    return csvRows.join('\n');
}

function showLoading() {
    let overlay = document.getElementById('loadingOverlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.id = 'loadingOverlay';
        overlay.className = 'loading-overlay';
        overlay.innerHTML = `
            <div class="loading-content">
                <div class="loading-spinner"></div>
                <div>Loading reports...</div>
            </div>
        `;
        document.body.appendChild(overlay);
    }
    overlay.style.display = 'flex';
}

function hideLoading() {
    const overlay = document.getElementById('loadingOverlay');
    if (overlay) {
        overlay.style.display = 'none';
        overlay.style.visibility = 'hidden';
        overlay.style.opacity = '0';
    }
    // Also try class-based overlay
    const classOverlay = document.querySelector('.loading-overlay');
    if (classOverlay) {
        classOverlay.style.display = 'none';
        classOverlay.style.visibility = 'hidden';
        classOverlay.style.opacity = '0';
    }
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatDate(dateString) {
    if (!dateString) return '';
    try {
        return new Date(dateString).toLocaleDateString();
    } catch (error) {
        return dateString;
    }
}

function formatCurrency(amount) {
    if (!amount) return '0';
    return parseFloat(amount).toLocaleString('en-IN', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    });
}
})();
</script>

@endsection
