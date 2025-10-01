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
                <div class="filter-group">
                    <label for="reportTypeFilter">Report Type:</label>
                    <select id="reportTypeFilter">
                        <option value="all">All Reports</option>
                        <option value="policies">Policies</option>
                        <option value="renewals">Renewals</option>
                        <option value="followups">Follow-ups</option>
                        <option value="agents">Agents</option>
                        <option value="revenue">Revenue</option>
                    </select>
                </div>
                <button class="generate-report-btn" id="generateReportBtn">
                    <i class="fas fa-sync-alt"></i>
                    Refresh
                </button>
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

        <!-- Charts Section -->
        <div class="charts-section">
            <div class="chart-row">
                <div class="chart-container glass-effect">
                    <div class="chart-header">
                        <h3>Premium vs Revenue Trend</h3>
                        <div class="chart-controls">
                            <select id="trendPeriod">
                                <option value="7">Last 7 Days</option>
                                <option value="30" selected>Last 30 Days</option>
                                <option value="90">Last 90 Days</option>
                                <option value="365">Last Year</option>
                            </select>
                        </div>
                    </div>
                    <div class="chart-body">
                        <canvas id="premiumRevenueChart"></canvas>
                    </div>
                </div>
                <div class="chart-container glass-effect">
                    <div class="chart-header">
                        <h3>Agent Performance</h3>
                        <div class="chart-controls">
                            <select id="agentPeriod">
                                <option value="7">Last 7 Days</option>
                                <option value="30" selected>Last 30 Days</option>
                                <option value="90">Last 90 Days</option>
                                <option value="365">Last Year</option>
                            </select>
                        </div>
                    </div>
                    <div class="chart-body">
                        <canvas id="agentPerformanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reports Tabs -->
        <div class="reports-tabs">
            <button class="tab-btn active" data-tab="policies">Policies</button>
            <button class="tab-btn" data-tab="renewals">Renewals</button>
            <button class="tab-btn" data-tab="followups">Follow-ups</button>
            <button class="tab-btn" data-tab="agents">Agents</button>
        </div>

        <!-- Reports Tables -->
        <div class="reports-tables">
            <!-- Policies Table -->
            <div class="report-table" id="policiesTable">
                <div class="table-header">
                    <h3>Policies Report</h3>
                    <div class="table-controls">
                        <input type="text" id="policiesSearch" placeholder="Search policies...">
                        <select id="policiesRowsPerPage">
                            <option value="10">10 rows</option>
                            <option value="25" selected>25 rows</option>
                            <option value="50">50 rows</option>
                            <option value="100">100 rows</option>
                        </select>
                    </div>
                </div>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th data-sort="id">Policy ID</th>
                                <th data-sort="customerName">Customer Name</th>
                                <th data-sort="policyType">Policy Type</th>
                                <th data-sort="companyName">Company</th>
                                <th data-sort="premium">Premium</th>
                                <th data-sort="status">Status</th>
                                <th data-sort="startDate">Start Date</th>
                                <th data-sort="endDate">End Date</th>
                            </tr>
                        </thead>
                        <tbody id="policiesTableBody">
                            <!-- Data will be populated here -->
                        </tbody>
                    </table>
                </div>
                <div class="table-footer">
                    <div class="pagination-info">
                        <span id="policiesPaginationInfo">Showing 0 of 0 policies</span>
                    </div>
                    <div class="pagination-controls">
                        <button id="policiesPrevPage" disabled>Previous</button>
                        <span id="policiesPageNumbers"></span>
                        <button id="policiesNextPage" disabled>Next</button>
                    </div>
                </div>
            </div>

            <!-- Renewals Table -->
            <div class="report-table" id="renewalsTable" style="display: none;">
                <div class="table-header">
                    <h3>Renewals Report</h3>
                    <div class="table-controls">
                        <input type="text" id="renewalsSearch" placeholder="Search renewals...">
                        <select id="renewalsRowsPerPage">
                            <option value="10">10 rows</option>
                            <option value="25" selected>25 rows</option>
                            <option value="50">50 rows</option>
                            <option value="100">100 rows</option>
                        </select>
                    </div>
                </div>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th data-sort="id">Policy ID</th>
                                <th data-sort="customerName">Customer Name</th>
                                <th data-sort="dueDate">Due Date</th>
                                <th data-sort="daysLeft">Days Left</th>
                                <th data-sort="status">Status</th>
                                <th data-sort="priority">Priority</th>
                                <th data-sort="agentName">Assigned To</th>
                            </tr>
                        </thead>
                        <tbody id="renewalsTableBody">
                            <!-- Data will be populated here -->
                        </tbody>
                    </table>
                </div>
                <div class="table-footer">
                    <div class="pagination-info">
                        <span id="renewalsPaginationInfo">Showing 0 of 0 renewals</span>
                    </div>
                    <div class="pagination-controls">
                        <button id="renewalsPrevPage" disabled>Previous</button>
                        <span id="renewalsPageNumbers"></span>
                        <button id="renewalsNextPage" disabled>Next</button>
                    </div>
                </div>
            </div>

            <!-- Follow-ups Table -->
            <div class="report-table" id="followupsTable" style="display: none;">
                <div class="table-header">
                    <h3>Follow-ups Report</h3>
                    <div class="table-controls">
                        <input type="text" id="followupsSearch" placeholder="Search follow-ups...">
                        <select id="followupsRowsPerPage">
                            <option value="10">10 rows</option>
                            <option value="25" selected>25 rows</option>
                            <option value="50">50 rows</option>
                            <option value="100">100 rows</option>
                        </select>
                    </div>
                </div>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th data-sort="customerName">Customer Name</th>
                                <th data-sort="phone">Phone</th>
                                <th data-sort="followupType">Type</th>
                                <th data-sort="status">Status</th>
                                <th data-sort="assignedTo">Assigned To</th>
                                <th data-sort="lastFollowupDate">Last Follow-up</th>
                                <th data-sort="nextFollowupDate">Next Follow-up</th>
                            </tr>
                        </thead>
                        <tbody id="followupsTableBody">
                            <!-- Data will be populated here -->
                        </tbody>
                    </table>
                </div>
                <div class="table-footer">
                    <div class="pagination-info">
                        <span id="followupsPaginationInfo">Showing 0 of 0 follow-ups</span>
                    </div>
                    <div class="pagination-controls">
                        <button id="followupsPrevPage" disabled>Previous</button>
                        <span id="followupsPageNumbers"></span>
                        <button id="followupsNextPage" disabled>Next</button>
                    </div>
                </div>
            </div>

            <!-- Agents Table -->
            <div class="report-table" id="agentsTable" style="display: none;">
                <div class="table-header">
                    <h3>Agents Report</h3>
                    <div class="table-controls">
                        <input type="text" id="agentsSearch" placeholder="Search agents...">
                        <select id="agentsRowsPerPage">
                            <option value="10">10 rows</option>
                            <option value="25" selected>25 rows</option>
                            <option value="50">50 rows</option>
                            <option value="100">100 rows</option>
                        </select>
                    </div>
                </div>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th data-sort="name">Agent Name</th>
                                <th data-sort="policies">Policies Sold</th>
                                <th data-sort="totalPremium">Total Premium</th>
                                <th data-sort="renewalsHandled">Renewals Handled</th>
                                <th data-sort="followups">Follow-ups</th>
                                <th data-sort="performance">Performance</th>
                            </tr>
                        </thead>
                        <tbody id="agentsTableBody">
                            <!-- Data will be populated here -->
                        </tbody>
                    </table>
                </div>
                <div class="table-footer">
                    <div class="pagination-info">
                        <span id="agentsPaginationInfo">Showing 0 of 0 agents</span>
                    </div>
                    <div class="pagination-controls">
                        <button id="agentsPrevPage" disabled>Previous</button>
                        <span id="agentsPageNumbers"></span>
                        <button id="agentsNextPage" disabled>Next</button>
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

