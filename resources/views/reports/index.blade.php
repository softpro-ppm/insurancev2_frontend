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
    // Reports page initialization
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Reports page initialized');
        
        // Initialize charts if Chart.js is available
        if (typeof Chart !== 'undefined') {
            // Trend Chart
            const trendCtx = document.getElementById('trendChart');
            if (trendCtx) {
                new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                        datasets: [{
                            label: 'Premium',
                            data: [45000, 52000, 48000, 61000],
                            borderColor: '#4F46E5',
                            backgroundColor: 'rgba(79, 70, 229, 0.1)',
                            tension: 0.4
                        }, {
                            label: 'Revenue',
                            data: [18000, 22000, 19000, 25000],
                            borderColor: '#10B981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top'
                            }
                        }
                    }
                });
            }

            // Policy Type Distribution Chart
            const policyTypeCtx = document.getElementById('policyTypeChart');
            if (policyTypeCtx) {
                new Chart(policyTypeCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Motor', 'Health', 'Life', 'Others'],
                        datasets: [{
                            data: [45, 30, 20, 5],
                            backgroundColor: [
                                '#4F46E5',
                                '#10B981',
                                '#F59E0B',
                                '#6B7280'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
            }

            // Agent Performance Chart
            const agentPerformanceCtx = document.getElementById('agentPerformanceChart');
            if (agentPerformanceCtx) {
                new Chart(agentPerformanceCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Agent 1', 'Agent 2', 'Agent 3', 'Agent 4'],
                        datasets: [{
                            label: 'Policies Sold',
                            data: [25, 18, 22, 15],
                            backgroundColor: 'rgba(79, 70, 229, 0.8)'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
            }

            // Renewal Status Chart
            const renewalStatusCtx = document.getElementById('renewalStatusChart');
            if (renewalStatusCtx) {
                new Chart(renewalStatusCtx, {
                    type: 'pie',
                    data: {
                        labels: ['Renewed', 'Pending', 'Expired'],
                        datasets: [{
                            data: [65, 25, 10],
                            backgroundColor: [
                                '#10B981',
                                '#F59E0B',
                                '#EF4444'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
            }
        }
        
        // Tab functionality
        const tabBtns = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');
        
        tabBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const tabName = this.getAttribute('data-tab');
                
                // Remove active class from all tabs
                tabBtns.forEach(b => b.classList.remove('active'));
                tabContents.forEach(c => c.classList.remove('active'));
                
                // Add active class to clicked tab
                this.classList.add('active');
                document.getElementById(tabName + 'Report').classList.add('active');
            });
        });
        
        // Generate report button functionality
        const generateReportBtn = document.getElementById('generateReportBtn');
        if (generateReportBtn) {
            generateReportBtn.addEventListener('click', function() {
                console.log('Generating report...');
                // Add report generation functionality here
            });
        }
        
        // Export report button functionality
        const exportReportBtn = document.getElementById('exportReportBtn');
        if (exportReportBtn) {
            exportReportBtn.addEventListener('click', function() {
                console.log('Exporting report...');
                // Add export functionality here
            });
        }
    });
</script>
@endpush

@endsection
