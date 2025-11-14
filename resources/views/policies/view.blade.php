@extends('layouts.insurance')

@section('title', 'View Policy - Insurance Management System')

@section('content')
<div class="page active" id="viewPolicy">
    <div class="page-header">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <h1><i class="fas fa-file-alt"></i> Policy Details</h1>
                <p style="color: #666; margin: 5px 0 0 0;">Complete policy information and renewal history</p>
            </div>
            <div>
                <a href="{{ route('policies.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Policies
                </a>
            </div>
        </div>
    </div>

    <div class="page-content" id="policyViewContent">
        <!-- Loading State -->
        <div id="policyViewLoading" style="text-align: center; padding: 50px;">
            <i class="fas fa-spinner fa-spin" style="font-size: 48px; color: #3b82f6;"></i>
            <p style="margin-top: 20px; color: #666;">Loading policy details...</p>
        </div>

        <!-- Policy Content (Hidden initially) -->
        <div id="policyViewData" style="display: none;">
            <!-- Current Policy Section -->
            <div class="policy-card" style="background: white; border-radius: 8px; padding: 30px; margin-bottom: 30px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 25px;">
                    <div>
                        <h2 style="margin: 0 0 10px 0; color: #1f2937;">
                            <span class="policy-badge" id="currentPolicyType" style="background: #3b82f6; color: white; padding: 5px 12px; border-radius: 4px; font-size: 14px; margin-right: 10px;"></span>
                            <span id="currentCustomerName"></span>
                        </h2>
                        <p style="margin: 0; color: #666;">
                            <i class="fas fa-building"></i> <span id="currentCompanyName"></span> | 
                            <span id="currentInsuranceType"></span>
                        </p>
                    </div>
                    <div style="text-align: right;">
                        <span class="status-badge" id="currentStatus"></span>
                        <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;">
                            Policy ID: #<span id="currentPolicyId"></span>
                        </p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div style="margin-bottom: 25px; display: flex; gap: 10px; justify-content: flex-end;">
                    <button class="action-btn edit" id="editPolicyBtn" style="padding: 10px 20px; background: #3b82f6; color: white; border: none; border-radius: 6px; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-edit"></i> Edit Policy
                    </button>
                    <button class="action-btn renew" id="renewPolicyBtn" style="padding: 10px 20px; background: #F59E0B; color: white; border: none; border-radius: 6px; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-sync-alt"></i> Renew Policy
                    </button>
                    <button class="action-btn delete" id="deletePolicyBtn" style="padding: 10px 20px; background: #ef4444; color: white; border: none; border-radius: 6px; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-trash"></i> Delete Policy
                    </button>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 25px;">
                    <div class="info-box">
                        <label style="display: block; color: #666; font-size: 13px; margin-bottom: 5px;">
                            <i class="fas fa-calendar-check"></i> Policy Issue Date
                        </label>
                        <span id="currentPolicyIssueDate" style="font-weight: 600; color: #1f2937;"></span>
                    </div>
                    <div class="info-box">
                        <label style="display: block; color: #666; font-size: 13px; margin-bottom: 5px;">
                            <i class="fas fa-calendar-alt"></i> Coverage Period
                        </label>
                        <span id="currentCoveragePeriod" style="font-weight: 600; color: #1f2937;"></span>
                    </div>
                    <div class="info-box">
                        <label style="display: block; color: #666; font-size: 13px; margin-bottom: 5px;">
                            <i class="fas fa-rupee-sign"></i> Premium
                        </label>
                        <span id="currentPremium" style="font-weight: 600; color: #1f2937; font-size: 18px;"></span>
                    </div>
                    <div class="info-box">
                        <label style="display: block; color: #666; font-size: 13px; margin-bottom: 5px;">
                            <i class="fas fa-hand-holding-usd"></i> Payout
                        </label>
                        <span id="currentPayout" style="font-weight: 600; color: #f59e0b; font-size: 18px;"></span>
                    </div>
                    <div class="info-box">
                        <label style="display: block; color: #666; font-size: 13px; margin-bottom: 5px;">
                            <i class="fas fa-wallet"></i> Customer Paid
                        </label>
                        <span id="currentCustomerPaid" style="font-weight: 600; color: #8b5cf6; font-size: 18px;"></span>
                    </div>
                    <div class="info-box">
                        <label style="display: block; color: #666; font-size: 13px; margin-bottom: 5px;">
                            <i class="fas fa-chart-line"></i> Revenue
                        </label>
                        <span id="currentRevenue" style="font-weight: 600; color: #10b981; font-size: 18px;"></span>
                    </div>
                </div>

                <!-- Contact Information -->
                <div style="background: #f9fafb; padding: 15px; border-radius: 6px; margin-bottom: 25px;">
                    <h4 style="margin: 0 0 15px 0; color: #374151;">
                        <i class="fas fa-user"></i> Contact Information
                    </h4>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                        <div>
                            <label style="display: block; color: #666; font-size: 13px;">Phone</label>
                            <span id="currentPhone" style="color: #1f2937;"></span>
                        </div>
                        <div>
                            <label style="display: block; color: #666; font-size: 13px;">Email</label>
                            <span id="currentEmail" style="color: #1f2937;"></span>
                        </div>
                        <div>
                            <label style="display: block; color: #666; font-size: 13px;">Agent</label>
                            <span id="currentAgent" style="color: #1f2937;"></span>
                        </div>
                        <div id="currentVehicleInfo" style="display: none;">
                            <label style="display: block; color: #666; font-size: 13px;">Vehicle Number</label>
                            <span id="currentVehicleNumber" style="color: #1f2937;"></span>
                        </div>
                    </div>
                </div>

                <!-- Current Documents -->
                <div style="margin-bottom: 20px;">
                    <h4 style="margin: 0 0 15px 0; color: #374151;">
                        <i class="fas fa-file-pdf"></i> Current Documents
                    </h4>
                    <div id="currentDocuments" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px;">
                        <!-- Documents will be populated by JavaScript -->
                    </div>
                </div>
            </div>

            <!-- Policy History Section -->
            <div class="history-card" style="background: white; border-radius: 8px; padding: 30px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <h2 style="margin: 0 0 20px 0; color: #1f2937;">
                    <i class="fas fa-history"></i> Policy History
                </h2>
                
                <div id="policyHistoryTable">
                    <!-- History table will be populated by JavaScript -->
                </div>

                <div id="noHistoryMessage" style="display: none; text-align: center; padding: 40px; color: #666;">
                    <i class="fas fa-info-circle" style="font-size: 48px; color: #d1d5db; margin-bottom: 15px;"></i>
                    <p>No renewal history yet. This policy has not been renewed.</p>
                </div>
            </div>
        </div>

        <!-- Error State -->
        <div id="policyViewError" style="display: none; text-align: center; padding: 50px;">
            <i class="fas fa-exclamation-triangle" style="font-size: 48px; color: #ef4444; margin-bottom: 20px;"></i>
            <h3 style="color: #1f2937; margin-bottom: 10px;">Policy Not Found</h3>
            <p style="color: #666; margin-bottom: 20px;">The requested policy could not be found.</p>
            <a href="{{ route('policies.index') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Back to Policies
            </a>
        </div>
    </div>
