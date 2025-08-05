// Global variables
let currentPage = 1;
let rowsPerPage = 10;
let currentSort = { column: 'id', direction: 'asc' };
let filteredData = [];
let allPolicies = [];
let allAgents = [];

// Policies page variables
let policiesCurrentPage = 1;
let policiesRowsPerPage = 10;
let policiesCurrentSort = { column: 'id', direction: 'asc' };
let policiesFilteredData = [];

// Renewals page variables
let renewalsCurrentPage = 1;
let renewalsRowsPerPage = 10;
let renewalsCurrentSort = { column: 'id', direction: 'asc' };
let renewalsFilteredData = [];
let allRenewals = [];

// Follow-ups page variables
let followupsCurrentPage = 1;
let followupsRowsPerPage = 10;
let followupsCurrentSort = { column: 'id', direction: 'asc' };
let followupsFilteredData = [];
let allFollowups = [];

// Reports page variables
let reportCharts = {};
let currentReportType = 'all';
let reportDateRange = { start: null, end: null };

// Multi-step modal variables
let currentStep = 1;
let selectedPolicyType = '';
let selectedBusinessType = '';

// Debounce function for search inputs
const debounce = (func, wait) => {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
};

// Company name mapping (Full Name -> Short Name)
const companyNameMapping = {
    // Life Insurance Companies
    'Life Insurance Corporation of India': 'LIC',
    'HDFC Life Insurance Co. Ltd.': 'HDFC Life',
    'ICICI Prudential Life Insurance Co. Ltd.': 'ICICI Life',
    'SBI Life Insurance Co. Ltd.': 'SBI Life',
    'Max Life Insurance Co. Ltd.': 'Max Life',
    'Bajaj Allianz Life Insurance Co. Ltd.': 'Bajaj Life',
    'Kotak Mahindra Life Insurance Co. Ltd.': 'Kotak Life',
    'Aditya Birla Sun Life Insurance Co. Ltd.': 'AB Sun Life',
    'PNB MetLife India Insurance Co. Ltd.': 'PNB MetLife',
    'Tata AIA Life Insurance Co. Ltd.': 'Tata AIA',
    'Reliance Nippon Life Insurance Co. Ltd.': 'Reliance Life',
    'Canara HSBC Life Insurance Co. Ltd.': 'Canara HSBC Life',
    'Edelweiss Tokio Life Insurance Co. Ltd.': 'Edelweiss Life',
    'IndiaFirst Life Insurance Co. Ltd.': 'IndiaFirst',
    'Future Generali India Life Insurance Co. Ltd.': 'Future Life',
    'Star Union Dai-ichi Life Insurance Co. Ltd.': 'SUD Life',
    'Shriram Life Insurance Co. Ltd.': 'Shriram Life',
    'Sahara India Life Insurance Co. Ltd.': 'Sahara Life',
    'Aegon Life Insurance Co. Ltd.': 'Aegon Life',
    'Pramerica Life Insurance Co. Ltd.': 'Pramerica',
    'Aviva Life Insurance Co. Ltd.': 'Aviva Life',
    'Ageas Federal Life Insurance Co. Ltd.': 'Ageas Life',
    'Exide Life Insurance Co. Ltd.': 'Exide Life (old)',
    'Bharti AXA Life Insurance Co. Ltd.': 'Bharti AXA Life (old)',
    
    // General Insurance Companies
    'The New India Assurance Co. Ltd.': 'New India',
    'United India Insurance Co. Ltd.': 'United India',
    'National Insurance Co. Ltd.': 'National',
    'The Oriental Insurance Co. Ltd.': 'Oriental',
    'ICICI Lombard General Insurance Co. Ltd.': 'ICICI GI',
    'Bajaj Allianz General Insurance Co. Ltd.': 'Bajaj GI',
    'HDFC ERGO General Insurance Co. Ltd.': 'HDFC ERGO',
    'Tata AIG General Insurance Co. Ltd.': 'Tata AIG',
    'Reliance General Insurance Co. Ltd.': 'Reliance GI',
    'SBI General Insurance Co. Ltd.': 'SBI GI',
    'IFFCO Tokio General Insurance Co. Ltd.': 'IFFCO Tokio',
    'Future Generali India Insurance Co. Ltd.': 'Future GI',
    'Kotak Mahindra General Insurance Co. Ltd.': 'Kotak GI',
    'Cholamandalam MS General Insurance Co. Ltd.': 'Chola MS',
    'Magma HDI General Insurance Co. Ltd.': 'Magma HDI',
    'Zuno General Insurance Ltd.': 'Zuno GI',
    'Liberty General Insurance Ltd.': 'Liberty GI',
    'Royal Sundaram General Insurance Co. Ltd.': 'Royal Sundaram',
    'Shriram General Insurance Co. Ltd.': 'Shriram GI',
    'Universal Sompo General Insurance Co. Ltd.': 'Universal Sompo',
    'Go Digit General Insurance Ltd.': 'Go Digit',
    'Raheja QBE General Insurance Co. Ltd.': 'Raheja QBE',
    'ACKO General Insurance Ltd.': 'ACKO',
    'Navi General Insurance Ltd.': 'Navi GI',
    'Aditya Birla General Insurance Ltd.': 'AB General',
    
    // Health Insurance Companies
    'Star Health and Allied Insurance Co. Ltd.': 'Star Health',
    'Niva Bupa Health Insurance Co. Ltd.': 'Niva Bupa',
    'Care Health Insurance Ltd.': 'Care Health',
    'ManipalCigna Health Insurance Co. Ltd.': 'ManipalCigna',
    'Aditya Birla Health Insurance Co. Ltd.': 'AB Health'
};

// Helper function to get short company name
const getShortCompanyName = (fullName) => {
    return companyNameMapping[fullName] || fullName;
};

