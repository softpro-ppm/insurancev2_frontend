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

// API Functions
const API_BASE_URL = window.location.origin;

const apiCall = async (endpoint, options = {}) => {
    try {
        // Get CSRF token from meta tag
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        const defaultOptions = {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        };

        // Add CSRF token if available (try both header names for compatibility)
        if (csrfToken) {
            defaultOptions.headers['X-CSRF-TOKEN'] = csrfToken;
            defaultOptions.headers['X-XSRF-TOKEN'] = csrfToken;
        }

        const response = await fetch(endpoint, {
            ...defaultOptions,
            ...options,
            headers: {
                ...defaultOptions.headers,
                ...options.headers
            }
        });

        if (!response.ok) {
            // Try to get the response body for better error handling
            let errorData = null;
            try {
                errorData = await response.json();
            } catch (e) {
                // If we can't parse JSON, create a basic error object
                errorData = { message: `HTTP error! status: ${response.status}` };
            }
            
            const error = new Error(`HTTP error! status: ${response.status}`);
            error.response = { status: response.status, data: errorData };
            throw error;
        }

        return await response.json();
    } catch (error) {
        console.error('API call failed:', error);
        throw error;
    }
};

// Dashboard API calls
const fetchDashboardStats = async () => {
    try {
        const data = await apiCall('/api/dashboard/stats');
        return data;
    } catch (error) {
        console.error('Failed to fetch dashboard stats:', error);
        return null;
    }
};

const fetchRecentPolicies = async () => {
    try {
        const data = await apiCall('/api/dashboard/recent-policies');
        return data.recentPolicies || [];
    } catch (error) {
        console.error('Failed to fetch recent policies:', error);
        return [];
    }
};

const fetchExpiringPolicies = async () => {
    try {
        const data = await apiCall('/api/dashboard/expiring-policies');
        return data.expiringPolicies || [];
    } catch (error) {
        console.error('Failed to fetch expiring policies:', error);
        return [];
    }
};

// Policies API calls
const fetchPolicies = async () => {
    try {
        const data = await apiCall('/api/policies');
        return data.policies || [];
    } catch (error) {
        console.error('Failed to fetch policies:', error);
        return [];
    }
};

const createPolicy = async (policyData) => {
    try {
        const data = await apiCall('/policies', {
            method: 'POST',
            body: JSON.stringify(policyData)
        });
        return data;
    } catch (error) {
        console.error('Failed to create policy:', error);
        throw error;
    }
};

const updatePolicy = async (id, policyData) => {
    try {
        const data = await apiCall(`/policies/${id}`, {
            method: 'PUT',
            body: JSON.stringify(policyData)
        });
        return data;
    } catch (error) {
        console.error('Failed to update policy:', error);
        throw error;
    }
};

const deletePolicy = async (id) => {
    try {
        const data = await apiCall(`/policies/${id}`, {
            method: 'DELETE'
        });
        return data;
    } catch (error) {
        console.error('Failed to delete policy:', error);
        throw error;
    }
};

// Agents API calls
const fetchAgents = async () => {
    try {
        const data = await apiCall('/api/agents');
        return data.agents || [];
    } catch (error) {
        console.error('Failed to fetch agents:', error);
        return [];
    }
};

const createAgent = async (agentData) => {
    try {
        const data = await apiCall('/agents', {
            method: 'POST',
            body: JSON.stringify(agentData)
        });
        return data;
    } catch (error) {
        console.error('Failed to create agent:', error);
        throw error;
    }
};

const updateAgent = async (id, agentData) => {
    try {
        const data = await apiCall(`/agents/${id}`, {
            method: 'PUT',
            body: JSON.stringify(agentData)
        });
        return data;
    } catch (error) {
        console.error('Failed to update agent:', error);
        throw error;
    }
};

const deleteAgent = async (id) => {
    try {
        const data = await apiCall(`/agents/${id}`, {
            method: 'DELETE'
        });
        return data;
    } catch (error) {
        console.error('Failed to delete agent:', error);
        throw error;
    }
};

// Renewals API calls
const fetchRenewals = async () => {
    try {
        const data = await apiCall('/api/renewals');
        return data.renewals || [];
    } catch (error) {
        console.error('Failed to fetch renewals:', error);
        return [];
    }
};

const createRenewal = async (renewalData) => {
    try {
        const data = await apiCall('/renewals', {
            method: 'POST',
            body: JSON.stringify(renewalData)
        });
        return data;
    } catch (error) {
        console.error('Failed to create renewal:', error);
        throw error;
    }
};

const updateRenewal = async (id, renewalData) => {
    try {
        const data = await apiCall(`/renewals/${id}`, {
            method: 'PUT',
            body: JSON.stringify(renewalData)
        });
        return data;
    } catch (error) {
        console.error('Failed to update renewal:', error);
        throw error;
    }
};

const deleteRenewal = async (id) => {
    try {
        const data = await apiCall(`/renewals/${id}`, {
            method: 'DELETE'
        });
        return data;
    } catch (error) {
        console.error('Failed to delete renewal:', error);
        throw error;
    }
};

// Followups API calls
const fetchFollowups = async () => {
    try {
        const data = await apiCall('/api/followups');
        return data.followups || [];
    } catch (error) {
        console.error('Failed to fetch followups:', error);
        return [];
    }
};

const createFollowup = async (followupData) => {
    try {
        const data = await apiCall('/followups', {
            method: 'POST',
            body: JSON.stringify(followupData)
        });
        return data;
    } catch (error) {
        console.error('Failed to create followup:', error);
        throw error;
    }
};

const updateFollowup = async (id, followupData) => {
    try {
        const data = await apiCall(`/followups/${id}`, {
            method: 'PUT',
            body: JSON.stringify(followupData)
        });
        return data;
    } catch (error) {
        console.error('Failed to update followup:', error);
        throw error;
    }
};

const deleteFollowup = async (id) => {
    try {
        const data = await apiCall(`/followups/${id}`, {
            method: 'DELETE'
        });
        return data;
    } catch (error) {
        console.error('Failed to delete followup:', error);
        throw error;
    }
};

// Reports API calls
const fetchReports = async () => {
    try {
        const data = await apiCall('/api/reports');
        return data.reports || [];
    } catch (error) {
        console.error('Failed to fetch reports:', error);
        return [];
    }
};

// Notifications API calls
const fetchNotifications = async () => {
    try {
        const data = await apiCall('/api/notifications');
        return data.notifications || [];
    } catch (error) {
        console.error('Failed to fetch notifications:', error);
        return [];
    }
};

// Settings API calls
const fetchSettings = async () => {
    try {
        const data = await apiCall('/api/settings');
        return data.settings || [];
    } catch (error) {
        console.error('Failed to fetch settings:', error);
        return [];
    }
};

const updateSettings = async (settingsData) => {
    try {
        const data = await apiCall('/settings', {
            method: 'POST',
            body: JSON.stringify(settingsData)
        });
        return data;
    } catch (error) {
        console.error('Failed to update settings:', error);
        throw error;
    }
};

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

// Dummy data for agents
const generateDummyAgents = () => {
    const agents = [
        { name: 'Rajesh Kumar', phone: '+919876543210', email: 'rajesh@example.com', userId: 'AG001' },
        { name: 'Priya Sharma', phone: '+919876543211', email: 'priya@example.com', userId: 'AG002' },
        { name: 'Amit Patel', phone: '+919876543212', email: 'amit@example.com', userId: 'AG003' },
        { name: 'Sneha Singh', phone: '+919876543213', email: 'sneha@example.com', userId: 'AG004' },
        { name: 'Vikram Malhotra', phone: '+919876543214', email: 'vikram@example.com', userId: 'AG005' }
    ];
    
    return agents;
};

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
    const callResults = ['Answered', 'Not Answered', 'Busy', 'Wrong Number', 'Call Back Later'];
    const telecallerNames = ['Rajesh Kumar', 'Priya Sharma', 'Amit Patel', 'Sneha Singh', 'Vikram Malhotra'];
    
    const followups = [];
    
    for (let i = 1; i <= 25; i++) {
        const policy = allPolicies[Math.floor(Math.random() * allPolicies.length)];
        const today = new Date();
        const lastFollowupDate = new Date(today);
        lastFollowupDate.setDate(today.getDate() - Math.floor(Math.random() * 30));
        
        const nextFollowupDate = new Date(lastFollowupDate);
        nextFollowupDate.setDate(lastFollowupDate.getDate() + Math.floor(Math.random() * 7) + 1);
        
        // Generate notes history
        const notesHistory = [];
        const numNotes = Math.floor(Math.random() * 3) + 1;
        
        for (let j = 0; j < numNotes; j++) {
            const noteDate = new Date(lastFollowupDate);
            noteDate.setDate(lastFollowupDate.getDate() - (j * 2));
            
            const noteContent = [
                `Customer ${policy.owner} was contacted regarding ${types[Math.floor(Math.random() * types.length)].toLowerCase()}. ${Math.random() > 0.5 ? 'Customer showed interest and requested more information.' : 'Customer was busy and asked to call back later.'}`,
                `Follow-up call made to ${policy.owner}. ${Math.random() > 0.5 ? 'Discussed policy benefits and customer seemed interested.' : 'Customer was not available, left message.'}`,
                `Called ${policy.owner} for ${types[Math.floor(Math.random() * types.length)].toLowerCase()} follow-up. ${Math.random() > 0.5 ? 'Customer agreed to proceed with the offer.' : 'Customer needs more time to think about it.'}`,
                `Contacted ${policy.owner} regarding policy renewal. ${Math.random() > 0.5 ? 'Customer confirmed renewal and provided updated details.' : 'Customer requested to call back next week.'}`
            ];
            
            notesHistory.push({
                date: noteDate.toISOString().split('T')[0],
                time: `${Math.floor(Math.random() * 12) + 9}:${Math.floor(Math.random() * 60).toString().padStart(2, '0')} ${Math.random() > 0.5 ? 'AM' : 'PM'}`,
                telecaller: telecallerNames[Math.floor(Math.random() * telecallerNames.length)],
                note: noteContent[Math.floor(Math.random() * noteContent.length)],
                callDuration: Math.floor(Math.random() * 15) + 2,
                callResult: callResults[Math.floor(Math.random() * callResults.length)]
            });
        }
        
        // Determine status based on last follow-up
        let status = 'Pending';
        if (Math.random() > 0.7) {
            status = 'Completed';
        } else if (Math.random() > 0.5) {
            status = 'In Progress';
        } else if (Math.random() > 0.3) {
            status = 'No Response';
        }
        
        followups.push({
            id: i,
            customerName: policy.owner,
            phone: policy.phone,
            email: policy.email,
            policyId: Math.random() > 0.3 ? policy.id : null,
            followupType: types[Math.floor(Math.random() * types.length)],
            status: status,
            assignedTo: telecallerNames[Math.floor(Math.random() * telecallerNames.length)],
            priority: Math.random() > 0.6 ? 'High' : Math.random() > 0.3 ? 'Medium' : 'Low',
            lastFollowupDate: lastFollowupDate.toISOString().split('T')[0],
            nextFollowupDate: nextFollowupDate.toISOString().split('T')[0],
            reminderTime: `${Math.floor(Math.random() * 12) + 9}:${Math.floor(Math.random() * 60).toString().padStart(2, '0')}`,
            notesHistory: notesHistory,
            recentNote: notesHistory[0]?.note || 'No notes available',
            createdAt: new Date(Date.now() - Math.random() * 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0]
        });
    }
    
    return followups;
};

// Initialize the application
$(document).ready(function() {
    // Show loading state
    showLoadingState();
    
    // Initialize components with real data
    initializeApplication();
});

// Main initialization function
const initializeApplication = async () => {
    try {
        // Load all data from APIs
        await Promise.all([
            loadDashboardData(),
            loadPoliciesData(),
            loadAgentsData(),
            loadRenewalsData(),
            loadFollowupsData()
        ]);
        
        // Initialize components
        initializeCharts();
        initializeTable();
        initializeAgents();
        initializePoliciesPage();
        initializeRenewalsPage();
        initializeFollowupsPage();
        initializeReportsPage();
        initializeEventListeners();
        initializeModals(); // Initialize modals
        
        // Set default date for policy form
        const today = new Date();
        const oneYearLater = new Date(today);
        oneYearLater.setFullYear(today.getFullYear() + 1);
        oneYearLater.setDate(oneYearLater.getDate() - 1);
        
        $('#startDate').val(today.toISOString().split('T')[0]);
        $('#endDate').val(oneYearLater.toISOString().split('T')[0]);
        
        // Hide loading state
        hideLoadingState();
    } catch (error) {
        console.error('Failed to initialize application:', error);
        showNotification('Failed to load application data', 'error');
        hideLoadingState();
    }
};

// Load dashboard data
const loadDashboardData = async () => {
    try {
        const stats = await fetchDashboardStats();
        if (stats) {
            updateDashboardStats(stats);
        }
        
        const recentPolicies = await fetchRecentPolicies();
        if (recentPolicies.length > 0) {
            updateRecentPoliciesTable(recentPolicies);
        }
        
        const expiringPolicies = await fetchExpiringPolicies();
        if (expiringPolicies.length > 0) {
            updateExpiringPoliciesList(expiringPolicies);
        }
    } catch (error) {
        console.error('Failed to load dashboard data:', error);
    }
};

// Load policies data
const loadPoliciesData = async () => {
    try {
        allPolicies = await fetchPolicies();
        filteredData = [...allPolicies];
        updatePoliciesStats();
    } catch (error) {
        console.error('Failed to load policies data:', error);
        allPolicies = [];
        filteredData = [];
    }
};

// Load agents data
const loadAgentsData = async () => {
    try {
        allAgents = await fetchAgents();
    } catch (error) {
        console.error('Failed to load agents data:', error);
        allAgents = [];
    }
};

// Load renewals data
const loadRenewalsData = async () => {
    try {
        allRenewals = await fetchRenewals();
    } catch (error) {
        console.error('Failed to load renewals data:', error);
        allRenewals = [];
    }
};

// Load followups data
const loadFollowupsData = async () => {
    try {
        allFollowups = await fetchFollowups();
    } catch (error) {
        console.error('Failed to load followups data:', error);
        allFollowups = [];
    }
};

// Update dashboard statistics
const updateDashboardStats = (stats) => {
    if (stats.stats) {
        $('#monthlyPremium').text('₹' + (stats.stats.monthlyPremium || 0).toLocaleString());
        $('#yearlyPremium').text('₹' + (stats.stats.yearlyPremium || 0).toLocaleString() + ' (FY)');
        $('#monthlyPolicies').text(stats.stats.monthlyPolicies || 0);
        $('#yearlyPolicies').text(stats.stats.yearlyPolicies || 0 + ' (FY)');
        $('#monthlyRenewals').text(stats.stats.monthlyRenewals || 0);
        $('#pendingRenewals').text(stats.stats.pendingRenewals || 0 + ' Pending');
        $('#monthlyRevenue').text('₹' + (stats.stats.monthlyRevenue || 0).toLocaleString());
        $('#yearlyRevenue').text('₹' + (stats.stats.yearlyRevenue || 0).toLocaleString() + ' (FY)');
    }
    
    if (stats.chartData) {
        updateDashboardCharts(stats.chartData, stats.policyTypes);
    }
};

// Update recent policies table
const updateRecentPoliciesTable = (policies) => {
    const tbody = $('#policiesTable tbody');
    tbody.empty();
    
    policies.forEach((policy, index) => {
        const row = `
            <tr>
                <td>${index + 1}</td>
                <td>${policy.policyType}</td>
                <td>${policy.customerName}</td>
                <td>${policy.phone}</td>
                <td>${policy.companyName}</td>
                <td>${policy.endDate}</td>
                <td>₹${policy.premium}</td>
                <td><span class="status-badge ${policy.status.toLowerCase()}">${policy.status}</span></td>
                <td>
                    <button class="action-btn view" data-policy-id="${policy.id}" title="View Details">
                        <i class="fas fa-eye"></i>
                    </button>
                </td>
            </tr>
        `;
        tbody.append(row);
    });

    // Bind view action for recent policies
    tbody.find('.action-btn.view').off('click').on('click', function() {
        const policyId = parseInt($(this).data('policy-id'));
        if (!isNaN(policyId)) {
            viewPolicyDetails(policyId);
        }
    });
};

// Update expiring policies list
const updateExpiringPoliciesList = (policies) => {
    const container = $('#expiringPoliciesList');
    if (container.length) {
        container.empty();
        
        policies.forEach(policy => {
            const item = `
                <div class="expiring-policy-item">
                    <div class="policy-info">
                        <h4>${policy.policyNumber}</h4>
                        <p>${policy.customerName} - ${policy.phone}</p>
                    </div>
                    <div class="expiry-info">
                        <span class="expiry-date">${policy.endDate}</span>
                        <span class="days-left ${policy.daysUntilExpiry <= 7 ? 'urgent' : ''}">
                            ${policy.daysUntilExpiry} days left
                        </span>
                    </div>
                </div>
            `;
            container.append(item);
        });
    }
};

// Update dashboard charts
const updateDashboardCharts = (chartData, policyTypes) => {
    // Update bar chart with real data
    if (window.barChart && chartData) {
        const labels = chartData.map(item => item.month);
        const premiumData = chartData.map(item => item.premium);
        const revenueData = chartData.map(item => item.revenue);
        const policiesData = chartData.map(item => item.policies);
        
        window.barChart.data.labels = labels;
        window.barChart.data.datasets[0].data = premiumData;
        window.barChart.data.datasets[1].data = revenueData;
        window.barChart.data.datasets[2].data = policiesData;
        window.barChart.update();
    }
    
    // Update pie chart with real data
    if (window.pieChart && policyTypes) {
        const labels = Object.keys(policyTypes);
        const data = Object.values(policyTypes);
        
        window.pieChart.data.labels = labels;
        window.pieChart.data.datasets[0].data = data;
        window.pieChart.update();
    }
};

// Loading state functions
const showLoadingState = () => {
    $('body').append('<div id="loadingOverlay"><div class="loading-spinner"></div></div>');
};

const hideLoadingState = () => {
    $('#loadingOverlay').fadeOut(300, function() {
        $(this).remove();
    });
};

// Initialize charts
const initializeCharts = () => {
    // Only initialize charts on dashboard page
    if (!$('#dashboard').hasClass('active')) {
        return;
    }
    
    // Wait for DOM to be ready
    setTimeout(() => {
        const barCtx = document.getElementById('barChart');
        const pieCtx = document.getElementById('pieChart');
        
        if (!barCtx || !pieCtx) {
            console.log('Chart canvases not found - this is normal on non-dashboard pages');
            return;
        }
        
        // Destroy existing charts if they exist
        if (window.barChart) {
            window.barChart.destroy();
        }
        if (window.pieChart) {
            window.pieChart.destroy();
        }
        
        // Bar Chart
        const barChart = new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [
                    {
                        label: 'Premium (₹)',
                        data: [45000, 52000, 48000, 61000, 55000, 67000, 72000, 68000, 75000, 82000, 78000, 85000],
                        backgroundColor: 'rgba(79, 70, 229, 0.8)',
                        borderColor: 'rgba(79, 70, 229, 1)',
                        borderWidth: 1,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Revenue (₹)',
                        data: [12000, 14000, 13000, 16000, 15000, 18000, 19000, 17000, 20000, 22000, 21000, 23000],
                        backgroundColor: 'rgba(16, 185, 129, 0.8)',
                        borderColor: 'rgba(16, 185, 129, 1)',
                        borderWidth: 1,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Policies (Count)',
                        data: [15, 18, 16, 22, 20, 25, 28, 24, 30, 32, 29, 35],
                        backgroundColor: 'rgba(245, 158, 11, 0.8)',
                        borderColor: 'rgba(245, 158, 11, 1)',
                        borderWidth: 1,
                        yAxisID: 'y1',
                        type: 'line'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            color: $('body').hasClass('dark-theme') ? '#F1F5F9' : '#111827',
                            usePointStyle: true,
                            padding: 20
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.dataset.label.includes('Count')) {
                                    label += context.parsed.y + ' policies';
                                } else {
                                    label += '₹' + context.parsed.y.toLocaleString();
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Month',
                            color: $('body').hasClass('dark-theme') ? '#F1F5F9' : '#111827'
                        },
                        ticks: {
                            color: $('body').hasClass('dark-theme') ? '#F1F5F9' : '#111827'
                        },
                        grid: {
                            color: $('body').hasClass('dark-theme') ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)'
                        }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Amount (₹)',
                            color: $('body').hasClass('dark-theme') ? '#F1F5F9' : '#111827'
                        },
                        ticks: {
                            color: $('body').hasClass('dark-theme') ? '#F1F5F9' : '#111827',
                            callback: function(value) {
                                return '₹' + value.toLocaleString();
                            }
                        },
                        grid: {
                            color: $('body').hasClass('dark-theme') ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Number of Policies',
                            color: $('body').hasClass('dark-theme') ? '#F1F5F9' : '#111827'
                        },
                        ticks: {
                            color: $('body').hasClass('dark-theme') ? '#F1F5F9' : '#111827'
                        },
                        grid: {
                            drawOnChartArea: false,
                        },
                    }
                }
            }
        });

        // Pie Chart
        const pieChart = new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: ['Motor Insurance', 'Health Insurance', 'Life Insurance'],
                datasets: [{
                    data: [65, 20, 15],
                    backgroundColor: [
                        'rgba(79, 70, 229, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)'
                    ],
                    borderColor: [
                        'rgba(79, 70, 229, 1)',
                        'rgba(16, 185, 129, 1)',
                        'rgba(245, 158, 11, 1)'
                    ],
                    borderWidth: 2,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: $('body').hasClass('dark-theme') ? '#F1F5F9' : '#111827',
                            usePointStyle: true,
                            padding: 20
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        // Store chart references
        window.barChart = barChart;
        window.pieChart = pieChart;
    }, 100);
};

// Initialize data table
const initializeTable = () => {
    renderTable();
    updatePagination();
};

