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

                <!-- Insurance Information -->
                <div class="detail-section">
                    <h3><i class="fas fa-shield-alt"></i> Insurance Information</h3>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <label>Insurance Company:</label>
                            <span id="viewCompanyName">-</span>
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