// Dummy data for policies
const generateDummyPolicies = () => {
    const companies = [
        // Life Insurance Companies
        'Life Insurance Corporation of India', 'HDFC Life Insurance Co. Ltd.', 
        'ICICI Prudential Life Insurance Co. Ltd.', 'SBI Life Insurance Co. Ltd.',
        'Max Life Insurance Co. Ltd.', 'Bajaj Allianz Life Insurance Co. Ltd.',
        'Kotak Mahindra Life Insurance Co. Ltd.', 'Aditya Birla Sun Life Insurance Co. Ltd.',
        'PNB MetLife India Insurance Co. Ltd.', 'Tata AIA Life Insurance Co. Ltd.',
        'Reliance Nippon Life Insurance Co. Ltd.', 'Canara HSBC Life Insurance Co. Ltd.',
        'Edelweiss Tokio Life Insurance Co. Ltd.', 'IndiaFirst Life Insurance Co. Ltd.',
        'Future Generali India Life Insurance Co. Ltd.', 'Star Union Dai-ichi Life Insurance Co. Ltd.',
        'Shriram Life Insurance Co. Ltd.', 'Sahara India Life Insurance Co. Ltd.',
        'Aegon Life Insurance Co. Ltd.', 'Pramerica Life Insurance Co. Ltd.',
        'Aviva Life Insurance Co. Ltd.', 'Ageas Federal Life Insurance Co. Ltd.',
        'Exide Life Insurance Co. Ltd.', 'Bharti AXA Life Insurance Co. Ltd.',
        
        // General Insurance Companies
        'The New India Assurance Co. Ltd.', 'United India Insurance Co. Ltd.',
        'National Insurance Co. Ltd.', 'The Oriental Insurance Co. Ltd.',
        'ICICI Lombard General Insurance Co. Ltd.', 'Bajaj Allianz General Insurance Co. Ltd.',
        'HDFC ERGO General Insurance Co. Ltd.', 'Tata AIG General Insurance Co. Ltd.',
        'Reliance General Insurance Co. Ltd.', 'SBI General Insurance Co. Ltd.',
        'IFFCO Tokio General Insurance Co. Ltd.', 'Future Generali India Insurance Co. Ltd.',
        'Kotak Mahindra General Insurance Co. Ltd.', 'Cholamandalam MS General Insurance Co. Ltd.',
        'Magma HDI General Insurance Co. Ltd.', 'Zuno General Insurance Ltd.',
        'Liberty General Insurance Ltd.', 'Royal Sundaram General Insurance Co. Ltd.',
        'Shriram General Insurance Co. Ltd.', 'Universal Sompo General Insurance Co. Ltd.',
        'Go Digit General Insurance Ltd.', 'Raheja QBE General Insurance Co. Ltd.',
        'ACKO General Insurance Ltd.', 'Navi General Insurance Ltd.',
        'Aditya Birla General Insurance Ltd.',
        
        // Health Insurance Companies
        'Star Health and Allied Insurance Co. Ltd.', 'Niva Bupa Health Insurance Co. Ltd.',
        'Care Health Insurance Ltd.', 'ManipalCigna Health Insurance Co. Ltd.',
        'Aditya Birla Health Insurance Co. Ltd.'
    ];
    
    const vehicleTypes = ['Car', 'Bike', 'Auto', 'Bus', 'Lorry', 'Truck', 'Tractor'];
    const insuranceTypes = ['Comprehensive', 'Stand Alone OD', 'Third Party'];
    const businessTypes = ['Self', 'Agent1', 'Agent2'];
    const customerPaidStatus = ['Yes', 'No', 'Partial'];
    const policyTypes = ['Motor', 'Health', 'Life'];
    const statuses = ['Active', 'Expired', 'Pending'];
    
    const policies = [];
    
    for (let i = 1; i <= 50; i++) {
        const startDate = new Date();
        startDate.setDate(startDate.getDate() - Math.floor(Math.random() * 365));
        const endDate = new Date(startDate);
        endDate.setFullYear(endDate.getFullYear() + 1);
        endDate.setDate(endDate.getDate() - 1);
        
        const premium = Math.floor(Math.random() * 50000) + 5000;
        const revenue = Math.floor(premium * 0.15) + Math.floor(Math.random() * 5000);
        
        policies.push({
            id: i,
            type: policyTypes[Math.floor(Math.random() * policyTypes.length)],
            owner: `Customer ${i}`,
            vehicle: `${vehicleTypes[Math.floor(Math.random() * vehicleTypes.length)]} - ${Math.random().toString(36).substr(2, 8).toUpperCase()}`,
            startDate: startDate.toISOString().split('T')[0],
            endDate: endDate.toISOString().split('T')[0],
            premium: premium,
            revenue: revenue,
            company: companies[Math.floor(Math.random() * companies.length)],
            insuranceType: insuranceTypes[Math.floor(Math.random() * insuranceTypes.length)],
            businessType: businessTypes[Math.floor(Math.random() * businessTypes.length)],
            customerPaid: customerPaidStatus[Math.floor(Math.random() * customerPaidStatus.length)],
            phone: `+91${Math.floor(Math.random() * 9000000000) + 1000000000}`,
            email: `customer${i}@example.com`,
            status: statuses[Math.floor(Math.random() * statuses.length)]
        });
    }
    
    return policies;
};

// Initialize demo data
const initializeDemoData = () => {
    allPolicies = generateDummyPolicies();
    policiesFilteredData = [...allPolicies];
};

// Initialize policies page
const initializePoliciesPage = () => {
    updatePoliciesStats();
    updatePoliciesTable();
    updatePoliciesPagination();
    
    // Attach event listeners
    $('#policyTypeFilter, #statusFilter').change(filterPoliciesData);
    $('#policiesSearch').on('input', debounce(filterPoliciesData, 300));
    $('#policiesRowsPerPage').change(function() {
        policiesRowsPerPage = parseInt($(this).val());
        policiesCurrentPage = 1;
        updatePoliciesTable();
        updatePoliciesPagination();
    });
    
    $('#policiesPrevPage').click(() => {
        if (policiesCurrentPage > 1) {
            policiesCurrentPage--;
            updatePoliciesTable();
            updatePoliciesPagination();
        }
    });
    
    $('#policiesNextPage').click(() => {
        const totalPages = Math.ceil(policiesFilteredData.length / policiesRowsPerPage);
        if (policiesCurrentPage < totalPages) {
            policiesCurrentPage++;
            updatePoliciesTable();
            updatePoliciesPagination();
        }
    });
    
    // Table sorting
    $('#policiesPageTable th[data-sort]').click(function() {
        const column = $(this).data('sort');
        if (policiesCurrentSort.column === column) {
            policiesCurrentSort.direction = policiesCurrentSort.direction === 'asc' ? 'desc' : 'asc';
        } else {
            policiesCurrentSort.column = column;
            policiesCurrentSort.direction = 'asc';
        }
        sortPoliciesData();
        updatePoliciesTable();
        updatePoliciesPagination();
    });
    
    // Modal event handlers
    $('#addPolicyBtn').click(openPolicyModal);
    $('#closePolicyModal').click(closePolicyModal);
    $('#policyForm').submit(handlePolicySubmit);
    
    // Step navigation handlers
    $('#nextStep1').click(() => goToStep(2));
    $('#nextStep2').click(() => goToStep(3));
    $('#prevStep2').click(() => goToStep(1));
    $('#prevStep3').click(() => goToStep(2));
    
    // Policy type selection handler
    $('#policyTypeSelect').change(function() {
        selectedPolicyType = $(this).val();
        $('#nextStep1').prop('disabled', !selectedPolicyType);
    });
    
    // Business type selection handler
    $('#businessTypeSelect').change(function() {
        selectedBusinessType = $(this).val();
        $('#nextStep2').prop('disabled', !selectedBusinessType);
    });
};

// Update policies statistics
const updatePoliciesStats = () => {
    const activePolicies = allPolicies.filter(p => p.status === 'Active').length;
    const expiredPolicies = allPolicies.filter(p => p.status === 'Expired').length;
    const pendingPolicies = allPolicies.filter(p => p.status === 'Pending').length;
    
    $('#activePoliciesCount').text(activePolicies);
    $('#expiredPoliciesCount').text(expiredPolicies);
    $('#pendingRenewalsCount').text(pendingPolicies);
    $('#totalPoliciesCount').text(allPolicies.length);
};