// Render table with current data
const renderTable = () => {
    const startIndex = (currentPage - 1) * rowsPerPage;
    const endIndex = startIndex + rowsPerPage;
    const pageData = filteredData.slice(startIndex, endIndex);
    
    const tbody = $('#policiesTableBody');
    
    // Use document fragment for better performance
    const fragment = document.createDocumentFragment();
    
    pageData.forEach((policy, idx) => {
        const row = document.createElement('tr');
        
        // Use the correct property names from the API response
        const policyType = policy.policyType || policy.type || 'Unknown';
        const customerName = policy.customerName || policy.owner || 'Unknown';
        const phone = policy.phone || 'Unknown';
        const companyName = policy.companyName || policy.company || 'Unknown';
        const endDate = policy.endDate || 'Unknown';
        const premium = policy.premium || 0;
        const status = policy.status || 'Active';
        
        row.innerHTML = `
            <td>${startIndex + idx + 1}</td>
            <td><span class="policy-type-badge ${policyType.toLowerCase()}">${policyType}</span></td>
            <td>${customerName}</td>
            <td>${phone}</td>
            <td>${getShortCompanyName(companyName)}</td>
            <td>${formatDate(endDate)}</td>
            <td>₹${premium.toLocaleString()}</td>
            <td><span class="status-badge ${status.toLowerCase()}">${status}</span></td>
            <td>
                <div class="action-buttons">
                    <button class="action-btn edit" data-policy-id="${policy.id}" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="action-btn delete" data-policy-id="${policy.id}" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                    <button class="action-btn view" data-policy-id="${policy.id}" title="View Details">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </td>
        `;
        fragment.appendChild(row);
    });
    
    tbody.empty().append(fragment);
    updatePaginationInfo();
    
    // Add event listeners for action buttons
    tbody.find('.action-btn.edit').click(function() {
        const policyId = parseInt($(this).data('policy-id'));
        editPolicy(policyId);
    });
    
    tbody.find('.action-btn.delete').click(function() {
        const policyId = parseInt($(this).data('policy-id'));
        deletePolicyHandler(policyId);
    });
    
    tbody.find('.action-btn.view').off('click').on('click', function() {
        const policyId = parseInt($(this).data('policy-id'));
        if (!isNaN(policyId)) {
            viewPolicyDetails(policyId);
        }
    });
};

// Update pagination
const updatePagination = () => {
    const totalPages = Math.ceil(filteredData.length / rowsPerPage);
    const pageNumbers = $('#pageNumbers');
    pageNumbers.empty();
    
    // Previous button
    $('#prevPage').prop('disabled', currentPage === 1);
    
    // Page numbers
    const maxVisiblePages = 5;
    let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
    let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
    
    if (endPage - startPage + 1 < maxVisiblePages) {
        startPage = Math.max(1, endPage - maxVisiblePages + 1);
    }
    
    for (let i = startPage; i <= endPage; i++) {
        const pageBtn = $(`<button class="page-number ${i === currentPage ? 'active' : ''}">${i}</button>`);
        pageBtn.click(() => goToPage(i));
        pageNumbers.append(pageBtn);
    }
    
    // Next button
    $('#nextPage').prop('disabled', currentPage === totalPages);
};

// Update pagination info
const updatePaginationInfo = () => {
    const startRecord = (currentPage - 1) * rowsPerPage + 1;
    const endRecord = Math.min(currentPage * rowsPerPage, filteredData.length);
    
    $('#startRecord').text(startRecord);
    $('#endRecord').text(endRecord);
    $('#totalRecords').text(filteredData.length);
};

// Go to specific page
const goToPage = (page) => {
    currentPage = page;
    renderTable();
    updatePagination();
};

// Initialize agents page
const initializeAgents = () => {
    agentsFilteredData = [...allAgents];
    renderAgentsTable();
    updateAgentsPagination();
    updateAgentsStats();
};

// Initialize policies page
const initializePoliciesPage = () => {
    policiesFilteredData = [...allPolicies];
    renderPoliciesTable();
    updatePoliciesPagination();
    updatePoliciesStats();
};

// Initialize renewals page
const initializeRenewalsPage = () => {
    // Only initialize renewals if we're on the renewals page
    if (!$('#renewals').hasClass('active')) {
        return;
    }
    
    // Use real data from API
    renewalsFilteredData = [...allRenewals];
    renderRenewalsTable();
    updateRenewalsPagination();
    updateRenewalsStats();
    
    // Initialize search and filter functionality
    $('#renewalsSearch').off('input').on('input', debounce(handleRenewalsSearch, 300));
    $('#renewalStatusFilter, #renewalPriorityFilter').off('change').on('change', handleRenewalsFilter);
    $('#renewalsRowsPerPage').off('change').on('change', handleRenewalsRowsPerPageChange);
    
    // Initialize sort functionality
    $('#renewalsTable th[data-sort]').off('click').on('click', function() {
        const column = $(this).data('sort');
        handleRenewalsSort(column);
    });
};

// Initialize follow-ups page
const initializeFollowupsPage = () => {
    followupsFilteredData = [...allFollowups];
    renderFollowupsTable();
    updateFollowupsPagination();
    updateFollowupsStats();
};

// Initialize reports page
const initializeReportsPage = () => {
    // Only initialize reports if we're on the reports page
    if (!$('#reports').hasClass('active')) {
        return;
    }
    
    // Set default date range (last 30 days)
    const endDate = new Date();
    const startDate = new Date();
    startDate.setDate(endDate.getDate() - 30);
    
    $('#reportStartDate').val(startDate.toISOString().split('T')[0]);
    $('#reportEndDate').val(endDate.toISOString().split('T')[0]);
    
    reportDateRange.start = startDate.toISOString().split('T')[0];
    reportDateRange.end = endDate.toISOString().split('T')[0];
    
    updateKPIs();
    initializeReportCharts();
    initializeReportTabs();
    generateReports();
};

// Initialize event listeners
const initializeEventListeners = () => {
    // Theme toggle
    $('#themeToggle').click(toggleTheme);
    
    // Sidebar toggle
    $('#sidebarToggle').off('click').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        toggleSidebar();
    });
    
    // Navigation
    $('.nav-item').click(function() {
        const page = $(this).data('page');
        navigateToPage(page);
    });
    
    // Profile dropdown
    $('#profileBtn').click(toggleProfileDropdown);
    $(document).click(function(e) {
        if (!$(e.target).closest('.profile-dropdown').length) {
            $('#profileDropdown').removeClass('show');
        }
    });
    
    // Modal controls - Policy Modal
    $('#addPolicyBtn, #addPolicyFromPoliciesBtn').off('click').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        openPolicyModal();
    });
    $('#closePolicyModal, #cancelPolicy').click(() => closePolicyModal());
    
    // Modal controls - Agent Modal
    $('#addAgentBtn').click(() => openAgentModal());
    $('#closeAgentModal, #cancelAgent').click(() => closeAgentModal());
    
    // Modal controls - Followup Modal
    $('#addFollowupBtn').click(() => openFollowupModal());
    $('#closeFollowupModal, #cancelFollowup').click(() => closeFollowupModal());
    
    // Modal controls - Renewal Modal
    $('#addRenewalBtn').click(() => openRenewalModal());
    $('#closeRenewalModal, #cancelRenewal').click(() => closeRenewalModal());
    
    // Form submissions
    $('#policyForm').submit(handlePolicySubmit);
    $('#agentForm').submit(handleAgentSubmit);
    $('#followupForm').submit(handleFollowupSubmit);
    $('#renewalForm').submit(handleRenewalSubmit);
    
    // Modal backdrop clicks
    $(document).on('click', '.modal', function(e) {
        if (e.target === this) {
            $(this).removeClass('show');
        }
    });
    
    // Escape key to close modals
    $(document).keydown(function(e) {
        if (e.key === 'Escape') {
            $('.modal.show').removeClass('show');
        }
    });
    
    // Table controls
    $('#policySearch').on('input', handleSearch);
    $('#rowsPerPage').change(handleRowsPerPageChange);
    
    // Pagination
    $('#prevPage').click(() => goToPage(currentPage - 1));
    $('#nextPage').click(() => goToPage(currentPage + 1));
    
    // Table sorting
    $('.data-table th[data-sort]').click(function() {
        const column = $(this).data('sort');
        handleSort(column);
    });
    
    // Chart period change
    $('#chartPeriod').change(handleChartPeriodChange);
    
    // Policies page controls
    $('#policiesSearch').on('input', handlePoliciesSearch);
    $('#policyTypeFilter').change(handlePoliciesFilter);
    $('#statusFilter').change(handlePoliciesFilter);
    $('#policiesRowsPerPage').change(handlePoliciesRowsPerPageChange);
    $('#exportPolicies').click(exportPoliciesData);
    
    // Policies pagination
    $('#policiesPrevPage').click(() => goToPoliciesPage(policiesCurrentPage - 1));
    $('#policiesNextPage').click(() => goToPoliciesPage(policiesCurrentPage + 1));
    
    // Policies table sorting
    $('#policiesPageTable th[data-sort]').click(function() {
        const column = $(this).data('sort');
        handlePoliciesSort(column);
    });
    
    // Renewals page controls
    $('#renewalsSearch').on('input', handleRenewalsSearch);
    $('#renewalStatusFilter').change(handleRenewalsFilter);
    $('#renewalPriorityFilter').change(handleRenewalsFilter);
    $('#renewalsRowsPerPage').change(handleRenewalsRowsPerPageChange);
    $('#exportRenewals').click(exportRenewalsData);
    
    // Renewals pagination
    $('#renewalsPrevPage').click(() => goToRenewalsPage(renewalsCurrentPage - 1));
    $('#renewalsNextPage').click(() => goToRenewalsPage(renewalsCurrentPage + 1));
    
    // Renewals table sorting
    $('#renewalsTable th[data-sort]').click(function() {
        const column = $(this).data('sort');
        handleRenewalsSort(column);
    });
    
    // Follow-ups page controls
    $('#followupsSearch').on('input', handleFollowupsSearch);
    $('#followupStatusFilter').change(handleFollowupsFilter);
    $('#followupTypeFilter').change(handleFollowupsFilter);
    $('#followupsRowsPerPage').change(handleFollowupsRowsPerPageChange);
    $('#exportFollowups').click(exportFollowupsData);
    
    // Follow-ups pagination
    $('#followupsPrevPage').click(() => goToFollowupsPage(followupsCurrentPage - 1));
    $('#followupsNextPage').click(() => goToFollowupsPage(followupsCurrentPage + 1));
    
    // Follow-ups table sorting
    $('#followupsTable th[data-sort]').click(function() {
        const column = $(this).data('sort');
        handleFollowupsSort(column);
    });
    
    // Agents page controls
    $('#agentsSearch').on('input', handleAgentsSearch);
    $('#agentsRowsPerPage').change(handleAgentsRowsPerPageChange);
    $('#exportAgents').click(exportAgentsData);
    
    // Agents pagination
    $('#agentsPrevPage').click(() => goToAgentsPage(agentsCurrentPage - 1));
    $('#agentsNextPage').click(() => goToAgentsPage(agentsCurrentPage + 1));
    
    // Agents table sorting
    $('#agentsTable th[data-sort]').click(function() {
        const column = $(this).data('sort');
        handleAgentsSort(column);
    });
    
    // Settings page controls
    $('.settings-tab-btn').click(function() {
        const tab = $(this).data('tab');
        switchSettingsTab(tab);
    });
    
    $('#saveSettings').click(saveSettings);
    $('#cancelSettings').click(cancelSettings);
    $('#exportAllData').click(exportAllData);
    $('#clearCache').click(clearCache);
    $('#resetSettings').click(resetSettings);
    
    // Auto-generate User ID for agent
    $('#agentPhone').on('input', function() {
        const phone = $(this).val();
        if (phone.length >= 10) {
            $('#agentUserId').val(`AG${phone.slice(-6)}`);
        }
    });
    
    // Auto-calculate end date for policy
    $('#startDate').change(function() {
        const startDate = new Date($(this).val());
        const endDate = new Date(startDate);
        endDate.setFullYear(endDate.getFullYear() + 1);
        endDate.setDate(endDate.getDate() - 1);
        $('#endDate').val(endDate.toISOString().split('T')[0]);
    });

    // Multi-step modal functions
    initializeMultiStepModal();
    
    // View Policy Modal controls
    $('#closeViewPolicyModal').click(() => closeViewPolicyModal());
    $('#closeViewPolicyBtn').click(() => closeViewPolicyModal());
    $('#editPolicyFromViewBtn').click(function() {
        const policyId = parseInt($('#viewPolicyId').text().replace('#', ''));
        closeViewPolicyModal();
        editPolicy(policyId);
    });
    
    // Renewal Modal controls
    $('#closeRenewalModal').click(() => closeRenewalModal());
    $('#cancelRenewal').click(() => closeRenewalModal());
    $('#renewalForm').submit(handleRenewalSubmit);
    
    // Policy selection in renewal modal
    $('#renewalPolicyId').change(function() {
        const policyId = parseInt($(this).val());
        if (policyId) {
            const policy = allPolicies.find(p => p.id === policyId);
            if (policy) {
                $('#renewalCustomerName').val(policy.owner);
                $('#renewalPolicyType').val(policy.type);
                $('#renewalExpiryDate').val(policy.endDate);
            }
        }
    });
    
    // Follow-up Modal controls
    $('#closeFollowupModal').click(() => closeFollowupModal());
    $('#cancelFollowup').click(() => closeFollowupModal());
    $('#followupForm').submit(handleFollowupSubmit);
    
    // Policy selection in follow-up modal
    $('#followupPolicyId').change(function() {
        const policyId = parseInt($(this).val());
        if (policyId) {
            const policy = allPolicies.find(p => p.id === policyId);
            if (policy) {
                $('#followupCustomerName').val(policy.owner);
                $('#followupPhone').val(policy.phone);
                $('#followupEmail').val(policy.email || '');
            }
        }
    });
};

// Theme toggle
const toggleTheme = () => {
    $('body').toggleClass('dark-theme');
    const isDark = $('body').hasClass('dark-theme');
    
    // Update theme toggle icon
    const icon = $('#themeToggle i');
    icon.removeClass('fas fa-moon fas fa-sun');
    icon.addClass(isDark ? 'fas fa-sun' : 'fas fa-moon');
    
    // Update charts colors if they exist
    if (window.barChart) {
        updateChartColors(window.barChart);
    }
    if (window.pieChart) {
        updateChartColors(window.pieChart);
    }
};

// Update chart colors for theme
const updateChartColors = (chart) => {
    const isDark = $('body').hasClass('dark-theme');
    const textColor = isDark ? '#F1F5F9' : '#111827';
    const gridColor = isDark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';
    
    if (chart.options.scales) {
        chart.options.scales.y.ticks.color = textColor;
        chart.options.scales.y.grid.color = gridColor;
        chart.options.scales.x.ticks.color = textColor;
        chart.options.scales.x.grid.color = gridColor;
    }
    
    if (chart.options.plugins.legend) {
        chart.options.plugins.legend.labels.color = textColor;
    }
    
    chart.update();
};

// Sidebar toggle
const toggleSidebar = () => {
    const sidebar = $('#sidebar');
    const mainContent = $('#mainContent');
    
    if (window.innerWidth <= 1024) {
        // Mobile/Tablet behavior
        sidebar.toggleClass('show');
    } else {
        // Desktop behavior
        sidebar.toggleClass('collapsed');
    }
    
    // Update main content margin
    if (sidebar.hasClass('collapsed') && window.innerWidth > 1024) {
        mainContent.css('margin-left', '80px');
    } else if (!sidebar.hasClass('collapsed') && window.innerWidth > 1024) {
        mainContent.css('margin-left', '280px');
    } else {
        mainContent.css('margin-left', '0');
    }
};

// Close sidebar on mobile when clicking outside
$(document).on('click', function(e) {
    if (window.innerWidth <= 1024) {
        const sidebar = $('#sidebar');
        const sidebarToggle = $('#sidebarToggle');
        
        if (!sidebar.is(e.target) && 
            sidebar.has(e.target).length === 0 && 
            !sidebarToggle.is(e.target) && 
            sidebar.hasClass('show')) {
            sidebar.removeClass('show');
        }
    }
});

// Handle window resize
$(window).on('resize', function() {
    const sidebar = $('#sidebar');
    const mainContent = $('#mainContent');
    
    if (window.innerWidth > 1024) {
        sidebar.removeClass('show');
        if (sidebar.hasClass('collapsed')) {
            mainContent.css('margin-left', '80px');
        } else {
            mainContent.css('margin-left', '280px');
        }
    } else {
        sidebar.removeClass('collapsed');
        mainContent.css('margin-left', '0');
    }
});

// Navigation
const navigateToPage = (page) => {
    // Navigate to Laravel routes instead of SPA navigation
    const routes = {
        'dashboard': '/dashboard',
        'policies': '/policies',
        'renewals': '/renewals',
        'followups': '/followups',
        'reports': '/reports',
        'agents': '/agents',
        'notifications': '/notifications',
        'settings': '/settings'
    };
    
    if (routes[page]) {
        window.location.href = routes[page];
    }
};

// Profile dropdown toggle
const toggleProfileDropdown = () => {
    $('#profileDropdown').toggleClass('show');
};

// Modal functions
const openPolicyModal = () => {
    $('#policyModalTitle').text('Add New Policy');
    $('#savePolicyBtn').text('Add Policy');
    $('#policyForm')[0].reset();
    $('#policyForm').removeData('edit-id');
    $('#policyModal').addClass('show');
};

const closePolicyModal = () => {
    $('#policyModal').removeClass('show');
    
    // Reset form
    $('#policyForm')[0].reset();
    
    // Reset modal state
    $('#policyForm').removeData('edit-id');
    $('#policyModalTitle').text('Add New Policy');
    $('#savePolicyBtn').text('Add Policy');
    
    // Reset steps
    $('#step1').show();
    $('#step2, #step3').hide();
    
    // Reset policy type and business type selections
    $('#policyTypeSelect').val('');
    $('#businessTypeSelect').val('');
    
    // Disable next buttons
    $('#nextStep1, #nextStep2').prop('disabled', true);
    
    // Reset global variables
    selectedPolicyType = null;
    selectedBusinessType = null;
    
    // Disable validation on all hidden fields
    $('.policy-form input[required], .policy-form select[required]').prop('required', false);
    
    // Hide all forms
    $('.policy-form').hide();
};

const openAgentModal = () => {
    $('#agentModalTitle').text('Add New Agent');
    $('#saveAgentBtn').text('Add Agent');
    $('#agentForm')[0].reset();
    $('#agentForm').removeData('edit-id');
    $('#agentModal').addClass('show');
};

const closeAgentModal = () => {
    $('#agentModal').removeClass('show');
    $('#agentForm')[0].reset();
    $('#agentForm').removeData('edit-id');
    $('#agentModalTitle').text('Add New Agent');
    $('#saveAgentBtn').text('Add Agent');
};

const closeViewPolicyModal = () => {
    $('#viewPolicyModal').removeClass('show');
};

const downloadDocument = (documentType) => {
    const policyId = $('#viewPolicyModal').data('policy-id');
    if (!policyId) {
        showNotification('Policy ID not found', 'error');
        return;
    }
    
    // Create download URL
    const downloadUrl = `/api/policies/${policyId}/download/${documentType}`;
    
    // Create temporary link and trigger download
    const link = document.createElement('a');
    link.href = downloadUrl;
    link.download = `${documentType}_document.pdf`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    showNotification(`Downloading ${documentType} document...`, 'info');
};

const setupDocumentDownloadButtons = (policy) => {
    // Store policy ID in modal for download functions
    $('#viewPolicyModal').data('policy-id', policy.id);
    
    // Enable/disable download buttons based on document availability
    const documents = {
        'policy': policy.policyCopyPath,
        'rc': policy.rcCopyPath,
        'aadhar': policy.aadharCopyPath,
        'pan': policy.panCopyPath,
        'medical': policy.medicalReportsPath
    };
    
    Object.keys(documents).forEach(docType => {
        const button = $(`#download${docType.charAt(0).toUpperCase() + docType.slice(1)}Btn`);
        if (documents[docType]) {
            button.prop('disabled', false).removeClass('disabled');
        } else {
            button.prop('disabled', true).addClass('disabled');
        }
    });
};

