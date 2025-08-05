# Insurance Management System 2.0 - Developer Guide

## 🚀 Quick Start

### Project Overview
Frontend-only insurance management system with Material 3 + Glassmorphism design. Features policy management, renewals, follow-ups, reporting, and automated notifications.

### Tech Stack
- **HTML5** + **CSS3** + **jQuery 3.7.1**
- **Chart.js 4.4.0** for data visualization
- **Font Awesome 6.4.0** for icons
- **LocalStorage** for data persistence

### File Structure
```
├── index.html          # Main application
├── styles.css          # Complete styling
├── script.js           # Core functionality
└── README_DEVELOPER.md # This guide
```

---

## 📄 Pages & Functions

### 1. Dashboard (`data-page="dashboard"`)
**Purpose**: Main landing page with KPIs and charts

**Key Functions**:
- `initializeDashboard()` - Setup dashboard
- `updateDashboardStats()` - Calculate KPIs
- `renderDashboardCharts()` - Create charts
- `renderRecentPolicies()` - Show recent policies

### 2. Policies (`data-page="policies"`)
**Purpose**: Complete policy management

**Key Functions**:
- `initializePoliciesPage()` - Setup page
- `renderPoliciesTable()` - Render table
- `handlePolicySearch()` - Search functionality
- `openPolicyModal()` - Open modal

**Data Structure**:
```javascript
{
    id: "POL001",
    type: "Motor|Health|Life",
    customerName: "string",
    vehicleNumber: "string",
    startDate: "YYYY-MM-DD",
    endDate: "YYYY-MM-DD",
    premium: number,
    revenue: number,
    customerPaid: number,
    company: "string",
    status: "Active|Expired|Cancelled"
}
```

### 3. Renewals (`data-page="renewals"`)
**Purpose**: Policy renewal tracking

**Priority Levels**:
- Critical (Red): ≤ 7 days
- High (Orange): ≤ 30 days
- Medium (Yellow): ≤ 60 days
- Low (Green): ≤ 90 days

**Key Functions**:
- `initializeRenewalsPage()` - Setup page
- `calculateRenewalPriority()` - Determine urgency
- `renderRenewalsTable()` - Render data

### 4. Follow-ups (`data-page="followups"`)
**Purpose**: Customer interaction tracking

**Key Functions**:
- `initializeFollowupsPage()` - Setup page
- `addFollowupNote()` - Add notes
- `viewFollowupHistory()` - Show history

### 5. Reports (`data-page="reports"`)
**Purpose**: Analytics and data export

**Key Functions**:
- `initializeReportsPage()` - Setup page
- `generateReports()` - Create reports
- `exportToCSV()` - Export data

### 6. Agents (`data-page="agents"`)
**Purpose**: Agent management

**Key Functions**:
- `initializeAgents()` - Setup page
- `calculateAgentStats()` - Performance metrics
- `renderAgentsTable()` - Render data

### 7. Notifications (`data-page="notifications"`)
**Purpose**: Advanced notification system

**Key Functions**:
- `initializeNotificationsPage()` - Setup page
- `updateNotificationStats()` - Analytics
- `openBulkNotificationModal()` - Bulk sending
- `openScheduleModal()` - Scheduling

### 8. Settings (`data-page="settings"`)
**Purpose**: System configuration

**Key Functions**:
- `initializeSettings()` - Setup page
- `saveSettings()` - Save config
- `loadSettings()` - Load config

---

## ⚙️ Core Functions

### Application Management
```javascript
initializeApplication()    // Initialize app
navigateToPage(page)       // Page navigation
toggleTheme()             // Theme switching
```

### Data Management
```javascript
generateDummyData()        // Create sample data
saveToLocalStorage(key, data)  // Save data
loadFromLocalStorage(key)  // Load data
```

### Utilities
```javascript
debounce(func, wait)       // Debounce function
formatDate(date)           // Format date
formatCurrency(amount)     // Format currency
showNotification(message, type)  // Show notifications
```

---

## 📊 Data Structures

### Policy Object
```javascript
{
    id: "POL001",
    type: "Motor|Health|Life",
    customerName: "string",
    customerPhone: "string",
    customerEmail: "string",
    vehicleNumber: "string", // Motor only
    company: "string",
    startDate: "YYYY-MM-DD",
    endDate: "YYYY-MM-DD",
    premium: number,
    revenue: number,
    customerPaid: number,
    businessType: "Self|Agent1|Agent2",
    status: "Active|Expired|Cancelled"
}
```

### Renewal Object
```javascript
{
    id: "REN001",
    policyId: "POL001",
    customerName: "string",
    phone: "string",
    email: "string",
    policyType: "string",
    currentEndDate: "YYYY-MM-DD",
    renewalDate: "YYYY-MM-DD",
    premium: number,
    commission: number,
    agentName: "string",
    status: "Pending|Completed|Overdue",
    priority: "Low|Medium|High|Critical",
    daysLeft: number
}
```

### Follow-up Object
```javascript
{
    id: "FUP001",
    customerName: "string",
    phone: "string",
    policyId: "POL001",
    status: "Pending|In Progress|Completed",
    notes: [
        {
            date: "YYYY-MM-DD",
            time: "HH:MM",
            note: "string",
            callDuration: "string",
            outcome: "Positive|Negative|Neutral"
        }
    ]
}
```

### Agent Object
```javascript
{
    id: "AGT001",
    name: "string",
    phone: "string",
    email: "string",
    userId: "string",
    role: "Agent|Admin|Reception",
    totalPolicies: number,
    totalCommission: number,
    status: "Active|Inactive"
}
```

---

## 🧩 Components

