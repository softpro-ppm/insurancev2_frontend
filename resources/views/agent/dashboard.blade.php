@extends('layouts.agent')

@section('title', 'Agent Dashboard - Insurance Management System')

@section('content')
<div class="page active" id="agent-dashboard">
    <div class="page-header">
        <h1>Welcome, {{ Auth::guard('agent')->user()->name ?? 'Agent' }}</h1>
        <p class="text-gray-600">Agent Dashboard</p>
    </div>
    
    <div class="page-content">
        <!-- Agent Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card glass-effect">
                <div class="stat-icon">
                    <i class="fas fa-file-contract"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Policies ({{ $currentMonth }})</h3>
                    <p class="stat-value">{{ $totalPoliciesCurrentMonth }}</p>
                </div>
            </div>
            
            <div class="stat-card glass-effect">
                <div class="stat-icon">
                    <i class="fas fa-sync-alt"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Renewals ({{ $currentMonth }})</h3>
                    <p class="stat-value">{{ $totalRenewalsCurrentMonth }}</p>
                </div>
            </div>
            
            <div class="stat-card glass-effect">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Renewed ({{ $currentMonth }})</h3>
                    <p class="stat-value">{{ $totalRenewedCurrentMonth }}</p>
                </div>
            </div>
            
            <div class="stat-card glass-effect">
                <div class="stat-icon">
                    <i class="fas fa-rupee-sign"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Premium ({{ $currentMonth }})</h3>
                    <p class="stat-value">₹{{ number_format($totalPremiumCurrentMonth) }}</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <h2>Quick Actions</h2>
            <div class="action-buttons">
                <a href="{{ route('agent.policies') }}" class="action-btn">
                    <i class="fas fa-file-contract"></i>
                    <span>View Policies</span>
                </a>
                <a href="{{ route('agent.renewals') }}" class="action-btn">
                    <i class="fas fa-sync-alt"></i>
                    <span>View Renewals</span>
                </a>
                <a href="{{ route('agent.followups') }}" class="action-btn">
                    <i class="fas fa-bell"></i>
                    <span>View Follow-ups</span>
                </a>
            </div>
        </div>

        <!-- Current Month Policies -->
        <div class="recent-section">
            <h2>Current Month Policies ({{ $currentMonth }})</h2>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Serial No.</th>
                            <th>Customer Name</th>
                            <th>Vehicle Number</th>
                            <th>Policy Type</th>
                            <th>Insurance Company</th>
                            <th>Premium</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($currentMonthPolicies as $index => $policy)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $policy->customerName }}</td>
                            <td>{{ $policy->vehicleNumber }}</td>
                            <td>{{ $policy->policyType }}</td>
                            <td>{{ $policy->insuranceCompany }}</td>
                            <td>₹{{ number_format($policy->premium) }}</td>
                            <td>{{ \Carbon\Carbon::parse($policy->startDate)->format('d M Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($policy->endDate)->format('d M Y') }}</td>
                            <td>
                                <button class="btn btn-sm btn-primary view-policy-btn" 
                                        data-policy-id="{{ $policy->id }}"
                                        data-customer-name="{{ $policy->customerName }}"
                                        data-vehicle-number="{{ $policy->vehicleNumber }}"
                                        data-policy-type="{{ $policy->policyType }}"
                                        data-insurance-company="{{ $policy->insuranceCompany }}"
                                        data-premium="{{ $policy->premium }}"
                                        data-revenue="{{ $policy->revenue }}"
                                        data-status="{{ $policy->status }}"
                                        data-start-date="{{ $policy->startDate }}"
                                        data-end-date="{{ $policy->endDate }}"
                                        data-chassis="{{ $policy->chassis }}"
                                        data-vehicle-type="{{ $policy->vehicleType }}"
                                        data-fc-expiry="{{ $policy->fcExpiryDate }}"
                                        data-permit-expiry="{{ $policy->permitExpiryDate }}"
                                        data-phone="{{ $policy->phone }}">
                                    <i class="fas fa-eye"></i> View
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">No policies found for current month</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- View Policy Details Modal -->
<div class="modal" id="viewPolicyModal">
    <div class="modal-content modal-large">
        <div class="modal-header">
            <h2>Policy Details</h2>
            <button class="modal-close" id="closeViewPolicyModal">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="policy-details-container">
                <!-- Policy Overview -->
                <div class="policy-overview">
                    <div class="policy-header">
                        <div class="policy-badge">
                            <span class="policy-type-badge" id="viewPolicyType">Motor</span>
                            <span class="status-badge" id="viewPolicyStatus">Active</span>
                        </div>
                        <div class="policy-id">
                            <strong>Policy ID:</strong> <span id="viewPolicyId">#001</span>
                        </div>
                    </div>
                </div>

                <!-- Customer Information -->
                <div class="detail-section">
                    <h3><i class="fas fa-user"></i> Customer Information</h3>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <label>Customer Name:</label>
                            <span id="viewCustomerName">-</span>
                        </div>
                        <div class="detail-item">
                            <label>Phone Number:</label>
                            <span id="viewCustomerPhone">-</span>
                        </div>
                        <div class="detail-item">
                            <label>Email Address:</label>
                            <span id="viewCustomerEmail">-</span>
                        </div>
                    </div>
                </div>

                <!-- Vehicle Information (Motor only) -->
                <div class="detail-section" id="viewVehicleSection">
                    <h3><i class="fas fa-car"></i> Vehicle Information</h3>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <label>Vehicle Number:</label>
                            <span id="viewVehicleNumber">-</span>
                        </div>
                        <div class="detail-item">
                            <label>Vehicle Type:</label>
                            <span id="viewVehicleType">-</span>
                        </div>
                        <div class="detail-item">
                            <label>Chassis Number:</label>
                            <span id="viewChassisNumber">-</span>
                        </div>
                    </div>
                </div>

                <!-- Business Information -->
                <div class="detail-section">
                    <h3><i class="fas fa-briefcase"></i> Business Information</h3>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <label>BUSINESS TYPE:</label>
                            <span id="viewBusinessType" class="business-info-value">Motor</span>
                        </div>
                        <div class="detail-item">
                            <label>AGENT NAME:</label>
                            <span id="viewAgentName" class="business-info-value">-</span>
                        </div>
                    </div>
                </div>

                <!-- Insurance Information -->
                <div class="detail-section">
                    <h3><i class="fas fa-shield-alt"></i> Insurance Information</h3>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <label>Insurance Company:</label>
                            <span id="viewCompanyName">-</span>
                        </div>
                        <div class="detail-item">
                            <label>Insurance Type:</label>
                            <span id="viewInsuranceType">-</span>
                        </div>
                        <div class="detail-item">
                            <label>Start Date:</label>
                            <span id="viewStartDate">-</span>
                        </div>
                        <div class="detail-item">
                            <label>End Date:</label>
                            <span id="viewEndDate">-</span>
                        </div>
                    </div>
                </div>

                <!-- Financial Information -->
                <div class="detail-section">
                    <h3><i class="fas fa-rupee-sign"></i> Financial Information</h3>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <label>Premium Amount:</label>
                            <span id="viewPremium">-</span>
                        </div>
                        <div class="detail-item">
                            <label>Customer Paid:</label>
                            <span id="viewCustomerPaid">-</span>
                        </div>
                        <div class="detail-item">
                            <label>Revenue:</label>
                            <span id="viewRevenue">-</span>
                        </div>
                        <div class="detail-item">
                            <label>Payout:</label>
                            <span id="viewPayout">-</span>
                        </div>
                    </div>
                </div>

                <!-- Documents Section -->
                <div class="detail-section">
                    <h3><i class="fas fa-file-alt"></i> Documents</h3>
                    <div class="documents-grid">
                        <div class="document-item">
                            <i class="fas fa-file-pdf"></i>
                            <span>Policy Copy</span>
                            <button class="download-btn" onclick="showAdminContactWarning()">
                                <i class="fas fa-download"></i> Download
                            </button>
                        </div>
                        <div class="document-item">
                            <i class="fas fa-file-pdf"></i>
                            <span>RC Copy</span>
                            <button class="download-btn" onclick="showAdminContactWarning()">
                                <i class="fas fa-download"></i> Download
                            </button>
                        </div>
                        <div class="document-item">
                            <i class="fas fa-file-pdf"></i>
                            <span>Aadhar Copy</span>
                            <button class="download-btn" onclick="showAdminContactWarning()">
                                <i class="fas fa-download"></i> Download
                            </button>
                        </div>
                        <div class="document-item">
                            <i class="fas fa-file-pdf"></i>
                            <span>PAN Copy</span>
                            <button class="download-btn" onclick="showAdminContactWarning()">
                                <i class="fas fa-download"></i> Download
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" id="closeViewPolicyBtn">Close</button>
        </div>
    </div>
</div>

<style>
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    padding: 1.5rem;
    border-radius: 12px;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: transform 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.stat-content h3 {
    margin: 0 0 0.5rem 0;
    color: #6b7280;
    font-size: 0.875rem;
    font-weight: 600;
}

.stat-value {
    margin: 0;
    font-size: 1.875rem;
    font-weight: 700;
    color: #111827;
}

.quick-actions {
    margin-bottom: 2rem;
}

.quick-actions h2 {
    margin-bottom: 1rem;
    color: #374151;
    font-size: 1.25rem;
    font-weight: 700;
}

.action-buttons {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.action-btn {
    padding: 1rem;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    text-decoration: none;
    color: #374151;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}

.action-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
}

.action-btn i {
    font-size: 1.25rem;
    color: #667eea;
}

.recent-section h2 {
    margin-bottom: 1rem;
    color: #374151;
    font-size: 1.25rem;
    font-weight: 700;
}

.table-container {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    padding: 1.5rem;
    backdrop-filter: blur(10px);
    overflow-x: auto;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 8px;
    overflow: hidden;
}

.data-table th {
    background: #f9fafb;
    padding: 1rem;
    text-align: left;
    font-weight: 600;
    color: #374151;
    border-bottom: 1px solid #e5e7eb;
}

.data-table td {
    padding: 1rem;
    border-bottom: 1px solid #e5e7eb;
    color: #374151;
}

.data-table tr:hover {
    background: #f9fafb;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-badge.active {
    background: #d1fae5;
    color: #065f46;
}

.status-badge.expired {
    background: #fee2e2;
    color: #991b1b;
}

.status-badge.pending {
    background: #fef3c7;
    color: #92400e;
}

.text-center {
    text-align: center;
}

.btn {
    padding: 0.375rem 0.75rem;
    border-radius: 0.375rem;
    border: 1px solid transparent;
    font-size: 0.875rem;
    font-weight: 500;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.15s ease-in-out;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

.btn-primary {
    color: #fff;
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.btn-primary:hover {
    color: #fff;
    background-color: #0b5ed7;
    border-color: #0a58ca;
}

.btn-warning {
    color: #000;
    background-color: #ffc107;
    border-color: #ffc107;
}

.btn-warning:hover {
    color: #000;
    background-color: #ffca2c;
    border-color: #ffc720;
}

.btn-secondary {
    color: #fff;
    background-color: #6c757d;
    border-color: #6c757d;
}

.btn-secondary:hover {
    color: #fff;
    background-color: #5c636a;
    border-color: #565e64;
}

.btn-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
}

.modal {
    display: none;
    position: fixed;
    z-index: 1055;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
}

.modal.show {
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-dialog {
    max-width: 800px;
    margin: 1.75rem;
}

.modal-content {
    background-color: #fff;
    border-radius: 0.5rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.modal-header {
    padding: 1rem;
    border-bottom: 1px solid #dee2e6;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.modal-body {
    padding: 1rem;
}

.modal-footer {
    padding: 1rem;
    border-top: 1px solid #dee2e6;
    display: flex;
    gap: 0.5rem;
    justify-content: flex-end;
}

.form-label {
    margin-bottom: 0.5rem;
    font-weight: 600;
}

.form-control-plaintext {
    margin-bottom: 0;
    padding: 0.375rem 0;
    line-height: 1.5;
    color: #212529;
}

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1055;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
}

.modal-content {
    background-color: #fff;
    border-radius: 0.5rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    max-width: 900px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
    margin: auto;
    margin-top: 5vh;
}

.modal-large {
    max-width: 1000px;
}

.modal-header {
    padding: 1.5rem;
    border-bottom: 1px solid #dee2e6;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #f8f9fa;
    border-radius: 0.5rem 0.5rem 0 0;
}

.modal-header h2 {
    margin: 0;
    color: #495057;
    font-size: 1.5rem;
    font-weight: 600;
}

.modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: #6c757d;
    padding: 0.25rem;
    border-radius: 0.25rem;
    transition: all 0.2s;
}

.modal-close:hover {
    color: #495057;
    background: #e9ecef;
}

.modal-body {
    padding: 1.5rem;
}

.modal-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid #dee2e6;
    display: flex;
    gap: 0.5rem;
    justify-content: flex-end;
    background: #f8f9fa;
    border-radius: 0 0 0.5rem 0.5rem;
}

/* Policy Details Styles */
.policy-details-container {
    max-width: 100%;
}

.policy-overview {
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 0.75rem;
    color: white;
}

.policy-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.policy-badge {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.policy-type-badge {
    background: rgba(255, 255, 255, 0.2);
    padding: 0.5rem 1rem;
    border-radius: 2rem;
    font-weight: 600;
    font-size: 0.875rem;
}

.policy-id {
    font-size: 1.125rem;
}

.detail-section {
    margin-bottom: 2rem;
    background: #f8f9fa;
    border-radius: 0.5rem;
    padding: 1.5rem;
}

.detail-section h3 {
    margin: 0 0 1rem 0;
    color: #495057;
    font-size: 1.125rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.detail-section h3 i {
    color: #667eea;
}

.detail-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1rem;
}

.detail-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.detail-item label {
    font-weight: 600;
    color: #6c757d;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.detail-item span {
    color: #495057;
    font-size: 1rem;
    font-weight: 500;
}

.business-info-value {
    color: #667eea !important;
    font-weight: 600 !important;
}

.documents-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
}

.document-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    background: white;
    border-radius: 0.5rem;
    border: 1px solid #dee2e6;
    transition: all 0.2s;
}

.document-item:hover {
    border-color: #667eea;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.1);
}

.document-item i {
    color: #dc3545;
    font-size: 1.25rem;
}

.document-item span {
    flex: 1;
    font-weight: 500;
    color: #495057;
}

.download-btn {
    background: #ffc107;
    color: #000;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 0.25rem;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.download-btn:hover {
    background: #ffca2c;
    transform: translateY(-1px);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // View Policy Modal functionality
    const viewPolicyButtons = document.querySelectorAll('.view-policy-btn');
    const viewPolicyModal = document.getElementById('viewPolicyModal');
    
    viewPolicyButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Get policy data from data attributes
            const policyId = this.getAttribute('data-policy-id');
            const customerName = this.getAttribute('data-customer-name');
            const vehicleNumber = this.getAttribute('data-vehicle-number');
            const policyType = this.getAttribute('data-policy-type');
            const insuranceCompany = this.getAttribute('data-insurance-company');
            const premium = this.getAttribute('data-premium');
            const revenue = this.getAttribute('data-revenue');
            const status = this.getAttribute('data-status');
            const startDate = this.getAttribute('data-start-date');
            const endDate = this.getAttribute('data-end-date');
            const chassis = this.getAttribute('data-chassis');
            const vehicleType = this.getAttribute('data-vehicle-type');
            const phone = this.getAttribute('data-phone');
            
            // Populate modal with data
            document.getElementById('viewPolicyId').textContent = '#' + String(policyId).padStart(3, '0');
            document.getElementById('viewCustomerName').textContent = customerName || '-';
            document.getElementById('viewCustomerPhone').textContent = phone || '-';
            document.getElementById('viewCustomerEmail').textContent = '-';
            document.getElementById('viewVehicleNumber').textContent = vehicleNumber || '-';
            document.getElementById('viewVehicleType').textContent = vehicleType || '-';
            document.getElementById('viewChassisNumber').textContent = chassis || '-';
            document.getElementById('viewAgentName').textContent = '{{ Auth::guard("agent")->user()->name ?? "Unknown Agent" }}';
            document.getElementById('viewCompanyName').textContent = insuranceCompany || '-';
            document.getElementById('viewInsuranceType').textContent = policyType || '-';
            document.getElementById('viewStartDate').textContent = startDate ? new Date(startDate).toLocaleDateString('en-GB') : '-';
            document.getElementById('viewEndDate').textContent = endDate ? new Date(endDate).toLocaleDateString('en-GB') : '-';
            document.getElementById('viewPremium').textContent = premium ? '₹' + parseFloat(premium).toLocaleString() : '-';
            document.getElementById('viewCustomerPaid').textContent = premium ? '₹' + parseFloat(premium).toLocaleString() : '-';
            document.getElementById('viewRevenue').textContent = revenue ? '₹' + parseFloat(revenue).toLocaleString() : '-';
            document.getElementById('viewPayout').textContent = '-';
            
            // Set status badge
            const statusElement = document.getElementById('viewPolicyStatus');
            statusElement.textContent = status || 'Active';
            statusElement.className = 'status-badge ' + (status ? status.toLowerCase() : 'active');
            
            // Show modal
            viewPolicyModal.style.display = 'flex';
        });
    });
    
    // Close modal functionality
    const closeButtons = document.querySelectorAll('#closeViewPolicyModal, #closeViewPolicyBtn');
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            viewPolicyModal.style.display = 'none';
        });
    });
    
    // Close modal when clicking outside
    viewPolicyModal.addEventListener('click', function(e) {
        if (e.target === viewPolicyModal) {
            viewPolicyModal.style.display = 'none';
        }
    });
});

// Function to show admin contact warning
function showAdminContactWarning() {
    alert('Contact admin to download policy documents.');
}
</script>
@endsection