// Form handlers
const handlePolicySubmit = async (e) => {
    e.preventDefault();
    
    // Prepare form for submission to prevent validation errors on hidden fields
    prepareFormForSubmission();
    
    // Determine which policy type is currently active
    const activePolicyType = $('#policyTypeSelect').val() || 'Motor';
    const businessType = $('#businessTypeSelect').val() || 'Self';
    
    // Build policy data based on the active policy type
    let policyData = {
        policyType: activePolicyType,
        businessType: businessType,
        customerName: '',
        customerPhone: '',
        customerEmail: '',
        companyName: '',
        insuranceType: '',
        startDate: '',
        endDate: '',
        premium: 0,
        customerPaidAmount: 0,
        revenue: 0,
        vehicleNumber: '',
        vehicleType: '',
        payout: 0
    };
    
    // Get data based on active policy type
    if (activePolicyType === 'Motor') {
        policyData = {
            ...policyData,
            customerName: $('#customerName').val() || '',
            customerPhone: $('#customerPhone').val() || '',
            customerEmail: $('#customerEmail').val() || '',
            companyName: $('#companyName').val() || '',
            insuranceType: $('#insuranceType').val() || '',
            startDate: $('#startDate').val() || '',
            endDate: $('#endDate').val() || '',
            premium: parseFloat($('#premium').val() || 0),
            customerPaidAmount: parseFloat($('#customerPaidAmount').val() || 0),
            revenue: parseFloat($('#revenue').val() || 0),
            vehicleNumber: $('#vehicleNumber').val() || '',
            vehicleType: $('#vehicleType').val() || '',
            payout: parseFloat($('#payout').val() || 0)
        };
    } else if (activePolicyType === 'Health') {
        policyData = {
            ...policyData,
            customerName: $('#healthCustomerName').val() || '',
            customerPhone: $('#healthCustomerPhone').val() || '',
            customerEmail: $('#healthCustomerEmail').val() || '',
            companyName: $('#healthCompanyName').val() || '',
            insuranceType: $('#healthPlanType').val() || '',
            startDate: $('#healthStartDate').val() || '',
            endDate: $('#healthEndDate').val() || '',
            premium: parseFloat($('#healthPremium').val() || 0),
            customerPaidAmount: parseFloat($('#healthCustomerPaid').val() || 0),
            revenue: parseFloat($('#healthRevenue').val() || 0),
            payout: parseFloat($('#healthPayout').val() || 0)
        };
    } else if (activePolicyType === 'Life') {
        policyData = {
            ...policyData,
            customerName: $('#lifeCustomerName').val() || '',
            customerPhone: $('#lifeCustomerPhone').val() || '',
            customerEmail: $('#lifeCustomerEmail').val() || '',
            companyName: $('#lifeCompanyName').val() || '',
            insuranceType: $('#lifePlanType').val() || '',
            startDate: $('#lifeStartDate').val() || '',
            endDate: $('#lifeEndDate').val() || '',
            premium: parseFloat($('#lifePremium').val() || 0),
            customerPaidAmount: parseFloat($('#lifeCustomerPaid').val() || 0),
            revenue: parseFloat($('#lifeRevenue').val() || 0),
            payout: parseFloat($('#lifePayout').val() || 0)
        };
    }
    
    // Validate required fields based on active policy type
    if (!policyData.customerName || !policyData.customerPhone || !policyData.companyName) {
        showNotification('Please fill in all required fields (Customer Name, Phone, Company)', 'error');
        return;
    }
    
    // Validate dates
    if (!policyData.startDate || !policyData.endDate) {
        showNotification('Please select start and end dates', 'error');
        return;
    }
    
    // Validate amounts
    if (policyData.premium <= 0 || policyData.revenue <= 0) {
        showNotification('Premium and revenue must be greater than 0', 'error');
        return;
    }

    // Client-side phone validation: must be exactly 10 digits
    const phoneDigits = (policyData.customerPhone || '').replace(/\D/g, '');
    if (phoneDigits.length !== 10) {
        showNotification('Phone number must be exactly 10 digits', 'error');
        return;
    }
    
    // Additional validation for Motor policies
    if (activePolicyType === 'Motor') {
        if (!policyData.vehicleNumber || !policyData.vehicleType) {
            showNotification('Please fill in vehicle number and type for Motor policies', 'error');
            return;
        }
    }
    
    try {
        const editId = $('#policyForm').data('edit-id');
        
        console.log('Submitting policy data:', policyData);
        
        if (editId) {
            // Update existing policy
            const response = await updatePolicy(editId, policyData);
            
            // Update local data
            const index = allPolicies.findIndex(p => p.id === editId);
            if (index !== -1) {
                allPolicies[index] = { ...allPolicies[index], ...response.policy };
            }
            
            showNotification('Policy updated successfully!', 'success');
            // Redirect to dashboard after update
            window.location.href = '/dashboard';
        } else {
            // Create new policy
            console.log('Creating new policy with data:', JSON.stringify(policyData, null, 2));
            const response = await createPolicy(policyData);
            
            // Add to local data
            if (response.policy) {
                allPolicies.push(response.policy);
            }
            
            showNotification('Policy added successfully!', 'success');
            // Redirect to dashboard after create
            window.location.href = '/dashboard';
        }
        
        // Update filtered data
        filteredData = [...allPolicies];
        policiesFilteredData = [...allPolicies];
        
        // Close modal and refresh
        closePolicyModal();
        renderTable();
        updatePagination();
        
        // Update policies page if it's currently active
        if ($('#policies').hasClass('active')) {
            renderPoliciesTable();
            updatePoliciesPagination();
            updatePoliciesStats();
        }
        
        // Update renewals page if it's currently active
        if ($('#renewals').hasClass('active')) {
            renderRenewalsTable();
            updateRenewalsPagination();
            updateRenewalsStats();
        }
        
        // Update follow-ups page if it's currently active
        if ($('#followups').hasClass('active')) {
            renderFollowupsTable();
            updateFollowupsPagination();
            updateFollowupsStats();
        }
        
        // Update agents page if it's currently active
        if ($('#agents').hasClass('active')) {
            renderAgentsTable();
            updateAgentsPagination();
            updateAgentsStats();
        }
        
    } catch (error) {
        console.error('Failed to save policy:', error);
        
        // Handle validation errors
        if (error.response && error.response.data) {
            if (error.response.data.errors) {
                const errors = error.response.data.errors;
                const errorMessages = Object.values(errors).flat().join(', ');
                showNotification(errorMessages, 'error');
            } else if (error.response.data.message) {
                showNotification(error.response.data.message, 'error');
            } else {
                showNotification(`Validation failed: ${error.response.status}`, 'error');
            }
        } else {
            showNotification('Failed to save policy. Please check your input and try again.', 'error');
        }
    }
};

const handleAgentSubmit = async (e) => {
    e.preventDefault();
    
    // Get form data
    const agentData = {
        name: $('#agentName').val().trim(),
        phone: $('#agentPhone').val().trim(),
        email: $('#agentEmail').val().trim(),
        userId: $('#agentUserId').val().trim(),
        status: $('#agentStatus').val(),
        address: $('#agentAddress').val().trim()
    };
    
    // Only add password if it's not empty
    const password = $('#agentPassword').val();
    if (password && password.trim() !== '') {
        agentData.password = password;
    }
    
    // Validate required fields
    if (!agentData.name || !agentData.phone || !agentData.email) {
        showNotification('Please fill in all required fields', 'error');
        return;
    }
    
    try {
        const editId = $('#agentForm').data('edit-id');
        
        if (editId) {
            // Update existing agent
            const response = await updateAgent(editId, agentData);
            
            // Update local data
            const index = allAgents.findIndex(a => a.id === editId);
            if (index !== -1) {
                allAgents[index] = { ...allAgents[index], ...agentData };
            }
            
            showNotification('Agent updated successfully!', 'success');
        } else {
            // Create new agent - password is required for new agents
            if (!password || password.trim() === '') {
                showNotification('Password is required for new agents', 'error');
                return;
            }
            
            // Validate password length
            if (password.trim().length < 6) {
                showNotification('Password must be at least 6 characters long', 'error');
                return;
            }
            
            agentData.password = password;
            
            console.log('Creating agent with data:', agentData);
            const response = await createAgent(agentData);
            
            // Add to local data
            if (response.agent) {
                allAgents.push(response.agent);
            }
            
            showNotification('Agent added successfully!', 'success');
        }
        
        // Close modal and refresh
    closeAgentModal();
    initializeAgents();
    
    } catch (error) {
        console.error('Failed to save agent:', error);
        
        // Handle validation errors
        if (error.response && error.response.data) {
            if (error.response.data.errors) {
                const errors = error.response.data.errors;
                const errorMessages = Object.values(errors).flat().join(', ');
                showNotification(errorMessages, 'error');
            } else if (error.response.data.message) {
                showNotification(error.response.data.message, 'error');
            } else {
                showNotification(`Validation failed: ${error.response.status}`, 'error');
            }
        } else {
            showNotification('Failed to save agent. Please check your input and try again.', 'error');
        }
    }
};

// Debounce function for search
let searchTimeout;
const handleSearch = () => {
    clearTimeout(searchTimeout);
    
    searchTimeout = setTimeout(() => {
        const searchTerm = $('#policySearch').val().toLowerCase().trim();
        
        if (searchTerm === '') {
            filteredData = [...allPolicies];
        } else {
            filteredData = allPolicies.filter(policy => 
                policy.owner.toLowerCase().includes(searchTerm) ||
                policy.vehicle.toLowerCase().includes(searchTerm) ||
                policy.phone.toLowerCase().includes(searchTerm) ||
                policy.company.toLowerCase().includes(searchTerm) ||
                policy.id.toString().includes(searchTerm)
            );
        }
        
        currentPage = 1;
        renderTable();
        updatePagination();
    }, 300); // 300ms delay to prevent excessive filtering
};

// Rows per page change
const handleRowsPerPageChange = () => {
    rowsPerPage = parseInt($('#rowsPerPage').val());
    currentPage = 1;
    renderTable();
    updatePagination();
};

// Sorting functionality
const handleSort = (column) => {
    if (currentSort.column === column) {
        currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
    } else {
        currentSort.column = column;
        currentSort.direction = 'asc';
    }
    
    filteredData.sort((a, b) => {
        let aVal = a[column];
        let bVal = b[column];
        
        if (typeof aVal === 'string') {
            aVal = aVal.toLowerCase();
            bVal = bVal.toLowerCase();
        }
        
        if (aVal < bVal) return currentSort.direction === 'asc' ? -1 : 1;
        if (aVal > bVal) return currentSort.direction === 'asc' ? 1 : -1;
        return 0;
    });
    
    renderTable();
    updateSortIcons();
};

// Update sort icons
const updateSortIcons = () => {
    $('.data-table th[data-sort] i').removeClass('fa-sort-up fa-sort-down').addClass('fa-sort');
    
    const currentHeader = $(`.data-table th[data-sort="${currentSort.column}"]`);
    const icon = currentHeader.find('i');
    
    icon.removeClass('fa-sort');
    icon.addClass(currentSort.direction === 'asc' ? 'fa-sort-up' : 'fa-sort-down');
};

// Chart period change
const handleChartPeriodChange = () => {
    const period = $('#chartPeriod').val();
    // Here you would typically fetch new data based on the selected period
    // For now, we'll just show a notification
    showNotification(`Chart data updated for ${period}`, 'info');
};

// Policy actions
const editPolicy = (id) => {
    const policy = allPolicies.find(p => p.id === id);
    if (policy) {
        $('#policyModalTitle').text('Edit Policy');
        
        // Get the correct property names from the API response
        const policyType = policy.policyType || policy.type || 'Motor';
        const customerName = policy.customerName || policy.owner || '';
        const customerPhone = policy.phone || '';
        const customerEmail = policy.customerEmail || policy.email || '';
        const companyName = policy.companyName || policy.company || '';
        const insuranceType = policy.insuranceType || '';
        const businessType = policy.businessType || 'Self';
        const startDate = policy.startDate || '';
        const endDate = policy.endDate || '';
        const premium = policy.premium || '';
        const customerPaidAmount = policy.customerPaidAmount || policy.customerPaid || '';
        const revenue = policy.revenue || '';
        const payout = policy.payout || '';
        const vehicleNumber = policy.vehicleNumber || '';
        const vehicleType = policy.vehicleType || '';
        
        // Set global variables for the modal
        selectedPolicyType = policyType;
        selectedBusinessType = businessType;
        
        // Step 1: Set policy type
        $('#policyTypeSelect').val(policyType);
        
        // Step 2: Set business type
        $('#businessTypeSelect').val(businessType);
        
        // Show the modal and go directly to step 3 (form)
        $('#policyModal').addClass('show');
        $('#step1, #step2').hide();
        $('#step3').show();
        
        // Show the correct form and populate fields
        showPolicyForm(policyType);
        
        // Populate form fields based on policy type
        if (policyType === 'Motor') {
            // Populate motor-specific fields
            $('#vehicleNumber').val(vehicleNumber);
            $('#vehicleType').val(vehicleType);
            $('#customerName').val(customerName);
            $('#customerPhone').val(customerPhone);
            $('#customerEmail').val(customerEmail);
            $('#companyName').val(companyName);
            $('#insuranceType').val(insuranceType);
            $('#startDate').val(startDate);
            $('#endDate').val(endDate);
            $('#premium').val(premium);
            $('#payout').val(payout);
            $('#customerPaidAmount').val(customerPaidAmount);
            $('#revenue').val(revenue);
            
        } else if (policyType === 'Health') {
            // Populate health-specific fields
            $('#healthCustomerName').val(customerName);
            $('#healthCustomerPhone').val(customerPhone);
            $('#healthCustomerEmail').val(customerEmail);
            $('#healthCompanyName').val(companyName);
            $('#healthPlanType').val(insuranceType);
            $('#healthStartDate').val(startDate);
            $('#healthEndDate').val(endDate);
            $('#healthPremium').val(premium);
            $('#healthPayout').val(payout);
            $('#healthCustomerPaid').val(customerPaidAmount);
            $('#healthRevenue').val(revenue);
            
            // Additional health fields if available
            if (policy.customerAge) $('#healthCustomerAge').val(policy.customerAge);
            if (policy.customerGender) $('#healthCustomerGender').val(policy.customerGender);
            if (policy.sumInsured) $('#healthSumInsured').val(policy.sumInsured);
            
        } else if (policyType === 'Life') {
            // Populate life-specific fields
            $('#lifeCustomerName').val(customerName);
            $('#lifeCustomerPhone').val(customerPhone);
            $('#lifeCustomerEmail').val(customerEmail);
            $('#lifeCompanyName').val(companyName);
            $('#lifePlanType').val(insuranceType);
            $('#lifeStartDate').val(startDate);
            $('#lifeEndDate').val(endDate);
            $('#lifePremium').val(premium);
            $('#lifePayout').val(payout);
            $('#lifeCustomerPaid').val(customerPaidAmount);
            $('#lifeRevenue').val(revenue);
            
            // Additional life fields if available
            if (policy.customerAge) $('#lifeCustomerAge').val(policy.customerAge);
            if (policy.customerGender) $('#lifeCustomerGender').val(policy.customerGender);
            if (policy.sumAssured) $('#lifeSumAssured').val(policy.sumAssured);
            if (policy.policyTerm) $('#lifePolicyTerm').val(policy.policyTerm);
            if (policy.premiumFrequency) $('#lifePremiumFrequency').val(policy.premiumFrequency);
        }
        
        // Store the policy ID for form submission
        $('#policyForm').data('edit-id', id);
        
        // Update step indicators if they exist
        $('.step-indicator').removeClass('active');
        $('.step-indicator[data-step="3"]').addClass('active');
        
        // Add event listener for form submission if not already added
        if (!$('#policyForm').data('edit-listener-added')) {
            $('#savePolicyBtn').off('click').on('click', handlePolicySubmit);
            $('#policyForm').data('edit-listener-added', true);
        }
    } else {
        showNotification('Policy not found', 'error');
    }
};

const deletePolicyHandler = async (id) => {
    if (confirm('Are you sure you want to delete this policy?')) {
        try {
            await deletePolicy(id);
        allPolicies = allPolicies.filter(p => p.id !== id);
        filteredData = filteredData.filter(p => p.id !== id);
        policiesFilteredData = policiesFilteredData.filter(p => p.id !== id);
        
        renderTable();
        updatePagination();
        
        // Update policies page if it's currently active
        if ($('#policies').hasClass('active')) {
            renderPoliciesTable();
            updatePoliciesPagination();
            updatePoliciesStats();
        }
        
        showNotification('Policy deleted successfully!', 'success');
        } catch (error) {
            console.error('Failed to delete policy:', error);
            showNotification('Failed to delete policy', 'error');
        }
    }
};

// Utility functions
const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-IN');
};

const showNotification = (message, type = 'info') => {
    // Create notification element
    const notification = $(`
        <div class="notification notification-${type}">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
            <span>${message}</span>
        </div>
    `);
    
    // Add to body
    $('body').append(notification);
    
    // Show notification
    setTimeout(() => {
        notification.addClass('show');
    }, 100);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.removeClass('show');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
};

// Add notification styles
$('<style>')
    .prop('type', 'text/css')
    .html(`
        .notification {
            position: fixed;
            top: 90px;
            right: 24px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            z-index: 3000;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            max-width: 400px;
        }
        
        .dark-theme .notification {
            background: rgba(30, 41, 59, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .notification.show {
            transform: translateX(0);
        }
        
        .notification-success {
            border-left: 4px solid #10B981;
        }
        
        .notification-error {
            border-left: 4px solid #EF4444;
        }
        
        .notification-info {
            border-left: 4px solid #4F46E5;
        }
        
        .notification i {
            font-size: 18px;
        }
        
        .notification-success i {
            color: #10B981;
        }
        
        .notification-error i {
            color: #EF4444;
        }
        
        .notification-info i {
            color: #4F46E5;
        }
        
        .notification span {
            color: #111827;
            font-weight: 500;
        }
        
        .dark-theme .notification span {
            color: #F1F5F9;
        }
    `)
    .appendTo('head');

// Policies page functions
const renderPoliciesTable = () => {
    const startIndex = (policiesCurrentPage - 1) * policiesRowsPerPage;
    const endIndex = startIndex + policiesRowsPerPage;
    const pageData = policiesFilteredData.slice(startIndex, endIndex);
    
    const tbody = $('#policiesPageTableBody');
    
    // Use document fragment for better performance
    const fragment = document.createDocumentFragment();
    
    pageData.forEach((policy, index) => {
        const row = document.createElement('tr');
        
        // Use the correct property names from the API response with fallbacks
        const policyType = policy.policyType || policy.type || 'Unknown';
        const customerName = policy.customerName || policy.owner || 'Unknown';
        const phone = policy.phone || 'Unknown';
        const companyName = policy.companyName || policy.company || 'Unknown';
        const endDate = policy.endDate || 'Unknown';
        const premium = policy.premium || 0;
        const status = policy.status || 'Active';
        
        // Format additional info based on policy type
        let additionalInfo = '';
        if (policyType === 'Motor' && (policy.vehicleNumber || policy.vehicle)) {
            additionalInfo = policy.vehicleNumber || policy.vehicle || '';
        } else if (policyType === 'Health' && policy.sumInsured) {
            additionalInfo = `Sum: ₹${policy.sumInsured.toLocaleString()}`;
        } else if (policyType === 'Life' && policy.sumAssured) {
            additionalInfo = `Sum: ₹${policy.sumAssured.toLocaleString()}`;
        }
        
        row.innerHTML = `
            <td>${startIndex + index + 1}</td>
            <td><span class="policy-type-badge ${policyType.toLowerCase()}">${policyType}</span></td>
            <td>${customerName}</td>
            <td>${phone}</td>
            <td>${getShortCompanyName(companyName)}</td>
            <td>${formatDate(endDate)}</td>
            <td>₹${premium.toLocaleString()}</td>
            <td><span class="status-badge ${status.toLowerCase()}">${status}</span></td>
            <td>
                <div class="action-buttons">
                    <button class="action-btn edit" data-policy-id="${policy.id}" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="action-btn delete" data-policy-id="${policy.id}" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                    <button class="action-btn view" data-policy-id="${policy.id}" title="View Details">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </td>
        `;
        fragment.appendChild(row);
    });
    
    tbody.empty().append(fragment);
    updatePoliciesPaginationInfo();
    
    // Add event listeners for action buttons
    tbody.find('.action-btn.edit').off('click').on('click', function() {
        const policyId = parseInt($(this).data('policy-id'));
        editPolicy(policyId);
    });
    
    tbody.find('.action-btn.delete').off('click').on('click', function() {
        const policyId = parseInt($(this).data('policy-id'));
        deletePolicyHandler(policyId);
    });
    
    tbody.find('.action-btn.view').off('click').on('click', function() {
        const policyId = parseInt($(this).data('policy-id'));
        if (!isNaN(policyId)) {
            viewPolicyDetails(policyId);
        }
    });
};

const updatePoliciesPagination = () => {
    const totalPages = Math.ceil(policiesFilteredData.length / policiesRowsPerPage);
    const pageNumbers = $('#policiesPageNumbers');
    pageNumbers.empty();
    
    // Previous button
    $('#policiesPrevPage').prop('disabled', policiesCurrentPage === 1);
    
    // Page numbers
    const maxVisiblePages = 5;
    let startPage = Math.max(1, policiesCurrentPage - Math.floor(maxVisiblePages / 2));
    let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
    
    if (endPage - startPage + 1 < maxVisiblePages) {
        startPage = Math.max(1, endPage - maxVisiblePages + 1);
    }
    
    for (let i = startPage; i <= endPage; i++) {
        const pageBtn = $(`<button class="page-number ${i === policiesCurrentPage ? 'active' : ''}">${i}</button>`);
        pageBtn.click(() => goToPoliciesPage(i));
        pageNumbers.append(pageBtn);
    }
    
    // Next button
    $('#policiesNextPage').prop('disabled', policiesCurrentPage === totalPages);
};

const updatePoliciesPaginationInfo = () => {
    const startRecord = (policiesCurrentPage - 1) * policiesRowsPerPage + 1;
    const endRecord = Math.min(policiesCurrentPage * policiesRowsPerPage, policiesFilteredData.length);
    
    $('#policiesStartRecord').text(startRecord);
    $('#policiesEndRecord').text(endRecord);
    $('#policiesTotalRecords').text(policiesFilteredData.length);
};

const goToPoliciesPage = (page) => {
    policiesCurrentPage = page;
    renderPoliciesTable();
    updatePoliciesPagination();
};

const updatePoliciesStats = () => {
    const activeCount = allPolicies.filter(p => p.status === 'Active').length;
    const expiredCount = allPolicies.filter(p => p.status === 'Expired').length;
    const pendingCount = allPolicies.filter(p => p.status === 'Pending').length;
    const totalCount = allPolicies.length;
    
    $('#activePoliciesCount').text(activeCount);
    $('#expiredPoliciesCount').text(expiredCount);
    $('#pendingRenewalsCount').text(pendingCount);
    $('#totalPoliciesCount').text(totalCount);
};

const handlePoliciesSearch = () => {
    clearTimeout(searchTimeout);
    
    searchTimeout = setTimeout(() => {
        applyPoliciesFilters();
    }, 300);
};

const handlePoliciesFilter = () => {
    applyPoliciesFilters();
};

