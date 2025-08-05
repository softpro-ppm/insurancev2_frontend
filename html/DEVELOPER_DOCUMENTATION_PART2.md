# Insurance Management System 2.0 - Developer Documentation (Part 2)

## 📄 Page Documentation

### 1. Dashboard Page (`data-page="dashboard"`)

**Purpose**: Main landing page with overview statistics and recent activities

**Components**:
- **Statistics Cards**: 4 KPI cards (Premium, Policies, Renewals, Revenue)
- **Charts Section**: Bar chart (Premium vs Revenue vs Policies) and Pie chart (Insurance Types)
- **Recent Policies Table**: Latest 10 policies with actions
- **Quick Actions**: Add Policy button

**Key Functions**:
- `initializeDashboard()`: Sets up dashboard components
- `updateDashboardStats()`: Calculates and displays KPIs
- `renderDashboardCharts()`: Creates interactive charts
- `renderRecentPolicies()`: Displays recent policies table

**Data Sources**:
- `allPolicies` array
- `allRenewals` array
- `allAgents` array

**KPI Calculations**:
```javascript
// Premium (Current Month)
const currentMonthPremium = allPolicies
    .filter(policy => {
        const policyDate = new Date(policy.startDate);
        const currentDate = new Date();
        return policyDate.getMonth() === currentDate.getMonth() && 
               policyDate.getFullYear() === currentDate.getFullYear();
    })
    .reduce((sum, policy) => sum + policy.premium, 0);

// Policies (Current Month)
const currentMonthPolicies = allPolicies
    .filter(policy => {
        const policyDate = new Date(policy.startDate);
        const currentDate = new Date();
        return policyDate.getMonth() === currentDate.getMonth() && 
               policyDate.getFullYear() === currentDate.getFullYear();
    }).length;
```

### 2. Policies Page (`data-page="policies"`)

**Purpose**: Complete policy management with search, filter, and pagination

**Components**:
- **Statistics Bar**: Policy count, total premium, active policies
- **Search & Filter**: Advanced filtering options
- **Data Table**: Sortable, paginated policy list
- **Add Policy Modal**: Multi-step policy creation form

**Key Functions**:
- `initializePoliciesPage()`: Sets up policies page
- `renderPoliciesTable()`: Renders paginated table
- `handlePolicySearch()`: Debounced search functionality
- `handlePolicyFilter()`: Filter by policy type, status, etc.
- `openPolicyModal()`: Opens add/edit policy modal

**Data Structure**:
```javascript
{
    id: "POL001",
    type: "Motor",
    customerName: "John Doe",
    vehicleNumber: "MH12AB1234",
    startDate: "2024-01-01",
    endDate: "2025-01-01",
    premium: 5000,
    revenue: 7500,
    customerPaid: 5000,
    company: "LIC",
    status: "Active"
}
```

**Search Implementation**:
```javascript
const handlePolicySearch = debounce(() => {
    const searchTerm = $('#policySearch').val().toLowerCase();
    filteredData = allPolicies.filter(policy => 
        policy.customerName.toLowerCase().includes(searchTerm) ||
        policy.vehicleNumber.toLowerCase().includes(searchTerm) ||
        policy.type.toLowerCase().includes(searchTerm)
    );
    currentPage = 1;
    renderPoliciesTable();
}, 300);
```

### 3. Renewals Page (`data-page="renewals"`)

**Purpose**: Track and manage policy renewals with priority-based workflow

**Components**:
- **Renewal Statistics**: Pending, due soon, overdue counts
- **Renewal Table**: Sortable list with priority indicators
- **Add Renewal Modal**: Create new renewal records
- **Priority System**: Color-coded urgency levels

**Key Functions**:
- `initializeRenewalsPage()`: Sets up renewals page
- `renderRenewalsTable()`: Renders renewal data
- `calculateRenewalPriority()`: Determines urgency level
- `openRenewalModal()`: Opens renewal management modal

