# Insurance Management System 2.0 - Quick Reference

## 🚀 Most Used Functions

### Page Navigation
```javascript
navigateToPage('dashboard')     // Navigate to dashboard
navigateToPage('policies')      // Navigate to policies
navigateToPage('renewals')      // Navigate to renewals
navigateToPage('followups')     // Navigate to follow-ups
navigateToPage('reports')       // Navigate to reports
navigateToPage('agents')        // Navigate to agents
navigateToPage('notifications') // Navigate to notifications
navigateToPage('settings')      // Navigate to settings
```

### Modal Management
```javascript
openModal('policyModal')        // Open policy modal
openModal('agentModal')         // Open agent modal
openModal('renewalModal')       // Open renewal modal
openModal('followupModal')      // Open follow-up modal
openModal('bulkNotificationModal') // Open bulk notification modal
openModal('scheduleNotificationModal') // Open schedule modal

closeModal('modalId')           // Close specific modal
closeAllModals()                // Close all modals
```

### Data Management
```javascript
saveToLocalStorage('key', data)     // Save data
loadFromLocalStorage('key')         // Load data
generateDummyData()                 // Generate sample data
```

### Notifications
```javascript
showNotification('Message', 'success')  // Success notification
showNotification('Message', 'error')    // Error notification
showNotification('Message', 'warning')  // Warning notification
showNotification('Message', 'info')     // Info notification
```

### Utility Functions
```javascript
formatDate('2024-12-15')             // Format date
formatCurrency(5000)                 // Format currency (₹5,000)
debounce(function, 300)              // Debounce function
toggleTheme()                        // Switch theme
```

## 📊 Data Structures Quick Reference

### Policy
```javascript
{
    id: "POL001",
    type: "Motor|Health|Life",
    customerName: "John Doe",
    vehicleNumber: "MH12AB1234", // Motor only
    startDate: "2024-01-01",
    endDate: "2025-01-01",
    premium: 5000,
    revenue: 7500,
    customerPaid: 5000,
    company: "LIC",
    status: "Active|Expired|Cancelled"
}
```

### Renewal
```javascript
{
    id: "REN001",
    policyId: "POL001",
    customerName: "John Doe",
    phone: "+91-9876543210",
    email: "john@example.com",
    policyType: "Motor",
    currentEndDate: "2024-12-31",
    renewalDate: "2025-01-01",
    premium: 5000,
    commission: 500,
    agentName: "Agent Name",
    status: "Pending|Completed|Overdue",
    priority: "Low|Medium|High|Critical",
    daysLeft: 15
}
```

### Follow-up
```javascript
{
    id: "FUP001",
    customerName: "John Doe",
    phone: "+91-9876543210",
    policyId: "POL001",
    status: "Pending|In Progress|Completed",
    notes: [
        {
            date: "2024-12-15",
            time: "14:30",
            note: "Customer interested in renewal",
            callDuration: "5 minutes",
            outcome: "Positive|Negative|Neutral"
        }
    ]
}
```

### Agent
```javascript
{
    id: "AGT001",
    name: "Agent Name",
    phone: "+91-9876543210",
    email: "agent@example.com",
    userId: "agent001",
    role: "Agent|Admin|Reception",
    totalPolicies: 25,
    totalCommission: 15000,
    status: "Active|Inactive"
}
```

## 🎨 CSS Classes Quick Reference

### Layout
```css
.page                    /* Page container */
.dashboard-card          /* Dashboard card */
.data-table-container    /* Table wrapper */
.modal                   /* Modal overlay */
.modal-content           /* Modal content */
```

### Components
```css
.btn                     /* Base button */
.btn-primary             /* Primary button */
.btn-secondary           /* Secondary button */
.btn-success             /* Success button */
.btn-danger              /* Danger button */
.btn-warning             /* Warning button */
.btn-info                /* Info button */

.form-group              /* Form field group */
.form-section            /* Form section */
.search-box              /* Search input */
.pagination              /* Pagination controls */
```

