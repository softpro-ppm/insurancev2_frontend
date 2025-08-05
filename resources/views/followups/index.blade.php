@extends('layouts.insurance')

@section('title', 'Follow Ups - Insurance Management System')

@section('content')
<div class="page active" id="followups">
    <div class="page-header">
        <h1>Follow Ups</h1>
        <p>Track customer interactions and telecaller follow-up activities</p>
    </div>
    <div class="page-content">
        <!-- Follow Ups Controls -->
        <div class="followups-controls">
            <div class="controls-left">
                <button class="add-followup-btn" id="addFollowupBtn">
                    <i class="fas fa-plus"></i>
                    Add Follow Up
                </button>
            </div>
            <div class="controls-right">
                <div class="filter-group">
                    <label for="followupStatusFilter">Status:</label>
                    <select id="followupStatusFilter">
                        <option value="">All Status</option>
                        <option value="Pending">Pending</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Completed">Completed</option>
                        <option value="No Response">No Response</option>
                        <option value="Not Interested">Not Interested</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="followupTypeFilter">Type:</label>
                    <select id="followupTypeFilter">
                        <option value="">All Types</option>
                        <option value="Renewal">Renewal</option>
                        <option value="New Policy">New Policy</option>
                        <option value="Claim">Claim</option>
                        <option value="General">General</option>
                    </select>
                </div>
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search follow-ups..." id="followupsSearch">
                </div>
            </div>
        </div>

        <!-- Follow Ups Statistics -->
        <div class="followups-stats">
            <div class="stat-card glass-effect">
                <div class="stat-icon pending">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <h3>Pending Follow-ups</h3>
                    <p class="stat-value" id="pendingFollowupsCount">12</p>
                </div>
            </div>
            <div class="stat-card glass-effect">
                <div class="stat-icon inprogress">
                    <i class="fas fa-phone"></i>
                </div>
                <div class="stat-content">
                    <h3>In Progress</h3>
                    <p class="stat-value" id="inProgressFollowupsCount">8</p>
                </div>
            </div>
            <div class="stat-card glass-effect">
                <div class="stat-icon completed">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3>Completed Today</h3>
                    <p class="stat-value" id="completedTodayCount">15</p>
                </div>
            </div>
            <div class="stat-card glass-effect">
                <div class="stat-icon total">
                    <i class="fas fa-list-alt"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Follow-ups</h3>
                    <p class="stat-value" id="totalFollowupsCount">35</p>
                </div>
            </div>
        </div>

        <!-- Follow Ups Data Table -->
        <div class="data-table-container glass-effect">
            <div class="table-header">
                <h3>Follow-up Management</h3>
                <div class="table-controls">
                    <select class="rows-per-page" id="followupsRowsPerPage">
                        <option value="10">10 rows</option>
                        <option value="25">25 rows</option>
                        <option value="50">50 rows</option>
                        <option value="100">100 rows</option>
                    </select>
                    <button class="export-btn" id="exportFollowups">
                        <i class="fas fa-download"></i>
                        Export
                    </button>
                </div>
            </div>
            <div class="table-wrapper">
                <table class="data-table" id="followupsTable">
                    <thead>
                        <tr>
                            <th data-sort="id">Sl. No <i class="fas fa-sort"></i></th>
                            <th data-sort="customerName">Customer Name <i class="fas fa-sort"></i></th>
                            <th data-sort="phone">Phone Number <i class="fas fa-sort"></i></th>
                            <th data-sort="followupType">Follow-up Type <i class="fas fa-sort"></i></th>
                            <th data-sort="status">Status <i class="fas fa-sort"></i></th>
                            <th data-sort="assignedTo">Assigned To <i class="fas fa-sort"></i></th>
                            <th data-sort="lastFollowupDate">Last Follow-up <i class="fas fa-sort"></i></th>
                            <th data-sort="nextFollowupDate">Next Follow-up <i class="fas fa-sort"></i></th>
                            <th data-sort="recentNote">Recent Note <i class="fas fa-sort"></i></th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="followupsTableBody">
                        <!-- Sample data -->
                        <tr>
                            <td>1</td>
                            <td>Sarah Connor</td>
                            <td>+91-9876543210</td>
                            <td><span class="followup-type-badge renewal">Renewal</span></td>
                            <td><span class="status-badge pending">Pending</span></td>
                            <td>Alice Brown</td>
                            <td>2025-01-30</td>
                            <td>2025-02-05</td>
                            <td>Customer interested in renewal quote</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-btn edit"><i class="fas fa-edit"></i></button>
                                    <button class="action-btn"><i class="fas fa-phone"></i></button>
                                    <button class="action-btn"><i class="fas fa-comments"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Mike Johnson</td>
                            <td>+91-8765432109</td>
                            <td><span class="followup-type-badge newpolicy">New Policy</span></td>
                            <td><span class="status-badge inprogress">In Progress</span></td>
                            <td>Charlie Wilson</td>
                            <td>2025-01-31</td>
                            <td>2025-02-03</td>
                            <td>Sent policy documents for review</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-btn edit"><i class="fas fa-edit"></i></button>
                                    <button class="action-btn"><i class="fas fa-phone"></i></button>
                                    <button class="action-btn"><i class="fas fa-comments"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Emma Wilson</td>
                            <td>+91-7654321098</td>
                            <td><span class="followup-type-badge claim">Claim</span></td>
                            <td><span class="status-badge completed">Completed</span></td>
                            <td>David Smith</td>
                            <td>2025-01-29</td>
                            <td>-</td>
                            <td>Claim processed successfully</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-btn edit"><i class="fas fa-edit"></i></button>
                                    <button class="action-btn"><i class="fas fa-phone"></i></button>
                                    <button class="action-btn"><i class="fas fa-comments"></i></button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="table-pagination">
                <div class="pagination-info">
                    Showing <span id="followupsStartRecord">1</span> to <span id="followupsEndRecord">10</span> of <span id="followupsTotalRecords">35</span> entries
                </div>
                <div class="pagination-controls">
                    <button class="pagination-btn" id="followupsPrevPage" disabled>
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <div class="page-numbers" id="followupsPageNumbers">
                        <button class="page-number active">1</button>
                        <button class="page-number">2</button>
                        <button class="page-number">3</button>
                    </div>
                    <button class="pagination-btn" id="followupsNextPage">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Follow-ups specific styles */