**Priority Levels**:
- **Critical** (Red): Expires within 7 days
- **High** (Orange): Expires within 30 days
- **Medium** (Yellow): Expires within 60 days
- **Low** (Green): Expires within 90 days

**Priority Calculation**:
```javascript
const calculateRenewalPriority = (endDate) => {
    const today = new Date();
    const expiryDate = new Date(endDate);
    const daysLeft = Math.ceil((expiryDate - today) / (1000 * 60 * 60 * 24));
    
    if (daysLeft <= 7) return 'Critical';
    if (daysLeft <= 30) return 'High';
    if (daysLeft <= 60) return 'Medium';
    return 'Low';
};
```

### 4. Follow-ups Page (`data-page="followups"`)

**Purpose**: Customer interaction tracking for telecallers

**Components**:
- **Follow-up Statistics**: Total, pending, completed counts
- **Follow-up Table**: Customer interactions with latest notes
- **Add Follow-up Modal**: Record customer interactions
- **Note History**: Complete interaction timeline

**Key Functions**:
- `initializeFollowupsPage()`: Sets up follow-ups page
- `renderFollowupsTable()`: Renders follow-up data
- `addFollowupNote()`: Adds new interaction notes
- `viewFollowupHistory()`: Shows complete interaction history

**Data Structure**:
```javascript
{
    id: "FUP001",
    customerName: "John Doe",
    phone: "+91-9876543210",
    policyId: "POL001",
    status: "Pending",
    lastContact: "2024-12-15",
    notes: [
        {
            date: "2024-12-15",
            time: "14:30",
            note: "Customer interested in renewal",
            callDuration: "5 minutes",
            outcome: "Positive"
        }
    ]
}
```

**Note Addition**:
```javascript
const addFollowupNote = (followupId, noteData) => {
    const followup = allFollowups.find(f => f.id === followupId);
    if (followup) {
        followup.notes.push({
            date: new Date().toISOString().split('T')[0],
            time: new Date().toLocaleTimeString('en-US', { 
                hour12: false, 
                hour: '2-digit', 
                minute: '2-digit' 
            }),
            note: noteData.note,
            callDuration: noteData.callDuration,
            outcome: noteData.outcome,
            agentName: noteData.agentName
        });
        followup.lastContact = new Date().toISOString().split('T')[0];
        saveToLocalStorage('insurance_followups', allFollowups);
        renderFollowupsTable();
    }
};
```

### 5. Reports Page (`data-page="reports"`)

**Purpose**: Comprehensive analytics and data export functionality

**Components**:
- **KPI Overview**: Key performance indicators
- **Interactive Charts**: Line, bar, pie, doughnut charts
- **Date Range Filter**: Customizable reporting periods
- **Report Tables**: Detailed data views with export options

**Key Functions**:
- `initializeReportsPage()`: Sets up reports page
- `generateReports()`: Creates various report types
- `renderReportCharts()`: Displays interactive charts
- `exportToCSV()`: Exports data to CSV format

**Report Types**:
- **Policy Reports**: Policy performance and trends
- **Renewal Reports**: Renewal success rates
- **Agent Reports**: Agent performance metrics
- **Financial Reports**: Revenue and commission analysis

**Chart Configuration**:
```javascript
const createReportChart = (canvasId, data, type) => {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return null;
    
    const config = {
        type: type,
        data: {
            labels: data.labels,
            datasets: [{
                data: data.values,
                backgroundColor: data.colors,
                borderColor: data.borderColors,
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    };
    
    return new Chart(ctx, config);
};
```

### 6. Agents Page (`data-page="agents"`)

**Purpose**: Agent management with performance tracking

**Components**:
- **Agent Statistics**: Total agents, active agents, total commission
- **Agent Table**: Sortable list with performance metrics
- **Add Agent Modal**: Agent registration form
- **Performance Tracking**: Commission and policy counts