### Status Indicators
```css
.status-active           /* Active status */
.status-pending          /* Pending status */
.status-expired          /* Expired status */
.status-completed        /* Completed status */

.priority-critical       /* Critical priority */
.priority-high           /* High priority */
.priority-medium         /* Medium priority */
.priority-low            /* Low priority */
```

### Theme Classes
```css
.dark-theme              /* Dark theme */
.light-theme             /* Light theme (default) */
```

## 🔧 Common Patterns

### Table Rendering
```javascript
const columns = [
    { key: 'id', label: 'ID' },
    { key: 'name', label: 'Name' },
    { key: 'status', label: 'Status', formatter: (value) => `<span class="status-${value.toLowerCase()}">${value}</span>` }
];

renderTable(data, columns, 'tableBody');
```

### Chart Creation
```javascript
const chartData = {
    labels: ['Jan', 'Feb', 'Mar'],
    datasets: [{
        data: [10, 20, 30],
        backgroundColor: ['#4F46E5', '#10B981', '#F59E0B']
    }]
};

createPieChart('chartCanvas', chartData, { responsive: true });
```

### Form Validation
```javascript
const errors = validateForm('formId');
if (errors.length > 0) {
    errors.forEach(error => showNotification(error, 'error'));
    return false;
}
```

### Data Filtering
```javascript
const filteredData = allPolicies.filter(policy => 
    policy.type === 'Motor' && 
    policy.status === 'Active'
);
```

## 📱 Responsive Breakpoints

```css
/* Mobile */
@media (max-width: 768px) { }

/* Tablet */
@media (max-width: 1024px) { }

/* Desktop */
@media (min-width: 1025px) { }
```

## 🎯 Event Handlers

### Common Event Patterns
```javascript
// Click events
$('.btn').click(function() { });

// Form submission
$('#formId').submit(function(e) {
    e.preventDefault();
    // Handle form submission
});

// Input changes
$('#searchInput').on('input', debounce(function() {
    // Handle search
}, 300));

// Modal events
$('.modal-close').click(function() {
    closeModal($(this).closest('.modal').attr('id'));
});
```

## 🔍 Debugging Tips

### Console Logging
```javascript
console.log('Data:', data);                    // Basic logging
console.table(data);                           // Table format
console.group('Group Name');                   // Group logs
console.groupEnd();                            // End group
console.time('operation');                     // Start timer
console.timeEnd('operation');                  // End timer
```

### Performance Monitoring
```javascript
performance.mark('start');
// ... operation
performance.mark('end');
performance.measure('operation', 'start', 'end');
console.log(performance.getEntriesByName('operation')[0].duration);
```

### Error Handling
```javascript
try {
    // Risky operation
} catch (error) {
    console.error('Error:', error);
    showNotification('Operation failed', 'error');
}
```

## 📋 LocalStorage Keys

```javascript
'insurance_settings'      // Application settings
'insurance_policies'      // Policy data
'insurance_renewals'      // Renewal data
'insurance_followups'     // Follow-up data
'insurance_agents'        // Agent data
'insurance_notifications' // Notification history
'theme'                   // Theme preference
```

## 🚨 Common Issues & Solutions

### Charts Not Rendering
```javascript
// Check if Chart.js is loaded
if (typeof Chart === 'undefined') {
    console.error('Chart.js not loaded');
    return;
}

// Destroy existing chart before creating new one
if (chartInstance) {
    chartInstance.destroy();
}
```

### Data Not Saving
```javascript
// Check localStorage availability
if (typeof Storage === 'undefined') {
    console.error('localStorage not available');
    return;
}

// Handle storage quota exceeded
try {
    localStorage.setItem('key', JSON.stringify(data));
} catch (error) {
    console.error('Storage quota exceeded:', error);
}
```

### Theme Not Switching
```javascript
// Force theme application
document.body.classList.remove('dark-theme', 'light-theme');
document.body.classList.add('dark-theme');
```

---

*This quick reference covers the most commonly used functions and patterns. For detailed documentation, see README_DEVELOPER.md* 