.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.dark-theme .loading-overlay {
    background: rgba(15, 23, 42, 0.9);
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
    
    .chart-row {
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
// Global variables
let allPolicies = [];
let allRenewals = [];
let allFollowups = [];
let allAgents = [];
let currentReportData = {};

// Chart instances
let premiumRevenueChart = null;
let agentPerformanceChart = null;

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
    // Generate Report button
    document.getElementById('generateReportBtn').addEventListener('click', generateReport);
    
    // Export Report button
    document.getElementById('exportReportBtn').addEventListener('click', exportReport);
    
    // Tab switching
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            switchTab(this.getAttribute('data-tab'));
        });
    });
    
    // Date range changes
    document.getElementById('reportStartDate').addEventListener('change', loadAllData);
    document.getElementById('reportEndDate').addEventListener('change', loadAllData);
    
    // Report type filter
    document.getElementById('reportTypeFilter').addEventListener('change', function() {
        const filter = this.value;
        if (filter !== 'all') {
            document.querySelectorAll('.report-table').forEach(table => {
                table.style.display = 'none';
            });
            document.getElementById(filter + 'Table').style.display = 'block';
        } else {
            document.querySelectorAll('.report-table').forEach(table => {
                table.style.display = 'block';
            });
        }
    });
    
    // Chart period changes
    document.getElementById('trendPeriod').addEventListener('change', renderCharts);
    document.getElementById('agentPeriod').addEventListener('change', renderCharts);
}

