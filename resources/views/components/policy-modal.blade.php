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
            <form id="policyForm" method="POST" action="{{ route('policies.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="policy_type" id="hiddenPolicyType">
                <input type="hidden" name="business_type" id="hiddenBusinessType">
                
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
                                <input type="number" id="revenue" name="revenue" step="0.01" required readonly>
                                <small class="hint">Auto-calculated: Customer Paid − (Premium − Payout)</small>
                            </div>
                        </div>
                    </div>

                    <!-- Upload Section -->
                    <div class="form-section">
                        <h3><i class="fas fa-upload"></i> Documents</h3>
                        <small class="hint" style="display: block; margin-bottom: 15px; color: #666;">Maximum file size: 3MB per file. Supported formats: PDF, JPG, JPEG, PNG</small>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="policyCopy">Policy Copy</label>
                                <input type="file" id="policyCopy" name="policyCopy" accept=".pdf,.jpg,.jpeg,.png">
                                <small class="hint">Max 3MB</small>
                            </div>
                            <div class="form-group">
                                <label for="rcCopy">RC Copy</label>
                                <input type="file" id="rcCopy" name="rcCopy" accept=".pdf,.jpg,.jpeg,.png">
                                <small class="hint">Max 3MB</small>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="aadharCopy">Aadhar Copy</label>
                                <input type="file" id="aadharCopy" name="aadharCopy" accept=".pdf,.jpg,.jpeg,.png">
                                <small class="hint">Max 3MB</small>
                            </div>
                            <div class="form-group">
                                <label for="panCopy">PAN Copy</label>
                                <input type="file" id="panCopy" name="panCopy" accept=".pdf,.jpg,.jpeg,.png">
                                <small class="hint">Max 3MB</small>
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
                                <label for="healthPayout">Payout Amount (₹)</label>
                                <input type="number" id="healthPayout" name="payout" step="0.01">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="healthCustomerPaid">Customer Paid (₹) *</label>
                                <input type="number" id="healthCustomerPaid" name="customerPaidAmount" step="0.01" required>
                            </div>
                            <div class="form-group">
                                <label for="healthRevenue">Revenue (₹) *</label>
                                <input type="number" id="healthRevenue" name="revenue" step="0.01" required readonly>
                                <small class="hint">Auto-calculated: Customer Paid − (Premium − Payout)</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3><i class="fas fa-upload"></i> Documents</h3>
                        <small class="hint" style="display: block; margin-bottom: 15px; color: #666;">Maximum file size: 3MB per file. Supported formats: PDF, JPG, JPEG, PNG</small>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="healthPolicyCopy">Policy Copy</label>
                                <input type="file" id="healthPolicyCopy" name="policyCopy" accept=".pdf,.jpg,.jpeg,.png">
                                <small class="hint">Max 3MB</small>
                            </div>
                            <div class="form-group">
                                <label for="healthAadharCopy">Aadhar Copy</label>
                                <input type="file" id="healthAadharCopy" name="aadharCopy" accept=".pdf,.jpg,.jpeg,.png">
                                <small class="hint">Max 3MB</small>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="healthPanCopy">PAN Copy</label>
                                <input type="file" id="healthPanCopy" name="panCopy" accept=".pdf,.jpg,.jpeg,.png">
                                <small class="hint">Max 3MB</small>
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
                                <input type="number" id="lifeCustomerAge" name="customerAge" min="1" max="120" required>
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
                        <div class="form-row">
                            <div class="form-group">
                                <label for="lifePolicyTerm">Policy Term (Years) *</label>
                                <input type="number" id="lifePolicyTerm" name="policyTerm" min="1" max="50" required>
                            </div>
                            <div class="form-group">
                                <label for="lifePremiumFrequency">Premium Frequency *</label>
                                <select id="lifePremiumFrequency" name="premiumFrequency" required>
                                    <option value="">Select Frequency</option>
                                    <option value="Monthly">Monthly</option>
                                    <option value="Quarterly">Quarterly</option>
                                    <option value="Half Yearly">Half Yearly</option>
                                    <option value="Yearly">Yearly</option>
                                </select>
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
                                        <option value="PNB MetLife India Insurance Co. Ltd.">PNB MetLife India Insurance Co. Ltd.</option>
                                        <option value="Tata AIA Life Insurance Co. Ltd.">Tata AIA Life Insurance Co. Ltd.</option>
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
                                <label for="lifePayout">Payout Amount (₹)</label>
                                <input type="number" id="lifePayout" name="payout" step="0.01">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="lifeCustomerPaid">Customer Paid (₹) *</label>
                                <input type="number" id="lifeCustomerPaid" name="customerPaidAmount" step="0.01" required>
                            </div>
                            <div class="form-group">
                                <label for="lifeRevenue">Revenue (₹) *</label>
                                <input type="number" id="lifeRevenue" name="revenue" step="0.01" required readonly>
                                <small class="hint">Auto-calculated: Customer Paid − (Premium − Payout)</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3><i class="fas fa-upload"></i> Documents</h3>
                        <small class="hint" style="display: block; margin-bottom: 15px; color: #666;">Maximum file size: 3MB per file. Supported formats: PDF, JPG, JPEG, PNG</small>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="lifePolicyCopy">Policy Copy</label>
                                <input type="file" id="lifePolicyCopy" name="policyCopy" accept=".pdf,.jpg,.jpeg,.png">
                                <small class="hint">Max 3MB</small>
                            </div>
                            <div class="form-group">
                                <label for="lifeAadharCopy">Aadhar Copy</label>
                                <input type="file" id="lifeAadharCopy" name="aadharCopy" accept=".pdf,.jpg,.jpeg,.png">
                                <small class="hint">Max 3MB</small>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="lifePanCopy">PAN Copy</label>
                                <input type="file" id="lifePanCopy" name="panCopy" accept=".pdf,.jpg,.jpeg,.png">
                                <small class="hint">Max 3MB</small>
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