const applyPoliciesFilters = () => {
    const searchTerm = $('#policiesSearch').val().toLowerCase().trim();
    const policyTypeFilter = $('#policyTypeFilter').val();
    const statusFilter = $('#statusFilter').val();
    
    policiesFilteredData = allPolicies.filter(policy => {
        const matchesSearch = searchTerm === '' || 
            policy.owner.toLowerCase().includes(searchTerm) ||
            policy.phone.toLowerCase().includes(searchTerm) ||
            policy.company.toLowerCase().includes(searchTerm) ||
            policy.type.toLowerCase().includes(searchTerm) ||
            policy.id.toString().includes(searchTerm);
        
        const matchesType = policyTypeFilter === '' || policy.type === policyTypeFilter;
        const matchesStatus = statusFilter === '' || policy.status === statusFilter;
        
        return matchesSearch && matchesType && matchesStatus;
    });
    
    policiesCurrentPage = 1;
    renderPoliciesTable();
    updatePoliciesPagination();
};

const handlePoliciesRowsPerPageChange = () => {
    policiesRowsPerPage = parseInt($('#policiesRowsPerPage').val());
    policiesCurrentPage = 1;
    renderPoliciesTable();
    updatePoliciesPagination();
};

const handlePoliciesSort = (column) => {
    if (policiesCurrentSort.column === column) {
        policiesCurrentSort.direction = policiesCurrentSort.direction === 'asc' ? 'desc' : 'asc';
    } else {
        policiesCurrentSort.column = column;
        policiesCurrentSort.direction = 'asc';
    }
    
    policiesFilteredData.sort((a, b) => {
        let aVal = a[column];
        let bVal = b[column];
        
        if (typeof aVal === 'string') {
            aVal = aVal.toLowerCase();
            bVal = bVal.toLowerCase();
        }
        
        if (aVal < bVal) return policiesCurrentSort.direction === 'asc' ? -1 : 1;
        if (aVal > bVal) return policiesCurrentSort.direction === 'asc' ? 1 : -1;
        return 0;
    });
    
    renderPoliciesTable();
    updatePoliciesSortIcons();
};

const updatePoliciesSortIcons = () => {
    $('#policiesPageTable th[data-sort] i').removeClass('fa-sort-up fa-sort-down').addClass('fa-sort');
    
    const currentHeader = $(`#policiesPageTable th[data-sort="${policiesCurrentSort.column}"]`);
    const icon = currentHeader.find('i');
    
    icon.removeClass('fa-sort');
    icon.addClass(policiesCurrentSort.direction === 'asc' ? 'fa-sort-up' : 'fa-sort-down');
};

const viewPolicyDetails = (id) => {
    let policy = allPolicies.find(p => p.id === id);
    
    if (!policy) {
        // If policy not found in allPolicies, fetch it from API
        apiCall(`/policies/${id}`)
            .then(data => {
                if (data && data.policy) {
                    policy = data.policy;
                    populatePolicyModal(policy);
                } else {
                    showNotification('Policy not found', 'error');
                }
            })
            .catch(error => {
                console.error('Failed to fetch policy details:', error);
                showNotification('Failed to load policy details', 'error');
            });
        return;
    }
    
    populatePolicyModal(policy);
};

const populatePolicyModal = (policy) => {
    // Populate modal with policy details
    $('#viewPolicyNumber').text(policy.policyNumber || `#${policy.id.toString().padStart(3, '0')}`);
    $('#viewPolicyType').text(policy.type || policy.policyType).removeClass().addClass(`policy-type-badge ${(policy.type || policy.policyType).toLowerCase()}`);
    $('#viewPolicyStatus').text(policy.status).removeClass().addClass(`status-badge ${policy.status.toLowerCase()}`);
    
    // Customer Information
    $('#viewCustomerName').text(policy.owner || policy.customerName);
    $('#viewCustomerPhone').text(policy.phone);
    $('#viewCustomerEmail').text(policy.email || 'Not provided');
    
    // Show/hide age and gender for Health/Life policies
    if ((policy.type || policy.policyType) === 'Health' || (policy.type || policy.policyType) === 'Life') {
        $('#viewCustomerAgeContainer').show();
        $('#viewCustomerGenderContainer').show();
        $('#viewCustomerAge').text(policy.customerAge || 'Not provided');
        $('#viewCustomerGender').text(policy.customerGender || 'Not provided');
    } else {
        $('#viewCustomerAgeContainer').hide();
        $('#viewCustomerGenderContainer').hide();
    }
    
    // Vehicle Information (Motor only)
    if ((policy.type || policy.policyType) === 'Motor') {
        $('#viewVehicleSection').show();
        const vehicleNumber = policy.vehicle ? policy.vehicle.split(' - ')[1] || policy.vehicle : (policy.vehicleNumber || 'N/A');
        const vehicleType = policy.vehicle ? policy.vehicle.split(' - ')[0] || 'N/A' : 'N/A';
        $('#viewVehicleNumber').text(vehicleNumber);
        $('#viewVehicleType').text(vehicleType);
    } else {
        $('#viewVehicleSection').hide();
    }
    
    // Insurance Information
    $('#viewCompanyName').text(policy.company || policy.companyName);
    
    // Show/hide insurance type vs plan type
    if ((policy.type || policy.policyType) === 'Motor') {
        $('#viewInsuranceTypeContainer').show();
        $('#viewPlanTypeContainer').hide();
        $('#viewInsuranceType').text(policy.insuranceType || 'Not provided');
    } else {
        $('#viewInsuranceTypeContainer').hide();
        $('#viewPlanTypeContainer').show();
        $('#viewPlanType').text(policy.planType || 'Not provided');
    }
    
    // Show/hide sum insured/assured
    if ((policy.type || policy.policyType) === 'Health') {
        $('#viewSumInsuredContainer').show();
        $('#viewSumAssuredContainer').hide();
        $('#viewPolicyTermContainer').hide();
        $('#viewPremiumFrequencyContainer').hide();
        $('#viewSumInsured').text(policy.sumInsured ? `₹${policy.sumInsured.toLocaleString()}` : 'Not provided');
    } else if ((policy.type || policy.policyType) === 'Life') {
        $('#viewSumInsuredContainer').hide();
        $('#viewSumAssuredContainer').show();
        $('#viewPolicyTermContainer').show();
        $('#viewPremiumFrequencyContainer').show();
        $('#viewSumAssured').text(policy.sumAssured ? `₹${policy.sumAssured.toLocaleString()}` : 'Not provided');
        $('#viewPolicyTerm').text(policy.policyTerm ? `${policy.policyTerm} years` : 'Not provided');
        $('#viewPremiumFrequency').text(policy.premiumFrequency || 'Not provided');
    } else {
        $('#viewSumInsuredContainer').hide();
        $('#viewSumAssuredContainer').hide();
        $('#viewPolicyTermContainer').hide();
        $('#viewPremiumFrequencyContainer').hide();
    }
    
    // Dates
    $('#viewStartDate').text(formatDate(policy.startDate));
    $('#viewEndDate').text(formatDate(policy.endDate));
    
    // Financial Information
    $('#viewPremium').text(`₹${policy.premium.toLocaleString()}`);
    $('#viewCustomerPaid').text(policy.customerPaidAmount ? `₹${policy.customerPaidAmount.toLocaleString()}` : 'Not provided');
    $('#viewRevenue').text(`₹${policy.revenue.toLocaleString()}`);
    $('#viewPayout').text(policy.payout ? `₹${policy.payout.toLocaleString()}` : 'Not provided');
    $('#viewBusinessType').text(policy.businessType || 'Not provided');
    $('#viewAgentName').text(policy.agentName || 'Not provided');
    
    // Show/hide medical reports for Health policies
    if ((policy.type || policy.policyType) === 'Health') {
        $('#medicalReportsItem').show();
    } else {
        $('#medicalReportsItem').hide();
    }
    
    // Show/hide RC copy for Motor policies
    if ((policy.type || policy.policyType) === 'Motor') {
        $('#rcCopyItem').show();
    } else {
        $('#rcCopyItem').hide();
    }
    
    // Handle document download buttons
    setupDocumentDownloadButtons(policy);
    
    // Open the modal
    $('#viewPolicyModal').addClass('show');
};

const exportPoliciesData = () => {
    const csvContent = generatePoliciesCSV();
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', `policies_export_${new Date().toISOString().split('T')[0]}.csv`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    showNotification('Policies data exported successfully!', 'success');
};

const generatePoliciesCSV = () => {
    const headers = ['Sl. No', 'Policy Type', 'Customer Name', 'Phone', 'Insurance Company', 'End Date', 'Premium', 'Status'];
    const csvRows = [headers.join(',')];
    
    policiesFilteredData.forEach(policy => {
        const row = [
            policy.id,
            policy.type,
            policy.owner,
            policy.phone,
            getShortCompanyName(policy.company),
            policy.endDate,
            policy.premium,
            policy.status
        ];
        csvRows.push(row.join(','));
    });
    
    return csvRows.join('\n');
};

// Follow-ups Functions
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

const handleFollowupSubmit = async (e) => {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    
    // Build followup data to match controller expectations
    const followupData = {
        customerName: formData.get('customerName'),
        phone: formData.get('phone'),
        email: formData.get('email'),
        policyId: formData.get('policyId') ? parseInt(formData.get('policyId')) : null,
        followupType: formData.get('followupType'),
        status: formData.get('status'),
        assignedTo: formData.get('assignedTo'),
        priority: formData.get('priority'),
        nextFollowupDate: formData.get('nextFollowupDate'),
        reminderTime: formData.get('reminderTime'),
        notes: formData.get('notes')
    };
    
    // Validate required fields
    if (!followupData.customerName || !followupData.phone) {
        showNotification('Please fill in all required fields', 'error');
        return;
    }
    
    try {
        const editId = $('#followupForm').data('edit-id');
        
        console.log('Submitting followup data:', followupData);
        
        if (editId) {
            // Update existing followup
            const response = await updateFollowup(editId, followupData);
            
            // Update local data
            const index = allFollowups.findIndex(f => f.id === editId);
            if (index !== -1) {
                allFollowups[index] = { ...allFollowups[index], ...response.followup };
            }
            
            showNotification('Follow-up updated successfully!', 'success');
        } else {
            // Create new followup
            const response = await createFollowup(followupData);
            
            // Add to local data
            if (response.followup) {
                allFollowups.push(response.followup);
            }
            
            showNotification('Follow-up added successfully!', 'success');
        }
        
        // Close modal and refresh
    closeFollowupModal();
    renderFollowupsTable();
    updateFollowupsPagination();
    updateFollowupsStats();
    
    } catch (error) {
        console.error('Failed to save followup:', error);
        
        // Handle validation errors
        if (error.response && error.response.data) {
            if (error.response.data.errors) {
                const errors = error.response.data.errors;
                const errorMessages = Object.values(errors).flat().join(', ');
                showNotification(errorMessages, 'error');
            } else if (error.response.data.message) {
                showNotification(error.response.data.message, 'error');
            } else {
                showNotification(`Validation failed: ${error.response.status}`, 'error');
            }
        } else {
            showNotification('Failed to save followup. Please check your input and try again.', 'error');
        }
    }
};

const renderFollowupsTable = () => {
    const startIndex = (followupsCurrentPage - 1) * followupsRowsPerPage;
    const endIndex = startIndex + followupsRowsPerPage;
    const pageData = followupsFilteredData.slice(startIndex, endIndex);
    
    const tbody = $('#followupsTableBody');
    
    // Use document fragment for better performance
    const fragment = document.createDocumentFragment();
    
    pageData.forEach(followup => {
        const row = document.createElement('tr');
        
        // Get follow-up type class
        const typeClass = followup.followupType.toLowerCase().replace(' ', '-');
        
        row.innerHTML = `
            <td>${followup.id}</td>
            <td>${followup.customerName}</td>
            <td>${followup.phone}</td>
            <td><span class="followup-type-badge ${typeClass}">${followup.followupType}</span></td>
            <td><span class="status-badge ${followup.status.toLowerCase().replace(' ', '')}">${followup.status}</span></td>
            <td>${followup.assignedTo}</td>
            <td>${formatDate(followup.lastFollowupDate)}</td>
            <td>${followup.nextFollowupDate ? formatDate(followup.nextFollowupDate) : '-'}</td>
            <td><div class="recent-note" title="${followup.recentNote}">${followup.recentNote}</div></td>
            <td>
                <div class="action-buttons">
                    <button class="action-btn edit" data-followup-id="${followup.id}" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="action-btn delete" data-followup-id="${followup.id}" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                    <button class="action-btn view" data-followup-id="${followup.id}" title="View Details">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="action-btn add-note" data-followup-id="${followup.id}" title="Add Note">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </td>
        `;
        fragment.appendChild(row);
    });
    
    tbody.empty().append(fragment);
    updateFollowupsPaginationInfo();
    
    // Add event listeners for action buttons
    tbody.find('.action-btn.edit').click(function() {
        const followupId = parseInt($(this).data('followup-id'));
        editFollowup(followupId);
    });
    
    tbody.find('.action-btn.delete').click(function() {
        const followupId = parseInt($(this).data('followup-id'));
        deleteFollowupHandler(followupId);
    });
    
    tbody.find('.action-btn.view').click(function() {
        const followupId = parseInt($(this).data('followup-id'));
        viewFollowupDetails(followupId);
    });
    
    tbody.find('.action-btn.add-note').click(function() {
        const followupId = parseInt($(this).data('followup-id'));
        addNoteToFollowup(followupId);
    });
};

const updateFollowupsPagination = () => {
    const totalPages = Math.ceil(followupsFilteredData.length / followupsRowsPerPage);
    const pageNumbers = $('#followupsPageNumbers');
    pageNumbers.empty();
    
    // Previous button
    $('#followupsPrevPage').prop('disabled', followupsCurrentPage === 1);
    
    // Page numbers
    const maxVisiblePages = 5;
    let startPage = Math.max(1, followupsCurrentPage - Math.floor(maxVisiblePages / 2));
    let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
    
    if (endPage - startPage + 1 < maxVisiblePages) {
        startPage = Math.max(1, endPage - maxVisiblePages + 1);
    }
    
    for (let i = startPage; i <= endPage; i++) {
        const pageBtn = $(`<button class="page-number ${i === followupsCurrentPage ? 'active' : ''}">${i}</button>`);
        pageBtn.click(() => goToFollowupsPage(i));
        pageNumbers.append(pageBtn);
    }
    
    // Next button
    $('#followupsNextPage').prop('disabled', followupsCurrentPage === totalPages);
};

const updateFollowupsPaginationInfo = () => {
    const startRecord = (followupsCurrentPage - 1) * followupsRowsPerPage + 1;
    const endRecord = Math.min(followupsCurrentPage * followupsRowsPerPage, followupsFilteredData.length);
    
    $('#followupsStartRecord').text(startRecord);
    $('#followupsEndRecord').text(endRecord);
    $('#followupsTotalRecords').text(followupsFilteredData.length);
};

const goToFollowupsPage = (page) => {
    followupsCurrentPage = page;
    renderFollowupsTable();
    updateFollowupsPagination();
};

const updateFollowupsStats = () => {
    const pending = followupsFilteredData.filter(f => f.status === 'Pending').length;
    const inProgress = followupsFilteredData.filter(f => f.status === 'In Progress').length;
    const completed = followupsFilteredData.filter(f => f.status === 'Completed').length;
    const total = followupsFilteredData.length;
    
    // Count completed today
    const today = new Date().toISOString().split('T')[0];
    const completedToday = followupsFilteredData.filter(f => 
        f.status === 'Completed' && f.lastFollowupDate === today
    ).length;
    
    $('#pendingFollowupsCount').text(pending);
    $('#inProgressFollowupsCount').text(inProgress);
    $('#completedTodayCount').text(completedToday);
    $('#totalFollowupsCount').text(total);
};

const handleFollowupsSearch = () => {
    const searchTerm = $('#followupsSearch').val().toLowerCase();
    
    followupsFilteredData = allFollowups.filter(followup => 
        followup.customerName.toLowerCase().includes(searchTerm) ||
        followup.phone.includes(searchTerm) ||
        followup.followupType.toLowerCase().includes(searchTerm) ||
        followup.assignedTo.toLowerCase().includes(searchTerm)
    );
    
    followupsCurrentPage = 1;
    renderFollowupsTable();
    updateFollowupsPagination();
    updateFollowupsStats();
};

const handleFollowupsFilter = () => {
    applyFollowupsFilters();
};

const applyFollowupsFilters = () => {
    const statusFilter = $('#followupStatusFilter').val();
    const typeFilter = $('#followupTypeFilter').val();
    
    followupsFilteredData = allFollowups.filter(followup => {
        const statusMatch = !statusFilter || followup.status === statusFilter;
        const typeMatch = !typeFilter || followup.followupType === typeFilter;
        return statusMatch && typeMatch;
    });
    
    followupsCurrentPage = 1;
    renderFollowupsTable();
    updateFollowupsPagination();
    updateFollowupsStats();
};

const handleFollowupsRowsPerPageChange = () => {
    followupsRowsPerPage = parseInt($('#followupsRowsPerPage').val());
    followupsCurrentPage = 1;
    renderFollowupsTable();
    updateFollowupsPagination();
};

const handleFollowupsSort = (column) => {
    if (followupsCurrentSort.column === column) {
        followupsCurrentSort.direction = followupsCurrentSort.direction === 'asc' ? 'desc' : 'asc';
    } else {
        followupsCurrentSort.column = column;
        followupsCurrentSort.direction = 'asc';
    }
    
    followupsFilteredData.sort((a, b) => {
        let aVal = a[column];
        let bVal = b[column];
        
        if (typeof aVal === 'string') {
            aVal = aVal.toLowerCase();
            bVal = bVal.toLowerCase();
        }
        
        if (aVal < bVal) return followupsCurrentSort.direction === 'asc' ? -1 : 1;
        if (aVal > bVal) return followupsCurrentSort.direction === 'asc' ? 1 : -1;
        return 0;
    });
    
    renderFollowupsTable();
    updateFollowupsSortIcons();
};

const updateFollowupsSortIcons = () => {
    $('#followupsTable th[data-sort] i').removeClass('fa-sort-up fa-sort-down').addClass('fa-sort');
    
    const currentHeader = $(`#followupsTable th[data-sort="${followupsCurrentSort.column}"]`);
    const icon = currentHeader.find('i');
    
    icon.removeClass('fa-sort');
    icon.addClass(followupsCurrentSort.direction === 'asc' ? 'fa-sort-up' : 'fa-sort-down');
};

const editFollowup = (id) => {
    const followup = allFollowups.find(f => f.id === id);
    if (followup) {
        $('#followupModalTitle').text('Edit Follow Up');
        
        // Populate form fields
        $('#followupCustomerName').val(followup.customerName || '');
        $('#followupPhone').val(followup.phone || '');
        $('#followupEmail').val(followup.email || '');
        $('#followupPolicyId').val(followup.policyId || '');
        $('#followupType').val(followup.followupType || '');
        $('#followupStatus').val(followup.status || '');
        $('#followupAssignedTo').val(followup.assignedTo || '');
        $('#followupPriority').val(followup.priority || '');
        $('#followupNextDate').val(followup.nextFollowupDate || '');
        $('#followupReminderTime').val(followup.reminderTime || '');
        
        // Store the followup ID for form submission
        $('#followupForm').data('edit-id', id);
        
        // Show the modal
        $('#followupModal').addClass('show');
        
        // Add event listener for form submission if not already added
        if (!$('#followupForm').data('edit-listener-added')) {
            $('#saveFollowupBtn').off('click').on('click', handleFollowupSubmit);
            $('#followupForm').data('edit-listener-added', true);
        }
        
        // Show previous notes if available
        if (followup.notesHistory && followup.notesHistory.length > 0) {
            showPreviousNotes(followup.notesHistory);
        }
    } else {
        showNotification('Follow-up not found', 'error');
    }
};

const deleteFollowupHandler = async (id) => {
    if (confirm('Are you sure you want to delete this follow-up?')) {
        try {
            await deleteFollowup(id);
        allFollowups = allFollowups.filter(f => f.id !== id);
        followupsFilteredData = followupsFilteredData.filter(f => f.id !== id);
        
        renderFollowupsTable();
        updateFollowupsPagination();
        updateFollowupsStats();
        
        showNotification('Follow-up deleted successfully!', 'success');
        } catch (error) {
            console.error('Failed to delete followup:', error);
            showNotification('Failed to delete followup', 'error');
        }
    }
};

const viewFollowupDetails = (id) => {
    const followup = allFollowups.find(f => f.id === id);
    if (followup) {
        showNotification(`Viewing details for follow-up #${followup.id}`, 'info');
        // Here you would typically open a detailed view modal
    }
};

const addNoteToFollowup = (id) => {
    const followup = allFollowups.find(f => f.id === id);
    if (followup) {
        $('#followupModalTitle').text('Add Note to Follow Up');
        
        // Pre-populate customer info
        $('#followupCustomerName').val(followup.customerName);
        $('#followupPhone').val(followup.phone);
        $('#followupEmail').val(followup.email);
        $('#followupPolicyId').val(followup.policyId);
        $('#followupType').val(followup.followupType);
        $('#followupStatus').val(followup.status);
        $('#followupAssignedTo').val(followup.assignedTo);
        $('#followupPriority').val(followup.priority);
        $('#followupNextDate').val(followup.nextFollowupDate);
        $('#followupReminderTime').val(followup.reminderTime);
        
        // Clear note field for new note
        $('#followupNote').val('');
        
        // Show previous notes
        showPreviousNotes(followup.notesHistory);
        
        $('#followupModal').addClass('show');
    }
};

const showPreviousNotes = (notesHistory) => {
    const container = $('#previousNotesContainer');
    container.empty();
    
    if (notesHistory && notesHistory.length > 0) {
        notesHistory.forEach(note => {
            const noteHtml = `
                <div class="note-item">
                    <div class="note-header">
                        <span class="note-date">${formatDate(note.date)} at ${note.time}</span>
                        <span class="note-telecaller">${note.telecaller}</span>
                    </div>
                    <div class="note-content">${note.note}</div>
                    <div class="note-meta">
                        <span><i class="fas fa-clock"></i> ${note.callDuration} min</span>
                        <span><i class="fas fa-phone"></i> ${note.callResult}</span>
                    </div>
                </div>
            `;
            container.append(noteHtml);
        });
        
        $('#previousNotesSection').show();
    } else {
        $('#previousNotesSection').hide();
    }
};