async function loadAllData() {
    console.log('📊 Loading all reports data...');
    showLoading();
    
    try {
        const startDate = document.getElementById('reportStartDate').value;
        const endDate = document.getElementById('reportEndDate').value;
        
        console.log('📅 Date range:', startDate, 'to', endDate);
        
        // Fetch all data in parallel
        const [policiesRes, renewalsRes, followupsRes, agentsRes] = await Promise.all([
            fetch('/api/policies').then(r => r.json()),
            fetch('/api/renewals').then(r => r.json()),
            fetch('/api/followups').then(r => r.json()),
            fetch('/api/agents').then(r => r.json())
        ]);
        
        allPolicies = policiesRes.policies || [];
        allRenewals = renewalsRes.renewals || [];
        allFollowups = followupsRes.followups || [];
        allAgents = agentsRes.agents || [];
        
        console.log('📊 Data loaded:', {
            policies: allPolicies.length,
            renewals: allRenewals.length,
            followups: allFollowups.length,
            agents: allAgents.length
        });
        
        // Filter data by date range
        const filteredData = filterDataByDateRange(startDate, endDate);
        
        // Update UI
        updateKPIs(filteredData);
        renderCharts();
        renderTables(filteredData);
        
        hideLoading();
        
    } catch (error) {
        console.error('❌ Error loading data:', error);
        showNotification('Failed to load data', 'error');
        hideLoading();
    }
}

