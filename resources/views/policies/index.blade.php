@extends('layouts.insurance')

@section('title', 'Policies - Insurance Management System 2.0')

@section('content')
<div class="page" id="policies">
    <div class="page-header">
        <h1>Policies</h1>
        <p>Manage all insurance policies</p>
    </div>
    <div class="page-content">
        <!-- Policies Controls -->
        <div class="policies-controls">
            <div class="controls-left">
                <button class="add-policy-btn" id="addPolicyFromPoliciesBtn">
                    <i class="fas fa-plus"></i>
                    Add New Policy
                </button>
            </div>
            <div class="controls-right">
                <div class="filter-group">
                    <label for="policyTypeFilter">Policy Type:</label>
                    <select id="policyTypeFilter">
                        <option value="">All Types</option>
                        <option value="Motor">Motor</option>
                        <option value="Health">Health</option>
                        <option value="Life">Life</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="statusFilter">Status:</label>
                    <select id="statusFilter">
                        <option value="">All Status</option>
                        <option value="Active">Active</option>
                        <option value="Expired">Expired</option>
                        <option value="Pending">Pending</option>
                    </select>
                </div>
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search policies..." id="policiesSearch">
                </div>
            </div>
        </div>

        <!-- Policies Statistics -->
        <div class="policies-stats">
            <div class="stat-card">
                <div class="stat-icon active">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3>Active Policies</h3>
                    <p class="stat-value" id="activePoliciesCount">0</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon expired">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-content">
                    <h3>Expired Policies</h3>
                    <p class="stat-value" id="expiredPoliciesCount">0</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon pending">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <h3>Pending Renewals</h3>
                    <p class="stat-value" id="pendingRenewalsCount">0</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon total">
                    <i class="fas fa-file-contract"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Policies</h3>
                    <p class="stat-value" id="totalPoliciesCount">0</p>
                </div>
            </div>
        </div>

        <!-- Policies Data Table -->
        <div class="data-table-container">
            <div class="table-header">
                <h3>All Policies</h3>
                <div class="table-controls">
                    <select class="rows-per-page" id="policiesRowsPerPage">
                        <option value="10">10 rows</option>
                        <option value="25">25 rows</option>
                        <option value="50">50 rows</option>
                        <option value="100">100 rows</option>
                    </select>
                    <button class="export-btn" id="exportPolicies">
                        <i class="fas fa-download"></i>
                        Export
                    </button>
                </div>
            </div>
            <div class="table-wrapper">
                <table class="data-table" id="policiesPageTable">
                    <thead>
                        <tr>
                            <th data-sort="id">Sl. No <i class="fas fa-sort"></i></th>
                            <th data-sort="type">Policy Type <i class="fas fa-sort"></i></th>
                            <th data-sort="owner">Customer Name <i class="fas fa-sort"></i></th>
                            <th data-sort="phone">Phone <i class="fas fa-sort"></i></th>
                            <th data-sort="company">Insurance Company <i class="fas fa-sort"></i></th>
                            <th data-sort="endDate">End Date <i class="fas fa-sort"></i></th>
                            <th data-sort="premium">Premium <i class="fas fa-sort"></i></th>
                            <th data-sort="status">Status <i class="fas fa-sort"></i></th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="policiesPageTableBody">
                        <!-- Sample data for demonstration -->
                        <tr>
                            <td>1</td>
                            <td><span class="policy-type-badge motor">Motor</span></td>
                            <td>John Doe</td>
                            <td>+91 9876543210</td>
                            <td>HDFC ERGO</td>
                            <td>2025-01-15</td>
                            <td>₹25,000</td>
                            <td><span class="status-badge active">Active</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-btn edit"><i class="fas fa-edit"></i></button>
                                    <button class="action-btn"><i class="fas fa-eye"></i></button>
                                    <button class="action-btn delete"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td><span class="policy-type-badge health">Health</span></td>
                            <td>Jane Smith</td>
                            <td>+91 9876543211</td>
                            <td>Star Health</td>
                            <td>2025-02-01</td>
                            <td>₹15,000</td>
                            <td><span class="status-badge active">Active</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-btn edit"><i class="fas fa-edit"></i></button>
                                    <button class="action-btn"><i class="fas fa-eye"></i></button>
                                    <button class="action-btn delete"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td><span class="policy-type-badge life">Life</span></td>
                            <td>Bob Johnson</td>
                            <td>+91 9876543212</td>
                            <td>LIC</td>
                            <td>2024-12-31</td>
                            <td>₹50,000</td>
                            <td><span class="status-badge expired">Expired</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-btn edit"><i class="fas fa-edit"></i></button>
                                    <button class="action-btn"><i class="fas fa-eye"></i></button>
                                    <button class="action-btn delete"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="table-pagination">
                <div class="pagination-info">
                    Showing <span id="policiesStartRecord">1</span> to <span id="policiesEndRecord">10</span> of <span id="policiesTotalRecords">50</span> entries
                </div>
                <div class="pagination-controls">
                    <button class="pagination-btn" id="policiesPrevPage" disabled>
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <div class="page-numbers" id="policiesPageNumbers">
                        <button class="page-number active">1</button>
                        <button class="page-number">2</button>
                        <button class="page-number">3</button>
                    </div>
                    <button class="pagination-btn" id="policiesNextPage">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
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
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize policies statistics
    updatePoliciesStats();
});

