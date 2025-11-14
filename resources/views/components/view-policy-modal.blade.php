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
                        <div class="detail-item" id="viewCustomerAgeContainer">
                            <label>Age:</label>
                            <span id="viewCustomerAge">-</span>
                        </div>
                        <div class="detail-item" id="viewCustomerGenderContainer">
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
                        <div class="detail-item" id="viewPlanTypeContainer">
                            <label>Plan Type:</label>
                            <span id="viewPlanType">-</span>
                        </div>
                        <div class="detail-item" id="viewSumInsuredContainer">
                            <label>Sum Insured:</label>
                            <span id="viewSumInsured">-</span>
                        </div>
                        <div class="detail-item" id="viewSumAssuredContainer">
                            <label>Sum Assured:</label>
                            <span id="viewSumAssured">-</span>
                        </div>
                        <div class="detail-item" id="viewPolicyTermContainer">
                            <label>Policy Term:</label>
                            <span id="viewPolicyTerm">-</span>
                        </div>
                        <div class="detail-item" id="viewPremiumFrequencyContainer">
                            <label>Premium Frequency:</label>
                            <span id="viewPremiumFrequency">-</span>
                        </div>
                        <div class="detail-item">
                            <label>Policy Issue Date:</label>
                            <span id="viewPolicyIssueDate">-</span>
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
                    <div class="documents-container">
                        <div class="document-card">
                            <div class="document-header">
                                <div class="document-icon">
                                    <i class="fas fa-file-pdf"></i>
                                </div>
                                <div class="document-info">
                                    <h4>Policy Copy</h4>
                                    <p>Insurance policy document</p>
                                </div>
                                <div class="document-status" id="policyStatus">
                                    <span class="status-badge available">Available</span>
                                </div>
                            </div>
                            <div class="document-actions">
                                <button class="action-btn download-action" id="downloadPolicyBtn">
                                    <i class="fas fa-download"></i>
                                    <span>Download</span>
                                </button>
                                <button class="action-btn remove-action" id="removePolicyBtn">
                                    <i class="fas fa-trash"></i>
                                    <span>Remove</span>
                                </button>
                            </div>
                        </div>

                        <div class="document-card">
                            <div class="document-header">
                                <div class="document-icon">
                                    <i class="fas fa-file-pdf"></i>
                                </div>
                                <div class="document-info">
                                    <h4>RC Copy</h4>
                                    <p>Registration certificate</p>
                                </div>
                                <div class="document-status" id="rcStatus">
                                    <span class="status-badge not-available">Not Available</span>
                                </div>
                            </div>
                            <div class="document-actions">
                                <button class="action-btn download-action" id="downloadRcBtn" disabled>
                                    <i class="fas fa-download"></i>
                                    <span>Download</span>
                                </button>
                                <button class="action-btn remove-action" id="removeRcBtn" disabled>
                                    <i class="fas fa-trash"></i>
                                    <span>Remove</span>
                                </button>
                            </div>
                        </div>

                        <div class="document-card">
                            <div class="document-header">
                                <div class="document-icon">
                                    <i class="fas fa-file-pdf"></i>
                                </div>
                                <div class="document-info">
                                    <h4>Aadhar Copy</h4>
                                    <p>Identity verification document</p>
                                </div>
                                <div class="document-status" id="aadharStatus">
                                    <span class="status-badge not-available">Not Available</span>
                                </div>
                            </div>
                            <div class="document-actions">
                                <button class="action-btn download-action" id="downloadAadharBtn" disabled>
                                    <i class="fas fa-download"></i>
                                    <span>Download</span>
                                </button>
                                <button class="action-btn remove-action" id="removeAadharBtn" disabled>
                                    <i class="fas fa-trash"></i>
                                    <span>Remove</span>
                                </button>
                            </div>
                        </div>

                        <div class="document-card">
                            <div class="document-header">
                                <div class="document-icon">
                                    <i class="fas fa-file-pdf"></i>
                                </div>
                                <div class="document-info">
                                    <h4>PAN Copy</h4>
                                    <p>Tax identification document</p>
                                </div>
                                <div class="document-status" id="panStatus">
                                    <span class="status-badge not-available">Not Available</span>
                                </div>
                            </div>
                            <div class="document-actions">
                                <button class="action-btn download-action" id="downloadPanBtn" disabled>
                                    <i class="fas fa-download"></i>
                                    <span>Download</span>
                                </button>
                                <button class="action-btn remove-action" id="removePanBtn" disabled>
                                    <i class="fas fa-trash"></i>
                                    <span>Remove</span>
                                </button>
                            </div>
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