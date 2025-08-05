@extends('layouts.insurance')

@section('title', 'Reports & Analytics - Insurance Management System 2.0')

@section('content')
<div class="page" id="reports">
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
                <div class="kpi-card">
                    <div class="kpi-icon premium">
                        <i class="fas fa-rupee-sign"></i>
                    </div>
                    <div class="kpi-content">
                        <h4>Total Premium</h4>
                        <p class="kpi-value" id="totalPremiumKPI">₹0</p>
                        <span class="kpi-change positive" id="premiumChange">+0%</span>
                    </div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-icon revenue">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="kpi-content">
                        <h4>Total Revenue</h4>
                        <p class="kpi-value" id="totalRevenueKPI">₹0</p>
                        <span class="kpi-change positive" id="revenueChange">+0%</span>
                    </div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-icon policies">
                        <i class="fas fa-file-contract"></i>
                    </div>
                    <div class="kpi-content">
                        <h4>Active Policies</h4>
                        <p class="kpi-value" id="activePoliciesKPI">0</p>
                        <span class="kpi-change positive" id="policiesChange">+0%</span>
                    </div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-icon conversion">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <div class="kpi-content">
                        <h4>Conversion Rate</h4>
                        <p class="kpi-value" id="conversionRateKPI">0%</p>
                        <span class="kpi-change positive" id="conversionChange">+0%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="charts-section">
            <div class="chart-row">
                <div class="chart-container">
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
                <div class="chart-container">
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
                                <span class="stat-value" id="totalPoliciesReport">0</span>
                            </div>
                            <div class="summary-stat">
                                <span class="stat-label">Active Policies:</span>
                                <span class="stat-value" id="activePoliciesReport">0</span>
                            </div>
                            <div class="summary-stat">
                                <span class="stat-label">Expired Policies:</span>
                                <span class="stat-value" id="expiredPoliciesReport">0</span>
                            </div>
                            <div class="summary-stat">
                                <span class="stat-label">Average Premium:</span>
                                <span class="stat-value" id="avgPremiumReport">₹0</span>
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
                            <!-- Data will be populated by JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