// Update policies table
const updatePoliciesTable = () => {
    const startIndex = (policiesCurrentPage - 1) * policiesRowsPerPage;
    const endIndex = Math.min(startIndex + policiesRowsPerPage, policiesFilteredData.length);
    const pageData = policiesFilteredData.slice(startIndex, endIndex);
    
    const tbody = $('#policiesPageTableBody');
    tbody.empty();
    
    pageData.forEach((policy, index) => {
        const row = `
            <tr>
                <td>${startIndex + index + 1}</td>
                <td><span class="policy-type ${policy.type.toLowerCase()}">${policy.type}</span></td>
                <td>${policy.owner}</td>
                <td>${policy.phone}</td>
                <td title="${policy.company}">${getShortCompanyName(policy.company)}</td>
                <td>${new Date(policy.endDate).toLocaleDateString()}</td>
                <td>₹${policy.premium.toLocaleString()}</td>
                <td><span class="status ${policy.status.toLowerCase()}">${policy.status}</span></td>
                <td>
                    <div class="action-buttons">
                        <button class="action-btn view" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="action-btn edit" title="Edit Policy">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="action-btn delete" title="Delete Policy" onclick="deletePolicy(${policy.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        tbody.append(row);
    });
};

// Update policies pagination
const updatePoliciesPagination = () => {
    const totalPages = Math.ceil(policiesFilteredData.length / policiesRowsPerPage);
    
    // Update pagination controls
    $('#policiesPrevPage').prop('disabled', policiesCurrentPage === 1);
    $('#policiesNextPage').prop('disabled', policiesCurrentPage === totalPages);
    
    // Update pagination info
    const startRecord = policiesFilteredData.length === 0 ? 0 : (policiesCurrentPage - 1) * policiesRowsPerPage + 1;
    const endRecord = Math.min(policiesCurrentPage * policiesRowsPerPage, policiesFilteredData.length);
    
    $('#policiesStartRecord').text(startRecord);
    $('#policiesEndRecord').text(endRecord);
    $('#policiesTotalRecords').text(policiesFilteredData.length);
    
    // Update page numbers
    const pageNumbers = $('#policiesPageNumbers');
    pageNumbers.empty();
    
    const maxVisiblePages = 5;
    let startPage = Math.max(1, policiesCurrentPage - Math.floor(maxVisiblePages / 2));
    let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
    
    if (endPage - startPage + 1 < maxVisiblePages) {
        startPage = Math.max(1, endPage - maxVisiblePages + 1);
    }
    
    for (let i = startPage; i <= endPage; i++) {
        const pageBtn = $(`<button class="page-number ${i === policiesCurrentPage ? 'active' : ''}">${i}</button>`);
        pageBtn.click(() => {
            policiesCurrentPage = i;
            updatePoliciesTable();
            updatePoliciesPagination();
        });
        pageNumbers.append(pageBtn);
    }
};

// Filter policies data
const filterPoliciesData = () => {
    const typeFilter = $('#policyTypeFilter').val();
    const statusFilter = $('#statusFilter').val();
    const searchFilter = $('#policiesSearch').val().toLowerCase();
    
    policiesFilteredData = allPolicies.filter(policy => {
        const matchesType = !typeFilter || policy.type === typeFilter;
        const matchesStatus = !statusFilter || policy.status === statusFilter;
        const matchesSearch = !searchFilter || 
            policy.owner.toLowerCase().includes(searchFilter) ||
            policy.phone.includes(searchFilter) ||
            policy.type.toLowerCase().includes(searchFilter) ||
            getShortCompanyName(policy.company).toLowerCase().includes(searchFilter);
        
        return matchesType && matchesStatus && matchesSearch;
    });
    
    policiesCurrentPage = 1;
    updatePoliciesTable();
    updatePoliciesPagination();
};

// Sort policies data
const sortPoliciesData = () => {
    policiesFilteredData.sort((a, b) => {
        let valueA, valueB;
        
        switch (policiesCurrentSort.column) {
            case 'id':
                valueA = a.id;
                valueB = b.id;
                break;
            case 'type':
                valueA = a.type;
                valueB = b.type;
                break;
            case 'owner':
                valueA = a.owner;
                valueB = b.owner;
                break;
            case 'phone':
                valueA = a.phone;
                valueB = b.phone;
                break;
            case 'company':
                valueA = getShortCompanyName(a.company);
                valueB = getShortCompanyName(b.company);
                break;
            case 'endDate':
                valueA = new Date(a.endDate);
                valueB = new Date(b.endDate);
                break;
            case 'premium':
                valueA = a.premium;
                valueB = b.premium;
                break;
            case 'status':
                valueA = a.status;
                valueB = b.status;
                break;
            default:
                return 0;
        }
        
        if (valueA < valueB) return policiesCurrentSort.direction === 'asc' ? -1 : 1;
        if (valueA > valueB) return policiesCurrentSort.direction === 'asc' ? 1 : -1;
        return 0;
    });
    
    // Update sort indicators
    $('#policiesPageTable th[data-sort] i').removeClass('fa-sort-up fa-sort-down').addClass('fa-sort');
    
    const currentHeader = $(`#policiesPageTable th[data-sort="${policiesCurrentSort.column}"]`);
    const icon = currentHeader.find('i');
    icon.removeClass('fa-sort').addClass(policiesCurrentSort.direction === 'asc' ? 'fa-sort-up' : 'fa-sort-down');
};

// Delete policy function
const deletePolicy = (id) => {
    if (confirm('Are you sure you want to delete this policy?')) {
        allPolicies = allPolicies.filter(p => p.id !== id);
        policiesFilteredData = policiesFilteredData.filter(p => p.id !== id);
        updatePoliciesStats();
        updatePoliciesTable();
        updatePoliciesPagination();
        showNotification('Policy deleted successfully!', 'success');
    }
};

// Show notification function
const showNotification = (message, type = 'info') => {
    // Simple notification - you can enhance this
    alert(message);
};

// Initialize everything when document is ready
$(document).ready(function() {
    initializeDemoData();
    
    // Check if we're on the policies page
    if ($('#policies').length > 0) {
        initializePoliciesPage();
    }
});

// Insurance Management System 2.0 - Basic functionality

document.addEventListener('DOMContentLoaded', function() {
    // Theme toggle functionality
    const themeToggle = document.getElementById('themeToggle');
    const body = document.body;
    
    // Load saved theme
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
        body.classList.toggle('dark-theme', savedTheme === 'dark');
    }
    
    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            body.classList.toggle('dark-theme');
            const isDark = body.classList.contains('dark-theme');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        });
    }
    
    // Sidebar toggle functionality
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
        });
    }
    
    // Profile dropdown functionality
    const profileBtn = document.querySelector('.profile-btn');
    const dropdownMenu = document.querySelector('.dropdown-menu');
    
    if (profileBtn && dropdownMenu) {
        profileBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdownMenu.classList.toggle('show');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function() {
            dropdownMenu.classList.remove('show');
        });
        
        dropdownMenu.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
    
    // Navigation active state
    const navItems = document.querySelectorAll('.nav-item');
    const currentPath = window.location.pathname;
    
    navItems.forEach(item => {
        const link = item.querySelector('a');
        if (link && link.getAttribute('href') === currentPath) {
            item.classList.add('active');
        }
    });
    
    // Data table functionality
    initializeDataTables();
    
    // Chart initialization
    initializeCharts();
    
    // Search functionality
    initializeSearch();
    
    // Pagination
    initializePagination();
});