### Modal System
```html
<div class="modal" id="modalId">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Title</h2>
            <button class="modal-close" id="closeModalId">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <!-- Content -->
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary">Cancel</button>
            <button class="btn btn-primary">Save</button>
        </div>
    </div>
</div>
```

**Functions**:
- `openModal(modalId)` - Open modal
- `closeModal(modalId)` - Close modal
- `closeAllModals()` - Close all modals

### Data Table
```html
<div class="data-table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th data-sort="column">Header</th>
            </tr>
        </thead>
        <tbody id="tableBody">
            <!-- Dynamic content -->
        </tbody>
    </table>
    <div class="pagination">
        <!-- Pagination -->
    </div>
</div>
```

**Functions**:
- `renderTable(data, columns, tableId)` - Render table
- `handleTableSort(column)` - Handle sorting
- `handlePagination(page)` - Handle pagination

### Charts
**Types**: Bar, Line, Pie, Doughnut, Mini charts

**Functions**:
- `createBarChart(canvasId, data, options)` - Bar chart
- `createLineChart(canvasId, data, options)` - Line chart
- `createPieChart(canvasId, data, options)` - Pie chart
- `destroyChart(chartInstance)` - Cleanup

---

## 🎨 Theme System

### CSS Variables
```css
:root {
    --bg-primary: #F9FAFB;
    --bg-secondary: rgba(255, 255, 255, 0.7);
    --text-primary: #111827;
    --text-secondary: #6B7280;
    --accent-primary: #4F46E5;
    --accent-secondary: #10B981;
    --border-color: rgba(255, 255, 255, 0.2);
}

.dark-theme {
    --bg-primary: #0F172A;
    --bg-secondary: rgba(30, 41, 59, 0.3);
    --text-primary: #F1F5F9;
    --text-secondary: #9CA3AF;
    --accent-primary: #6366F1;
    --accent-secondary: #34D399;
    --border-color: rgba(255, 255, 255, 0.1);
}
```

### Theme Switching
```javascript
const toggleTheme = () => {
    const body = document.body;
    const isDark = body.classList.contains('dark-theme');
    
    if (isDark) {
        body.classList.remove('dark-theme');
        localStorage.setItem('theme', 'light');
    } else {
        body.classList.add('dark-theme');
        localStorage.setItem('theme', 'dark');
    }
};
```

---

## 🔌 API Integration (Future)

### Endpoints
```
GET    /api/policies          # Get policies
POST   /api/policies          # Create policy
PUT    /api/policies/:id      # Update policy
DELETE /api/policies/:id      # Delete policy

GET    /api/renewals          # Get renewals
POST   /api/renewals          # Create renewal

GET    /api/followups         # Get follow-ups
POST   /api/followups         # Create follow-up

GET    /api/agents            # Get agents
POST   /api/agents            # Create agent

POST   /api/notifications/send     # Send notification
POST   /api/notifications/schedule # Schedule notification
```

### Data Format
```javascript
// Request
{
    method: "GET|POST|PUT|DELETE",
    url: "/api/endpoint",
    headers: {
        "Content-Type": "application/json",
        "Authorization": "Bearer token"
    },
    data: { /* payload */ }
}

// Response
{
    success: true|false,
    data: { /* response data */ },
    message: "string",
    errors: [ /* validation errors */ ]
}
```

---

## 🛠 Development Guidelines

### Code Style
- Use ES6+ features
- Prefer `const` and `let` over `var`
- Use arrow functions for callbacks
- Use template literals
- Use destructuring

### Naming Conventions
- **Functions**: camelCase, descriptive names
- **Variables**: camelCase, meaningful names
- **CSS Classes**: kebab-case, BEM methodology

### Error Handling
```javascript
const safeFunction = async () => {
    try {
        const result = await riskyOperation();
        return result;
    } catch (error) {
        console.error('Operation failed:', error);
        showNotification('Operation failed', 'error');
        return null;
    }
};
```

### Performance
- Use debouncing for search
- Implement pagination
- Use DocumentFragment for table rendering
- Destroy chart instances properly
- Clean up event listeners

---

## 🔧 Troubleshooting

### Common Issues

#### Page Not Loading
- Check browser console for errors
- Verify all files are loaded
- Check for syntax errors
- Ensure jQuery and Chart.js are loaded

#### Charts Not Rendering
- Verify Chart.js is loaded
- Check canvas element exists
- Ensure data is properly formatted
- Check for JavaScript errors

#### Data Not Persisting
- Check localStorage availability
- Verify save functions are called
- Check for storage quota exceeded
- Validate data format

#### Theme Not Switching
- Check CSS custom properties
- Verify localStorage permissions
- Check for JavaScript errors
- Validate theme class application

### Debug Tools
```javascript
// Console logging
console.log('Debug message', data);

// Performance monitoring
console.time('operation');
// ... operation
console.timeEnd('operation');

// Error handling
window.addEventListener('error', (event) => {
    console.error('Global error:', event.error);
});
```

---

## 📚 Resources

### Documentation
- [jQuery API](https://api.jquery.com/)
- [Chart.js Docs](https://www.chartjs.org/docs/)
- [Font Awesome](https://fontawesome.com/icons)
- [Material Design](https://material.io/design)

### Tools
- [Chrome DevTools](https://developers.google.com/web/tools/chrome-devtools)
- [VS Code](https://code.visualstudio.com/)
- [Lighthouse](https://developers.google.com/web/tools/lighthouse)

---

## 📞 Support

For issues or questions:
1. Check troubleshooting section
2. Review browser console for errors
3. Test on different browsers
4. Create detailed issue report

---

*Last updated: December 2024* 