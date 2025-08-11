@extends('layouts.insurance')

@section('title', 'Reports & Analytics - Insurance Management System')

@section('content')
<div class="page active" id="reports">
    <div class="page-header">
        <h1>Reports & Analytics</h1>
        <p>Comprehensive insights and performance analytics</p>
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
                    <i class="fas fa-chart-bar"></i>
                    Generate Report
                </button>
                <button class="export-report-btn" id="exportReportBtn">
                    <i class="fas fa-download"></i>
                    Export Report
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
                        <p class="kpi-value" id="totalPremiumKPI">₹2,45,500</p>
                        <span class="kpi-change positive" id="premiumChange">+12.5%</span>
                    </div>
                </div>
                <div class="kpi-card glass-effect">
                    <div class="kpi-icon revenue">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="kpi-content">
                        <h4>Total Revenue</h4>
                        <p class="kpi-value" id="totalRevenueKPI">₹98,200</p>
                        <span class="kpi-change positive" id="revenueChange">+8.3%</span>
                    </div>
                </div>
                <div class="kpi-card glass-effect">
                    <div class="kpi-icon policies">
                        <i class="fas fa-file-contract"></i>
                    </div>
                    <div class="kpi-content">
                        <h4>Active Policies</h4>
                        <p class="kpi-value" id="activePoliciesKPI">156</p>
                        <span class="kpi-change positive" id="policiesChange">+5.4%</span>
                    </div>
                </div>
                <div class="kpi-card glass-effect">
                    <div class="kpi-icon conversion">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <div class="kpi-content">
                        <h4>Conversion Rate</h4>
                        <p class="kpi-value" id="conversionRateKPI">73.2%</p>
                        <span class="kpi-change positive" id="conversionChange">+3.1%</span>
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
                    <canvas id="trendChart"></canvas>
                </div>
                <div class="chart-container glass-effect">
                    <div class="chart-header">
                        <h3>Policy Type Distribution</h3>
                        <div class="chart-controls">
                            <select id="policyTypePeriod">
                                <option value="30" selected>Last 30 Days</option>
                                <option value="90">Last 90 Days</option>
                                <option value="365">Last Year</option>
                            </select>
                        </div>
                    </div>
                    <canvas id="policyTypeChart"></canvas>
                </div>
            </div>
            <div class="chart-row">
                <div class="chart-container glass-effect">
                    <div class="chart-header">
                        <h3>Agent Performance</h3>
                        <div class="chart-controls">
                            <select id="agentPerformancePeriod">
                                <option value="30" selected>Last 30 Days</option>
                                <option value="90">Last 90 Days</option>
                                <option value="365">Last Year</option>
                            </select>
                        </div>
                    </div>
                    <canvas id="agentPerformanceChart"></canvas>
                </div>
                <div class="chart-container glass-effect">
                    <div class="chart-header">
                        <h3>Renewal Status</h3>
                        <div class="chart-controls">
                            <select id="renewalStatusPeriod">
                                <option value="30" selected>Last 30 Days</option>
                                <option value="90">Last 90 Days</option>
                                <option value="365">Last Year</option>
                            </select>
                        </div>
                    </div>
                    <canvas id="renewalStatusChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Detailed Reports Section -->
        <div class="reports-section">
            <div class="reports-tabs">
                <button class="tab-btn active" data-tab="policies">Policies Report</button>
                <button class="tab-btn" data-tab="renewals">Renewals Report</button>
                <button class="tab-btn" data-tab="followups">Follow-ups Report</button>
                <button class="tab-btn" data-tab="agents">Agents Report</button>
            </div>
            
            <!-- Policies Report Tab -->
            <div class="tab-content active" id="policiesReport">
                <div class="report-summary">
                    <div class="summary-card">
                        <h4>Policy Summary</h4>
                        <div class="summary-stats">
                            <div class="summary-stat">
                                <span class="stat-label">Total Policies:</span>
                                <span class="stat-value" id="totalPoliciesReport">178</span>
                            </div>
                            <div class="summary-stat">
                                <span class="stat-label">Active Policies:</span>
                                <span class="stat-value" id="activePoliciesReport">156</span>
                            </div>
                            <div class="summary-stat">
                                <span class="stat-label">Expired Policies:</span>
                                <span class="stat-value" id="expiredPoliciesReport">22</span>
                            </div>
                            <div class="summary-stat">
                                <span class="stat-label">Average Premium:</span>
                                <span class="stat-value" id="avgPremiumReport">₹15,750</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="report-table-container">
                    <table class="report-table" id="policiesReportTable">
                        <thead>
                            <tr>
                                <th>Policy ID</th>
                                <th>Customer Name</th>
                                <th>Policy Type</th>
                                <th>Company</th>
                                <th>Premium</th>
                                <th>Status</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                            </tr>
                        </thead>
                        <tbody id="policiesReportTableBody">
                            <tr>
                                <td>POL001</td>
                                <td>John Doe</td>
                                <td>Motor</td>
                                <td>ABC Insurance</td>
                                <td>₹12,500</td>
                                <td><span class="status-badge active">Active</span></td>
                                <td>2024-02-15</td>
                                <td>2025-02-15</td>
                            </tr>
                            <tr>
                                <td>POL002</td>
                                <td>Jane Smith</td>
                                <td>Health</td>
                                <td>XYZ Insurance</td>
                                <td>₹18,000</td>
                                <td><span class="status-badge active">Active</span></td>
                                <td>2024-03-01</td>
                                <td>2025-03-01</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Renewals Report Tab -->
            <div class="tab-content" id="renewalsReport">
                <div class="report-summary">
                    <div class="summary-card">
                        <h4>Renewal Summary</h4>
                        <div class="summary-stats">
                            <div class="summary-stat">
                                <span class="stat-label">Pending Renewals:</span>
                                <span class="stat-value" id="pendingRenewalsReport">15</span>
                            </div>
                            <div class="summary-stat">
                                <span class="stat-label">Completed Renewals:</span>
                                <span class="stat-value" id="completedRenewalsReport">28</span>
                            </div>
                            <div class="summary-stat">
                                <span class="stat-label">Overdue Renewals:</span>
                                <span class="stat-value" id="overdueRenewalsReport">3</span>
                            </div>
                            <div class="summary-stat">
                                <span class="stat-label">Renewal Rate:</span>
                                <span class="stat-value" id="renewalRateReport">85.2%</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="report-table-container">
                    <table class="report-table" id="renewalsReportTable">
                        <thead>
                            <tr>
                                <th>Policy ID</th>
                                <th>Customer Name</th>
                                <th>Expiry Date</th>
                                <th>Days Left</th>
                                <th>Status</th>
                                <th>Priority</th>
                                <th>Assigned To</th>
                            </tr>
                        </thead>
                        <tbody id="renewalsReportTableBody">
                            <tr>
                                <td>POL001</td>
                                <td>John Doe</td>
                                <td>2025-02-15</td>
                                <td>5 days</td>
                                <td><span class="status-badge pending">Pending</span></td>
                                <td><span class="priority-badge high">High</span></td>
                                <td>Alice Brown</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Follow-ups Report Tab -->
            <div class="tab-content" id="followupsReport">
                <div class="report-summary">
                    <div class="summary-card">
                        <h4>Follow-up Summary</h4>
                        <div class="summary-stats">
                            <div class="summary-stat">
                                <span class="stat-label">Total Follow-ups:</span>
                                <span class="stat-value" id="totalFollowupsReport">35</span>
                            </div>
                            <div class="summary-stat">
                                <span class="stat-label">Completed Today:</span>
                                <span class="stat-value" id="completedTodayReport">15</span>
                            </div>
                            <div class="summary-stat">
                                <span class="stat-label">Pending Follow-ups:</span>
                                <span class="stat-value" id="pendingFollowupsReport">12</span>
                            </div>
                            <div class="summary-stat">
                                <span class="stat-label">Success Rate:</span>
                                <span class="stat-value" id="successRateReport">78.5%</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="report-table-container">
                    <table class="report-table" id="followupsReportTable">
                        <thead>
                            <tr>
                                <th>Customer Name</th>
                                <th>Phone</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Assigned To</th>
                                <th>Last Follow-up</th>
                                <th>Next Follow-up</th>
                            </tr>
                        </thead>
                        <tbody id="followupsReportTableBody">
                            <tr>
                                <td>Sarah Connor</td>
                                <td>+91-9876543210</td>
                                <td>Renewal</td>
                                <td><span class="status-badge pending">Pending</span></td>
                                <td>Alice Brown</td>
                                <td>2025-01-30</td>
                                <td>2025-02-05</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Agents Report Tab -->
            <div class="tab-content" id="agentsReport">
                <div class="report-summary">
                    <div class="summary-card">
                        <h4>Agent Performance Summary</h4>
                        <div class="summary-stats">
                            <div class="summary-stat">
                                <span class="stat-label">Total Agents:</span>
                                <span class="stat-value" id="totalAgentsReport">8</span>
                            </div>
                            <div class="summary-stat">
                                <span class="stat-label">Active Agents:</span>
                                <span class="stat-value" id="activeAgentsReport">7</span>
                            </div>
                            <div class="summary-stat">
                                <span class="stat-label">Top Performer:</span>
                                <span class="stat-value" id="topPerformerReport">Alice Brown</span>
                            </div>
                            <div class="summary-stat">
                                <span class="stat-label">Avg. Performance:</span>
                                <span class="stat-value" id="avgPerformanceReport">82.5%</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="report-table-container">
                    <table class="report-table" id="agentsReportTable">
                        <thead>
                            <tr>
                                <th>Agent Name</th>
                                <th>Policies Sold</th>
                                <th>Total Premium</th>
                                <th>Renewals Handled</th>
                                <th>Follow-ups</th>
                                <th>Performance Score</th>
                            </tr>
                        </thead>
                        <tbody id="agentsReportTableBody">
                            <tr>
                                <td>Alice Brown</td>
                                <td>42</td>
                                <td>₹68,500</td>
                                <td>18</td>
                                <td>25</td>
                                <td>92%</td>
                            </tr>
                            <tr>
                                <td>Charlie Wilson</td>
                                <td>38</td>
                                <td>₹52,300</td>
                                <td>15</td>
                                <td>22</td>
                                <td>85%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Reports specific styles */