.followups-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 16px;
}

.add-followup-btn {
    background: linear-gradient(135deg, #10B981, #059669);
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
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.add-followup-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
}

.followups-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 32px;
}

.stat-icon.inprogress {
    background: linear-gradient(135deg, #F59E0B, #D97706);
}

/* Follow-up Type Badges */
.followup-type-badge {
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.followup-type-badge.renewal {
    background: rgba(59, 130, 246, 0.1);
    color: #3b82f6;
    border: 1px solid rgba(59, 130, 246, 0.2);
}

.followup-type-badge.newpolicy {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
    border: 1px solid rgba(16, 185, 129, 0.2);
}

.followup-type-badge.claim {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
    border: 1px solid rgba(239, 68, 68, 0.2);
}

.followup-type-badge.general {
    background: rgba(107, 114, 128, 0.1);
    color: #6b7280;
    border: 1px solid rgba(107, 114, 128, 0.2);
}

/* Status Badge for In Progress */
.status-badge.inprogress {
    background: rgba(245, 158, 11, 0.1);
    color: #F59E0B;
    border: 1px solid rgba(245, 158, 11, 0.3);
}

/* Follow-up specific styles */
.followups-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 16px;
}

.add-followup-btn {
    background: linear-gradient(135deg, #10B981, #059669);
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

.add-followup-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
}
</style>

<!-- Include Modals -->
@include('components.followup-modal')

@push('scripts')
<script>
    // Global variables
    let followups = [];
    let filteredFollowups = [];
    let currentPage = 1;
    let rowsPerPage = 10;
    let currentSortColumn = '';
    let currentSortDirection = 'asc';
    let currentEditingFollowupId = null;

    // Followups page initialization
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Followups page initialized');
        
        // Load followups data
        loadFollowups();
        
        // Initialize event listeners
        initializeEventListeners();
        
        // Initialize search and filters
        initializeSearchAndFilters();
    });

    // Load followups from API
    function loadFollowups() {
        fetch('/api/followups')
            .then(response => response.json())
            .then(data => {
                followups = data.followups || [];
                filteredFollowups = [...followups];
                renderFollowupsTable();
                updateStatistics();
            })
            .catch(error => {
                console.error('Error loading followups:', error);
                showNotification('Error loading followups. Please refresh the page.', 'error');
            });
    }

    // Initialize event listeners
    function initializeEventListeners() {
        // Add followup button
        const addFollowupBtn = document.getElementById('addFollowupBtn');
        if (addFollowupBtn) {
            addFollowupBtn.addEventListener('click', () => openFollowupModal());
        }

        // Rows per page
        const rowsPerPageSelect = document.getElementById('followupsRowsPerPage');
        if (rowsPerPageSelect) {
            rowsPerPageSelect.addEventListener('change', function() {
                rowsPerPage = parseInt(this.value);
                currentPage = 1;
                renderFollowupsTable();
            });
        }

        // Export button
        const exportBtn = document.getElementById('exportFollowups');
        if (exportBtn) {
            exportBtn.addEventListener('click', exportFollowupsData);
        }

        // Pagination buttons
        const prevBtn = document.getElementById('followupsPrevPage');
        const nextBtn = document.getElementById('followupsNextPage');
        if (prevBtn) prevBtn.addEventListener('click', () => changePage(currentPage - 1));
        if (nextBtn) nextBtn.addEventListener('click', () => changePage(currentPage + 1));

        // Sort headers
        const sortHeaders = document.querySelectorAll('#followupsTable th[data-sort]');
        sortHeaders.forEach(header => {
            header.addEventListener('click', () => {
                const column = header.getAttribute('data-sort');
                sortFollowups(column);
            });
        });

        // Form submission
        const followupForm = document.getElementById('followupForm');
        if (followupForm) {
            followupForm.addEventListener('submit', function(e) {
                e.preventDefault();
                saveFollowup();
            });
        }

        // Modal close buttons
        const closeFollowupBtn = document.getElementById('closeFollowupModal');
        const cancelFollowupBtn = document.getElementById('cancelFollowup');
        const saveFollowupBtn = document.getElementById('saveFollowupBtn');
        if (closeFollowupBtn) closeFollowupBtn.addEventListener('click', closeFollowupModal);
        if (cancelFollowupBtn) cancelFollowupBtn.addEventListener('click', closeFollowupModal);
        if (saveFollowupBtn) saveFollowupBtn.addEventListener('click', saveFollowup);
    }

    // Initialize search and filters
    function initializeSearchAndFilters() {
        // Search functionality
        const searchInput = document.getElementById('followupsSearch');
        if (searchInput) {
            searchInput.addEventListener('input', debounce(function() {
                filterFollowups();
            }, 300));
        }

        // Status filter
        const statusFilter = document.getElementById('followupStatusFilter');
        if (statusFilter) {
            statusFilter.addEventListener('change', filterFollowups);
        }

        // Type filter
        const typeFilter = document.getElementById('followupTypeFilter');
        if (typeFilter) {
            typeFilter.addEventListener('change', filterFollowups);
        }
    }

    // Filter followups
    function filterFollowups() {
        const searchTerm = document.getElementById('followupsSearch').value.toLowerCase();
        const status = document.getElementById('followupStatusFilter').value;
        const type = document.getElementById('followupTypeFilter').value;

        filteredFollowups = followups.filter(followup => {
            const matchesSearch = !searchTerm || 
                followup.customerName.toLowerCase().includes(searchTerm) ||
                followup.policyNumber.toLowerCase().includes(searchTerm) ||
                followup.phone.includes(searchTerm);

            const matchesStatus = !status || followup.status === status;
            const matchesType = !type || followup.followupType === type;

            return matchesSearch && matchesStatus && matchesType;
        });

        currentPage = 1;
        renderFollowupsTable();
        updateStatistics();
    }

    // Sort followups
    function sortFollowups(column) {
        if (currentSortColumn === column) {
            currentSortDirection = currentSortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            currentSortColumn = column;
            currentSortDirection = 'asc';
        }

        filteredFollowups.sort((a, b) => {
            let aVal = a[column];
            let bVal = b[column];

            if (aVal < bVal) return currentSortDirection === 'asc' ? -1 : 1;
            if (aVal > bVal) return currentSortDirection === 'asc' ? 1 : -1;
            return 0;
        });

        renderFollowupsTable();
    }

    // Render followups table
    function renderFollowupsTable() {
        const tableBody = document.getElementById('followupsTableBody');
        if (!tableBody) return;

        const startIndex = (currentPage - 1) * rowsPerPage;
        const endIndex = startIndex + rowsPerPage;
        const pageData = filteredFollowups.slice(startIndex, endIndex);

        tableBody.innerHTML = pageData.map(followup => {
            const typeClass = (followup.followupType || '').toLowerCase().replace(' ', '');
            const statusClass = (followup.status || '').toLowerCase().replace(' ', '');
            
            return `
                <tr>
                    <td>${followup.id}</td>
                    <td>${followup.customerName || 'N/A'}</td>
                    <td>${followup.phone || 'N/A'}</td>
                    <td><span class="followup-type-badge ${typeClass}">${followup.followupType || 'N/A'}</span></td>
                    <td><span class="status-badge ${statusClass}">${followup.status || 'Pending'}</span></td>
                    <td>${followup.agentName || 'N/A'}</td>
                    <td>${followup.followupDate || 'N/A'}</td>
                    <td>${followup.followupDate || 'N/A'}</td>
                    <td>${followup.notes || 'N/A'}</td>
                    <td>
                        <div class="action-buttons">
                            <button class="action-btn edit" onclick="editFollowup(${followup.id})" title="Edit Followup">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="action-btn view" onclick="viewFollowup(${followup.id})" title="View Followup">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="action-btn delete" onclick="deleteFollowup(${followup.id})" title="Delete Followup">
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
        const totalPages = Math.ceil(filteredFollowups.length / rowsPerPage);
        const startRecord = (currentPage - 1) * rowsPerPage + 1;
        const endRecord = Math.min(currentPage * rowsPerPage, filteredFollowups.length);

        // Update pagination info
        document.getElementById('followupsStartRecord').textContent = startRecord;
        document.getElementById('followupsEndRecord').textContent = endRecord;
        document.getElementById('followupsTotalRecords').textContent = filteredFollowups.length;

        // Update pagination buttons
        document.getElementById('followupsPrevPage').disabled = currentPage === 1;
        document.getElementById('followupsNextPage').disabled = currentPage === totalPages;

        // Generate page numbers
        const pageNumbersContainer = document.getElementById('followupsPageNumbers');
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
        const totalPages = Math.ceil(filteredFollowups.length / rowsPerPage);
        if (page >= 1 && page <= totalPages) {
            currentPage = page;
            renderFollowupsTable();
        }
    }

    // Update statistics
    function updateStatistics() {
        const pendingCount = filteredFollowups.filter(f => f.status === 'Pending').length;
        const inProgressCount = filteredFollowups.filter(f => f.status === 'In Progress').length;
        const completedCount = filteredFollowups.filter(f => f.status === 'Completed').length;
        const totalCount = filteredFollowups.length;

        document.getElementById('pendingFollowupsCount').textContent = pendingCount;
        document.getElementById('inProgressFollowupsCount').textContent = inProgressCount;
        document.getElementById('completedTodayCount').textContent = completedCount;
        document.getElementById('totalFollowupsCount').textContent = totalCount;
    }

    // CRUD Functions
    function openFollowupModal(followup = null) {
        const modal = document.getElementById('followupModal');
        if (!modal) return;
        
        if (followup) {
            // Edit mode
            document.getElementById('followupModalTitle').textContent = 'Edit Followup';
            populateFollowupForm(followup);
            currentEditingFollowupId = followup.id;
        } else {
            // Add mode
            document.getElementById('followupModalTitle').textContent = 'Add New Followup';
            resetFollowupForm();
            currentEditingFollowupId = null;
        }
        
        modal.classList.add('show');
    }

    function populateFollowupForm(followup) {
        document.getElementById('policyNumber').value = followup.policyNumber || '';
        document.getElementById('customerName').value = followup.customerName || '';
        document.getElementById('phone').value = followup.phone || '';
        document.getElementById('email').value = followup.email || '';
        document.getElementById('followupDate').value = followup.followupDate || '';
        document.getElementById('followupType').value = followup.followupType || '';
        document.getElementById('status').value = followup.status || '';
        document.getElementById('agentName').value = followup.agentName || '';
        document.getElementById('notes').value = followup.notes || '';
    }

    function resetFollowupForm() {
        const form = document.getElementById('followupForm');
        if (form) form.reset();
    }

    function saveFollowup() {
        const formData = new FormData(document.getElementById('followupForm'));
        const isEdit = currentEditingFollowupId !== null;

        const url = isEdit ? `/followups/${currentEditingFollowupId}` : '/followups';
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
                closeFollowupModal();
                loadFollowups(); // Reload data
            } else if (data.errors) {
                showNotification('Please check the form and try again.', 'error');
            }
        })
        .catch(error => {
            console.error('Error saving followup:', error);
            showNotification('Error saving followup. Please try again.', 'error');
        });
    }

    function closeFollowupModal() {
        const modal = document.getElementById('followupModal');
        if (modal) {
            modal.classList.remove('show');
            resetFollowupForm();
        }
    }

    // Global functions for table actions
    window.editFollowup = function(id) {
        const followup = followups.find(f => f.id === id);
        if (followup) {
            openFollowupModal(followup);
        }
    };

    window.viewFollowup = function(id) {
        const followup = followups.find(f => f.id === id);
        if (followup) {
            openViewFollowupModal(followup);
        }
    };

    window.deleteFollowup = function(id) {
        if (confirm('Are you sure you want to delete this followup? This action cannot be undone.')) {
            fetch(`/followups/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                showNotification(data.message, 'success');
                loadFollowups(); // Reload data
            })
            .catch(error => {
                console.error('Error deleting followup:', error);
                showNotification('Error deleting followup. Please try again.', 'error');
            });
        }
    };

    function openViewFollowupModal(followup) {
        const modal = document.getElementById('viewFollowupModal');
        if (!modal || !followup) return;
        
        // Populate view modal with followup data
        document.getElementById('viewPolicyNumber').textContent = followup.policyNumber || 'N/A';
        document.getElementById('viewCustomerName').textContent = followup.customerName || 'N/A';
        document.getElementById('viewCustomerPhone').textContent = followup.phone || 'N/A';
        document.getElementById('viewCustomerEmail').textContent = followup.email || 'N/A';
        document.getElementById('viewFollowupDate').textContent = followup.followupDate || 'N/A';
        document.getElementById('viewFollowupType').textContent = followup.followupType || 'N/A';
        document.getElementById('viewStatus').textContent = followup.status || 'N/A';
        document.getElementById('viewAgentName').textContent = followup.agentName || 'N/A';
        document.getElementById('viewNotes').textContent = followup.notes || 'N/A';
        
        modal.classList.add('show');
    }

    function closeViewFollowupModal() {
        const modal = document.getElementById('viewFollowupModal');
        if (modal) {
            modal.classList.remove('show');
        }
    }

    function exportFollowupsData() {
        const csvContent = generateFollowupsCSV();
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', `followups_${new Date().toISOString().split('T')[0]}.csv`);
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function generateFollowupsCSV() {
        const headers = ['ID', 'Policy Number', 'Customer Name', 'Phone', 'Email', 'Followup Date', 'Type', 'Status', 'Agent', 'Notes'];
        const rows = filteredFollowups.map(followup => [
            followup.id,
            followup.policyNumber,
            followup.customerName,
            followup.phone,
            followup.email,
            followup.followupDate,
            followup.followupType,
            followup.status,
            followup.agentName,
            followup.notes
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
        const followupModal = document.getElementById('followupModal');
        const viewFollowupModal = document.getElementById('viewFollowupModal');
        
        if (event.target === followupModal) {
            closeFollowupModal();
        }
        if (event.target === viewFollowupModal) {
            closeViewFollowupModal();
        }
    });
</script>
@endpush

@endsection
