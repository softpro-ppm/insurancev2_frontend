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
                        <option value="Agent">Agent</option>
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
            <form id="policyForm" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="policy_type" id="hiddenPolicyType">
                <input type="hidden" name="business_type" id="hiddenBusinessType">
                
                <!-- Policy Overview Section -->
                <div class="form-section">
                    <h3><i class="fas fa-info-circle"></i> Policy Overview</h3>
                    <div class="form-group" id="agentNameGroup">
                        <label for="agentName">Agent Name</label>
                        <select id="agentName" name="agent_name">
                            <option value="">Select Agent</option>
                            <!-- Agent options will be populated dynamically -->
                        </select>
                    </div>
                </div>

                <!-- Motor Insurance Form -->
                <div class="policy-form" id="motorForm">
                    <!-- Vehicle Info Section -->
                    <div class="form-section">
                        <h3><i class="fas fa-car"></i> Vehicle Information</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="vehicleNumber">Vehicle Number *</label>
                                <input type="text" id="vehicleNumber" name="vehicleNumber" required maxlength="10" pattern="[A-Z0-9]{8,10}">
                            </div>
                            <div class="form-group">
                                <label for="vehicleType">Vehicle Type *</label>
                                <select id="vehicleType" name="vehicleType" required>
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
                                <input type="tel" id="customerPhone" name="customerPhone" required minlength="10" maxlength="10" pattern="[0-9]{10}" title="Please enter exactly 10 digits">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="customerEmail">Email Address</label>
                            <input type="email" id="customerEmail" name="customerEmail" 
                                   pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" 
                                   placeholder="Enter valid email address"
                                   onblur="validateEmail(this)">
                            <div class="validation-message" id="customerEmail-error"></div>
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
                                <label for="policyIssueDate">Policy Issue Date *</label>
                                <input type="date" id="policyIssueDate" name="policyIssueDate" required>
                                <small class="hint" style="color: #666; font-size: 12px;">Date when you issued this policy</small>
                            </div>
                            <div class="form-group">
                                <label for="startDate">Coverage Start Date *</label>
                                <input type="date" id="startDate" name="startDate" required>
                                <small class="hint" style="color: #666; font-size: 12px;">When insurance coverage starts</small>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="endDate">Coverage End Date *</label>
                                <input type="date" id="endDate" name="endDate" required>
                                <small class="hint" style="color: #666; font-size: 12px;">When insurance coverage ends</small>
                            </div>
                            <div class="form-group">
                                <!-- Empty space for alignment -->
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
                            </div>
                        </div>
                    </div>

                    <!-- Documents Section -->
                    <div class="form-section">
                        <h3><i class="fas fa-file-alt"></i> Documents</h3>
                        <small class="hint" style="display: block; margin-bottom: 15px; color: #666;">Maximum file size: 10MB per file. Supported formats: PDF, JPG, JPEG, PNG</small>
                        
                        <!-- Existing Documents -->
                        <div id="existingDocuments" style="display: none;">
                            <h4 style="margin-bottom: 10px; color: #374151;">Current Documents:</h4>
                            <div class="existing-docs-grid">
                                <div class="existing-doc-item" id="existingPolicyCopy" style="display: none;">
                                    <i class="fas fa-file-pdf"></i>
                                    <span>Policy Copy</span>
                                    <div class="doc-actions">
                                        <button type="button" class="btn-download-small" onclick="downloadExistingDocument('policy')" title="Download Policy Copy">
                                            <i class="fas fa-download"></i>
                                        </button>
                                        <button type="button" class="btn-remove-small" onclick="removeExistingDocument('policy')" title="Remove Policy Copy">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="existing-doc-item" id="existingRcCopy" style="display: none;">
                                    <i class="fas fa-file-pdf"></i>
                                    <span>RC Copy</span>
                                    <div class="doc-actions">
                                        <button type="button" class="btn-download-small" onclick="downloadExistingDocument('rc')" title="Download RC Copy">
                                            <i class="fas fa-download"></i>
                                        </button>
                                        <button type="button" class="btn-remove-small" onclick="removeExistingDocument('rc')" title="Remove RC Copy">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="existing-doc-item" id="existingAadharCopy" style="display: none;">
                                    <i class="fas fa-file-pdf"></i>
                                    <span>Aadhar Copy</span>
                                    <div class="doc-actions">
                                        <button type="button" class="btn-download-small" onclick="downloadExistingDocument('aadhar')" title="Download Aadhar Copy">
                                            <i class="fas fa-download"></i>
                                        </button>
                                        <button type="button" class="btn-remove-small" onclick="removeExistingDocument('aadhar')" title="Remove Aadhar Copy">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="existing-doc-item" id="existingPanCopy" style="display: none;">
                                    <i class="fas fa-file-pdf"></i>
                                    <span>PAN Copy</span>
                                    <div class="doc-actions">
                                        <button type="button" class="btn-download-small" onclick="downloadExistingDocument('pan')" title="Download PAN Copy">
                                            <i class="fas fa-download"></i>
                                        </button>
                                        <button type="button" class="btn-remove-small" onclick="removeExistingDocument('pan')" title="Remove PAN Copy">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <hr style="margin: 15px 0;">
                        </div>

                        <!-- Upload New Documents -->
                        <h4 style="margin-bottom: 10px; color: #374151;">Upload New Documents:</h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="policyCopy">Policy Copy *</label>
                                <input type="file" id="policyCopy" name="policyCopy" accept=".pdf,.jpg,.jpeg,.png" required multiple>
                                <small class="file-hint">Required - You can select multiple files</small>
                            </div>
                            <div class="form-group">
                                <label for="rcCopy">RC Copy</label>
                                <input type="file" id="rcCopy" name="rcCopy" accept=".pdf,.jpg,.jpeg,.png" multiple>
                                <small class="file-hint">You can select multiple files</small>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="aadharCopy">Aadhar Copy</label>
                                <input type="file" id="aadharCopy" name="aadharCopy" accept=".pdf,.jpg,.jpeg,.png" multiple>
                                <small class="file-hint">You can select multiple files</small>
                            </div>
                            <div class="form-group">
                                <label for="panCopy">PAN Copy</label>
                                <input type="file" id="panCopy" name="panCopy" accept=".pdf,.jpg,.jpeg,.png" multiple>
                                <small class="file-hint">You can select multiple files</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Health Insurance Form -->
                <div class="policy-form" id="healthForm">
                    <div class="form-section">
                        <h3><i class="fas fa-user"></i> Customer Information</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="healthCustomerName">Customer Name *</label>
                                <input type="text" id="healthCustomerName" name="customerName" required>
                            </div>
                            <div class="form-group">
                                <label for="healthCustomerPhone">Phone Number *</label>
                                <input type="tel" id="healthCustomerPhone" name="customerPhone" required minlength="10" maxlength="10" pattern="[0-9]{10}" title="Please enter exactly 10 digits">
                            </div>
                        </div>
                        <div class="form-row">
                                                    <div class="form-group">
                            <label for="healthCustomerEmail">Email Address</label>
                                <input type="email" id="healthCustomerEmail" name="customerEmail"
                                   pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" 
                                   placeholder="Enter valid email address"
                                   onblur="validateEmail(this)">
                            <div class="validation-message" id="healthCustomerEmail-error"></div>
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
                            <div class="form-group">
                                <label for="healthSumAssured">Sum Assured (₹)</label>
                                <input type="number" id="healthSumAssured" name="sumAssured" step="0.01">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="healthPolicyTerm">Policy Term (Years)</label>
                                <input type="text" id="healthPolicyTerm" name="policyTerm" placeholder="e.g., 1 Year, 2 Years">
                            </div>
                            <div class="form-group">
                                <label for="healthPremiumFrequency">Premium Frequency</label>
                                <select id="healthPremiumFrequency" name="premiumFrequency">
                                    <option value="">Select Frequency</option>
                                    <option value="Annual">Annual</option>
                                    <option value="Half Yearly">Half Yearly</option>
                                    <option value="Quarterly">Quarterly</option>
                                    <option value="Monthly">Monthly</option>
                                </select>
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
                                <select id="healthPlanType" name="insuranceType" required>
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
                                <label for="healthPolicyIssueDate">Policy Issue Date *</label>
                                <input type="date" id="healthPolicyIssueDate" name="policyIssueDate" required>
                                <small class="hint" style="color: #666; font-size: 12px;">Date when you issued this policy</small>
                            </div>
                            <div class="form-group">
                                <label for="healthStartDate">Coverage Start Date *</label>
                                <input type="date" id="healthStartDate" name="startDate" required>
                                <small class="hint" style="color: #666; font-size: 12px;">When insurance coverage starts</small>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="healthEndDate">Coverage End Date *</label>
                                <input type="date" id="healthEndDate" name="endDate" required>
                                <small class="hint" style="color: #666; font-size: 12px;">When insurance coverage ends</small>
                            </div>
                            <div class="form-group">
                                <!-- Empty space for alignment -->
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
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3><i class="fas fa-upload"></i> Documents</h3>
                        <small class="hint" style="display: block; margin-bottom: 15px; color: #666;">Maximum file size: 10MB per file. Supported formats: PDF, JPG, JPEG, PNG</small>
                        
                        <!-- Existing Documents -->
                        <div id="healthExistingDocuments" style="display: none;">
                            <h4 style="margin-bottom: 10px; color: #374151;">Current Documents:</h4>
                            <div class="existing-docs-grid">
                                <div class="existing-doc-item" id="healthExistingPolicyCopy" style="display: none;">
                                    <i class="fas fa-file-pdf"></i>
                                    <span>Policy Copy</span>
                                    <div class="doc-actions">
                                        <button type="button" class="btn-download-small" onclick="downloadExistingDocument('policy')" title="Download Policy Copy">
                                            <i class="fas fa-download"></i>
                                        </button>
                                        <button type="button" class="btn-remove-small" onclick="removeExistingDocument('policy')" title="Remove Policy Copy">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="existing-doc-item" id="healthExistingAadharCopy" style="display: none;">
                                    <i class="fas fa-file-pdf"></i>
                                    <span>Aadhar Copy</span>
                                    <div class="doc-actions">
                                        <button type="button" class="btn-download-small" onclick="downloadExistingDocument('aadhar')" title="Download Aadhar Copy">
                                            <i class="fas fa-download"></i>
                                        </button>
                                        <button type="button" class="btn-remove-small" onclick="removeExistingDocument('aadhar')" title="Remove Aadhar Copy">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="existing-doc-item" id="healthExistingPanCopy" style="display: none;">
                                    <i class="fas fa-file-pdf"></i>
                                    <span>PAN Copy</span>
                                    <div class="doc-actions">
                                        <button type="button" class="btn-download-small" onclick="downloadExistingDocument('pan')" title="Download PAN Copy">
                                            <i class="fas fa-download"></i>
                                        </button>
                                        <button type="button" class="btn-remove-small" onclick="removeExistingDocument('pan')" title="Remove PAN Copy">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <hr style="margin: 15px 0;">
                        </div>

                        <!-- Upload New Documents -->
                        <h4 style="margin-bottom: 10px; color: #374151;">Upload New Documents:</h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="healthPolicyCopy">Policy Copy *</label>
                                <input type="file" id="healthPolicyCopy" name="policyCopy" accept=".pdf,.jpg,.jpeg,.png" required>
                            </div>
                            <div class="form-group">
                                <label for="healthAadharCopy">Aadhar Copy</label>
                                <input type="file" id="healthAadharCopy" name="aadharCopy" accept=".pdf,.jpg,.jpeg,.png">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="healthPanCopy">PAN Copy</label>
                                <input type="file" id="healthPanCopy" name="panCopy" accept=".pdf,.jpg,.jpeg,.png">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Life Insurance Form -->
                <div class="policy-form" id="lifeForm">
                    <div class="form-section">
                        <h3><i class="fas fa-user"></i> Customer Information</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="lifeCustomerName">Customer Name *</label>
                                <input type="text" id="lifeCustomerName" name="customerName" required>
                            </div>
                            <div class="form-group">
                                <label for="lifeCustomerPhone">Phone Number *</label>
                                <input type="tel" id="lifeCustomerPhone" name="customerPhone" required minlength="10" maxlength="10" pattern="[0-9]{10}" title="Please enter exactly 10 digits">
                            </div>
                        </div>
                        <div class="form-row">
                                                    <div class="form-group">
                            <label for="lifeCustomerEmail">Email Address</label>
                                <input type="email" id="lifeCustomerEmail" name="customerEmail"
                                   pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" 
                                   placeholder="Enter valid email address"
                                   onblur="validateEmail(this)">
                            <div class="validation-message" id="lifeCustomerEmail-error"></div>
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
                                <select id="lifePlanType" name="insuranceType" required>
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
                                <label for="lifePolicyIssueDate">Policy Issue Date *</label>
                                <input type="date" id="lifePolicyIssueDate" name="policyIssueDate" required>
                                <small class="hint" style="color: #666; font-size: 12px;">Date when you issued this policy</small>
                            </div>
                            <div class="form-group">
                                <label for="lifeStartDate">Coverage Start Date *</label>
                                <input type="date" id="lifeStartDate" name="startDate" required>
                                <small class="hint" style="color: #666; font-size: 12px;">When insurance coverage starts</small>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="lifeEndDate">Coverage End Date *</label>
                                <input type="date" id="lifeEndDate" name="endDate" required>
                                <small class="hint" style="color: #666; font-size: 12px;">When insurance coverage ends</small>
                            </div>
                            <div class="form-group">
                                <!-- Empty space for alignment -->
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
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3><i class="fas fa-upload"></i> Documents</h3>
                        <small class="hint" style="display: block; margin-bottom: 15px; color: #666;">Maximum file size: 10MB per file. Supported formats: PDF, JPG, JPEG, PNG</small>
                        
                        <!-- Existing Documents -->
                        <div id="lifeExistingDocuments" style="display: none;">
                            <h4 style="margin-bottom: 10px; color: #374151;">Current Documents:</h4>
                            <div class="existing-docs-grid">
                                <div class="existing-doc-item" id="lifeExistingPolicyCopy" style="display: none;">
                                    <i class="fas fa-file-pdf"></i>
                                    <span>Policy Copy</span>
                                    <div class="doc-actions">
                                        <button type="button" class="btn-download-small" onclick="downloadExistingDocument('policy')" title="Download Policy Copy">
                                            <i class="fas fa-download"></i>
                                        </button>
                                        <button type="button" class="btn-remove-small" onclick="removeExistingDocument('policy')" title="Remove Policy Copy">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="existing-doc-item" id="lifeExistingAadharCopy" style="display: none;">
                                    <i class="fas fa-file-pdf"></i>
                                    <span>Aadhar Copy</span>
                                    <div class="doc-actions">
                                        <button type="button" class="btn-download-small" onclick="downloadExistingDocument('aadhar')" title="Download Aadhar Copy">
                                            <i class="fas fa-download"></i>
                                        </button>
                                        <button type="button" class="btn-remove-small" onclick="removeExistingDocument('aadhar')" title="Remove Aadhar Copy">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="existing-doc-item" id="lifeExistingPanCopy" style="display: none;">
                                    <i class="fas fa-file-pdf"></i>
                                    <span>PAN Copy</span>
                                    <div class="doc-actions">
                                        <button type="button" class="btn-download-small" onclick="downloadExistingDocument('pan')" title="Download PAN Copy">
                                            <i class="fas fa-download"></i>
                                        </button>
                                        <button type="button" class="btn-remove-small" onclick="removeExistingDocument('pan')" title="Remove PAN Copy">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <hr style="margin: 15px 0;">
                        </div>

                        <!-- Upload New Documents -->
                        <h4 style="margin-bottom: 10px; color: #374151;">Upload New Documents:</h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="lifePolicyCopy">Policy Copy *</label>
                                <input type="file" id="lifePolicyCopy" name="policyCopy" accept=".pdf,.jpg,.jpeg,.png" required>
                            </div>
                            <div class="form-group">
                                <label for="lifeAadharCopy">Aadhar Copy</label>
                                <input type="file" id="lifeAadharCopy" name="aadharCopy" accept=".pdf,.jpg,.jpeg,.png">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="lifePanCopy">PAN Copy</label>
                                <input type="file" id="lifePanCopy" name="panCopy" accept=".pdf,.jpg,.jpeg,.png">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="prevStep3">Previous</button>
                <button type="submit" form="policyForm" class="btn btn-primary" id="savePolicyBtn">Add Policy</button>
            </div>
        </div>
    </div>
</div> 