.reports-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 16px;
}

.date-range-picker {
    display: flex;
    align-items: center;
    gap: 12px;
}

.date-range-picker label {
    font-weight: 600;
    color: #374151;
}

.date-range-picker input[type="date"] {
    padding: 8px 12px;
    border: 1px solid #D1D5DB;
    border-radius: 6px;
    font-size: 14px;
}

.generate-report-btn, .export-report-btn {
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.generate-report-btn {
    background: linear-gradient(135deg, #3B82F6, #1D4ED8);
    color: white;
}

.export-report-btn {
    background: linear-gradient(135deg, #10B981, #059669);
    color: white;
}

/* KPI Section */
.kpi-section {
    margin-bottom: 32px;
}

.kpi-section h3 {
    margin-bottom: 20px;
    color: #1F2937;
    font-size: 18px;
    font-weight: 600;
}

.kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
}

.kpi-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    padding: 24px;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
}

.kpi-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
}

.kpi-icon.premium {
    background: linear-gradient(135deg, #F59E0B, #D97706);
}

.kpi-icon.revenue {
    background: linear-gradient(135deg, #10B981, #059669);
}

.kpi-icon.policies {
    background: linear-gradient(135deg, #3B82F6, #1D4ED8);
}

.kpi-icon.conversion {
    background: linear-gradient(135deg, #8B5CF6, #7C3AED);
}

.kpi-content h4 {
    font-size: 14px;
    color: #6B7280;
    margin-bottom: 8px;
    font-weight: 500;
}

.kpi-value {
    font-size: 24px;
    font-weight: 700;
    color: #1F2937;
    margin-bottom: 4px;
}

.kpi-change {
    font-size: 12px;
    font-weight: 600;
    padding: 2px 6px;
    border-radius: 4px;
}

.kpi-change.positive {
    background: rgba(16, 185, 129, 0.1);
    color: #10B981;
}

/* Charts Section */
.charts-section {
    margin-bottom: 32px;
}

.chart-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 24px;
    margin-bottom: 24px;
}

.chart-container {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
}

.chart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.chart-header h3 {
    font-size: 16px;
    font-weight: 600;
    color: #1F2937;
}

.chart-controls select {
    padding: 6px 12px;
    border: 1px solid #D1D5DB;
    border-radius: 6px;
    font-size: 12px;
}

/* Reports Tabs */
.reports-section {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
}

.reports-tabs {
    display: flex;
    background: #F9FAFB;
    border-bottom: 1px solid #E5E7EB;
}

.tab-btn {
    padding: 16px 24px;
    background: none;
    border: none;
    font-size: 14px;
    font-weight: 600;
    color: #6B7280;
    cursor: pointer;
    transition: all 0.3s ease;
    border-bottom: 3px solid transparent;
}

.tab-btn.active {
    color: #3B82F6;
    border-bottom-color: #3B82F6;
    background: white;
}

.tab-content {
    display: none;
    padding: 24px;
}

.tab-content.active {
    display: block;
}

/* Report Summary */
.report-summary {
    margin-bottom: 24px;
}

.summary-card {
    background: #F9FAFB;
    border-radius: 8px;
    padding: 20px;
}

.summary-card h4 {
    font-size: 16px;
    font-weight: 600;
    color: #1F2937;
    margin-bottom: 16px;
}

.summary-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
}

.summary-stat {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.stat-label {
    font-size: 14px;
    color: #6B7280;
    font-weight: 500;
}

.stat-value {
    font-size: 16px;
    font-weight: 700;
    color: #1F2937;
}

/* Report Tables */
.report-table-container {
    overflow-x: auto;
}

.report-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}

.report-table th,
.report-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #E5E7EB;
}

.report-table th {
    background: #F9FAFB;
    font-weight: 600;
    color: #374151;
}

.report-table td {
    color: #6B7280;
}

/* Reports specific styles */
.reports-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 16px;
}