const exportFollowupsData = () => {
    const csvContent = generateFollowupsCSV();
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', `followups_export_${new Date().toISOString().split('T')[0]}.csv`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    showNotification('Follow-ups data exported successfully!', 'success');
};

const generateFollowupsCSV = () => {
    const headers = ['Sl. No', 'Customer Name', 'Phone Number', 'Follow-up Type', 'Status', 'Assigned To', 'Last Follow-up Date', 'Next Follow-up Date', 'Recent Note'];
    const csvRows = [headers.join(',')];
    
    followupsFilteredData.forEach(followup => {
        const row = [
            followup.id,
            followup.customerName,
            followup.phone,
            followup.followupType,
            followup.status,
            followup.assignedTo,
            followup.lastFollowupDate,
            followup.nextFollowupDate || '',
            followup.recentNote
        ];
        csvRows.push(row.join(','));
    });
    
    return csvRows.join('\n');
}; 

// Multi-step modal functions
const initializeMultiStepModal = () => {
    // Update agent names in business type dropdown
    updateAgentDropdown();
    
    // Policy type selection
    $('#policyTypeSelect').change(function() {
        selectedPolicyType = $(this).val();
        $('#nextStep1').prop('disabled', !selectedPolicyType);
    });
    
    // Business type selection
    $('#businessTypeSelect').change(function() {
        selectedBusinessType = $(this).val();
        $('#nextStep2').prop('disabled', !selectedBusinessType);
    });
    
    // Step navigation
    $('#nextStep1').click(() => goToStep(2));
    $('#nextStep2').click(() => goToStep(3));
    $('#prevStep2').click(() => goToStep(1));
    $('#prevStep3').click(() => goToStep(2));
    
    // Cancel button
    $('#cancelPolicy').click(closePolicyModal);
};

const updateAgentDropdown = () => {
    const businessTypeSelect = $('#businessTypeSelect');
    
    // Clear existing agent options
    businessTypeSelect.find('option[value^="Agent"]').remove();
    
    // Add agent options
    allAgents.forEach((agent, index) => {
        businessTypeSelect.append(`<option value="Agent${index + 1}">${agent.name}</option>`);
    });
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
    
    // Disable validation on ALL hidden fields first (including those in hidden forms)
    $('.policy-form input[required], .policy-form select[required]').prop('required', false);
    
    // Show selected form
    $(`#${policyType.toLowerCase()}Form`).show();
    
    // Enable validation only on visible fields in the current form
    $(`#${policyType.toLowerCase()}Form input[required], #${policyType.toLowerCase()}Form select[required]`).prop('required', true);
    
    // Set default dates for the visible form
    setDefaultDates(policyType);

    // Setup auto calculation for revenue based on inputs
    setupRevenueAutoCalcForPolicyType(policyType);
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

// Auto-calculate Revenue field for the active policy form
const setupRevenueAutoCalcForPolicyType = (policyType) => {
    let $premiumInput;
    let $payoutInput;
    let $customerPaidInput;
    let $revenueInput;

    if (policyType === 'Motor') {
        $premiumInput = $('#premium');
        $payoutInput = $('#payout');
        $customerPaidInput = $('#customerPaidAmount');
        $revenueInput = $('#revenue');
    } else if (policyType === 'Health') {
        $premiumInput = $('#healthPremium');
        $payoutInput = $('#healthPayout');
        $customerPaidInput = $('#healthCustomerPaid');
        $revenueInput = $('#healthRevenue');
    } else if (policyType === 'Life') {
        $premiumInput = $('#lifePremium');
        $payoutInput = $('#lifePayout');
        $customerPaidInput = $('#lifeCustomerPaid');
        $revenueInput = $('#lifeRevenue');
    } else {
        return;
    }

    // Make revenue read-only to reflect auto-calc nature
    if ($revenueInput && $revenueInput.length) {
        $revenueInput.prop('readonly', true);
    }

    const recalcRevenue = () => {
        const premium = parseFloat($premiumInput.val()) || 0;
        const payout = parseFloat($payoutInput.val()) || 0;
        const customerPaid = parseFloat($customerPaidInput.val()) || 0;

        let revenue = customerPaid - (premium - payout);
        if (!isFinite(revenue)) revenue = 0;
        // Prevent negative revenue to satisfy backend min:0 validation
        if (revenue < 0) revenue = 0;

        $revenueInput.val(revenue.toFixed(2));
    };

    // Remove existing namespaced listeners to avoid duplicates, then attach
    $premiumInput.off('input.revenueCalc change.revenueCalc').on('input.revenueCalc change.revenueCalc', recalcRevenue);
    $payoutInput.off('input.revenueCalc change.revenueCalc').on('input.revenueCalc change.revenueCalc', recalcRevenue);
    $customerPaidInput.off('input.revenueCalc change.revenueCalc').on('input.revenueCalc change.revenueCalc', recalcRevenue);

    // Initial calculation to populate field
    recalcRevenue();
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

// Renewals Functions
const openRenewalModal = () => {
    $('#renewalModalTitle').text('Add Renewal Reminder');
    $('#renewalForm')[0].reset();
    
    // Populate policy dropdown with correct property names
    const policySelect = $('#renewalPolicyId');
    policySelect.empty().append('<option value="">Select Policy</option>');
    
    allPolicies.forEach(policy => {
        const policyId = policy.id || 0;
        const customerName = policy.customerName || policy.owner || 'Unknown';
        const policyType = policy.policyType || policy.type || 'Unknown';
        policySelect.append(`<option value="${policyId}">#${policyId.toString().padStart(3, '0')} - ${customerName} (${policyType})</option>`);
    });
    
    // Set default reminder date (30 days from today)
    const today = new Date();
    const defaultReminder = new Date(today);
    defaultReminder.setDate(today.getDate() + 30);
    $('#renewalReminderDate').val(defaultReminder.toISOString().split('T')[0]);
    
    // Set default priority
    $('#renewalPriority').val('Medium');
    
    $('#renewalModal').addClass('show');
};

const closeRenewalModal = () => {
    $('#renewalModal').removeClass('show');
    
    // Reset form
    $('#renewalForm')[0].reset();
    
    // Reset modal state
    $('#renewalForm').removeData('edit-id');
    $('#renewalModalTitle').text('Add Renewal Reminder');
    
    // Clear any edit listener
    $('#renewalForm').removeData('edit-listener-added');
};

const handleRenewalSubmit = async (e) => {
    e.preventDefault();
    
    // Build renewal data using the correct field names from the modal
    const renewalData = {
        policy_id: parseInt($('#renewalPolicyId').val()) || null,
        customer_name: $('#renewalCustomerName').val() || '',
        reminder_date: $('#renewalReminderDate').val() || '',
        priority: $('#renewalPriority').val() || 'Medium',
        notes: $('#renewalNotes').val() || ''
    };
    
    // Validate required fields
    if (!renewalData.policy_id || !renewalData.customer_name || !renewalData.reminder_date) {
        showNotification('Please fill in all required fields (Policy ID, Customer Name, Reminder Date)', 'error');
        return;
    }
    
    try {
        const editId = $('#renewalForm').data('edit-id');
        
        console.log('Submitting renewal data:', renewalData);
        
        if (editId) {
            // Update existing renewal
            const response = await updateRenewal(editId, renewalData);
            
            // Update local data
            const index = allRenewals.findIndex(r => r.id === editId);
            if (index !== -1) {
                allRenewals[index] = { ...allRenewals[index], ...response.renewal };
            }
            
            showNotification('Renewal updated successfully!', 'success');
        } else {
            // Create new renewal
            const response = await createRenewal(renewalData);
            
            // Add to local data
            if (response.renewal) {
                allRenewals.push(response.renewal);
            }
            
            showNotification('Renewal reminder added successfully!', 'success');
        }
        
        // Close modal and refresh
        closeRenewalModal();
        renderRenewalsTable();
        updateRenewalsPagination();
        updateRenewalsStats();
        
    } catch (error) {
        console.error('Failed to save renewal:', error);
        
        // Handle validation errors
        if (error.response && error.response.data) {
            if (error.response.data.errors) {
                const errors = error.response.data.errors;
                const errorMessages = Object.values(errors).flat().join(', ');
                showNotification(errorMessages, 'error');
            } else if (error.response.data.message) {
                showNotification(error.response.data.message, 'error');
            } else {
                showNotification(`Validation failed: ${error.response.status}`, 'error');
            }
        } else {
            showNotification('Failed to save renewal. Please check your input and try again.', 'error');
        }
    }
};

const renderRenewalsTable = () => {
    const startIndex = (renewalsCurrentPage - 1) * renewalsRowsPerPage;
    const endIndex = startIndex + renewalsRowsPerPage;
    const pageData = renewalsFilteredData.slice(startIndex, endIndex);
    
    const tbody = $('#renewalsTableBody');
    
    // Use document fragment for better performance
    const fragment = document.createDocumentFragment();
    
    pageData.forEach(renewal => {
        const row = document.createElement('tr');
        
        // Get the correct property names from the API response with fallbacks
        const policyId = renewal.policyId || renewal.policy_id || 0;
        const customerName = renewal.customerName || renewal.customer_name || 'Unknown';
        const policyType = renewal.policyType || renewal.policy_type || 'Unknown';
        const expiryDate = renewal.expiryDate || renewal.expiry_date || 'Unknown';
        const daysLeft = renewal.daysLeft || renewal.days_left || 0;
        const status = renewal.status || 'Pending';
        const priority = renewal.priority || 'Medium';
        const assignedTo = renewal.assignedTo || renewal.assigned_to || 'Unassigned';
        
        // Determine days left class
        let daysLeftClass = 'safe';
        if (daysLeft < 0) {
            daysLeftClass = 'urgent';
        } else if (daysLeft <= 7) {
            daysLeftClass = 'warning';
        }
        
        row.innerHTML = `
            <td>${renewal.id}</td>
            <td>#${policyId.toString().padStart(3, '0')}</td>
            <td>${customerName}</td>
            <td><span class="policy-type-badge ${policyType.toLowerCase()}">${policyType}</span></td>
            <td>${formatDate(expiryDate)}</td>
            <td><span class="days-left ${daysLeftClass}">${daysLeft < 0 ? Math.abs(daysLeft) + ' days overdue' : daysLeft + ' days'}</span></td>
            <td><span class="status-badge ${status.toLowerCase().replace(' ', '')}">${status}</span></td>
            <td><span class="priority-badge ${priority.toLowerCase()}">${priority}</span></td>
            <td>${assignedTo}</td>
            <td>
                <div class="action-buttons">
                    <button class="action-btn edit" data-renewal-id="${renewal.id}" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="action-btn delete" data-renewal-id="${renewal.id}" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                    <button class="action-btn view" data-renewal-id="${renewal.id}" title="View Details">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </td>
        `;
        fragment.appendChild(row);
    });
    
    tbody.empty().append(fragment);
    updateRenewalsPaginationInfo();
    
    // Add event listeners for action buttons
    tbody.find('.action-btn.edit').click(function() {
        const renewalId = parseInt($(this).data('renewal-id'));
        editRenewal(renewalId);
    });
    
    tbody.find('.action-btn.delete').click(function() {
        const renewalId = parseInt($(this).data('renewal-id'));
        deleteRenewalHandler(renewalId);
    });
    
    tbody.find('.action-btn.view').click(function() {
        const renewalId = parseInt($(this).data('renewal-id'));
        viewRenewalDetails(renewalId);
    });
};

const updateRenewalsPagination = () => {
    const totalPages = Math.ceil(renewalsFilteredData.length / renewalsRowsPerPage);
    const pageNumbers = $('#renewalsPageNumbers');
    pageNumbers.empty();
    
    // Previous button
    $('#renewalsPrevPage').prop('disabled', renewalsCurrentPage === 1);
    
    // Page numbers
    const maxVisiblePages = 5;
    let startPage = Math.max(1, renewalsCurrentPage - Math.floor(maxVisiblePages / 2));
    let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
    
    if (endPage - startPage + 1 < maxVisiblePages) {
        startPage = Math.max(1, endPage - maxVisiblePages + 1);
    }
    
    for (let i = startPage; i <= endPage; i++) {
        const pageBtn = $(`<button class="page-number ${i === renewalsCurrentPage ? 'active' : ''}">${i}</button>`);
        pageBtn.click(() => goToRenewalsPage(i));
        pageNumbers.append(pageBtn);
    }
    
    // Next button
    $('#renewalsNextPage').prop('disabled', renewalsCurrentPage === totalPages);
};

const updateRenewalsPaginationInfo = () => {
    const startRecord = (renewalsCurrentPage - 1) * renewalsRowsPerPage + 1;
    const endRecord = Math.min(renewalsCurrentPage * renewalsRowsPerPage, renewalsFilteredData.length);
    
    $('#renewalsStartRecord').text(startRecord);
    $('#renewalsEndRecord').text(endRecord);
    $('#renewalsTotalRecords').text(renewalsFilteredData.length);
};

const goToRenewalsPage = (page) => {
    renewalsCurrentPage = page;
    renderRenewalsTable();
    updateRenewalsPagination();
};

const updateRenewalsStats = () => {
    const pending = renewalsFilteredData.filter(r => (r.status || 'Pending') === 'Pending').length;
    const overdue = renewalsFilteredData.filter(r => (r.status || 'Pending') === 'Overdue').length;
    const completed = renewalsFilteredData.filter(r => (r.status || 'Pending') === 'Completed').length;
    const total = renewalsFilteredData.length;
    
    $('#pendingRenewalsCount').text(pending);
    $('#overdueRenewalsCount').text(overdue);
    $('#completedRenewalsCount').text(completed);
    $('#totalRenewalsCount').text(total);
};

const handleRenewalsSearch = () => {
    const searchTerm = $('#renewalsSearch').val().toLowerCase();
    
    renewalsFilteredData = allRenewals.filter(renewal => {
        const customerName = renewal.customerName || renewal.customer_name || '';
        const policyId = renewal.policyId || renewal.policy_id || '';
        const policyType = renewal.policyType || renewal.policy_type || '';
        const assignedTo = renewal.assignedTo || renewal.assigned_to || '';
        
        return customerName.toLowerCase().includes(searchTerm) ||
               policyId.toString().includes(searchTerm) ||
               policyType.toLowerCase().includes(searchTerm) ||
               assignedTo.toLowerCase().includes(searchTerm);
    });
    
    renewalsCurrentPage = 1;
    renderRenewalsTable();
    updateRenewalsPagination();
    updateRenewalsStats();
};

const handleRenewalsFilter = () => {
    applyRenewalsFilters();
};

const applyRenewalsFilters = () => {
    const statusFilter = $('#renewalStatusFilter').val();
    const priorityFilter = $('#renewalPriorityFilter').val();
    
    renewalsFilteredData = allRenewals.filter(renewal => {
        const status = renewal.status || 'Pending';
        const priority = renewal.priority || 'Medium';
        
        const statusMatch = !statusFilter || status === statusFilter;
        const priorityMatch = !priorityFilter || priority === priorityFilter;
        return statusMatch && priorityMatch;
    });
    
    renewalsCurrentPage = 1;
    renderRenewalsTable();
    updateRenewalsPagination();
    updateRenewalsStats();
};

const handleRenewalsRowsPerPageChange = () => {
    renewalsRowsPerPage = parseInt($('#renewalsRowsPerPage').val());
    renewalsCurrentPage = 1;
    renderRenewalsTable();
    updateRenewalsPagination();
};

const handleRenewalsSort = (column) => {
    if (renewalsCurrentSort.column === column) {
        renewalsCurrentSort.direction = renewalsCurrentSort.direction === 'asc' ? 'desc' : 'asc';
    } else {
        renewalsCurrentSort.column = column;
        renewalsCurrentSort.direction = 'asc';
    }
    
    renewalsFilteredData.sort((a, b) => {
        let aVal = a[column];
        let bVal = b[column];
        
        // Handle property name variations
        if (column === 'customerName' && !aVal) aVal = a.customer_name || '';
        if (column === 'customerName' && !bVal) bVal = b.customer_name || '';
        if (column === 'policyId' && !aVal) aVal = a.policy_id || 0;
        if (column === 'policyId' && !bVal) bVal = b.policy_id || 0;
        if (column === 'policyType' && !aVal) aVal = a.policy_type || '';
        if (column === 'policyType' && !bVal) bVal = b.policy_type || '';
        if (column === 'assignedTo' && !aVal) aVal = a.assigned_to || '';
        if (column === 'assignedTo' && !bVal) bVal = b.assigned_to || '';
        
        if (typeof aVal === 'string') {
            aVal = aVal.toLowerCase();
            bVal = bVal.toLowerCase();
        }
        
        if (aVal < bVal) return renewalsCurrentSort.direction === 'asc' ? -1 : 1;
        if (aVal > bVal) return renewalsCurrentSort.direction === 'asc' ? 1 : -1;
        return 0;
    });
    
    renderRenewalsTable();
    updateRenewalsSortIcons();
};

const updateRenewalsSortIcons = () => {
    $('#renewalsTable th[data-sort] i').removeClass('fa-sort-up fa-sort-down').addClass('fa-sort');
    
    const currentHeader = $(`#renewalsTable th[data-sort="${renewalsCurrentSort.column}"]`);
    const icon = currentHeader.find('i');
    
    icon.removeClass('fa-sort');
    icon.addClass(renewalsCurrentSort.direction === 'asc' ? 'fa-sort-up' : 'fa-sort-down');
};

const editRenewal = (id) => {
    const renewal = allRenewals.find(r => r.id === id);
    if (renewal) {
        $('#renewalModalTitle').text('Edit Renewal Reminder');
        
        // Get the correct property names from the API response with fallbacks
        const policyId = renewal.policyId || renewal.policy_id || '';
        const customerName = renewal.customerName || renewal.customer_name || '';
        const reminderDate = renewal.reminderDate || renewal.reminder_date || '';
        const priority = renewal.priority || 'Medium';
        const notes = renewal.notes || '';
        
        // Populate form fields with the correct field names from the modal
        $('#renewalPolicyId').val(policyId);
        $('#renewalCustomerName').val(customerName);
        $('#renewalReminderDate').val(reminderDate);
        $('#renewalPriority').val(priority);
        $('#renewalNotes').val(notes);
        
        // Store the renewal ID for form submission
        $('#renewalForm').data('edit-id', id);
        
        // Show the modal
        $('#renewalModal').addClass('show');
        
        // Add event listener for form submission if not already added
        if (!$('#renewalForm').data('edit-listener-added')) {
            $('#renewalForm').off('submit').on('submit', handleRenewalSubmit);
            $('#renewalForm').data('edit-listener-added', true);
        }
    } else {
        showNotification('Renewal not found', 'error');
    }
};

const deleteRenewalHandler = async (id) => {
    if (confirm('Are you sure you want to delete this renewal reminder?')) {
        try {
            await deleteRenewal(id);
            allRenewals = allRenewals.filter(r => r.id !== id);
            renewalsFilteredData = renewalsFilteredData.filter(r => r.id !== id);
            
            renderRenewalsTable();
            updateRenewalsPagination();
            updateRenewalsStats();
            
            showNotification('Renewal reminder deleted successfully!', 'success');
        } catch (error) {
            console.error('Failed to delete renewal:', error);
            showNotification('Failed to delete renewal', 'error');
        }
    }
};

const viewRenewalDetails = (id) => {
    const renewal = allRenewals.find(r => r.id === id);
    if (renewal) {
        // Get the correct property names from the API response with fallbacks
        const policyId = renewal.policyId || renewal.policy_id || 0;
        const customerName = renewal.customerName || renewal.customer_name || 'Unknown';
        const policyType = renewal.policyType || renewal.policy_type || 'Unknown';
        const expiryDate = renewal.expiryDate || renewal.expiry_date || 'Unknown';
        const daysLeft = renewal.daysLeft || renewal.days_left || 0;
        const status = renewal.status || 'Pending';
        const priority = renewal.priority || 'Medium';
        const assignedTo = renewal.assignedTo || renewal.assigned_to || 'Unassigned';
        const reminderDate = renewal.reminderDate || renewal.reminder_date || 'Unknown';
        const notes = renewal.notes || 'No notes available';
        
        // Create a detailed view modal content
        const modalContent = `
            <div class="modal" id="viewRenewalModal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2>Renewal Details - #${renewal.id}</h2>
                        <button class="modal-close" onclick="closeViewRenewalModal()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="detail-section">
                            <h3><i class="fas fa-file-contract"></i> Policy Information</h3>
                            <div class="detail-row">
                                <div class="detail-item">
                                    <label>Policy ID:</label>
                                    <span>#${policyId.toString().padStart(3, '0')}</span>
                                </div>
                                <div class="detail-item">
                                    <label>Customer Name:</label>
                                    <span>${customerName}</span>
                                </div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-item">
                                    <label>Policy Type:</label>
                                    <span><span class="policy-type-badge ${policyType.toLowerCase()}">${policyType}</span></span>
                                </div>
                                <div class="detail-item">
                                    <label>Expiry Date:</label>
                                    <span>${formatDate(expiryDate)}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="detail-section">
                            <h3><i class="fas fa-calendar-alt"></i> Renewal Information</h3>
                            <div class="detail-row">
                                <div class="detail-item">
                                    <label>Status:</label>
                                    <span><span class="status-badge ${status.toLowerCase().replace(' ', '')}">${status}</span></span>
                                </div>
                                <div class="detail-item">
                                    <label>Priority:</label>
                                    <span><span class="priority-badge ${priority.toLowerCase()}">${priority}</span></span>
                                </div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-item">
                                    <label>Reminder Date:</label>
                                    <span>${formatDate(reminderDate)}</span>
                                </div>
                                <div class="detail-item">
                                    <label>Days Left:</label>
                                    <span><span class="days-left ${daysLeft < 0 ? 'urgent' : daysLeft <= 7 ? 'warning' : 'safe'}">${daysLeft < 0 ? Math.abs(daysLeft) + ' days overdue' : daysLeft + ' days'}</span></span>
                                </div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-item">
                                    <label>Assigned To:</label>
                                    <span>${assignedTo}</span>
                                </div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-item full-width">
                                    <label>Notes:</label>
                                    <span>${notes}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="closeViewRenewalModal()">Close</button>
                    </div>
                </div>
            </div>
        `;
        
        // Remove existing modal if any
        $('#viewRenewalModal').remove();
        
        // Add new modal to body
        $('body').append(modalContent);
        
        // Show the modal
        $('#viewRenewalModal').addClass('show');
    } else {
        showNotification('Renewal not found', 'error');
    }
};

const closeViewRenewalModal = () => {
    $('#viewRenewalModal').removeClass('show');
    setTimeout(() => {
        $('#viewRenewalModal').remove();
    }, 300);
};

const exportRenewalsData = () => {
    const csvContent = generateRenewalsCSV();
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', `renewals_export_${new Date().toISOString().split('T')[0]}.csv`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    showNotification('Renewals data exported successfully!', 'success');
};

const generateRenewalsCSV = () => {
    const headers = ['Sl. No', 'Policy ID', 'Customer Name', 'Policy Type', 'Expiry Date', 'Days Left', 'Status', 'Priority', 'Assigned To', 'Reminder Date', 'Notes'];
    const csvRows = [headers.join(',')];
    
    renewalsFilteredData.forEach(renewal => {
        const row = [
            renewal.id,
            `#${renewal.policyId.toString().padStart(3, '0')}`,
            renewal.customerName,
            renewal.policyType,
            renewal.expiryDate,
            renewal.daysLeft < 0 ? `${Math.abs(renewal.daysLeft)} days overdue` : `${renewal.daysLeft} days`,
            renewal.status,
            renewal.priority,
            renewal.assignedTo,
            renewal.reminderDate,
            renewal.notes
        ];
        csvRows.push(row.join(','));
    });
    
    return csvRows.join('\n');
}; 

// Reports Functions
const updateReportDateRange = () => {
    reportDateRange.start = $('#reportStartDate').val();
    reportDateRange.end = $('#reportEndDate').val();
    generateReports();
};

const updateReportType = () => {
    currentReportType = $('#reportTypeFilter').val();
    generateReports();
};

const updateKPIs = () => {
    // Calculate KPIs based on current data
    const totalPremium = allPolicies.reduce((sum, policy) => sum + parseFloat(policy.premium), 0);
    const totalRevenue = allPolicies.reduce((sum, policy) => sum + parseFloat(policy.revenue), 0);
    const activePolicies = allPolicies.filter(policy => policy.status === 'Active').length;
    const conversionRate = allFollowups.filter(f => f.status === 'Completed').length / allFollowups.length * 100;
    
    // Update KPI values
    $('#totalPremiumKPI').text(`₹${totalPremium.toLocaleString()}`);
    $('#totalRevenueKPI').text(`₹${totalRevenue.toLocaleString()}`);
    $('#activePoliciesKPI').text(activePolicies);
    $('#conversionRateKPI').text(`${conversionRate.toFixed(1)}%`);
    
    // Calculate changes (mock data for demonstration)
    const premiumChange = Math.random() * 20 - 10; // -10% to +10%
    const revenueChange = Math.random() * 15 - 5; // -5% to +10%
    const policiesChange = Math.random() * 25 - 10; // -10% to +15%
    const conversionChange = Math.random() * 30 - 15; // -15% to +15%
    
    // Update change indicators
    $('#premiumChange').text(`${premiumChange > 0 ? '+' : ''}${premiumChange.toFixed(1)}%`).removeClass('positive negative').addClass(premiumChange >= 0 ? 'positive' : 'negative');
    $('#revenueChange').text(`${revenueChange > 0 ? '+' : ''}${revenueChange.toFixed(1)}%`).removeClass('positive negative').addClass(revenueChange >= 0 ? 'positive' : 'negative');
    $('#policiesChange').text(`${policiesChange > 0 ? '+' : ''}${policiesChange.toFixed(1)}%`).removeClass('positive negative').addClass(policiesChange >= 0 ? 'positive' : 'negative');
    $('#conversionChange').text(`${conversionChange > 0 ? '+' : ''}${conversionChange.toFixed(1)}%`).removeClass('positive negative').addClass(conversionChange >= 0 ? 'positive' : 'negative');
};

const initializeReportCharts = () => {
    // Only initialize report charts on reports page
    if (!$('#reports').hasClass('active')) {
        return;
    }
    
    // Initialize all report charts
    updateTrendChart();
    updatePolicyTypeChart();
    updateAgentPerformanceChart();
    updateRenewalStatusChart();
};

const updateTrendChart = () => {
    const period = parseInt($('#trendPeriod').val());
    const ctx = document.getElementById('trendChart');
    
    if (!ctx) return;
    
    // Destroy existing chart
    if (reportCharts.trendChart) {
        reportCharts.trendChart.destroy();
    }
    
    // Generate data based on period
    const labels = generateDateLabels(period);
    const premiumData = generateRandomData(period, 50000, 100000);
    const revenueData = generateRandomData(period, 10000, 30000);
    
    reportCharts.trendChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Premium (₹)',
                    data: premiumData,
                    borderColor: '#4F46E5',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Revenue (₹)',
                    data: revenueData,
                    borderColor: '#10B981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        color: $('body').hasClass('dark-theme') ? '#F1F5F9' : '#111827'
                    }
                }
            },
            scales: {
                x: {
                    ticks: {
                        color: $('body').hasClass('dark-theme') ? '#9CA3AF' : '#6B7280'
                    },
                    grid: {
                        color: $('body').hasClass('dark-theme') ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)'
                    }
                },
                y: {
                    ticks: {
                        color: $('body').hasClass('dark-theme') ? '#9CA3AF' : '#6B7280',
                        callback: function(value) {
                            return '₹' + value.toLocaleString();
                        }
                    },
                    grid: {
                        color: $('body').hasClass('dark-theme') ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)'
                    }
                }
            }
        }
    });
};

