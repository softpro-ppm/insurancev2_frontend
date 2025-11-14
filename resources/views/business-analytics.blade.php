@extends('layouts.insurance')

@section('title', 'My Business - Analytics')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/business-analytics.css') }}?v=1.0">
@endpush

@section('content')
<div class="page active" id="businessAnalytics">
    <div class="page-header">
        <h1>My Business Analytics</h1>
        <div class="header-controls">
            <select class="period-selector" id="periodSelector">
                <option value="month">This Month</option>
                <option value="quarter">This Quarter</option>
                <option value="6months">Last 6 Months</option>
                <option value="12months" selected>Last 12 Months</option>
                <option value="year">This Year</option>
                <option value="all">All Time</option>
            </select>
            <button class="btn btn-primary" id="exportBusinessReport">
                <i class="fas fa-download"></i> Export Report
            </button>
        </div>
    </div>

    <!-- SECTION 1: Key Performance Indicators -->
    <div class="kpi-section">
        <h2><i class="fas fa-chart-line"></i> Key Performance Indicators</h2>
        <div class="kpi-cards">
            <div class="kpi-card">
                <div class="kpi-icon total-value">
                    <i class="fas fa-rupee-sign"></i>
                </div>
                <div class="kpi-content">
                    <div class="kpi-label">Total Business Value</div>
                    <div class="kpi-value" id="kpiTotalRevenue">₹0</div>
                    <div class="kpi-sublabel">
                        <span id="kpiTotalPremium">₹0</span> Premium Collected
                    </div>
                    <div class="kpi-growth positive" id="kpiRevenueGrowth">
                        <i class="fas fa-arrow-up"></i> 0%
                    </div>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon active">
                    <i class="fas fa-file-contract"></i>
                </div>
                <div class="kpi-content">
                    <div class="kpi-label">Active Business</div>
                    <div class="kpi-value" id="kpiActivePolicies">0</div>
                    <div class="kpi-sublabel">
                        Active Policies
                    </div>
                    <div class="kpi-growth positive" id="kpiPolicyGrowth">
                        <i class="fas fa-arrow-up"></i> 0%
                    </div>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon profit">
                    <i class="fas fa-percentage"></i>
                </div>
                <div class="kpi-content">
                    <div class="kpi-label">Profit Margin</div>
                    <div class="kpi-value" id="kpiProfitMargin">0%</div>
                    <div class="kpi-sublabel">
                        Average Across All Policies
                    </div>
                    <div class="kpi-metric">
                        Avg. Policy: <span id="kpiAvgValue">₹0</span>
                    </div>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon recurring">
                    <i class="fas fa-sync-alt"></i>
                </div>
                <div class="kpi-content">
                    <div class="kpi-label">Monthly Recurring</div>
                    <div class="kpi-value" id="kpiMRR">₹0</div>
                    <div class="kpi-sublabel">
                        Estimated Monthly Revenue
                    </div>
                    <div class="kpi-metric">
                        Projected Annual: <span id="kpiProjectedAnnual">₹0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SECTION 2: Visual Analytics -->
    <div class="analytics-section">
        <h2><i class="fas fa-chart-area"></i> Revenue Trend Analysis</h2>
        <div class="chart-container glass-effect">
            <div class="chart-header">
                <h3>Premium vs Revenue vs Profit Trend</h3>
                <div class="chart-legend">
                    <span class="legend-item"><span class="legend-color" style="background: #4f46e5;"></span> Premium</span>
                    <span class="legend-item"><span class="legend-color" style="background: #10b981;"></span> Revenue</span>
                    <span class="legend-item"><span class="legend-color" style="background: #f59e0b;"></span> Net Profit</span>
                    <span class="legend-item"><span class="legend-color" style="background: #ef4444;"></span> Payout</span>
                </div>
            </div>
            <canvas id="revenueTrendChart"></canvas>
        </div>
    </div>

    <!-- Policy Distribution and Business Performance -->
    <div class="analytics-grid">
        <!-- Policy Type Distribution -->
        <div class="analytics-card glass-effect">
            <h3><i class="fas fa-chart-pie"></i> Policy Type Distribution</h3>
            <canvas id="policyDistributionChart"></canvas>
            <div class="distribution-stats" id="distributionStats">
                <!-- Will be populated by JavaScript -->
            </div>
        </div>

        <!-- Business Type Performance -->
        <div class="analytics-card glass-effect">
            <h3><i class="fas fa-users"></i> Business Type Performance</h3>
            <canvas id="businessTypeChart"></canvas>
            <div class="performance-comparison" id="businessTypeStats">
                <!-- Will be populated by JavaScript -->
            </div>
        </div>
    </div>

    <!-- Top Companies Chart -->
    <div class="analytics-section">
        <h2><i class="fas fa-building"></i> Top Insurance Companies</h2>
        <div class="chart-container glass-effect">
            <canvas id="topCompaniesChart"></canvas>
        </div>
    </div>

    <!-- SECTION 3: Profitability Analysis -->
    <div class="analytics-section">
        <h2><i class="fas fa-dollar-sign"></i> Profitability Analysis</h2>
        <div class="table-container glass-effect">
            <table class="analytics-table" id="profitabilityTable">
                <thead>
                    <tr>
                        <th>Metric</th>
                        <th>Motor</th>
                        <th>Health</th>
                        <th>Life</th>
                        <th class="total-column">Total</th>
                    </tr>
                </thead>
                <tbody id="profitabilityTableBody">
                    <!-- Will be populated by JavaScript -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- SECTION 4: Agent Performance -->
    <div class="analytics-section">
        <h2><i class="fas fa-user-tie"></i> Agent Performance</h2>
        <div class="table-container glass-effect">
            <table class="analytics-table" id="agentPerformanceTable">
                <thead>
                    <tr>
                        <th>Agent Name</th>
                        <th>Policies</th>
                        <th>Total Premium</th>
                        <th>Revenue</th>
                        <th>Payout</th>
                        <th>Avg. Policy Value</th>
                        <th>Profit Margin</th>
                        <th>Performance</th>
                    </tr>
                </thead>
                <tbody id="agentPerformanceBody">
                    <!-- Will be populated by JavaScript -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- SECTION 5: Monthly Growth Trend -->
    <div class="analytics-section">
        <h2><i class="fas fa-chart-line"></i> Monthly Growth Trend</h2>
        <div class="chart-container glass-effect">
            <canvas id="monthlyGrowthChart"></canvas>
        </div>
    </div>

    <!-- Financial Insights: Renewal Opportunities -->
    <div class="analytics-section">
        <h2><i class="fas fa-calendar-check"></i> Renewal Opportunities</h2>
        <div class="renewal-cards">
            <div class="renewal-card">
                <div class="renewal-icon next-30">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="renewal-content">
                    <div class="renewal-label">Next 30 Days</div>
                    <div class="renewal-count" id="renewalNext30Count">0 Policies</div>
                    <div class="renewal-revenue">
                        Est. Revenue: <span id="renewalNext30Revenue">₹0</span>
                    </div>
                </div>
            </div>

            <div class="renewal-card">
                <div class="renewal-icon next-60">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="renewal-content">
                    <div class="renewal-label">31-60 Days</div>
                    <div class="renewal-count" id="renewalNext60Count">0 Policies</div>
                    <div class="renewal-revenue">
                        Est. Revenue: <span id="renewalNext60Revenue">₹0</span>
                    </div>
                </div>
            </div>

            <div class="renewal-card">
                <div class="renewal-icon next-90">
                    <i class="fas fa-calendar"></i>
                </div>
                <div class="renewal-content">
                    <div class="renewal-label">61-90 Days</div>
                    <div class="renewal-count" id="renewalNext90Count">0 Policies</div>
                    <div class="renewal-revenue">
                        Est. Revenue: <span id="renewalNext90Revenue">₹0</span>
                    </div>
                </div>
            </div>

            <div class="renewal-card highlight">
                <div class="renewal-icon rate">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="renewal-content">
                    <div class="renewal-label">Historical Renewal Rate</div>
                    <div class="renewal-count" id="historicalRenewalRate">0%</div>
                    <div class="renewal-revenue">
                        Based on Past Performance
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Business Analytics page marker - actual functionality is in app.js
console.log('✅ Business Analytics page loaded - functionality handled by app.js');
</script>
@endpush

@endsection

