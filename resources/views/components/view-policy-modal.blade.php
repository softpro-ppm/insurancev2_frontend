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
                        <div class="detail-item" id="viewCustomerAgeContainer" style="display: none;">
                            <label>Age:</label>
                            <span id="viewCustomerAge">-</span>
                        </div>
                        <div class="detail-item" id="viewCustomerGenderContainer" style="display: none;">
                            <label>Gender:</label>
                            <span id="viewCustomerGender">-</span>
                        </div>
                    </div>
                </div>

                <!-- Vehicle Information (Motor only) -->
                <div class="detail-section" id="viewVehicleSection" style="display: none;">
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
                    </div>
                </div>

                <!-- Business Information -->
                <div class="detail-section">
                    <h3><i class="fas fa-briefcase"></i> Business Information</h3>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <label>BUSINESS TYPE:</label>
                            <span id="viewBusinessType" class="business-info-value">-</span>
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
                        <div class="detail-item" id="viewInsuranceTypeContainer">
                            <label>Insurance Type:</label>
                            <span id="viewInsuranceType">-</span>
                        </div>
                        <div class="detail-item" id="viewPlanTypeContainer" style="display: none;">
                            <label>Plan Type:</label>
                            <span id="viewPlanType">-</span>
                        </div>
                        <div class="detail-item" id="viewSumInsuredContainer" style="display: none;">
                            <label>Sum Insured:</label>
                            <span id="viewSumInsured">-</span>
                        </div>
                        <div class="detail-item" id="viewSumAssuredContainer" style="display: none;">
                            <label>Sum Assured:</label>
                            <span id="viewSumAssured">-</span>
                        </div>
                        <div class="detail-item" id="viewPolicyTermContainer" style="display: none;">
                            <label>Policy Term:</label>
                            <span id="viewPolicyTerm">-</span>
                        </div>
                        <div class="detail-item" id="viewPremiumFrequencyContainer" style="display: none;">
                            <label>Premium Frequency:</label>
                            <span id="viewPremiumFrequency">-</span>
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
                            <button class="download-btn" onclick="downloadDocument('policy')" id="downloadPolicyBtn">
                                <i class="fas fa-download"></i> Download
                            </button>
                        </div>
                        <div class="document-item" id="rcCopyItem">
                            <i class="fas fa-file-pdf"></i>
                            <span>RC Copy</span>
                            <button class="download-btn" onclick="downloadDocument('rc')" id="downloadRcBtn">
                                <i class="fas fa-download"></i> Download
                            </button>
                        </div>
                        <div class="document-item">
                            <i class="fas fa-file-pdf"></i>
                            <span>Aadhar Copy</span>
                            <button class="download-btn" onclick="downloadDocument('aadhar')" id="downloadAadharBtn">
                                <i class="fas fa-download"></i> Download
                            </button>
                        </div>
                        <div class="document-item">
                            <i class="fas fa-file-pdf"></i>
                            <span>PAN Copy</span>
                            <button class="download-btn" onclick="downloadDocument('pan')" id="downloadPanBtn">
                                <i class="fas fa-download"></i> Download
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" id="closeViewPolicyBtn">Close</button>
            <button type="button" class="btn btn-primary" id="editPolicyFromViewBtn">Edit Policy</button>
        </div>
    </div>
</div> 