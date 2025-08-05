@extends('layouts.insurance')

@section('title', 'Policies - Insurance Management System')

@section('content')
<div class="page active" id="policies">
    <div class="page-header">
        <h1>Policies</h1>
        <p>Manage all insurance policies</p>
    </div>
    <div class="page-content">
        <!-- Policies Controls -->
        <div class="policies-controls">
            <div class="controls-left">
                <button class="add-policy-btn" id="addPolicyFromPoliciesBtn">
                    <i class="fas fa-plus"></i>
                    Add New Policy
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
                            <th data-sort="policyType">Policy Type <i class="fas fa-sort"></i></th>
                            <th data-sort="customerName">Customer Name <i class="fas fa-sort"></i></th>
                            <th data-sort="phone">Phone <i class="fas fa-sort"></i></th>
                            <th data-sort="companyName">Insurance Company <i class="fas fa-sort"></i></th>
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

<!-- Include Modals -->
@include('components.policy-modal')
@include('components.view-policy-modal')

@push('scripts')
<script>
    // Global variables
    let policies = [];
    let filteredPolicies = [];
    let currentPage = 1;
    let rowsPerPage = 10;
    let currentSortColumn = '';
    let currentSortDirection = 'asc';
    let currentEditingPolicyId = null;

    // Policies page initialization
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Policies page initialized');
        
        // Load policies data
        loadPolicies();
        
        // Initialize event listeners
        initializeEventListeners();
        
        // Initialize search and filters
        initializeSearchAndFilters();
    });

    // Load policies from API
    function loadPolicies() {
        fetch('/api/policies')
            .then(response => response.json())
            .then(data => {
                policies = data.policies || [];
                filteredPolicies = [...policies];
                renderPoliciesTable();
                updateStatistics();
            })
            .catch(error => {
                console.error('Error loading policies:', error);
                showNotification('Error loading policies. Please refresh the page.', 'error');
            });
    }

    // Initialize event listeners
    function initializeEventListeners() {
        // Add policy button
        const addPolicyBtn = document.getElementById('addPolicyFromPoliciesBtn');
        if (addPolicyBtn) {
            addPolicyBtn.addEventListener('click', () => openPolicyModal());
        }

        // Rows per page
        const rowsPerPageSelect = document.getElementById('policiesRowsPerPage');
        if (rowsPerPageSelect) {
            rowsPerPageSelect.addEventListener('change', function() {
                rowsPerPage = parseInt(this.value);
                currentPage = 1;
                renderPoliciesTable();
            });
        }

        // Export button
        const exportBtn = document.getElementById('exportPolicies');
        if (exportBtn) {
            exportBtn.addEventListener('click', exportPoliciesData);
        }

        // Pagination buttons
        const prevBtn = document.getElementById('policiesPrevPage');
        const nextBtn = document.getElementById('policiesNextPage');
        if (prevBtn) prevBtn.addEventListener('click', () => changePage(currentPage - 1));
        if (nextBtn) nextBtn.addEventListener('click', () => changePage(currentPage + 1));

        // Sort headers
        const sortHeaders = document.querySelectorAll('#policiesPageTable th[data-sort]');
        sortHeaders.forEach(header => {
            header.addEventListener('click', () => {
                const column = header.getAttribute('data-sort');
                sortPolicies(column);
            });
        });
    }

    // Initialize search and filters
    function initializeSearchAndFilters() {
        // Search functionality
        const searchInput = document.getElementById('policiesSearch');
        if (searchInput) {
            searchInput.addEventListener('input', debounce(function() {
                filterPolicies();
            }, 300));
        }

        // Policy type filter
        const policyTypeFilter = document.getElementById('policyTypeFilter');
        if (policyTypeFilter) {
            policyTypeFilter.addEventListener('change', filterPolicies);
        }

        // Status filter
        const statusFilter = document.getElementById('statusFilter');
        if (statusFilter) {
            statusFilter.addEventListener('change', filterPolicies);
        }
    }

    // Filter policies
    function filterPolicies() {
        const searchTerm = document.getElementById('policiesSearch').value.toLowerCase();
        const policyType = document.getElementById('policyTypeFilter').value;
        const status = document.getElementById('statusFilter').value;

        filteredPolicies = policies.filter(policy => {
            const matchesSearch = !searchTerm || 
                policy.customerName.toLowerCase().includes(searchTerm) ||
                policy.policyNumber.toLowerCase().includes(searchTerm) ||
                policy.phone.includes(searchTerm) ||
                policy.companyName.toLowerCase().includes(searchTerm);

            const matchesType = !policyType || policy.policyType === policyType;
            const matchesStatus = !status || policy.status === status;

            return matchesSearch && matchesType && matchesStatus;
        });

        currentPage = 1;
        renderPoliciesTable();
        updateStatistics();
    }

    // Sort policies
    function sortPolicies(column) {
        if (currentSortColumn === column) {
            currentSortDirection = currentSortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            currentSortColumn = column;
            currentSortDirection = 'asc';
        }

        filteredPolicies.sort((a, b) => {
            let aVal = a[column];
            let bVal = b[column];

            if (column === 'premium' || column === 'revenue') {
                aVal = parseFloat(aVal);
                bVal = parseFloat(bVal);
            }

            if (aVal < bVal) return currentSortDirection === 'asc' ? -1 : 1;
            if (aVal > bVal) return currentSortDirection === 'asc' ? 1 : -1;
            return 0;
        });

        renderPoliciesTable();
    }

    // Render policies table
    function renderPoliciesTable() {
        const tableBody = document.getElementById('policiesPageTableBody');
        if (!tableBody) return;

        const startIndex = (currentPage - 1) * rowsPerPage;
        const endIndex = startIndex + rowsPerPage;
        const pageData = filteredPolicies.slice(startIndex, endIndex);

        tableBody.innerHTML = pageData.map(policy => `
            <tr>
                <td>${policy.id}</td>
                <td><span class="policy-type-badge ${policy.policyType.toLowerCase()}">${policy.policyType}</span></td>
                <td>${policy.customerName || 'N/A'}</td>
                <td>${policy.phone || 'N/A'}</td>
                <td>${policy.companyName || 'N/A'}</td>
                <td>${policy.endDate || 'N/A'}</td>
                <td>₹${(policy.premium || 0).toLocaleString()}</td>
                <td><span class="status-badge ${(policy.status || 'Active').toLowerCase()}">${policy.status || 'Active'}</span></td>
                <td>
                    <div class="action-buttons">
                        <button class="action-btn edit" onclick="editPolicy(${policy.id})" title="Edit Policy">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="action-btn view" onclick="viewPolicy(${policy.id})" title="View Policy">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="action-btn delete" onclick="deletePolicy(${policy.id})" title="Delete Policy">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');

        updatePagination();
    }

    // Update pagination
    function updatePagination() {
        const totalPages = Math.ceil(filteredPolicies.length / rowsPerPage);
        const startRecord = (currentPage - 1) * rowsPerPage + 1;
        const endRecord = Math.min(currentPage * rowsPerPage, filteredPolicies.length);

        // Update pagination info
        document.getElementById('policiesStartRecord').textContent = startRecord;
        document.getElementById('policiesEndRecord').textContent = endRecord;
        document.getElementById('policiesTotalRecords').textContent = filteredPolicies.length;

        // Update pagination buttons
        document.getElementById('policiesPrevPage').disabled = currentPage === 1;
        document.getElementById('policiesNextPage').disabled = currentPage === totalPages;

        // Generate page numbers
        const pageNumbersContainer = document.getElementById('policiesPageNumbers');
        let pageNumbersHTML = '';

        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
                pageNumbersHTML += `<button class="page-number ${i === currentPage ? 'active' : ''}" onclick="changePage(${i})">${i}</button>`;
            } else if (i === currentPage - 3 || i === currentPage + 3) {
                pageNumbersHTML += '<span class="page-ellipsis">...</span>';
            }
        }

        pageNumbersContainer.innerHTML = pageNumbersHTML;
    }

    // Change page
    function changePage(page) {
        const totalPages = Math.ceil(filteredPolicies.length / rowsPerPage);
        if (page >= 1 && page <= totalPages) {
            currentPage = page;
            renderPoliciesTable();
        }
    }

    // Update statistics
    function updateStatistics() {
        const activeCount = filteredPolicies.filter(p => p.status === 'Active').length;
        const expiredCount = filteredPolicies.filter(p => p.status === 'Expired').length;
        const pendingCount = filteredPolicies.filter(p => p.status === 'Pending').length;
        const totalCount = filteredPolicies.length;

        document.getElementById('activePoliciesCount').textContent = activeCount;
        document.getElementById('expiredPoliciesCount').textContent = expiredCount;
        document.getElementById('pendingRenewalsCount').textContent = pendingCount;
        document.getElementById('totalPoliciesCount').textContent = totalCount;
    }

    // CRUD Functions
    function openPolicyModal(policy = null) {
        const modal = document.getElementById('policyModal');
        if (!modal) return;
        
        if (policy) {
            // Edit mode
            document.getElementById('policyModalTitle').textContent = 'Edit Policy';
            // Populate form fields with policy data
            populatePolicyForm(policy);
        } else {
            // Add mode
            document.getElementById('policyModalTitle').textContent = 'Add New Policy';
            resetPolicyForm();
        }
        
        modal.classList.add('show');
    }

    function populatePolicyForm(policy) {
        // Populate form fields with policy data
        document.getElementById('policyTypeSelect').value = policy.policyType || '';
        document.getElementById('businessTypeSelect').value = policy.businessType || '';
        document.getElementById('customerName').value = policy.customerName || '';
        document.getElementById('customerPhone').value = policy.phone || '';
        document.getElementById('customerEmail').value = policy.email || '';
        document.getElementById('companyName').value = policy.companyName || '';
        document.getElementById('insuranceType').value = policy.insuranceType || '';
        document.getElementById('startDate').value = policy.startDate || '';
        document.getElementById('endDate').value = policy.endDate || '';
        document.getElementById('premium').value = policy.premium || '';
        document.getElementById('payout').value = policy.payout || '';
        document.getElementById('customerPaidAmount').value = policy.customerPaidAmount || '';
        document.getElementById('revenue').value = policy.revenue || '';
        document.getElementById('vehicleNumber').value = policy.vehicleNumber || '';
        document.getElementById('vehicleType').value = policy.vehicleType || '';
    }

    function resetPolicyForm() {
        // Reset all form fields
        const form = document.getElementById('policyForm');
        if (form) form.reset();
    }

    function savePolicy() {
        const formData = new FormData(document.getElementById('policyForm'));
        const isEdit = document.getElementById('policyModalTitle').textContent.includes('Edit');
        const policyId = isEdit ? currentEditingPolicyId : null;

        const url = isEdit ? `/policies/${policyId}` : '/policies';
        const method = isEdit ? 'PUT' : 'POST';

        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(Object.fromEntries(formData))
        })
        .then(response => response.json())
        .then(data => {
            if (data.message) {
                showNotification(data.message, 'success');
                closePolicyModal();
                loadPolicies(); // Reload data
            } else if (data.errors) {
                showNotification('Please check the form and try again.', 'error');
            }
        })
        .catch(error => {
            console.error('Error saving policy:', error);
            showNotification('Error saving policy. Please try again.', 'error');
        });
    }

    function closePolicyModal() {
        const modal = document.getElementById('policyModal');
        if (modal) {
            modal.classList.remove('show');
            resetPolicyForm();
        }
    }

    // Global functions for table actions
    window.editPolicy = function(id) {
        const policy = policies.find(p => p.id === id);
        if (policy) {
            currentEditingPolicyId = id;
            openPolicyModal(policy);
        }
    };

    window.viewPolicy = function(id) {
        const policy = policies.find(p => p.id === id);
        if (policy) {
            openViewPolicyModal(policy);
        }
    };

    window.deletePolicy = function(id) {
        if (confirm('Are you sure you want to delete this policy? This action cannot be undone.')) {
            fetch(`/policies/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                showNotification(data.message, 'success');
                loadPolicies(); // Reload data
            })
            .catch(error => {
                console.error('Error deleting policy:', error);
                showNotification('Error deleting policy. Please try again.', 'error');
            });
        }
    };

    function openViewPolicyModal(policy) {
        const modal = document.getElementById('viewPolicyModal');
        if (!modal || !policy) return;
        
        // Populate view modal with policy data
        document.getElementById('viewPolicyNumber').textContent = policy.policyNumber || 'N/A';
        document.getElementById('viewCustomerName').textContent = policy.customerName || 'N/A';
        document.getElementById('viewCustomerPhone').textContent = policy.phone || 'N/A';
        document.getElementById('viewCustomerEmail').textContent = policy.email || 'N/A';
        document.getElementById('viewPolicyType').textContent = policy.policyType || 'N/A';
        document.getElementById('viewCompanyName').textContent = policy.companyName || 'N/A';
        document.getElementById('viewInsuranceType').textContent = policy.insuranceType || 'N/A';
        document.getElementById('viewStartDate').textContent = policy.startDate || 'N/A';
        document.getElementById('viewEndDate').textContent = policy.endDate || 'N/A';
        document.getElementById('viewPremium').textContent = `₹${(policy.premium || 0).toLocaleString()}`;
        document.getElementById('viewRevenue').textContent = `₹${(policy.revenue || 0).toLocaleString()}`;
        document.getElementById('viewStatus').textContent = policy.status || 'N/A';
        document.getElementById('viewAgentName').textContent = policy.agentName || 'N/A';
        
        modal.classList.add('show');
    }

    function closeViewPolicyModal() {
        const modal = document.getElementById('viewPolicyModal');
        if (modal) {
            modal.classList.remove('show');
        }
    }

    function exportPoliciesData() {
        const csvContent = generatePoliciesCSV();
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', `policies_${new Date().toISOString().split('T')[0]}.csv`);
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function generatePoliciesCSV() {
        const headers = ['ID', 'Policy Number', 'Customer Name', 'Phone', 'Email', 'Policy Type', 'Company', 'Insurance Type', 'Start Date', 'End Date', 'Premium', 'Revenue', 'Status', 'Agent'];
        const rows = filteredPolicies.map(policy => [
            policy.id,
            policy.policyNumber,
            policy.customerName,
            policy.phone,
            policy.email,
            policy.policyType,
            policy.companyName,
            policy.insuranceType,
            policy.startDate,
            policy.endDate,
            policy.premium,
            policy.revenue,
            policy.status,
            policy.agentName
        ]);
        
        return [headers, ...rows].map(row => row.map(cell => `"${cell}"`).join(',')).join('\n');
    }

    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
                <span>${message}</span>
            </div>
            <button class="notification-close">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        document.body.appendChild(notification);
        setTimeout(() => notification.classList.add('show'), 100);
        
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, 5000);
        
        notification.querySelector('.notification-close').addEventListener('click', () => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        });
    }

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Modal close event listeners
    document.addEventListener('click', function(event) {
        const policyModal = document.getElementById('policyModal');
        const viewPolicyModal = document.getElementById('viewPolicyModal');
        
        if (event.target === policyModal) {
            closePolicyModal();
        }
        if (event.target === viewPolicyModal) {
            closeViewPolicyModal();
        }
    });
</script>
@endpush

@endsection
