@extends('layouts.insurance')

@section('title', 'Renewals - Insurance Management System')

@section('content')
<div class="page active" id="renewals">
    <div class="page-header">
        <h1>Renewals</h1>
        <p>Manage policy renewals and track upcoming expirations</p>
    </div>
    <div class="page-content">
        <!-- Renewals Controls -->
        <div class="renewals-controls">
            <div class="controls-left">
                <button class="add-renewal-btn" id="addRenewalBtn">
                    <i class="fas fa-plus"></i>
                    Add Renewal Reminder
                </button>
            </div>
            <div class="controls-right">
                <div class="filter-group">
                    <label for="renewalStatusFilter">Status:</label>
                    <select id="renewalStatusFilter">
                        <option value="">All Status</option>
                        <option value="Pending">Pending</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Completed">Completed</option>
                        <option value="Overdue">Overdue</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="renewalPriorityFilter">Priority:</label>
                    <select id="renewalPriorityFilter">
                        <option value="">All Priorities</option>
                        <option value="High">High</option>
                        <option value="Medium">Medium</option>
                        <option value="Low">Low</option>
                    </select>
                </div>
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search renewals..." id="renewalsSearch">
                </div>
            </div>
        </div>

        <!-- Renewals Statistics -->
        <div class="renewals-stats">
            <div class="stat-card glass-effect">
                <div class="stat-icon pending">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <h3>Pending Renewals</h3>
                    <p class="stat-value" id="pendingRenewalsCount">15</p>
                </div>
            </div>
            <div class="stat-card glass-effect">
                <div class="stat-icon overdue">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-content">
                    <h3>Overdue Renewals</h3>
                    <p class="stat-value" id="overdueRenewalsCount">3</p>
                </div>
            </div>
            <div class="stat-card glass-effect">
                <div class="stat-icon completed">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3>Completed This Month</h3>
                    <p class="stat-value" id="completedRenewalsCount">28</p>
                </div>
            </div>
            <div class="stat-card glass-effect">
                <div class="stat-icon total">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Renewals</h3>
                    <p class="stat-value" id="totalRenewalsCount">46</p>
                </div>
            </div>
        </div>

        <!-- Renewals Data Table -->
        <div class="data-table-container glass-effect">
            <div class="table-header">
                <h3>Renewals Management</h3>
                <div class="table-controls">
                    <select class="rows-per-page" id="renewalsRowsPerPage">
                        <option value="10">10 rows</option>
                        <option value="25">25 rows</option>
                        <option value="50">50 rows</option>
                        <option value="100">100 rows</option>
                    </select>
                    <button class="export-btn" id="exportRenewals">
                        <i class="fas fa-download"></i>
                        Export
                    </button>
                </div>
            </div>
            <div class="table-wrapper">
                <table class="data-table" id="renewalsTable">
                    <thead>
                        <tr>
                            <th data-sort="id">Sl. No <i class="fas fa-sort"></i></th>
                            <th data-sort="policyId">Policy ID <i class="fas fa-sort"></i></th>
                            <th data-sort="customerName">Customer Name <i class="fas fa-sort"></i></th>
                            <th data-sort="policyType">Policy Type <i class="fas fa-sort"></i></th>
                            <th data-sort="expiryDate">Expiry Date <i class="fas fa-sort"></i></th>
                            <th data-sort="daysLeft">Days Left <i class="fas fa-sort"></i></th>
                            <th data-sort="status">Status <i class="fas fa-sort"></i></th>
                            <th data-sort="priority">Priority <i class="fas fa-sort"></i></th>
                            <th data-sort="assignedTo">Assigned To <i class="fas fa-sort"></i></th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="renewalsTableBody">
                        <!-- Sample data -->
                        <tr>
                            <td>1</td>
                            <td>POL001</td>
                            <td>John Doe</td>
                            <td><span class="policy-type-badge motor">Motor</span></td>
                            <td>2025-02-15</td>
                            <td><span class="days-left urgent">5 days</span></td>
                            <td><span class="status-badge pending">Pending</span></td>
                            <td><span class="priority-badge high">High</span></td>
                            <td>Alice Brown</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-btn edit"><i class="fas fa-edit"></i></button>
                                    <button class="action-btn"><i class="fas fa-eye"></i></button>
                                    <button class="action-btn"><i class="fas fa-phone"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>POL002</td>
                            <td>Jane Smith</td>
                            <td><span class="policy-type-badge health">Health</span></td>
                            <td>2025-03-01</td>
                            <td><span class="days-left warning">20 days</span></td>
                            <td><span class="status-badge pending">In Progress</span></td>
                            <td><span class="priority-badge medium">Medium</span></td>
                            <td>Charlie Wilson</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-btn edit"><i class="fas fa-edit"></i></button>
                                    <button class="action-btn"><i class="fas fa-eye"></i></button>
                                    <button class="action-btn"><i class="fas fa-phone"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>POL003</td>
                            <td>Bob Johnson</td>
                            <td><span class="policy-type-badge life">Life</span></td>
                            <td>2025-01-31</td>
                            <td><span class="days-left urgent">-2 days</span></td>
                            <td><span class="status-badge expired">Overdue</span></td>
                            <td><span class="priority-badge high">High</span></td>
                            <td>Alice Brown</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-btn edit"><i class="fas fa-edit"></i></button>
                                    <button class="action-btn"><i class="fas fa-eye"></i></button>
                                    <button class="action-btn"><i class="fas fa-phone"></i></button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="table-pagination">
                <div class="pagination-info">
                    Showing <span id="renewalsStartRecord">1</span> to <span id="renewalsEndRecord">10</span> of <span id="renewalsTotalRecords">46</span> entries
                </div>
                <div class="pagination-controls">
                    <button class="pagination-btn" id="renewalsPrevPage" disabled>
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <div class="page-numbers" id="renewalsPageNumbers">
                        <button class="page-number active">1</button>
                        <button class="page-number">2</button>
                        <button class="page-number">3</button>
                    </div>
                    <button class="pagination-btn" id="renewalsNextPage">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Renewal specific styles */
