@extends('layouts.insurance')

@section('title', 'Policies - Insurance Management System')

@section('content')
<div class="page active" id="policies">
    <div class="page-header">
        <h1>Policies</h1>
    </div>
    <div class="page-content">
        <!-- Policies Controls -->
        <div class="policies-controls">
            <div class="controls-left">
                <button class="add-policy-btn" id="addPolicyFromPoliciesBtn">
                    <i class="fas fa-plus"></i>
                    Add New Policy
                </button>
                <button class="bulk-upload-btn" id="bulkUploadBtn">
                    <i class="fas fa-upload"></i>
                    Bulk Upload
                </button>
                <button class="export-btn" id="exportPoliciesBtn">
                    <i class="fas fa-download"></i>
                    Export Data
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
            <div class="stat-card glass-effect">
                <div class="stat-icon active">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3>Active Policies</h3>
                    <p class="stat-value" id="activePoliciesCount">0</p>
                </div>
            </div>
            <div class="stat-card glass-effect">
                <div class="stat-icon expired">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-content">
                    <h3>Expired Policies</h3>
                    <p class="stat-value" id="expiredPoliciesCount">0</p>
                </div>
            </div>
            <div class="stat-card glass-effect">
                <div class="stat-icon pending">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <h3>Pending Renewals</h3>
                    <p class="stat-value" id="pendingRenewalsCount">0</p>
                </div>
            </div>
            <div class="stat-card glass-effect">
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
        <div class="data-table-container glass-effect">
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
                            <th data-sort="vehicleNumber">Vehicle Number <i class="fas fa-sort"></i></th>
                            <th data-sort="customerName">Customer Name <i class="fas fa-sort"></i></th>
                            <th data-sort="phone">Phone Number <i class="fas fa-sort"></i></th>
                            <th data-sort="vehicleType">Vehicle Type <i class="fas fa-sort"></i></th>
                            <th data-sort="endDate">End Date <i class="fas fa-sort"></i></th>
                            <th data-sort="premium">Premium <i class="fas fa-sort"></i></th>
                            <th data-sort="status">Status <i class="fas fa-sort"></i></th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="policiesPageTableBody">
                        <!-- Table data will be populated by JavaScript -->
                    </tbody>
                </table>
            </div>
            <div class="table-pagination">
                <div class="pagination-info">
                    Showing <span id="policiesStartRecord">1</span> to <span id="policiesEndRecord">10</span> of <span id="policiesTotalRecords">0</span> entries
                </div>
                <div class="pagination-controls">
                    <button class="pagination-btn" id="policiesPrevPage" disabled>
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <div class="page-numbers" id="policiesPageNumbers">
                        <!-- Page numbers will be generated by JavaScript -->
                    </div>
                    <button class="pagination-btn" id="policiesNextPage">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Upload Modal -->
<div class="modal" id="bulkUploadModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Bulk Upload Policies</h3>
            <button class="close-modal" id="closeBulkUploadModal">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="upload-instructions">
                <h4>Instructions:</h4>
                <ol>
                    <li>Download the template using the buttons below (Excel or CSV format)</li>
                    <li>Fill in your policy data following the template format</li>
                    <li>Save the file as .xlsx, .xls, or .csv format</li>
                    <li>Upload the file using the form below</li>
                    <li>Fields marked with * are required</li>
                    <li><strong>CSV format is recommended for better compatibility</strong></li>
                </ol>
                
                <div class="template-download">
                    <button type="button" class="secondary-button" id="downloadTemplateBtn">
                        <i class="fas fa-download"></i>
                        Download Excel Template
                    </button>
                    <button type="button" class="secondary-button" id="downloadCSVTemplateBtn">
                        <i class="fas fa-file-csv"></i>
                        Download CSV Template
                    </button>
                </div>
            </div>
            
            <form id="bulkUploadForm" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="excelFile">Select File:</label>
                    <input type="file" id="excelFile" name="excel_file" accept=".xlsx,.xls,.csv" required>
                    <small>Maximum file size: 10MB. Supported formats: .xlsx, .xls, .csv</small>
                </div>
                
                <!-- Preview Section -->
                <div class="preview-section" id="previewSection" style="display: none;">
                    <h4>File Preview</h4>
                    <div class="preview-stats">
                        <div class="stat-item">
                            <span class="stat-label">Total Rows:</span>
                            <span class="stat-value" id="totalRows">0</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Valid Rows:</span>
                            <span class="stat-value valid" id="validRows">0</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Invalid Rows:</span>
                            <span class="stat-value invalid" id="invalidRows">0</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Success Rate:</span>
                            <span class="stat-value" id="successRate">0%</span>
                        </div>
                    </div>
                    
                    <!-- Preview Table (Unified) -->
                    <div class="preview-tables">
                        <div class="table-section">
                            <h5>Rows</h5>
                            <div class="table-container">
                                <table class="preview-table" id="previewTable">
                                    <thead>
                                        <tr>
                                            <th>Row</th>
                                            <th>Policy Type</th>
                                            <th>Customer Name</th>
                                            <th>Phone</th>
                                            <th>Company</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="upload-progress" id="uploadProgress" style="display: none;">
                    <div class="progress-bar">
                        <div class="progress-fill" id="progressFill"></div>
                    </div>
                    <div class="progress-text" id="progressText">Uploading...</div>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="secondary-button" id="cancelBulkUpload">Cancel</button>
                    <button type="button" class="secondary-button" id="previewBtn" style="display: none;">Preview File</button>
                    <button type="submit" class="primary-button" id="submitBulkUpload">
                        <i class="fas fa-upload"></i>
                        Upload Policies
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modals are globally included in the layout -->

@push('scripts')
<script>
// Policies page is handled by main app.js
console.log('Policies page loaded - functionality handled by main app.js');
</script>
@endpush

@endsection