.generate-report-btn, .export-report-btn {
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

.generate-report-btn:hover, .export-report-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(79, 70, 229, 0.4);
}

.export-report-btn {
    background: linear-gradient(135deg, #10B981, #059669);
}

.export-report-btn:hover {
    box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
}
</style>

@push('scripts')
<script>
    // Dynamic Reports: fetch datasets, compute KPIs, render tables and charts without changing layout
    let policies = [];
    let renewals = [];
    let followups = [];
    let agents = [];
    let reports = [];

    let charts = {
        trend: null,
        policyType: null,
        agentPerformance: null,
        renewalStatus: null
    };

    document.addEventListener('DOMContentLoaded', () => {

        // Default date range: last 30 days
        const end = new Date();
        const start = new Date();
        start.setDate(end.getDate() - 29);
        setDateInput('reportStartDate', start);
        setDateInput('reportEndDate', end);

        // Load all datasets then render
        loadAll().then(renderAll).catch((e) => {
            console.error('Failed initializing reports', e);
            showNotification('Error loading reports data', 'error');
        });

        // Wire controls
    const controls = ['reportStartDate', 'reportEndDate', 'reportTypeFilter'];
        controls.forEach(id => {
            const el = document.getElementById(id);
            if (el) el.addEventListener('change', () => renderAll());
        });

        // Tabs
        const tabBtns = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');
        tabBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const tabName = this.getAttribute('data-tab');
                tabBtns.forEach(b => b.classList.remove('active'));
                tabContents.forEach(c => c.classList.remove('active'));
                this.classList.add('active');
                const activeEl = document.getElementById(tabName + 'Report');
                if (activeEl) activeEl.classList.add('active');
            });
        });

        // Buttons
        const generateReportBtn = document.getElementById('generateReportBtn');
        if (generateReportBtn) generateReportBtn.addEventListener('click', onGenerateReport);
        const exportReportBtn = document.getElementById('exportReportBtn');
        if (exportReportBtn) exportReportBtn.addEventListener('click', onExportReport);
    });

    function setDateInput(id, date) {
        const el = document.getElementById(id);
        if (!el) return;
        const yyyy = date.getFullYear();
        const mm = String(date.getMonth() + 1).padStart(2, '0');
        const dd = String(date.getDate()).padStart(2, '0');
        el.value = `${yyyy}-${mm}-${dd}`;
    }

    async function loadAll() {
        const fetchJson = (url) => fetch(url, {
            method: 'GET',
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json' }
        }).then(r => r.ok ? r.json() : Promise.reject(new Error(`${r.status} ${r.statusText}`)));

        const [pol, ren, fol, ag, rep] = await Promise.all([
            fetchJson('/api/policies').catch(() => ({ policies: [] })),
            fetchJson('/api/renewals').catch(() => ({ renewals: [] })),
            fetchJson('/api/followups').catch(() => ({ followups: [] })),
            fetchJson('/api/agents').catch(() => ({ agents: [] })),
            fetchJson('/api/reports').catch(() => ({ reports: [] }))
        ]);

        policies = pol.policies || [];
        renewals = ren.renewals || [];
        followups = fol.followups || [];
        agents = ag.agents || [];
        reports = rep.reports || [];
    }

    function renderAll() {
        const range = getSelectedRange();
        // Filter datasets by range
        const polInRange = filterByDate(policies, 'createdAt', range.start, range.end);
        const renInRange = filterByDate(renewals, 'dueDate', range.start, range.end);
        const folInRange = filterByDate(followups, 'nextFollowupDate', range.start, range.end);
        const agAll = agents; // no date field; show all

        // Update KPIs
        updateKPIs(polInRange, renInRange, folInRange);

        // Render tables
        renderPoliciesTable(polInRange);
        renderRenewalsTable(renInRange);
        renderFollowupsTable(folInRange);
        renderAgentsTable(agAll);

        // Charts
        renderCharts(polInRange, renInRange, folInRange, agAll);

        // Apply report type filter (show only relevant tab content)
        applyReportTypeVisibility();
    }

    function getSelectedRange() {
        const start = new Date(document.getElementById('reportStartDate')?.value || '1970-01-01');
        const end = new Date(document.getElementById('reportEndDate')?.value || '2999-12-31');
        // Normalize to end of day
        end.setHours(23, 59, 59, 999);
        return { start, end };
    }

    function filterByDate(items, field, start, end) {
        return (items || []).filter(it => {
            const v = it && it[field];
            if (!v) return false;
            const d = new Date(v);
            return d >= start && d <= end;
        });
    }

    function formatCurrency(n) {
        const val = Number(n || 0);
        return '₹' + val.toLocaleString('en-IN');
    }

    function updateText(id, text) {
        const el = document.getElementById(id);
        if (el) el.textContent = text;
    }

    function updateKPIs(pol, ren, fol) {
        // Total Premium & Revenue from policies
        const totalPremium = pol.reduce((s, p) => s + Number(p.premium || 0), 0);
        const totalRevenue = pol.reduce((s, p) => s + Number(p.revenue || 0), 0);
        const activePolicies = (policies || []).filter(p => p.status === 'Active').length;

        // Conversion rate from followups: Completed / Total
        const totalFollowups = fol.length;
        const completedFollowups = fol.filter(f => (f.status || '').toLowerCase() === 'completed').length;
        const conversionRate = totalFollowups ? ((completedFollowups / totalFollowups) * 100) : 0;

        updateText('totalPremiumKPI', formatCurrency(totalPremium));
        updateText('totalRevenueKPI', formatCurrency(totalRevenue));
        updateText('activePoliciesKPI', String(activePolicies));
        updateText('conversionRateKPI', `${conversionRate.toFixed(1)}%`);

        // Optional trend vs previous period
        const { start, end } = getSelectedRange();
        const spanDays = Math.max(1, Math.ceil((end - start) / (1000*60*60*24)) + 1);
        const prevStart = new Date(start); prevStart.setDate(start.getDate() - spanDays);
        const prevEnd = new Date(start); prevEnd.setDate(start.getDate() - 1); prevEnd.setHours(23,59,59,999);

    const polPrev = filterByDate(policies, 'createdAt', prevStart, prevEnd);
        const totalPremiumPrev = polPrev.reduce((s, p) => s + Number(p.premium || 0), 0);
        const totalRevenuePrev = polPrev.reduce((s, p) => s + Number(p.revenue || 0), 0);

        const premiumDelta = pctDelta(totalPremiumPrev, totalPremium);
        const revenueDelta = pctDelta(totalRevenuePrev, totalRevenue);
        updateDelta('premiumChange', premiumDelta);
        updateDelta('revenueChange', revenueDelta);
        // For Active Policies and Conversion, keep static or compute similarly if needed
    }

    function pctDelta(prev, curr) {
        if (!prev && !curr) return 0;
        if (!prev) return 100;
        return ((curr - prev) / prev) * 100;
    }

    function updateDelta(id, val) {
        const el = document.getElementById(id);
        if (!el) return;
        const sign = val >= 0 ? '+' : '';
        el.textContent = `${sign}${val.toFixed(1)}%`;
        el.classList.toggle('positive', val >= 0);
    }

    function renderPoliciesTable(items) {
        const tbody = document.getElementById('policiesReportTableBody');
        if (!tbody) return;
        const rows = (items || []).map(p => `
            <tr>
                <td>${p.policyNumber || p.id}</td>
                <td>${p.customerName || '—'}</td>
                <td>${p.policyType || '—'}</td>
                <td>${p.companyName || '—'}</td>
                <td>${formatCurrency(p.premium)}</td>
                <td><span class="status-badge ${p.status && p.status.toLowerCase() === 'active' ? 'active' : 'pending'}">${p.status || '—'}</span></td>
                <td>${p.startDate || '—'}</td>
                <td>${p.endDate || '—'}</td>
            </tr>
        `).join('');
        tbody.innerHTML = rows || '';
        // Summary
        updateText('totalPoliciesReport', String(policies.length));
        updateText('activePoliciesReport', String((policies || []).filter(p => p.status === 'Active').length));
        updateText('expiredPoliciesReport', String((policies || []).filter(p => p.status === 'Expired').length));
        const avgPrem = items.length ? (items.reduce((s, p) => s + Number(p.premium || 0), 0) / items.length) : 0;
        updateText('avgPremiumReport', formatCurrency(avgPrem));
    }

    function renderRenewalsTable(items) {
        const tbody = document.getElementById('renewalsReportTableBody');
        if (!tbody) return;
        const rows = (items || []).map(r => {
            const due = r.dueDate ? new Date(r.dueDate) : null;
            const daysLeft = due ? Math.ceil((due - new Date()) / (1000*60*60*24)) : null;
            const statusClass = (r.status || '').toLowerCase();
            const priority = daysLeft !== null ? (daysLeft <= 7 ? 'high' : daysLeft <= 14 ? 'medium' : 'low') : 'low';
            return `
            <tr>
                <td>${r.policyNumber || r.id}</td>
                <td>${r.customerName || '—'}</td>
                <td>${r.dueDate || '—'}</td>
                <td>${daysLeft !== null ? daysLeft + ' days' : '—'}</td>
                <td><span class="status-badge ${statusClass}">${r.status || '—'}</span></td>
                <td><span class="priority-badge ${priority}">${priority.charAt(0).toUpperCase() + priority.slice(1)}</span></td>
                <td>${r.agentName || '—'}</td>
            </tr>`;
        }).join('');
        tbody.innerHTML = rows || '';
        // Summary
        updateText('pendingRenewalsReport', String(items.filter(r => r.status === 'Pending').length));
        updateText('completedRenewalsReport', String(items.filter(r => r.status === 'Completed').length));
        updateText('overdueRenewalsReport', String(items.filter(r => r.status === 'Overdue').length));
        const total = items.length || 1; // avoid div by zero
        const rate = (items.filter(r => r.status === 'Completed').length / total) * 100;
        updateText('renewalRateReport', rate.toFixed(1) + '%');
    }

    function renderFollowupsTable(items) {
        const tbody = document.getElementById('followupsReportTableBody');
        if (!tbody) return;
        const rows = (items || []).map(f => `
            <tr>
                <td>${f.customerName || '—'}</td>
                <td>${f.phone || '—'}</td>
                <td>${f.followupType || '—'}</td>
                <td><span class="status-badge ${(f.status || '').toLowerCase()}">${f.status || '—'}</span></td>
                <td>${f.assignedTo || '—'}</td>
                <td>${f.lastFollowupDate || '—'}</td>
                <td>${f.nextFollowupDate || '—'}</td>
            </tr>
        `).join('');
        tbody.innerHTML = rows || '';
        // Summary
        updateText('totalFollowupsReport', String(items.length));
        const todayStr = new Date().toISOString().slice(0,10);
        updateText('completedTodayReport', String(items.filter(f => f.status === 'Completed' && f.lastFollowupDate === todayStr).length));
        updateText('pendingFollowupsReport', String(items.filter(f => (f.status || '').toLowerCase() === 'pending').length));
        const total = items.length || 1;
        const success = items.filter(f => (f.status || '').toLowerCase() === 'completed').length;
        updateText('successRateReport', ((success / total) * 100).toFixed(1) + '%');
    }

    function renderAgentsTable(items) {
        const tbody = document.getElementById('agentsReportTableBody');
        if (!tbody) return;
        const rows = (items || []).map(a => {
            const perf = parseFloat(String(a.performance || '0').replace('%','')) || 0;
            return `
            <tr>
                <td>${a.name || '—'}</td>
                <td>${a.policies || 0}</td>
                <td>${formatCurrency(a.totalPremium || 0)}</td>
                <td>${a.renewalsHandled || 0}</td>
                <td>${a.followups || 0}</td>
                <td>${perf.toFixed(0)}%</td>
            </tr>`;
        }).join('');
        tbody.innerHTML = rows || '';
        // Summary
        updateText('totalAgentsReport', String(items.length));
        updateText('activeAgentsReport', String(items.filter(a => a.status === 'Active').length));
        // Top performer by perf
        const top = [...items].sort((a,b) => (parseFloat(String(b.performance||'0').replace('%',''))||0) - (parseFloat(String(a.performance||'0').replace('%',''))||0))[0];
        updateText('topPerformerReport', top ? (top.name || '—') : '—');
        const avgPerf = items.length ? (items.reduce((s,a)=> s + (parseFloat(String(a.performance||'0').replace('%',''))||0), 0) / items.length) : 0;
        updateText('avgPerformanceReport', avgPerf.toFixed(1) + '%');
    }

    function renderCharts(pol, ren, fol, ag) {
        if (typeof Chart === 'undefined') return;
        // Destroy existing charts to avoid overlay
        Object.keys(charts).forEach(k => { if (charts[k]) { charts[k].destroy(); charts[k] = null; } });

    // Trend: daily Premium and Revenue over selected period
    const range = getSelectedRange();
        const days = [];
    for (let d = new Date(range.start); d <= range.end; d.setDate(d.getDate()+1)) {
            days.push(new Date(d.getFullYear(), d.getMonth(), d.getDate()));
        }
        const fmt = (dt) => dt.toISOString().slice(0,10);
        const labels = days.map(fmt);
    const premiumSeries = labels.map(day => pol.filter(p => (p.createdAt || p.startDate || '').slice(0,10) === day).reduce((s,p) => s + Number(p.premium||0), 0));
    const revenueSeries = labels.map(day => pol.filter(p => (p.createdAt || p.startDate || '').slice(0,10) === day).reduce((s,p) => s + Number(p.revenue||0), 0));

        const trendCtx = document.getElementById('trendChart');
        if (trendCtx) {
            charts.trend = new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels,
                    datasets: [
                        { label: 'Premium', data: premiumSeries, borderColor: '#4F46E5', backgroundColor: 'rgba(79, 70, 229, 0.1)', tension: 0.4 },
                        { label: 'Revenue', data: revenueSeries, borderColor: '#10B981', backgroundColor: 'rgba(16, 185, 129, 0.1)', tension: 0.4 }
                    ]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'top' } } }
            });
        }

    // Policy Type Distribution
    const typeCounts = pol.reduce((acc, p) => { const t = p.policyType || 'Other'; acc[t] = (acc[t]||0) + 1; return acc; }, {});
    const ptLabels = Object.keys(typeCounts);
    const ptData = Object.values(typeCounts);
        const policyTypeCtx = document.getElementById('policyTypeChart');
        if (policyTypeCtx) {
            charts.policyType = new Chart(policyTypeCtx, {
                type: 'doughnut',
                data: { labels: ptLabels, datasets: [{ data: ptData, backgroundColor: ['#4F46E5','#10B981','#F59E0B','#6B7280','#EF4444','#3B82F6'] }] },
                options: { responsive: true, maintainAspectRatio: false }
            });
        }

    // Agent Performance (policies sold per agent from policies dataset)
    const perAgent = pol.reduce((acc,p) => { const a = p.agentName || 'Unknown'; acc[a] = (acc[a]||0) + 1; return acc; }, {});
        const agLabels = Object.keys(perAgent);
        const agData = Object.values(perAgent);
        const agentPerformanceCtx = document.getElementById('agentPerformanceChart');
        if (agentPerformanceCtx) {
            charts.agentPerformance = new Chart(agentPerformanceCtx, {
                type: 'bar',
                data: { labels: agLabels, datasets: [{ label: 'Policies Sold', data: agData, backgroundColor: 'rgba(79, 70, 229, 0.8)' }] },
                options: { responsive: true, maintainAspectRatio: false }
            });
        }

    // Renewal Status
    const statusCounts = ren.reduce((acc, r) => { const s = r.status || 'Unknown'; acc[s] = (acc[s]||0) + 1; return acc; }, {});
        const rsLabels = Object.keys(statusCounts);
        const rsData = Object.values(statusCounts);
        const renewalStatusCtx = document.getElementById('renewalStatusChart');
        if (renewalStatusCtx) {
            charts.renewalStatus = new Chart(renewalStatusCtx, {
                type: 'pie',
                data: { labels: rsLabels, datasets: [{ data: rsData, backgroundColor: ['#10B981','#F59E0B','#EF4444','#6B7280','#3B82F6'] }] },
                options: { responsive: true, maintainAspectRatio: false }
            });
        }
    }

    function applyReportTypeVisibility() {
        const filter = document.getElementById('reportTypeFilter')?.value || 'all';
        const tabs = {
            policies: document.getElementById('policiesReport'),
            renewals: document.getElementById('renewalsReport'),
            followups: document.getElementById('followupsReport'),
            agents: document.getElementById('agentsReport')
        };
        Object.values(tabs).forEach(el => { if (el) el.style.display = ''; });
        if (filter !== 'all') {
            Object.entries(tabs).forEach(([key, el]) => { if (el) el.style.display = (key === filter) ? '' : 'none'; });
        }
    }

    function onGenerateReport() {
        showNotification('Report generation queued', 'success');
        // Optionally POST to /reports to log a generated report; keeping UI unchanged per requirement
    }

    function onExportReport() {
        showNotification('Exporting current view...', 'info');
        // Could export the currently visible table to CSV in future
    }

    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
                <span>${message}</span>
            </div>
            <button class="notification-close">
                <i class="fas fa-times"></i>
            </button>
        `;
        document.body.appendChild(notification);
        setTimeout(() => notification.classList.add('show'), 50);
        setTimeout(() => { notification.classList.remove('show'); setTimeout(() => notification.remove(), 300); }, 3000);
        notification.querySelector('.notification-close')?.addEventListener('click', () => { notification.classList.remove('show'); setTimeout(() => notification.remove(), 300); });
    }
</script>
@endpush

@endsection