.renewals-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 16px;
}

.add-renewal-btn {
    background: linear-gradient(135deg, #4F46E5, #7C3AED);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
}

.add-renewal-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(79, 70, 229, 0.4);
}

.renewals-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 32px;
}

.stat-icon.overdue {
    background: linear-gradient(135deg, #EF4444, #DC2626);
}

.stat-icon.completed {
    background: linear-gradient(135deg, #10B981, #059669);
}

/* Priority Badges */
.priority-badge {
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.priority-badge.high {
    background: rgba(239, 68, 68, 0.1);
    color: #EF4444;
    border: 1px solid rgba(239, 68, 68, 0.3);
}

.priority-badge.medium {
    background: rgba(245, 158, 11, 0.1);
    color: #F59E0B;
    border: 1px solid rgba(245, 158, 11, 0.3);
}

.priority-badge.low {
    background: rgba(16, 185, 129, 0.1);
    color: #10B981;
    border: 1px solid rgba(16, 185, 129, 0.3);
}

/* Days Left Indicators */
.days-left {
    font-weight: 600;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
}

.days-left.urgent {
    background: rgba(239, 68, 68, 0.1);
    color: #EF4444;
}

.days-left.warning {
    background: rgba(245, 158, 11, 0.1);
    color: #F59E0B;
}

.days-left.safe {
    background: rgba(16, 185, 129, 0.1);
    color: #10B981;
}

/* Policy Type Badges */
.policy-type-badge {
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.policy-type-badge.motor {
    background: rgba(59, 130, 246, 0.1);
    color: #3b82f6;
    border: 1px solid rgba(59, 130, 246, 0.2);
}

.policy-type-badge.health {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
    border: 1px solid rgba(16, 185, 129, 0.2);
}

.policy-type-badge.life {
    background: rgba(245, 158, 11, 0.1);
    color: #f59e0b;
    border: 1px solid rgba(245, 158, 11, 0.2);
}

/* Renewals specific styles */
.renewals-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 16px;
}

.add-renewal-btn {
    background: linear-gradient(135deg, #4F46E5, #6366F1);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
}

.add-renewal-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(79, 70, 229, 0.4);
}
</style>

<!-- Include Modals -->
@include('components.renewal-modal')

@push('scripts')
<script>
    // Global variables
    let renewals = [];
    let filteredRenewals = [];
    let currentPage = 1;
    let rowsPerPage = 10;
    let currentSortColumn = '';
    let currentSortDirection = 'asc';
    let currentEditingRenewalId = null;

    // Renewals page initialization
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Renewals page initialized');
        
        // Load renewals data
        loadRenewals();
        
        // Initialize event listeners
        initializeEventListeners();
        
        // Initialize search and filters
        initializeSearchAndFilters();
    });

    // Load renewals from API
    function loadRenewals() {
        fetch('/api/renewals')
            .then(response => response.json())
            .then(data => {
                renewals = data.renewals || [];
                filteredRenewals = [...renewals];
                renderRenewalsTable();
                updateStatistics();
            })
            .catch(error => {
                console.error('Error loading renewals:', error);
                showNotification('Error loading renewals. Please refresh the page.', 'error');
            });
    }

    // Initialize event listeners
    function initializeEventListeners() {
        // Add renewal button
        const addRenewalBtn = document.getElementById('addRenewalBtn');
        if (addRenewalBtn) {
            addRenewalBtn.addEventListener('click', () => openRenewalModal());
        }

        // Rows per page
        const rowsPerPageSelect = document.getElementById('renewalsRowsPerPage');
        if (rowsPerPageSelect) {
            rowsPerPageSelect.addEventListener('change', function() {
                rowsPerPage = parseInt(this.value);
                currentPage = 1;
                renderRenewalsTable();
            });
        }

        // Export button
        const exportBtn = document.getElementById('exportRenewals');
        if (exportBtn) {
            exportBtn.addEventListener('click', exportRenewalsData);
        }

        // Pagination buttons
        const prevBtn = document.getElementById('renewalsPrevPage');
        const nextBtn = document.getElementById('renewalsNextPage');
        if (prevBtn) prevBtn.addEventListener('click', () => changePage(currentPage - 1));
        if (nextBtn) nextBtn.addEventListener('click', () => changePage(currentPage + 1));

        // Sort headers
        const sortHeaders = document.querySelectorAll('#renewalsTable th[data-sort]');
        sortHeaders.forEach(header => {
            header.addEventListener('click', () => {
                const column = header.getAttribute('data-sort');
                sortRenewals(column);
            });
        });
    }

    // Initialize search and filters
    function initializeSearchAndFilters() {
        // Search functionality
        const searchInput = document.getElementById('renewalsSearch');
        if (searchInput) {
            searchInput.addEventListener('input', debounce(function() {
                filterRenewals();
            }, 300));
        }

        // Status filter
        const statusFilter = document.getElementById('renewalStatusFilter');
        if (statusFilter) {
            statusFilter.addEventListener('change', filterRenewals);
        }

        // Priority filter
        const priorityFilter = document.getElementById('renewalPriorityFilter');
        if (priorityFilter) {
            priorityFilter.addEventListener('change', filterRenewals);
        }
    }

    // Filter renewals
    function filterRenewals() {
        const searchTerm = document.getElementById('renewalsSearch').value.toLowerCase();
        const status = document.getElementById('renewalStatusFilter').value;
        const priority = document.getElementById('renewalPriorityFilter').value;

        filteredRenewals = renewals.filter(renewal => {
            const matchesSearch = !searchTerm || 
                renewal.customerName.toLowerCase().includes(searchTerm) ||
                renewal.policyNumber.toLowerCase().includes(searchTerm) ||
                renewal.phone.includes(searchTerm);

            const matchesStatus = !status || renewal.status === status;
            const matchesPriority = !priority || renewal.priority === priority;

            return matchesSearch && matchesStatus && matchesPriority;
        });

        currentPage = 1;
        renderRenewalsTable();
        updateStatistics();
    }

    // Sort renewals
    function sortRenewals(column) {
        if (currentSortColumn === column) {
            currentSortDirection = currentSortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            currentSortColumn = column;
            currentSortDirection = 'asc';
        }

        filteredRenewals.sort((a, b) => {
            let aVal = a[column];
            let bVal = b[column];

            if (column === 'currentPremium' || column === 'renewalPremium') {
                aVal = parseFloat(aVal);
                bVal = parseFloat(bVal);
            }

            if (aVal < bVal) return currentSortDirection === 'asc' ? -1 : 1;
            if (aVal > bVal) return currentSortDirection === 'asc' ? 1 : -1;
            return 0;
        });

        renderRenewalsTable();
    }

    // Render renewals table
    function renderRenewalsTable() {
        const tableBody = document.getElementById('renewalsTableBody');
        if (!tableBody) return;

        const startIndex = (currentPage - 1) * rowsPerPage;
        const endIndex = startIndex + rowsPerPage;
        const pageData = filteredRenewals.slice(startIndex, endIndex);

        tableBody.innerHTML = pageData.map(renewal => {
            const dueDate = new Date(renewal.dueDate);
            const today = new Date();
            const daysLeft = Math.ceil((dueDate - today) / (1000 * 60 * 60 * 24));
            
            const daysLeftClass = daysLeft <= 7 ? 'urgent' : daysLeft <= 15 ? 'warning' : 'safe';
            const statusClass = renewal.status.toLowerCase();
            
            return `
                <tr>
                    <td>${renewal.id}</td>
                    <td>${renewal.policyNumber}</td>
                    <td>${renewal.customerName || 'N/A'}</td>
                    <td><span class="policy-type-badge ${renewal.policyType.toLowerCase()}">${renewal.policyType}</span></td>
                    <td>${renewal.dueDate || 'N/A'}</td>
                    <td><span class="days-left ${daysLeftClass}">${daysLeft} days</span></td>
                    <td><span class="status-badge ${statusClass}">${renewal.status || 'Pending'}</span></td>
                    <td><span class="priority-badge medium">Medium</span></td>
                    <td>${renewal.agentName || 'N/A'}</td>
                    <td>
                        <div class="action-buttons">
                            <button class="action-btn edit" onclick="editRenewal(${renewal.id})" title="Edit Renewal">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="action-btn view" onclick="viewRenewal(${renewal.id})" title="View Renewal">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="action-btn delete" onclick="deleteRenewal(${renewal.id})" title="Delete Renewal">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');

        updatePagination();
    }

    // Update pagination
    function updatePagination() {
        const totalPages = Math.ceil(filteredRenewals.length / rowsPerPage);
        const startRecord = (currentPage - 1) * rowsPerPage + 1;
        const endRecord = Math.min(currentPage * rowsPerPage, filteredRenewals.length);

        // Update pagination info
        document.getElementById('renewalsStartRecord').textContent = startRecord;
        document.getElementById('renewalsEndRecord').textContent = endRecord;
        document.getElementById('renewalsTotalRecords').textContent = filteredRenewals.length;

        // Update pagination buttons
        document.getElementById('renewalsPrevPage').disabled = currentPage === 1;
        document.getElementById('renewalsNextPage').disabled = currentPage === totalPages;

        // Generate page numbers
        const pageNumbersContainer = document.getElementById('renewalsPageNumbers');
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
        const totalPages = Math.ceil(filteredRenewals.length / rowsPerPage);
        if (page >= 1 && page <= totalPages) {
            currentPage = page;
            renderRenewalsTable();
        }
    }

    // Update statistics
    function updateStatistics() {
        const pendingCount = filteredRenewals.filter(r => r.status === 'Pending').length;
        const overdueCount = filteredRenewals.filter(r => r.status === 'Overdue').length;
        const completedCount = filteredRenewals.filter(r => r.status === 'Completed').length;
        const totalCount = filteredRenewals.length;

        document.getElementById('pendingRenewalsCount').textContent = pendingCount;
        document.getElementById('overdueRenewalsCount').textContent = overdueCount;
        document.getElementById('completedRenewalsCount').textContent = completedCount;
        document.getElementById('totalRenewalsCount').textContent = totalCount;
    }

    // CRUD Functions
    function openRenewalModal(renewal = null) {
        const modal = document.getElementById('renewalModal');
        if (!modal) return;
        
        if (renewal) {
            // Edit mode
            document.getElementById('renewalModalTitle').textContent = 'Edit Renewal';
            populateRenewalForm(renewal);
        } else {
            // Add mode
            document.getElementById('renewalModalTitle').textContent = 'Add New Renewal';
            resetRenewalForm();
        }
        
        modal.classList.add('show');
    }

    function populateRenewalForm(renewal) {
        document.getElementById('policyNumber').value = renewal.policyNumber || '';
        document.getElementById('customerName').value = renewal.customerName || '';
        document.getElementById('phone').value = renewal.phone || '';
        document.getElementById('email').value = renewal.email || '';
        document.getElementById('policyType').value = renewal.policyType || '';
        document.getElementById('currentPremium').value = renewal.currentPremium || '';
        document.getElementById('renewalPremium').value = renewal.renewalPremium || '';
        document.getElementById('dueDate').value = renewal.dueDate || '';
        document.getElementById('status').value = renewal.status || '';
        document.getElementById('agentName').value = renewal.agentName || '';
        document.getElementById('notes').value = renewal.notes || '';
    }

    function resetRenewalForm() {
        const form = document.getElementById('renewalForm');
        if (form) form.reset();
    }

    function saveRenewal() {
        const formData = new FormData(document.getElementById('renewalForm'));
        const isEdit = document.getElementById('renewalModalTitle').textContent.includes('Edit');
        const renewalId = isEdit ? currentEditingRenewalId : null;

        const url = isEdit ? `/renewals/${renewalId}` : '/renewals';
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
                closeRenewalModal();
                loadRenewals(); // Reload data
            } else if (data.errors) {
                showNotification('Please check the form and try again.', 'error');
            }
        })
        .catch(error => {
            console.error('Error saving renewal:', error);
            showNotification('Error saving renewal. Please try again.', 'error');
        });
    }

    function closeRenewalModal() {
        const modal = document.getElementById('renewalModal');
        if (modal) {
            modal.classList.remove('show');
            resetRenewalForm();
        }
    }

    // Global functions for table actions
    window.editRenewal = function(id) {
        const renewal = renewals.find(r => r.id === id);
        if (renewal) {
            currentEditingRenewalId = id;
            openRenewalModal(renewal);
        }
    };

    window.viewRenewal = function(id) {
        const renewal = renewals.find(r => r.id === id);
        if (renewal) {
            openViewRenewalModal(renewal);
        }
    };

    window.deleteRenewal = function(id) {
        if (confirm('Are you sure you want to delete this renewal? This action cannot be undone.')) {
            fetch(`/renewals/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                showNotification(data.message, 'success');
                loadRenewals(); // Reload data
            })
            .catch(error => {
                console.error('Error deleting renewal:', error);
                showNotification('Error deleting renewal. Please try again.', 'error');
            });
        }
    };

    function openViewRenewalModal(renewal) {
        const modal = document.getElementById('viewRenewalModal');
        if (!modal || !renewal) return;
        
        // Populate view modal with renewal data
        document.getElementById('viewPolicyNumber').textContent = renewal.policyNumber || 'N/A';
        document.getElementById('viewCustomerName').textContent = renewal.customerName || 'N/A';
        document.getElementById('viewCustomerPhone').textContent = renewal.phone || 'N/A';
        document.getElementById('viewCustomerEmail').textContent = renewal.email || 'N/A';
        document.getElementById('viewPolicyType').textContent = renewal.policyType || 'N/A';
        document.getElementById('viewCurrentPremium').textContent = `₹${(renewal.currentPremium || 0).toLocaleString()}`;
        document.getElementById('viewRenewalPremium').textContent = `₹${(renewal.renewalPremium || 0).toLocaleString()}`;
        document.getElementById('viewDueDate').textContent = renewal.dueDate || 'N/A';
        document.getElementById('viewStatus').textContent = renewal.status || 'N/A';
        document.getElementById('viewAgentName').textContent = renewal.agentName || 'N/A';
        document.getElementById('viewNotes').textContent = renewal.notes || 'N/A';
        
        modal.classList.add('show');
    }

    function closeViewRenewalModal() {
        const modal = document.getElementById('viewRenewalModal');
        if (modal) {
            modal.classList.remove('show');
        }
    }

    function exportRenewalsData() {
        const csvContent = generateRenewalsCSV();
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', `renewals_${new Date().toISOString().split('T')[0]}.csv`);
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function generateRenewalsCSV() {
        const headers = ['ID', 'Policy Number', 'Customer Name', 'Phone', 'Email', 'Policy Type', 'Current Premium', 'Renewal Premium', 'Due Date', 'Status', 'Agent', 'Notes'];
        const rows = filteredRenewals.map(renewal => [
            renewal.id,
            renewal.policyNumber,
            renewal.customerName,
            renewal.phone,
            renewal.email,
            renewal.policyType,
            renewal.currentPremium,
            renewal.renewalPremium,
            renewal.dueDate,
            renewal.status,
            renewal.agentName,
            renewal.notes
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
        const renewalModal = document.getElementById('renewalModal');
        const viewRenewalModal = document.getElementById('viewRenewalModal');
        
        if (event.target === renewalModal) {
            closeRenewalModal();
        }
        if (event.target === viewRenewalModal) {
            closeViewRenewalModal();
        }
    });
    
    // Renewal functions
    function editRenewal(id) {
        console.log('Edit renewal:', id);
        // Show edit modal
        const renewalModal = document.getElementById('renewalModal');
        if (renewalModal) {
            renewalModal.classList.add('show');
        }
    }
    
    function viewRenewal(id) {
        console.log('View renewal:', id);
        // Show view modal
        const viewRenewalModal = document.getElementById('viewRenewalModal');
        if (viewRenewalModal) {
            viewRenewalModal.classList.add('show');
        }
    }
    
    function deleteRenewal(id) {
        if (confirm('Are you sure you want to delete this renewal?')) {
            console.log('Delete renewal:', id);
            // Add delete functionality here
        }
    }
</script>
@endpush

@endsection