const updatePolicyTypeChart = () => {
    const period = parseInt($('#policyTypePeriod').val());
    const ctx = document.getElementById('policyTypeChart');
    
    if (!ctx) return;
    
    // Destroy existing chart
    if (reportCharts.policyTypeChart) {
        reportCharts.policyTypeChart.destroy();
    }
    
    // Calculate policy type distribution
    const motorCount = allPolicies.filter(p => p.type === 'Motor').length;
    const healthCount = allPolicies.filter(p => p.type === 'Health').length;
    const lifeCount = allPolicies.filter(p => p.type === 'Life').length;
    
    reportCharts.policyTypeChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Motor', 'Health', 'Life'],
            datasets: [{
                data: [motorCount, healthCount, lifeCount],
                backgroundColor: [
                    'rgba(79, 70, 229, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(245, 158, 11, 0.8)'
                ],
                borderColor: [
                    'rgba(79, 70, 229, 1)',
                    'rgba(16, 185, 129, 1)',
                    'rgba(245, 158, 11, 1)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: $('body').hasClass('dark-theme') ? '#F1F5F9' : '#111827',
                        padding: 20
                    }
                }
            }
        }
    });
};

const updateAgentPerformanceChart = () => {
    const period = parseInt($('#agentPerformancePeriod').val());
    const ctx = document.getElementById('agentPerformanceChart');
    
    if (!ctx) return;
    
    // Destroy existing chart
    if (reportCharts.agentPerformanceChart) {
        reportCharts.agentPerformanceChart.destroy();
    }
    
    // Calculate agent performance
    const agentData = allAgents.map(agent => {
        const policies = allPolicies.filter(p => p.assignedTo === agent.name);
        const renewals = allRenewals.filter(r => r.assignedTo === agent.name);
        const followups = allFollowups.filter(f => f.assignedTo === agent.name);
        
        return {
            name: agent.name,
            policies: policies.length,
            renewals: renewals.length,
            followups: followups.length,
            performance: Math.floor(Math.random() * 40) + 60 // 60-100%
        };
    });
    
    reportCharts.agentPerformanceChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: agentData.map(a => a.name),
            datasets: [{
                label: 'Performance Score (%)',
                data: agentData.map(a => a.performance),
                backgroundColor: 'rgba(79, 70, 229, 0.8)',
                borderColor: 'rgba(79, 70, 229, 1)',
                borderWidth: 1
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
                x: {
                    ticks: {
                        color: $('body').hasClass('dark-theme') ? '#9CA3AF' : '#6B7280'
                    },
                    grid: {
                        color: $('body').hasClass('dark-theme') ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)'
                    }
                },
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        color: $('body').hasClass('dark-theme') ? '#9CA3AF' : '#6B7280',
                        callback: function(value) {
                            return value + '%';
                        }
                    },
                    grid: {
                        color: $('body').hasClass('dark-theme') ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)'
                    }
                }
            }
        }
    });
};

const updateRenewalStatusChart = () => {
    const period = parseInt($('#renewalStatusPeriod').val());
    const ctx = document.getElementById('renewalStatusChart');
    
    if (!ctx) return;
    
    // Destroy existing chart
    if (reportCharts.renewalStatusChart) {
        reportCharts.renewalStatusChart.destroy();
    }
    
    // Calculate renewal status distribution
    const pendingCount = allRenewals.filter(r => r.status === 'Pending').length;
    const completedCount = allRenewals.filter(r => r.status === 'Completed').length;
    const overdueCount = allRenewals.filter(r => r.status === 'Overdue').length;
    const inProgressCount = allRenewals.filter(r => r.status === 'In Progress').length;
    
    reportCharts.renewalStatusChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Pending', 'Completed', 'Overdue', 'In Progress'],
            datasets: [{
                data: [pendingCount, completedCount, overdueCount, inProgressCount],
                backgroundColor: [
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(239, 68, 68, 0.8)',
                    'rgba(245, 158, 11, 0.8)'
                ],
                borderColor: [
                    'rgba(59, 130, 246, 1)',
                    'rgba(16, 185, 129, 1)',
                    'rgba(239, 68, 68, 1)',
                    'rgba(245, 158, 11, 1)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: $('body').hasClass('dark-theme') ? '#F1F5F9' : '#111827',
                        padding: 20
                    }
                }
            }
        }
    });
};

const initializeReportTabs = () => {
    $('.tab-btn').click(function() {
        const tab = $(this).data('tab');
        
        // Update active tab
        $('.tab-btn').removeClass('active');
        $(this).addClass('active');
        
        // Show active content
        $('.tab-content').removeClass('active');
        $(`#${tab}Report`).addClass('active');
    });
};

const generateReports = () => {
    // Check if data is available before generating reports
    if (!allPolicies && !allAgents && !allRenewals && !allFollowups) {
        console.log('Data not yet loaded, skipping report generation');
        return;
    }
    
    updateKPIs();
    generatePoliciesReport();
    generateRenewalsReport();
    generateFollowupsReport();
    generateAgentsReport();
};

const generatePoliciesReport = () => {
    const tbody = $('#policiesReportTableBody');
    tbody.empty();
    
    // Filter policies based on date range if needed
    const filteredPolicies = allPolicies || [];
    
    filteredPolicies.forEach(policy => {
        // Use the correct property names from the API response with fallbacks
        const policyType = policy.policyType || policy.type || 'Unknown';
        const customerName = policy.customerName || policy.owner || 'Unknown';
        const companyName = policy.companyName || policy.company || 'Unknown';
        const premium = policy.premium || 0;
        const status = policy.status || 'Active';
        const startDate = policy.startDate || 'Unknown';
        const endDate = policy.endDate || 'Unknown';
        
        const row = `
            <tr>
                <td>#${policy.id.toString().padStart(3, '0')}</td>
                <td>${customerName}</td>
                <td><span class="policy-type-badge ${policyType.toLowerCase()}">${policyType}</span></td>
                <td>${getShortCompanyName(companyName)}</td>
                <td>₹${parseFloat(premium).toLocaleString()}</td>
                <td><span class="status-badge ${status.toLowerCase()}">${status}</span></td>
                <td>${formatDate(startDate)}</td>
                <td>${formatDate(endDate)}</td>
            </tr>
        `;
        tbody.append(row);
    });
    
    // Update summary stats
    $('#totalPoliciesReport').text(filteredPolicies.length);
    $('#activePoliciesReport').text(filteredPolicies.filter(p => (p.status || 'Active') === 'Active').length);
    $('#expiredPoliciesReport').text(filteredPolicies.filter(p => (p.status || 'Active') === 'Expired').length);
    
    const avgPremium = filteredPolicies.length > 0 
        ? filteredPolicies.reduce((sum, p) => sum + parseFloat(p.premium || 0), 0) / filteredPolicies.length 
        : 0;
    $('#avgPremiumReport').text(`₹${avgPremium.toFixed(0)}`);
};

const generateRenewalsReport = () => {
    const tbody = $('#renewalsReportTableBody');
    tbody.empty();
    
    const renewals = allRenewals || [];
    
    renewals.forEach(renewal => {
        const customerName = renewal.customerName || 'Unknown';
        const expiryDate = renewal.expiryDate || 'Unknown';
        const daysLeft = renewal.daysLeft || 0;
        const status = renewal.status || 'Pending';
        const priority = renewal.priority || 'Medium';
        const assignedTo = renewal.assignedTo || 'Unassigned';
        
        const row = `
            <tr>
                <td>#${renewal.policyId ? renewal.policyId.toString().padStart(3, '0') : '000'}</td>
                <td>${customerName}</td>
                <td>${formatDate(expiryDate)}</td>
                <td><span class="days-left ${daysLeft < 0 ? 'urgent' : daysLeft <= 7 ? 'warning' : 'safe'}">${daysLeft < 0 ? Math.abs(daysLeft) + ' days overdue' : daysLeft + ' days'}</span></td>
                <td><span class="status-badge ${status.toLowerCase().replace(' ', '')}">${status}</span></td>
                <td><span class="priority-badge ${priority.toLowerCase()}">${priority}</span></td>
                <td>${assignedTo}</td>
            </tr>
        `;
        tbody.append(row);
    });
    
    // Update summary stats
    $('#pendingRenewalsReport').text(renewals.filter(r => (r.status || 'Pending') === 'Pending').length);
    $('#completedRenewalsReport').text(renewals.filter(r => (r.status || 'Pending') === 'Completed').length);
    $('#overdueRenewalsReport').text(renewals.filter(r => (r.status || 'Pending') === 'Overdue').length);
    
    const renewalRate = renewals.length > 0 
        ? renewals.filter(r => (r.status || 'Pending') === 'Completed').length / renewals.length * 100 
        : 0;
    $('#renewalRateReport').text(`${renewalRate.toFixed(1)}%`);
};

const generateFollowupsReport = () => {
    const tbody = $('#followupsReportTableBody');
    tbody.empty();
    
    const followups = allFollowups || [];
    
    followups.forEach(followup => {
        const customerName = followup.customerName || 'Unknown';
        const phone = followup.phone || 'Unknown';
        const followupType = followup.followupType || 'General';
        const status = followup.status || 'Pending';
        const assignedTo = followup.assignedTo || 'Unassigned';
        const lastFollowupDate = followup.lastFollowupDate || 'Unknown';
        const nextFollowupDate = followup.nextFollowupDate || null;
        
        const row = `
            <tr>
                <td>${customerName}</td>
                <td>${phone}</td>
                <td><span class="followup-type-badge ${followupType.toLowerCase().replace(' ', '-')}">${followupType}</span></td>
                <td><span class="status-badge ${status.toLowerCase().replace(' ', '')}">${status}</span></td>
                <td>${assignedTo}</td>
                <td>${formatDate(lastFollowupDate)}</td>
                <td>${nextFollowupDate ? formatDate(nextFollowupDate) : '-'}</td>
            </tr>
        `;
        tbody.append(row);
    });
    
    // Update summary stats
    $('#totalFollowupsReport').text(followups.length);
    $('#completedTodayReport').text(followups.filter(f => (f.status || 'Pending') === 'Completed' && f.lastFollowupDate === new Date().toISOString().split('T')[0]).length);
    $('#pendingFollowupsReport').text(followups.filter(f => (f.status || 'Pending') === 'Pending').length);
    
    const successRate = followups.length > 0 
        ? followups.filter(f => (f.status || 'Pending') === 'Completed').length / followups.length * 100 
        : 0;
    $('#successRateReport').text(`${successRate.toFixed(1)}%`);
};

const generateAgentsReport = () => {
    const tbody = $('#agentsReportTableBody');
    tbody.empty();
    
    const agents = allAgents || [];
    const policies = allPolicies || [];
    const renewals = allRenewals || [];
    const followups = allFollowups || [];
    
    const agentStats = agents.map(agent => {
        const agentName = agent.name || 'Unknown';
        const agentPolicies = policies.filter(p => (p.assignedTo || p.customerName) === agentName);
        const agentRenewals = renewals.filter(r => (r.assignedTo || r.customerName) === agentName);
        const agentFollowups = followups.filter(f => (f.assignedTo || f.customerName) === agentName);
        const totalPremium = agentPolicies.reduce((sum, p) => sum + parseFloat(p.premium || 0), 0);
        const performance = Math.floor(Math.random() * 40) + 60; // 60-100%
        
        return {
            name: agentName,
            policies: agentPolicies.length,
            totalPremium,
            renewals: agentRenewals.length,
            followups: agentFollowups.length,
            performance
        };
    });
    
    agentStats.forEach(agent => {
        const row = `
            <tr>
                <td>${agent.name}</td>
                <td>${agent.policies}</td>
                <td>₹${agent.totalPremium.toLocaleString()}</td>
                <td>${agent.renewals}</td>
                <td>${agent.followups}</td>
                <td><span class="performance-score">${agent.performance}%</span></td>
            </tr>
        `;
        tbody.append(row);
    });
    
    // Update summary stats
    $('#totalAgentsReport').text(agents.length);
    $('#activeAgentsReport').text(agents.length);
    
    const topPerformer = agentStats.length > 0 
        ? agentStats.reduce((max, agent) => agent.performance > max.performance ? agent : max, agentStats[0])
        : null;
    $('#topPerformerReport').text(topPerformer ? topPerformer.name : '-');
    
    const avgPerformance = agentStats.length > 0 
        ? agentStats.reduce((sum, agent) => sum + agent.performance, 0) / agentStats.length 
        : 0;
    $('#avgPerformanceReport').text(`${avgPerformance.toFixed(1)}%`);
};

const exportReport = () => {
    const activeTab = $('.tab-btn.active').data('tab');
    let csvContent = '';
    
    switch (activeTab) {
        case 'policies':
            csvContent = generatePoliciesCSV();
            break;
        case 'renewals':
            csvContent = generateRenewalsCSV();
            break;
        case 'followups':
            csvContent = generateFollowupsCSV();
            break;
        case 'agents':
            csvContent = generateAgentsCSV();
            break;
        default:
            csvContent = generatePoliciesCSV();
    }
    
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', `${activeTab}_report_${new Date().toISOString().split('T')[0]}.csv`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    showNotification(`${activeTab.charAt(0).toUpperCase() + activeTab.slice(1)} report exported successfully!`, 'success');
};

const generateAgentsCSV = () => {
    const headers = ['Agent Name', 'Policies Sold', 'Total Premium', 'Renewals Handled', 'Follow-ups', 'Performance Score'];
    const csvRows = [headers.join(',')];
    
    const agentStats = allAgents.map(agent => {
        const policies = allPolicies.filter(p => p.assignedTo === agent.name);
        const renewals = allRenewals.filter(r => r.assignedTo === agent.name);
        const followups = allFollowups.filter(f => f.assignedTo === agent.name);
        const totalPremium = policies.reduce((sum, p) => sum + parseFloat(p.premium), 0);
        const performance = Math.floor(Math.random() * 40) + 60;
        
        return [agent.name, policies.length, totalPremium, renewals.length, followups.length, performance + '%'];
    });
    
    agentStats.forEach(row => {
        csvRows.push(row.join(','));
    });
    
    return csvRows.join('\n');
};

// Helper functions for charts
const generateDateLabels = (days) => {
    const labels = [];
    const today = new Date();
    
    for (let i = days - 1; i >= 0; i--) {
        const date = new Date(today);
        date.setDate(today.getDate() - i);
        labels.push(date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }));
    }
    
    return labels;
};

const generateRandomData = (days, min, max) => {
    const data = [];
    for (let i = 0; i < days; i++) {
        data.push(Math.floor(Math.random() * (max - min + 1)) + min);
    }
    return data;
};

// Agents page variables
let agentsCurrentPage = 1;
let agentsRowsPerPage = 10;
let agentsCurrentSort = { column: 'id', direction: 'asc' };
let agentsFilteredData = [];