**Key Functions**:
- `initializeAgents()`: Sets up agents page
- `renderAgentsTable()`: Renders agent data
- `calculateAgentStats()`: Computes performance metrics
- `openAgentModal()`: Opens agent management modal

**Performance Calculation**:
```javascript
const calculateAgentStats = () => {
    const totalAgents = allAgents.length;
    const activeAgents = allAgents.filter(agent => agent.status === 'Active').length;
    const totalCommission = allAgents.reduce((sum, agent) => sum + agent.totalCommission, 0);
    
    return {
        totalAgents,
        activeAgents,
        totalCommission,
        averageCommission: totalCommission / totalAgents
    };
};
```

### 7. Notifications Page (`data-page="notifications"`)

**Purpose**: Advanced notification management and automation

**Components**:
- **Notification Statistics**: Performance metrics and analytics
- **Scheduled Notifications**: Upcoming and recurring schedules
- **Active Notifications**: Real-time notification center
- **Notification History**: Complete communication log
- **Bulk Notification Modal**: Mass communication tool
- **Schedule Notification Modal**: Advanced scheduling interface

**Key Functions**:
- `initializeNotificationsPage()`: Sets up notifications page
- `updateNotificationStats()`: Calculates performance metrics
- `renderNotificationHistory()`: Displays communication history
- `openBulkNotificationModal()`: Opens bulk sending interface
- `openScheduleModal()`: Opens scheduling interface

**Analytics Features**:
- **Delivery Rate**: Percentage of successful deliveries
- **Open Rate**: Email open rates
- **Response Rate**: Customer response rates
- **Channel Performance**: Email, WhatsApp, SMS comparison

**Analytics Implementation**:
```javascript
const updateNotificationStats = () => {
    const stats = {
        expiringTomorrow: allPolicies.filter(policy => {
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            const endDate = new Date(policy.endDate);
            return endDate.toDateString() === tomorrow.toDateString();
        }).length,
        
        followupsDueToday: allFollowups.filter(followup => {
            const today = new Date().toISOString().split('T')[0];
            return followup.nextFollowUp === today;
        }).length,
        
        renewalsDueThisWeek: allRenewals.filter(renewal => {
            const today = new Date();
            const weekFromNow = new Date();
            weekFromNow.setDate(today.getDate() + 7);
            const renewalDate = new Date(renewal.renewalDate);
            return renewalDate >= today && renewalDate <= weekFromNow;
        }).length,
        
        notificationsSentToday: 45 // Mock data
    };
    
    // Update UI with calculated stats
    $('#expiringTomorrow').text(stats.expiringTomorrow);
    $('#followupsDueToday').text(stats.followupsDueToday);
    $('#renewalsDueThisWeek').text(stats.renewalsDueThisWeek);
    $('#notificationsSentToday').text(stats.notificationsSentToday);
};
```

### 8. Settings Page (`data-page="settings"`)

**Purpose**: System configuration and customization

**Components**:
- **Tabbed Interface**: Organized settings categories
- **General Settings**: Company information and preferences
- **Notification Settings**: Email, SMS, WhatsApp configuration
- **Security Settings**: Password and access controls
- **Appearance Settings**: Theme and display preferences
- **Backup & Export**: Data management options

**Key Functions**:
- `initializeSettings()`: Sets up settings page
- `saveSettings()`: Persists configuration changes
- `loadSettings()`: Retrieves saved settings
- `resetSettings()`: Restores default values

**Settings Structure**:
```javascript
const defaultSettings = {
    general: {
        companyName: "Insurance Agency",
        contactEmail: "contact@insurance.com",
        contactPhone: "+91-9876543210",
        timezone: "Asia/Kolkata"
    },
    notifications: {
        email: true,
        sms: false,
        whatsapp: true,
        smtpServer: "smtp.gmail.com",
        smtpPort: "587",
        smtpUsername: "noreply@insurance.com",
        smtpPassword: "********",
        whatsappBusinessId: "",
        whatsappAccessToken: "",
        whatsappPhoneNumber: "",
        whatsappWebhookUrl: ""
    },
    appearance: {
        theme: "light",
        fontSize: "medium",
        compactMode: false
    },
    security: {
        sessionTimeout: 30,
        requirePasswordChange: false,
        twoFactorAuth: false
    }
};
```