// Data table functionality
function initializeDataTables() {
    const searchInputs = document.querySelectorAll('.search-box input');
    const rowsPerPageSelects = document.querySelectorAll('.rows-per-page');
    
    searchInputs.forEach(input => {
        input.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const table = this.closest('.data-table-container').querySelector('.data-table');
            const rows = table.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    });
    
    rowsPerPageSelects.forEach(select => {
        select.addEventListener('change', function() {
            const rowsToShow = parseInt(this.value);
            const table = this.closest('.data-table-container').querySelector('.data-table');
            const rows = table.querySelectorAll('tbody tr');
            
            rows.forEach((row, index) => {
                row.style.display = index < rowsToShow ? '' : 'none';
            });
        });
    });
    
    // Table sorting
    const sortableHeaders = document.querySelectorAll('.data-table th[data-sort]');
    sortableHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const table = this.closest('table');
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            const column = this.dataset.sort;
            const currentOrder = this.dataset.order || 'asc';
            const newOrder = currentOrder === 'asc' ? 'desc' : 'asc';
            
            rows.sort((a, b) => {
                const aVal = a.querySelector(`td[data-${column}]`)?.textContent || '';
                const bVal = b.querySelector(`td[data-${column}]`)?.textContent || '';
                
                if (newOrder === 'asc') {
                    return aVal.localeCompare(bVal);
                } else {
                    return bVal.localeCompare(aVal);
                }
            });
            
            rows.forEach(row => tbody.appendChild(row));
            this.dataset.order = newOrder;
        });
    });
}

// Chart initialization
function initializeCharts() {
    // Monthly Revenue Chart
    const revenueCtx = document.getElementById('monthlyRevenueChart');
    if (revenueCtx) {
        new Chart(revenueCtx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Revenue',
                    data: [65000, 59000, 80000, 81000, 56000, 85000, 72000, 88000, 79000, 92000, 87000, 95000],
                    backgroundColor: 'rgba(79, 70, 229, 0.8)',
                    borderColor: 'rgba(79, 70, 229, 1)',
                    borderWidth: 1,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
    
    // Policy Distribution Chart
    const policyCtx = document.getElementById('policyDistributionChart');
    if (policyCtx) {
        new Chart(policyCtx, {
            type: 'doughnut',
            data: {
                labels: ['Life Insurance', 'Health Insurance', 'Vehicle Insurance', 'Property Insurance'],
                datasets: [{
                    data: [35, 25, 25, 15],
                    backgroundColor: [
                        '#4F46E5',
                        '#10B981',
                        '#F59E0B',
                        '#EF4444'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    }
                }
            }
        });
    }
}

// Search functionality
function initializeSearch() {
    const searchButtons = document.querySelectorAll('.search-btn');
    searchButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const searchInput = this.parentElement.querySelector('input');
            if (searchInput) {
                searchInput.focus();
            }
        });
    });
}

// Pagination functionality
function initializePagination() {
    const paginationBtns = document.querySelectorAll('.pagination-btn');
    const pageNumbers = document.querySelectorAll('.page-number');
    
    paginationBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const action = this.dataset.action;
            const table = this.closest('.data-table-container').querySelector('.data-table');
            
            // Pagination logic here
            console.log('Pagination action:', action);
        });
    });
    
    pageNumbers.forEach(number => {
        number.addEventListener('click', function() {
            const page = this.dataset.page;
            
            // Remove active class from all page numbers
            pageNumbers.forEach(n => n.classList.remove('active'));
            
            // Add active class to clicked page
            this.classList.add('active');
            
            console.log('Navigate to page:', page);
        });
    });
}

// Utility functions
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    
    // Style the notification
    Object.assign(notification.style, {
        position: 'fixed',
        top: '20px',
        right: '20px',
        padding: '16px 24px',
        borderRadius: '12px',
        color: 'white',
        fontWeight: '500',
        zIndex: '9999',
        transform: 'translateX(100%)',
        transition: 'transform 0.3s ease'
    });
    
    // Set background color based on type
    const colors = {
        success: '#10B981',
        error: '#EF4444',
        warning: '#F59E0B',
        info: '#4F46E5'
    };
    notification.style.backgroundColor = colors[type] || colors.info;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Show notification
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Remove notification after 3 seconds
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}