function filterDataByDateRange(startDate, endDate) {
    const start = new Date(startDate);
    const end = new Date(endDate);
    
    const filteredPolicies = allPolicies.filter(policy => {
        const policyDate = new Date(policy.startDate || policy.created_at);
        return policyDate >= start && policyDate <= end;
    });
    
    const filteredRenewals = allRenewals.filter(renewal => {
        const renewalDate = new Date(renewal.dueDate || renewal.created_at);
        return renewalDate >= start && renewalDate <= end;
    });
    
    const filteredFollowups = allFollowups.filter(followup => {
        const followupDate = new Date(followup.nextFollowupDate || followup.created_at);
        return followupDate >= start && followupDate <= end;
    });
    
    // Agents don't have date filtering
    const filteredAgents = allAgents;
    
    return {
        policies: filteredPolicies,
        renewals: filteredRenewals,
        followups: filteredFollowups,
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
    console.log('📊 Rendering charts...');
    
    // Premium vs Revenue Chart
    renderPremiumRevenueChart();
    
    // Agent Performance Chart
    renderAgentPerformanceChart();
}

function renderPremiumRevenueChart() {
    const ctx = document.getElementById('premiumRevenueChart');
    if (!ctx) return;
    
    // Destroy existing chart
    if (premiumRevenueChart) {
        premiumRevenueChart.destroy();
    }
    
    // Generate mock data for the last 30 days
    const days = 30;
    const labels = [];
    const premiumData = [];
    const revenueData = [];
    
    for (let i = days - 1; i >= 0; i--) {
        const date = new Date();
        date.setDate(date.getDate() - i);
        labels.push(date.toLocaleDateString());
        
        // Mock data - in real implementation, this would come from actual data
        premiumData.push(Math.floor(Math.random() * 50000) + 20000);
        revenueData.push(Math.floor(Math.random() * 10000) + 5000);
    }
    
    premiumRevenueChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Premium (₹)',
                data: premiumData,
                borderColor: '#8b5cf6',
                backgroundColor: 'rgba(139, 92, 246, 0.1)',
                tension: 0.4
            }, {
                label: 'Revenue (₹)',
                data: revenueData,
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₹' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
}

function renderAgentPerformanceChart() {
    const ctx = document.getElementById('agentPerformanceChart');
    if (!ctx) return;
    
    // Destroy existing chart
    if (agentPerformanceChart) {
        agentPerformanceChart.destroy();
    }
    
    // Use actual agent data
    const agentNames = allAgents.slice(0, 5).map(agent => agent.name || 'Unknown');
    const agentPolicies = allAgents.slice(0, 5).map(agent => agent.policies || 0);
    
    agentPerformanceChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: agentNames,
            datasets: [{
                label: 'Policies Sold',
                data: agentPolicies,
                backgroundColor: '#3b82f6',
                borderColor: '#2563eb',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

function renderTables(data) {
    console.log('📊 Rendering tables with data:', data);
    
    // Store current data for export
    currentReportData = data;
    
    // Render each table
    renderPoliciesTable(data.policies);
    renderRenewalsTable(data.renewals);
    renderFollowupsTable(data.followups);
    renderAgentsTable(data.agents);
}

function renderPoliciesTable(policies) {
    const tbody = document.getElementById('policiesTableBody');
    if (!tbody) return;
    
    tbody.innerHTML = '';
    
    policies.forEach(policy => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>#${String(policy.id || 0).padStart(3, '0')}</td>
            <td>${escapeHtml(policy.customerName || '')}</td>
            <td>${escapeHtml(policy.policyType || '')}</td>
            <td>${escapeHtml(policy.companyName || '')}</td>
            <td>₹${parseFloat(policy.premium || 0).toLocaleString()}</td>
            <td>${escapeHtml(policy.status || '')}</td>
            <td>${formatDate(policy.startDate)}</td>
            <td>${formatDate(policy.endDate)}</td>
        `;
        tbody.appendChild(row);
    });
    
    updatePaginationInfo('policies', policies.length);
}

function renderRenewalsTable(renewals) {
    const tbody = document.getElementById('renewalsTableBody');
    if (!tbody) return;
    
    tbody.innerHTML = '';
    
    renewals.forEach(renewal => {
        const dueDate = renewal.dueDate ? new Date(renewal.dueDate) : null;
        const daysLeft = dueDate ? Math.ceil((dueDate - new Date()) / (1000 * 60 * 60 * 24)) : '';
        const priority = daysLeft === '' ? '' : (daysLeft <= 7 ? 'High' : daysLeft <= 14 ? 'Medium' : 'Low');
        
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>#${String(renewal.id || 0).padStart(3, '0')}</td>
            <td>${escapeHtml(renewal.customerName || '')}</td>
            <td>${formatDate(renewal.dueDate)}</td>
            <td>${daysLeft}</td>
            <td>${escapeHtml(renewal.status || '')}</td>
            <td>${priority}</td>
            <td>${escapeHtml(renewal.agentName || '')}</td>
        `;
        tbody.appendChild(row);
    });
    
    updatePaginationInfo('renewals', renewals.length);
}

function renderFollowupsTable(followups) {
    const tbody = document.getElementById('followupsTableBody');
    if (!tbody) return;
    
    tbody.innerHTML = '';
    
    followups.forEach(followup => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${escapeHtml(followup.customerName || '')}</td>
            <td>${escapeHtml(followup.phone || '')}</td>
            <td>${escapeHtml(followup.followupType || '')}</td>
            <td>${escapeHtml(followup.status || '')}</td>
            <td>${escapeHtml(followup.assignedTo || '')}</td>
            <td>${formatDate(followup.lastFollowupDate)}</td>
            <td>${formatDate(followup.nextFollowupDate)}</td>
        `;
        tbody.appendChild(row);
    });
    
    updatePaginationInfo('followups', followups.length);
}

function renderAgentsTable(agents) {
    const tbody = document.getElementById('agentsTableBody');
    if (!tbody) return;
    
    tbody.innerHTML = '';
    
    agents.forEach(agent => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${escapeHtml(agent.name || '')}</td>
            <td>${agent.policies || 0}</td>
            <td>₹${parseFloat(agent.totalPremium || 0).toLocaleString()}</td>
            <td>${agent.renewalsHandled || 0}</td>
            <td>${agent.followups || 0}</td>
            <td>${agent.performance || '0%'}</td>
        `;
        tbody.appendChild(row);
    });
    
    updatePaginationInfo('agents', agents.length);
}

function updatePaginationInfo(tableType, totalCount) {
    const infoElement = document.getElementById(tableType + 'PaginationInfo');
    if (infoElement) {
        infoElement.textContent = `Showing ${totalCount} ${tableType}`;
    }
}

function switchTab(tabName) {
    // Update tab buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    document.querySelector(`[data-tab="${tabName}"]`).classList.add('active');
    
    // Show/hide tables
    document.querySelectorAll('.report-table').forEach(table => {
        table.style.display = 'none';
    });
    document.getElementById(tabName + 'Table').style.display = 'block';
}

function generateReport() {
    console.log('🔄 Generate Report clicked');
    showNotification('Generating report...', 'info');
    loadAllData();
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
            case 'followups':
                csv = generateFollowupsCSV(currentReportData.followups || []);
                filename = 'followups_report';
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
    const headers = ['Policy ID', 'Customer Name', 'Policy Type', 'Company', 'Premium', 'Revenue', 'Status', 'Start Date', 'End Date'];
    const rows = policies.map(policy => [
        `#${String(policy.id || 0).padStart(3, '0')}`,
        policy.customerName || '',
        policy.policyType || '',
        policy.companyName || '',
        parseFloat(policy.premium || 0),
        parseFloat(policy.revenue || 0),
        policy.status || '',
        formatDate(policy.startDate),
        formatDate(policy.endDate)
    ]);
    
    return generateCSV(headers, rows);
}

function generateRenewalsCSV(renewals) {
    const headers = ['Policy ID', 'Customer Name', 'Due Date', 'Days Left', 'Status', 'Priority', 'Assigned To'];
    const rows = renewals.map(renewal => {
        const dueDate = renewal.dueDate ? new Date(renewal.dueDate) : null;
        const daysLeft = dueDate ? Math.ceil((dueDate - new Date()) / (1000 * 60 * 60 * 24)) : '';
        const priority = daysLeft === '' ? '' : (daysLeft <= 7 ? 'High' : daysLeft <= 14 ? 'Medium' : 'Low');
        
        return [
            `#${String(renewal.id || 0).padStart(3, '0')}`,
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

function generateFollowupsCSV(followups) {
    const headers = ['Customer Name', 'Phone', 'Type', 'Status', 'Assigned To', 'Last Follow-up', 'Next Follow-up'];
    const rows = followups.map(followup => [
        followup.customerName || '',
        followup.phone || '',
        followup.followupType || '',
        followup.status || '',
        followup.assignedTo || '',
        formatDate(followup.lastFollowupDate),
        formatDate(followup.nextFollowupDate)
    ]);
    
    return generateCSV(headers, rows);
}

function generateAgentsCSV(agents) {
    const headers = ['Agent Name', 'Policies Sold', 'Total Premium', 'Renewals Handled', 'Follow-ups', 'Performance'];
    const rows = agents.map(agent => [
        agent.name || '',
        agent.policies || 0,
        parseFloat(agent.totalPremium || 0),
        agent.renewalsHandled || 0,
        agent.followups || 0,
        agent.performance || '0%'
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
</script>

@endsection