---

## 🧩 Component Documentation

### Modal System

**Base Modal Structure**:
```html
<div class="modal" id="modalId">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Modal Title</h2>
            <button class="modal-close" id="closeModalId">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <!-- Modal content -->
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary">Cancel</button>
            <button class="btn btn-primary">Save</button>
        </div>
    </div>
</div>
```

**Modal Functions**:
- `openModal(modalId)`: Opens specified modal
- `closeModal(modalId)`: Closes specified modal
- `closeAllModals()`: Closes all open modals

**Modal Implementation**:
```javascript
const openModal = (modalId) => {
    closeAllModals(); // Close any open modals
    $(`#${modalId}`).addClass('show');
    $('body').addClass('modal-open');
};

const closeModal = (modalId) => {
    $(`#${modalId}`).removeClass('show');
    $('body').removeClass('modal-open');
};

const closeAllModals = () => {
    $('.modal').removeClass('show');
    $('body').removeClass('modal-open');
};
```

### Data Table Component

**Structure**:
```html
<div class="data-table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th data-sort="column">Column Header</th>
            </tr>
        </thead>
        <tbody id="tableBody">
            <!-- Dynamic content -->
        </tbody>
    </table>
    <div class="pagination">
        <!-- Pagination controls -->
    </div>
</div>
```

**Table Functions**:
- `renderTable(data, columns, tableId)`: Renders data table
- `handleTableSort(column)`: Handles column sorting
- `handlePagination(page)`: Manages pagination
- `updateTableStats()`: Updates table statistics

**Table Rendering**:
```javascript
const renderTable = (data, columns, tableId) => {
    const tableBody = $(`#${tableId}`);
    const fragment = document.createDocumentFragment();
    
    data.forEach(item => {
        const row = document.createElement('tr');
        columns.forEach(column => {
            const cell = document.createElement('td');
            cell.textContent = item[column.key] || '';
            if (column.formatter) {
                cell.innerHTML = column.formatter(item[column.key], item);
            }
            row.appendChild(cell);
        });
        fragment.appendChild(row);
    });
    
    tableBody.empty().append(fragment);
};
```

### Chart Components

**Chart Types**:
- **Bar Charts**: Policy comparisons and trends
- **Line Charts**: Time-series data visualization
- **Pie/Doughnut Charts**: Distribution analysis
- **Mini Charts**: Compact trend indicators

**Chart Functions**:
- `createBarChart(canvasId, data, options)`: Creates bar chart
- `createLineChart(canvasId, data, options)`: Creates line chart
- `createPieChart(canvasId, data, options)`: Creates pie chart
- `destroyChart(chartInstance)`: Cleans up chart instances

**Chart Management**:
```javascript
const chartInstances = {};

const createChart = (canvasId, data, type, options = {}) => {
    // Destroy existing chart if it exists
    if (chartInstances[canvasId]) {
        chartInstances[canvasId].destroy();
    }
    
    const ctx = document.getElementById(canvasId);
    if (!ctx) return null;
    
    const config = {
        type: type,
        data: data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            ...options
        }
    };
    
    chartInstances[canvasId] = new Chart(ctx, config);
    return chartInstances[canvasId];
};

const destroyChart = (canvasId) => {
    if (chartInstances[canvasId]) {
        chartInstances[canvasId].destroy();
        delete chartInstances[canvasId];
    }
};
```

### Form Components

**Form Structure**:
```html
<div class="form-section">
    <h3><i class="fas fa-icon"></i> Section Title</h3>
    <div class="form-row">
        <div class="form-group">
            <label for="fieldId">Field Label</label>
            <input type="text" id="fieldId" required>
        </div>
    </div>
