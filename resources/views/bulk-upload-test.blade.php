@extends('layouts.insurance')

@section('title', 'Bulk Upload Test - Insurance Management System')

@section('content')
<div class="page active" id="bulkUploadTest">
    <div class="page-header">
        <h1>Bulk Upload Test</h1>
        <p>Testing bulk upload functionality</p>
    </div>
    <div class="page-content">
        <div class="test-controls">
            <button class="bulk-upload-btn" id="bulkUploadBtn">
                <i class="fas fa-upload"></i>
                Test Bulk Upload
            </button>
            <button class="secondary-button" id="downloadTemplateBtn">
                <i class="fas fa-download"></i>
                Download Template
            </button>
            <button class="secondary-button" id="downloadCSVTemplateBtn">
                <i class="fas fa-file-csv"></i>
                Download CSV Template
            </button>
        </div>
        
        <div id="testResults" style="margin-top: 20px; padding: 20px; background: #f5f5f5; border-radius: 8px;">
            <h3>Test Results:</h3>
            <div id="testOutput"></div>
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
                    <label for="excelFile">Select Excel File:</label>
                    <input type="file" id="excelFile" name="excel_file" accept=".xlsx,.xls" required>
                    <small>Maximum file size: 10MB. Supported formats: .xlsx, .xls</small>
                </div>
                
                <div class="upload-progress" id="uploadProgress" style="display: none;">
                    <div class="progress-bar">
                        <div class="progress-fill" id="progressFill"></div>
                    </div>
                    <div class="progress-text" id="progressText">Uploading...</div>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="secondary-button" id="cancelBulkUpload">Cancel</button>
                    <button type="submit" class="primary-button" id="submitBulkUpload">
                        <i class="fas fa-upload"></i>
                        Upload Policies
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Bulk upload test page loaded');
    
    // Test functions
    function logTest(message) {
        console.log(message);
        const output = document.getElementById('testOutput');
        if (output) {
            output.innerHTML += '<p>' + message + '</p>';
        }
    }
    
    // Test bulk upload initialization
    logTest('Testing bulk upload initialization...');
    
    const bulkUploadBtn = document.getElementById('bulkUploadBtn');
    const modal = document.getElementById('bulkUploadModal');
    const downloadTemplateBtn = document.getElementById('downloadTemplateBtn');
    const downloadCSVTemplateBtn = document.getElementById('downloadCSVTemplateBtn');
    
    logTest('Bulk upload button found: ' + !!bulkUploadBtn);
    logTest('Modal found: ' + !!modal);
    logTest('Download template button found: ' + !!downloadTemplateBtn);
    logTest('Download CSV template button found: ' + !!downloadCSVTemplateBtn);
    
    // Test modal opening
    if (bulkUploadBtn) {
        bulkUploadBtn.addEventListener('click', function() {
            logTest('Bulk upload button clicked');
            if (modal) {
                modal.classList.add('show');
                logTest('Modal opened successfully');
            } else {
                logTest('ERROR: Modal not found');
            }
        });
    }
    
    // Test modal closing
    const closeBtn = document.getElementById('closeBulkUploadModal');
    if (closeBtn) {
        closeBtn.addEventListener('click', function() {
            logTest('Close button clicked');
            if (modal) {
                modal.classList.remove('show');
                logTest('Modal closed successfully');
            }
        });
    }
    
    // Test template downloads
    if (downloadTemplateBtn) {
        downloadTemplateBtn.addEventListener('click', function() {
            logTest('Download template button clicked');
            const link = document.createElement('a');
            link.href = '/api/policies/template/download';
            link.download = 'policies_template.xlsx';
            link.style.display = 'none';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            logTest('Template download initiated');
        });
    }
    
    if (downloadCSVTemplateBtn) {
        downloadCSVTemplateBtn.addEventListener('click', function() {
            logTest('Download CSV template button clicked');
            const link = document.createElement('a');
            link.href = '/api/policies/template/download-csv';
            link.download = 'policies_template.csv';
            link.style.display = 'none';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            logTest('CSV template download initiated');
        });
    }
    
    logTest('Bulk upload test setup complete');
});
</script>
@endpush

@endsection