// Agents Table Functions
const renderAgentsTable = () => {
    const tbody = $('#agentsTableBody');
    tbody.empty();
    
    const startIndex = (agentsCurrentPage - 1) * agentsRowsPerPage;
    const endIndex = startIndex + agentsRowsPerPage;
    const pageData = agentsFilteredData.slice(startIndex, endIndex);
    
    const fragment = document.createDocumentFragment();
    
    pageData.forEach((agent, index) => {
        const policies = allPolicies.filter(p => p.assignedTo === agent.name).length;
        const performance = Math.floor(Math.random() * 40) + 60; // 60-100%
        
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${startIndex + index + 1}</td>
            <td>${agent.name}</td>
            <td>${agent.phone}</td>
            <td>${agent.email}</td>
            <td>${agent.userId}</td>
            <td><span class="status-badge active">Active</span></td>
            <td>${policies}</td>
            <td><span class="performance-score">${performance}%</span></td>
            <td>
                <div class="action-buttons">
                    <button class="action-btn edit" onclick="editAgent(${agent.id})" title="Edit Agent">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="action-btn delete" onclick="deleteAgentHandler(${agent.id})" title="Delete Agent">
                        <i class="fas fa-trash"></i>
                    </button>
                    <button class="action-btn view" onclick="viewAgentDetails(${agent.id})" title="View Details">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </td>
        `;
        fragment.appendChild(row);
    });
    
    tbody.append(fragment);
};

const updateAgentsPagination = () => {
    const totalPages = Math.ceil(agentsFilteredData.length / agentsRowsPerPage);
    const pageNumbers = $('#agentsPageNumbers');
    pageNumbers.empty();
    
    const maxVisiblePages = 5;
    let startPage = Math.max(1, agentsCurrentPage - Math.floor(maxVisiblePages / 2));
    let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
    
    if (endPage - startPage + 1 < maxVisiblePages) {
        startPage = Math.max(1, endPage - maxVisiblePages + 1);
    }
    
    for (let i = startPage; i <= endPage; i++) {
        const pageBtn = $(`<button class="page-btn ${i === agentsCurrentPage ? 'active' : ''}">${i}</button>`);
        pageBtn.click(() => goToAgentsPage(i));
        pageNumbers.append(pageBtn);
    }
    
    $('#agentsPrevPage').prop('disabled', agentsCurrentPage === 1);
    $('#agentsNextPage').prop('disabled', agentsCurrentPage === totalPages);
    
    updateAgentsPaginationInfo();
};

const updateAgentsPaginationInfo = () => {
    const startRecord = (agentsCurrentPage - 1) * agentsRowsPerPage + 1;
    const endRecord = Math.min(agentsCurrentPage * agentsRowsPerPage, agentsFilteredData.length);
    const totalRecords = agentsFilteredData.length;
    
    $('#agentsStartRecord').text(startRecord);
    $('#agentsEndRecord').text(endRecord);
    $('#agentsTotalRecords').text(totalRecords);
};

const goToAgentsPage = (page) => {
    const totalPages = Math.ceil(agentsFilteredData.length / agentsRowsPerPage);
    if (page >= 1 && page <= totalPages) {
        agentsCurrentPage = page;
        renderAgentsTable();
        updateAgentsPagination();
    }
};

const updateAgentsStats = () => {
    $('#totalAgentsCount').text(allAgents.length);
    $('#activeAgentsCount').text(allAgents.length);
    
    const totalPolicies = allPolicies.length;
    $('#totalPoliciesCount').text(totalPolicies);
    
    const avgPerformance = allAgents.reduce((sum, agent) => {
        const policies = allPolicies.filter(p => p.assignedTo === agent.name).length;
        const performance = Math.floor(Math.random() * 40) + 60;
        return sum + performance;
    }, 0) / allAgents.length;
    
    $('#avgPerformanceCount').text(`${avgPerformance.toFixed(1)}%`);
};

const handleAgentsSearch = () => {
    const searchTerm = $('#agentsSearch').val().toLowerCase();
    
    agentsFilteredData = allAgents.filter(agent => 
        agent.name.toLowerCase().includes(searchTerm) ||
        agent.phone.includes(searchTerm) ||
        agent.email.toLowerCase().includes(searchTerm) ||
        agent.userId.toLowerCase().includes(searchTerm)
    );
    
    agentsCurrentPage = 1;
    renderAgentsTable();
    updateAgentsPagination();
};

const handleAgentsRowsPerPageChange = () => {
    agentsRowsPerPage = parseInt($('#agentsRowsPerPage').val());
    agentsCurrentPage = 1;
    renderAgentsTable();
    updateAgentsPagination();
};

const handleAgentsSort = (column) => {
    if (agentsCurrentSort.column === column) {
        agentsCurrentSort.direction = agentsCurrentSort.direction === 'asc' ? 'desc' : 'asc';
    } else {
        agentsCurrentSort.column = column;
        agentsCurrentSort.direction = 'asc';
    }
    
    agentsFilteredData.sort((a, b) => {
        let aVal, bVal;
        
        switch (column) {
            case 'id':
                aVal = a.id;
                bVal = b.id;
                break;
            case 'name':
                aVal = a.name.toLowerCase();
                bVal = b.name.toLowerCase();
                break;
            case 'phone':
                aVal = a.phone;
                bVal = b.phone;
                break;
            case 'email':
                aVal = a.email.toLowerCase();
                bVal = b.email.toLowerCase();
                break;
            case 'userId':
                aVal = a.userId.toLowerCase();
                bVal = b.userId.toLowerCase();
                break;
            case 'status':
                aVal = 'Active';
                bVal = 'Active';
                break;
            case 'policies':
                aVal = allPolicies.filter(p => p.assignedTo === a.name).length;
                bVal = allPolicies.filter(p => p.assignedTo === b.name).length;
                break;
            case 'performance':
                aVal = Math.floor(Math.random() * 40) + 60;
                bVal = Math.floor(Math.random() * 40) + 60;
                break;
            default:
                return 0;
        }
        
        if (aVal < bVal) return agentsCurrentSort.direction === 'asc' ? -1 : 1;
        if (aVal > bVal) return agentsCurrentSort.direction === 'asc' ? 1 : -1;
        return 0;
    });
    
    renderAgentsTable();
    updateAgentsSortIcons();
};

const updateAgentsSortIcons = () => {
    $('#agentsTable th[data-sort] i').removeClass('fa-sort-up fa-sort-down').addClass('fa-sort');
    
    const currentHeader = $(`#agentsTable th[data-sort="${agentsCurrentSort.column}"]`);
    const icon = currentHeader.find('i');
    
    icon.removeClass('fa-sort');
    icon.addClass(agentsCurrentSort.direction === 'asc' ? 'fa-sort-up' : 'fa-sort-down');
};

const editAgent = (id) => {
    const agent = allAgents.find(a => a.id === id);
    if (agent) {
        // Set modal title
        $('#agentModalTitle').text('Edit Agent');
        
        // Populate form fields
        $('#agentName').val(agent.name || '');
        $('#agentPhone').val(agent.phone || '');
        $('#agentEmail').val(agent.email || '');
        $('#agentUserId').val(agent.userId || '');
        $('#agentStatus').val(agent.status || 'Active');
        $('#agentAddress').val(agent.address || '');
        $('#agentPassword').val(''); // Clear password for security
        
        // Store the agent ID for form submission
        $('#agentForm').data('edit-id', id);
        
        // Show the modal
        $('#agentModal').addClass('show');
        
        // Add event listener for form submission if not already added
        if (!$('#agentForm').data('edit-listener-added')) {
            $('#saveAgentBtn').off('click').on('click', handleAgentSubmit);
            $('#agentForm').data('edit-listener-added', true);
        }
    } else {
        showNotification('Agent not found', 'error');
    }
};

const deleteAgentHandler = async (id) => {
    if (confirm('Are you sure you want to delete this agent?')) {
        try {
            await deleteAgent(id);
        allAgents = allAgents.filter(a => a.id !== id);
        agentsFilteredData = agentsFilteredData.filter(a => a.id !== id);
        
        renderAgentsTable();
        updateAgentsPagination();
        updateAgentsStats();
        
        showNotification('Agent deleted successfully!', 'success');
        } catch (error) {
            console.error('Failed to delete agent:', error);
            showNotification('Failed to delete agent', 'error');
        }
    }
};

const viewAgentDetails = (id) => {
    const agent = allAgents.find(a => a.id === id);
    if (agent) {
        showNotification(`Viewing details for agent ${agent.name}`, 'info');
        // Here you would typically open a detailed view modal
    }
};

const exportAgentsData = () => {
    const csvContent = generateAgentsTableCSV();
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', `agents_export_${new Date().toISOString().split('T')[0]}.csv`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    showNotification('Agents data exported successfully!', 'success');
};

const generateAgentsTableCSV = () => {
    const headers = ['Sl. No', 'Agent Name', 'Phone Number', 'Email', 'User ID', 'Status', 'Policies', 'Performance'];
    const csvRows = [headers.join(',')];
    
    agentsFilteredData.forEach((agent, index) => {
        const policies = allPolicies.filter(p => p.assignedTo === agent.name).length;
        const performance = Math.floor(Math.random() * 40) + 60;
        
        const row = [
            index + 1,
            agent.name,
            agent.phone,
            agent.email,
            agent.userId,
            'Active',
            policies,
            performance + '%'
        ];
        csvRows.push(row.join(','));
    });
    
    return csvRows.join('\n');
};

// Settings Functions
const switchSettingsTab = (tab) => {
    // Update active tab
    $('.settings-tab-btn').removeClass('active');
    $(`.settings-tab-btn[data-tab="${tab}"]`).addClass('active');
    
    // Show active content
    $('.settings-content').removeClass('active');
    $(`#${tab}Settings`).addClass('active');
};

const saveSettings = () => {
    // Collect all settings values
    const settings = {
        company: {
            name: $('#companyName').val(),
            email: $('#companyEmail').val(),
            phone: $('#companyPhone').val(),
            address: $('#companyAddress').val()
        },
        system: {
            currency: $('#defaultCurrency').val(),
            dateFormat: $('#dateFormat').val(),
            timezone: $('#timezone').val(),
            language: $('#language').val()
        },
        policy: {
            duration: $('#defaultPolicyDuration').val(),
            renewalReminder: $('#renewalReminderDays').val(),
            maxPolicies: $('#maxPoliciesPerAgent').val(),
            commissionRate: $('#commissionRate').val()
        },
        notifications: {
            email: $('#emailNotifications').is(':checked'),
            sms: $('#smsNotifications').is(':checked'),
            whatsapp: $('#whatsappEnabled').is(':checked'),
            smtpServer: $('#smtpServer').val(),
            smtpPort: $('#smtpPort').val(),
            smtpUsername: $('#smtpUsername').val(),
            smtpPassword: $('#smtpPassword').val(),
            smsProvider: $('#smsProvider').val(),
            smsApiKey: $('#smsApiKey').val(),
            smsSenderId: $('#smsSenderId').val(),
            whatsappBusinessId: $('#whatsappBusinessId').val(),
            whatsappAccessToken: $('#whatsappAccessToken').val(),
            whatsappPhoneNumber: $('#whatsappPhoneNumber').val(),
            whatsappWebhookUrl: $('#whatsappWebhookUrl').val(),
            policyExpiry: $('#policyExpiryAlerts').is(':checked'),
            renewalReminders: $('#renewalReminders').is(':checked'),
            followupAlerts: $('#followupAlerts').is(':checked'),
            commissionAlerts: $('#commissionAlerts').is(':checked')
        },
        security: {
            minPasswordLength: $('#minPasswordLength').val(),
            requireUppercase: $('#requireUppercase').is(':checked'),
            requireLowercase: $('#requireLowercase').is(':checked'),
            requireNumbers: $('#requireNumbers').is(':checked'),
            requireSpecialChars: $('#requireSpecialChars').is(':checked'),
            sessionTimeout: $('#sessionTimeout').val(),
            maxLoginAttempts: $('#maxLoginAttempts').val(),
            lockoutDuration: $('#lockoutDuration').val(),
            enable2FA: $('#enable2FA').is(':checked'),
            twoFactorMethod: $('#twoFactorMethod').val()
        },
        appearance: {
            themeMode: $('#themeMode').val(),
            primaryColor: $('#primaryColor').val(),
            secondaryColor: $('#secondaryColor').val(),
            defaultRowsPerPage: $('#defaultRowsPerPage').val(),
            showAnimations: $('#showAnimations').is(':checked'),
            compactMode: $('#compactMode').is(':checked')
        },
        backup: {
            autoBackup: $('#autoBackup').is(':checked'),
            backupFrequency: $('#backupFrequency').val(),
            backupTime: $('#backupTime').val(),
            retainBackups: $('#retainBackups').val(),
            exportFormat: $('#exportFormat').val(),
            includeDeleted: $('#includeDeleted').is(':checked'),
            exportDateRange: $('#exportDateRange').val(),
            dataRetention: $('#dataRetention').val(),
            autoArchive: $('#autoArchive').is(':checked')
        }
    };
    
    // Save to localStorage (in a real app, this would go to a database)
    localStorage.setItem('insuranceSettings', JSON.stringify(settings));
    
    showNotification('Settings saved successfully!', 'success');
};

const cancelSettings = () => {
    // Reset form to original values
    loadSettings();
    showNotification('Settings reset to last saved values', 'info');
};

const loadSettings = () => {
    const savedSettings = localStorage.getItem('insuranceSettings');
    if (savedSettings) {
        const settings = JSON.parse(savedSettings);
        
        // Apply settings to form fields
        $('#companyName').val(settings.company?.name || 'Insurance Management System');
        $('#companyEmail').val(settings.company?.email || 'info@insurance.com');
        $('#companyPhone').val(settings.company?.phone || '+91 98765 43210');
        $('#companyAddress').val(settings.company?.address || '123 Insurance Street, Business District, City - 123456');
        
        $('#defaultCurrency').val(settings.system?.currency || 'INR');
        $('#dateFormat').val(settings.system?.dateFormat || 'DD/MM/YYYY');
        $('#timezone').val(settings.system?.timezone || 'Asia/Kolkata');
        $('#language').val(settings.system?.language || 'en');
        
        // Apply theme if changed
        if (settings.appearance?.themeMode) {
            if (settings.appearance.themeMode === 'dark') {
                $('body').addClass('dark-theme');
            } else if (settings.appearance.themeMode === 'light') {
                $('body').removeClass('dark-theme');
            }
        }
        
        // Apply notification settings
        if (settings.notifications) {
            $('#emailNotifications').prop('checked', settings.notifications.email || false);
            $('#smsNotifications').prop('checked', settings.notifications.sms || false);
            $('#whatsappEnabled').prop('checked', settings.notifications.whatsapp || false);
            $('#smtpServer').val(settings.notifications.smtpServer || 'smtp.gmail.com');
            $('#smtpPort').val(settings.notifications.smtpPort || '587');
            $('#smtpUsername').val(settings.notifications.smtpUsername || 'noreply@insurance.com');
            $('#smtpPassword').val(settings.notifications.smtpPassword || '********');
            $('#smsProvider').val(settings.notifications.smsProvider || 'twilio');
            $('#smsApiKey').val(settings.notifications.smsApiKey || 'your_api_key_here');
            $('#smsSenderId').val(settings.notifications.smsSenderId || 'INSURANCE');
            $('#whatsappBusinessId').val(settings.notifications.whatsappBusinessId || '');
            $('#whatsappAccessToken').val(settings.notifications.whatsappAccessToken || '');
            $('#whatsappPhoneNumber').val(settings.notifications.whatsappPhoneNumber || '');
            $('#whatsappWebhookUrl').val(settings.notifications.whatsappWebhookUrl || '');
            $('#policyExpiryAlerts').prop('checked', settings.notifications.policyExpiry || true);
            $('#renewalReminders').prop('checked', settings.notifications.renewalReminders || true);
            $('#followupAlerts').prop('checked', settings.notifications.followupAlerts || true);
            $('#commissionAlerts').prop('checked', settings.notifications.commissionAlerts || true);
        }
    }
};

const exportAllData = () => {
    const format = $('#exportFormat').val();
    const includeDeleted = $('#includeDeleted').is(':checked');
    const dateRange = $('#exportDateRange').val();
    
    showNotification(`Exporting all data in ${format.toUpperCase()} format...`, 'info');
    
    // In a real app, this would trigger a server-side export
    setTimeout(() => {
        showNotification('Data export completed! Check your downloads folder.', 'success');
    }, 2000);
};

const clearCache = () => {
    if (confirm('Are you sure you want to clear the cache? This will refresh all data.')) {
        // Clear localStorage cache
        localStorage.removeItem('insuranceSettings');
        
        // Reload page data
        location.reload();
        
        showNotification('Cache cleared successfully!', 'success');
    }
};

const resetSettings = () => {
    if (confirm('Are you sure you want to reset all settings to defaults? This action cannot be undone.')) {
        // Clear saved settings
        localStorage.removeItem('insuranceSettings');
        
        // Reset form to default values
        $('#companyName').val('Insurance Management System');
        $('#companyEmail').val('info@insurance.com');
        $('#companyPhone').val('+91 98765 43210');
        $('#companyAddress').val('123 Insurance Street, Business District, City - 123456');
        
        $('#defaultCurrency').val('INR');
        $('#dateFormat').val('DD/MM/YYYY');
        $('#timezone').val('Asia/Kolkata');
        $('#language').val('en');
        
        // Reset theme
        $('body').removeClass('dark-theme');
        
        showNotification('Settings reset to defaults!', 'success');
    }
};

// Initialize settings on page load
$(document).ready(() => {
    loadSettings();
});

// Notification System Functions
const openBulkNotificationModal = () => {
    $('#bulkNotificationModal').addClass('show');
    updateRecipientsList();
    updateMessagePreview();
};

const closeBulkNotificationModal = () => {
    $('#bulkNotificationModal').removeClass('show');
};

const updateRecipientsList = () => {
    const filterType = $('#bulkFilterType').val();
    const daysRange = parseInt($('#bulkDaysRange').val());
    const policyType = $('#bulkPolicyType').val();
    
    let recipients = [];
    
    switch(filterType) {
        case 'expiring':
            recipients = getExpiringPoliciesRecipients(daysRange, policyType);
            break;
        case 'renewals':
            recipients = getRenewalsRecipients(daysRange, policyType);
            break;
        case 'followups':
            recipients = getFollowupsRecipients(daysRange, policyType);
            break;
        case 'custom':
            recipients = getAllCustomers();
            break;
    }
    
    const recipientsList = $('#recipientsList');
    const recipientsCount = $('#recipientsCount');
    
    recipientsList.empty();
    recipientsCount.text(recipients.length);
    
    recipients.forEach(recipient => {
        const recipientItem = $(`
            <div class="recipient-item">
                <div class="recipient-info">
                    <div class="recipient-name">${recipient.name}</div>
                    <div class="recipient-details">${recipient.phone} • ${recipient.email}</div>
                </div>
                <div class="recipient-policy">${recipient.policyType || 'N/A'}</div>
            </div>
        `);
        recipientsList.append(recipientItem);
    });
};

const getExpiringPoliciesRecipients = (daysRange, policyType) => {
    const today = new Date();
    const endDate = new Date(today.getTime() + (daysRange * 24 * 60 * 60 * 1000));
    
    return allPolicies
        .filter(policy => {
            const expiryDate = new Date(policy.endDate);
            const matchesDate = expiryDate >= today && expiryDate <= endDate;
            const matchesType = policyType === 'all' || policy.type.toLowerCase() === policyType;
            return matchesDate && matchesType;
        })
        .map(policy => ({
            name: policy.owner,
            phone: policy.phone,
            email: policy.email,
            policyType: policy.type,
            policyId: policy.id
        }));
};

const getRenewalsRecipients = (daysRange, policyType) => {
    return allRenewals
        .filter(renewal => {
            const reminderDate = new Date(renewal.reminderDate);
            const today = new Date();
            const endDate = new Date(today.getTime() + (daysRange * 24 * 60 * 60 * 1000));
            const matchesDate = reminderDate >= today && reminderDate <= endDate;
            const matchesType = policyType === 'all' || renewal.policyType.toLowerCase() === policyType;
            return matchesDate && matchesType;
        })
        .map(renewal => ({
            name: renewal.customerName,
            phone: renewal.phone,
            email: renewal.email,
            policyType: renewal.policyType,
            policyId: renewal.policyId
        }));
};

const getFollowupsRecipients = (daysRange, policyType) => {
    const today = new Date();
    const endDate = new Date(today.getTime() + (daysRange * 24 * 60 * 60 * 1000));
    
    return allFollowups
        .filter(followup => {
            const reminderDate = new Date(followup.reminderDate);
            const matchesDate = reminderDate >= today && reminderDate <= endDate;
            const matchesType = policyType === 'all' || followup.policyType.toLowerCase() === policyType;
            return matchesDate && matchesType;
        })
        .map(followup => ({
            name: followup.customerName,
            phone: followup.phone,
            email: followup.email,
            policyType: followup.policyType,
            policyId: followup.policyId
        }));
};

const getAllCustomers = () => {
    return allPolicies.map(policy => ({
        name: policy.owner,
        phone: policy.phone,
        email: policy.email,
        policyType: policy.type,
        policyId: policy.id
    }));
};

const updateMessagePreview = () => {
    const filterType = $('#bulkFilterType').val();
    
    // Update preview based on filter type
    switch(filterType) {
        case 'expiring':
            updateExpiringPreview();
            break;
        case 'renewals':
            updateRenewalPreview();
            break;
        case 'followups':
            updateFollowupPreview();
            break;
        default:
            updateDefaultPreview();
    }
};

const updateExpiringPreview = () => {
    $('#emailPreview .email-header strong').text('Subject: Your [POLICY_TYPE] policy expires on [EXPIRY_DATE]');
    $('#emailPreview .email-body').html(`
        Dear [CUSTOMER_NAME],<br><br>
        Your [POLICY_TYPE] policy expires on [EXPIRY_DATE]. 
        Please renew to maintain continuous coverage.<br><br>
        Contact: [CONTACT_PHONE]<br><br>
        Best regards,<br>
        [COMPANY_NAME] Team
    `);
    
    $('#whatsappPreview .whatsapp-message').text(
        'Hi [CUSTOMER_NAME], your [POLICY_TYPE] policy expires on [EXPIRY_DATE]. Renew now to avoid coverage lapse. Call [CONTACT_PHONE] for assistance.'
    );
    
    $('#smsPreview .sms-message').text(
        '[COMPANY_NAME]: Your [POLICY_TYPE] policy expires on [EXPIRY_DATE]. Call [CONTACT_PHONE] to renew.'
    );
};

const updateRenewalPreview = () => {
    $('#emailPreview .email-header strong').text('Subject: Renewal Reminder: [POLICY_TYPE] Policy');
    $('#emailPreview .email-body').html(`
        Dear [CUSTOMER_NAME],<br><br>
        This is a friendly reminder that your [POLICY_TYPE] policy is due for renewal.<br><br>
        <strong>Policy Details:</strong><br>
        • Policy ID: [POLICY_ID]<br>
        • Premium: ₹[PREMIUM_AMOUNT]<br><br>
        Contact: [CONTACT_PHONE]<br><br>
        Best regards,<br>
        [COMPANY_NAME] Team
    `);
    
    $('#whatsappPreview .whatsapp-message').text(
        'Hi [CUSTOMER_NAME], your [POLICY_TYPE] policy renewal is due. Premium: ₹[PREMIUM_AMOUNT]. Call [CONTACT_PHONE] to renew.'
    );
    
    $('#smsPreview .sms-message').text(
        '[COMPANY_NAME]: [POLICY_TYPE] renewal due. Premium: ₹[PREMIUM_AMOUNT]. Call [CONTACT_PHONE].'
    );
};

const updateFollowupPreview = () => {
    $('#emailPreview .email-header strong').text('Subject: Follow-up Required: [CUSTOMER_NAME]');
    $('#emailPreview .email-body').html(`
        Dear [AGENT_NAME],<br><br>
        A follow-up is required for customer [CUSTOMER_NAME] regarding their [POLICY_TYPE] policy.<br><br>
        <strong>Customer Details:</strong><br>
        • Phone: [CUSTOMER_PHONE]<br>
        • Email: [CUSTOMER_EMAIL]<br><br>
        Please contact the customer at your earliest convenience.<br><br>
        Best regards,<br>
        [COMPANY_NAME] Team
    `);
    
    $('#whatsappPreview .whatsapp-message').text(
        'Hi [AGENT_NAME], follow-up required for [CUSTOMER_NAME] ([POLICY_TYPE]). Contact: [CUSTOMER_PHONE].'
    );
    
    $('#smsPreview .sms-message').text(
        '[COMPANY_NAME]: Follow-up required for [CUSTOMER_NAME]. Call [CUSTOMER_PHONE].'
    );
};

const updateDefaultPreview = () => {
    $('#emailPreview .email-header strong').text('Subject: Important Update from [COMPANY_NAME]');
    $('#emailPreview .email-body').html(`
        Dear [CUSTOMER_NAME],<br><br>
        We hope this message finds you well.<br><br>
        [MESSAGE_CONTENT]<br><br>
        Contact: [CONTACT_PHONE]<br><br>
        Best regards,<br>
        [COMPANY_NAME] Team
    `);
    
    $('#whatsappPreview .whatsapp-message').text(
        'Hi [CUSTOMER_NAME], [MESSAGE_CONTENT]. Contact: [CONTACT_PHONE].'
    );
    
    $('#smsPreview .sms-message').text(
        '[COMPANY_NAME]: [MESSAGE_CONTENT]. Call [CONTACT_PHONE].'
    );
};

const sendBulkNotifications = () => {
    const emailEnabled = $('#bulkEmail').is(':checked');
    const whatsappEnabled = $('#bulkWhatsApp').is(':checked');
    const smsEnabled = $('#bulkSMS').is(':checked');
    
    if (!emailEnabled && !whatsappEnabled && !smsEnabled) {
        showNotification('Please select at least one notification channel', 'error');
        return;
    }
    
    const recipientsCount = parseInt($('#recipientsCount').text());
    if (recipientsCount === 0) {
        showNotification('No recipients found for the selected criteria', 'error');
        return;
    }
    
    // Show sending progress
    showNotification(`Sending notifications to ${recipientsCount} recipients...`, 'info');
    
    // Simulate sending process
    setTimeout(() => {
        let sentCount = 0;
        let channels = [];
        
        if (emailEnabled) {
            channels.push('Email');
            sentCount += Math.floor(recipientsCount * 0.95); // 95% success rate
        }
        if (whatsappEnabled) {
            channels.push('WhatsApp');
            sentCount += Math.floor(recipientsCount * 0.87); // 87% success rate
        }
        if (smsEnabled) {
            channels.push('SMS');
            sentCount += Math.floor(recipientsCount * 0.92); // 92% success rate
        }
        
        showNotification(`Successfully sent ${sentCount} notifications via ${channels.join(', ')}`, 'success');
        closeBulkNotificationModal();
    }, 2000);
};

const sendBulkEmail = (type) => {
    showNotification(`Sending bulk email notifications for ${type}...`, 'info');
    setTimeout(() => {
        showNotification('Bulk email notifications sent successfully!', 'success');
    }, 1500);
};

const sendBulkWhatsApp = (type) => {
    showNotification(`Sending bulk WhatsApp notifications for ${type}...`, 'info');
    setTimeout(() => {
        showNotification('Bulk WhatsApp notifications sent successfully!', 'success');
    }, 1500);
};

const editEmailTemplate = (templateType) => {
    showNotification(`Opening ${templateType} template editor...`, 'info');
    // In a real implementation, this would open a template editor modal
};

// Event Listeners for Notification System
$(document).ready(() => {
    // Bulk notification modal
    $('#sendBulkNotifications').click(openBulkNotificationModal);
    $('#closeBulkModal').click(closeBulkNotificationModal);
    $('#cancelBulk').click(closeBulkNotificationModal);
    
    // Filter changes
    $('#bulkFilterType, #bulkDaysRange, #bulkPolicyType').on('change', () => {
        updateRecipientsList();
        updateMessagePreview();
    });
    
    // Preview tabs
    $('.preview-tab').click(function() {
        const tab = $(this).data('tab');
        $('.preview-tab').removeClass('active');
        $(this).addClass('active');
        $('.preview-panel').removeClass('active');
        $(`#${tab}Preview`).addClass('active');
    });
    
    // Send notifications
    $('#sendBulkNotifications').click(sendBulkNotifications);
    
    // Notifications page search and filter
    $('#notificationSearch').on('input', debounce(handleNotificationSearch, 300));
    $('#notificationFilter').on('change', handleNotificationFilter);
    
    // Schedule notifications button
    $('#scheduleNotifications').click(openScheduleModal);
    $('#closeScheduleModal').click(closeScheduleModal);
    $('#cancelSchedule').click(closeScheduleModal);
    
    // View analytics button
    $('#viewAnalytics').click(() => {
        showNotification('Detailed analytics coming soon!', 'info');
    });
    
    // Manage templates button
    $('#manageTemplates').click(() => {
        showNotification('Template management coming soon!', 'info');
    });
    
    // Schedule modal interactions
    $('#scheduleType').on('change', handleScheduleTypeChange);
    $('#scheduleFilterType, #scheduleDaysRange').on('change', updateScheduleRecipients);
    $('#scheduleTemplate').on('change', updateSchedulePreview);
    
    // Schedule preview tabs
    $('.preview-tab').click(function() {
        const tab = $(this).data('tab');
        $('.preview-tab').removeClass('active');
        $(this).addClass('active');
        $('.preview-panel').removeClass('active');
        $(`#schedule${tab.charAt(0).toUpperCase() + tab.slice(1)}Preview`).addClass('active');
    });
    
    // Save schedule
    $('#saveSchedule').click(saveScheduledNotification);
});

// Notifications page functions
const initializeNotificationsPage = () => {
    updateNotificationStats();
    renderNotificationHistory();
    initializeAnalyticsCharts();
};

const updateNotificationStats = () => {
    // Calculate expiring policies (tomorrow)
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    const expiringCount = allPolicies.filter(policy => {
        const expiryDate = new Date(policy.endDate);
        return expiryDate.toDateString() === tomorrow.toDateString();
    }).length;
    
    // Calculate follow-ups due today
    const today = new Date();
    const followupsCount = allFollowups.filter(followup => {
        const reminderDate = new Date(followup.reminderDate);
        return reminderDate.toDateString() === today.toDateString();
    }).length;
    
    // Calculate renewals due this week
    const weekFromNow = new Date();
    weekFromNow.setDate(weekFromNow.getDate() + 7);
    const renewalsCount = allRenewals.filter(renewal => {
        const reminderDate = new Date(renewal.reminderDate);
        return reminderDate >= today && reminderDate <= weekFromNow;
    }).length;
    
    // Update stats
    $('#expiringCount').text(expiringCount);
    $('#followupsCount').text(followupsCount);
    $('#renewalsCount').text(renewalsCount);
    $('#sentToday').text(Math.floor(Math.random() * 200) + 100); // Mock data
};

const renderNotificationHistory = () => {
    const tbody = $('#notificationHistoryBody');
    tbody.empty();
    
    // Generate mock notification history
    const historyData = [
        {
            date: '2024-01-15 10:30',
            type: 'Policy Renewal',
            recipients: 15,
            channels: 'Email, WhatsApp',
            status: 'Completed',
            successRate: '95%'
        },
        {
            date: '2024-01-15 09:15',
            type: 'Follow-up Reminder',
            recipients: 8,
            channels: 'WhatsApp',
            status: 'Completed',
            successRate: '87%'
        },
        {
            date: '2024-01-14 16:45',
            type: 'Commission Alert',
            recipients: 12,
            channels: 'Email',
            status: 'Completed',
            successRate: '98%'
        },
        {
            date: '2024-01-14 14:20',
            type: 'Policy Expiry',
            recipients: 23,
            channels: 'Email, SMS',
            status: 'Completed',
            successRate: '92%'
        },
        {
            date: '2024-01-14 11:30',
            type: 'Custom Notification',
            recipients: 45,
            channels: 'Email, WhatsApp, SMS',
            status: 'In Progress',
            successRate: '78%'
        }
    ];
    
    historyData.forEach((item, index) => {
        const row = $(`
            <tr>
                <td>${item.date}</td>
                <td>${item.type}</td>
                <td>${item.recipients}</td>
                <td>${item.channels}</td>
                <td><span class="status-badge ${item.status.toLowerCase()}">${item.status}</span></td>
                <td>${item.successRate}</td>
                <td>
                    <div class="action-buttons">
                        <button class="action-btn view" onclick="viewNotificationHistoryDetails(${index})" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="action-btn edit" onclick="resendNotification(${index})" title="Resend">
                            <i class="fas fa-redo"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `);
        tbody.append(row);
    });
};

const handleNotificationSearch = () => {
    const searchTerm = $('#notificationSearch').val().toLowerCase().trim();
    // In a real implementation, this would filter the notification list
    showNotification(`Searching for: ${searchTerm}`, 'info');
};

const handleNotificationFilter = () => {
    const filterType = $('#notificationFilter').val();
    // In a real implementation, this would filter the notification list
    showNotification(`Filtering by: ${filterType}`, 'info');
};

const viewNotificationDetails = (type) => {
    showNotification(`Viewing details for ${type} notifications`, 'info');
    // In a real implementation, this would open a details modal
};

const viewNotificationHistory = (type) => {
    showNotification(`Viewing history for ${type} notifications`, 'info');
    // In a real implementation, this would open a history modal
};

const viewNotificationHistoryDetails = (index) => {
    showNotification(`Viewing notification history details #${index + 1}`, 'info');
    // In a real implementation, this would open a details modal
};

const resendNotification = (index) => {
    showNotification(`Resending notification #${index + 1}`, 'info');
    // In a real implementation, this would resend the notification
};

// Schedule Notification Functions
const openScheduleModal = () => {
    $('#scheduleNotificationModal').addClass('show');
    setDefaultScheduleDate();
    updateScheduleRecipients();
    updateSchedulePreview();
};

const closeScheduleModal = () => {
    $('#scheduleNotificationModal').removeClass('show');
};

const setDefaultScheduleDate = () => {
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    $('#scheduleDate').val(tomorrow.toISOString().split('T')[0]);
    $('#scheduleTime').val('10:00');
};

const handleScheduleTypeChange = () => {
    const scheduleType = $('#scheduleType').val();
    const recurringOptions = $('#recurringOptions');
    
    if (scheduleType === 'once') {
        recurringOptions.hide();
    } else {
        recurringOptions.show();
        // Pre-select weekdays for weekly schedule
        if (scheduleType === 'weekly') {
            $('input[value="monday"], input[value="tuesday"], input[value="wednesday"], input[value="thursday"], input[value="friday"]').prop('checked', true);
        }
    }
};

const updateScheduleRecipients = () => {
    const filterType = $('#scheduleFilterType').val();
    const daysRange = parseInt($('#scheduleDaysRange').val());
    
    let recipients = [];
    
    switch(filterType) {
        case 'expiring':
            recipients = getExpiringPoliciesRecipients(daysRange, 'all');
            break;
        case 'renewals':
            recipients = getRenewalsRecipients(daysRange, 'all');
            break;
        case 'followups':
            recipients = getFollowupsRecipients(daysRange, 'all');
            break;
        case 'custom':
            recipients = getAllCustomers();
            break;
    }
    
    const recipientsList = $('#scheduleRecipientsList');
    const recipientsCount = $('#scheduleRecipientsCount');
    
    recipientsList.empty();
    recipientsCount.text(recipients.length);
    
    recipients.forEach(recipient => {
        const recipientItem = $(`
            <div class="recipient-item">
                <div class="recipient-info">
                    <div class="recipient-name">${recipient.name}</div>
                    <div class="recipient-details">${recipient.phone} • ${recipient.email}</div>
                </div>
                <div class="recipient-policy">${recipient.policyType || 'N/A'}</div>
            </div>
        `);
        recipientsList.append(recipientItem);
    });
};

const updateSchedulePreview = () => {
    const template = $('#scheduleTemplate').val();
    
    switch(template) {
        case 'policy_renewal':
            updateScheduleRenewalPreview();
            break;
        case 'followup':
            updateScheduleFollowupPreview();
            break;
        case 'commission':
            updateScheduleCommissionPreview();
            break;
        default:
            updateScheduleDefaultPreview();
    }
};

const updateScheduleRenewalPreview = () => {
    $('#scheduleEmailPreview .email-header strong').text('Subject: Your [POLICY_TYPE] policy expires on [EXPIRY_DATE]');
    $('#scheduleEmailPreview .email-body').html(`
        Dear [CUSTOMER_NAME],<br><br>
        Your [POLICY_TYPE] policy expires on [EXPIRY_DATE]. 
        Please renew to maintain continuous coverage.<br><br>
        Contact: [CONTACT_PHONE]<br><br>
        Best regards,<br>
        [COMPANY_NAME] Team
    `);
    
    $('#scheduleWhatsAppPreview .whatsapp-message').text(
        'Hi [CUSTOMER_NAME], your [POLICY_TYPE] policy expires on [EXPIRY_DATE]. Renew now to avoid coverage lapse. Call [CONTACT_PHONE] for assistance.'
    );
    
    $('#scheduleSmsPreview .sms-message').text(
        '[COMPANY_NAME]: Your [POLICY_TYPE] policy expires on [EXPIRY_DATE]. Call [CONTACT_PHONE] to renew.'
    );
};

const updateScheduleFollowupPreview = () => {
    $('#scheduleEmailPreview .email-header strong').text('Subject: Follow-up Required: [CUSTOMER_NAME]');
    $('#scheduleEmailPreview .email-body').html(`
        Dear [AGENT_NAME],<br><br>
        A follow-up is required for customer [CUSTOMER_NAME] regarding their [POLICY_TYPE] policy.<br><br>
        <strong>Customer Details:</strong><br>
        • Phone: [CUSTOMER_PHONE]<br>
        • Email: [CUSTOMER_EMAIL]<br><br>
        Please contact the customer at your earliest convenience.<br><br>
        Best regards,<br>
        [COMPANY_NAME] Team
    `);
    
    $('#scheduleWhatsAppPreview .whatsapp-message').text(
        'Hi [AGENT_NAME], follow-up required for [CUSTOMER_NAME] ([POLICY_TYPE]). Contact: [CUSTOMER_PHONE].'
    );
    
    $('#scheduleSmsPreview .sms-message').text(
        '[COMPANY_NAME]: Follow-up required for [CUSTOMER_NAME]. Call [CUSTOMER_PHONE].'
    );
};

const updateScheduleCommissionPreview = () => {
    $('#scheduleEmailPreview .email-header strong').text('Subject: Commission Earned: ₹[COMMISSION_AMOUNT]');
    $('#scheduleEmailPreview .email-body').html(`
        Dear [AGENT_NAME],<br><br>
        Congratulations! You have earned a commission for successfully closing a [POLICY_TYPE] policy.<br><br>
        <strong>Commission Details:</strong><br>
        • Policy Type: [POLICY_TYPE]<br>
        • Customer: [CUSTOMER_NAME]<br>
        • Commission Amount: ₹[COMMISSION_AMOUNT]<br><br>
        The commission will be processed in the next payment cycle.<br><br>
        Keep up the great work!<br><br>
        Best regards,<br>
        [COMPANY_NAME] Team
    `);
    
    $('#scheduleWhatsAppPreview .whatsapp-message').text(
        'Hi [AGENT_NAME], congratulations! You earned ₹[COMMISSION_AMOUNT] commission for [POLICY_TYPE] policy. Keep up the great work!'
    );
    
    $('#scheduleSmsPreview .sms-message').text(
        '[COMPANY_NAME]: Congratulations! Commission earned: ₹[COMMISSION_AMOUNT] for [POLICY_TYPE] policy.'
    );
};

const updateScheduleDefaultPreview = () => {
    $('#scheduleEmailPreview .email-header strong').text('Subject: Important Update from [COMPANY_NAME]');
    $('#scheduleEmailPreview .email-body').html(`
        Dear [CUSTOMER_NAME],<br><br>
        We hope this message finds you well.<br><br>
        [MESSAGE_CONTENT]<br><br>
        Contact: [CONTACT_PHONE]<br><br>
        Best regards,<br>
        [COMPANY_NAME] Team
    `);
    
    $('#scheduleWhatsAppPreview .whatsapp-message').text(
        'Hi [CUSTOMER_NAME], [MESSAGE_CONTENT]. Contact: [CONTACT_PHONE].'
    );
    
    $('#scheduleSmsPreview .sms-message').text(
        '[COMPANY_NAME]: [MESSAGE_CONTENT]. Call [CONTACT_PHONE].'
    );
};

const saveScheduledNotification = () => {
    const scheduleDate = $('#scheduleDate').val();
    const scheduleTime = $('#scheduleTime').val();
    const scheduleType = $('#scheduleType').val();
    const recipientsCount = parseInt($('#scheduleRecipientsCount').text());
    
    if (!scheduleDate || !scheduleTime) {
        showNotification('Please select date and time', 'error');
        return;
    }
    
    if (recipientsCount === 0) {
        showNotification('No recipients selected', 'error');
        return;
    }
    
    // Show scheduling progress
    showNotification(`Scheduling notification for ${recipientsCount} recipients...`, 'info');
    
    // Simulate scheduling process
    setTimeout(() => {
        showNotification('Notification scheduled successfully!', 'success');
        closeScheduleModal();
        
        // In a real implementation, this would save to database
        // and update the scheduled notifications list
    }, 1500);
};

// Analytics Charts
const initializeAnalyticsCharts = () => {
    // Delivery Rate Chart
    const deliveryCtx = document.getElementById('deliveryRateChart');
    if (deliveryCtx) {
        new Chart(deliveryCtx, {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    data: [92, 94, 96, 95, 97, 93, 95],
                    borderColor: '#10B981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { display: false },
                    y: { display: false }
                },
                elements: { point: { radius: 0 } }
            }
        });
    }
    
    // Open Rate Chart
    const openCtx = document.getElementById('openRateChart');
    if (openCtx) {
        new Chart(openCtx, {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    data: [65, 68, 70, 67, 72, 69, 68],
                    borderColor: '#4F46E5',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { display: false },
                    y: { display: false }
                },
                elements: { point: { radius: 0 } }
            }
        });
    }
    
    // Response Rate Chart
    const responseCtx = document.getElementById('responseRateChart');
    if (responseCtx) {
        new Chart(responseCtx, {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    data: [13, 12, 14, 11, 15, 12, 11],
                    borderColor: '#EF4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { display: false },
                    y: { display: false }
                },
                elements: { point: { radius: 0 } }
            }
        });
    }
};