</div>
```

**Form Functions**:
- `validateForm(formId)`: Validates form inputs
- `clearForm(formId)`: Resets form fields
- `populateForm(data)`: Pre-fills form with data
- `serializeForm(formId)`: Collects form data

**Form Validation**:
```javascript
const validateForm = (formId) => {
    const form = $(`#${formId}`);
    const errors = [];
    
    form.find('[required]').each(function() {
        const field = $(this);
        const value = field.val().trim();
        
        if (!value) {
            errors.push(`${field.attr('name') || field.attr('id')} is required`);
            field.addClass('error');
        } else {
            field.removeClass('error');
        }
    });
    
    // Email validation
    form.find('[type="email"]').each(function() {
        const email = $(this).val();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (email && !emailRegex.test(email)) {
            errors.push('Please enter a valid email address');
            $(this).addClass('error');
        }
    });
    
    return errors;
};

const serializeForm = (formId) => {
    const form = $(`#${formId}`);
    const data = {};
    
    form.find('input, select, textarea').each(function() {
        const field = $(this);
        const name = field.attr('name') || field.attr('id');
        const value = field.val();
        
        if (name && value !== undefined) {
            data[name] = value;
        }
    });
    
    return data;
};
```

---

## ⚙️ Function Documentation

### Core Functions

#### `initializeApplication()`
**Purpose**: Initializes the entire application
**Parameters**: None
**Returns**: void
**Description**: Sets up the application on load, including theme, data, and event listeners

**Implementation**:
```javascript
const initializeApplication = () => {
    // Load saved theme
    const savedTheme = localStorage.getItem('theme') || 'light';
    if (savedTheme === 'dark') {
        document.body.classList.add('dark-theme');
    }
    
    // Generate dummy data if not exists
    if (!localStorage.getItem('insurance_policies')) {
        generateDummyData();
    }
    
    // Load data from localStorage
    loadDataFromStorage();
    
    // Setup event listeners
    setupEventListeners();
    
    // Initialize dashboard
    navigateToPage('dashboard');
    
    // Hide loading screen
    setTimeout(() => {
        $('.loading-screen').fadeOut();
    }, 1000);
};
```

#### `navigateToPage(page)`
**Purpose**: Handles page navigation
**Parameters**: 
- `page` (string): Target page identifier
**Returns**: void
**Description**: Switches between application pages and initializes page-specific functionality

**Implementation**:
```javascript
const navigateToPage = (page) => {
    // Hide all pages
    $('.page').hide();
    
    // Show target page
    $(`#${page}`).show();
    
    // Update active navigation
    $('.nav-item').removeClass('active');
    $(`.nav-item[data-page="${page}"]`).addClass('active');
    
    // Initialize page-specific functionality
    switch(page) {
        case 'dashboard':
            initializeDashboard();
            break;
        case 'policies':
            initializePoliciesPage();
            break;
        case 'renewals':
            initializeRenewalsPage();
            break;
        case 'followups':
            initializeFollowupsPage();
            break;
        case 'reports':
            initializeReportsPage();
            break;
        case 'agents':
            initializeAgents();
            break;
        case 'notifications':
            initializeNotificationsPage();
            break;
        case 'settings':
            initializeSettings();
            break;
    }
};
```

#### `toggleTheme()`
**Purpose**: Switches between light and dark themes
**Parameters**: None
**Returns**: void
**Description**: Toggles theme class and saves preference to localStorage

**Implementation**:
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
    
    // Update theme toggle button
    const themeIcon = $('#themeToggle i');
    themeIcon.removeClass('fa-moon fa-sun');
    themeIcon.addClass(isDark ? 'fa-moon' : 'fa-sun');
};
```

### Data Management Functions