function updatePoliciesStats() {
    // Update statistics (this would normally come from your backend)
    document.getElementById('activePoliciesCount').textContent = '142';
    document.getElementById('expiredPoliciesCount').textContent = '23';
    document.getElementById('pendingRenewalsCount').textContent = '8';
    document.getElementById('totalPoliciesCount').textContent = '173';
}
</script>

<!-- Add/Edit Policy Modal -->
<div class="modal" id="policyModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="policyModalTitle">Add New Policy</h2>
            <button class="modal-close" id="closePolicyModal">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Step 1: Policy Type Selection -->
        <div class="modal-body" id="step1">
            <h3 style="text-align: center; margin-bottom: 20px;">Select Policy Type</h3>
            <div class="form-section">
                <div class="form-group">
                    <label for="policyTypeSelect">Policy Type *</label>
                    <select id="policyTypeSelect" required>
                        <option value="">Select Policy Type</option>
                        <option value="Motor">Motor Insurance</option>
                        <option value="Health">Health Insurance</option>
                        <option value="Life">Life Insurance</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="cancelPolicy">Cancel</button>
                <button type="button" class="btn btn-primary" id="nextStep1" disabled>Next</button>
            </div>
        </div>

        <!-- Step 2: Business Type Selection -->
        <div class="modal-body" id="step2" style="display: none;">
            <h3 style="text-align: center; margin-bottom: 20px;">Select Business Type</h3>
            <div class="form-section">
                <div class="form-group">
                    <label for="businessTypeSelect">Business Type *</label>
                    <select id="businessTypeSelect" required>
                        <option value="">Select Business Type</option>
                        <option value="Self">Self</option>
                        <option value="Agent1">Agent 1</option>
                        <option value="Agent2">Agent 2</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="prevStep2">Previous</button>
                <button type="button" class="btn btn-primary" id="nextStep2" disabled>Next</button>
            </div>
        </div>

        <!-- Step 3: Policy Details Form -->
        <div class="modal-body" id="step3" style="display: none;">
            <form id="policyForm">
                <!-- Motor Insurance Form -->
                <div class="policy-form" id="motorForm">
                    <!-- Vehicle Info Section -->
                    <div class="form-section">
                        <h3><i class="fas fa-car"></i> Vehicle Information</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="vehicleNumber">Vehicle Number *</label>
                                <input type="text" id="vehicleNumber" name="vehicleNumber" required>
                            </div>
                            <div class="form-group">
                                <label for="vehicleType">Vehicle Type *</label>
                                <select id="vehicleType" name="vehicleType" required>
                                    <option value="">Select Vehicle Type</option>
                                    <option value="Auto">Auto</option>
                                    <option value="Bus">Bus</option>
                                    <option value="Lorry">Lorry</option>
                                    <option value="Car">Car</option>
                                    <option value="Bike">Bike</option>
                                    <option value="Truck">Truck</option>
                                    <option value="Tractor">Tractor</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Info Section -->
                    <div class="form-section">
                        <h3><i class="fas fa-user"></i> Customer Information</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="customerName">Customer Name *</label>
                                <input type="text" id="customerName" name="customerName" required>
                            </div>
                            <div class="form-group">
                                <label for="customerPhone">Phone Number *</label>
                                <input type="tel" id="customerPhone" name="customerPhone" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="customerEmail">Email Address</label>
                            <input type="email" id="customerEmail" name="customerEmail">
                        </div>
                    </div>

                    <!-- Insurance Info Section -->
                    <div class="form-section">
                        <h3><i class="fas fa-shield-alt"></i> Insurance Information</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="companyName">Company Name *</label>
                                <select id="companyName" name="companyName" required>
                                    <option value="">Select Company</option>
                                    <!-- General Insurance Companies -->
                                    <optgroup label="General Insurance">
                                        <option value="The New India Assurance Co. Ltd.">The New India Assurance Co. Ltd.</option>
                                        <option value="United India Insurance Co. Ltd.">United India Insurance Co. Ltd.</option>
                                        <option value="National Insurance Co. Ltd.">National Insurance Co. Ltd.</option>
                                        <option value="The Oriental Insurance Co. Ltd.">The Oriental Insurance Co. Ltd.</option>
                                        <option value="ICICI Lombard General Insurance Co. Ltd.">ICICI Lombard General Insurance Co. Ltd.</option>
                                        <option value="Bajaj Allianz General Insurance Co. Ltd.">Bajaj Allianz General Insurance Co. Ltd.</option>
                                        <option value="HDFC ERGO General Insurance Co. Ltd.">HDFC ERGO General Insurance Co. Ltd.</option>
                                        <option value="Tata AIG General Insurance Co. Ltd.">Tata AIG General Insurance Co. Ltd.</option>
                                        <option value="Reliance General Insurance Co. Ltd.">Reliance General Insurance Co. Ltd.</option>
                                        <option value="SBI General Insurance Co. Ltd.">SBI General Insurance Co. Ltd.</option>
                                        <option value="IFFCO Tokio General Insurance Co. Ltd.">IFFCO Tokio General Insurance Co. Ltd.</option>
                                        <option value="Future Generali India Insurance Co. Ltd.">Future Generali India Insurance Co. Ltd.</option>
                                        <option value="Kotak Mahindra General Insurance Co. Ltd.">Kotak Mahindra General Insurance Co. Ltd.</option>
                                        <option value="Cholamandalam MS General Insurance Co. Ltd.">Cholamandalam MS General Insurance Co. Ltd.</option>
                                        <option value="Magma HDI General Insurance Co. Ltd.">Magma HDI General Insurance Co. Ltd.</option>
                                        <option value="Zuno General Insurance Ltd.">Zuno General Insurance Ltd.</option>
                                        <option value="Liberty General Insurance Ltd.">Liberty General Insurance Ltd.</option>
                                        <option value="Royal Sundaram General Insurance Co. Ltd.">Royal Sundaram General Insurance Co. Ltd.</option>
                                        <option value="Shriram General Insurance Co. Ltd.">Shriram General Insurance Co. Ltd.</option>
                                        <option value="Universal Sompo General Insurance Co. Ltd.">Universal Sompo General Insurance Co. Ltd.</option>
                                        <option value="Go Digit General Insurance Ltd.">Go Digit General Insurance Ltd.</option>
                                        <option value="Raheja QBE General Insurance Co. Ltd.">Raheja QBE General Insurance Co. Ltd.</option>
                                        <option value="ACKO General Insurance Ltd.">ACKO General Insurance Ltd.</option>
                                        <option value="Navi General Insurance Ltd.">Navi General Insurance Ltd.</option>
                                        <option value="Aditya Birla General Insurance Ltd.">Aditya Birla General Insurance Ltd.</option>
                                    </optgroup>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="insuranceType">Insurance Type *</label>
                                <select id="insuranceType" name="insuranceType" required>
                                    <option value="">Select Insurance Type</option>
                                    <option value="Comprehensive">Comprehensive</option>
                                    <option value="Stand Alone OD">Stand Alone OD</option>
                                    <option value="Third Party">Third Party</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="startDate">Start Date *</label>
                                <input type="date" id="startDate" name="startDate" required>
                            </div>
                            <div class="form-group">
                                <label for="endDate">End Date *</label>
                                <input type="date" id="endDate" name="endDate" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="premium">Premium Amount (₹) *</label>
                                <input type="number" id="premium" name="premium" step="0.01" required>
                            </div>
                            <div class="form-group">
                                <label for="payout">Payout Amount (₹)</label>
                                <input type="number" id="payout" name="payout" step="0.01">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="customerPaidAmount">Customer Paid (₹) *</label>
                                <input type="number" id="customerPaidAmount" name="customerPaidAmount" step="0.01" required>
                            </div>
                            <div class="form-group">
                                <label for="revenue">Revenue (₹) *</label>
                                <input type="number" id="revenue" name="revenue" step="0.01" required>
                            </div>
                        </div>
                    </div>

                    <!-- Upload Section -->
                    <div class="form-section">
                        <h3><i class="fas fa-upload"></i> Documents</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="policyCopy">Policy Copy</label>
                                <input type="file" id="policyCopy" name="policyCopy" accept=".pdf,.jpg,.jpeg,.png">
                            </div>
                            <div class="form-group">
                                <label for="rcCopy">RC Copy</label>
                                <input type="file" id="rcCopy" name="rcCopy" accept=".pdf,.jpg,.jpeg,.png">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="aadharCopy">Aadhar Copy</label>
                                <input type="file" id="aadharCopy" name="aadharCopy" accept=".pdf,.jpg,.jpeg,.png">
                            </div>
                            <div class="form-group">
                                <label for="panCopy">PAN Copy</label>
                                <input type="file" id="panCopy" name="panCopy" accept=".pdf,.jpg,.jpeg,.png">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Health Insurance Form -->
                <div class="policy-form" id="healthForm" style="display: none;">
                    <div class="form-section">
                        <h3><i class="fas fa-user"></i> Customer Information</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="healthCustomerName">Customer Name *</label>
                                <input type="text" id="healthCustomerName" name="customerName" required>
                            </div>
                            <div class="form-group">
                                <label for="healthCustomerPhone">Phone Number *</label>
                                <input type="tel" id="healthCustomerPhone" name="customerPhone" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="healthCustomerEmail">Email Address</label>
                                <input type="email" id="healthCustomerEmail" name="customerEmail">
                            </div>
                            <div class="form-group">
                                <label for="healthCustomerAge">Age *</label>
                                <input type="number" id="healthCustomerAge" name="customerAge" min="1" max="120" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="healthCustomerGender">Gender *</label>
                                <select id="healthCustomerGender" name="customerGender" required>
                                    <option value="">Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="healthSumInsured">Sum Insured (₹) *</label>
                                <input type="number" id="healthSumInsured" name="sumInsured" step="0.01" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3><i class="fas fa-shield-alt"></i> Insurance Information</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="healthCompanyName">Company Name *</label>
                                <select id="healthCompanyName" name="companyName" required>
                                    <option value="">Select Company</option>
                                    <!-- Health Insurance Companies -->
                                    <optgroup label="Health Insurance">
                                        <option value="Star Health and Allied Insurance Co. Ltd.">Star Health and Allied Insurance Co. Ltd.</option>
                                        <option value="Niva Bupa Health Insurance Co. Ltd.">Niva Bupa Health Insurance Co. Ltd.</option>
                                        <option value="Care Health Insurance Ltd.">Care Health Insurance Ltd.</option>
                                        <option value="ManipalCigna Health Insurance Co. Ltd.">ManipalCigna Health Insurance Co. Ltd.</option>
                                        <option value="Aditya Birla Health Insurance Co. Ltd.">Aditya Birla Health Insurance Co. Ltd.</option>
                                    </optgroup>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="healthPlanType">Plan Type *</label>
                                <select id="healthPlanType" name="planType" required>
                                    <option value="">Select Plan Type</option>
                                    <option value="Individual">Individual</option>
                                    <option value="Family Floater">Family Floater</option>
                                    <option value="Senior Citizen">Senior Citizen</option>
                                    <option value="Critical Illness">Critical Illness</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="healthStartDate">Start Date *</label>
                                <input type="date" id="healthStartDate" name="startDate" required>
                            </div>
                            <div class="form-group">
                                <label for="healthEndDate">End Date *</label>
                                <input type="date" id="healthEndDate" name="endDate" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="healthPremium">Premium Amount (₹) *</label>
                                <input type="number" id="healthPremium" name="premium" step="0.01" required>
                            </div>
                            <div class="form-group">
                                <label for="healthCustomerPaid">Customer Paid (₹) *</label>
                                <input type="number" id="healthCustomerPaid" name="customerPaidAmount" step="0.01" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Life Insurance Form -->
                <div class="policy-form" id="lifeForm" style="display: none;">
                    <div class="form-section">
                        <h3><i class="fas fa-user"></i> Customer Information</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="lifeCustomerName">Customer Name *</label>
                                <input type="text" id="lifeCustomerName" name="customerName" required>
                            </div>
                            <div class="form-group">
                                <label for="lifeCustomerPhone">Phone Number *</label>
                                <input type="tel" id="lifeCustomerPhone" name="customerPhone" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="lifeCustomerEmail">Email Address</label>
                                <input type="email" id="lifeCustomerEmail" name="customerEmail">
                            </div>
                            <div class="form-group">
                                <label for="lifeCustomerAge">Age *</label>
                                <input type="number" id="lifeCustomerAge" name="customerAge" min="18" max="75" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="lifeCustomerGender">Gender *</label>
                                <select id="lifeCustomerGender" name="customerGender" required>
                                    <option value="">Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="lifeSumAssured">Sum Assured (₹) *</label>
                                <input type="number" id="lifeSumAssured" name="sumAssured" step="0.01" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3><i class="fas fa-shield-alt"></i> Insurance Information</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="lifeCompanyName">Company Name *</label>
                                <select id="lifeCompanyName" name="companyName" required>
                                    <option value="">Select Company</option>
                                    <!-- Life Insurance Companies -->
                                    <optgroup label="Life Insurance">
                                        <option value="Life Insurance Corporation of India">Life Insurance Corporation of India</option>
                                        <option value="HDFC Life Insurance Co. Ltd.">HDFC Life Insurance Co. Ltd.</option>
                                        <option value="ICICI Prudential Life Insurance Co. Ltd.">ICICI Prudential Life Insurance Co. Ltd.</option>
                                        <option value="SBI Life Insurance Co. Ltd.">SBI Life Insurance Co. Ltd.</option>
                                        <option value="Max Life Insurance Co. Ltd.">Max Life Insurance Co. Ltd.</option>
                                        <option value="Bajaj Allianz Life Insurance Co. Ltd.">Bajaj Allianz Life Insurance Co. Ltd.</option>
                                        <option value="Kotak Mahindra Life Insurance Co. Ltd.">Kotak Mahindra Life Insurance Co. Ltd.</option>
                                        <option value="Aditya Birla Sun Life Insurance Co. Ltd.">Aditya Birla Sun Life Insurance Co. Ltd.</option>
                                    </optgroup>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="lifePlanType">Plan Type *</label>
                                <select id="lifePlanType" name="planType" required>
                                    <option value="">Select Plan Type</option>
                                    <option value="Term Life">Term Life</option>
                                    <option value="Whole Life">Whole Life</option>
                                    <option value="Endowment">Endowment</option>
                                    <option value="Money Back">Money Back</option>
                                    <option value="ULIP">ULIP</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="lifeStartDate">Start Date *</label>
                                <input type="date" id="lifeStartDate" name="startDate" required>
                            </div>
                            <div class="form-group">
                                <label for="lifeEndDate">End Date *</label>
                                <input type="date" id="lifeEndDate" name="endDate" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="lifePremium">Premium Amount (₹) *</label>
                                <input type="number" id="lifePremium" name="premium" step="0.01" required>
                            </div>
                            <div class="form-group">
                                <label for="lifeCustomerPaid">Customer Paid (₹) *</label>
                                <input type="number" id="lifeCustomerPaid" name="customerPaidAmount" step="0.01" required>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="prevStep3">Previous</button>
                <button type="submit" form="policyForm" class="btn btn-primary">Submit Policy</button>
            </div>
        </div>
    </div>
</div>

@endsection
