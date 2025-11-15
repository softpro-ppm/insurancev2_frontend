<!-- Renew Policy Modal -->
<div class="modal" id="renewPolicyModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="renewPolicyModalTitle">
                <i class="fas fa-sync-alt"></i> Renew Policy
            </h2>
            <button class="modal-close" id="closeRenewPolicyModal">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="modal-body">
            <div class="renewal-notice" style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
                <p style="margin: 0; color: #92400e;">
                    <i class="fas fa-info-circle"></i> 
                    <strong>Renewal Mode:</strong> The current policy details will be saved as history. 
                    Update the dates, premium, and upload new documents for the renewal period.
                </p>
            </div>

            <form id="renewPolicyForm" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" id="renewPolicyId" name="policy_id">
                <input type="hidden" id="renewPolicyType" name="policy_type">
                <input type="hidden" id="renewBusinessType" name="business_type">
                
                <!-- Customer Information (Editable) -->
                <div class="form-section">
                    <h3><i class="fas fa-user"></i> Customer Information</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="renewCustomerName">Customer Name *</label>
                            <input type="text" id="renewCustomerName" name="customerName" required>
                        </div>
                        <div class="form-group">
                            <label for="renewCustomerPhone">Phone Number *</label>
                            <input type="tel" id="renewCustomerPhone" name="customerPhone" required minlength="10" maxlength="10" pattern="[0-9]{10}" title="Please enter exactly 10 digits">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="renewCustomerEmail">Email Address</label>
                        <input type="email" id="renewCustomerEmail" name="customerEmail">
                    </div>
                </div>

                <!-- Vehicle/Policy Information (Editable for Motor) -->
                <div class="form-section" id="renewVehicleSection" style="display: none;">
                    <h3><i class="fas fa-car"></i> Vehicle Information</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="renewVehicleNumber">Vehicle Number *</label>
                            <input type="text" id="renewVehicleNumber" name="vehicleNumber" required>
                        </div>
                        <div class="form-group">
                            <label for="renewVehicleType">Vehicle Type *</label>
                            <select id="renewVehicleType" name="vehicleType" required>
                                <option value="">Select Vehicle Type</option>
                                <option value="Auto (G)">Auto (G)</option>
                                <option value="Auto">Auto</option>
                                <option value="Bus">Bus</option>
                                <option value="Car (Taxi)">Car (Taxi)</option>
                                <option value="Car">Car</option>
                                <option value="E-Auto">E-Auto</option>
                                <option value="E-Car">E-Car</option>
                                <option value="HGV">HGV</option>
                                <option value="JCB">JCB</option>
                                <option value="LCV">LCV</option>
                                <option value="Others">Others</option>
                                <option value="Tractor">Tractor</option>
                                <option value="Trailer">Trailer</option>
                                <option value="2-Wheeler">2-Wheeler</option>
                                <option value="Van/Jeep">Van/Jeep</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Insurance Company (Editable - can switch company) -->
                <div class="form-section">
                    <h3><i class="fas fa-building"></i> Insurance Company</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="renewCompanyName">Company Name *</label>
                            <select id="renewCompanyName" name="companyName" required>
                                <option value="">Select Company</option>
                                <option value="The New India">The New India</option>
                                <option value="United India">United India</option>
                                <option value="National Insurance">National Insurance</option>
                                <option value="The Oriental">The Oriental</option>
                                <option value="ICICI Lombard">ICICI Lombard</option>
                                <option value="HDFC ERGO">HDFC ERGO</option>
                                <option value="Bajaj Allianz">Bajaj Allianz</option>
                                <option value="Tata AIG">Tata AIG</option>
                                <option value="Reliance General">Reliance General</option>
                                <option value="SBI General">SBI General</option>
                                <option value="IFFCO-Tokio">IFFCO-Tokio</option>
                                <option value="Royal Sundaram">Royal Sundaram</option>
                                <option value="Kotak Mahindra">Kotak Mahindra</option>
                                <option value="Chola MS">Chola MS</option>
                                <option value="Shriram General">Shriram General</option>
                                <option value="Universal Sompo">Universal Sompo</option>
                                <option value="Future Generali">Future Generali</option>
                                <option value="Magma HDI">Magma HDI</option>
                                <option value="Raheja QBE">Raheja QBE</option>
                                <option value="Go Digit">Go Digit</option>
                                <option value="ACKO">ACKO</option>
                                <option value="Zuno">Zuno</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="renewInsuranceType">Insurance Type *</label>
                            <select id="renewInsuranceType" name="insuranceType" required>
                                <option value="">Select Insurance Type</option>
                                <option value="Comprehensive">Comprehensive</option>
                                <option value="Stand Alone OD">Stand Alone OD</option>
                                <option value="Third Party">Third Party</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Renewal Dates (Editable) -->
                <div class="form-section">
                    <h3><i class="fas fa-calendar-alt"></i> Renewal Period</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="renewPolicyIssueDate">Policy Issue Date *</label>
                            <input type="date" id="renewPolicyIssueDate" name="policyIssueDate" required>
                            <small class="hint" style="color: #666; font-size: 12px;">Date when you issued the renewal</small>
                        </div>
                        <div class="form-group">
                            <label for="renewStartDate">Coverage Start Date *</label>
                            <input type="date" id="renewStartDate" name="startDate" required>
                            <small class="hint" style="color: #666; font-size: 12px;">When renewed coverage starts</small>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="renewEndDate">Coverage End Date *</label>
                            <input type="date" id="renewEndDate" name="endDate" required>
                            <small class="hint" style="color: #666; font-size: 12px;">When renewed coverage ends</small>
                        </div>
                        <div class="form-group">
                            <!-- Empty space for alignment -->
                        </div>
                    </div>
                </div>

                <!-- Financial Information (Editable) -->
                <div class="form-section">
                    <h3><i class="fas fa-rupee-sign"></i> Financial Details</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="renewPremium">Premium Amount (₹) *</label>
                            <input type="number" id="renewPremium" name="premium" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label for="renewPayout">Payout Amount (₹)</label>
                            <input type="number" id="renewPayout" name="payout" step="0.01">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="renewCustomerPaidAmount">Customer Paid (₹) *</label>
                            <input type="number" id="renewCustomerPaidAmount" name="customerPaidAmount" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label for="renewRevenue">Revenue (₹) *</label>
                            <input type="number" id="renewRevenue" name="revenue" step="0.01" required readonly>
                        </div>
                    </div>
                </div>

                <!-- Upload New Documents -->
                <div class="form-section">
                    <h3><i class="fas fa-file-upload"></i> Upload Renewal Documents</h3>
                    <small class="hint" style="display: block; margin-bottom: 15px; color: #666;">
                        Upload new documents for this renewal period. Previous documents will be preserved in history.
                    </small>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="renewPolicyCopy">Policy Copy *</label>
                            <input type="file" id="renewPolicyCopy" name="policyCopy" accept=".pdf,.jpg,.jpeg,.png" required>
                        </div>
                        <div class="form-group">
                            <label for="renewRcCopy">RC Copy</label>
                            <input type="file" id="renewRcCopy" name="rcCopy" accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="renewAadharCopy">Aadhar Copy</label>
                            <input type="file" id="renewAadharCopy" name="aadharCopy" accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                        <div class="form-group">
                            <label for="renewPanCopy">PAN Copy</label>
                            <input type="file" id="renewPanCopy" name="panCopy" accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" id="cancelRenewPolicy">Cancel</button>
            <button type="submit" form="renewPolicyForm" class="btn btn-primary" id="saveRenewalBtn">
                <i class="fas fa-sync-alt"></i> Save Renewal
            </button>
        </div>
    </div>
</div>

