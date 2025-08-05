// Insurance Management System 2.0 - Admin Panel Scripts

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