</div>

<style>
.info-box {
    padding: 15px;
    background: #f9fafb;
    border-radius: 6px;
    border-left: 3px solid #3b82f6;
}

.policy-badge {
    display: inline-block;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 600;
}

.status-badge.active {
    background: #d1fae5;
    color: #065f46;
}

.status-badge.expired {
    background: #fee2e2;
    color: #991b1b;
}

.document-card {
    display: flex;
    align-items: center;
    padding: 12px 15px;
    background: #f9fafb;
    border-radius: 6px;
    border: 1px solid #e5e7eb;
    transition: all 0.2s;
    cursor: pointer;
}

.document-card:hover {
    background: #f3f4f6;
    border-color: #3b82f6;
}

.document-card i {
    font-size: 24px;
    color: #ef4444;
    margin-right: 12px;
}

.history-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.history-table th {
    background: #f9fafb;
    padding: 12px;
    text-align: left;
    font-weight: 600;
    color: #374151;
    border-bottom: 2px solid #e5e7eb;
}

.history-table td {
    padding: 15px 12px;
    border-bottom: 1px solid #e5e7eb;
    color: #1f2937;
}

.history-table tr:hover {
    background: #f9fafb;
}

.history-table tr:last-child td {
    border-bottom: none;
}

.download-doc-btn {
    padding: 6px 12px;
    background: #3b82f6;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 13px;
    transition: background 0.2s;
}

.download-doc-btn:hover {
    background: #2563eb;
}

.download-doc-btn i {
    margin-right: 5px;
}
</style>

@endsection

@push('scripts')
<script>
// Set global variable for app.js to pick up
window.viewPolicyId = {{ $id ?? 'null' }};
console.log('🔍 View Policy Page - Setting Policy ID:', window.viewPolicyId);
</script>
@endpush