#### `generateDummyData()`
**Purpose**: Creates sample data for demonstration
**Parameters**: None
**Returns**: void
**Description**: Generates comprehensive dummy data for all entities

**Implementation**:
```javascript
const generateDummyData = () => {
    // Generate policies
    allPolicies = generatePolicies(50);
    saveToLocalStorage('insurance_policies', allPolicies);
    
    // Generate renewals
    allRenewals = generateRenewals(30);
    saveToLocalStorage('insurance_renewals', allRenewals);
    
    // Generate follow-ups
    allFollowups = generateFollowups(25);
    saveToLocalStorage('insurance_followups', allFollowups);
    
    // Generate agents
    allAgents = generateAgents(8);
    saveToLocalStorage('insurance_agents', allAgents);
    
    // Initialize filtered data
    filteredPolicies = [...allPolicies];
    filteredRenewals = [...allRenewals];
    filteredFollowups = [...allFollowups];
    filteredAgents = [...allAgents];
};
```

#### `saveToLocalStorage(key, data)`
**Purpose**: Persists data to browser storage
**Parameters**:
- `key` (string): Storage key
- `data` (any): Data to store
**Returns**: void

**Implementation**:
```javascript
const saveToLocalStorage = (key, data) => {
    try {
        localStorage.setItem(key, JSON.stringify(data));
    } catch (error) {
        console.error('Error saving to localStorage:', error);
        showNotification('Error saving data', 'error');
    }
};
```

#### `loadFromLocalStorage(key)`
**Purpose**: Retrieves data from browser storage
**Parameters**:
- `key` (string): Storage key
**Returns**: any - Stored data or null

**Implementation**:
```javascript
const loadFromLocalStorage = (key) => {
    try {
        const data = localStorage.getItem(key);
        return data ? JSON.parse(data) : null;
    } catch (error) {
        console.error('Error loading from localStorage:', error);
        return null;
    }
};
```

### Utility Functions

#### `debounce(func, wait)`
**Purpose**: Limits function execution frequency
**Parameters**:
- `func` (function): Function to debounce
- `wait` (number): Delay in milliseconds
**Returns**: function - Debounced function

**Implementation**:
```javascript
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
```

#### `formatDate(date)`
**Purpose**: Formats date for display
**Parameters**:
- `date` (Date|string): Date to format
**Returns**: string - Formatted date string

**Implementation**:
```javascript
const formatDate = (date) => {
    if (!date) return '';
    
    const dateObj = new Date(date);
    return dateObj.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};
```

#### `formatCurrency(amount)`
**Purpose**: Formats currency values
**Parameters**:
- `amount` (number): Amount to format
**Returns**: string - Formatted currency string

**Implementation**:
```javascript
const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-IN', {
        style: 'currency',
        currency: 'INR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(amount);
};
```

#### `showNotification(message, type)`
**Purpose**: Displays user notifications
**Parameters**:
- `message` (string): Notification message
- `type` (string): Notification type (success, error, warning, info)
**Returns**: void

**Implementation**:
```javascript
const showNotification = (message, type = 'info') => {
    const notification = $(`
        <div class="notification notification-${type}">
            <div class="notification-content">
                <i class="fas fa-${getNotificationIcon(type)}"></i>
                <span>${message}</span>
            </div>
            <button class="notification-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `);
    
    $('.notification-container').append(notification);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        notification.fadeOut(() => notification.remove());
    }, 5000);
    
    // Manual close
    notification.find('.notification-close').click(() => {
        notification.fadeOut(() => notification.remove());
    });
};

const getNotificationIcon = (type) => {
    const icons = {
        success: 'check-circle',
        error: 'exclamation-circle',
        warning: 'exclamation-triangle',
        info: 'info-circle'
    };
    return icons[type] || 'info-circle';
};
```

---

*This is Part 2 of the Developer Documentation. Continue to Part 3 for Data Management, Theme System, and API Integration Points.* 