// Export functionality
function exportTable(format = 'csv') {
    const tables = document.querySelectorAll('.data-table');
    if (tables.length === 0) return;
    
    const table = tables[0]; // Export first table found
    const rows = table.querySelectorAll('tr');
    let data = [];
    
    rows.forEach(row => {
        const cols = row.querySelectorAll('th, td');
        const rowData = Array.from(cols).map(col => col.textContent.trim());
        data.push(rowData);
    });
    
    if (format === 'csv') {
        const csv = data.map(row => row.join(',')).join('\n');
        const blob = new Blob([csv], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'export.csv';
        a.click();
        window.URL.revokeObjectURL(url);
    }
    
    showNotification('Data exported successfully!', 'success');
}

// Dummy data for renewals
const generateDummyRenewals = () => {
    const statuses = ['Pending', 'In Progress', 'Completed', 'Overdue'];
    const priorities = ['High', 'Medium', 'Low'];
    const agentNames = ['Rajesh Kumar', 'Priya Sharma', 'Amit Patel', 'Sneha Singh', 'Vikram Malhotra'];
    
    const renewals = [];
    
    for (let i = 1; i <= 30; i++) {
        const policy = allPolicies[Math.floor(Math.random() * allPolicies.length)];
        const expiryDate = new Date(policy.endDate);
        const today = new Date();
        const daysLeft = Math.ceil((expiryDate - today) / (1000 * 60 * 60 * 24));
        
        // Determine status based on days left
        let status = 'Pending';
        if (daysLeft < 0) {
            status = 'Overdue';
        } else if (daysLeft <= 7) {
            status = 'In Progress';
        } else if (Math.random() > 0.8) {
            status = 'Completed';
        }
        
        // Determine priority based on days left
        let priority = 'Low';
        if (daysLeft < 0 || daysLeft <= 7) {
            priority = 'High';
        } else if (daysLeft <= 30) {
            priority = 'Medium';
        }
        
        renewals.push({
            id: i,
            policyId: policy.id,
            customerName: policy.owner,
            policyType: policy.type,
            expiryDate: policy.endDate,
            daysLeft: daysLeft,
            status: status,
            priority: priority,
            assignedTo: agentNames[Math.floor(Math.random() * agentNames.length)],
            reminderDate: new Date(expiryDate.getTime() - (30 * 24 * 60 * 60 * 1000)).toISOString().split('T')[0],
            notes: `Renewal reminder for ${policy.owner}'s ${policy.type} policy`,
            emailNotification: Math.random() > 0.3,
            smsNotification: Math.random() > 0.5,
            notificationDays: Math.floor(Math.random() * 30) + 15,
            createdAt: new Date(Date.now() - Math.random() * 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0]
        });
    }
    
    return renewals;
};

// Dummy data for follow-ups
const generateDummyFollowups = () => {
    const statuses = ['Pending', 'In Progress', 'Completed', 'No Response', 'Not Interested'];
    const types = ['Renewal', 'New Policy', 'Claim', 'General'];
    const telecallerNames = ['Rajesh Kumar', 'Priya Sharma', 'Amit Patel', 'Sneha Singh', 'Vikram Malhotra'];
    
    const followups = [];
    
    for (let i = 1; i <= 25; i++) {
        const policy = allPolicies[Math.floor(Math.random() * allPolicies.length)];
        const today = new Date();
        const lastFollowupDate = new Date(today);
        lastFollowupDate.setDate(today.getDate() - Math.floor(Math.random() * 30));
        
        const nextFollowupDate = new Date(lastFollowupDate);
        nextFollowupDate.setDate(lastFollowupDate.getDate() + Math.floor(Math.random() * 7) + 1);
        
        followups.push({
            id: i,
            customerName: policy.owner,
            phone: policy.phone,
            followupType: types[Math.floor(Math.random() * types.length)],
            status: statuses[Math.floor(Math.random() * statuses.length)],
            assignedTo: telecallerNames[Math.floor(Math.random() * telecallerNames.length)],
            lastFollowupDate: lastFollowupDate.toISOString().split('T')[0],
            nextFollowupDate: nextFollowupDate.toISOString().split('T')[0],
            recentNote: `Customer contacted regarding ${types[Math.floor(Math.random() * types.length)].toLowerCase()}. Follow-up required.`,
            priority: Math.random() > 0.5 ? 'High' : 'Medium',
            createdAt: new Date(Date.now() - Math.random() * 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0]
        });
    }
    
    return followups;
};

// Dummy data for agents
const generateDummyAgents = () => {
    const agents = [
        { id: 1, name: 'Rajesh Kumar', phone: '+919876543210', email: 'rajesh@example.com', userId: 'AG001', status: 'Active', performance: 95 },
        { id: 2, name: 'Priya Sharma', phone: '+919876543211', email: 'priya@example.com', userId: 'AG002', status: 'Active', performance: 87 },
        { id: 3, name: 'Amit Patel', phone: '+919876543212', email: 'amit@example.com', userId: 'AG003', status: 'Active', performance: 92 },
        { id: 4, name: 'Sneha Singh', phone: '+919876543213', email: 'sneha@example.com', userId: 'AG004', status: 'Inactive', performance: 78 },
        { id: 5, name: 'Vikram Malhotra', phone: '+919876543214', email: 'vikram@example.com', userId: 'AG005', status: 'Active', performance: 89 }
    ];
    
    // Add policy counts for each agent
    agents.forEach(agent => {
        agent.policies = allPolicies.filter(p => p.businessType === agent.name || Math.random() > 0.7).length;
    });
    
    return agents;
};

// Initialize renewals page
const initializeRenewalsPage = () => {
    if (!allRenewals.length) {
        allRenewals = generateDummyRenewals();
        renewalsFilteredData = [...allRenewals];
    }
    
    updateRenewalsStats();
    updateRenewalsTable();
    updateRenewalsPagination();
    
    // Attach event listeners
    $('#renewalStatusFilter, #renewalPriorityFilter').change(filterRenewalsData);
    $('#renewalsSearch').on('input', debounce(filterRenewalsData, 300));
    $('#renewalsRowsPerPage').change(function() {
        renewalsRowsPerPage = parseInt($(this).val());
        renewalsCurrentPage = 1;
        updateRenewalsTable();
        updateRenewalsPagination();
    });
    
    $('#renewalsPrevPage').click(() => {
        if (renewalsCurrentPage > 1) {
            renewalsCurrentPage--;
            updateRenewalsTable();
            updateRenewalsPagination();
        }
    });
    
    $('#renewalsNextPage').click(() => {
        const totalPages = Math.ceil(renewalsFilteredData.length / renewalsRowsPerPage);
        if (renewalsCurrentPage < totalPages) {
            renewalsCurrentPage++;
            updateRenewalsTable();
            updateRenewalsPagination();
        }
    });
    
    // Modal event handlers
    $('#addRenewalBtn').click(openRenewalModal);
    $('#closeRenewalModal, #cancelRenewal').click(closeRenewalModal);
    $('#renewalForm').submit(handleRenewalSubmit);
    
    // Policy selection change handler
    $('#renewalPolicyId').change(function() {
        const policyId = parseInt($(this).val());
        if (policyId) {
            const policy = allPolicies.find(p => p.id === policyId);
            if (policy) {
                $('#renewalCustomerName').val(policy.owner);
                $('#renewalPolicyType').val(policy.type);
                $('#renewalExpiryDate').val(policy.endDate);
            }
        } else {
            $('#renewalCustomerName').val('');
            $('#renewalPolicyType').val('');
            $('#renewalExpiryDate').val('');
        }
    });
};

// Initialize followups page
const initializeFollowupsPage = () => {
    if (!allFollowups.length) {
        allFollowups = generateDummyFollowups();
        followupsFilteredData = [...allFollowups];
    }
    
    updateFollowupsStats();
    updateFollowupsTable();
    updateFollowupsPagination();
    
    // Attach event listeners
    $('#followupStatusFilter, #followupTypeFilter').change(filterFollowupsData);
    $('#followupsSearch').on('input', debounce(filterFollowupsData, 300));
    $('#followupsRowsPerPage').change(function() {
        followupsRowsPerPage = parseInt($(this).val());
        followupsCurrentPage = 1;
        updateFollowupsTable();
        updateFollowupsPagination();
    });
    
    $('#followupsPrevPage').click(() => {
        if (followupsCurrentPage > 1) {
            followupsCurrentPage--;
            updateFollowupsTable();
            updateFollowupsPagination();
        }
    });
    
    $('#followupsNextPage').click(() => {
        const totalPages = Math.ceil(followupsFilteredData.length / followupsRowsPerPage);
        if (followupsCurrentPage < totalPages) {
            followupsCurrentPage++;
            updateFollowupsTable();
            updateFollowupsPagination();
        }
    });
    
    // Modal event handlers
    $('#addFollowupBtn').click(openFollowupModal);
    $('#closeFollowupModal, #cancelFollowup').click(closeFollowupModal);
    $('#followupForm').submit(handleFollowupSubmit);
};

// Initialize agents page
const initializeAgentsPage = () => {
    if (!allAgents.length) {
        allAgents = generateDummyAgents();
    }
    
    updateAgentsStats();
    updateAgentsTable();
    
    // Attach event listeners
    $('#agentsSearch').on('input', debounce(filterAgentsData, 300));
    $('#agentsRowsPerPage').change(function() {
        agentsRowsPerPage = parseInt($(this).val());
        agentsCurrentPage = 1;
        updateAgentsTable();
        updateAgentsPagination();
    });
};

// Update functions for renewals
const updateRenewalsStats = () => {
    const pending = allRenewals.filter(r => r.status === 'Pending').length;
    const overdue = allRenewals.filter(r => r.status === 'Overdue').length;
    const completed = allRenewals.filter(r => r.status === 'Completed').length;
    
    $('#pendingRenewalsCount').text(pending);
    $('#overdueRenewalsCount').text(overdue);
    $('#completedRenewalsCount').text(completed);
    $('#totalRenewalsCount').text(allRenewals.length);
};

const updateRenewalsTable = () => {
    const startIndex = (renewalsCurrentPage - 1) * renewalsRowsPerPage;
    const endIndex = Math.min(startIndex + renewalsRowsPerPage, renewalsFilteredData.length);
    const pageData = renewalsFilteredData.slice(startIndex, endIndex);
    
    const tbody = $('#renewalsTableBody');
    tbody.empty();
    
    pageData.forEach((renewal, index) => {
        const row = `
            <tr>
                <td>${startIndex + index + 1}</td>
                <td>${renewal.policyId}</td>
                <td>${renewal.customerName}</td>
                <td><span class="policy-type ${renewal.policyType.toLowerCase()}">${renewal.policyType}</span></td>
                <td>${new Date(renewal.expiryDate).toLocaleDateString()}</td>
                <td><span class="days-left ${renewal.daysLeft < 0 ? 'overdue' : renewal.daysLeft <= 7 ? 'urgent' : ''}">${renewal.daysLeft < 0 ? 'Overdue' : renewal.daysLeft + ' days'}</span></td>
                <td><span class="status ${renewal.status.toLowerCase().replace(' ', '-')}">${renewal.status}</span></td>
                <td><span class="priority ${renewal.priority.toLowerCase()}">${renewal.priority}</span></td>
                <td>${renewal.assignedTo}</td>
                <td>
                    <div class="action-buttons">
                        <button class="action-btn view" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="action-btn edit" title="Edit Renewal">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="action-btn delete" title="Delete Renewal">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        tbody.append(row);
    });
};

const updateRenewalsPagination = () => {
    const totalPages = Math.ceil(renewalsFilteredData.length / renewalsRowsPerPage);
    
    $('#renewalsPrevPage').prop('disabled', renewalsCurrentPage === 1);
    $('#renewalsNextPage').prop('disabled', renewalsCurrentPage === totalPages);
    
    const startRecord = renewalsFilteredData.length === 0 ? 0 : (renewalsCurrentPage - 1) * renewalsRowsPerPage + 1;
    const endRecord = Math.min(renewalsCurrentPage * renewalsRowsPerPage, renewalsFilteredData.length);
    
    $('#renewalsStartRecord').text(startRecord);
    $('#renewalsEndRecord').text(endRecord);
    $('#renewalsTotalRecords').text(renewalsFilteredData.length);
};

const filterRenewalsData = () => {
    const statusFilter = $('#renewalStatusFilter').val();
    const priorityFilter = $('#renewalPriorityFilter').val();
    const searchFilter = $('#renewalsSearch').val().toLowerCase();
    
    renewalsFilteredData = allRenewals.filter(renewal => {
        const matchesStatus = !statusFilter || renewal.status === statusFilter;
        const matchesPriority = !priorityFilter || renewal.priority === priorityFilter;
        const matchesSearch = !searchFilter || 
            renewal.customerName.toLowerCase().includes(searchFilter) ||
            renewal.policyType.toLowerCase().includes(searchFilter) ||
            renewal.assignedTo.toLowerCase().includes(searchFilter);
        
        return matchesStatus && matchesPriority && matchesSearch;
    });
    
    renewalsCurrentPage = 1;
    updateRenewalsTable();
    updateRenewalsPagination();
};

// Update functions for followups
const updateFollowupsStats = () => {
    const pending = allFollowups.filter(f => f.status === 'Pending').length;
    const inProgress = allFollowups.filter(f => f.status === 'In Progress').length;
    const completedToday = allFollowups.filter(f => f.status === 'Completed' && new Date(f.lastFollowupDate).toDateString() === new Date().toDateString()).length;
    
    $('#pendingFollowupsCount').text(pending);
    $('#inProgressFollowupsCount').text(inProgress);
    $('#completedTodayCount').text(completedToday);
    $('#totalFollowupsCount').text(allFollowups.length);
};

const updateFollowupsTable = () => {
    const startIndex = (followupsCurrentPage - 1) * followupsRowsPerPage;
    const endIndex = Math.min(startIndex + followupsRowsPerPage, followupsFilteredData.length);
    const pageData = followupsFilteredData.slice(startIndex, endIndex);
    
    const tbody = $('#followupsTableBody');
    tbody.empty();
    
    pageData.forEach((followup, index) => {
        const row = `
            <tr>
                <td>${startIndex + index + 1}</td>
                <td>${followup.customerName}</td>
                <td>${followup.phone}</td>
                <td><span class="followup-type ${followup.followupType.toLowerCase().replace(' ', '-')}">${followup.followupType}</span></td>
                <td><span class="status ${followup.status.toLowerCase().replace(' ', '-')}">${followup.status}</span></td>
                <td>${followup.assignedTo}</td>
                <td>${new Date(followup.lastFollowupDate).toLocaleDateString()}</td>
                <td>${new Date(followup.nextFollowupDate).toLocaleDateString()}</td>
                <td><span class="note-preview" title="${followup.recentNote}">${followup.recentNote.substring(0, 30)}...</span></td>
                <td>
                    <div class="action-buttons">
                        <button class="action-btn view" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="action-btn edit" title="Edit Follow-up">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="action-btn delete" title="Delete Follow-up">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        tbody.append(row);
    });
};

const updateFollowupsPagination = () => {
    const totalPages = Math.ceil(followupsFilteredData.length / followupsRowsPerPage);
    
    $('#followupsPrevPage').prop('disabled', followupsCurrentPage === 1);
    $('#followupsNextPage').prop('disabled', followupsCurrentPage === totalPages);
    
    const startRecord = followupsFilteredData.length === 0 ? 0 : (followupsCurrentPage - 1) * followupsRowsPerPage + 1;
    const endRecord = Math.min(followupsCurrentPage * followupsRowsPerPage, followupsFilteredData.length);
    
    $('#followupsStartRecord').text(startRecord);
    $('#followupsEndRecord').text(endRecord);
    $('#followupsTotalRecords').text(followupsFilteredData.length);
};

const filterFollowupsData = () => {
    const statusFilter = $('#followupStatusFilter').val();
    const typeFilter = $('#followupTypeFilter').val();
    const searchFilter = $('#followupsSearch').val().toLowerCase();
    
    followupsFilteredData = allFollowups.filter(followup => {
        const matchesStatus = !statusFilter || followup.status === statusFilter;
        const matchesType = !typeFilter || followup.followupType === typeFilter;
        const matchesSearch = !searchFilter || 
            followup.customerName.toLowerCase().includes(searchFilter) ||
            followup.phone.includes(searchFilter) ||
            followup.assignedTo.toLowerCase().includes(searchFilter);
        
        return matchesStatus && matchesType && matchesSearch;
    });
    
    followupsCurrentPage = 1;
    updateFollowupsTable();
    updateFollowupsPagination();
};

// Update functions for agents
const updateAgentsStats = () => {
    const totalAgents = allAgents.length;
    const activeAgents = allAgents.filter(a => a.status === 'Active').length;
    const avgPerformance = Math.round(allAgents.reduce((sum, a) => sum + a.performance, 0) / totalAgents);
    const totalPolicies = allAgents.reduce((sum, a) => sum + a.policies, 0);
    
    $('#totalAgentsCount').text(totalAgents);
    $('#activeAgentsCount').text(activeAgents);
    $('#avgPerformanceCount').text(avgPerformance + '%');
    $('#totalPoliciesCount').text(totalPolicies);
};

const updateAgentsTable = () => {
    const tbody = $('#agentsTableBody');
    tbody.empty();
    
    allAgents.forEach((agent, index) => {
        const row = `
            <tr>
                <td>${index + 1}</td>
                <td>${agent.name}</td>
                <td>${agent.phone}</td>
                <td>${agent.email}</td>
                <td>${agent.userId}</td>
                <td><span class="status ${agent.status.toLowerCase()}">${agent.status}</span></td>
                <td>${agent.policies}</td>
                <td><span class="performance-score ${agent.performance >= 90 ? 'excellent' : agent.performance >= 75 ? 'good' : 'average'}">${agent.performance}%</span></td>
                <td>
                    <div class="action-buttons">
                        <button class="action-btn view" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="action-btn edit" title="Edit Agent">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="action-btn delete" title="Delete Agent">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        tbody.append(row);
    });
};

const filterAgentsData = () => {
    const searchFilter = $('#agentsSearch').val().toLowerCase();
    
    const filteredAgents = allAgents.filter(agent => 
        agent.name.toLowerCase().includes(searchFilter) ||
        agent.email.toLowerCase().includes(searchFilter) ||
        agent.phone.includes(searchFilter) ||
        agent.userId.toLowerCase().includes(searchFilter)
    );
    
    // Update table with filtered data
    const tbody = $('#agentsTableBody');
    tbody.empty();
    
    filteredAgents.forEach((agent, index) => {
        const row = `
            <tr>
                <td>${index + 1}</td>
                <td>${agent.name}</td>
                <td>${agent.phone}</td>
                <td>${agent.email}</td>
                <td>${agent.userId}</td>
                <td><span class="status ${agent.status.toLowerCase()}">${agent.status}</span></td>
                <td>${agent.policies}</td>
                <td><span class="performance-score ${agent.performance >= 90 ? 'excellent' : agent.performance >= 75 ? 'good' : 'average'}">${agent.performance}%</span></td>
                <td>
                    <div class="action-buttons">
                        <button class="action-btn view" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="action-btn edit" title="Edit Agent">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="action-btn delete" title="Delete Agent">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        tbody.append(row);
    });
};

// Modal Functions for Policies
const openPolicyModal = () => {
    $('#policyModal').modal('show');
    resetMultiStepModal();
};

const closePolicyModal = () => {
    $('#policyModal').modal('hide');
    resetMultiStepModal();
};

const handlePolicySubmit = (event) => {
    event.preventDefault();
    
    const policyType = selectedPolicyType;
    
    if (!policyType) {
        alert('Please select a policy type');
        return;
    }
    
    let formData = {};
    
    if (policyType === 'Motor') {
        formData = {
            type: 'Motor',
            customerName: $('#customerName').val(),
            email: $('#email').val(),
            phone: $('#phone').val(),
            address: $('#address').val(),
            vehicleMake: $('#vehicleMake').val(),
            vehicleModel: $('#vehicleModel').val(),
            vehicleYear: $('#vehicleYear').val(),
            registrationNo: $('#registrationNo').val(),
            engineNo: $('#engineNo').val(),
            chassisNo: $('#chassisNo').val(),
            previousPolicy: $('#previousPolicy').val(),
            ncb: $('#ncb').val(),
            startDate: $('#startDate').val(),
            endDate: $('#endDate').val(),
            sumInsured: $('#sumInsured').val(),
            premium: $('#premium').val()
        };
    } else if (policyType === 'Health') {
        formData = {
            type: 'Health',
            customerName: $('#healthCustomerName').val(),
            email: $('#healthEmail').val(),
            phone: $('#healthPhone').val(),
            address: $('#healthAddress').val(),
            age: $('#age').val(),
            gender: $('#gender').val(),
            height: $('#height').val(),
            weight: $('#weight').val(),
            occupation: $('#occupation').val(),
            medicalHistory: $('#medicalHistory').val(),
            familyHistory: $('#familyHistory').val(),
            lifestyle: $('#lifestyle').val(),
            startDate: $('#healthStartDate').val(),
            endDate: $('#healthEndDate').val(),
            sumInsured: $('#healthSumInsured').val(),
            premium: $('#healthPremium').val()
        };
    } else if (policyType === 'Life') {
        formData = {
            type: 'Life',
            customerName: $('#lifeCustomerName').val(),
            email: $('#lifeEmail').val(),
            phone: $('#lifePhone').val(),
            address: $('#lifeAddress').val(),
            age: $('#lifeAge').val(),
            gender: $('#lifeGender').val(),
            occupation: $('#lifeOccupation').val(),
            nominee: $('#nominee').val(),
            relationship: $('#relationship').val(),
            medicalHistory: $('#lifeMedicalHistory').val(),
            lifestyle: $('#lifeLifestyle').val(),
            startDate: $('#lifeStartDate').val(),
            endDate: $('#lifeEndDate').val(),
            sumInsured: $('#lifeSumInsured').val(),
            premium: $('#lifePremium').val()
        };
    }
    
    // Generate new ID
    const newId = Math.max(...policiesData.map(p => p.id)) + 1;
    
    // Create new policy object
    const newPolicy = {
        id: newId,
        policyNumber: `POL${String(newId).padStart(6, '0')}`,
        customerName: formData.customerName,
        type: formData.type,
        startDate: formData.startDate,
        endDate: formData.endDate,
        premium: parseFloat(formData.premium) || 0,
        status: 'Active',
        agent: 'John Doe' // Default agent
    };
    
    // Add to policies data
    policiesData.push(newPolicy);
    
    // Refresh table
    if (typeof initializePoliciesPage === 'function') {
        initializePoliciesPage();
    }
    
    // Close modal
    closePolicyModal();
    
    // Show success message
    alert(`${policyType} policy created successfully!`);
};

const goToStep = (step) => {
    // Hide all step contents
    $('#step1, #step2, #step3').hide();
    
    // Show current step content
    $(`#step${step}`).show();
    
    currentStep = step;
    
    // Show appropriate form based on policy type
    if (step === 3) {
        showPolicyForm(selectedPolicyType);
    }
};

const showPolicyForm = (policyType) => {
    // Hide all forms
    $('.policy-form').hide();
    
    // Show selected form
    $(`#${policyType.toLowerCase()}Form`).show();
    
    // Set default dates for the visible form
    setDefaultDates(policyType);
};

const setDefaultDates = (policyType) => {
    const today = new Date();
    const oneYearLater = new Date(today);
    oneYearLater.setFullYear(today.getFullYear() + 1);
    oneYearLater.setDate(oneYearLater.getDate() - 1);
    
    const startDate = today.toISOString().split('T')[0];
    const endDate = oneYearLater.toISOString().split('T')[0];
    
    if (policyType === 'Motor') {
        $('#startDate').val(startDate);
        $('#endDate').val(endDate);
    } else if (policyType === 'Health') {
        $('#healthStartDate').val(startDate);
        $('#healthEndDate').val(endDate);
    } else if (policyType === 'Life') {
        $('#lifeStartDate').val(startDate);
        $('#lifeEndDate').val(endDate);
    }
};

const resetMultiStepModal = () => {
    currentStep = 1;
    selectedPolicyType = '';
    selectedBusinessType = '';
    
    // Reset step content
    $('#step1, #step2, #step3').hide();
    $('#step1').show();
    
    // Reset dropdowns
    $('#policyTypeSelect, #businessTypeSelect').val('');
    
    // Reset buttons
    $('#nextStep1, #nextStep2').prop('disabled', true);
    
    // Hide all forms
    $('.policy-form').hide();
    
    // Reset form
    $('#policyForm')[0].reset();
};

// Renewal Modal Functions
const openRenewalModal = () => {
    $('#renewalModalTitle').text('Add Renewal Reminder');
    $('#renewalForm')[0].reset();
    
    // Populate policy dropdown
    const policySelect = $('#renewalPolicyId');
    policySelect.empty().append('<option value="">Select Policy</option>');
    
    allPolicies.forEach(policy => {
        policySelect.append(`<option value="${policy.id}">#${policy.id.toString().padStart(3, '0')} - ${policy.owner} (${policy.type})</option>`);
    });
    
    // Populate agent dropdown
    const agentSelect = $('#renewalAssignedTo');
    agentSelect.empty().append('<option value="">Select Agent</option>');
    
    allAgents.forEach(agent => {
        agentSelect.append(`<option value="${agent.name}">${agent.name}</option>`);
    });
    
    // Set default reminder date (30 days from today)
    const today = new Date();
    const defaultReminder = new Date(today);
    defaultReminder.setDate(today.getDate() + 30);
    $('#renewalReminderDate').val(defaultReminder.toISOString().split('T')[0]);
    
    $('#renewalModal').addClass('show');
};

const closeRenewalModal = () => {
    $('#renewalModal').removeClass('show');
    $('#renewalForm')[0].reset();
};

const handleRenewalSubmit = (e) => {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const renewalData = {
        id: allRenewals.length + 1,
        policyId: parseInt(formData.get('policyId')),
        customerName: formData.get('customerName'),
        policyType: formData.get('policyType'),
        expiryDate: formData.get('expiryDate'),
        daysLeft: Math.ceil((new Date(formData.get('expiryDate')) - new Date()) / (1000 * 60 * 60 * 24)),
        status: formData.get('status'),
        priority: formData.get('priority'),
        assignedTo: formData.get('assignedTo'),
        reminderDate: formData.get('reminderDate'),
        notes: formData.get('notes'),
        emailNotification: formData.get('emailNotification') === 'on',
        smsNotification: formData.get('smsNotification') === 'on',
        notificationDays: parseInt(formData.get('notificationDays')),
        createdAt: new Date().toISOString().split('T')[0]
    };
    
    allRenewals.push(renewalData);
    renewalsFilteredData = [...allRenewals];
    
    closeRenewalModal();
    if (typeof initializeRenewalsPage === 'function') {
        initializeRenewalsPage();
    }
    
    alert('Renewal reminder added successfully!');
};

// Followup Modal Functions
const openFollowupModal = () => {
    $('#followupModalTitle').text('Add Follow Up');
    $('#followupForm')[0].reset();
    
    // Populate policy dropdown
    const policySelect = $('#followupPolicyId');
    policySelect.empty().append('<option value="">Select Policy (Optional)</option>');
    
    allPolicies.forEach(policy => {
        policySelect.append(`<option value="${policy.id}">#${policy.id.toString().padStart(3, '0')} - ${policy.owner} (${policy.type})</option>`);
    });
    
    // Populate telecaller dropdown
    const telecallerSelect = $('#followupAssignedTo');
    telecallerSelect.empty().append('<option value="">Select Telecaller</option>');
    
    allAgents.forEach(agent => {
        telecallerSelect.append(`<option value="${agent.name}">${agent.name}</option>`);
    });
    
    // Set default next follow-up date (3 days from today)
    const today = new Date();
    const nextFollowup = new Date(today);
    nextFollowup.setDate(today.getDate() + 3);
    $('#followupNextDate').val(nextFollowup.toISOString().split('T')[0]);
    
    // Hide previous notes section for new follow-ups
    $('#previousNotesSection').hide();
    
    $('#followupModal').addClass('show');
};

const closeFollowupModal = () => {
    $('#followupModal').removeClass('show');
    $('#followupForm')[0].reset();
    $('#previousNotesSection').hide();
};

const handleFollowupSubmit = (e) => {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const currentTime = new Date().toLocaleTimeString('en-US', { 
        hour12: true, 
        hour: '2-digit', 
        minute: '2-digit' 
    });
    
    const newNote = {
        date: new Date().toISOString().split('T')[0],
        time: currentTime,
        telecaller: formData.get('assignedTo'),
        note: formData.get('note'),
        callDuration: parseInt(formData.get('callDuration')) || 0,
        callResult: formData.get('callResult') || ''
    };
    
    const followupData = {
        id: allFollowups.length + 1,
        customerName: formData.get('customerName'),
        phone: formData.get('phone'),
        email: formData.get('email'),
        policyId: formData.get('policyId') ? parseInt(formData.get('policyId')) : null,
        followupType: formData.get('followupType'),
        status: formData.get('status'),
        assignedTo: formData.get('assignedTo'),
        priority: formData.get('priority'),
        lastFollowupDate: new Date().toISOString().split('T')[0],
        nextFollowupDate: formData.get('nextFollowupDate'),
        reminderTime: formData.get('reminderTime'),
        notesHistory: [newNote],
        recentNote: newNote.note,
        createdAt: new Date().toISOString().split('T')[0]
    };
    
    allFollowups.push(followupData);
    followupsFilteredData = [...allFollowups];
    
    closeFollowupModal();
    if (typeof initializeFollowupsPage === 'function') {
        initializeFollowupsPage();
    }
    
    alert('Follow-up added successfully!');
};

// Enhanced initialization function
$(document).ready(function() {
    initializeDemoData();
    
    // Initialize pages based on current page
    if ($('#policies').length > 0) {
        initializePoliciesPage();
    }
    
    if ($('#renewals').length > 0) {
        initializeRenewalsPage();
    }
    
    if ($('#followups').length > 0) {
        initializeFollowupsPage();
    }
    
    if ($('#agents').length > 0) {
        initializeAgentsPage();
    }
    
    if ($('#reports').length > 0) {
        // Initialize reports page functionality here
        console.log('Reports page loaded');
    }
    
    if ($('#notifications').length > 0) {
        // Initialize notifications page functionality here
        console.log('Notifications page loaded');
    }
    
    if ($('#settings').length > 0) {
        // Initialize settings page functionality here
        console.log('Settings page loaded');
    }
});