// Initialize all modals and their event listeners
const initializeModals = () => {
    // Agent Modal
    $('#addAgentBtn').off('click').on('click', openAgentModal);
    $('#closeAgentModal, #cancelAgent').off('click').on('click', closeAgentModal);
    $('#saveAgentBtn').off('click').on('click', handleAgentSubmit);
    
    // Policy Modal - Fix for header button
    $('#addPolicyBtn, #addPolicyFromPoliciesBtn').off('click').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        openPolicyModal();
    });
    $('#closePolicyModal, #cancelPolicy').off('click').on('click', closePolicyModal);
    $('#savePolicyBtn').off('click').on('click', handlePolicySubmit);
    
    // Followup Modal
    $('#addFollowupBtn').off('click').on('click', openFollowupModal);
    $('#closeFollowupModal, #cancelFollowup').off('click').on('click', closeFollowupModal);
    $('#saveFollowupBtn').off('click').on('click', handleFollowupSubmit);
    
    // Renewal Modal
    $('#addRenewalBtn').off('click').on('click', openRenewalModal);
    $('#closeRenewalModal, #cancelRenewal').off('click').on('click', closeRenewalModal);
    $('#renewalForm').off('submit').on('submit', handleRenewalSubmit);
    
    // Modal backdrop clicks
    $(document).off('click', '.modal').on('click', '.modal', function(e) {
        if (e.target === this) {
            $(this).removeClass('show');
        }
    });
    
    // Escape key to close modals
    $(document).off('keydown.modal').on('keydown.modal', function(e) {
        if (e.key === 'Escape') {
            $('.modal.show').removeClass('show');
        }
    });
    
    console.log('Modals initialized successfully');
};

// Function to handle form validation before submission
const prepareFormForSubmission = () => {
    const activePolicyType = $('#policyTypeSelect').val() || selectedPolicyType || 'Motor';
    
    // Disable validation on all hidden forms
    $('.policy-form').each(function() {
        const formId = $(this).attr('id');
        if (formId !== `${activePolicyType.toLowerCase()}Form`) {
            $(this).find('input[required], select[required]').prop('required', false);
        }
    });
    
    // Enable validation only on the active form
    $(`#${activePolicyType.toLowerCase()}Form input[required], #${activePolicyType.toLowerCase()}Form select[required]`).prop('required', true);
};