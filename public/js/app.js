// Global variables
let currentPage = 1;
let rowsPerPage = 10;
let currentSort = { column: 'id', direction: 'asc' };
let filteredData = [];
let allPolicies = [];
let allAgents = [];

// Vehicle number search debounce timer and toggle
let vehicleSearchTimeout = null;
let vehicleDuplicateCheckEnabled = true;
let duplicateDialogOpen = false;

// Navbar date/time update interval (prevent multiple intervals)
let dateTimeInterval = null;

// Update navbar date/time display every second
const updateNavDateTime = () => {
    const now = new Date();
    
    // Format: HH:MM AM/PM only (big hr:min)
    const hours = now.getHours();
    const minutes = now.getMinutes();
    const ampm = hours >= 12 ? 'pm' : 'am';
    const displayHours = hours % 12 || 12;
    const displayMinutes = minutes.toString().padStart(2, '0');
    
    const formattedTime = `${displayHours.toString().padStart(2, '0')}:${displayMinutes} ${ampm}`;
    
    const dateTimeElement = document.getElementById('currentDateTime');
    if (dateTimeElement) {
        // Force update - clear any existing content first
        dateTimeElement.textContent = '';
        dateTimeElement.textContent = formattedTime;
        // Also update innerHTML to ensure it's set
        dateTimeElement.innerHTML = formattedTime;
    }
};

// Initialize navbar date/time display (only once)
const initializeNavDateTime = () => {
    // Clear any existing interval first
    if (dateTimeInterval) {
        clearInterval(dateTimeInterval);
        dateTimeInterval = null;
    }
    
    // Try to find the element
    const dateTimeElement = document.getElementById('currentDateTime');
    if (dateTimeElement) {
        console.log('✅ Navbar date/time element found, initializing...');
        // Update immediately
        updateNavDateTime();
        // Update every minute (since we only show hours:minutes)
        dateTimeInterval = setInterval(() => {
            updateNavDateTime();
        }, 60000);
    } else {
        console.log('⚠️ Navbar date/time element not found yet, will retry...');
        // Retry after 500ms in case element loads later
        setTimeout(() => {
            initializeNavDateTime();
        }, 500);
    }
};

// Helper to show a small confirm dialog for duplicate policy
const showDuplicateDialog = (policy) => {
    if (duplicateDialogOpen) return;
    duplicateDialogOpen = true;
    
    // Create overlay
    const overlay = document.createElement('div');
    overlay.id = 'duplicatePolicyOverlay';
    overlay.style.cssText = `
        position: fixed; inset: 0; background: rgba(0,0,0,0.35);
        display: flex; align-items: center; justify-content: center;
        z-index: 100000;
    `;
    
    const dialog = document.createElement('div');
    dialog.style.cssText = `
        background: #fff; color: #111; width: 420px; max-width: 90vw;
        border-radius: 10px; box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        padding: 18px; font-size: 14px;
    `;
    
    const title = document.createElement('div');
    title.style.cssText = 'font-weight: 700; margin-bottom: 8px;';
    title.textContent = 'Policy already exists';
    
    const body = document.createElement('div');
    body.style.cssText = 'margin-bottom: 14px; line-height: 1.4;';
    body.innerHTML = `Customer: <strong>${policy.customer_name}</strong><br>
                      Vehicle: <strong>${policy.vehicle_number}</strong> (${policy.vehicle_type || 'Motor'})<br>
                      Period: ${policy.start_date} → ${policy.end_date}`;
    
    const actions = document.createElement('div');
    actions.style.cssText = 'display:flex; gap:10px; justify-content:flex-end;';
    
    const cancelBtn = document.createElement('button');
    cancelBtn.type = 'button';
    cancelBtn.textContent = 'Cancel';
    cancelBtn.style.cssText = 'padding:8px 12px; border:1px solid #e5e7eb; background:#fff; border-radius:6px; cursor:pointer;';
    cancelBtn.addEventListener('click', () => {
        document.body.removeChild(overlay);
        duplicateDialogOpen = false;
    });
    
    const confirmBtn = document.createElement('button');
    confirmBtn.type = 'button';
    confirmBtn.textContent = 'Renew';
    confirmBtn.style.cssText = 'padding:8px 12px; background:#059669; color:#fff; border:none; border-radius:6px; cursor:pointer;';
    confirmBtn.addEventListener('click', async () => {
        confirmBtn.disabled = true;
        confirmBtn.textContent = 'Opening...';
        vehicleDuplicateCheckEnabled = false;
        try {
            // Close the add-policy modal and open the dedicated Renew Policy flow
            closePolicyModal();
            await renewPolicy(policy.id);
        } finally {
            if (document.body.contains(overlay)) document.body.removeChild(overlay);
            duplicateDialogOpen = false;
        }
    }, { once: true });
    
    actions.appendChild(cancelBtn);
    actions.appendChild(confirmBtn);
    dialog.appendChild(title);
    dialog.appendChild(body);
    dialog.appendChild(actions);
    overlay.appendChild(dialog);
    document.body.appendChild(overlay);
};

// Vehicle number validation and duplicate check
const checkVehicleNumberDuplicate = async (vehicleNumber, inputElement) => {
    // Clear previous timeout
    if (vehicleSearchTimeout) {
        clearTimeout(vehicleSearchTimeout);
        vehicleSearchTimeout = null;
    }
    
    // Remove any existing inline message (legacy)
    const existingMessage = inputElement.parentElement.querySelector('.vehicle-validation-message');
    if (existingMessage) existingMessage.remove();
    
    // Don't search if disabled or vehicle number is too short
    if (!vehicleDuplicateCheckEnabled || !vehicleNumber || vehicleNumber.length < 4) {
        return;
    }
    
    vehicleSearchTimeout = setTimeout(async () => {
        if (!vehicleDuplicateCheckEnabled) return;
        try {
            const result = await searchPolicyByVehicleNumber(vehicleNumber);
            if (result.found && result.policies.length > 0) {
                const policy = result.policies[0];
                showDuplicateDialog(policy);
            }
        } catch (error) {
            console.error('Error checking vehicle number:', error);
        }
    }, 500);
};

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
let policiesAsRenewals = [];

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

// Refresh CSRF token
const refreshCSRFToken = async () => {
    try {
        const response = await fetch('/sanctum/csrf-cookie', {
            credentials: 'same-origin'
        });
        if (response.ok) {
            console.log('✅ CSRF token refreshed');
            return true;
        }
    } catch (error) {
        console.error('❌ Failed to refresh CSRF token:', error);
    }
    return false;
};

const apiCall = async (endpoint, options = {}, retryCount = 0) => {
    try {
        // Get CSRF token from meta tag
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        if (!csrfToken) {
            console.error('❌ CSRF token not found in meta tag');
            showNotification('Security token missing. Please refresh the page.', 'error');
            throw new Error('CSRF token not found');
        }
        
        // Check if we're sending FormData (for file uploads)
        const isFormData = options.body instanceof FormData;
        
        const defaultOptions = {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        };

        // Only set Content-Type for JSON, not for FormData
        if (!isFormData) {
            defaultOptions.headers['Content-Type'] = 'application/json';
        }

        // Add CSRF token (try both header names for compatibility)
            defaultOptions.headers['X-CSRF-TOKEN'] = csrfToken;
            defaultOptions.headers['X-XSRF-TOKEN'] = csrfToken;

        const response = await fetch(endpoint, {
            ...defaultOptions,
            ...options,
            credentials: 'same-origin',
            headers: {
                ...defaultOptions.headers,
                ...options.headers
            }
        });

        // Handle CSRF token mismatch (419 error)
        if (response.status === 419) {
            console.error('❌ CSRF Token Mismatch - Endpoint:', endpoint);
            
            // For document operations, just show error without reload
            if (endpoint.includes('/document/')) {
                console.warn('⚠️ Document operation failed with 419 - Not reloading page');
                showNotification('Authentication failed. Please refresh the page and try again.', 'error');
                const error = new Error('HTTP error! status: 419');
                error.response = { status: 419, data: { message: 'CSRF token mismatch' } };
                throw error;
            }
            
            // For other operations, reload page
            showNotification('Session expired. Reloading page...', 'error');
            await refreshCSRFToken();
            setTimeout(() => {
                window.location.reload();
            }, 1000);
            
            const error = new Error('Session expired - reloading page');
            error.response = { status: 419, data: { message: 'Session expired' } };
            throw error;
        }

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
        console.log('🔄 fetchDashboardStats: Making API call to /api/dashboard/stats');
        // Add cache-busting parameter to force fresh data
        const timestamp = new Date().getTime();
        const url = `/api/dashboard/stats?t=${timestamp}`;
        console.log('🔄 fetchDashboardStats: URL:', url);

        const data = await apiCall(url);
        console.log('✅ fetchDashboardStats: Success, received data:', data);
        return data;
    } catch (error) {
        console.error('❌ fetchDashboardStats: Failed to fetch dashboard stats:', error);
        console.error('❌ fetchDashboardStats: Error details:', error.message);
        return null;
    }
};

const fetchRecentPolicies = async () => {
    try {
        // Add cache-busting parameter to force fresh data
        const timestamp = new Date().getTime();
        const data = await apiCall(`/api/dashboard/recent-policies?t=${timestamp}`);
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
const fetchPolicies = async (policyType = 'All') => {
    try {
        const url = `/api/policies${policyType !== 'All' ? '?policy_type=' + encodeURIComponent(policyType) : ''}`;
        console.log('📋 fetchPolicies: Making API call to:', url);
        console.log('📋 fetchPolicies: Policy type filter:', policyType);

        // Try direct fetch call for debugging
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });

        console.log('📋 fetchPolicies: Response status:', response.status);
        console.log('📋 fetchPolicies: Response ok:', response.ok);

        if (!response.ok) {
            console.error('📋 fetchPolicies: API call failed with status:', response.status);
            const errorText = await response.text();
            console.error('📋 fetchPolicies: Error response:', errorText);
            showNotification('Failed to load policies data: ' + response.status, 'error');
            return { policies: [], stats: null };
        }

        const data = await response.json();
        console.log('📋 fetchPolicies: Success, received data:', data);
        console.log('📋 fetchPolicies: Policies count:', data.policies ? data.policies.length : 0);
        console.log('📋 fetchPolicies: Stats:', data.stats);
        
        if (!data.policies || !Array.isArray(data.policies)) {
            console.error('📋 fetchPolicies: Invalid data format:', data);
            showNotification('Invalid policies data format received', 'error');
            return { policies: [], stats: null };
        }
        
        return { policies: data.policies, stats: data.stats };
    } catch (error) {
        console.error('❌ fetchPolicies: Failed to fetch policies:', error);
        console.error('❌ fetchPolicies: Error details:', error.message);
        showNotification('Failed to load policies: ' + error.message, 'error');
        return { policies: [], stats: null };
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

const createPolicyWithFiles = async (formData) => {
    try {
        // Add CSRF token to FormData as backup
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (csrfToken) {
            formData.append('_token', csrfToken);
        }
        
        console.log('Creating policy with files - FormData contents:');
        for (let pair of formData.entries()) {
            if (pair[1] instanceof File) {
                console.log(pair[0] + ':', pair[1].name, pair[1].size + ' bytes');
            } else {
                console.log(pair[0] + ':', pair[1]);
            }
        }
        
        const data = await apiCall('/policies', {
            method: 'POST',
            body: formData,
            headers: {} // Let browser set Content-Type for FormData
        });
        return data;
    } catch (error) {
        console.error('Failed to create policy with files:', error);
        console.error('Error details:', error.response);
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

const updatePolicyWithFiles = async (id, formData) => {
    try {
        const data = await apiCall(`/policies/${id}`, {
            // Use POST with method override for file uploads in Laravel
            method: 'POST',
            body: formData,
            headers: {} // Let browser set Content-Type for FormData
        });
        return data;
    } catch (error) {
        console.error('Failed to update policy with files:', error);
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
        console.log('👥 fetchAgents: Making API call to /api/agents');
        const data = await apiCall('/api/agents');
        console.log('👥 fetchAgents: Success, received data:', data);
        console.log('👥 fetchAgents: Agents count:', data.agents ? data.agents.length : 0);
        return data.agents || [];
    } catch (error) {
        console.error('❌ fetchAgents: Failed to fetch agents:', error);
        console.error('❌ fetchAgents: Error details:', error.message);
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

// Vehicle number search API call
const searchPolicyByVehicleNumber = async (vehicleNumber) => {
    try {
        const data = await apiCall(`/api/policies/search/vehicle/${encodeURIComponent(vehicleNumber)}`);
        return data;
    } catch (error) {
        console.error('Failed to search policy by vehicle number:', error);
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
    const t = Date.now();
    const data = await apiCall(`/api/followups?t=${t}`);
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
    
    const vehicleTypes = ['Auto (G)', 'Auto', 'Bus', 'Car (Taxi)', 'Car', 'E-Auto', 'E-Car', 'HGV', 'JCB', 'LCV', 'Others', 'Tractor', 'Trailer', '2-Wheeler', 'Van/Jeep'];
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
console.log('UPDATED JAVASCRIPT FILE LOADED - app.js with vehicle numbers AND POLICY HISTORY FEATURE');
$(document).ready(function() {
    // Initialize theme immediately to prevent flash
    initializeTheme();
    
    // Initialize navbar date/time display immediately when DOM is ready
    initializeNavDateTime();
    
    // Show loading state
    showLoadingState();
    
    // Initialize components with real data
    initializeApplication();
    
    // Auto-initialize View Policy page if on that page
    initViewPolicyPageIfNeeded();
});

// Main initialization function
const initializeApplication = async () => {
    try {
        console.log('🚀 Starting application initialization...');
        console.log('🚀 Current path:', window.location.pathname);
        console.log('🚀 Dashboard element exists:', $('#dashboard').length);
        console.log('🚀 Policies element exists:', $('#policies').length);
        console.log('🚀 jQuery available:', typeof $ !== 'undefined');
        console.log('🚀 jQuery version:', $.fn ? $.fn.jquery : 'unknown');
        
        // Check CSRF token on initialization
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!csrfToken) {
            console.error('❌ CSRF token not found on page load!');
            showNotification('Security error: Please refresh the page', 'error');
        } else {
            console.log('✅ CSRF token found:', csrfToken.substring(0, 20) + '...');
        }
        
        // Initialize charts first so they're ready for data
        initializeCharts();
        
        // Load all essential data to ensure pages work properly
        const currentPath = window.location && window.location.pathname ? window.location.pathname : '';
        console.log('🔍 Current path detected:', currentPath);
        const loads = [];
        
        // Skip dashboard data load on follow-ups page (it has its own initialization)
        if (currentPath !== '/followups') {
            // Always load dashboard data (needed for stats)
            console.log('🚀 Loading dashboard data...');
            loads.push(loadDashboardData());
        } else {
            console.log('⏭️ Skipping dashboard data load on Follow Ups page (has its own initialization)');
        }
        
        // Always load policies data (needed for policies page and other features)
        console.log('🚀 Loading policies data...');
        loads.push(loadPoliciesData());
        
        // Load other data based on current page
        if (currentPath === '/agents') {
            console.log('🚀 Loading agents data...');
            loads.push(loadAgentsData());
        }
        if (currentPath === '/renewals') {
            loads.push(loadRenewalsData());
        }
        // Skip old followups data load - new page has its own API
        if (currentPath === '/followups') {
            console.log('⏭️ Skipping old followups data load - new Follow Ups page has its own dashboard API');
        }
        await Promise.allSettled(loads);
        
        console.log('📊 Data loaded, ensuring charts have data...');
        
        // Ensure charts get the loaded data
        if (window.latestChartData && window.barChart) {
            console.log('🔄 Applying data to initialized charts');
            updateDashboardCharts(window.latestChartData, window.latestPolicyTypes || {});
        } else if (window.barChart) {
            console.log('📊 Chart initialized but no data yet, triggering fresh fetch...');
            // Force a fresh data fetch for the default period
            setTimeout(() => {
                const defaultPeriod = $('#chartPeriod').val() || 'fy';
                console.log('🔄 Fetching data for default period:', defaultPeriod);
                handleChartPeriodChange();
            }, 300);
        }
        
    // Build renewals VM only when needed
    if (currentPath === '/policies' || currentPath === '/renewals') {
        await buildRenewalsFromPoliciesAsync();
    }

    // Initialize other components
        initializeTable();
        initializeAgents();
        initializePoliciesPage();
        // Skip legacy renewals initializer on the Renewals page; Blade v2 script owns it
        if (currentPath !== '/renewals' || !window.RENEWALS_V2) {
            initializeRenewalsPage();
        }
        // Skip legacy followups initializer on the Follow Ups page; new blade v2 script owns it
        if (currentPath !== '/followups') {
            initializeFollowupsPage();
        } else {
            console.log('⏭️ Skipping legacy followups initializer - new Follow Ups page has its own initialization');
        }
        // Skip legacy reports initializer on the Reports page; Blade v2 script owns it
        if (currentPath !== '/reports' || !window.REPORTS_V2) {
            initializeReportsPage();
        }
        initializeEventListeners();
        initializeModals(); // Initialize modals
        
        // Set default date for policy form using local timezone (IST)
        const today = new Date();
        const oneYearLater = new Date(today);
        oneYearLater.setFullYear(today.getFullYear() + 1);
        oneYearLater.setDate(oneYearLater.getDate() - 1);
        
        // Use global formatLocalDate function (defined in utility functions)
        $('#startDate').val(formatLocalDate(today));
        $('#endDate').val(formatLocalDate(oneYearLater));
        
        // Ensure navbar date/time is initialized (in case it wasn't initialized earlier)
        initializeNavDateTime();
        
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
        console.log('🎯 loadDashboardData called - Dashboard element:', $('#dashboard').length);
        console.log('🎯 Dashboard has active class:', $('#dashboard').hasClass('active'));
        
        // Show loading indicators for cards
        $('.card-value').text('Loading...');
        
        // Load all data in parallel for better performance
        const [stats, recentPolicies, expiringPolicies] = await Promise.allSettled([
            fetchDashboardStats(),
            fetchRecentPolicies(),
            fetchExpiringPolicies()
        ]);

        // Handle stats
        if (stats.status === 'fulfilled' && stats.value) {
            console.log('🎯 Dashboard stats loaded successfully:', stats.value);
            updateDashboardStats(stats.value);
        } else {
            console.warn('❌ Failed to load dashboard stats:', stats.reason);
            // Reset loading indicators
            $('.card-value').text('0');
        }

        // Handle recent policies
        if (recentPolicies.status === 'fulfilled' && recentPolicies.value?.length > 0) {
            updateRecentPoliciesTable(recentPolicies.value);
            // If we're on dashboard, also update the main table to use recent policies
            if ($('#dashboard').hasClass('active')) {
                console.log('Dashboard: Using recent policies data for main table');
            }
        } else {
            console.warn('Failed to load recent policies:', recentPolicies.reason);
        }

        // Handle expiring policies
        if (expiringPolicies.status === 'fulfilled' && expiringPolicies.value?.length > 0) {
            updateExpiringPoliciesList(expiringPolicies.value);
        } else {
            console.warn('Failed to load expiring policies:', expiringPolicies.reason);
        }
        
    } catch (error) {
        console.error('Failed to load dashboard data:', error);
        // Reset loading indicators
        $('.card-value').text('Error');
    }
};
// Load policies data
const loadPoliciesData = async (policyType = 'All') => {
    try {
        console.log('📋 loadPoliciesData called - Policies element:', $('#policies').length);
        console.log('📋 Policies has active class:', $('#policies').hasClass('active'));
        console.log('📋 Loading policies with type filter:', policyType);
        
        const result = await fetchPolicies(policyType);
        allPolicies = result.policies || [];
        const stats = result.stats;
        
        console.log('📋 Policies loaded:', allPolicies.length);
        console.log('📋 Stats received:', stats);
        
        if (!allPolicies || allPolicies.length === 0) {
            console.warn('📋 No policies data received');
            allPolicies = [];
            filteredData = [];
        } else {
            filteredData = [...allPolicies];
        }
        
        // Update policies stats with backend data if available
        if (stats) {
            updatePoliciesStatsWithData(stats);
        } else {
            updatePoliciesStats();
        }
        
        // Initialize policies page if we're on the policies page
        if ($('#policies').hasClass('active')) {
            initializePoliciesPage();
        }
        
        console.log('📋 Policies data loaded successfully');
    } catch (error) {
        console.error('Failed to load policies data:', error);
        showNotification('Failed to load policies data: ' + error.message, 'error');
        allPolicies = [];
        filteredData = [];
    }
};

// Load agents data
const loadAgentsData = async () => {
    try {
        console.log('👥 loadAgentsData called');
        allAgents = await fetchAgents();
        console.log('👥 Agents loaded:', allAgents.length);
        console.log('👥 Agents data:', allAgents);
    } catch (error) {
        console.error('❌ Failed to load agents data:', error);
        allAgents = [];
    }
};

// Load renewals data
const loadRenewalsData = async () => {
    try {
        // Skip loading old renewals data if we're on the renewals page to prevent conflicts
        const currentPath = window.location.pathname;
        if (currentPath === '/renewals') {
            console.log('🔄 Skipping old renewals data load on renewals page - using policy-based logic instead');
            allRenewals = []; // Keep empty to prevent conflicts
            return;
        }

        // Keep API call for backward compatibility but we'll derive the visible list from policies
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
    console.log('🔧 updateDashboardStats called with:', stats);
    console.log('🔧 updateDashboardStats: stats.stats exists:', !!stats.stats);

    if (stats && stats.stats) {
        console.log('🔧 updateDashboardStats: Processing stats data:', stats.stats);
        const fmtINR = (v) => '₹' + Number(v || 0).toLocaleString('en-IN');

        // Use CURRENT MONTH counts for the 4 dashboard cards
        const totalPremium = stats.stats.monthlyPremium || 0;
        const totalPolicies = stats.stats.monthlyPolicies || 0;
        const totalRevenue = stats.stats.monthlyRevenue || 0;
        const totalRenewals = stats.stats.monthlyRenewals || 0;

        console.log('🔧 updateDashboardStats: Calculated values:', {
            totalPremium, totalPolicies, totalRevenue, totalRenewals
        });
        
        console.log('📊 Setting dashboard values:', {
            totalPremium, totalPolicies, totalRevenue, totalRenewals
        });
        
        console.log('📝 Updating DOM elements...');
        $('#monthlyPremium').text(fmtINR(totalPremium));
        $('#yearlyPremium').text(fmtINR(stats.stats.yearlyPremium) + ' (FY)');
        $('#monthlyPolicies').text(totalPolicies);
        $('#yearlyPolicies').text(stats.stats.yearlyPolicies || 0 + ' (FY)');

        // Use total renewals for main card, but keep monthly breakdown for detailed view
        const renewed = stats.stats.monthlyRenewed || 0;
        $('#monthlyRenewals').text(totalRenewals);
        $('#pendingRenewals').text(stats.stats.pendingRenewals || 0 + ' Pending');
        $('#monthlyRevenue').text(fmtINR(totalRevenue));
        $('#yearlyRevenue').text(fmtINR(stats.stats.yearlyRevenue) + ' (FY)');

        console.log('📝 DOM elements updated, checking current values:');
        console.log('  monthlyPremium:', $('#monthlyPremium').text());
        console.log('  monthlyPolicies:', $('#monthlyPolicies').text());
        console.log('  monthlyRenewals:', $('#monthlyRenewals').text());
        console.log('  monthlyRevenue:', $('#monthlyRevenue').text());

        console.log('✅ Dashboard stats updated successfully');
    }
    
    if (stats.chartData) {
        console.log('📊 Received chart data from API:', stats.chartData);
        // Cache the latest chart data in case charts are not yet initialized
        window.latestChartData = stats.chartData;
        window.latestPolicyTypes = stats.policyTypes || {};
        updateDashboardCharts(stats.chartData, stats.policyTypes);
    } else {
        console.warn('⚠️ No chart data received from API');
    }
};

// Update recent policies table
const updateRecentPoliciesTable = (policies) => {
    // Store the recent policies data for search and sort functionality
    window.recentPoliciesData = policies || [];
    
    // Clear any previous sort when loading fresh data (unless user clicked a column)
    // This ensures default sort by most recent start date
    if (!window.currentSort || !window.currentSort.userClicked) {
        window.currentSort = { column: null, direction: 'desc', userClicked: false };
    }
    
    // Apply search filter if there's a search term
    const searchTerm = $('#policySearch').val().toLowerCase().trim();
    let filteredPolicies = [...window.recentPoliciesData];
    
    if (searchTerm !== '') {
        console.log('🔍 Applying search filter:', searchTerm);
        console.log('🔍 Sample policy data:', window.recentPoliciesData[0]);
        filteredPolicies = window.recentPoliciesData.filter(policy => {
            // Search across ALL possible fields in recent policies
            const searchableFields = [
                // Basic info
                policy.customerName || '',
                policy.phone || '',
                policy.customerEmail || policy.email || '',
                policy.policyType || '',
                policy.vehicleNumber || '',
                policy.vehicleType || policy.vehicle_type || '',
                
                // Insurance details
                policy.companyName || policy.company || '',
                policy.insuranceType || policy.planType || '',
                policy.startDate || '',
                policy.endDate || '',
                policy.status || '',
                
                // Financial details
                (policy.premium || 0).toString(),
                (policy.payout || 0).toString(),
                (policy.customerPaidAmount || 0).toString(),
                (policy.revenue || 0).toString(),
                
                // Business details
                policy.businessType || policy.business_type || '',
                policy.agentName || policy.agent_name || '',
                
                // ID
                (policy.id || '').toString()
            ];
            
            // Create a combined search string for easier matching
            const combinedSearchString = searchableFields
                .map(field => String(field || '').toLowerCase())
                .join(' ');
            
            // Check if the combined string contains the search term
            const matches = combinedSearchString.includes(searchTerm);
            
            // Debug logging for vehicle type searches
            if (searchTerm.toLowerCase().includes('auto') || searchTerm.toLowerCase().includes('car')) {
                const vehicleType = (policy.vehicleType || policy.vehicle_type || '').toLowerCase();
                if (vehicleType && vehicleType.includes(searchTerm.toLowerCase())) {
                    console.log('✅ Vehicle type match found:', {
                        vehicleType: policy.vehicleType || policy.vehicle_type,
                        searchTerm: searchTerm,
                        customerName: policy.customerName
                    });
                }
            }
            
            return matches;
        });
        console.log('🔍 Filtered policies count:', filteredPolicies.length);
        console.log('🔍 Sample filtered policy:', filteredPolicies[0]);
    }
    
    // Apply sorting if there's a current sort
    if (window.currentSort && window.currentSort.column) {
        console.log('Applying sort:', window.currentSort.column, window.currentSort.direction);
        filteredPolicies.sort((a, b) => {
            const property = getColumnProperty(window.currentSort.column);
            let aVal = a[property] || '';
            let bVal = b[property] || '';
            
            // Special handling for date columns
            if (property === 'startDate' || property === 'endDate') {
                const parseDate = (dateStr) => {
                    if (!dateStr) return new Date(0);
                    // Handle YYYY-MM-DD format
                    if (dateStr.match(/^\d{4}-\d{2}-\d{2}$/)) {
                        return new Date(dateStr);
                    }
                    // Handle DD-MM-YYYY format
                    if (dateStr.match(/^\d{2}-\d{2}-\d{4}$/)) {
                        const parts = dateStr.split('-');
                        return new Date(parts[2], parts[1] - 1, parts[0]);
                    }
                    return new Date(dateStr);
                };
                
                const dateA = parseDate(aVal);
                const dateB = parseDate(bVal);
                return window.currentSort.direction === 'asc' ? dateA - dateB : dateB - dateA;
            }
            // Handle different data types properly
            else if (typeof aVal === 'number' && typeof bVal === 'number') {
                // Numeric sorting
                if (window.currentSort.direction === 'asc') {
                    return aVal - bVal;
                } else {
                    return bVal - aVal;
                }
            } else if (typeof aVal === 'string' && typeof bVal === 'string') {
                // String sorting (case-insensitive)
                aVal = aVal.toLowerCase();
                bVal = bVal.toLowerCase();
                if (aVal < bVal) return window.currentSort.direction === 'asc' ? -1 : 1;
                if (aVal > bVal) return window.currentSort.direction === 'asc' ? 1 : -1;
                return 0;
            } else {
                // Mixed types - convert to string for comparison
                aVal = String(aVal).toLowerCase();
                bVal = String(bVal).toLowerCase();
                if (aVal < bVal) return window.currentSort.direction === 'asc' ? -1 : 1;
                if (aVal > bVal) return window.currentSort.direction === 'asc' ? 1 : -1;
                return 0;
            }
        });
    }
    
    // Default sort by most recent start date if no explicit sort chosen
    if (!window.currentSort || !window.currentSort.column) {
        filteredPolicies.sort((a, b) => {
            // Parse dates properly (format: YYYY-MM-DD or DD-MM-YYYY)
            const parseDate = (dateStr) => {
                if (!dateStr) return new Date(0);
                // Handle YYYY-MM-DD format
                if (dateStr.match(/^\d{4}-\d{2}-\d{2}$/)) {
                    return new Date(dateStr);
                }
                // Handle DD-MM-YYYY format
                if (dateStr.match(/^\d{2}-\d{2}-\d{4}$/)) {
                    const parts = dateStr.split('-');
                    return new Date(parts[2], parts[1] - 1, parts[0]);
                }
                return new Date(dateStr);
            };
            
            const dateA = parseDate(a.startDate);
            const dateB = parseDate(b.startDate);
            return dateB - dateA; // Most recent first
        });
    }

    // Sync with main table data + pagination so rowsPerPage works correctly
    allPolicies = [...filteredPolicies];
    filteredData = [...filteredPolicies];
    currentPage = 1;
    renderTable();
    updatePagination();
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
                        <h4>#${policy.id.toString().padStart(3, '0')}</h4>
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
    console.log('🔍 Updating dashboard charts with data:', chartData, policyTypes);
    
    // Validate chart data
    if (!chartData || !Array.isArray(chartData)) {
        console.warn('⚠️ Invalid chart data received:', chartData);
        return;
    }
    
    // Prevent double updates by checking if data has changed
    const dataHash = JSON.stringify(chartData);
    if (window.lastChartDataHash === dataHash) {
        console.log('📊 Chart data unchanged, skipping update');
        return;
    }
    window.lastChartDataHash = dataHash;
    
    // Update bar chart with real data
    if (window.barChart && window.barChart.data && window.barChart.data.datasets) {
        console.log('📈 Updating bar chart with', chartData.length, 'data points');
        
        const labels = chartData.map(item => item.month || 'Unknown');
        const premiumData = chartData.map(item => parseFloat(item.premium || 0));
        const revenueData = chartData.map(item => parseFloat(item.revenue || 0));
        const policiesData = chartData.map(item => parseInt(item.policies || 0));
        
        console.log('📊 Processed chart data:', { 
            labels, 
            premiumData, 
            revenueData, 
            policiesData,
            hasData: premiumData.some(val => val > 0) || revenueData.some(val => val > 0) || policiesData.some(val => val > 0)
        });
        
        // Update chart data
        window.barChart.data.labels = labels;
        window.barChart.data.datasets[0].data = premiumData;
        window.barChart.data.datasets[1].data = revenueData;
        window.barChart.data.datasets[2].data = policiesData;
        
        // Force chart update with animation
        window.barChart.update('active');
        console.log('✅ Bar chart updated successfully');
        
        // Show notification if no data
        const hasAnyData = premiumData.some(val => val > 0) || revenueData.some(val => val > 0) || policiesData.some(val => val > 0);
        if (!hasAnyData) {
            console.log('📊 No data available for current period');
            showNotification('No data available for the selected time period', 'info');
        }
    } else {
        console.error('❌ Bar chart not initialized:', {
            hasBarChart: !!window.barChart,
            hasBarChartData: !!(window.barChart && window.barChart.data),
            hasDatasets: !!(window.barChart && window.barChart.data && window.barChart.data.datasets)
        });
        
        // Cache the data for later use
        window.latestChartData = chartData;
        window.latestPolicyTypes = policyTypes;
        
        // Try to reinitialize chart
        setTimeout(() => {
            console.log('🔄 Attempting to reinitialize charts...');
            initializeCharts();
        }, 1000);
    }
    
    // Update pie chart with real data
    if (window.pieChart && window.pieChart.data && policyTypes && Object.keys(policyTypes).length > 0) {
        console.log('🥧 Updating pie chart');
        const labels = Object.keys(policyTypes);
        const data = Object.values(policyTypes);
        
        window.pieChart.data.labels = labels;
        window.pieChart.data.datasets[0].data = data;
        window.pieChart.update('active');
        console.log('✅ Pie chart updated successfully');
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
    console.log('🚀 Initializing charts...');
    console.log('Chart.js available:', typeof Chart !== 'undefined');
    console.log('Current URL:', window.location.href);
    console.log('Current pathname:', window.location.pathname);
    
    // Check if we're on dashboard page OR if this is initial load
    const isDashboardActive = $('#dashboard').hasClass('active');
    const isDashboardPage = window.location.pathname === '/dashboard' || window.location.pathname === '/';
    
    console.log('Dashboard active:', isDashboardActive);
    console.log('Dashboard page:', isDashboardPage);
    
    if (!isDashboardActive && !isDashboardPage) {
        console.log('⏭️ Not on dashboard page, skipping chart initialization');
        return;
    }
    
    if (!isDashboardActive) {
        console.log('📊 Dashboard not active yet but we\'re on dashboard page, proceeding...');
    }
    
    // Wait for DOM to be ready
    setTimeout(() => {
        const barCtx = document.getElementById('barChart');
        console.log('Canvas element found:', barCtx);
        if (!barCtx) {
            console.error('❌ Bar chart canvas not found');
            console.log('Available elements with "chart" in ID:', document.querySelectorAll('[id*="chart"]'));
            return;
        }
        
        console.log('📊 Found bar chart canvas, proceeding with initialization');
        
        // Destroy existing charts if they exist
        if (window.barChart && typeof window.barChart.destroy === 'function') {
            console.log('🗑️ Destroying existing bar chart');
            window.barChart.destroy();
        }
        if (window.pieChart && typeof window.pieChart.destroy === 'function') {
            console.log('🗑️ Destroying existing pie chart');
            window.pieChart.destroy();
        }
        
        // Bar Chart
        console.log('Creating Chart instance...');
        console.log('Chart constructor available:', typeof Chart);
        console.log('Canvas context:', barCtx);
        
        const barChart = new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [
                    {
                        label: 'Premium (₹)',
                        data: [],
                        backgroundColor: 'rgba(79, 70, 229, 0.8)',
                        borderColor: 'rgba(79, 70, 229, 1)',
                        borderWidth: 1,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Revenue (₹)',
                        data: [],
                        backgroundColor: 'rgba(16, 185, 129, 0.8)',
                        borderColor: 'rgba(16, 185, 129, 1)',
                        borderWidth: 1,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Policies (Count)',
                        data: [],
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
                animation: {
                    duration: 0  // Disable initial animation
                },
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

        // Pie chart removed from layout; no initialization

        // Store chart references
        window.barChart = barChart;
        console.log('✅ Bar chart initialized successfully');
        console.log('Chart instance:', barChart);
        console.log('Chart data:', barChart.data);
        console.log('Chart options:', barChart.options);
        
        // Wait a bit more to ensure chart is fully initialized
        setTimeout(() => {
            // If we already fetched data before charts were ready, apply it now
            if (window.latestChartData) {
                console.log('📊 Applying cached chart data');
                updateDashboardCharts(window.latestChartData, window.latestPolicyTypes || {});
            } else {
                console.log('⏳ No cached chart data, fetching fresh data...');
                // Fetch data immediately if not cached
                setTimeout(() => {
                    fetch('/api/dashboard/stats')
                        .then(response => response.json())
                        .then(data => {
                            console.log('📊 Fresh chart data fetched for initialization:', data);
                            if (data.chartData) {
                                window.latestChartData = data.chartData;
                                window.latestPolicyTypes = data.policyTypes || {};
                                updateDashboardCharts(data.chartData, data.policyTypes);
                            }
                        })
                        .catch(error => {
                            console.error('❌ Failed to fetch initial chart data:', error);
                        });
                }, 200);
            }
        }, 500);
    }, 100);
};

// Debug and utility functions for charts
window.debugChartStatus = () => {
    console.log('=== 📊 CHART DEBUG STATUS ===');
    console.log('Bar Chart exists:', !!window.barChart);
    console.log('Bar Chart data exists:', !!(window.barChart && window.barChart.data));
    console.log('Dashboard element active:', $('#dashboard').hasClass('active'));
    console.log('Chart canvas element:', document.getElementById('barChart'));
    console.log('Latest Chart Data:', window.latestChartData);
    console.log('Latest Policy Types:', window.latestPolicyTypes);
    
    // Test API endpoint
    fetch('/api/dashboard/stats')
        .then(response => response.json())
        .then(data => {
            console.log('📡 API Response:', data);
            console.log('📊 Chart Data from API:', data.chartData);
            if (data.chartData && data.chartData.length > 0) {
                console.log('✅ API has chart data');
            } else {
                console.log('❌ API has no chart data');
            }
        })
        .catch(error => {
            console.error('❌ API Error:', error);
        });
};
window.forceChartRefresh = () => {
    console.log('🔄 Forcing chart refresh...');
    
    // Destroy existing chart
    if (window.barChart) {
        window.barChart.destroy();
        window.barChart = null;
    }
    
    // Clear cache
    window.latestChartData = null;
    window.latestPolicyTypes = null;
    window.lastChartDataHash = null;
    
    // Re-initialize
    setTimeout(() => {
        initializeCharts();
        
        // Re-fetch data
        setTimeout(() => {
            fetch('/api/dashboard/stats')
                .then(response => response.json())
                .then(data => {
                    console.log('📊 Fresh data fetched:', data);
                    if (data.chartData) {
                        updateDashboardCharts(data.chartData, data.policyTypes);
                    }
                })
                .catch(error => {
                    console.error('❌ Failed to fetch fresh data:', error);
                });
        }, 500);
    }, 100);
};

// Initialize data table
const initializeTable = () => {
    // Check if we're on the dashboard page
    if ($('#dashboard').hasClass('active')) {
        // On dashboard, use recent policies data if available
        if (window.recentPoliciesData && window.recentPoliciesData.length > 0) {
            updateRecentPoliciesTable(window.recentPoliciesData);
            return;
        }
    }
    
    // For other pages or if no recent policies data, use main table
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
        const startDate = policy.startDate && policy.startDate.trim() !== '' ? policy.startDate : null;
        const premium = policy.premium || 0;
        const status = policy.status || 'Active';
        
        // Extract vehicle information based on policy type
        let vehicleNumber = 'N/A';
        let vehicleType = 'N/A';
        
        if (policyType === 'Motor') {
            vehicleNumber = policy.vehicleNumber || policy.vehicle_number || 'N/A';
            vehicleType = policy.vehicleType || policy.vehicle_type || 'N/A';
        } else if (policyType === 'Health') {
            vehicleNumber = 'Health Policy';
            vehicleType = policy.insuranceType || 'Health Insurance';
        } else if (policyType === 'Life') {
            vehicleNumber = 'Life Policy';
            vehicleType = policy.insuranceType || 'Life Insurance';
        }
        
        // Format vehicle/details info based on policy type
        let vehicleDetails = '';
        if (policyType === 'Motor') {
            vehicleDetails = policy.vehicleType || policy.vehicle_type || 'N/A';
            if (policy.vehicleNumber || policy.vehicle_number) {
                vehicleDetails += `<br><small style="color: #666;">${policy.vehicleNumber || policy.vehicle_number}</small>`;
            }
        } else if (policyType === 'Health') {
            vehicleDetails = policy.insuranceType || 'Health Insurance';
            if (policy.sumInsured) {
                vehicleDetails += `<br><small style="color: #666;">Sum: ₹${policy.sumInsured.toLocaleString()}</small>`;
            }
        } else if (policyType === 'Life') {
            vehicleDetails = policy.insuranceType || 'Life Insurance';
            if (policy.sumAssured) {
                vehicleDetails += `<br><small style="color: #666;">Sum: ₹${policy.sumAssured.toLocaleString()}</small>`;
            }
        }

        // Ensure we extract string values, not HTML elements
        const safeVehicleNumber = typeof vehicleNumber === 'string' ? vehicleNumber : (vehicleNumber?.value || 'N/A');
        const safeVehicleType = typeof vehicleType === 'string' ? vehicleType : (vehicleType?.value || 'N/A');
        
        // Check if we're rendering the dashboard recent policies table
        const isDashboardTable = window.recentPoliciesData && window.recentPoliciesData.length > 0 && $('#dashboard').hasClass('active');
        
        // Build action buttons HTML based on context
        let actionButtonsHTML = '';
        if (isDashboardTable) {
            // Dashboard: Only show View button
            actionButtonsHTML = `
                <div class="action-buttons">
                    <button class="action-btn view" data-policy-id="${policy.id}" title="View Details">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            `;
        } else {
            // Other pages: Show all buttons
            actionButtonsHTML = `
                <div class="action-buttons">
                    <button class="action-btn edit" data-policy-id="${policy.id}" title="Edit Policy">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="action-btn renew" data-policy-id="${policy.id}" title="Renew Policy">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                    <button class="action-btn view" data-policy-id="${policy.id}" title="View Details">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="action-btn delete" data-policy-id="${policy.id}" title="Delete Policy">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
        }
        
        row.innerHTML = `
            <td>${startIndex + idx + 1}</td>
            <td>${safeVehicleNumber}</td>
            <td>${customerName}</td>
            <td>${phone}</td>
            <td>${safeVehicleType}</td>
            <td style="white-space: nowrap;">${startDate && startDate.trim() !== '' ? formatDate(startDate) : '<span style="color: #999; font-style: italic;">Not set</span>'}</td>
            <td>₹${premium.toLocaleString()}</td>
            <td><span class="status-badge ${status.toLowerCase()}">${status}</span></td>
            <td>${actionButtonsHTML}</td>
        `;
        fragment.appendChild(row);
    });
    
    tbody.empty().append(fragment);
    updatePaginationInfo();
    
    // Check if we're rendering the dashboard recent policies table
    const isDashboardTable = window.recentPoliciesData && window.recentPoliciesData.length > 0 && $('#dashboard').hasClass('active');
    
    // Add event listeners for action buttons (only attach listeners for buttons that exist)
    if (!isDashboardTable) {
        // Only attach edit, renew, and delete listeners if not on dashboard
        tbody.find('.action-btn.edit').click(async function() {
            const policyId = parseInt($(this).data('policy-id'));
            await editPolicy(policyId);
        });
        
        tbody.find('.action-btn.renew').click(async function() {
            const policyId = parseInt($(this).data('policy-id'));
            await renewPolicy(policyId);
        });
        
        tbody.find('.action-btn.delete').click(function() {
            const policyId = parseInt($(this).data('policy-id'));
            deletePolicyHandler(policyId);
        });
    }
    
    // View button listener (always present)
    tbody.find('.action-btn.view').click(function() {
        const policyId = parseInt($(this).data('policy-id'));
        window.location.href = `/policies/${policyId}/view`;
    });
    
    tbody.find('.action-btn.history').off('click').on('click', function() {
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
    // Add "Self" as the first agent if there are any policies with businessType 'Self'
    const selfPolicies = allPolicies.filter(p => (p.businessType === 'Self' || p.business_type === 'Self'));
    const agentsWithSelf = [...allAgents];
    
    if (selfPolicies.length > 0) {
        // Add "Self" as an agent at the beginning
        agentsWithSelf.unshift({
            id: 0,
            name: 'Self',
            phone: '-',
            email: '-',
            userId: '-',
            status: 'Active'
        });
    }
    
    agentsFilteredData = agentsWithSelf;
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
    
    // Initialize bulk upload functionality
    initializeBulkUpload();
    
    // Add policy type filter listener
    $('#policyTypeFilter').off('change').on('change', async function() {
        const selectedType = $(this).val() || 'All';
        console.log('📋 Policy type filter changed to:', selectedType);
        
        // Show loading state
        $('#activePoliciesCount, #expiredPoliciesCount, #pendingRenewalsCount, #totalPoliciesCount').text('...');
        
        // Reload policies with filter
        await loadPoliciesData(selectedType);
        
        // Re-render table with new data
        policiesFilteredData = [...allPolicies];
        applyPoliciesFilters(); // Re-apply any search/status filters
        renderPoliciesTable();
        updatePoliciesPagination();
        
        showNotification(`Showing ${selectedType === 'All' ? 'all' : selectedType} policies`, 'success');
    });
};

// Initialize renewals page
const initializeRenewalsPage = () => {
    // Only initialize renewals if we're on the renewals page
    if (!$('#renewals').hasClass('active')) {
        return;
    }
    
    // Use real data from API
    // Build from policies (already prepared in buildRenewalsFromPolicies)
    renewalsFilteredData = [...policiesAsRenewals];
    renderRenewalsTable();
    updateRenewalsPagination();
    updateRenewalsStats();
    
    // Initialize search functionality
    $('#renewalsSearch').off('input').on('input', debounce(handleRenewalsSearch, 300));
    // Status/Priority filters removed in v2; ensure no legacy bindings
    $('#renewalStatusFilter, #renewalPriorityFilter').off('change');
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
    $('#themeToggle').off('click').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        toggleTheme();
    });
    
    // Sidebar toggle
    $('#sidebarToggle').off('click').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        toggleSidebar();
    });
    
    // Mobile menu toggle
    $('#mobileMenuToggle').off('click').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        toggleSidebar();
    });
    
    // Navigation
    $('.nav-item').click(function() {
        const page = $(this).data('page');
        navigateToPage(page);
    });
    
    // Profile dropdown - Handle both click and touch events for mobile compatibility
    $('#profileBtn').off('click touchend').on('click touchend', function(e) {
        console.log('🔄 Profile button clicked/touched');
        e.preventDefault();
        e.stopPropagation();
        toggleProfileDropdown();
        return false;
    });
    
    // Debug: Check if profile elements exist
    console.log('🔄 Profile button exists:', $('#profileBtn').length > 0);
    console.log('🔄 Profile dropdown exists:', $('#profileDropdown').length > 0);
    
    // Close dropdown when clicking outside - use proper event delegation
    $(document).off('click.profileDropdown touchend.profileDropdown').on('click.profileDropdown touchend.profileDropdown', function(e) {
        // Don't close if clicking inside the dropdown or on the profile button
        // Also allow links and form buttons to work normally
        const target = $(e.target);
        const isLink = target.is('a') || target.closest('a').length > 0;
        const isFormButton = target.is('button[type="submit"]') || target.closest('button[type="submit"]').length > 0;
        
        if (isLink || isFormButton) {
            // Allow link/form button clicks to proceed normally
            return true;
        }
        
        if (!$(e.target).closest('.profile-dropdown').length) {
            const dropdown = $('#profileDropdown');
            if (dropdown.hasClass('show')) {
                dropdown.removeClass('show');
                dropdown.css({
                    'display': 'none',
                    'opacity': '0',
                    'visibility': 'hidden',
                    'transform': 'translateY(-10px)',
                    'pointer-events': 'none'
                });
                console.log('🔄 Dropdown closed by outside click');
            }
        }
    });
    
    // Ensure dropdown items with links work properly - don't interfere with link navigation
    $('#profileDropdown .dropdown-item a').off('click.profileLink').on('click.profileLink', function(e) {
        // Stop propagation to prevent dropdown close handler from interfering
        e.stopPropagation();
        // Close dropdown immediately
        $('#profileDropdown').removeClass('show').css({
            'display': 'none',
            'opacity': '0',
            'visibility': 'hidden',
            'transform': 'translateY(-10px)',
            'pointer-events': 'none'
        });
        console.log('🔄 Profile link clicked, navigating to:', $(this).attr('href'));
        // Don't prevent default - allow the browser to navigate normally
    });
    
    // Modal controls - Agent Modal
    $('#addAgentBtn').click(() => openAgentModal());
    $('#closeAgentModal, #cancelAgent').click(() => closeAgentModal());
    
    // Modal controls - Followup Modal
    $('#addFollowupBtn').click(() => openFollowupModal());
    $('#closeFollowupModal, #cancelFollowup').click(() => closeFollowupModal());
    
    // Modal controls - Renewal Modal
    $('#addRenewalBtn').click(() => openRenewalModal());
    $('#closeRenewalModal, #cancelRenewal').click(() => closeRenewalModal());
    
    // Form submissions - remove existing handlers first to prevent duplicates
    $('#policyForm').off('submit').on('submit', handlePolicySubmit);
    $('#renewPolicyForm').off('submit').on('submit', handleRenewPolicySubmit);
    $('#agentForm').off('submit').on('submit', handleAgentSubmit);
    $('#followupForm').off('submit').on('submit', handleFollowupSubmit);
    $('#renewalForm').off('submit').on('submit', handleRenewalSubmit);
    
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
    // Policies export (support both header and table controls buttons)
    $('#exportPoliciesBtn').off('click').on('click', exportPoliciesData);
    $('#exportPolicies').off('click').on('click', exportPoliciesData);
    
    // Policies pagination
    $('#policiesPrevPage').click(() => goToPoliciesPage(policiesCurrentPage - 1));
    $('#policiesNextPage').click(() => goToPoliciesPage(policiesCurrentPage + 1));
    
    // Policies table sorting
    $('#policiesPageTable th[data-sort]').click(function() {
        const column = $(this).data('sort');
        handlePoliciesSort(column);
    });
    
    // Global revenue calculation for all policy types
    $(document).on('input change', '#premium, #payout, #customerPaidAmount, #healthPremium, #healthPayout, #healthCustomerPaid, #lifePremium, #lifePayout, #lifeCustomerPaid', function() {
        const $input = $(this);
        let policyType = '';
        
        // Determine which policy type this input belongs to
        if ($input.attr('id').startsWith('health')) {
            policyType = 'Health';
        } else if ($input.attr('id').startsWith('life')) {
            policyType = 'Life';
        } else {
            policyType = 'Motor';
        }
        
        // Trigger revenue calculation for the appropriate policy type
        setupRevenueAutoCalcForPolicyType(policyType);
    });
    
    // Renewals page controls (skip when Renewals v2 owns the page)
    const onRenewalsV2 = (window.location && window.location.pathname === '/renewals') && (window.RENEWALS_V2 === true);
    if (!onRenewalsV2) {
        $('#renewalsSearch').on('input', handleRenewalsSearch);
        // Removed: status/priority filters for renewals v2
        $('#renewalsRowsPerPage').off('change').on('change', handleRenewalsRowsPerPageChange);
        $('#exportRenewals').click(exportRenewalsData);
        
        // Renewals pagination
        $('#renewalsPrevPage').click(() => goToRenewalsPage(renewalsCurrentPage - 1));
        $('#renewalsNextPage').click(() => goToRenewalsPage(renewalsCurrentPage + 1));
        
        // Renewals table sorting
        $('#renewalsTable th[data-sort]').off('click').on('click', function() {
            const column = $(this).data('sort');
            handleRenewalsSort(column);
        });
    }
    
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
    
    // Auto-calculate dates from Policy Issue Date (Issue Date → Start Date → End Date)
    $('#policyIssueDate, #healthPolicyIssueDate, #lifePolicyIssueDate').change(function() {
        const issueDateVal = $(this).val();
        if (!issueDateVal) return;
        
        const issueDate = new Date(issueDateVal);
        
        // Calculate start date (issue_date + 1 day)
        const startDate = new Date(issueDate);
        startDate.setDate(startDate.getDate() + 1);
        
        // Calculate end date (start_date + 1 year - 1 day)
        const endDate = new Date(startDate);
        endDate.setFullYear(endDate.getFullYear() + 1);
        endDate.setDate(endDate.getDate() - 1);
        
        // Update the appropriate fields based on which form is active
        const formPrefix = $(this).attr('id').includes('health') ? 'health' : 
                          $(this).attr('id').includes('life') ? 'life' : '';
        
        if (formPrefix) {
            $(`#${formPrefix}StartDate`).val(formatLocalDate(startDate));
            $(`#${formPrefix}EndDate`).val(formatLocalDate(endDate));
        } else {
            $('#startDate').val(formatLocalDate(startDate));
            $('#endDate').val(formatLocalDate(endDate));
        }
    });
    
    // Auto-calculate End Date when Start Date is manually edited
    $('#startDate, #healthStartDate, #lifeStartDate').change(function() {
        const startDateVal = $(this).val();
        if (!startDateVal) return;
        
        const startDate = new Date(startDateVal);
        
        // Calculate end date (start_date + 1 year - 1 day)
        const endDate = new Date(startDate);
        endDate.setFullYear(endDate.getFullYear() + 1);
        endDate.setDate(endDate.getDate() - 1);
        
        // Update the appropriate end date field
        const formPrefix = $(this).attr('id').includes('health') ? 'health' : 
                          $(this).attr('id').includes('life') ? 'life' : '';
        
        if (formPrefix) {
            $(`#${formPrefix}EndDate`).val(formatLocalDate(endDate));
        } else {
            $('#endDate').val(formatLocalDate(endDate));
        }
    });
    
    // Auto-calculate dates from Policy Issue Date for renewals (Issue Date → Start Date → End Date)
    $('#renewPolicyIssueDate').change(function() {
        const issueDateVal = $(this).val();
        if (!issueDateVal) return;
        
        const issueDate = new Date(issueDateVal);
        
        // Calculate start date (issue_date + 1 day)
        const startDate = new Date(issueDate);
        startDate.setDate(startDate.getDate() + 1);
        $('#renewStartDate').val(formatLocalDate(startDate));
        
        // Calculate end date (start_date + 1 year - 1 day)
        const endDate = new Date(startDate);
        endDate.setFullYear(endDate.getFullYear() + 1);
        endDate.setDate(endDate.getDate() - 1);
        $('#renewEndDate').val(formatLocalDate(endDate));
    });
    
    // Auto-calculate End Date when Renew Start Date is manually edited
    $('#renewStartDate').change(function() {
        const startDateVal = $(this).val();
        if (!startDateVal) return;
        
        const startDate = new Date(startDateVal);
        
        // Calculate end date (start_date + 1 year - 1 day)
        const endDate = new Date(startDate);
        endDate.setFullYear(endDate.getFullYear() + 1);
        endDate.setDate(endDate.getDate() - 1);
        $('#renewEndDate').val(formatLocalDate(endDate));
    });

    // Multi-step modal functions
    initializeMultiStepModal();
    
    // View Policy Modal controls
    $('#closeViewPolicyModal').click(() => closeViewPolicyModal());
    $('#closeViewPolicyBtn').click(() => closeViewPolicyModal());
    $('#editPolicyFromViewBtn').click(async function() {
        // Get the policy ID from the current policy being viewed
        // We need to store the current policy ID when populating the modal
        if (window.currentViewingPolicyId) {
            closeViewPolicyModal();
            await editPolicy(window.currentViewingPolicyId);
        } else {
            showNotification('Policy ID not found. Please try viewing the policy again.', 'error');
        }
    });
    
    // Renewal Modal controls
    $('#closeRenewalModal').click(() => closeRenewalModal());
    $('#cancelRenewal').click(() => closeRenewalModal());
    $('#renewalForm').submit(handleRenewalSubmit);
    
    // Policy selection in renewal modal (robust mapping for API variations)
    $('#renewalPolicyId').change(function() {
        const policyId = parseInt($(this).val());
        const p = (allPolicies || []).find(pp => (pp.id || 0) === policyId);
        $('#renewalCustomerName').val(p ? (p.customerName || p.owner || '') : '');
        $('#renewalPolicyType').val(p ? (p.policyType || p.type || '') : '');
        $('#renewalExpiryDate').val(p ? (p.endDate || p.end_date || '') : '');
    });
    
    // Follow-up Modal controls
    $('#closeFollowupModal').click(() => closeFollowupModal());
    $('#cancelFollowup').click(() => closeFollowupModal());
    $('#followupForm').off('submit').on('submit', handleFollowupSubmit);
    // Ensure the Save button triggers the form submit event
    $('#saveFollowupBtn').off('click').on('click', function (ev) {
        ev.preventDefault();
        $('#followupForm').trigger('submit');
    });
    
    // Policy selection in follow-up modal
    $('#followupPolicyId').change(function() {
        const policyId = parseInt($(this).val());
        if (policyId) {
            const p = (allPolicies || []).find(pp => (pp.id || 0) === policyId);
            if (p) {
                const name = p.customerName || p.owner || p.customer_name || '';
                const phone = p.phone || p.customerPhone || p.customer_phone || '';
                const email = p.email || p.customerEmail || p.customer_email || '';
                $('#followupCustomerName').val(name);
                $('#followupPhone').val(phone);
                $('#followupEmail').val(email);
            }
        }
    });
    
    // Reports page controls
    $('#generateReportBtn').click(generateReports);
    $('#exportReportBtn').click(exportReport);
    $('#reportStartDate, #reportEndDate').change(updateReportDateRange);
    $('#reportTypeFilter').change(updateReportType);
};

// Theme toggle
const toggleTheme = () => {
    console.log('Toggle theme clicked');
    $('body').toggleClass('dark-theme');
    const isDark = $('body').hasClass('dark-theme');
    console.log('Theme is now:', isDark ? 'dark' : 'light');
    
    // Save theme preference to localStorage
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
    console.log('Theme saved to localStorage');
    
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

// Initialize theme from localStorage
const initializeTheme = () => {
    console.log('Initializing theme...');
    const savedTheme = localStorage.getItem('theme');
    console.log('Saved theme:', savedTheme);
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    
    // Use saved theme, or system preference, or default to light
    const shouldUseDark = savedTheme === 'dark' || (!savedTheme && prefersDark);
    
    if (shouldUseDark) {
        $('body').addClass('dark-theme');
        $('#themeToggle i').removeClass('fas fa-moon').addClass('fas fa-sun');
    } else {
        $('body').removeClass('dark-theme');
        $('#themeToggle i').removeClass('fas fa-sun').addClass('fas fa-moon');
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
    const topNav = $('.top-nav');
    const overlay = $('#sidebarOverlay');
    
    if (window.innerWidth <= 1024) {
        // Mobile/Tablet behavior
        sidebar.toggleClass('show');
        overlay.toggleClass('show');
    } else {
        // Desktop behavior
        sidebar.toggleClass('collapsed');
    }
    
    // Update main content margin and top nav position
    if (sidebar.hasClass('collapsed') && window.innerWidth > 1024) {
        mainContent.css('margin-left', '80px');
        topNav.css('left', '80px');
    } else if (!sidebar.hasClass('collapsed') && window.innerWidth > 1024) {
        mainContent.css('margin-left', '280px');
        topNav.css('left', '280px');
    } else {
        mainContent.css('margin-left', '0');
        topNav.css('left', '0');
    }
};

// Close sidebar on mobile when clicking outside
$(document).on('click', function(e) {
    if (window.innerWidth <= 1024) {
        const sidebar = $('#sidebar');
        const sidebarToggle = $('#sidebarToggle');
        const mobileMenuToggle = $('#mobileMenuToggle');
        const overlay = $('#sidebarOverlay');
        
        if (!sidebar.is(e.target) && 
            sidebar.has(e.target).length === 0 && 
            !sidebarToggle.is(e.target) &&
            !mobileMenuToggle.is(e.target) &&
            !mobileMenuToggle.has(e.target).length &&
            sidebar.hasClass('show')) {
            sidebar.removeClass('show');
            overlay.removeClass('show');
        }
    }
});

// Close sidebar when clicking overlay
$('#sidebarOverlay').on('click', function() {
    $('#sidebar').removeClass('show');
    $(this).removeClass('show');
});

// Handle window resize
$(window).on('resize', function() {
    const sidebar = $('#sidebar');
    const mainContent = $('#mainContent');
    const overlay = $('#sidebarOverlay');
    
    if (window.innerWidth > 1024) {
        sidebar.removeClass('show');
        overlay.removeClass('show');
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
    // Clear recent policies data when navigating away from dashboard
    if (page !== 'dashboard') {
        window.recentPoliciesData = null;
        window.currentSort = null;
    }
    
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
    console.log('🔄 toggleProfileDropdown called');
    const dropdown = $('#profileDropdown');
    const wasShown = dropdown.hasClass('show');
    
    console.log('🔄 Dropdown element found:', dropdown.length > 0);
    console.log('🔄 Was shown:', wasShown);
    
    // Toggle the show class
    dropdown.toggleClass('show');
    
    const isShown = dropdown.hasClass('show');
    console.log('🔄 Now showing:', isShown);
    
    // Force styles to ensure visibility (as a fallback)
    if (isShown) {
        dropdown.css({
            'display': 'block',
            'opacity': '1',
            'visibility': 'visible',
            'transform': 'translateY(0)',
            'pointer-events': 'auto'
        });
        console.log('✅ Dropdown opened successfully');
    } else {
        dropdown.css({
            'display': 'none',
            'opacity': '0',
            'visibility': 'hidden',
            'transform': 'translateY(-10px)',
            'pointer-events': 'none'
        });
        console.log('❌ Dropdown closed');
    }
};

// Modal functions
const openPolicyModal = () => {
    console.log('openPolicyModal called');
    console.log('Policy modal element:', $('#policyModal').length);
    
    // Enable duplicate check for add mode
    vehicleDuplicateCheckEnabled = true;
    
    $('#policyModalTitle').text('Add New Policy');
    $('#savePolicyBtn').text('Add Policy');
    $('#policyForm')[0].reset();
    $('#policyForm').removeData('edit-id');
    
    $('#policyForm').attr('action', '/policies');
    $('#formMethod').val('POST');
    
    $('#hiddenPolicyType').val('');
    $('#hiddenBusinessType').val('');
    updatePolicyAgentDropdown();
    // Keep enabled; only toggle required later
    $('#policyModal #agentName').prop('required', false);
    
    // Reset step navigation
    $('#step1').show();
    $('#step2, #step3').hide();
    $('#nextStep1').prop('disabled', true);
    $('#nextStep2').prop('disabled', true);
    
    // Reset selections
    selectedPolicyType = '';
    selectedBusinessType = '';
    
    // Reset dropdowns to empty state
    $('#policyTypeSelect').val('');
    $('#businessTypeSelect').val('');
    
    console.log('Initial button states - Next Step 1 disabled:', $('#nextStep1').prop('disabled'));
    console.log('Initial button states - Next Step 2 disabled:', $('#nextStep2').prop('disabled'));
    
    // Setup form validations
    setupFormValidations();
    
    // Setup modal-specific event handlers after modal is visible
    setupModalEventHandlers();
    
    // Explicitly ensure Agent Name field is hidden if Self is selected
    const currentBusinessType = $('#businessTypeSelect').val();
    if (currentBusinessType === 'Self') {
        $('#agentNameGroup').hide(); // Hide the entire field group
        $('#policyModal #agentName').val('').prop('required', false);
        $('#policyModal #agentName').removeAttr('required');
        $('#policyModal #agentName').removeClass('required');
        console.log('Agent name field group hidden for Self in openPolicyModal');
    } else if (currentBusinessType === 'Agent') {
        $('#agentNameGroup').show(); // Show the field group
        $('#policyModal #agentName').prop('required', true);
        console.log('Agent name field group shown for Agent in openPolicyModal');
    }
    
    $('#policyModal').addClass('show');
    console.log('Policy modal should now be visible');
    
    // Add vehicle number validation event listeners
    setupVehicleNumberValidation();
};

// Setup vehicle number validation for all vehicle number inputs
const setupVehicleNumberValidation = () => {
    // Remove existing listeners to prevent duplicates
    $(document).off('input blur', '#vehicleNumber');
    
    // Add event listeners for the vehicle number input
    $(document).on('input blur', '#vehicleNumber', function(e) {
        if (!vehicleDuplicateCheckEnabled) {
            // If disabled (edit mode), ensure any warning is removed
            const existingMessage = this.parentElement.querySelector('.vehicle-validation-message');
            if (existingMessage) existingMessage.remove();
            return;
        }
        const vehicleNumber = $(this).val().trim();
        if (vehicleNumber.length >= 4) {
            checkVehicleNumberDuplicate(vehicleNumber, this);
        }
    });
};

const closePolicyModal = () => {
    $('#policyModal').removeClass('show');
    
    $('#policyForm')[0].reset();
    
    $('#policyForm').removeData('edit-id');
    $('#policyModalTitle').text('Add New Policy');
    $('#savePolicyBtn').text('Add Policy');
    
    $('#policyForm').attr('action', '/policies');
    $('#formMethod').val('POST');
    
    $('#step1').show();
    $('#step2, #step3').hide();
    
    $('#policyTypeSelect').val('');
    $('#businessTypeSelect').val('');
    $('#hiddenPolicyType').val('');
    $('#hiddenBusinessType').val('');
    $('#policyModal #agentName').prop('required', false);
    
    $('#nextStep1, #nextStep2').prop('disabled', true);
    
    selectedPolicyType = null;
    selectedBusinessType = null;
    
    $('.policy-form input[required], .policy-form select[required]').prop('required', false);
    $('.policy-form').removeClass('active');
    $('#policyForm').removeData('edit-listener-added');
    
    // Reset duplicate check to default (enabled)
    vehicleDuplicateCheckEnabled = true;
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
// Remove a document from a policy
window.removeDocument = async (documentType) => {
    const policyId = $('#viewPolicyModal').data('policy-id');
    console.log('🗑️ removeDocument called:', { documentType, policyId, modalExists: $('#viewPolicyModal').length });
    
    if (!policyId) {
        console.error('❌ Policy ID not found in modal data attribute');
        showNotification('Policy ID not found', 'error');
        return;
    }

    if (!confirm('Remove this document? This action cannot be undone.')) {
        return;
    }

    console.log('🗑️ Removing document:', documentType, 'from policy:', policyId);
    
    try {
        const data = await apiCall(`/api/policies/${policyId}/document/${documentType}`, {
            method: 'DELETE'
        });
        
        console.log('✅ Document removed successfully');
        
        // Update the local policy data
        const policy = allPolicies.find(p => p.id === policyId);
        if (policy) {
            const pathField = `${documentType}_copy_path`;
            policy[pathField] = null; // Clear the document path
        }
        
        // Disable the buttons for this doc type
        const cap = documentType.charAt(0).toUpperCase() + documentType.slice(1);
        $(`#download${cap}Btn`).prop('disabled', true).addClass('disabled');
        $(`#remove${cap}Btn`).prop('disabled', true).addClass('disabled');
        
        // Update the status badge
        const statusBadge = $(`#${documentType}Status .status-badge`);
        statusBadge.removeClass('available').addClass('not-available').text('Not Available');
        
        showNotification('Document removed successfully', 'success');
    } catch (err) {
        console.error('❌ Remove document failed:', err);
        showNotification(err.message || 'Failed to remove document', 'error');
    }
};

// Download existing document from edit modal
window.downloadExistingDocument = (documentType) => {
    const policyId = $('#policyForm').data('edit-id');
    console.log('Download existing document - Policy ID:', policyId, 'Document Type:', documentType);
    
    if (!policyId) {
        showNotification('Policy ID not found', 'error');
        return;
    }
    
    // Create a temporary link element for download
    const downloadUrl = `/api/policies/${policyId}/download/${documentType}?_=${Date.now()}`;
    console.log('Download URL:', downloadUrl);
    
    // Create a temporary anchor element and trigger download
    const link = document.createElement('a');
    link.href = downloadUrl;
    link.download = `${documentType}_document.pdf`;
    link.target = '_blank';
    
    // Add to DOM, click, and remove
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    showNotification(`Downloading ${documentType} document...`, 'info');
};

// Remove existing document from edit modal
window.removeExistingDocument = (documentType) => {
    const policyId = $('#policyForm').data('edit-id');
    if (!policyId) {
        showNotification('Policy ID not found', 'error');
        return;
    }

    if (!confirm('Remove this document? This action cannot be undone.')) {
        return;
    }

    fetch(`/api/policies/${policyId}/document/${documentType}`, {
        method: 'DELETE',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
    })
    .then(async (response) => {
        if (!response.ok) {
            const data = await response.json().catch(() => ({}));
            throw new Error(data.message || 'Failed to remove document');
        }
        return response.json();
    })
    .then(() => {
        // Update the local policy data
        const policyId = $('#policyForm').data('edit-id');
        const policy = allPolicies.find(p => p.id === policyId);
        if (policy) {
            const pathField = `${documentType}_copy_path`;
            policy[pathField] = null; // Clear the document path
        }
        
        // Hide the existing document item
        const cap = documentType.charAt(0).toUpperCase() + documentType.slice(1);
        $(`#existing${cap}Copy`).hide();
        
        // Check if any existing documents are left
        const remainingDocs = $('.existing-doc-item:visible').length;
        if (remainingDocs === 0) {
            $('#existingDocuments').hide();
        }
        
        showNotification('Document removed successfully', 'success');
    })
    .catch((err) => {
        console.error('Remove document failed:', err);
        showNotification(err.message || 'Failed to remove document', 'error');
    });
};

// Setup existing documents display in edit modal
const setupExistingDocuments = (policy) => {
    console.log('Setting up existing documents for policy:', policy);
    
    // Hide all existing document items first
    $('.existing-doc-item').hide();
    $('#existingDocuments, #healthExistingDocuments, #lifeExistingDocuments').hide();
    
    // Get the policy type to determine which document sections to show
    const policyType = policy.policyType || policy.policy_type || 'Motor';
    
    // Check which documents exist and show them based on policy type
    const documentTypes = [
        { type: 'policy', field: 'policy_copy_path' },
        { type: 'rc', field: 'rc_copy_path' },
        { type: 'aadhar', field: 'aadhar_copy_path' },
        { type: 'pan', field: 'pan_copy_path' }
    ];
    
    let hasExistingDocs = false;
    
    documentTypes.forEach(doc => {
        const path = policy[doc.field];
        if (path && path.trim() !== '') {
            const cap = doc.type.charAt(0).toUpperCase() + doc.type.slice(1);
            
            // Show document in the appropriate form based on policy type
            if (policyType === 'Motor') {
                $(`#existing${cap}Copy`).show();
                hasExistingDocs = true;
            } else if (policyType === 'Health') {
                $(`#healthExisting${cap}Copy`).show();
                hasExistingDocs = true;
            } else if (policyType === 'Life') {
                $(`#lifeExisting${cap}Copy`).show();
                hasExistingDocs = true;
            }
        }
    });
    
    // Show the existing documents section if any documents exist
    if (hasExistingDocs) {
        if (policyType === 'Motor') {
            $('#existingDocuments').show();
        } else if (policyType === 'Health') {
            $('#healthExistingDocuments').show();
        } else if (policyType === 'Life') {
            $('#lifeExistingDocuments').show();
        }
    }
    
    // Also setup document download buttons for the specific policy
    setupDocumentDownloadButtons(policy);
    
    // Store policy data in modal for document functions
    $('#policyModal').data('policy-data', policy);
};

const setupDocumentDownloadButtons = (policy) => {
    // Store policy ID in modal for download functions
    console.log('📋 setupDocumentDownloadButtons called with policy:', policy);
    console.log('📋 Setting policy-id on modal to:', policy.id);
    $('#viewPolicyModal').data('policy-id', policy.id);
    console.log('📋 Verify policy-id set:', $('#viewPolicyModal').data('policy-id'));
    
    // Enable/disable download buttons based on document availability
    const documents = {
        'policy': policy.policy_copy_path || policy.policyCopyPath,
        'rc': policy.rc_copy_path || policy.rcCopyPath,
        'aadhar': policy.aadhar_copy_path || policy.aadharCopyPath,
        'pan': policy.pan_copy_path || policy.panCopyPath,
    };
    
    Object.keys(documents).forEach(docType => {
        const docTypeCapitalized = docType.charAt(0).toUpperCase() + docType.slice(1);
        const downloadButton = $(`#download${docTypeCapitalized}Btn`);
        const removeButton = $(`#remove${docTypeCapitalized}Btn`);
        const statusBadge = $(`#${docType}Status .status-badge`);
        
        // Remove existing event handlers to prevent duplicates
        downloadButton.off('click');
        removeButton.off('click');
        
        if (documents[docType] && documents[docType].trim() !== '') {
            // Enable buttons
            downloadButton.prop('disabled', false).removeClass('disabled');
            downloadButton.attr('title', `Download ${docType} document`);
            
            // Use jQuery event handler instead of inline onclick
            downloadButton.on('click', function(e) {
                e.preventDefault();
                console.log('📥 Download button clicked for:', docType);
                const currentPolicyId = $('#viewPolicyModal').data('policy-id');
                console.log('📥 Current policy ID from modal:', currentPolicyId);
                if (currentPolicyId) {
                    // Call the global downloadDocument(policyId, documentType)
                    downloadDocument(currentPolicyId, docType);
                } else {
                    showNotification('Policy ID not found', 'error');
                }
            });
            
            removeButton.prop('disabled', false).removeClass('disabled');
            removeButton.attr('title', `Remove ${docType} document`);
            
            // Use jQuery event handler instead of inline onclick
            removeButton.on('click', function(e) {
                e.preventDefault();
                console.log('🗑️ Remove button clicked for:', docType);
                const currentPolicyId = $('#viewPolicyModal').data('policy-id');
                console.log('🗑️ Current policy ID from modal:', currentPolicyId);
                if (currentPolicyId) {
                    window.removeDocument(docType);
                } else {
                    showNotification('Policy ID not found', 'error');
                }
            });
            
            // Update status badge
            statusBadge.removeClass('not-available').addClass('available').text('Available');
        } else {
            // Disable buttons
            downloadButton.prop('disabled', true).addClass('disabled');
            downloadButton.attr('title', `${docType} document not available`);
            removeButton.prop('disabled', true).addClass('disabled');
            removeButton.attr('title', `${docType} document not available`);
            
            // Update status badge
            statusBadge.removeClass('available').addClass('not-available').text('Not Available');
        }
    });
};
// Form handlers
const handlePolicySubmit = async (e) => {
    e.preventDefault();
    
    console.log('===============================================');
    console.log('HandlePolicySubmit: Form submission started');
    console.log('HandlePolicySubmit: Current Step:', {
        step1Visible: $('#step1').is(':visible'),
        step2Visible: $('#step2').is(':visible'),
        step3Visible: $('#step3').is(':visible')
    });
    
    // Prepare form for submission to prevent validation errors on hidden fields
    prepareFormForSubmission();
    
    // Determine which policy type is currently active (robust)
    const resolveActivePolicyType = () => {
        const hidden = $('#hiddenPolicyType').val();
        if (hidden) return hidden;
        if ($('#healthForm').hasClass('active')) return 'Health';
        if ($('#lifeForm').hasClass('active')) return 'Life';
        if ($('#motorForm').hasClass('active')) return 'Motor';
        const selected = $('#policyTypeSelect').val();
        return selected || 'Motor';
    };
    const resolveBusinessType = () => {
        const hidden = $('#hiddenBusinessType').val();
        if (hidden) return hidden;
        const selected = $('#businessTypeSelect').val();
        return selected || 'Self';
    };

    const activePolicyType = resolveActivePolicyType();
    const businessType = resolveBusinessType();
    
    // Explicitly ensure Agent Name field is hidden if Self is selected
    if (businessType === 'Self') {
        $('#agentNameGroup').hide(); // Hide the entire field group
        $('#policyModal #agentName').val('').prop('required', false);
        $('#policyModal #agentName').removeAttr('required');
        $('#policyModal #agentName').removeClass('required');
        console.log('HandlePolicySubmit: Agent name field group hidden for Self');
    } else if (businessType === 'Agent') {
        $('#agentNameGroup').show(); // Show the field group
        $('#policyModal #agentName').prop('required', true);
        console.log('HandlePolicySubmit: Agent name field group shown for Agent');
    }
    
    console.log('HandlePolicySubmit: Active policy type:', activePolicyType, 'Business type:', businessType);
    console.log('HandlePolicySubmit: Hidden policy type field value:', $('#hiddenPolicyType').val());
    console.log('HandlePolicySubmit: Hidden business type field value:', $('#hiddenBusinessType').val());
    
    // Debug form visibility
    console.log('HandlePolicySubmit: Form visibility check:', {
        motorFormVisible: $('#motorForm').is(':visible'),
        healthFormVisible: $('#healthForm').is(':visible'),
        lifeFormVisible: $('#lifeForm').is(':visible'),
        motorFormActive: $('#motorForm').hasClass('active'),
        healthFormActive: $('#healthForm').hasClass('active'),
        lifeFormActive: $('#lifeForm').hasClass('active')
    });
    
    // Build policy data based on the active policy type
    let policyData = {
        policyType: activePolicyType,
        businessType: businessType,
    agent_name: $('#policyModal #agentName').val() || '',
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
    
    // Ensure we have the hidden field values for policyType and businessType
    const hiddenPolicyType = $('#hiddenPolicyType').val();
    const hiddenBusinessType = $('#hiddenBusinessType').val();
    
    console.log('HandlePolicySubmit: Hidden field values:', {
        hiddenPolicyType,
        hiddenBusinessType
    });
    
    if (!policyData.policyType) policyData.policyType = activePolicyType;
    if (!policyData.businessType) policyData.businessType = businessType;
    
    // Get data based on active policy type
    if (activePolicyType === 'Motor') {
        policyData = {
            ...policyData,
            customerName: $('#customerName').val() || '',
            customerPhone: $('#customerPhone').val() || '',
            customerEmail: $('#customerEmail').val() || '',
            companyName: $('#companyName').val() || '',
            insuranceType: $('#insuranceType').val() || '',
            policyIssueDate: $('#policyIssueDate').val() || '',
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
            policyIssueDate: $('#healthPolicyIssueDate').val() || '',
            startDate: $('#healthStartDate').val() || '',
            endDate: $('#healthEndDate').val() || '',
            premium: parseFloat($('#healthPremium').val() || 0),
            customerPaidAmount: parseFloat($('#healthCustomerPaid').val() || 0),
            revenue: parseFloat($('#healthRevenue').val() || 0),
            payout: parseFloat($('#healthPayout').val() || 0),
            // Additional health-specific fields
            customerAge: $('#healthCustomerAge').val() || '',
            customerGender: $('#healthCustomerGender').val() || '',
            sumInsured: parseFloat($('#healthSumInsured').val() || 0),
            sumAssured: parseFloat($('#healthSumAssured').val() || 0),
            policyTerm: $('#healthPolicyTerm').val() || '',
            premiumFrequency: $('#healthPremiumFrequency').val() || ''
        };
    } else if (activePolicyType === 'Life') {
        policyData = {
            ...policyData,
            customerName: $('#lifeCustomerName').val() || '',
            customerPhone: $('#lifeCustomerPhone').val() || '',
            customerEmail: $('#lifeCustomerEmail').val() || '',
            companyName: $('#lifeCompanyName').val() || '',
            insuranceType: $('#lifePlanType').val() || '',
            policyIssueDate: $('#lifePolicyIssueDate').val() || '',
            startDate: $('#lifeStartDate').val() || '',
            endDate: $('#lifeEndDate').val() || '',
            premium: parseFloat($('#lifePremium').val() || 0),
            customerPaidAmount: parseFloat($('#lifeCustomerPaid').val() || 0),
            revenue: parseFloat($('#lifeRevenue').val() || 0),
            payout: parseFloat($('#lifePayout').val() || 0),
            // Additional life-specific fields
            customerAge: $('#lifeCustomerAge').val() || '',
            customerGender: $('#lifeCustomerGender').val() || '',
            sumAssured: parseFloat($('#lifeSumAssured').val() || 0),
            policyTerm: $('#lifePolicyTerm').val() || '',
            premiumFrequency: $('#lifePremiumFrequency').val() || ''
        };
    }
    
    // Log the final policy data being sent
    console.log('HandlePolicySubmit: Final policy data to be sent:', policyData);
    
    // Debug: Log individual field values
    console.log('HandlePolicySubmit: Field validation check:', {
        customerName: policyData.customerName,
        customerPhone: policyData.customerPhone,
        companyName: policyData.companyName,
        insuranceType: policyData.insuranceType,
        startDate: policyData.startDate,
        endDate: policyData.endDate,
        premium: policyData.premium,
        customerPaidAmount: policyData.customerPaidAmount,
        vehicleNumber: policyData.vehicleNumber,
        vehicleType: policyData.vehicleType,
        revenue: policyData.revenue
    });
    
    // Additional debugging for form field values
    console.log('HandlePolicySubmit: Form field values:');
    if (activePolicyType === 'Motor') {
        console.log('  Motor form - Customer Name:', $('#customerName').val());
        console.log('  Motor form - Customer Phone:', $('#customerPhone').val());
        console.log('  Motor form - Company Name:', $('#companyName').val());
        console.log('  Motor form - Insurance Type:', $('#insuranceType').val());
        console.log('  Motor form - Start Date:', $('#startDate').val());
        console.log('  Motor form - End Date:', $('#endDate').val());
        console.log('  Motor form - Premium:', $('#premium').val());
        console.log('  Motor form - Customer Paid:', $('#customerPaidAmount').val());
        console.log('  Motor form - Vehicle Number:', $('#vehicleNumber').val());
        console.log('  Motor form - Vehicle Type:', $('#vehicleType').val());
    } else if (activePolicyType === 'Health') {
        console.log('  Health form - Customer Name:', $('#healthCustomerName').val());
        console.log('  Health form - Customer Phone:', $('#healthCustomerPhone').val());
        console.log('  Health form - Company Name:', $('#healthCompanyName').val());
        console.log('  Health form - Insurance Type:', $('#healthPlanType').val());
        console.log('  Health form - Start Date:', $('#healthStartDate').val());
        console.log('  Health form - End Date:', $('#healthEndDate').val());
        console.log('  Health form - Premium:', $('#healthPremium').val());
        console.log('  Health form - Customer Paid:', $('#healthCustomerPaid').val());
    } else if (activePolicyType === 'Life') {
        console.log('  Life form - Customer Name:', $('#lifeCustomerName').val());
        console.log('  Life form - Customer Phone:', $('#lifeCustomerPhone').val());
        console.log('  Life form - Company Name:', $('#lifeCompanyName').val());
        console.log('  Life form - Insurance Type:', $('#lifePlanType').val());
        console.log('  Life form - Start Date:', $('#lifeStartDate').val());
        console.log('  Life form - End Date:', $('#lifeEndDate').val());
        console.log('  Life form - Premium:', $('#lifePremium').val());
        console.log('  Life form - Customer Paid:', $('#lifeCustomerPaid').val());
    }
    
    // Validate required fields based on active policy type
    if (!policyData.customerName || !policyData.customerPhone || !policyData.companyName) {
        showNotification('Please fill in all required fields (Customer Name, Phone, Company)', 'error');
        return;
    }

    // If business type is Agent, ensure an agent is selected
    if ((policyData.businessType || '').toLowerCase() === 'agent') {
    const agentVal = $('#policyModal #agentName').val();
        if (!agentVal) {
            showNotification('Please select an Agent name', 'error');
            return;
        }
        policyData.agent_name = agentVal;
    } else {
        policyData.agent_name = 'Self';
    }
    
    // Validate insurance type
    if (!policyData.insuranceType) {
        showNotification('Please select an insurance type', 'error');
        return;
    }
    
    // Validate dates
    if (!policyData.startDate || !policyData.endDate) {
        showNotification('Please select start and end dates', 'error');
        return;
    }
    
    // Validate amounts
    if (policyData.premium <= 0) {
        showNotification('Premium must be greater than 0', 'error');
        return;
    }
    
    if (policyData.customerPaidAmount <= 0) {
        showNotification('Customer paid amount must be greater than 0', 'error');
        return;
    }

    // Client-side phone validation: must be exactly 10 digits
    const phoneDigits = (policyData.customerPhone || '').replace(/\D/g, '');
    if (phoneDigits.length !== 10) {
        showNotification('Phone number must be exactly 10 digits', 'error');
        return;
    }
    
    // Client-side email validation
    if (policyData.customerEmail && policyData.customerEmail.trim() !== '') {
        const emailField = activePolicyType === 'Motor' ? '#customerEmail' : 
                          activePolicyType === 'Health' ? '#healthCustomerEmail' : 
                          '#lifeCustomerEmail';
        const emailInput = document.querySelector(emailField);
        if (emailInput && !validateEmail(emailInput)) {
            showNotification('Please enter a valid email address', 'error');
            return;
        }
    }
    
    // Additional validation for Motor policies
    if (activePolicyType === 'Motor') {
        if (!policyData.vehicleNumber || !policyData.vehicleType) {
            showNotification('Please fill in vehicle number and type for Motor policies', 'error');
            return;
        }
    }
    
    // Validate file sizes before submission
    const fileErrors = validateAllFiles();
    if (fileErrors.length > 0) {
        showNotification(fileErrors, 'error');
        return;
    }
    
    // Validate CSRF token is present
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!csrfToken) {
        console.error('HandlePolicySubmit: CSRF token not found!');
        showNotification('Security token missing. Please refresh the page and try again.', 'error');
        return;
    }
    console.log('HandlePolicySubmit: CSRF token found:', csrfToken.substring(0, 10) + '...');
    
    try {
        const editId = $('#policyForm').data('edit-id');
        
        // Create FormData for file uploads
        const formData = new FormData();
        
        // Add all policy data to FormData
        Object.keys(policyData).forEach(key => {
            formData.append(key, policyData[key]);
        });
        
        // Log what's being added to FormData
        console.log('HandlePolicySubmit: Adding to FormData:');
        Object.keys(policyData).forEach(key => {
            console.log(`  ${key}: ${policyData[key]}`);
        });
        
        // Add file uploads if they exist
        let fileInputs = {};
        
        if (activePolicyType === 'Motor') {
            fileInputs = {
                'policyCopy': $('#policyCopy')[0],
                'rcCopy': $('#rcCopy')[0],
                'aadharCopy': $('#aadharCopy')[0],
                'panCopy': $('#panCopy')[0]
            };
        } else if (activePolicyType === 'Health') {
            fileInputs = {
                'policyCopy': $('#healthPolicyCopy')[0],
                'aadharCopy': $('#healthAadharCopy')[0],
                'panCopy': $('#healthPanCopy')[0]
            };
        } else if (activePolicyType === 'Life') {
            fileInputs = {
                'policyCopy': $('#lifePolicyCopy')[0],
                'aadharCopy': $('#lifeAadharCopy')[0],
                'panCopy': $('#lifePanCopy')[0]
            };
        }
        
        Object.keys(fileInputs).forEach(key => {
            const fileInput = fileInputs[key];
            if (fileInput && fileInput.files && fileInput.files[0]) {
                formData.append(key, fileInput.files[0]);
                console.log(`HandlePolicySubmit: Added file ${key}:`, fileInput.files[0].name);
            }
        });
        
        console.log('Submitting policy data with files:', policyData);
        
        if (editId) {
            // Update existing policy
            console.log('HandlePolicySubmit: Updating policy with ID:', editId);
            console.log('HandlePolicySubmit: Policy data being sent:', policyData);
            
            // Laravel requires method spoofing for multipart PUT
            formData.append('_method', 'PUT');
            
            try {
                const response = await updatePolicyWithFiles(editId, formData);
                console.log('HandlePolicySubmit: Update response received:', response);
                
                // Update local data
                const index = allPolicies.findIndex(p => p.id === editId);
                if (index !== -1) {
                    allPolicies[index] = { ...allPolicies[index], ...response.policy };
                }
                
                console.log('Showing policy update notification - should only appear once');
                showNotification('Policy updated successfully!', 'success');
                // Redirect to dashboard after update
                window.location.href = '/dashboard';
            } catch (error) {
                console.error('HandlePolicySubmit: Update failed with error:', error);
                showNotification('Failed to update policy: ' + (error.message || 'Unknown error'), 'error');
                return;
            }
        } else {
            // Create new policy
            console.log('Creating new policy with data and files');
            const response = await createPolicyWithFiles(formData);
            
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
        
    } catch (error) {
        console.error('HandlePolicySubmit: Error submitting policy:', error);
        console.error('HandlePolicySubmit: Error stack:', error.stack);
        console.error('HandlePolicySubmit: Error response:', error.response);
        
        if (error.response && error.response.data && error.response.data.errors) {
            console.error('HandlePolicySubmit: Validation errors:', error.response.data.errors);
            const errorMessages = Object.values(error.response.data.errors).flat();
            showNotification(errorMessages, 'error');
        } else if (error.response && error.response.data && error.response.data.message) {
            // Show the specific error message from server
            console.error('HandlePolicySubmit: Server error message:', error.response.data.message);
            showNotification(error.response.data.message, 'error');
        } else if (error.message) {
            // Show the error message
            console.error('HandlePolicySubmit: Error message:', error.message);
            showNotification('Failed to submit policy: ' + error.message, 'error');
        } else {
            showNotification('Failed to submit policy. Please check console for details and try again.', 'error');
        }
    }
};

const handleAgentSubmit = async (e) => {
    e.preventDefault();
    
    // Get form data
    const agentData = {
        name: $('#agentNameInput').val().trim(),
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
    
    // For new agents, password is required
    const editId = $('#agentForm').data('edit-id');
    if (!editId && !password) {
        showNotification('Password is required for new agents', 'error');
        return;
    }
    
    try {
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
                const errorMessages = Object.values(errors).flat();
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
        
        // Check if we're on the dashboard page (Recent Policies table)
        if (window.recentPoliciesData && window.recentPoliciesData.length > 0) {
            // We're on dashboard, update the recent policies table
            updateRecentPoliciesTable(window.recentPoliciesData);
            return;
        }
        
        // Enhanced search for main policies table - search ALL columns
        if (searchTerm === '') {
            filteredData = [...allPolicies];
        } else {
            console.log('Searching for term:', searchTerm);
            filteredData = allPolicies.filter(policy => {
                // Search across ALL possible fields
                const searchableFields = [
                    // Basic info
                    policy.customerName || policy.owner || '',
                    policy.phone || '',
                    policy.customerEmail || policy.email || '',
                    policy.policyType || policy.type || '',
                    policy.vehicleNumber || policy.vehicle || '',
                    policy.vehicleType || '',
                    
                    // Insurance details
                    policy.companyName || policy.company || '',
                    policy.insuranceType || policy.planType || '',
                    policy.startDate || '',
                    policy.endDate || '',
                    policy.status || '',
                    
                    // Financial details
                    (policy.premium || 0).toString(),
                    (policy.payout || 0).toString(),
                    (policy.customerPaidAmount || 0).toString(),
                    (policy.revenue || 0).toString(),
                    
                    // Business details
                    policy.businessType || policy.business_type || '',
                    policy.agentName || policy.agent_name || '',
                    
                    // ID
                    (policy.id || '').toString()
                ];
                
                // Check if any field contains the search term
                return searchableFields.some(field => 
                    field.toString().toLowerCase().includes(searchTerm)
                );
            });
            console.log('Search results:', filteredData.length, 'policies found');
        }
        
        currentPage = 1;
        renderTable();
        updatePagination();
    }, 200); // Reduced delay for more responsive search
};

// Rows per page change
const handleRowsPerPageChange = () => {
    rowsPerPage = parseInt($('#rowsPerPage').val());
    currentPage = 1;
    renderTable();
    updatePagination();
};

// Column mapping function to map table headers to actual data properties
const getColumnProperty = (column) => {
    const columnMap = {
        'id': 'id',
        'type': 'policyType',
        'owner': 'customerName',
        'phone': 'phone',
        'company': 'companyName',
        'startDate': 'startDate',
        'premium': 'premium',
        'status': 'status',
        // Add more mappings as needed
    };
    
    return columnMap[column] || column;
};

// Enhanced sorting functionality with proper data type handling
const handleSort = (column) => {
    // Check if we're on the dashboard page (Recent Policies table)
    if (window.recentPoliciesData && window.recentPoliciesData.length > 0) {
        // Initialize currentSort if not exists
        if (!window.currentSort) {
            window.currentSort = { column: '', direction: 'asc', userClicked: false };
        }
        
        if (window.currentSort.column === column) {
            window.currentSort.direction = window.currentSort.direction === 'asc' ? 'desc' : 'asc';
        } else {
            window.currentSort.column = column;
            window.currentSort.direction = 'asc';
        }
        
        // Mark that user explicitly clicked a column (so it doesn't reset)
        window.currentSort.userClicked = true;
        
        // Update the recent policies table with sorting
        updateRecentPoliciesTable(window.recentPoliciesData);
        updateSortIcons();
        return;
    }
    
    // Enhanced logic for main policies table
    if (currentSort.column === column) {
        currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
    } else {
        currentSort.column = column;
        currentSort.direction = 'asc';
    }
    
    console.log('Sorting by:', column, 'Direction:', currentSort.direction);
    
    filteredData.sort((a, b) => {
        const property = getColumnProperty(column);
        let aVal = a[property] || '';
        let bVal = b[property] || '';
        
        // Special handling for date columns
        if (property === 'startDate' || property === 'endDate') {
            const parseDate = (dateStr) => {
                if (!dateStr) return new Date(0);
                // Handle YYYY-MM-DD format
                if (dateStr.match(/^\d{4}-\d{2}-\d{2}$/)) {
                    return new Date(dateStr);
                }
                // Handle DD-MM-YYYY format
                if (dateStr.match(/^\d{2}-\d{2}-\d{4}$/)) {
                    const parts = dateStr.split('-');
                    return new Date(parts[2], parts[1] - 1, parts[0]);
                }
                return new Date(dateStr);
            };
            
            const dateA = parseDate(aVal);
            const dateB = parseDate(bVal);
            return currentSort.direction === 'asc' ? dateA - dateB : dateB - dateA;
        }
        // Handle different data types properly
        else if (typeof aVal === 'number' && typeof bVal === 'number') {
            // Numeric sorting
            if (currentSort.direction === 'asc') {
                return aVal - bVal;
            } else {
                return bVal - aVal;
            }
        } else if (typeof aVal === 'string' && typeof bVal === 'string') {
            // String sorting (case-insensitive)
            aVal = aVal.toLowerCase();
            bVal = bVal.toLowerCase();
            if (aVal < bVal) return currentSort.direction === 'asc' ? -1 : 1;
            if (aVal > bVal) return currentSort.direction === 'asc' ? 1 : -1;
            return 0;
        } else {
            // Mixed types - convert to string for comparison
            aVal = String(aVal).toLowerCase();
            bVal = String(bVal).toLowerCase();
            if (aVal < bVal) return currentSort.direction === 'asc' ? -1 : 1;
            if (aVal > bVal) return currentSort.direction === 'asc' ? 1 : -1;
            return 0;
        }
    });
    
    renderTable();
    updateSortIcons();
};

// Update sort icons
const updateSortIcons = () => {
    $('.data-table th[data-sort] i').removeClass('fa-sort-up fa-sort-down').addClass('fa-sort');
    
    // Determine which sort state to use
    const sortState = window.currentSort || currentSort;
    
    if (sortState && sortState.column) {
        const currentHeader = $(`.data-table th[data-sort="${sortState.column}"]`);
        const icon = currentHeader.find('i');
        
        icon.removeClass('fa-sort');
        icon.addClass(sortState.direction === 'asc' ? 'fa-sort-up' : 'fa-sort-down');
    }
};

// Chart period change
const handleChartPeriodChange = async () => {
    const period = $('#chartPeriod').val();
    console.log('Chart period changed to:', period);
    
    try {
        // Show loading state
        const chartContainer = $('#barChart').parent();
        chartContainer.addClass('loading');
        
        // Fetch filtered data based on period
        let url = '/api/dashboard/stats';
        const params = new URLSearchParams();
        
        switch(period) {
            case 'month':
                params.append('period', 'current_month');
                break;
            case 'quarter':
                params.append('period', 'current_quarter');
                break;
            case 'fy':
            default:
                params.append('period', 'financial_year');
                break;
        }
        
        if (params.toString()) {
            url += '?' + params.toString();
        }
        
        const response = await fetch(url + '&cache_bust=' + Date.now());
        const data = await response.json();
        
        if (data.chartData && data.chartData.length > 0) {
            // Update charts with filtered data
            updateDashboardCharts(data.chartData, data.policyTypes);
            showNotification(`Chart updated for ${getPeriodLabel(period)}`, 'success');
        } else {
            showNotification('No data available for selected period', 'warning');
        }
        
    } catch (error) {
        console.error('Error updating chart period:', error);
        showNotification('Failed to update chart data', 'error');
    } finally {
        // Remove loading state
        const chartContainer = $('#barChart').parent();
        chartContainer.removeClass('loading');
    }
};
// Helper function to get period label
const getPeriodLabel = (period) => {
    switch(period) {
        case 'month': return 'Current Month';
        case 'quarter': return 'Current Quarter';
        case 'fy': return 'Financial Year';
        default: return 'Financial Year';
    }
};
// Policy actions
const editPolicy = async (id) => {
    console.log('🚀 EditPolicy function called with ID:', id);
    
    // Initialize from local cache if available
    let policy = (Array.isArray(allPolicies) ? allPolicies.find(p => p.id === id) : null) || null;
    // Clear any pending duplicate timers and messages before proceeding
    if (vehicleSearchTimeout) {
        clearTimeout(vehicleSearchTimeout);
        vehicleSearchTimeout = null;
    }
    vehicleDuplicateCheckEnabled = false;
    const vehicleInputPre = document.querySelector('#vehicleNumber');
    if (vehicleInputPre) {
        const msgPre = vehicleInputPre.parentElement.querySelector('.vehicle-validation-message');
        if (msgPre) msgPre.remove();
    }
    
    // If policy not found in local array, fetch it from API
    if (!policy) {
        try {
            console.log('EditPolicy: Policy not found in local array, fetching from API...');
            const response = await apiCall(`/policies/${id}`);
            // Accept multiple response shapes
            if (response && response.policy) {
                policy = response.policy;
            } else if (response && response.data) {
                policy = response.data;
            } else if (response && response.success && response.result) {
                policy = response.result;
            } else {
                console.error('EditPolicy: Unexpected API response shape:', response);
                showNotification('Failed to fetch policy data. Please try again.', 'error');
                return;
            }
            console.log('EditPolicy: Policy fetched from API:', policy);
        } catch (error) {
            console.error('EditPolicy: Error fetching policy:', error);
            showNotification('Failed to fetch policy data. Please try again.', 'error');
            return;
        }
    }
    
    if (policy) {
        console.log('EditPolicy: Policy data received:', policy);
        $('#policyModalTitle').text('Edit Policy');
        $('#savePolicyBtn').text('Update Policy');
        
        // Set form action and method for updating existing policies
        $('#policyForm').attr('action', `/policies/${id}`);
        $('#formMethod').val('PUT');
        
    // Get the correct property names from the API response (support snake_case too)
    const policyType = policy.policyType || policy.type || policy.policy_type || 'Motor';
    const customerName = policy.customerName || policy.owner || policy.customer_name || '';
    const customerPhone = policy.phone || policy.customerPhone || policy.customer_phone || policy.phone_number || '';
    const customerEmail = policy.customerEmail || policy.email || policy.customer_email || '';
    const customerAge = policy.customerAge || policy.customer_age || '';
    const customerGender = policy.customerGender || policy.customer_gender || '';
    const companyName = policy.companyName || policy.company || policy.company_name || '';
    let insuranceType = policy.insuranceType || policy.insurance_type || policy.planType || policy.plan_type || '';
    let businessType = policy.businessType || policy.business_type || 'Self';
    const startDate = policy.startDate || policy.start_date || '';
    const endDate = policy.endDate || policy.end_date || '';
    const premium = policy.premium || '';
    const customerPaidAmount = policy.customerPaidAmount || policy.customer_paid_amount || policy.customerPaid || '';
    const revenue = policy.revenue || '';
    const payout = policy.payout || '';
    const vehicleNumber = policy.vehicleNumber || policy.vehicle_number || '';
    const vehicleType = policy.vehicleType || policy.vehicle_type || '';
    const sumInsured = policy.sumInsured || policy.sum_insured || '';
    const sumAssured = policy.sumAssured || policy.sum_assured || '';
    const policyTerm = policy.policyTerm || policy.policy_term || '';
    const premiumFrequency = policy.premiumFrequency || policy.premium_frequency || '';
        
    // New fields for policy overview
    const statusRaw = policy.status || policy.policy_status || 'Active';
    let agentName = policy.agentName || policy.agent_name || '';
    const agentId = policy.agent_id || policy.agentId;
        
    // Normalize to Self/Agent values
    const normalizedBusinessType = (/agent/i.test(String(businessType)) ? 'Agent' : 'Self');
    // Normalize status casing to match options
    const statusOptions = ['Active','Pending','Expired','Cancelled'];
    const status = statusOptions.find(s => s.toLowerCase() === String(statusRaw).toLowerCase()) || 'Active';
        
        console.log('EditPolicy: Extracted values:', {
            policyType, customerName, customerPhone, customerEmail, 
            companyName, insuranceType, businessType, startDate, endDate,
            premium, customerPaidAmount, revenue, payout, vehicleNumber, vehicleType,
            status, agentName
        });
        
        console.log('EditPolicy: Original policy dates:', {
            startDate: policy.startDate,
            endDate: policy.endDate,
            start_date: policy.start_date,
            end_date: policy.end_date,
            extractedStartDate: startDate,
            extractedEndDate: endDate
        });
        
        // Set global variables for the modal
        selectedPolicyType = policyType;
        selectedBusinessType = businessType;
        
        // Step 1: Set policy type
        $('#policyTypeSelect').val(policyType);
        
    // Step 2: Set business type (normalized)
    $('#businessTypeSelect').val(normalizedBusinessType);
        
        // Set the hidden fields that the form actually sends
    $('#hiddenPolicyType').val(policyType);
    $('#hiddenBusinessType').val(normalizedBusinessType);
    // Agent required for Agent type in edit
    $('#policyModal #agentName').prop('required', normalizedBusinessType === 'Agent');
        
        // Populate policy overview fields
        $('#policyStatus').val(status);
        
        // Ensure agents are loaded, update dropdown, and set selected value
        try {
            if (!allAgents || allAgents.length === 0) {
                allAgents = await fetchAgents();
            }
        } catch (e) {
            console.warn('EditPolicy: Failed to fetch agents, proceeding with existing list');
        }
        updatePolicyAgentDropdown();
        // If agentName still empty, try resolving by agentId
        if (!agentName && agentId && Array.isArray(allAgents)) {
            const match = allAgents.find(a => a.id === agentId || a.userId === agentId);
            if (match && match.name) agentName = match.name;
        }
    // Safely select the agent option, add if missing; also set required if Agent selected
        ensureSelectHasOption('#policyModal #agentName', agentName, agentName);
    $('#policyModal #agentName').prop('required', normalizedBusinessType === 'Agent');
        
        // Store the policy ID for form submission
        $('#policyForm').data('edit-id', id);
        
        console.log('EditPolicy: Hidden fields set:', {
            hiddenPolicyType: $('#hiddenPolicyType').val(),
            hiddenBusinessType: $('#hiddenBusinessType').val()
        });
        
        console.log('EditPolicy: Edit ID set:', $('#policyForm').data('edit-id'));
        
    // Show the modal starting at step 1 to match the add flow
    $('#policyModal').addClass('show');
    $('#step2, #step3').hide();
    $('#step1').show();
    
    // Setup existing documents display
    setupExistingDocuments(policy);
        
    // Enable Next buttons based on pre-filled selections
    $('#nextStep1').prop('disabled', !selectedPolicyType);
    $('#nextStep2').prop('disabled', !selectedBusinessType);
        
    // Sync hidden fields for submit logic
    updateHiddenFields();
        
        // Prepare the correct form so when user reaches step 3 it's ready
        showPolicyForm(policyType);
        
        // Ensure all form sections within the active form are visible
        const $activeForm = $(`#${policyType.toLowerCase()}Form`);
        $activeForm.find('.form-section').show();
        
        // Force the form to be visible and active
        $activeForm.removeClass('d-none').addClass('active').show();
        $activeForm.css('display', 'block');
        
        console.log('EditPolicy: After showPolicyForm call');
        console.log('EditPolicy: Motor form visibility:', $('#motorForm').is(':visible'));
        console.log('EditPolicy: Motor form has active class:', $('#motorForm').hasClass('active'));
        console.log('EditPolicy: Motor form display style:', $('#motorForm').css('display'));
        console.log('EditPolicy: Form sections within active form:', $activeForm.find('.form-section').length);
        console.log('EditPolicy: Form sections visibility:', $activeForm.find('.form-section').map(function() { return $(this).is(':visible'); }).get());
        
        // Populate form fields based on policy type
        if (policyType === 'Motor') {
            console.log('EditPolicy: Populating Motor form fields');
            
            // Temporarily disable auto-calculation during edit mode
            $('#startDate').off('change');
            
            console.log('EditPolicy: About to set dates:', {
                startDate: startDate,
                endDate: endDate,
                startDateType: typeof startDate,
                endDateType: typeof endDate
            });
            
            // Populate motor-specific fields
            $('#vehicleNumber').val(vehicleNumber);
            $('#vehicleType').val(vehicleType);
            $('#customerName').val(customerName);
            $('#customerPhone').val(customerPhone);
            $('#customerEmail').val(customerEmail);
            
            // Ensure dropdowns have the options before setting values
            ensureSelectHasOption('#companyName', companyName, companyName);
            ensureSelectHasOption('#insuranceType', insuranceType, insuranceType);
            
            $('#policyIssueDate').val(policy.policyIssueDate || policy.policy_issue_date || '');
            $('#startDate').val(startDate);
            $('#endDate').val(endDate);
            
            console.log('EditPolicy: After setting dates:', {
                startDateValue: $('#startDate').val(),
                endDateValue: $('#endDate').val()
            });
            $('#premium').val(premium);
            $('#payout').val(payout);
            $('#customerPaidAmount').val(customerPaidAmount);
            $('#revenue').val(revenue);
            
            // Re-enable auto-calculation for new policies
            $('#startDate').on('change', function() {
                // Don't auto-calculate if we're in edit mode
                if ($('#policyModalTitle').text() === 'Edit Policy') {
                    return;
                }
                
                const startDate = new Date($(this).val());
                const endDate = new Date(startDate);
                endDate.setFullYear(endDate.getFullYear() + 1);
                endDate.setDate(endDate.getDate() - 1);
                $('#endDate').val(endDate.toISOString().split('T')[0]);
            });
            
            console.log('EditPolicy: Motor form fields populated');
            console.log('EditPolicy: Customer name field value:', $('#customerName').val());
            console.log('EditPolicy: Customer phone field value:', $('#customerPhone').val());
            console.log('EditPolicy: Company name field value:', $('#companyName').val());
            console.log('EditPolicy: Insurance type field value:', $('#insuranceType').val());
            console.log('EditPolicy: Start date field value:', $('#startDate').val());
            console.log('EditPolicy: End date field value:', $('#endDate').val());
            
        } else if (policyType === 'Health') {
            // Temporarily disable auto-calculation during edit mode
            $('#healthStartDate').off('change');
            
            // Populate health-specific fields
            $('#healthCustomerName').val(customerName);
            $('#healthCustomerPhone').val(customerPhone);
            $('#healthCustomerEmail').val(customerEmail);
            
            // Ensure dropdowns have the options before setting values
            ensureSelectHasOption('#healthCompanyName', companyName, companyName);
            ensureSelectHasOption('#healthPlanType', insuranceType, insuranceType);
            
            $('#healthPolicyIssueDate').val(policy.policyIssueDate || policy.policy_issue_date || '');
            $('#healthStartDate').val(startDate);
            $('#healthEndDate').val(endDate);
            $('#healthPremium').val(premium);
            $('#healthPayout').val(payout);
            $('#healthCustomerPaid').val(customerPaidAmount);
            $('#healthRevenue').val(revenue);
            
            // Re-enable auto-calculation for new policies
            $('#healthStartDate').on('change', function() {
                if ($('#policyModalTitle').text() === 'Edit Policy') {
                    return;
                }
                
                const startDate = new Date($(this).val());
                const endDate = new Date(startDate);
                endDate.setFullYear(endDate.getFullYear() + 1);
                endDate.setDate(endDate.getDate() - 1);
                $('#healthEndDate').val(endDate.toISOString().split('T')[0]);
            });
            
            // Additional health fields if available
            if (customerAge) $('#healthCustomerAge').val(customerAge);
            if (customerGender) $('#healthCustomerGender').val(customerGender);
            if (sumInsured) $('#healthSumInsured').val(sumInsured);
            if (sumAssured) $('#healthSumAssured').val(sumAssured);
            if (policyTerm) $('#healthPolicyTerm').val(policyTerm);
            if (premiumFrequency) $('#healthPremiumFrequency').val(premiumFrequency);
            
        } else if (policyType === 'Life') {
            // Temporarily disable auto-calculation during edit mode
            $('#lifeStartDate').off('change');
            
            // Populate life-specific fields
            $('#lifeCustomerName').val(customerName);
            $('#lifeCustomerPhone').val(customerPhone);
            $('#lifeCustomerEmail').val(customerEmail);
            
            // Ensure dropdowns have the options before setting values
            ensureSelectHasOption('#lifeCompanyName', companyName, companyName);
            ensureSelectHasOption('#lifePlanType', insuranceType, insuranceType);
            
            $('#lifePolicyIssueDate').val(policy.policyIssueDate || policy.policy_issue_date || '');
            $('#lifeStartDate').val(startDate);
            $('#lifeEndDate').val(endDate);
            $('#lifePremium').val(premium);
            $('#lifePayout').val(payout);
            $('#lifeCustomerPaid').val(customerPaidAmount);
            $('#lifeRevenue').val(revenue);
            
            // Re-enable auto-calculation for new policies
            $('#lifeStartDate').on('change', function() {
                if ($('#policyModalTitle').text() === 'Edit Policy') {
                    return;
                }
                
                const startDate = new Date($(this).val());
                const endDate = new Date(startDate);
                endDate.setFullYear(endDate.getFullYear() + 1);
                endDate.setDate(endDate.getDate() - 1);
                $('#lifeEndDate').val(endDate.toISOString().split('T')[0]);
            });
            
            // Additional life fields if available
            if (customerAge) $('#lifeCustomerAge').val(customerAge);
            if (customerGender) $('#lifeCustomerGender').val(customerGender);
            if (sumAssured) $('#lifeSumAssured').val(sumAssured);
            if (policyTerm) $('#lifePolicyTerm').val(policyTerm);
            if (premiumFrequency) $('#lifePremiumFrequency').val(premiumFrequency);
        }
        

        
        // Update step indicators if they exist
        $('.step-indicator').removeClass('active');
        $('.step-indicator[data-step="3"]').addClass('active');
        
        // Form submission is already handled globally - no need for duplicate handlers
        // $('#policyForm').data('edit-listener-added', true);
        
        // Ensure all required fields are visible and populated
        console.log('EditPolicy: Form populated, checking required fields...');
        console.log('Customer Name:', $('#customerName').val() || $('#healthCustomerName').val() || $('#lifeCustomerName').val());
        console.log('Customer Phone:', $('#customerPhone').val() || $('#healthCustomerPhone').val() || $('#lifeCustomerPhone').val());
        console.log('Company Name:', $('#companyName').val() || $('#healthCompanyName').val() || $('#lifeCompanyName').val());
        console.log('Insurance Type:', $('#insuranceType').val() || $('#healthPlanType').val() || $('#lifePlanType').val());
        console.log('Start Date:', $('#startDate').val() || $('#healthStartDate').val() || $('#lifeStartDate').val());
        console.log('End Date:', $('#endDate').val() || $('#healthEndDate').val() || $('#lifeEndDate').val());
        console.log('Premium:', $('#premium').val() || $('#healthPremium').val() || $('#lifePremium').val());
        console.log('Customer Paid:', $('#customerPaidAmount').val() || $('#healthCustomerPaid').val() || $('#lifeCustomerPaid').val());
        
        // Additional debugging for Motor-specific fields
        if (policyType === 'Motor') {
            console.log('EditPolicy: Motor-specific fields:');
            console.log('  Vehicle Number:', $('#vehicleNumber').val());
            console.log('  Vehicle Type:', $('#vehicleType').val());
        }
        
        // Add a small delay to ensure form is properly populated
        setTimeout(() => {
            console.log('EditPolicy: After delay - checking form fields again:');
            if (policyType === 'Motor') {
                console.log('  Motor form - Customer Name:', $('#customerName').val());
                console.log('  Motor form - Customer Phone:', $('#customerPhone').val());
                console.log('  Motor form - Company Name:', $('#companyName').val());
                console.log('  Motor form - Insurance Type:', $('#insuranceType').val());
                console.log('  Motor form - Start Date:', $('#startDate').val());
                console.log('  Motor form - End Date:', $('#endDate').val());
                console.log('  Motor form - Premium:', $('#premium').val());
                console.log('  Motor form - Customer Paid:', $('#customerPaidAmount').val());
                console.log('  Motor form - Vehicle Number:', $('#vehicleNumber').val());
                console.log('  Motor form - Vehicle Type:', $('#vehicleType').val());
            }
        }, 100);
    } else {
        showNotification('Policy not found', 'error');
    }
};

// Renew Policy Function
const renewPolicy = async (id) => {
    console.log('🔄 RenewPolicy function called with ID:', id);
    
    // Fetch policy data
    let policy = (Array.isArray(allPolicies) ? allPolicies.find(p => p.id === id) : null) || null;
    
    if (!policy) {
        try {
            console.log('RenewPolicy: Fetching from API...');
            const response = await apiCall(`/policies/${id}`);
            if (response && response.policy) {
                policy = response.policy;
            } else if (response && response.data) {
                policy = response.data;
            } else {
                console.error('RenewPolicy: Unexpected API response:', response);
                showNotification('Failed to fetch policy data. Please try again.', 'error');
                return;
            }
        } catch (error) {
            console.error('RenewPolicy: Error fetching policy:', error);
            showNotification('Failed to fetch policy data. Please try again.', 'error');
            return;
        }
    }
    
    if (policy) {
        console.log('RenewPolicy: Policy data received:', policy);
        
        // Pre-fill renew modal with existing data
        $('#renewPolicyId').val(policy.id);
        $('#renewPolicyType').val(policy.policyType || policy.policy_type || policy.type);
        
        // Business Type and Agent Selection
        const previousBusinessType = policy.businessType || policy.business_type || 'Self';
        const previousAgentName = policy.agentName || policy.agent_name || '';
        
        // Load agents if not already loaded
        if (!allAgents || allAgents.length === 0) {
            await loadAgentsData();
        }
        
        // Populate agent dropdown
        const renewAgentSelect = $('#renewAgentName');
        renewAgentSelect.find('option:not(:first)').remove();
        (allAgents || []).forEach((agent) => {
            if (agent && agent.name) {
                renewAgentSelect.append(`<option value="${agent.name}">${agent.name}</option>`);
            }
        });
        
        // Pre-select business type
        $('#renewBusinessTypeSelect').val(previousBusinessType);
        
        // Show/hide agent name field based on business type
        if (previousBusinessType === 'Agent') {
            $('#renewAgentNameGroup').show();
            $('#renewAgentName').prop('required', true);
            // Pre-select agent name if available
            if (previousAgentName) {
                // Ensure the option exists in the dropdown, if not add it
                const agentOptionExists = renewAgentSelect.find(`option[value="${CSS.escape(previousAgentName)}"]`).length > 0;
                if (!agentOptionExists && previousAgentName) {
                    renewAgentSelect.append(`<option value="${previousAgentName}">${previousAgentName}</option>`);
                }
                renewAgentSelect.val(previousAgentName);
            }
        } else {
            $('#renewAgentNameGroup').hide();
            $('#renewAgentName').prop('required', false).val('');
        }
        
        // Add event listener for business type change
        $('#renewBusinessTypeSelect').off('change').on('change', function() {
            const selectedBusinessType = $(this).val();
            if (selectedBusinessType === 'Agent') {
                $('#renewAgentNameGroup').show();
                $('#renewAgentName').prop('required', true);
            } else {
                $('#renewAgentNameGroup').hide();
                $('#renewAgentName').prop('required', false).val('');
            }
        });
        
        // Customer info (read-only)
        $('#renewCustomerName').val(policy.customerName || policy.customer_name || policy.owner);
        $('#renewCustomerPhone').val(policy.phone || policy.customerPhone);
        $('#renewCustomerEmail').val(policy.email || policy.customerEmail || '');
        
        // Vehicle info for Motor policies
        const policyType = policy.policyType || policy.policy_type || policy.type;
        if (policyType === 'Motor') {
            $('#renewVehicleSection').show();
            $('#renewVehicleNumber').val(policy.vehicleNumber || policy.vehicle_number || '').prop('required', true);
            $('#renewVehicleType').val(policy.vehicleType || policy.vehicle_type || '').prop('required', true);
        } else {
            $('#renewVehicleSection').hide();
            // Remove required attribute for Health/Life to allow form submission
            $('#renewVehicleNumber').prop('required', false).val('');
            $('#renewVehicleType').prop('required', false).val('');
        }
        
        // Insurance company (editable - can switch)
        $('#renewCompanyName').val(policy.companyName || policy.company_name || policy.company);
        $('#renewInsuranceType').val(policy.insuranceType || policy.insurance_type || '');
        
        const toDateInputValue = (value) => {
            if (!value) return '';
            if (value instanceof Date) {
                return formatLocalDate(value);
            }
            const str = String(value).trim();
            if (str === '') return '';
            if (/^\d{4}-\d{2}-\d{2}$/.test(str)) {
                return str;
            }
            if (/^\d{2}-\d{2}-\d{4}$/.test(str)) {
                const [dd, mm, yyyy] = str.split('-');
                return `${yyyy}-${mm}-${dd}`;
            }
            const parsed = new Date(str);
            return isNaN(parsed.getTime()) ? '' : formatLocalDate(parsed);
        };
        
        const today = new Date();
        const tomorrow = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);
        const nextYear = new Date(tomorrow);
        nextYear.setFullYear(nextYear.getFullYear() + 1);
        nextYear.setDate(nextYear.getDate() - 1);
        
        const rawIssueDate = policy.policyIssueDate || policy.policy_issue_date;
        const rawStartDate = policy.startDate || policy.start_date;
        const rawEndDate = policy.endDate || policy.end_date;
        
        const issueDateValue = toDateInputValue(rawIssueDate) || formatLocalDate(today);
        const startDateValue = toDateInputValue(rawStartDate) || formatLocalDate(tomorrow);
        const endDateValue = toDateInputValue(rawEndDate) || formatLocalDate(nextYear);
        
        $('#renewPolicyIssueDate').val(issueDateValue);
        $('#renewStartDate').val(startDateValue);
        $('#renewEndDate').val(endDateValue);
        
        // Financial details (editable)
        $('#renewPremium').val(policy.premium || '');
        $('#renewPayout').val(policy.payout || '');
        $('#renewCustomerPaidAmount').val(policy.customerPaidAmount || policy.customer_paid_amount || '');
        $('#renewRevenue').val(policy.revenue || '');
        
        // Clear file inputs
        $('#renewPolicyCopy').val('');
        $('#renewRcCopy').val('');
        $('#renewAadharCopy').val('');
        $('#renewPanCopy').val('');
        
        // Show the renew modal (same method as other modals)
        $('#renewPolicyModal').addClass('show');
        console.log('✅ Renew modal displayed with show class');
        
        // Calculate revenue when amounts change (same formula as add policy forms)
        $('#renewPremium, #renewPayout, #renewCustomerPaidAmount').on('input', function() {
            const premium = parseFloat($('#renewPremium').val()) || 0;
            const payout = parseFloat($('#renewPayout').val()) || 0;
            const customerPaid = parseFloat($('#renewCustomerPaidAmount').val()) || 0;
            // Use consistent formula: customerPaid - (premium - payout)
            const revenue = customerPaid - (premium - payout);
            $('#renewRevenue').val(revenue.toFixed(2));
            
            // Apply color styling based on revenue value
            const $revenueInput = $('#renewRevenue');
            if (revenue < 0) {
                $revenueInput.removeClass('revenue-positive').addClass('revenue-negative');
            } else if (revenue > 0) {
                $revenueInput.removeClass('revenue-negative').addClass('revenue-positive');
            } else {
                $revenueInput.removeClass('revenue-positive revenue-negative');
            }
        });
        
        console.log('✅ Renew modal opened successfully');
    } else {
        showNotification('Policy not found', 'error');
    }
};
// Handle Renew Policy Form Submission
const handleRenewPolicySubmit = async (e) => {
    e.preventDefault();
    
    console.log('🔄 HandleRenewPolicySubmit: Form submission started');
    
    const policyId = $('#renewPolicyId').val();
    
    if (!policyId) {
        showNotification('Policy ID is missing', 'error');
        return;
    }
    
    // Create FormData object
    const formData = new FormData();
    
    // Add renewal flag
    formData.append('is_renewal', 'true');
    
    // Add basic fields
    formData.append('policy_type', $('#renewPolicyType').val());
    formData.append('business_type', $('#renewBusinessTypeSelect').val());
    const agentName = $('#renewBusinessTypeSelect').val() === 'Agent' ? $('#renewAgentName').val() : '';
    formData.append('agent_name', agentName);
    formData.append('customerName', $('#renewCustomerName').val());
    formData.append('customerPhone', $('#renewCustomerPhone').val());
    formData.append('customerEmail', $('#renewCustomerEmail').val() || '');
    
    // Vehicle info (if Motor)
    if ($('#renewVehicleSection').is(':visible')) {
        formData.append('vehicleNumber', $('#renewVehicleNumber').val());
        formData.append('vehicleType', $('#renewVehicleType').val());
    }
    
    // Insurance details
    formData.append('companyName', $('#renewCompanyName').val());
    formData.append('insuranceType', $('#renewInsuranceType').val());
    
    // Dates
    formData.append('policyIssueDate', $('#renewPolicyIssueDate').val());
    formData.append('startDate', $('#renewStartDate').val());
    formData.append('endDate', $('#renewEndDate').val());
    
    // Financial details
    formData.append('premium', $('#renewPremium').val());
    formData.append('payout', $('#renewPayout').val() || '0');
    formData.append('customerPaidAmount', $('#renewCustomerPaidAmount').val());
    formData.append('revenue', $('#renewRevenue').val());
    
    // Add document files
    const policyCopyFile = $('#renewPolicyCopy')[0].files[0];
    const rcCopyFile = $('#renewRcCopy')[0].files[0];
    const aadharCopyFile = $('#renewAadharCopy')[0].files[0];
    const panCopyFile = $('#renewPanCopy')[0].files[0];
    
    // Validate: Policy Copy is mandatory for renewal
    if (!policyCopyFile) {
        showNotification('⚠️ Policy Copy is required when renewing a policy', 'error');
        return;
    }
    
    if (policyCopyFile) formData.append('policyCopy', policyCopyFile);
    if (rcCopyFile) formData.append('rcCopy', rcCopyFile);
    if (aadharCopyFile) formData.append('aadharCopy', aadharCopyFile);
    if (panCopyFile) formData.append('panCopy', panCopyFile);
    
    // Log FormData contents
    console.log('HandleRenewPolicySubmit: FormData contents:');
    for (let pair of formData.entries()) {
        console.log('  ' + pair[0] + ':', pair[1]);
    }
    
    try {
        // Show loading notification
        showNotification('Renewing policy...', 'info');
        
        // Submit via API (PUT request to update the policy)
        const response = await fetch(`/policies/${policyId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'X-HTTP-Method-Override': 'PUT'
            },
            body: formData
        });
        
        const result = await response.json();
        
        console.log('HandleRenewPolicySubmit: Response:', result);
        
        if (result.success || response.ok) {
            showNotification('Policy renewed successfully! 🎉', 'success');
            
            // Close the renewal modal (use removeClass to match other modals)
            $('#renewPolicyModal').removeClass('show');
            
            // Reload policy data with current filter
            const currentPolicyType = $('#policyTypeFilter').val() || 'All';
            await loadPoliciesData(currentPolicyType);
            
            // Re-render tables
            renderTable();
            updatePagination();
            
            // Update policies page if active
            if ($('#policies').hasClass('active')) {
                renderPoliciesTable();
                updatePoliciesPagination();
                updatePoliciesStats();
            }
            
            // Update dashboard if active
            if ($('#dashboard').hasClass('active')) {
                await loadDashboardData();
            }
            
            // Refresh the view policy page if we are currently on it
            const viewPageContainer = document.getElementById('policyViewContent');
            if (viewPageContainer && typeof loadPolicyViewPage === 'function') {
                try {
                    await loadPolicyViewPage(parseInt(policyId, 10));
                } catch (refreshError) {
                    console.error('Failed to refresh view policy page after renewal:', refreshError);
                }
            }
            
            console.log('✅ Policy renewal completed successfully');
        } else {
            throw new Error(result.message || 'Failed to renew policy');
        }
    } catch (error) {
        console.error('HandleRenewPolicySubmit: Error:', error);
        showNotification(error.message || 'Failed to renew policy', 'error');
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
        
        // Update dashboard if it's currently active
        if ($('#dashboard').hasClass('active')) {
            await loadDashboardData();
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
    if (!dateString) return '';
    const raw = String(dateString).trim();
    if (raw === '') return '';

    // If already looks like dd-mm-yyyy, return as-is (normalize zeros)
    const ddmmyyyy = /^(\d{1,2})[-\/](\d{1,2})[-\/]?(\d{2,4})$/;
    const ddmmyyyyMatch = raw.match(ddmmyyyy);
    if (ddmmyyyyMatch) {
        const dd = ddmmyyyyMatch[1].padStart(2, '0');
        const mm = ddmmyyyyMatch[2].padStart(2, '0');
        let yyyy = ddmmyyyyMatch[3];
        if (yyyy.length === 2) {
            // Assume 20xx for two-digit years
            yyyy = `20${yyyy}`;
        }
        return `${dd}-${mm}-${yyyy}`;
    }

    // Try parsing common formats safely (yyyy-mm-dd, ISO)
    const isoLike = /^(\d{4})[-\/](\d{1,2})[-\/]?(\d{1,2})/;
    const isoMatch = raw.match(isoLike);
    if (isoMatch) {
        const yyyy = isoMatch[1];
        const mm = isoMatch[2].padStart(2, '0');
        const dd = isoMatch[3].padStart(2, '0');
        return `${dd}-${mm}-${yyyy}`;
    }

    // Fallback to Date parser; if invalid, return raw
    const d = new Date(raw);
    if (isNaN(d.getTime())) return raw;
    const dd = String(d.getDate()).padStart(2, '0');
    const mm = String(d.getMonth() + 1).padStart(2, '0');
    const yyyy = d.getFullYear();
    return `${dd}-${mm}-${yyyy}`;
};

// Format Date object to YYYY-MM-DD for HTML date inputs
const formatLocalDate = (date) => {
    if (!date || !(date instanceof Date) || isNaN(date.getTime())) {
        console.error('formatLocalDate: Invalid date provided', date);
        return '';
    }
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
};

const formatDateTime = (dateString) => {
    if (!dateString) return 'N/A';
    
    const date = new Date(dateString);
    
    // Check if date is valid
    if (isNaN(date.getTime())) {
        return 'Invalid Date';
    }
    
    // Format with IST timezone (UTC+5:30)
    try {
        return date.toLocaleString('en-IN', {
            timeZone: 'Asia/Kolkata',
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        });
    } catch (error) {
        console.error('Date formatting error:', error);
        // Fallback to basic formatting
        return date.toLocaleString('en-IN');
    }
};

const showNotification = (message, type = 'info') => {
    console.log('showNotification called:', message, type);
    
    // Escape HTML to prevent XSS
    const escapeHtml = (str) => String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');

    // Normalize message into either plain text or a list of items
    let items = [];
    let contentHtml = '';

    if (Array.isArray(message)) {
        items = message.filter(Boolean).map(escapeHtml);
    } else if (typeof message === 'string' && /^validation errors[:\-]/i.test(message)) {
        const parts = message.replace(/^validation errors[:\-]\s*/i, '');
        items = parts.split(',').map(s => escapeHtml(s.trim())).filter(Boolean);
    }

    if (items.length > 0) {
        contentHtml = `
            <div class="notification-body">
                <div class="notification-title">Validation errors</div>
                <ul class="notification-list">${items.map(i => `<li>${i}</li>`).join('')}</ul>
            </div>`;
    } else {
        contentHtml = `<div class="notification-body"><span>${escapeHtml(message || '')}</span></div>`;
    }

    const icon = type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle';
    const notification = $(`
        <div class="notification notification-${type}" role="alert" aria-live="polite">
            <i class="fas fa-${icon}"></i>
            ${contentHtml}
            <button class="notification-close" title="Close" aria-label="Close">&times;</button>
        </div>
    `);

    // Ensure a single container exists to stack notifications in the top-right
    let $container = $('#notifications-container');
    if (!$container.length) {
        $container = $('<div id="notifications-container" aria-live="polite" aria-atomic="true"></div>').appendTo('body');
    }

    // Add to container
    $container.append(notification);

    // Show notification
    setTimeout(() => {
        notification.addClass('show');
    }, 50);

    // Auto-hide (longer for errors)
    const DURATION = type === 'error' ? 6000 : 3000;
    let hideTimer = setTimeout(() => hide(), DURATION);

    const hide = () => {
        notification.removeClass('show');
        setTimeout(() => notification.remove(), 300);
    };

    // Close button
    notification.find('.notification-close').on('click', hide);

    // Pause on hover
    notification.on('mouseenter', () => clearTimeout(hideTimer));
    notification.on('mouseleave', () => {
        hideTimer = setTimeout(() => hide(), 1500);
    });
};

// Add notification styles
$('<style>')
    .prop('type', 'text/css')
    .html(`
        /* Fixed container to stack notifications in the top-right */
        #notifications-container {
            position: fixed;
            top: 90px; /* below top nav */
            right: 24px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            z-index: 3000;
            pointer-events: none; /* allow clicks to pass through except on the toasts themselves */
            max-width: calc(100vw - 48px);
        }

        .notification {
            position: relative; /* positioned within the container */
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            padding: 14px 40px 14px 16px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            z-index: 3000;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            width: 420px;
            max-width: 100%;
            max-height: 60vh;
            overflow: auto;
            word-break: break-word;
            white-space: normal;
            pointer-events: auto; /* clickable */
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
            line-height: 1.2;
            margin-top: 2px;
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
        
        .notification .notification-body { flex: 1; }
        .notification span { color: #111827; font-weight: 500; }
        .notification .notification-title { color: #111827; font-weight: 700; margin-bottom: 4px; }
        .notification .notification-list { margin: 6px 0 0 18px; padding: 0; }
        .notification .notification-list li { margin-bottom: 4px; }
        
        .dark-theme .notification span {
            color: #F1F5F9;
        }
        .dark-theme .notification .notification-title { color: #F1F5F9; }
        
        .notification .notification-close {
            position: absolute;
            top: 8px;
            right: 10px;
            background: transparent;
            border: none;
            font-size: 18px;
            line-height: 1;
            cursor: pointer;
            color: #6B7280;
        }
        .dark-theme .notification .notification-close { color: #94A3B8; }
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
        
        // Debug logging for missing dates
        if (!policy.startDate && policy.id) {
            console.log('Missing startDate for policy:', policy.id, policy);
        }
        
        // Use the correct property names from the API response with fallbacks
        const policyType = policy.policyType || policy.type || 'Unknown';
        const customerName = policy.customerName || policy.owner || 'Unknown';
        const phone = policy.phone || 'Unknown';
        const companyName = policy.companyName || policy.company || 'Unknown';
        const startDate = policy.startDate && policy.startDate.trim() !== '' ? policy.startDate : null;
        const premium = policy.premium || 0;
        const status = policy.status || 'Active';
        
        // Extract vehicle information based on policy type
        let vehicleNumber = 'N/A';
        let vehicleType = 'N/A';
        
        if (policyType === 'Motor') {
            vehicleNumber = policy.vehicleNumber || policy.vehicle_number || 'N/A';
            vehicleType = policy.vehicleType || policy.vehicle_type || 'N/A';
        } else if (policyType === 'Health') {
            vehicleNumber = 'Health Policy';
            vehicleType = policy.insuranceType || 'Health Insurance';
        } else if (policyType === 'Life') {
            vehicleNumber = 'Life Policy';
            vehicleType = policy.insuranceType || 'Life Insurance';
        }
        
        // Format additional info based on policy type
        let additionalInfo = '';
        if (policyType === 'Motor' && (policy.vehicleNumber || policy.vehicle)) {
            additionalInfo = policy.vehicleNumber || policy.vehicle || '';
        } else if (policyType === 'Health' && policy.sumInsured) {
            additionalInfo = `Sum: ₹${policy.sumInsured.toLocaleString()}`;
        } else if (policyType === 'Life' && policy.sumAssured) {
            additionalInfo = `Sum: ₹${policy.sumAssured.toLocaleString()}`;
        }
        
        // Format vehicle/details info based on policy type
        let vehicleDetails = '';
        if (policyType === 'Motor') {
            vehicleDetails = policy.vehicleType || policy.vehicle_type || 'N/A';
            if (policy.vehicleNumber || policy.vehicle_number) {
                vehicleDetails += `<br><small style="color: #666;">${policy.vehicleNumber || policy.vehicle_number}</small>`;
            }
        } else if (policyType === 'Health') {
            vehicleDetails = policy.insuranceType || 'Health Insurance';
            if (policy.sumInsured) {
                vehicleDetails += `<br><small style="color: #666;">Sum: ₹${policy.sumInsured.toLocaleString()}</small>`;
            }
        } else if (policyType === 'Life') {
            vehicleDetails = policy.insuranceType || 'Life Insurance';
            if (policy.sumAssured) {
                vehicleDetails += `<br><small style="color: #666;">Sum: ₹${policy.sumAssured.toLocaleString()}</small>`;
            }
        }

        // Ensure we extract string values, not HTML elements
        const safeVehicleNumber = typeof vehicleNumber === 'string' ? vehicleNumber : (vehicleNumber?.value || 'N/A');
        const safeVehicleType = typeof vehicleType === 'string' ? vehicleType : (vehicleType?.value || 'N/A');
        
        row.innerHTML = `
            <td>${startIndex + index + 1}</td>
            <td>${safeVehicleNumber}</td>
            <td>${customerName}</td>
            <td>${phone}</td>
            <td>${safeVehicleType}</td>
            <td style="white-space: nowrap;">${startDate && startDate.trim() !== '' ? formatDate(startDate) : '<span style="color: #999; font-style: italic;">Not set</span>'}</td>
            <td>₹${premium.toLocaleString()}</td>
            <td><span class="status-badge ${status.toLowerCase()}">${status}</span></td>
            <td>
                <div class="action-buttons">
                    <button class="action-btn edit" data-policy-id="${policy.id}" title="Edit Policy">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="action-btn renew" data-policy-id="${policy.id}" title="Renew Policy">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                    <button class="action-btn view" data-policy-id="${policy.id}" title="View Details">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="action-btn delete" data-policy-id="${policy.id}" title="Delete Policy">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        `;
        fragment.appendChild(row);
    });
    
    tbody.empty().append(fragment);
    updatePoliciesPaginationInfo();
    
    // Add event listeners for action buttons
    tbody.find('.action-btn.edit').off('click').on('click', async function() {
        const policyId = parseInt($(this).data('policy-id'));
        await editPolicy(policyId);
    });
    
    tbody.find('.action-btn.renew').off('click').on('click', async function() {
        const policyId = parseInt($(this).data('policy-id'));
        await renewPolicy(policyId);
    });
    
    tbody.find('.action-btn.view').off('click').on('click', function() {
        const policyId = parseInt($(this).data('policy-id'));
        window.location.href = `/policies/${policyId}/view`;
    });
    
    tbody.find('.action-btn.delete').off('click').on('click', function() {
        const policyId = parseInt($(this).data('policy-id'));
        deletePolicyHandler(policyId);
    });
    
    tbody.find('.action-btn.history').off('click').on('click', function() {
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
    console.log('📊 updatePoliciesStats called');
    console.log('📊 Current allPolicies length:', allPolicies.length);

    // Prevent cross-page override: do not touch renewals counters on Renewals page
    const currentPathForPolicies = window.location && window.location.pathname ? window.location.pathname : '';
    if (currentPathForPolicies === '/renewals' || currentPathForPolicies.startsWith('/renewals')) {
        console.log('📊 updatePoliciesStats: Skipping for renewals page');
        return; // The Renewals page owns these counters via its own script
    }

    const activeCount = allPolicies.filter(p => p.status === 'Active').length;
    const expiredCount = allPolicies.filter(p => p.status === 'Expired').length;
    const pendingCount = allPolicies.filter(p => p.status === 'Pending').length;
    const totalCount = allPolicies.length;

    console.log('📊 updatePoliciesStats: Calculated counts:', {
        activeCount, expiredCount, pendingCount, totalCount
    });

    $('#activePoliciesCount').text(activeCount);
    $('#expiredPoliciesCount').text(expiredCount);
    $('#pendingRenewalsCount').text(pendingCount);
    $('#totalPoliciesCount').text(totalCount);

    console.log('📊 updatePoliciesStats: DOM elements updated');
    console.log('  activePoliciesCount:', $('#activePoliciesCount').text());
    console.log('  totalPoliciesCount:', $('#totalPoliciesCount').text());
};

// Update policies stats with data from backend
const updatePoliciesStatsWithData = (stats) => {
    console.log('📊 updatePoliciesStatsWithData called with:', stats);

    // Prevent cross-page override: do not touch renewals counters on Renewals page
    const currentPathForPolicies = window.location && window.location.pathname ? window.location.pathname : '';
    if (currentPathForPolicies === '/renewals' || currentPathForPolicies.startsWith('/renewals')) {
        console.log('📊 updatePoliciesStatsWithData: Skipping for renewals page');
        return;
    }

    if (!stats) {
        console.warn('📊 No stats data provided');
        return;
    }

    const activeCount = stats.activeCount || 0;
    const expiredCount = stats.expiredCount || 0;
    const pendingCount = stats.pendingCount || 0;
    const totalCount = stats.totalCount || 0;

    console.log('📊 updatePoliciesStatsWithData: Using backend counts:', {
        activeCount, expiredCount, pendingCount, totalCount
    });

    $('#activePoliciesCount').text(activeCount);
    $('#expiredPoliciesCount').text(expiredCount);
    $('#pendingRenewalsCount').text(pendingCount);
    $('#totalPoliciesCount').text(totalCount);

    console.log('📊 updatePoliciesStatsWithData: DOM elements updated');
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
            (policy.customerName || policy.owner || '').toLowerCase().includes(searchTerm) ||
            (policy.phone || '').toLowerCase().includes(searchTerm) ||
            (policy.companyName || policy.company || '').toLowerCase().includes(searchTerm) ||
            (policy.policyType || policy.type || '').toLowerCase().includes(searchTerm) ||
            (policy.vehicleNumber || policy.vehicle || '').toLowerCase().includes(searchTerm) ||
            (policy.vehicleType || policy.vehicle_type || '').toLowerCase().includes(searchTerm) ||
            (policy.email || '').toLowerCase().includes(searchTerm) ||
            policy.id.toString().includes(searchTerm);
        
        const matchesType = policyTypeFilter === '' || (policy.policyType || policy.type) === policyTypeFilter;
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
const populatePolicyModal = async (policy) => {
    // Store the current policy ID for the edit button
    window.currentViewingPolicyId = policy.id;
    const $modal = $('#viewPolicyModal');
    
    // Normalize policy data to handle different formats from different pages
    const normalizedPolicy = {
        id: policy.id,
        type: policy.type || policy.policyType || policy.policy_type || 'Unknown',
        status: policy.status || 'Active',
        customerName: policy.customerName || policy.owner || policy.customer_name || 'Unknown',
        phone: policy.phone || 'Unknown',
        email: policy.email || 'Not provided',
        customerAge: policy.customerAge || policy.age || 'Not provided',
        customerGender: policy.customerGender || policy.gender || 'Not provided',
        vehicleNumber: policy.vehicleNumber || policy.vehicle_number || (policy.vehicle ? policy.vehicle.split(' - ')[1] : 'N/A'),
        vehicleType: policy.vehicleType || policy.vehicle_type || (policy.vehicle ? policy.vehicle.split(' - ')[0] : 'N/A'),
        companyName: policy.companyName || policy.company || policy.company_name || 'Unknown',
        insuranceType: policy.insuranceType || policy.insurance_type || 'Not provided',
        planType: policy.planType || policy.plan_type || 'Not provided',
        sumInsured: policy.sumInsured || policy.sum_insured || 0,
        sumAssured: policy.sumAssured || policy.sum_assured || 0,
        policyTerm: policy.policyTerm || policy.policy_term || 'Not provided',
        premiumFrequency: policy.premiumFrequency || policy.premium_frequency || 'Not provided',
        policyIssueDate: policy.policyIssueDate || policy.policy_issue_date || null,
        startDate: policy.startDate || policy.start_date || 'Unknown',
        endDate: policy.endDate || policy.end_date || 'Unknown',
        premium: policy.premium || 0,
        customerPaidAmount: policy.customerPaidAmount || policy.customer_paid_amount || policy.customerPaid || policy.customer_paid || 0,
        revenue: policy.revenue || 0,
        payout: policy.payout || 0,
        businessType: policy.businessType || policy.business_type || 'Not provided',
        agentName: policy.agentName || policy.agent_name || '',
        // Document paths
        policy_copy_path: policy.policy_copy_path || policy.policyCopyPath,
        rc_copy_path: policy.rc_copy_path || policy.rcCopyPath,
        aadhar_copy_path: policy.aadhar_copy_path || policy.aadharCopyPath,
        pan_copy_path: policy.pan_copy_path || policy.panCopyPath,

    };

    // Normalize Business Type to Self/Agent and resolve Agent Name robustly
    const normalizedBusinessType = /agent/i.test(String(normalizedPolicy.businessType)) ? 'Agent' : 'Self';
    let resolvedAgentName = (normalizedPolicy.agentName || '').trim();
    if (resolvedAgentName === '-' || /^not provided$/i.test(resolvedAgentName)) {
        resolvedAgentName = '';
    }

    if (normalizedBusinessType === 'Self') {
        resolvedAgentName = 'Self';
    } else {
        // Try to resolve using agent id identifiers if name missing
        if (!resolvedAgentName) {
            const agentId = policy.agent_id || policy.agentId || policy.user_id || policy.userId || null;
            try {
                if (!allAgents || allAgents.length === 0) {
                    allAgents = await fetchAgents();
                }
            } catch (e) {
                // ignore fetch error; we'll fallback
            }
            if (agentId && Array.isArray(allAgents) && allAgents.length > 0) {
                const match = allAgents.find(a => String(a.id) === String(agentId) || String(a.userId) === String(agentId));
                if (match && match.name) {
                    resolvedAgentName = match.name;
                }
            }
        }
        if (!resolvedAgentName) {
            resolvedAgentName = 'Not provided';
        }
    }

    // Populate modal with normalized policy details
    $modal.find('#viewPolicyId').text(`#${normalizedPolicy.id.toString().padStart(3, '0')}`);
    $modal.find('#viewPolicyType').text(normalizedPolicy.type).removeClass().addClass(`policy-type-badge ${normalizedPolicy.type.toLowerCase()}`);
    $modal.find('#viewPolicyStatus').text(normalizedPolicy.status).removeClass().addClass(`status-badge ${normalizedPolicy.status.toLowerCase()}`);
    
    // Customer Information
    $modal.find('#viewCustomerName').text(normalizedPolicy.customerName);
    $modal.find('#viewCustomerPhone').text(normalizedPolicy.phone);
    $modal.find('#viewCustomerEmail').text(normalizedPolicy.email);
    
    // Show age and gender only for Health and Life policies
    if (normalizedPolicy.type === 'Health' || normalizedPolicy.type === 'Life') {
    $modal.find('#viewCustomerAgeContainer').show();
    $modal.find('#viewCustomerGenderContainer').show();
        $modal.find('#viewCustomerAge').text(normalizedPolicy.customerAge || 'Not provided');
        $modal.find('#viewCustomerGender').text(normalizedPolicy.customerGender || 'Not provided');
    } else {
        $modal.find('#viewCustomerAgeContainer').hide();
        $modal.find('#viewCustomerGenderContainer').hide();
    }
    
    // Vehicle Information (Motor only)
    if (normalizedPolicy.type === 'Motor') {
    $modal.find('#viewVehicleSection').show();
    $modal.find('#viewVehicleNumber').text(normalizedPolicy.vehicleNumber);
    $modal.find('#viewVehicleType').text(normalizedPolicy.vehicleType);
    } else {
    $modal.find('#viewVehicleSection').hide();
    }
    
    // Insurance Information
    $modal.find('#viewCompanyName').text(normalizedPolicy.companyName);
    
    // Show both insurance type and plan type for all policies
    $modal.find('#viewInsuranceTypeContainer').show();
    $modal.find('#viewPlanTypeContainer').show();
    $modal.find('#viewInsuranceType').text(normalizedPolicy.insuranceType);
    $modal.find('#viewPlanType').text(normalizedPolicy.planType);
    
    // Show/hide Health/Life specific fields based on policy type
    if (normalizedPolicy.type === 'Health') {
        // Health: Show Sum Insured, hide Sum Assured
    $modal.find('#viewSumInsuredContainer').show();
        $modal.find('#viewSumAssuredContainer').hide();
        $modal.find('#viewSumInsured').text(normalizedPolicy.sumInsured ? `₹${parseFloat(normalizedPolicy.sumInsured).toLocaleString('en-IN')}` : 'Not provided');
        
        // Health: Show Policy Term and Premium Frequency
        $modal.find('#viewPolicyTermContainer').show();
        $modal.find('#viewPremiumFrequencyContainer').show();
        $modal.find('#viewPolicyTerm').text(normalizedPolicy.policyTerm || 'Not provided');
        $modal.find('#viewPremiumFrequency').text(normalizedPolicy.premiumFrequency || 'Not provided');
    } else if (normalizedPolicy.type === 'Life') {
        // Life: Show Sum Assured, hide Sum Insured
        $modal.find('#viewSumInsuredContainer').hide();
    $modal.find('#viewSumAssuredContainer').show();
        $modal.find('#viewSumAssured').text(normalizedPolicy.sumAssured ? `₹${parseFloat(normalizedPolicy.sumAssured).toLocaleString('en-IN')}` : 'Not provided');
    
        // Life: Show Policy Term and Premium Frequency
    $modal.find('#viewPolicyTermContainer').show();
    $modal.find('#viewPremiumFrequencyContainer').show();
        $modal.find('#viewPolicyTerm').text(normalizedPolicy.policyTerm || 'Not provided');
        $modal.find('#viewPremiumFrequency').text(normalizedPolicy.premiumFrequency || 'Not provided');
    } else {
        // Motor: Hide all these fields
        $modal.find('#viewSumInsuredContainer').hide();
        $modal.find('#viewSumAssuredContainer').hide();
        $modal.find('#viewPolicyTermContainer').hide();
        $modal.find('#viewPremiumFrequencyContainer').hide();
    }
    
    // Dates
    $modal.find('#viewPolicyIssueDate').text(normalizedPolicy.policyIssueDate ? formatDate(normalizedPolicy.policyIssueDate) : 'Not set');
    $modal.find('#viewStartDate').text(formatDate(normalizedPolicy.startDate));
    $modal.find('#viewEndDate').text(formatDate(normalizedPolicy.endDate));
    
    // Financial Information
    $modal.find('#viewPremium').text(`₹${normalizedPolicy.premium.toLocaleString()}`);
    $modal.find('#viewCustomerPaid').text(normalizedPolicy.customerPaidAmount ? `₹${normalizedPolicy.customerPaidAmount.toLocaleString()}` : '₹0');
    $modal.find('#viewRevenue').text(`₹${normalizedPolicy.revenue.toLocaleString()}`);
    $modal.find('#viewPayout').text(normalizedPolicy.payout ? `₹${normalizedPolicy.payout.toLocaleString()}` : '₹0');
    $modal.find('#viewBusinessType').text(normalizedBusinessType);
    $modal.find('#viewAgentName').text(resolvedAgentName);
    

    
    // Show/hide RC copy for Motor policies
    if (normalizedPolicy.type === 'Motor') {
        $modal.find('#rcCopyItem').show();
    } else {
        $modal.find('#rcCopyItem').hide();
    }
    
    // Handle document download buttons
    setupDocumentDownloadButtons(normalizedPolicy);
    
    // Open the modal
    $('#viewPolicyModal').addClass('show');
};

const exportPoliciesData = async () => {
    try {
        console.log('📥 Export function called');
        
        // Show loading state
        const exportBtn = $('#exportPoliciesBtn');
        const originalText = exportBtn.html();
        exportBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Exporting...');
        
        // Ensure filters are applied before export
        applyPoliciesFilters();
        
        // Use the filtered data that's currently displayed
        const dataToExport = policiesFilteredData || [];
        console.log('📥 Exporting filtered data:', dataToExport.length, 'policies');
        console.log('📥 Current filters:', {
            search: $('#policiesSearch').val(),
            policyType: $('#policyTypeFilter').val(),
            status: $('#statusFilter').val()
        });
        
        if (!dataToExport || dataToExport.length === 0) {
            showNotification('No data to export', 'error');
            exportBtn.prop('disabled', false).html(originalText);
            return;
        }
        
        // Generate CSV from filtered data
        const csv = generatePoliciesCSVFromData(dataToExport);
        
        if (!csv || csv.trim() === '') {
            showNotification('No data to export', 'error');
            exportBtn.prop('disabled', false).html(originalText);
            return;
        }
        
        // Get filter info for filename
        const statusFilter = $('#statusFilter').val() || 'all';
        const policyTypeFilter = $('#policyTypeFilter').val() || 'all';
        const filename = `policies_${policyTypeFilter}_${statusFilter}_${new Date().toISOString().split('T')[0]}.csv`;
        
        // Download CSV
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        console.log('✅ Export completed:', dataToExport.length, 'records');
        showNotification(`Export completed! (${dataToExport.length} records)`, 'success');
        
    } catch (error) {
        console.error('❌ Export error:', error);
        console.error('Error stack:', error.stack);
        showNotification('Failed to export policies. Please try again.', 'error');
    } finally {
        // Restore button state
        const exportBtn = $('#exportPoliciesBtn');
        exportBtn.prop('disabled', false).html('<i class="fas fa-download"></i> Export Data');
    }
};

const generatePoliciesCSV = () => {
    const headers = ['Sl. No', 'Policy Type', 'Customer Name', 'Phone', 'Insurance Company', 'Start Date', 'End Date', 'Premium', 'Status'];
    const csvRows = [headers.join(',')];
    const source = Array.isArray(window.reportsPoliciesData) ? window.reportsPoliciesData : policiesFilteredData;
    
    source.forEach((policy, idx) => {
        const row = [
            idx + 1,
            (policy.policyType || policy.type || ''),
            (policy.customerName || policy.owner || ''),
            (policy.phone || ''),
            getShortCompanyName(policy.companyName || policy.company || ''),
            (policy.startDate || ''),
            (policy.endDate || ''),
            (policy.premium || 0),
            (policy.status || '')
        ];
        csvRows.push(row.join(','));
    });
    
    return csvRows.join('\n');
};

// Generate CSV from specific data array (for Policies page export)
const generatePoliciesCSVFromData = (policies) => {
    const escapeValue = (value) => {
        const str = String(value || '');
        // Escape quotes and wrap in quotes if contains comma, quote, or newline
        if (str.includes(',') || str.includes('"') || str.includes('\n')) {
            return `"${str.replace(/"/g, '""')}"`;
        }
        return str;
    };
    
    const headers = [
        'Sl. No',
        'Customer Name',
        'Phone',
        'Email',
        'Policy Type',
        'Vehicle Number',
        'Vehicle Type',
        'Insurance Company',
        'Premium',
        'Revenue',
        'Status',
        'Business Type',
        'Agent Name',
        'Start Date',
        'End Date'
    ];
    
    const csvRows = [
        headers.map(escapeValue).join(',')
    ];
    
    policies.forEach((policy, index) => {
        const row = [
            index + 1,
            escapeValue(policy.customerName || policy.owner || ''),
            escapeValue(policy.phone || ''),
            escapeValue(policy.email || policy.customerEmail || ''),
            escapeValue(policy.policyType || policy.type || ''),
            escapeValue(policy.vehicleNumber || policy.vehicle || ''),
            escapeValue(policy.vehicleType || policy.vehicle_type || ''),
            escapeValue(policy.companyName || policy.company || ''),
            parseFloat(policy.premium || 0),
            parseFloat(policy.revenue || 0),
            escapeValue(policy.status || ''),
            escapeValue(policy.businessType || policy.business_type || ''),
            escapeValue(policy.agentName || policy.agent_name || ''),
            escapeValue(policy.startDate || ''),
            escapeValue(policy.endDate || '')
        ];
        csvRows.push(row.join(','));
    });
    
    return csvRows.join('\n');
};

// Follow-ups Functions
const openFollowupModal = () => {
    $('#followupModalTitle').text('Add Follow Up');
    $('#followupForm')[0].reset();
    // Ensure we're not in edit mode
    $('#followupForm').removeData('edit-id');
    
    // Populate policy dropdown
    const policySelect = $('#followupPolicyId');
    policySelect.empty().append('<option value="">Select Policy (Optional)</option>');
    
    allPolicies.forEach(policy => {
        const id = policy.id;
        const displayName = policy.customerName || policy.owner || policy.customer_name || 'Unknown';
        const displayType = policy.policyType || policy.type || policy.policy_type || 'Unknown';
        policySelect.append(`<option value="${id}">#${String(id).padStart(3, '0')} - ${displayName} (${displayType})</option>`);
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
    // Ensure clean save binding for add flow as well
    $('#saveFollowupBtn').off('click').on('click', function (ev) {
        ev.preventDefault();
        $('#followupForm').trigger('submit');
    });
};

const closeFollowupModal = () => {
    $('#followupModal').removeClass('show');
    $('#followupForm')[0].reset();
    $('#previousNotesSection').hide();
};

const handleFollowupSubmit = async (e) => {
    // Always handle via the form to avoid empty FormData when invoked from a button click
    e.preventDefault();
    const formEl = e && e.target && e.target.tagName === 'FORM' ? e.target : document.getElementById('followupForm');
    const formData = new FormData(formEl);
    
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
        // Refresh the filtered view to include latest changes
        applyFollowupsFilters();
        // If no filters are applied, ensure filtered list mirrors all data
        if (!$('#followupStatusFilter').val() && !$('#followupTypeFilter').val() && !$('#followupsSearch').val()) {
            followupsFilteredData = [...allFollowups];
        }
        renderFollowupsTable();
        updateFollowupsPagination();
        updateFollowupsStats();
    
    } catch (error) {
        console.error('Failed to save followup:', error);
        
        // Handle validation errors
        if (error.response && error.response.data) {
            if (error.response.data.errors) {
                const errors = error.response.data.errors;
                const errorMessages = Object.values(errors).flat();
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
    // Guard against undefined data arrays
    if (!Array.isArray(allFollowups)) allFollowups = [];
    if (!Array.isArray(followupsFilteredData) || followupsFilteredData.length === 0) {
        followupsFilteredData = [...allFollowups];
    }
    const startIndex = (followupsCurrentPage - 1) * followupsRowsPerPage;
    const endIndex = startIndex + followupsRowsPerPage;
    const pageData = followupsFilteredData.slice(startIndex, endIndex);
    
    const tbody = $('#followupsTableBody');
    
    // Use document fragment for better performance
    const fragment = document.createDocumentFragment();
    
    pageData.forEach(followup => {
        const row = document.createElement('tr');
        
        // Get follow-up type class
        const typeClass = (followup.followupType || '').toLowerCase().replace(/\s+/g, '-');
        
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
    
    // Count overdue followups (next followup date is in the past)
    const overdue = followupsFilteredData.filter(f => {
        if (!f.nextFollowupDate) return false;
        const nextDate = new Date(f.nextFollowupDate);
        const now = new Date();
        return nextDate < now && f.status === 'Pending';
    }).length;
    
    // Update stats on page - use the IDs from followups/index.blade.php
    $('#pendingFollowupsCount').text(pending);
    $('#overdueFollowupsCount').text(overdue);
    $('#completedTodayCount').text(completedToday);
    $('#expiringPoliciesCount').text(0); // This will be updated by CRM dashboard script
    
    // Also update old IDs for backwards compatibility
    $('#inProgressFollowupsCount').text(inProgress);
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
    if (!followup) {
        showNotification('Follow-up not found', 'error');
        return;
    }

    // Open modal to ensure selects are populated, then set into edit mode
    openFollowupModal();
    $('#followupModalTitle').text('Edit Follow Up');

    // Populate form fields
    $('#followupCustomerName').val(followup.customerName || '');
    $('#followupPhone').val(followup.phone || '');
    $('#followupEmail').val(followup.email || '');
    if (followup.policyId != null) {
        // Ensure the option exists before selecting
        if (!$('#followupPolicyId option[value="' + followup.policyId + '"]').length) {
            const p = (allPolicies || []).find(pp => (pp.id || 0) === followup.policyId);
            const name = p ? (p.customerName || p.owner || p.customer_name || 'Unknown') : 'Unknown';
            const type = p ? (p.policyType || p.type || p.policy_type || 'Unknown') : 'Unknown';
            $('#followupPolicyId').append(`<option value="${followup.policyId}">#${String(followup.policyId).padStart(3, '0')} - ${name} (${type})</option>`);
        }
        $('#followupPolicyId').val(String(followup.policyId));
    } else {
        $('#followupPolicyId').val('');
    }
    $('#followupType').val(followup.followupType || '');
    $('#followupStatus').val(followup.status || '');
    if (followup.assignedTo) {
        if (!$('#followupAssignedTo option[value="' + followup.assignedTo + '"]').length) {
            $('#followupAssignedTo').append(`<option value="${followup.assignedTo}">${followup.assignedTo}</option>`);
        }
        $('#followupAssignedTo').val(followup.assignedTo);
    } else {
        $('#followupAssignedTo').val('');
    }
    $('#followupPriority').val(followup.priority || '');
    $('#followupNextDate').val(followup.nextFollowupDate || '');
    $('#followupReminderTime').val(followup.reminderTime || '');

    // Store the followup ID for form submission
    $('#followupForm').data('edit-id', id);

    // Ensure the Save button triggers the form submit (binding set globally too)
    $('#saveFollowupBtn').off('click').on('click', function (ev) {
        ev.preventDefault();
        $('#followupForm').trigger('submit');
    });

    // Show previous notes if available
    if (followup.notesHistory && followup.notesHistory.length > 0) {
        showPreviousNotes(followup.notesHistory);
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
    // Ensure agent dropdowns are populated and listeners attached
    updateAgentDropdown();
    updatePolicyAgentDropdown();
    
    // Policy type selection
    $('#policyTypeSelect').change(function() {
        selectedPolicyType = $(this).val();
        // Enable next if a policy type is selected and sync hidden field
        $('#nextStep1').prop('disabled', !selectedPolicyType);
        if (selectedPolicyType) {
            $('#hiddenPolicyType').val(selectedPolicyType);
        }
    });
    
    // Business type selection (now Self/Agent). If Agent is chosen, show/require Agent Name
    console.log('Setting up business type selection handler...');
    console.log('Business type select element exists:', $('#businessTypeSelect').length);
    
    // Test if the element exists before setting up the handler
    if ($('#businessTypeSelect').length > 0) {
        console.log('Business type select found, setting up change handler...');
        $('#businessTypeSelect').off('change').on('change', function() {
            console.log('Business type change event triggered');
            selectedBusinessType = $(this).val();
            console.log('Business type selected:', selectedBusinessType);
            
            // Normalize values to exactly 'Self' or 'Agent'
            if (selectedBusinessType && selectedBusinessType.toLowerCase() === 'agent') {
                selectedBusinessType = 'Agent';
            } else if (selectedBusinessType && selectedBusinessType.toLowerCase() === 'self') {
                selectedBusinessType = 'Self';
            }
            
            console.log('Normalized business type:', selectedBusinessType);
            
            // Enable next if selected
            $('#nextStep2').prop('disabled', !selectedBusinessType);
            console.log('Next Step 2 button disabled:', $('#nextStep2').prop('disabled'));
            console.log('Next Step 2 button element:', $('#nextStep2').length);
            
            if (selectedBusinessType) {
                $('#hiddenBusinessType').val(selectedBusinessType);
            }
            
            // Toggle Agent Name required
            const isAgent = selectedBusinessType === 'Agent';
            $('#policyModal #agentName').prop('required', isAgent);
            console.log('Agent name required:', isAgent);
            
            // Pre-populate options
            updatePolicyAgentDropdown();
        });
        console.log('Business type change handler set up successfully');
    } else {
        console.log('Business type select element NOT found!');
    }
    
    // Step navigation
    $('#nextStep1').click(() => {
        // Ensure hidden policy type is set before moving on
        if (selectedPolicyType) {
            $('#hiddenPolicyType').val(selectedPolicyType);
        }
        goToStep(2);
    });
    // Next Step 2 button handler
    console.log('Setting up Next Step 2 button handler...');
    console.log('Next Step 2 button element exists:', $('#nextStep2').length);
    
    $('#nextStep2').off('click').on('click', function(e) {
        console.log('Next Step 2 button clicked');
        console.log('Event:', e);
        console.log('Selected business type:', selectedBusinessType);
        console.log('Selected policy type:', selectedPolicyType);
        
        // Ensure hidden fields are set before moving to form
        if (selectedBusinessType) {
            $('#hiddenBusinessType').val(selectedBusinessType);
        }
        if (selectedPolicyType) {
            $('#hiddenPolicyType').val(selectedPolicyType);
        }
        
        // Toggle agent requirement
        $('#policyModal #agentName').prop('required', ($('#hiddenBusinessType').val() === 'Agent'));
        
        console.log('Calling goToStep(3)...');
        goToStep(3);
    });
    $('#prevStep2').click(() => goToStep(1));
    $('#prevStep3').click(() => goToStep(2));
    
    // Cancel button
    $('#cancelPolicy').click(closePolicyModal);
};

// Setup modal-specific event handlers
const setupModalEventHandlers = () => {
    console.log('Setting up modal event handlers...');
    
    // Policy type selection
    console.log('Setting up policy type selection handler...');
    $('#policyTypeSelect').off('change').on('change', function() {
        selectedPolicyType = $(this).val();
        console.log('Policy type selected:', selectedPolicyType);
        // Enable next if a policy type is selected and sync hidden field
        $('#nextStep1').prop('disabled', !selectedPolicyType);
        if (selectedPolicyType) {
            $('#hiddenPolicyType').val(selectedPolicyType);
        }
    });
    
    // Business type selection
    console.log('Setting up business type selection handler...');
    console.log('Business type select element exists:', $('#businessTypeSelect').length);
    
    if ($('#businessTypeSelect').length > 0) {
        console.log('Business type select found, setting up change handler...');
        $('#businessTypeSelect').off('change').on('change', function() {
            console.log('Business type change event triggered');
            selectedBusinessType = $(this).val();
            console.log('Business type selected:', selectedBusinessType);
            
            // Normalize values to exactly 'Self' or 'Agent'
            if (selectedBusinessType && selectedBusinessType.toLowerCase() === 'agent') {
                selectedBusinessType = 'Agent';
            } else if (selectedBusinessType && selectedBusinessType.toLowerCase() === 'self') {
                selectedBusinessType = 'Self';
            }
            
            console.log('Normalized business type:', selectedBusinessType);
            
            // Enable next if selected
            $('#nextStep2').prop('disabled', !selectedBusinessType);
            console.log('Next Step 2 button disabled:', $('#nextStep2').prop('disabled'));
            console.log('Next Step 2 button element:', $('#nextStep2').length);
            
            if (selectedBusinessType) {
                $('#hiddenBusinessType').val(selectedBusinessType);
            }
            
            // Toggle Agent Name required
            const isAgent = selectedBusinessType === 'Agent';
            $('#policyModal #agentName').prop('required', isAgent);
            console.log('Agent name required:', isAgent);
            
            // Hide/show agent name field based on business type
            if (selectedBusinessType === 'Self') {
                $('#agentNameGroup').hide(); // Hide the entire field group
                $('#policyModal #agentName').val('').prop('required', false);
                $('#policyModal #agentName').removeAttr('required');
                $('#policyModal #agentName').removeClass('required');
                console.log('Agent name field group hidden for Self');
            } else if (selectedBusinessType === 'Agent') {
                $('#agentNameGroup').show(); // Show the field group
                $('#policyModal #agentName').prop('required', true);
                console.log('Agent name field group shown for Agent');
            }
            
            // Pre-populate options
            updatePolicyAgentDropdown();
        });
        console.log('Business type change handler set up successfully');
    } else {
        console.log('Business type select element NOT found!');
    }
    
    // Next Step 2 button handler
    console.log('Setting up Next Step 2 button handler...');
    console.log('Next Step 2 button element exists:', $('#nextStep2').length);
    
    $('#nextStep2').off('click').on('click', function(e) {
        console.log('Next Step 2 button clicked');
        console.log('Event:', e);
        console.log('Selected business type:', selectedBusinessType);
        console.log('Selected policy type:', selectedPolicyType);
        
        // Ensure hidden fields are set before moving to form
        if (selectedBusinessType) {
            $('#hiddenBusinessType').val(selectedBusinessType);
        }
        if (selectedPolicyType) {
            $('#hiddenPolicyType').val(selectedPolicyType);
        }
        
        // Toggle agent requirement
        const businessType = $('#hiddenBusinessType').val();
        $('#policyModal #agentName').prop('required', (businessType === 'Agent'));
        
        // Hide/show agent name field based on business type
        if (businessType === 'Self') {
            $('#agentNameGroup').hide(); // Hide the entire field group
            $('#policyModal #agentName').val('').prop('required', false);
            $('#policyModal #agentName').removeAttr('required');
            $('#policyModal #agentName').removeClass('required');
            console.log('Agent name field group hidden for Self in step 3');
        } else if (businessType === 'Agent') {
            $('#agentNameGroup').show(); // Show the field group
            $('#policyModal #agentName').prop('required', true);
            console.log('Agent name field group shown for Agent in step 3');
        }
        
        console.log('Calling goToStep(3)...');
        goToStep(3);
    });
    
    console.log('Modal event handlers setup complete');
};

// Keep hidden fields in sync with current step selections
const updateHiddenFields = () => {
    if (selectedPolicyType) {
        $('#hiddenPolicyType').val(selectedPolicyType);
    }
    if (selectedBusinessType) {
        $('#hiddenBusinessType').val(selectedBusinessType);
    }
};

const updateAgentDropdown = () => {
    // With new flow, business type only has Self/Agent; keep function for backward compatibility
    // Nothing to do here now other than ensure select exists
    return;
};

const updatePolicyAgentDropdown = () => {
    const agentNameSelect = $('#policyModal #agentName');
    if (!agentNameSelect.length) return;
    // Helper to render options
    const renderOptions = () => {
        agentNameSelect.find('option:not(:first)').remove();
        (allAgents || []).forEach((agent) => {
            if (agent && agent.name) {
                agentNameSelect.append(`<option value="${agent.name}">${agent.name}</option>`);
            }
        });
    };
    if (!allAgents || allAgents.length === 0) {
        // Try to fetch and then render
        fetchAgents().then(list => {
            if (Array.isArray(list)) {
                allAgents = list;
                renderOptions();
            }
        }).catch(() => {
            // keep silent if fails
        });
    } else {
        renderOptions();
    }
};

// Ensure a select has the option you want to select; if not, add it
const ensureSelectHasOption = (selectSelector, value, label) => {
    const $select = $(selectSelector);
    if (!$select.length) return;
    if (!value) return;
    const exists = $select.find(`option[value="${CSS.escape(value)}"]`).length > 0;
    if (!exists) {
        // Append a temporary option so the selection sticks visually
        $select.append(`<option value="${value}">${label || value}</option>`);
    }
    $select.val(value);
};

const goToStep = (step) => {
    console.log('goToStep called with step:', step);
    
    // Hide all step contents
    $('#step1, #step2, #step3').hide();
    
    // Show current step content
    $(`#step${step}`).show();
    
    currentStep = step;
    console.log('Current step set to:', currentStep);
    
    // Show appropriate form based on policy type
    if (step === 3) {
        console.log('Step 3 reached, showing policy form...');
        // Ensure hidden fields reflect current selections before showing the form
        updateHiddenFields();
        showPolicyForm(selectedPolicyType);
    }
};

const showPolicyForm = (policyType) => {
    console.log('showPolicyForm: Called with policyType:', policyType);
    
    // Hide all forms by removing active class (CSS will handle display)
    $('.policy-form').removeClass('active');
    console.log('showPolicyForm: All forms hidden');
    
    // Disable validation on ALL hidden fields first (including those in hidden forms)
    $('.policy-form input[required], .policy-form select[required]').prop('required', false);
    
    // Show selected form and add active class (CSS will handle display)
    const $selectedForm = $(`#${policyType.toLowerCase()}Form`);
    console.log('showPolicyForm: Selected form element:', $selectedForm);
    console.log('showPolicyForm: Form exists:', $selectedForm.length > 0);
    console.log('showPolicyForm: Total policy forms found:', $('.policy-form').length);
    console.log('showPolicyForm: All policy form IDs:', $('.policy-form').map(function() { return this.id; }).get());
    
    $selectedForm.addClass('active');
    console.log('showPolicyForm: Form should now be visible');
    console.log('showPolicyForm: Form has active class:', $selectedForm.hasClass('active'));
    console.log('showPolicyForm: Form display style:', $selectedForm.css('display'));
    
    // Enable validation only on visible fields in the current form
    $selectedForm.find('input[required], select[required]').prop('required', true);
    
    // Set default dates for the visible form ONLY when adding a new policy.
    // When editing, keep original dates intact.
    const isEditMode = $('#policyModalTitle').text().trim() === 'Edit Policy';
    if (!isEditMode) {
        setDefaultDates(policyType);
    }

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
    
    // Only set the dates if the fields are empty. This prevents overwriting
    // any values that were populated programmatically (e.g., during Edit Policy).
    if (policyType === 'Motor') {
        if (!$('#startDate').val()) { $('#startDate').val(startDate); }
        if (!$('#endDate').val()) { $('#endDate').val(endDate); }
    } else if (policyType === 'Health') {
        if (!$('#healthStartDate').val()) { $('#healthStartDate').val(startDate); }
        if (!$('#healthEndDate').val()) { $('#healthEndDate').val(endDate); }
    } else if (policyType === 'Life') {
        if (!$('#lifeStartDate').val()) { $('#lifeStartDate').val(startDate); }
        if (!$('#lifeEndDate').val()) { $('#lifeEndDate').val(endDate); }
    }
};

// Auto-calculate Revenue field for the active policy form
const setupRevenueAutoCalcForPolicyType = (policyType) => {
    let $premiumInput;
    let $payoutInput;
    let $customerPaidInput;
    let $revenueInput;
    
    // Set up field references based on policy type
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
    }
    
    if (!$premiumInput || !$customerPaidInput || !$revenueInput) return;
    
    // Calculate revenue: Customer Paid - (Premium - Payout)
    const calculateRevenue = () => {
        const premium = parseFloat($premiumInput.val()) || 0;
        const payout = parseFloat($payoutInput.val()) || 0;
        const customerPaid = parseFloat($customerPaidInput.val()) || 0;
        
        const revenue = customerPaid - (premium - payout);
        $revenueInput.val(revenue.toFixed(2));
        
        // Apply color styling based on revenue value
        if (revenue < 0) {
            $revenueInput.removeClass('revenue-positive').addClass('revenue-negative');
            console.log('Revenue negative:', revenue, '- Applied red color');
        } else if (revenue > 0) {
            $revenueInput.removeClass('revenue-negative').addClass('revenue-positive');
            console.log('Revenue positive:', revenue, '- Applied green color');
        } else {
            $revenueInput.removeClass('revenue-positive revenue-negative');
            console.log('Revenue zero:', revenue, '- Removed color classes');
        }
    };
    
    // Attach event listeners
    $premiumInput.off('input').on('input', calculateRevenue);
    $payoutInput.off('input').on('input', calculateRevenue);
    $customerPaidInput.off('input').on('input', calculateRevenue);
    
    // Initial calculation
    calculateRevenue();
};

// Form validation and formatting functions
const setupFormValidations = () => {
    // Vehicle Number - Force capital letters and validate format
    $('#vehicleNumber').off('input').on('input', function() {
        let value = $(this).val().toUpperCase();
        // Remove any non-alphanumeric characters
        value = value.replace(/[^A-Z0-9]/g, '');
        // Limit to 10 characters
        value = value.substring(0, 10);
        $(this).val(value);
    });
    
    // Customer Name - Capitalize first letter of each word
    $('#customerName').off('input').on('input', function() {
        let value = $(this).val();
        // Capitalize first letter of each word
        value = value.replace(/\b\w/g, function(l) { return l.toUpperCase(); });
        $(this).val(value);
    });
    
    // Phone Number - Ensure only digits and limit to 10
    $('#customerPhone').off('input').on('input', function() {
        let value = $(this).val();
        // Remove any non-digit characters
        value = value.replace(/\D/g, '');
        // Limit to 10 digits
        value = value.substring(0, 10);
        $(this).val(value);
    });
    
    // Agent Name requirement based on business type - REMOVED duplicate handler
    // This is now handled in setupModalEventHandlers() to avoid conflicts
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
    
    // Hide all forms by removing active class
    $('.policy-form').removeClass('active');
    
    // Reset form
    $('#policyForm')[0].reset();
    
    // Reset form action and method to default (add mode)
    $('#policyForm').attr('action', '/policies');
    $('#formMethod').val('POST');
    
    // Clear any edit data
    $('#policyForm').removeData('edit-id');
    $('#policyForm').removeData('edit-listener-added');
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
    // Set default status and assignment to match static modal
    $('#renewalStatus').val('Pending');
    $('#renewalAssignedTo').val('');
    // Default notification settings
    $('#renewalEmailNotification').prop('checked', false);
    $('#renewalSMSNotification').prop('checked', false);
    $('#renewalNotificationDays').val('');
    
    // Bind change to auto-fill customer name, policy type, and expiry date
    policySelect.off('change').on('change', function() {
        const pid = parseInt($(this).val());
        const p = (allPolicies || []).find(pp => (pp.id || 0) === pid);
        $('#renewalCustomerName').val(p ? (p.customerName || p.owner || '') : '');
        $('#renewalPolicyType').val(p ? (p.policyType || p.type || '') : '');
        $('#renewalExpiryDate').val(p ? (p.endDate || p.end_date || '') : '');
    });

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
    
    const policyId = parseInt($('#renewalPolicyId').val()) || null;
    const reminderDate = $('#renewalReminderDate').val() || '';
    const priority = $('#renewalPriority').val() || 'Medium';
    const notes = $('#renewalNotes').val() || '';
    
    // Validate minimal modal fields
    if (!policyId || !reminderDate) {
        showNotification('Please select a policy and reminder date.', 'error');
        return;
    }
    
    // Find selected policy and build payload expected by backend
    const policy = (allPolicies || []).find(p => (p.id || 0) === policyId);
    if (!policy) {
        showNotification('Selected policy not found.', 'error');
        return;
    }
    // Ensure the visible field is set for UX
    $('#renewalCustomerName').val(policy.customerName || '');
    
    const endDateStr = policy.endDate || policy.end_date;
    const endDate = endDateStr ? new Date(endDateStr + 'T00:00:00') : null;
    const today = new Date();
    const day0 = new Date(today.getFullYear(), today.getMonth(), today.getDate());
    const msPerDay = 24*60*60*1000;
    const daysLeft = endDate ? Math.floor((endDate.getTime() - day0.getTime())/msPerDay) : 0;
    // Map to server-allowed statuses only: Pending, Completed, Overdue, Scheduled
    // For saving reminders: use Overdue (past), Pending (<=30 days), Scheduled (>30 days)
    let backendStatus = 'Pending';
    if (daysLeft < 0) backendStatus = 'Overdue';
    else if (daysLeft <= 30) backendStatus = 'Pending';
    else backendStatus = 'Scheduled';
    
    const backendPayload = {
        customerName: policy.customerName,
        phone: policy.phone,
        email: policy.email ?? null,
        policyType: policy.policyType,
        currentPremium: isNaN(parseFloat(policy.premium)) ? 0 : parseFloat(policy.premium),
        renewalPremium: isNaN(parseFloat(policy.premium)) ? 0 : parseFloat(policy.premium),
        dueDate: endDateStr || reminderDate,
        status: backendStatus,
        agentName: policy.agentName || 'Self',
        notes: notes
    };
    
    try {
    const editId = $('#renewalForm').data('edit-id');
    const editSource = $('#renewalForm').data('edit-source') || 'policy';
        
    console.log('Submitting renewal data:', backendPayload);
        
        if (editId && editSource === 'api') {
            // Update existing API renewal with full backend payload
            const response = await updateRenewal(editId, backendPayload);
            const index = allRenewals.findIndex(r => r.id === editId);
            if (index !== -1) {
                allRenewals[index] = { ...allRenewals[index], ...response.renewal };
            }
            showNotification('Renewal updated successfully!', 'success');
        } else {
            // Create renewal for a policy-derived entry using backend-required fields
            const response = await createRenewal(backendPayload);
            if (response.renewal) {
                allRenewals.push(response.renewal);
            }
            showNotification('Renewal reminder saved!', 'success');
        }
        
        // Close modal and refresh
        closeRenewalModal();
    // Rebuild policies-derived list to reflect any status/priority changes
    buildRenewalsFromPolicies();
    applyRenewalsFilters();
    renderRenewalsTable();
        updateRenewalsPagination();
        updateRenewalsStats();
        
    } catch (error) {
        console.error('Failed to save renewal:', error);
        
        // Handle validation errors
        if (error.response && error.response.data) {
            if (error.response.data.errors) {
                const errors = error.response.data.errors;
                const errorMessages = Object.values(errors).flat();
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
                    <button class="action-btn edit" data-renewal-id="${renewal.id}" data-policy-id="${policyId}" data-source="${renewal.source || 'policy'}" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="action-btn delete" data-renewal-id="${renewal.id}" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                    <button class="action-btn view" data-renewal-id="${renewal.id}" title="View Details">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="action-btn history" data-policy-id="${policyId}" title="View Policy History">
                        <i class="fas fa-history"></i>
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
        const source = $(this).data('source') || 'policy';
        const policyId = parseInt($(this).data('policy-id')) || null;
        editRenewal(renewalId, source, policyId);
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
    // Prevent legacy renewals logic from overriding the dedicated Renewals page logic
    const currentPathForRenewals = window.location && window.location.pathname ? window.location.pathname : '';
    if (currentPathForRenewals === '/renewals' || currentPathForRenewals.startsWith('/renewals')) {
        return; // The Renewals page script computes and renders its own stats
    }
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
    
    renewalsFilteredData = policiesAsRenewals.filter(renewal => {
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
    // v2: only search affects filtering here; time period handled on the Blade page
    const searchTerm = ($('#renewalsSearch').val() || '').toLowerCase();
    
    renewalsFilteredData = policiesAsRenewals.filter(renewal => {
        if (!searchTerm) return true;
        const idMatch = (renewal.id || '').toString().includes(searchTerm);
        const nameMatch = (renewal.customerName || '').toLowerCase().includes(searchTerm);
        const typeMatch = (renewal.policyType || '').toLowerCase().includes(searchTerm);
        const agentMatch = (renewal.assignedTo || '').toLowerCase().includes(searchTerm);
        return idMatch || nameMatch || typeMatch || agentMatch;
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

// Build renewals view model from policies, sorted by expiry date
const buildRenewalsFromPolicies = () => {
    // Map policies -> renewals VM
    policiesAsRenewals = (allPolicies || []).map((p, idx) => {
        // Compute days left
        const endDateStr = p.endDate || p.end_date;
        const endDate = endDateStr ? new Date(endDateStr + 'T00:00:00') : null;
        const today = new Date();
        const day0 = new Date(today.getFullYear(), today.getMonth(), today.getDate());
        const msPerDay = 24*60*60*1000;
        const daysLeft = endDate ? Math.floor((endDate.getTime() - day0.getTime())/msPerDay) : 0;
        // Status mapping aligned with page filters
        let status = 'Pending';
        if (daysLeft < 0) status = 'Overdue';
        else if (daysLeft <= 7) status = 'Pending';
        else if (daysLeft <= 30) status = 'In Progress';
        else status = 'Completed';
        // Priority
        let priority = 'Low';
        if (daysLeft <= 7) priority = 'High';
        else if (daysLeft <= 30) priority = 'Medium';
        else priority = 'Low';

        return {
            id: idx + 1,
            policyId: p.id,
            customerName: p.customerName,
            policyType: p.policyType,
            expiryDate: endDateStr,
            daysLeft: daysLeft,
            status: status,
            priority: priority,
            assignedTo: p.agentName || 'Unassigned'
        };
    });

    // sort by expiry date asc
    policiesAsRenewals.sort((a, b) => {
        const ad = a.expiryDate || '';
        const bd = b.expiryDate || '';
        return ad.localeCompare(bd);
    });

    // Set as current visible data
    renewalsFilteredData = [...policiesAsRenewals];
    renewalsCurrentPage = 1;
};

// Async variant that ensures policies exist by fetching if needed
const buildRenewalsFromPoliciesAsync = async () => {
    try {
        if (!Array.isArray(allPolicies) || allPolicies.length === 0) {
            allPolicies = await fetchPolicies();
        }
    } catch (e) {
        console.error('Failed to (re)fetch policies for renewals:', e);
        allPolicies = allPolicies || [];
    }
    buildRenewalsFromPolicies();
};

const editRenewal = (id, source = 'policy', policyIdFromBtn = null) => {
    // If coming from policies-derived list, synthesize a renewal-like object for the modal
    let renewal = null;
    if (source === 'policy') {
        const vm = policiesAsRenewals.find(r => r.id === id);
        if (vm) {
            const policy = allPolicies.find(p => p.id === (policyIdFromBtn || vm.policyId));
            renewal = {
                id: vm.id,
                policyId: vm.policyId,
                customerName: policy?.customerName || vm.customerName,
                policyType: policy?.policyType || vm.policyType,
                expiryDate: policy?.endDate || vm.expiryDate,
                priority: vm.priority,
                reminderDate: new Date().toISOString().split('T')[0],
                notes: ''
            };
        }
    } else {
        renewal = allRenewals.find(r => r.id === id);
    }
    if (renewal) {
        $('#renewalModalTitle').text('Edit Renewal Reminder');
        
        // Get the correct property names from the API response with fallbacks
        const policyId = renewal.policyId || renewal.policy_id || '';
        const customerName = renewal.customerName || renewal.customer_name || '';
        const reminderDate = renewal.reminderDate || renewal.reminder_date || '';
        const priority = renewal.priority || 'Medium';
    const status = renewal.status || 'Pending';
    const assignedTo = renewal.assignedTo || renewal.assigned_to || '';
        const notes = renewal.notes || '';
        
        // Populate form fields with the correct field names from the modal
        // Ensure policy options list is present
        const policySelect = $('#renewalPolicyId');
        if (policySelect.children('option').length <= 1) {
            policySelect.empty().append('<option value="">Select Policy</option>');
            allPolicies.forEach(p => {
                const pid = p.id || 0;
                const cname = p.customerName || p.owner || 'Unknown';
                const ptype = p.policyType || p.type || 'Unknown';
                policySelect.append(`<option value="${pid}">#${pid.toString().padStart(3, '0')} - ${cname} (${ptype})</option>`);
            });
        }
    $('#renewalPolicyId').val(policyId).trigger('change');
    $('#renewalCustomerName').val(customerName);
    // Ensure Policy Type and Expiry show immediately for edit
    const pForType = (allPolicies || []).find(pp => (pp.id || 0) === policyId);
    const typeToShow = pForType ? (pForType.policyType || pForType.type || '') : (renewal.policyType || renewal.policy_type || '');
    const expiryToShow = pForType ? (pForType.endDate || pForType.end_date || '') : (renewal.expiryDate || renewal.expiry_date || '');
    $('#renewalPolicyType').val(typeToShow);
    $('#renewalExpiryDate').val(expiryToShow);
        $('#renewalReminderDate').val(reminderDate);
        $('#renewalPriority').val(priority);
    $('#renewalStatus').val(status);
    $('#renewalAssignedTo').val(assignedTo);
        $('#renewalNotes').val(notes);
    // Notifications - default unchecked if not present on the object
    $('#renewalEmailNotification').prop('checked', !!(renewal.emailNotification || renewal.email_notification));
    $('#renewalSMSNotification').prop('checked', !!(renewal.smsNotification || renewal.sms_notification));
    const notifyDays = renewal.notifyBeforeDays || renewal.notify_before_days || '';
    $('#renewalNotificationDays').val(notifyDays);
        
        // Store the renewal ID for form submission
    $('#renewalForm').data('edit-id', id);
    $('#renewalForm').data('edit-source', source);
        
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
    // Prefer the v2 renewals page dataset if present
    let csvContent = '';
    if (window.RENEWALS_V2 === true && Array.isArray(window.renewalsV2Filtered)) {
        csvContent = generateRenewalsCSVFromArray(window.renewalsV2Filtered);
    } else {
        csvContent = generateRenewalsCSV();
    }
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
    const headers = ['Sl. No', 'Customer Name', 'Policy Type', 'Expiry Date', 'Days Left', 'Status', 'Priority', 'Assigned To'];
    const csvRows = [headers.join(',')];
    
    renewalsFilteredData.forEach(renewal => {
        const row = [
            renewal.id,
            renewal.customerName,
            renewal.policyType,
            renewal.expiryDate,
            renewal.daysLeft < 0 ? `${Math.abs(renewal.daysLeft)} days overdue` : `${renewal.daysLeft} days`,
            renewal.status,
            renewal.priority,
            renewal.assignedTo
        ];
        csvRows.push(row.join(','));
    });
    
    return csvRows.join('\n');
}; 

// CSV from v2 array (policies view model)
const generateRenewalsCSVFromArray = (arr) => {
    const headers = ['Sl. No', 'Customer Name', 'Policy Type', 'Expiry Date', 'Days Left', 'Status', 'Priority', 'Assigned To'];
    const csvRows = [headers.join(',')];
    arr.forEach((row, idx) => {
        const isRenewed = !!row.hasRenewal;
        const status = isRenewed ? 'Renewed' : (row.daysLeft < 0 ? 'Overdue' : (row.daysLeft <= 7 ? 'Pending' : (row.daysLeft <= 30 ? 'In Progress' : 'Pending')));
        const priority = row.daysLeft <= 7 ? 'High' : (row.daysLeft <= 30 ? 'Medium' : 'Low');
        const csvRow = [
            idx + 1,
            row.customerName || '',
            row.policyType || '',
            row.endDate || '',
            row.daysLeft < 0 ? `${Math.abs(row.daysLeft)} days overdue` : `${row.daysLeft} days`,
            status,
            priority,
            row.agentName || ''
        ];
        csvRows.push(csvRow.join(','));
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

const generateReports = async () => {
    try {
        // Always fetch policies filtered by start_date from server for the selected range
        const start = $('#reportStartDate').val();
        const end = $('#reportEndDate').val();
        const params = new URLSearchParams();
        if (start) params.append('start', start);
        if (end) params.append('end', end);
        const url = `/api/reports/policies?${params.toString()}`;
        const data = await apiCall(url);
        // Cache filtered dataset for Reports usage and export
        window.reportsPoliciesData = (data && data.policies) ? data.policies : [];
        
        // Update KPIs using server-filtered data
        updateKPIs({ policies: window.reportsPoliciesData, renewals: [], followups: [], agents: [] });
        
        // Render tables based on server-filtered data
        generatePoliciesReport();
        generateRenewalsReport();
        generateFollowupsReport();
        generateAgentsReport();
    } catch (e) {
        console.error('Failed to generate reports:', e);
    }
};

const generatePoliciesReport = () => {
    const tbody = $('#policiesReportTableBody');
    tbody.empty();
    
    // Use server-filtered dataset when available
    const filteredPolicies = Array.isArray(window.reportsPoliciesData) ? window.reportsPoliciesData : (allPolicies || []);
    
    console.log('Reports: Showing', filteredPolicies.length, 'policies out of', allPolicies.length, 'for date range', startDate, 'to', endDate);
    
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
                <td>
                    <span class="policy-type-badge ${policyType.toLowerCase()}">${policyType}</span>
                    <div style="font-size: 11px; color: #666; margin-top: 2px;">${policy.vehicleNumber || policy.vehicle_number || ''}</div>
                </td>
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
        let policies = 0;
        let performance = 0;
        
        // Check if this is "Self" or an actual agent
        if (agent.name === 'Self') {
            // Count policies with businessType 'Self'
            policies = allPolicies.filter(p => (p.businessType === 'Self' || p.business_type === 'Self')).length;
        } else {
            // Count policies assigned to this agent
            policies = allPolicies.filter(p => (p.agentName === agent.name || p.agent_name === agent.name)).length;
        }
        
        // Calculate performance as (agent's policies / total policies) * 100
        const totalPolicies = allPolicies.length;
        performance = totalPolicies > 0 ? ((policies / totalPolicies) * 100).toFixed(1) : 0;
        
        const row = document.createElement('tr');
        
        // Show action buttons only for actual agents, not for "Self"
        const actionButtons = agent.name === 'Self' ? '<td>-</td>' : `
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
        
        row.innerHTML = `
            <td>${startIndex + index + 1}</td>
            <td>${agent.name}</td>
            <td>${agent.phone || '-'}</td>
            <td>${agent.email || '-'}</td>
            <td>${agent.userId || '-'}</td>
            <td><span class="status-badge active">Active</span></td>
            <td>${policies}</td>
            <td><span class="performance-score">${performance}%</span></td>
            ${actionButtons}
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
    console.log('👥 updateAgentsStats called');
    console.log('👥 Current allAgents length:', allAgents.length);
    console.log('👥 Current allPolicies length:', allPolicies.length);
    
    $('#totalAgentsCount').text(allAgents.length);
    $('#activeAgentsCount').text(allAgents.length);
    
    const totalPolicies = allPolicies.length;
    $('#totalPoliciesCount').text(totalPolicies);
    
    const avgPerformance = allAgents.length > 0 ? allAgents.reduce((sum, agent) => {
        const policies = allPolicies.filter(p => p.assignedTo === agent.name).length;
        const performance = Math.floor(Math.random() * 40) + 60;
        return sum + performance;
    }, 0) / allAgents.length : 0;
    
    $('#avgPerformanceCount').text(`${avgPerformance.toFixed(1)}%`);
    
    console.log('👥 updateAgentsStats: Updated DOM elements');
    console.log('  totalAgentsCount:', $('#totalAgentsCount').text());
    console.log('  activeAgentsCount:', $('#activeAgentsCount').text());
    console.log('  totalPoliciesCount:', $('#totalPoliciesCount').text());
    console.log('  avgPerformanceCount:', $('#avgPerformanceCount').text());
};

const handleAgentsSearch = () => {
    const searchTerm = $('#agentsSearch').val().toLowerCase();
    
    // Add "Self" as first agent if there are policies with businessType 'Self'
    const selfPolicies = allPolicies.filter(p => (p.businessType === 'Self' || p.business_type === 'Self'));
    const agentsWithSelf = [...allAgents];
    
    if (selfPolicies.length > 0) {
        agentsWithSelf.unshift({
            id: 0,
            name: 'Self',
            phone: '-',
            email: '-',
            userId: '-',
            status: 'Active'
        });
    }
    
    agentsFilteredData = agentsWithSelf.filter(agent => 
        agent.name.toLowerCase().includes(searchTerm) ||
        (agent.phone && agent.phone.includes(searchTerm)) ||
        (agent.email && agent.email.toLowerCase().includes(searchTerm)) ||
        (agent.userId && agent.userId.toLowerCase().includes(searchTerm))
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
    $('#agentNameInput').val(agent.name || '');
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
                labels: [],
                datasets: [{
                    data: [],
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
                labels: [],
                datasets: [{
                    data: [],
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
                labels: [],
                datasets: [{
                    data: [],
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
    console.log('initializeModals called');
    
    // Agent Modal
    $('#addAgentBtn').off('click').on('click', openAgentModal);
    $('#closeAgentModal, #cancelAgent').off('click').on('click', closeAgentModal);
    $('#saveAgentBtn').off('click').on('click', handleAgentSubmit);
    
    // Policy Modal - Fix for header button
    console.log('Setting up policy modal buttons in initializeModals...');
    console.log('addPolicyBtn found:', $('#addPolicyBtn').length);
    console.log('addPolicyFromPoliciesBtn found:', $('#addPolicyFromPoliciesBtn').length);
    
    // Test if buttons exist and add simple click handler
    if ($('#addPolicyBtn').length > 0) {
        console.log('addPolicyBtn exists, adding click handler');
        $('#addPolicyBtn').off('click').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Add Policy button clicked from initializeModals:', this.id);
            openPolicyModal();
        });
    } else {
        console.log('addPolicyBtn NOT found - checking if it exists in DOM...');
        console.log('All buttons with "add" in ID:', $('[id*="add"]').map(function() { return this.id; }).get());
    }
    
    if ($('#addPolicyFromPoliciesBtn').length > 0) {
        console.log('addPolicyFromPoliciesBtn exists, adding click handler');
        $('#addPolicyFromPoliciesBtn').off('click').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Add Policy button clicked from initializeModals:', this.id);
            openPolicyModal();
        });
    } else {
        console.log('addPolicyFromPoliciesBtn NOT found - checking if it exists in DOM...');
        console.log('All buttons with "policy" in ID:', $('[id*="policy"]').map(function() { return this.id; }).get());
    }
    $('#closePolicyModal, #cancelPolicy').off('click').on('click', closePolicyModal);
    // Remove this duplicate handler - already handled in form submission
    // $('#savePolicyBtn').off('click').on('click', handlePolicySubmit);
    
    // Renew Policy Modal handlers
    $('#closeRenewPolicyModal, #cancelRenewPolicy').off('click').on('click', function() {
        $('#renewPolicyModal').removeClass('show');
    });
    
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
    
    // Global fallback for add policy buttons (in case they're not found during initialization)
    $(document).off('click', '#addPolicyBtn, #addPolicyFromPoliciesBtn').on('click', '#addPolicyBtn, #addPolicyFromPoliciesBtn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        console.log('Add Policy button clicked from global fallback:', this.id);
        openPolicyModal();
    });
    
    console.log('Modals initialized successfully');
    
    // Test modal functionality
    setTimeout(() => {
        console.log('Testing modal functionality...');
        console.log('Policy modal element:', $('#policyModal').length);
        console.log('Policy modal display:', $('#policyModal').css('display'));
        console.log('Policy modal visibility:', $('#policyModal').css('visibility'));
        console.log('Policy modal opacity:', $('#policyModal').css('opacity'));
        
        // Test if we can manually show the modal
        if ($('#policyModal').length > 0) {
            console.log('Policy modal exists, testing manual show...');
            // Uncomment the next line to test if modal can be shown manually
            // $('#policyModal').addClass('show');
            // console.log('Modal should now be visible');
        }
        
        // Test button functionality
        console.log('Testing button functionality...');
        console.log('Next Step 2 button exists:', $('#nextStep2').length);
        console.log('Next Step 2 button disabled:', $('#nextStep2').prop('disabled'));
        console.log('Next Step 2 button visible:', $('#nextStep2').is(':visible'));
        console.log('Next Step 2 button clickable:', !$('#nextStep2').prop('disabled') && $('#nextStep2').is(':visible'));
        
        // Test business type select
        console.log('Business type select exists:', $('#businessTypeSelect').length);
        console.log('Business type select options:', $('#businessTypeSelect option').length);
        
        // Test manual button enable
        console.log('Manually enabling Next Step 2 button for testing...');
        $('#nextStep2').prop('disabled', false);
        console.log('Next Step 2 button disabled after manual enable:', $('#nextStep2').prop('disabled'));
        
        // Test business type selection manually - REMOVED to fix default selection issue
        console.log('Testing business type selection manually...');
        if ($('#businessTypeSelect').length > 0) {
            console.log('Business type select found, but not setting default value');
        }
        
        // Fallback: Check for add policy buttons again and set up if they exist
        console.log('Fallback: Checking for add policy buttons again...');
        if ($('#addPolicyBtn').length > 0) {
            console.log('Fallback: addPolicyBtn found, setting up click handler');
            $('#addPolicyBtn').off('click').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Add Policy button clicked from fallback:', this.id);
                openPolicyModal();
            });
        }
        
        if ($('#addPolicyFromPoliciesBtn').length > 0) {
            console.log('Fallback: addPolicyFromPoliciesBtn found, setting up click handler');
            $('#addPolicyFromPoliciesBtn').off('click').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Add Policy button clicked from fallback:', this.id);
                openPolicyModal();
            });
        }
    }, 2000);
};

// Function to handle form validation before submission
const prepareFormForSubmission = () => {
    // Get the active policy type from hidden fields first, then fallback to visible form detection
    let activePolicyType = $('#hiddenPolicyType').val();
    
    // If no hidden field value, determine it from the currently visible form
    if (!activePolicyType) {
        if ($('#motorForm').hasClass('active')) {
            activePolicyType = 'Motor';
        } else if ($('#healthForm').hasClass('active')) {
            activePolicyType = 'Health';
        } else if ($('#lifeForm').hasClass('active')) {
            activePolicyType = 'Life';
        } else {
            activePolicyType = 'Motor'; // default
        }
    }
    
    console.log('PrepareFormForSubmission: Active policy type:', activePolicyType);
    
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

// Email validation function
const validateEmail = (emailInput) => {
    const email = emailInput.value.trim();
    const errorElement = document.getElementById(emailInput.id + '-error');
    
    // Clear previous error
    if (errorElement) {
        errorElement.textContent = '';
        errorElement.style.display = 'none';
    }
    
    // If email is empty, it's valid (optional field)
    if (email === '') {
        emailInput.classList.remove('error');
        emailInput.classList.remove('valid');
        return true;
    }
    
    // Email validation regex pattern (RFC 5322 compliant)
    const emailPattern = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/;
    
    if (!emailPattern.test(email)) {
        // Show error message
        if (errorElement) {
            errorElement.textContent = 'Please enter a valid email address';
            errorElement.style.display = 'block';
        }
        emailInput.classList.add('error');
        emailInput.classList.remove('valid');
        return false;
    }
    
    // Additional validation checks
    const parts = email.split('@');
    const localPart = parts[0];
    const domainPart = parts[1];
    
    // Check local part length (RFC 5321 limit)
    if (localPart.length > 64) {
        if (errorElement) {
            errorElement.textContent = 'Email local part is too long (max 64 characters)';
            errorElement.style.display = 'block';
        }
        emailInput.classList.add('error');
        emailInput.classList.remove('valid');
        return false;
    }
    
    // Check domain part length (RFC 5321 limit)
    if (domainPart.length > 255) {
        if (errorElement) {
            errorElement.textContent = 'Email domain is too long (max 255 characters)';
            errorElement.style.display = 'block';
        }
        emailInput.classList.add('error');
        emailInput.classList.remove('valid');
        return false;
    }
    
    // Check for common invalid patterns
    if (email.includes('..') || email.includes('--') || email.startsWith('.') || email.endsWith('.')) {
        if (errorElement) {
            errorElement.textContent = 'Email contains invalid characters or patterns';
            errorElement.style.display = 'block';
        }
        emailInput.classList.add('error');
        emailInput.classList.remove('valid');
        return false;
    }
    
    // Check for common disposable email domains (optional - can be customized)
    const disposableDomains = [
        'tempmail.org', 'guerrillamail.com', 'mailinator.com', '10minutemail.com',
        'throwaway.email', 'temp-mail.org', 'fakeinbox.com', 'sharklasers.com'
    ];
    
    const domain = domainPart.toLowerCase();
    if (disposableDomains.some(d => domain.includes(d))) {
        if (errorElement) {
            errorElement.textContent = 'Please use a valid email address (disposable emails not allowed)';
            errorElement.style.display = 'block';
        }
        emailInput.classList.add('error');
        emailInput.classList.remove('valid');
        return false;
    }
    
    // Valid email
    emailInput.classList.remove('error');
    emailInput.classList.add('valid');
    return true;
};

// Real-time email validation
const setupEmailValidation = () => {
    const emailFields = ['customerEmail', 'healthCustomerEmail', 'lifeCustomerEmail'];
    
    emailFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            // Add input event for real-time validation
            field.addEventListener('input', function() {
                if (this.value.trim() !== '') {
                    validateEmail(this);
                } else {
                    // Clear error when field is empty
                    const errorElement = document.getElementById(this.id + '-error');
                    if (errorElement) {
                        errorElement.textContent = '';
                        errorElement.style.display = 'none';
                    }
                    this.classList.remove('error');
                    this.classList.remove('valid');
                }
            });
            
            // Add blur event for validation on focus out
            field.addEventListener('blur', function() {
                validateEmail(this);
            });
            
            // Add focus event to clear validation state
            field.addEventListener('focus', function() {
                this.classList.remove('error');
                this.classList.remove('valid');
            });
        }
    });
};
const validateFileSize = (file, maxSizeMB = 10) => {
    const maxSizeBytes = maxSizeMB * 1024 * 1024;
    if (file && file.size > maxSizeBytes) {
        return `File "${file.name}" is too large. Maximum size is ${maxSizeMB}MB.`;
    }
    return null;
};

// Validate all files before submission
const validateAllFiles = () => {
    // Get the active policy type to determine which file inputs to validate
    const activePolicyType = $('#hiddenPolicyType').val() || 'Motor';
    
    let fileInputs = {};
    
    if (activePolicyType === 'Motor') {
        fileInputs = {
            'policyCopy': $('#policyCopy')[0],
            'rcCopy': $('#rcCopy')[0],
            'aadharCopy': $('#aadharCopy')[0],
            'panCopy': $('#panCopy')[0]
        };
    } else if (activePolicyType === 'Health') {
        fileInputs = {
            'policyCopy': $('#healthPolicyCopy')[0],
            'aadharCopy': $('#healthAadharCopy')[0],
            'panCopy': $('#healthPanCopy')[0]
        };
    } else if (activePolicyType === 'Life') {
        fileInputs = {
            'policyCopy': $('#lifePolicyCopy')[0],
            'aadharCopy': $('#lifeAadharCopy')[0],
            'panCopy': $('#lifePanCopy')[0]
        };
    }
    
    const errors = [];
    
    Object.keys(fileInputs).forEach(key => {
        const fileInput = fileInputs[key];
        if (fileInput && fileInput.files && fileInput.files[0]) {
            const error = validateFileSize(fileInput.files[0]);
            if (error) {
                errors.push(error);
            }
        }
    });
    
    return errors;
};

// Test function to demonstrate file upload error
const testFileUploadError = () => {
    // Create a mock file that exceeds 3MB
    const mockFile = {
        name: 'test-large-file.pdf',
        size: 4 * 1024 * 1024, // 4MB file
        type: 'application/pdf'
    };
    
    // Test the validation
    const error = validateFileSize(mockFile, 3);
    if (error) {
        showNotification(error, 'error');
        console.log('File upload error test:', error);
    }
    
    // Test the 413 error handling
    const mockError = {
        response: {
            status: 413,
            data: {
                message: 'Payload Too Large'
            }
        }
    };
    
    // Simulate the error handling
    if (mockError.response && mockError.response.data) {
        if (mockError.response.status === 413) {
            showNotification('File size too large. Please ensure each file is under 5MB and total upload size is within server limits.', 'error');
        }
    }
};

// Add test function to window for easy access
window.testFileUploadError = testFileUploadError;

// Bulk Upload Functions
const openBulkUploadModal = () => {
    console.log('Opening bulk upload modal...');
    const modal = document.getElementById('bulkUploadModal');
    if (modal) {
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
        console.log('Modal opened successfully');
    } else {
        console.error('Bulk upload modal not found!');
    }
};

const closeBulkUploadModal = () => {
    const modal = document.getElementById('bulkUploadModal');
    if (modal) {
        modal.classList.remove('show');
        document.body.style.overflow = 'auto';
        // Reset form
        document.getElementById('bulkUploadForm').reset();
        document.getElementById('uploadProgress').style.display = 'none';
        document.getElementById('progressFill').style.width = '0%';
        document.getElementById('progressText').textContent = 'Uploading...';
    }
};

const handleBulkUpload = async (e) => {
    e.preventDefault();
    
    const fileInput = document.getElementById('excelFile');
    const file = fileInput.files[0];
    
    if (!file) {
        showNotification('Please select a file to upload', 'error');
        return;
    }
    
    // Validate file type
    const allowedTypes = [
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 
        'application/vnd.ms-excel',
        'text/csv',
        'application/csv'
    ];
    const allowedExtensions = ['.xlsx', '.xls', '.csv'];
    const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
    
    if (!allowedTypes.includes(file.type) && !allowedExtensions.includes(fileExtension)) {
        showNotification('Please select a valid file (.xlsx, .xls, or .csv)', 'error');
        return;
    }
    
    // Validate file size (10MB)
    if (file.size > 10 * 1024 * 1024) {
        showNotification('File size must be less than 10MB', 'error');
        return;
    }
    
    // Show progress bar
    const progressDiv = document.getElementById('uploadProgress');
    const progressFill = document.getElementById('progressFill');
    const progressText = document.getElementById('progressText');
    
    progressDiv.style.display = 'block';
    progressFill.style.width = '0%';
    progressText.textContent = 'Uploading...';
    
    // Disable submit button
    const submitBtn = document.getElementById('submitBulkUpload');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
    
    try {
        // Simulate progress
        let progress = 0;
        const progressInterval = setInterval(() => {
            progress += Math.random() * 20;
            if (progress > 90) progress = 90;
            progressFill.style.width = progress + '%';
        }, 200);
        
        // Create FormData
        const formData = new FormData();
        formData.append('excel_file', file);
        
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        if (!csrfToken) {
            throw new Error('CSRF token not found');
        }
        
        // Make API call
        const response = await fetch('/api/policies/bulk-upload', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });
        
        clearInterval(progressInterval);
        progressFill.style.width = '100%';
        progressText.textContent = 'Processing...';
        
        const result = await response.json();
        
        if (result.success) {
            showNotification(result.message, 'success');
            progressText.textContent = 'Upload Complete!';
            
            // Refresh policies data
            setTimeout(() => {
                loadPoliciesData();
                closeBulkUploadModal();
            }, 1500);
        } else {
            throw new Error(result.message || 'Upload failed');
        }
        
    } catch (error) {
        console.error('Bulk upload error:', error);
        progressText.textContent = 'Upload Failed';
        showNotification(error.message || 'An error occurred during upload', 'error');
    } finally {
        // Re-enable submit button
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-upload"></i> Upload Policies';
        
        // Hide progress after delay
        setTimeout(() => {
            progressDiv.style.display = 'none';
        }, 3000);
    }
};

// Preview file function
const previewFile = async () => {
    const fileInput = document.getElementById('excelFile');
    const file = fileInput.files[0];
    
    if (!file) {
        showNotification('Please select a file to preview', 'error');
        return;
    }
    
    // Validate file type
    const allowedTypes = [
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 
        'application/vnd.ms-excel',
        'text/csv',
        'application/csv'
    ];
    const allowedExtensions = ['.xlsx', '.xls', '.csv'];
    const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
    
    if (!allowedTypes.includes(file.type) && !allowedExtensions.includes(fileExtension)) {
        showNotification('Please select a valid file (.xlsx, .xls, or .csv)', 'error');
        return;
    }
    
    // Validate file size (10MB)
    if (file.size > 10 * 1024 * 1024) {
        showNotification('File size must be less than 10MB', 'error');
        return;
    }
    
    // Show loading state
    const previewBtn = document.getElementById('previewBtn');
    previewBtn.disabled = true;
    previewBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Previewing...';
    
    try {
        // Create FormData
        const formData = new FormData();
        formData.append('excel_file', file);
        
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        if (!csrfToken) {
            throw new Error('CSRF token not found');
        }
        
        // Make API call
        const response = await fetch('/api/policies/bulk-upload/preview', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            displayPreviewResults(result.data);
        } else {
            throw new Error(result.message || 'Preview failed');
        }
        
    } catch (error) {
        console.error('Preview error:', error);
        showNotification(error.message || 'An error occurred during preview', 'error');
    } finally {
        // Re-enable preview button
        previewBtn.disabled = false;
        previewBtn.innerHTML = 'Preview File';
    }
};

// Display preview results
const displayPreviewResults = (data) => {
    // Update stats
    document.getElementById('totalRows').textContent = data.total_rows;
    document.getElementById('validRows').textContent = data.valid_rows;
    document.getElementById('invalidRows').textContent = data.invalid_rows;
    document.getElementById('successRate').textContent = data.success_rate + '%';
    
    // Populate unified preview table
    const tableBody = document.querySelector('#previewTable tbody');
    tableBody.innerHTML = '';

    // Add valid rows first
    data.valid_data.forEach(row => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${row.row}</td>
            <td>${row.policy_type}</td>
            <td>${row.customer_name}</td>
            <td>${row.phone}</td>
            <td>${row.company_name}</td>
            <td><span class="status-badge valid">VALID</span></td>
        `;
        tableBody.appendChild(tr);
    });

    // Then invalid rows
    data.invalid_data.forEach(row => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${row.row}</td>
            <td>${row.policy_type}</td>
            <td>${row.customer_name}</td>
            <td>${row.phone}</td>
            <td>${row.company_name}</td>
            <td><span class="status-badge invalid" title="${row.errors}">INVALID</span></td>
        `;
        tableBody.appendChild(tr);
    });
    
    // Show preview section
    document.getElementById('previewSection').style.display = 'block';
    
    // Show success rate color
    const successRateElement = document.getElementById('successRate');
    if (data.success_rate >= 80) {
        successRateElement.className = 'stat-value valid';
    } else if (data.success_rate >= 50) {
        successRateElement.className = 'stat-value warning';
    } else {
        successRateElement.className = 'stat-value invalid';
    }
};

// Download template function
const downloadTemplate = () => {
    // Create a temporary link element
    const link = document.createElement('a');
    link.href = '/api/policies/template/download';
    link.download = 'policies_template.xlsx';
    link.style.display = 'none';
    
    // Add to document, click, and remove
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
};

// Download CSV template function
const downloadCSVTemplate = () => {
    // Create a temporary link element
    const link = document.createElement('a');
    link.href = '/api/policies/template/download-csv';
    link.download = 'policies_template.csv';
    link.style.display = 'none';
    
    // Add to document, click, and remove
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
};

// Initialize bulk upload event listeners
const initializeBulkUpload = () => {
    console.log('Initializing bulk upload...');
    const bulkUploadBtn = document.getElementById('bulkUploadBtn');
    const closeBulkUploadModalBtn = document.getElementById('closeBulkUploadModal');
    const cancelBulkUploadBtn = document.getElementById('cancelBulkUpload');
    const bulkUploadForm = document.getElementById('bulkUploadForm');
    const downloadTemplateBtn = document.getElementById('downloadTemplateBtn');
    const downloadCSVTemplateBtn = document.getElementById('downloadCSVTemplateBtn');
    const previewBtn = document.getElementById('previewBtn');
    const fileInput = document.getElementById('excelFile');
    
    console.log('Bulk upload button found:', !!bulkUploadBtn);
    console.log('Modal found:', !!document.getElementById('bulkUploadModal'));
    
    if (bulkUploadBtn) {
        bulkUploadBtn.addEventListener('click', openBulkUploadModal);
        console.log('Bulk upload button event listener added');
    } else {
        console.error('Bulk upload button not found!');
    }
    
    if (downloadTemplateBtn) {
        downloadTemplateBtn.addEventListener('click', downloadTemplate);
    }
    
    if (downloadCSVTemplateBtn) {
        downloadCSVTemplateBtn.addEventListener('click', downloadCSVTemplate);
    }
    
    if (closeBulkUploadModalBtn) {
        closeBulkUploadModalBtn.addEventListener('click', closeBulkUploadModal);
    }
    
    if (cancelBulkUploadBtn) {
        cancelBulkUploadBtn.addEventListener('click', closeBulkUploadModal);
    }
    
    if (bulkUploadForm) {
        bulkUploadForm.addEventListener('submit', handleBulkUpload);
    }
    
    if (previewBtn) {
        previewBtn.addEventListener('click', previewFile);
    }
    
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                previewBtn.style.display = 'inline-block';
                // Hide preview section when new file is selected
                document.getElementById('previewSection').style.display = 'none';
            } else {
                previewBtn.style.display = 'none';
            }
        });
    }
    
    // Close modal when clicking outside
    const modal = document.getElementById('bulkUploadModal');
    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeBulkUploadModal();
            }
        });
    }
};
// Policy History Functions
function openPolicyHistoryModal(policyId) {
    console.log('Opening policy history modal for ID:', policyId);
    console.log('Current timestamp:', new Date().toISOString());
    const modal = document.getElementById('policyHistoryModal');
    const content = document.getElementById('policyHistoryContent');
    
    if (!modal) {
        console.error('Policy history modal not found!');
        alert('Policy history modal not found. Please refresh the page.');
        return;
    }
    
    console.log('Modal element found:', modal);
    console.log('Modal current style:', modal.getAttribute('style'));
    
    // Force show with multiple methods
    modal.style.display = 'block !important';
    modal.style.visibility = 'visible !important';
    modal.style.opacity = '1 !important';
    modal.setAttribute('style', 'display: block !important; position: fixed !important; z-index: 99999 !important; left: 0 !important; top: 0 !important; width: 100% !important; height: 100% !important; background-color: rgba(0,0,0,0.8) !important; visibility: visible !important; opacity: 1 !important;');
    
    console.log('Modal display set to block, should be visible now');
    console.log('Modal after style change:', modal.getAttribute('style'));
    content.innerHTML = '<div class="loading">Loading policy history...</div>';
    
    // Fetch policy history with aggressive cache busting
    const timestamp = Date.now();
    const randomId = Math.random().toString(36).substring(7);
    fetch(`/api/policies/${policyId}/history?t=${timestamp}&r=${randomId}&cache_bust=${timestamp}&version_cleanup_fix=${timestamp}`, {
        method: 'GET',
        credentials: 'same-origin',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'Cache-Control': 'no-cache, no-store, must-revalidate',
            'Pragma': 'no-cache',
            'Expires': '0'
        }
    })
        .then(response => {
            console.log('API response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Policy history data:', data);
            console.log('Number of versions received:', data.versions ? data.versions.length : 0);
            console.log('First version details:', data.versions && data.versions[0] ? data.versions[0] : 'No versions');
            if (data.policy && data.versions) {
                renderPolicyHistory(data);
            } else {
                content.innerHTML = '<div class="error">Failed to load policy history</div>';
            }
        })
        .catch(error => {
            console.error('Error fetching policy history:', error);
            content.innerHTML = '<div class="error">Error loading policy history: ' + error.message + '</div>';
        });
}

function closePolicyHistoryModal() {
    const modal = document.getElementById('policyHistoryModal');
    modal.style.display = 'none';
}

function renderPolicyHistory(data) {
    const content = document.getElementById('policyHistoryContent');
    const { policy, versions } = data;
    
    if (!versions || versions.length === 0) {
        content.innerHTML = `
            <div class="policy-info">
                <h3>${policy.customer_name} - ${policy.vehicle_number || 'N/A'}</h3>
                <p>No version history available for this policy.</p>
            </div>
        `;
        return;
    }
    
    // Sort versions by version_created_at in descending order (newest first)
    // This ensures the latest version appears first and is marked as "Current"
    const sortedVersions = [...versions].sort((a, b) => {
        return new Date(b.version_created_at) - new Date(a.version_created_at);
    });
    
    let html = `
        <div class="policy-info">
            <h3>${policy.customer_name} - ${policy.vehicle_number || 'N/A'}</h3>
            <p class="policy-type-badge ${policy.policy_type.toLowerCase()}">${policy.policy_type}</p>
        </div>
        <div class="history-timeline">
    `;
    
    sortedVersions.forEach((version, index) => {
        const isLatest = index === 0;
        html += `
            <div class="history-item ${isLatest ? 'latest' : ''}">
                <div class="history-marker">
                    <i class="fas ${isLatest ? 'fa-star' : 'fa-circle'}"></i>
                </div>
                <div class="history-content">
                    <div class="history-header">
                        <h4>${version.version_label} ${isLatest ? '(Current)' : ''}</h4>
                        <span class="history-date">${formatDateTime(version.version_created_at)}</span>
                    </div>
                    <div class="history-details">
                        <div class="detail-grid">
                            <div class="detail-item">
                                <label>Period:</label>
                                <span>${version.policy_period}</span>
                            </div>
                            <div class="detail-item">
                                <label>Company:</label>
                                <span>${version.company_name}</span>
                            </div>
                            <div class="detail-item">
                                <label>Insurance Type:</label>
                                <span>${version.insurance_type}</span>
                            </div>
                            <div class="detail-item">
                                <label>Premium:</label>
                                <span>₹${parseFloat(version.premium).toLocaleString()}</span>
                            </div>
                            <div class="detail-item">
                                <label>Payout:</label>
                                <span>₹${parseFloat(version.payout).toLocaleString()}</span>
                            </div>
                            <div class="detail-item">
                                <label>Customer Paid:</label>
                                <span>₹${parseFloat(version.customer_paid_amount).toLocaleString()}</span>
                            </div>
                            <div class="detail-item">
                                <label>Revenue:</label>
                                <span>₹${parseFloat(version.revenue).toLocaleString()}</span>
                            </div>
                            <div class="detail-item">
                                <label>Status:</label>
                                <span class="status-badge ${version.status.toLowerCase()}">${version.status}</span>
                            </div>
                        </div>
                        <div class="documents-section">
                            <h5>📄 Documents Available:</h5>
                            <div class="document-list">
                                ${version.has_documents ? 
                                    Object.entries(version.documents).map(([type, path]) => {
                                        if (path) {
                                            const documentType = type.replace('_copy', '');
                                            const displayName = documentType.charAt(0).toUpperCase() + documentType.slice(1);
                                            return `
                                                <div class="document-item downloadable">
                                                    <i class="fas fa-file-pdf"></i>
                                                    <span class="doc-name">${displayName}</span>
                                                    <a href="/api/policy-versions/${version.id}/download/${documentType}" 
                                                       class="download-btn" 
                                                       target="_blank" 
                                                       title="Download ${displayName}">
                                                        <i class="fas fa-download"></i>
                                                        Download
                                                    </a>
                                                </div>
                                            `;
                                        }
                                        return '';
                                    }).filter(Boolean).join('') || '<div class="document-item no-docs"><i class="fas fa-exclamation-circle"></i> No documents available for this version</div>'
                                    : '<div class="document-item no-docs"><i class="fas fa-exclamation-circle"></i> No documents available for this version</div>'
                                }
                            </div>
                        </div>
                        ${version.notes ? `
                            <div class="notes-section">
                                <h5>Notes:</h5>
                                <p>${version.notes}</p>
                            </div>
                        ` : ''}
                        ${version.created_by ? `
                            <div class="created-by">
                                <small>Updated by: ${version.created_by}</small>
                            </div>
                        ` : ''}
                    </div>
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    content.innerHTML = html;
}

// Event delegation for history buttons
$(document).on('click', '.action-btn.history', function(e) {
    e.preventDefault();
    const policyId = $(this).data('policy-id');
    console.log('History button clicked for policy ID:', policyId);
    openPolicyHistoryModal(policyId);
});

// Close modal when clicking outside
$(document).on('click', '#policyHistoryModal', function(e) {
    if (e.target === this) {
        closePolicyHistoryModal();
    }
});

// Test function to debug modal
function testModal() {
    console.log('Testing modal manually...');
    openPolicyHistoryModal(1);
}

// Make test function available globally
window.testModal = testModal;
window.openPolicyHistoryModal = openPolicyHistoryModal;
window.closePolicyHistoryModal = closePolicyHistoryModal;

// Add bulk upload initialization to the main initialization
document.addEventListener('DOMContentLoaded', function() {
    initializeApplication();
    setupEmailValidation();
    
    // Ensure navbar date/time is initialized (final fallback)
    setTimeout(() => {
        initializeNavDateTime();
    }, 100);
    
    // Test if modal exists after DOM is loaded
    setTimeout(() => {
        const modal = document.getElementById('policyHistoryModal');
        console.log('Modal check after DOM loaded:', modal ? 'FOUND' : 'NOT FOUND');
        if (modal) {
            console.log('Modal HTML:', modal.outerHTML.substring(0, 200) + '...');
        }
    }, 1000);
    
    // Initialize Business Analytics page if present
    if ($('#businessAnalytics').length > 0) {
        initializeBusinessAnalytics();
    }
    
    // Set up event handler using event delegation (works even if element loads later)
    $(document).on('change', '#periodSelector', function() {
        console.log('📅 Period selector changed:', $(this).val());
        if (typeof loadBusinessAnalytics === 'function') {
            loadBusinessAnalytics();
        } else {
            console.error('loadBusinessAnalytics function not found');
        }
    });
});

// ==================== BUSINESS ANALYTICS FUNCTIONS ====================

// Chart instances for business analytics
let businessCharts = {
    revenueTrend: null,
    policyDistribution: null,
    businessType: null,
    topCompanies: null,
    monthlyGrowth: null
};

// Initialize Business Analytics Page
const initializeBusinessAnalytics = async () => {
    console.log('🚀 Initializing Business Analytics page...');
    
    try {
        // Wait a bit to ensure DOM is ready
        await new Promise(resolve => setTimeout(resolve, 100));
        
        // Load all data first
        await loadBusinessAnalytics();
        
        // Setup export button
        const exportBtn = $('#exportBusinessReport');
        if (exportBtn.length > 0) {
            exportBtn.off('click');
            exportBtn.on('click', exportBusinessReport);
            console.log('✅ Export button event handler attached');
        }
        
        console.log('✅ Business Analytics initialized');
        
    } catch (error) {
        console.error('Failed to initialize business analytics:', error);
        showNotification('Failed to load business analytics', 'error');
    }
};

// Load all business analytics data
const loadBusinessAnalytics = async () => {
    try {
        console.log('📊 Loading business analytics data...');
        
        const period = $('#periodSelector').val() || 'year';
        
        // Calculate date range based on period
        const getDateRange = (period) => {
            const today = new Date();
            // Set end date to end of today (23:59:59)
            const endDate = new Date(today.getFullYear(), today.getMonth(), today.getDate(), 23, 59, 59);
            let startDate = null;
            
            switch(period) {
                case 'month':
                    // This month: Start of current month to end of today
                    startDate = new Date(today.getFullYear(), today.getMonth(), 1, 0, 0, 0);
                    break;
                case 'quarter':
                    // This quarter: Start of current quarter to end of today
                    const quarter = Math.floor(today.getMonth() / 3);
                    startDate = new Date(today.getFullYear(), quarter * 3, 1, 0, 0, 0);
                    break;
                case '6months':
                    // Last 6 months: 6 months ago to end of today
                    startDate = new Date(today.getFullYear(), today.getMonth() - 5, 1, 0, 0, 0);
                    break;
                case 'year':
                    // Financial Year: April 1 to March 31 (current financial year)
                    const currentMonth = today.getMonth(); // 0-11 (Jan=0, Dec=11)
                    const currentYear = today.getFullYear();
                    if (currentMonth >= 3) {
                        // April to December: Current financial year started April 1 of current year
                        startDate = new Date(currentYear, 3, 1, 0, 0, 0); // April 1
                    } else {
                        // January to March: Current financial year started April 1 of previous year
                        startDate = new Date(currentYear - 1, 3, 1, 0, 0, 0); // April 1 of previous year
                    }
                    break;
                case 'all':
                    // All time: No date filter
                    startDate = null;
                    endDate = null;
                    break;
                default: // 12months
                    // Last 12 months
                    startDate = new Date(today.getFullYear(), today.getMonth() - 11, 1, 0, 0, 0);
            }
            
            return {
                start_date: startDate ? startDate.toISOString().split('T')[0] : null,
                end_date: endDate ? endDate.toISOString().split('T')[0] : null
            };
        };
        
        const dateRange = getDateRange(period);
        console.log('📅 Period selected:', period);
        console.log('📅 Date range calculated:', dateRange);
        console.log('📅 Start date:', dateRange.start_date);
        console.log('📅 End date:', dateRange.end_date);
        
        // Build query parameters
        const overviewParams = dateRange.start_date && dateRange.end_date 
            ? `?start_date=${dateRange.start_date}&end_date=${dateRange.end_date}` 
            : '';
        
        // Build params for all API calls that need date filtering
        const dateParams = dateRange.start_date && dateRange.end_date 
            ? `?start_date=${dateRange.start_date}&end_date=${dateRange.end_date}` 
            : '';
        
        console.log('📊 API URLs:', {
            overview: `/api/business/overview${overviewParams}`,
            distribution: `/api/business/policy-distribution${dateParams}`,
            period: period
        });
        
        // Fetch all data in parallel for better performance
        const [
            overview,
            revenueTrend,
            distribution,
            businessType,
            agents,
            companies,
            profitability,
            growth,
            renewals
        ] = await Promise.all([
            apiCall(`/api/business/overview${overviewParams}`).catch(err => {
                console.error('❌ Overview API error:', err);
                throw err;
            }),
            apiCall(`/api/business/revenue-trend?period=${period}`).catch(err => {
                console.error('❌ Revenue trend API error:', err);
                throw err;
            }),
            apiCall(`/api/business/policy-distribution${dateParams}`).catch(err => {
                console.error('❌ Policy distribution API error:', err);
                throw err;
            }),
            apiCall(`/api/business/business-type-performance${dateParams}`).catch(err => {
                console.error('❌ Business type API error:', err);
                throw err;
            }),
            apiCall(`/api/business/agent-performance${dateParams}`).catch(err => {
                console.error('❌ Agent performance API error:', err);
                throw err;
            }),
            apiCall(`/api/business/top-companies${dateParams}`).catch(err => {
                console.error('❌ Top companies API error:', err);
                throw err;
            }),
            apiCall(`/api/business/profitability-breakdown${dateParams}`).catch(err => {
                console.error('❌ Profitability API error:', err);
                throw err;
            }),
            apiCall(`/api/business/monthly-growth${dateParams}`).catch(err => {
                console.error('❌ Monthly growth API error:', err);
                throw err;
            }),
            apiCall('/api/business/renewal-opportunities').catch(err => {
                console.error('❌ Renewal opportunities API error:', err);
                throw err;
            }),
        ]);

        console.log('✅ All business data loaded');

        // Update all sections
        updateBusinessKPIs(overview.kpis);
        initializeRevenueTrendChart(revenueTrend.chartData);
        initializePolicyDistributionChart(distribution.distribution);
        initializeBusinessTypeChart(businessType.performance);
        initializeTopCompaniesChart(companies.companies);
        initializeMonthlyGrowthChart(growth.growthData);
        updateProfitabilityTable(profitability.breakdown, profitability.total);
        updateAgentPerformanceTable(agents.agents);
        updateRenewalOpportunities(renewals.opportunities);
        
        console.log('✅ Business Analytics page fully loaded');
        
    } catch (error) {
        console.error('❌ Failed to load business analytics:', error);
        console.error('Error details:', error.message, error.stack);
        showNotification('Failed to load business data. Please try again.', 'error');
    }
};

// Update KPI Cards
const updateBusinessKPIs = (kpis) => {
    console.log('📊 Updating KPIs:', kpis);
    
    // Total Business Value
    $('#kpiTotalRevenue').text(`₹${kpis.totalRevenue.toLocaleString('en-IN')}`);
    $('#kpiTotalPremium').text(`₹${kpis.totalPremium.toLocaleString('en-IN')}`);
    
    const revenueGrowth = kpis.revenueGrowth || 0;
    const revenueGrowthEl = $('#kpiRevenueGrowth');
    revenueGrowthEl.removeClass('positive negative');
    revenueGrowthEl.addClass(revenueGrowth >= 0 ? 'positive' : 'negative');
    revenueGrowthEl.html(`<i class="fas fa-arrow-${revenueGrowth >= 0 ? 'up' : 'down'}"></i> ${Math.abs(revenueGrowth).toFixed(1)}%`);
    
    // Active Business
    $('#kpiActivePolicies').text(kpis.activePolicies);
    
    const policyGrowth = kpis.policyGrowth || 0;
    const policyGrowthEl = $('#kpiPolicyGrowth');
    policyGrowthEl.removeClass('positive negative');
    policyGrowthEl.addClass(policyGrowth >= 0 ? 'positive' : 'negative');
    policyGrowthEl.html(`<i class="fas fa-arrow-${policyGrowth >= 0 ? 'up' : 'down'}"></i> ${Math.abs(policyGrowth).toFixed(1)}%`);
    
    // Profit Margin
    $('#kpiProfitMargin').text(`${kpis.profitMargin.toFixed(1)}%`);
    $('#kpiAvgValue').text(`₹${kpis.avgPolicyValue.toLocaleString('en-IN')}`);
    
    // Monthly Recurring Revenue
    $('#kpiMRR').text(`₹${kpis.monthlyRecurringRevenue.toLocaleString('en-IN')}`);
    const projectedAnnual = kpis.monthlyRecurringRevenue * 12;
    $('#kpiProjectedAnnual').text(`₹${projectedAnnual.toLocaleString('en-IN')}`);
};

// Initialize Revenue Trend Chart
const initializeRevenueTrendChart = (data) => {
    const ctx = document.getElementById('revenueTrendChart');
    if (!ctx) return;
    
    // Destroy existing chart if present
    if (businessCharts.revenueTrend) {
        businessCharts.revenueTrend.destroy();
    }
    
    const labels = data.map(d => d.month);
    const premiumData = data.map(d => d.premium);
    const revenueData = data.map(d => d.revenue);
    const payoutData = data.map(d => d.payout);
    const profitData = data.map(d => d.netProfit);
    
    businessCharts.revenueTrend = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Premium (₹)',
                    data: premiumData,
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Revenue (₹)',
                    data: revenueData,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Net Profit (₹)',
                    data: profitData,
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Payout (₹)',
                    data: payoutData,
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: false
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false // Using custom legend
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ₹' + context.parsed.y.toLocaleString('en-IN');
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₹' + value.toLocaleString('en-IN');
                        }
                    }
                }
            }
        }
    });
};

// Initialize Policy Distribution Chart
const initializePolicyDistributionChart = (data) => {
    const ctx = document.getElementById('policyDistributionChart');
    if (!ctx) return;
    
    if (businessCharts.policyDistribution) {
        businessCharts.policyDistribution.destroy();
    }
    
    const labels = data.map(d => d.type);
    const counts = data.map(d => d.count);
    const revenues = data.map(d => d.revenue);
    
    businessCharts.policyDistribution = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: counts,
                backgroundColor: [
                    '#4f46e5', // Purple for Motor
                    '#10b981', // Green for Health
                    '#f59e0b'  // Orange for Life
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: {
                            size: 13
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${label}: ${value} policies (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
    
    // Update distribution stats below chart
    let statsHtml = '';
    data.forEach((item, index) => {
        const colors = ['#4f46e5', '#10b981', '#f59e0b'];
        statsHtml += `
            <div class="stat-row">
                <div class="stat-label">
                    <span class="legend-color" style="background: ${colors[index]}; display: inline-block; width: 12px; height: 12px; border-radius: 3px; margin-right: 8px;"></span>
                    ${item.type}
                </div>
                <div class="stat-value">
                    ${item.count} policies | ₹${item.revenue.toLocaleString('en-IN')} | ${item.profitMargin.toFixed(1)}% margin
                </div>
            </div>
        `;
    });
    $('#distributionStats').html(statsHtml);
};

// Initialize Business Type Chart
const initializeBusinessTypeChart = (data) => {
    const ctx = document.getElementById('businessTypeChart');
    if (!ctx) return;
    
    if (businessCharts.businessType) {
        businessCharts.businessType.destroy();
    }
    
    const labels = data.map(d => d.businessType);
    const revenues = data.map(d => d.revenue);
    const counts = data.map(d => d.count);
    
    businessCharts.businessType = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Revenue (₹)',
                data: revenues,
                backgroundColor: '#4f46e5',
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const index = context.dataIndex;
                            const revenue = revenues[index];
                            const count = counts[index];
                            return [
                                `Revenue: ₹${revenue.toLocaleString('en-IN')}`,
                                `Policies: ${count}`
                            ];
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₹' + value.toLocaleString('en-IN');
                        }
                    }
                }
            }
        }
    });
    
    // Update stats below chart
    let statsHtml = '';
    data.forEach(item => {
        statsHtml += `
            <div class="comparison-row">
                <div class="comparison-label">${item.businessType}</div>
                <div class="comparison-value">
                    ${item.count} policies | ₹${item.revenue.toLocaleString('en-IN')} | ${item.profitMargin.toFixed(1)}% margin
                </div>
            </div>
        `;
    });
    $('#businessTypeStats').html(statsHtml);
};

// Initialize Top Companies Chart
const initializeTopCompaniesChart = (data) => {
    const ctx = document.getElementById('topCompaniesChart');
    if (!ctx) return;
    
    if (businessCharts.topCompanies) {
        businessCharts.topCompanies.destroy();
    }
    
    const labels = data.map(d => d.name);
    const revenues = data.map(d => d.revenue);
    const counts = data.map(d => d.policyCount);
    
    businessCharts.topCompanies = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Revenue (₹)',
                data: revenues,
                backgroundColor: '#10b981',
                borderRadius: 8
            }]
        },
        options: {
            indexAxis: 'y', // Horizontal bars
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const index = context.dataIndex;
                            return [
                                `Revenue: ₹${revenues[index].toLocaleString('en-IN')}`,
                                `Policies: ${counts[index]}`,
                                `Avg: ₹${(revenues[index] / counts[index]).toLocaleString('en-IN')}`
                            ];
                        }
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₹' + value.toLocaleString('en-IN');
                        }
                    }
                }
            }
        }
    });
};

// Initialize Monthly Growth Chart
const initializeMonthlyGrowthChart = (data) => {
    const ctx = document.getElementById('monthlyGrowthChart');
    if (!ctx) return;
    
    if (businessCharts.monthlyGrowth) {
        businessCharts.monthlyGrowth.destroy();
    }
    
    const labels = data.map(d => d.month);
    const policyGrowth = data.map(d => d.countGrowth);
    const revenueGrowth = data.map(d => d.revenueGrowth);
    
    businessCharts.monthlyGrowth = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Policy Count Growth (%)',
                    data: policyGrowth,
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.2)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Revenue Growth (%)',
                    data: revenueGrowth,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.2)',
                    borderWidth: 3,
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
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y.toFixed(1) + '%';
                        }
                    }
                }
            },
            scales: {
                y: {
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            }
        }
    });
};
// Update Profitability Table
const updateProfitabilityTable = (breakdown, total) => {
    console.log('📊 Updating profitability table:', breakdown, total);
    
    // Organize data by policy type
    const motor = breakdown.find(b => b.type === 'Motor') || {};
    const health = breakdown.find(b => b.type === 'Health') || {};
    const life = breakdown.find(b => b.type === 'Life') || {};
    
    const getMarginClass = (margin) => {
        if (margin >= 15) return 'margin-high';
        if (margin >= 10) return 'margin-medium';
        return 'margin-low';
    };
    
    const rows = [
        {
            label: 'Policies Count',
            motor: motor.count || 0,
            health: health.count || 0,
            life: life.count || 0,
            total: total.count || 0,
            isNumber: true
        },
        {
            label: 'Total Premium',
            motor: motor.premium || 0,
            health: health.premium || 0,
            life: life.premium || 0,
            total: total.premium || 0,
            isCurrency: true
        },
        {
            label: 'Customer Paid',
            motor: motor.customerPaid || 0,
            health: health.customerPaid || 0,
            life: life.customerPaid || 0,
            total: total.customerPaid || 0,
            isCurrency: true
        },
        {
            label: 'Payouts',
            motor: motor.payout || 0,
            health: health.payout || 0,
            life: life.payout || 0,
            total: total.payout || 0,
            isCurrency: true
        },
        {
            label: 'Revenue',
            motor: motor.revenue || 0,
            health: health.revenue || 0,
            life: life.revenue || 0,
            total: total.revenue || 0,
            isCurrency: true,
            isBold: true
        },
        {
            label: 'Profit Margin',
            motor: motor.profitMargin || 0,
            health: health.profitMargin || 0,
            life: life.profitMargin || 0,
            total: total.profitMargin || 0,
            isPercentage: true,
            isBold: true,
            useMarginClass: true
        }
    ];
    
    let html = '';
    rows.forEach(row => {
        html += '<tr>';
        html += `<td style="font-weight: ${row.isBold ? '700' : '500'};">${row.label}</td>`;
        
        ['motor', 'health', 'life', 'total'].forEach(col => {
            let value = row[col];
            let displayValue = '';
            
            if (row.isCurrency) {
                displayValue = '₹' + (value || 0).toLocaleString('en-IN');
            } else if (row.isPercentage) {
                const marginClass = row.useMarginClass ? getMarginClass(value) : '';
                displayValue = `<span class="${marginClass}">${(value || 0).toFixed(1)}%</span>`;
            } else {
                displayValue = value || 0;
            }
            
            const tdClass = col === 'total' ? 'total-column' : '';
            const tdStyle = row.isBold ? 'font-weight: 700;' : '';
            html += `<td class="${tdClass}" style="${tdStyle}">${displayValue}</td>`;
        });
        
        html += '</tr>';
    });
    
    $('#profitabilityTableBody').html(html);
};

// Update Agent Performance Table
const updateAgentPerformanceTable = (agents) => {
    console.log('👥 Updating agent performance table:', agents);
    
    const getPerformanceStars = (margin) => {
        const stars = Math.min(5, Math.max(1, Math.ceil(margin / 4)));
        let html = '<span class="performance-stars">';
        for (let i = 0; i < 5; i++) {
            html += `<i class="fas fa-star${i < stars ? '' : ' empty'}"></i>`;
        }
        html += '</span>';
        return html;
    };
    
    let html = '';
    agents.forEach((agent, index) => {
        const rowClass = index === 0 ? 'style="background: rgba(79, 70, 229, 0.05);"' : '';
        html += `<tr ${rowClass}>`;
        html += `<td><strong>${agent.name}</strong></td>`;
        html += `<td>${agent.policyCount}</td>`;
        html += `<td>₹${agent.premium.toLocaleString('en-IN')}</td>`;
        html += `<td>₹${agent.revenue.toLocaleString('en-IN')}</td>`;
        html += `<td>₹${agent.payout.toLocaleString('en-IN')}</td>`;
        html += `<td>₹${agent.avgPolicyValue.toLocaleString('en-IN')}</td>`;
        html += `<td><span class="${agent.profitMargin >= 15 ? 'margin-high' : agent.profitMargin >= 10 ? 'margin-medium' : 'margin-low'}">${agent.profitMargin.toFixed(1)}%</span></td>`;
        html += `<td>${getPerformanceStars(agent.profitMargin)}</td>`;
        html += '</tr>';
    });
    
    if (agents.length === 0) {
        html = '<tr><td colspan="8" style="text-align: center; padding: 40px; color: #9ca3af;">No agent data available</td></tr>';
    }
    
    $('#agentPerformanceBody').html(html);
};

// Update Renewal Opportunities
const updateRenewalOpportunities = (opportunities) => {
    console.log('📅 Updating renewal opportunities:', opportunities);
    
    $('#renewalNext30Count').text(`${opportunities.next30Days.count} Policies`);
    $('#renewalNext30Revenue').text(`₹${opportunities.next30Days.estimatedRevenue.toLocaleString('en-IN')}`);
    
    $('#renewalNext60Count').text(`${opportunities.next60Days.count} Policies`);
    $('#renewalNext60Revenue').text(`₹${opportunities.next60Days.estimatedRevenue.toLocaleString('en-IN')}`);
    
    $('#renewalNext90Count').text(`${opportunities.next90Days.count} Policies`);
    $('#renewalNext90Revenue').text(`₹${opportunities.next90Days.estimatedRevenue.toLocaleString('en-IN')}`);
    
    $('#historicalRenewalRate').text(`${opportunities.historicalRenewalRate.toFixed(1)}%`);
};

// Export Business Report
const exportBusinessReport = async () => {
    try {
        showNotification('Preparing business report...', 'info');
        
        // This will be implemented to generate Excel/PDF report
        // For now, show a coming soon message
        showNotification('Export feature coming soon! Will generate comprehensive Excel report.', 'info');
        
        // TODO: Implement actual export functionality
        // Can use existing Excel export infrastructure
        
    } catch (error) {
        console.error('Failed to export report:', error);
        showNotification('Failed to export report', 'error');
    }
};

// ============================================
// VIEW POLICY PAGE FUNCTIONALITY
// ============================================

// Auto-initialize View Policy Page if on that page
const initViewPolicyPageIfNeeded = () => {
    // Only run on actual view policy page
    const currentPath = window.location.pathname;
    if (!currentPath.includes('/policies/') || !currentPath.includes('/view')) {
        return; // Not on view policy page
    }
    
    // Validate viewPolicyId is a number, not an HTML element
    let policyId = window.viewPolicyId;
    
    // If it's an object/element, try to extract the ID from URL
    if (typeof policyId === 'object' || (typeof policyId === 'string' && policyId.includes('<span'))) {
        // Extract ID from URL path: /policies/{id}/view
        const match = currentPath.match(/\/policies\/(\d+)\/view/);
        if (match && match[1]) {
            policyId = parseInt(match[1]);
            console.log('🔍 Extracted Policy ID from URL:', policyId);
        } else {
            console.warn('⚠️ Could not extract policy ID from URL:', currentPath);
            return;
        }
    } else if (policyId && policyId !== null && policyId !== 'null') {
        // Convert to number if it's a string
        policyId = parseInt(policyId);
        if (isNaN(policyId)) {
            console.warn('⚠️ Invalid policy ID:', window.viewPolicyId);
            return;
        }
    } else {
        return; // No valid policy ID
    }
    
    if (policyId && !isNaN(policyId)) {
        console.log('🔍 Auto-initializing View Policy page for ID:', policyId);
        // Small delay to ensure DOM is ready
        setTimeout(() => {
            loadPolicyViewPage(policyId);
        }, 100);
    }
};

// Load Policy View Page Data
const loadPolicyViewPage = async (policyId) => {
    try {
        console.log('📥 Loading policy data for ID:', policyId);
        
        // Show loading state
        $('#policyViewLoading').show();
        $('#policyViewData').hide();
        $('#policyViewError').hide();
        
        // Fetch policy data with versions
        const response = await apiCall(`/policies/${policyId}`);
        
        if (!response || !response.policy) {
            throw new Error('Policy not found');
        }
        
        const policy = response.policy;
        const versions = policy.versions || [];
        
        console.log('✅ Policy data loaded:', policy);
        console.log('📋 Policy versions:', versions);
        
        // Populate current policy data
        $('#currentPolicyId').text(policy.id);
        $('#currentPolicyType').text(policy.policyType || policy.policy_type || 'Unknown');
        $('#currentCustomerName').text(policy.customerName || policy.customer_name || 'Unknown');
        $('#currentCompanyName').text(policy.companyName || policy.company_name || 'Unknown');
        $('#currentInsuranceType').text(policy.insuranceType || policy.insurance_type || 'Unknown');
        
        // Status badge
        const status = policy.status || 'Active';
        $('#currentStatus').text(status)
            .removeClass('active expired')
            .addClass(status.toLowerCase());
        
        // Dates
        const issueDate = policy.policyIssueDate || policy.policy_issue_date;
        $('#currentPolicyIssueDate').text(issueDate ? formatDate(issueDate) : 'Not set');
        
        const startDate = policy.startDate || policy.start_date;
        const endDate = policy.endDate || policy.end_date;
        $('#currentCoveragePeriod').text(
            `${formatDate(startDate)} to ${formatDate(endDate)}`
        );
        
        // Financial info
        $('#currentPremium').text('₹' + (policy.premium || 0).toLocaleString('en-IN'));
        $('#currentPayout').text('₹' + (policy.payout || 0).toLocaleString('en-IN'));
        // Check all possible field name variations for customer paid amount (same as modal)
        const customerPaid = policy.customerPaidAmount || policy.customer_paid_amount || policy.customerPaid || policy.customer_paid || 0;
        console.log('💰 Customer Paid mapping:', {
            customerPaidAmount: policy.customerPaidAmount,
            customer_paid_amount: policy.customer_paid_amount,
            customerPaid: policy.customerPaid,
            customer_paid: policy.customer_paid,
            final: customerPaid
        });
        $('#currentCustomerPaid').text('₹' + customerPaid.toLocaleString('en-IN'));
        $('#currentRevenue').text('₹' + (policy.revenue || 0).toLocaleString('en-IN'));
        
        // Wire up action buttons
        $('#editPolicyBtn').off('click').on('click', () => {
            console.log('🔧 Edit button clicked for policy:', policyId);
            editPolicy(policyId);
        });
        
        $('#renewPolicyBtn').off('click').on('click', async () => {
            console.log('🔄 Renew button clicked for policy:', policyId);
            console.log('🔄 Modal element exists:', $('#renewPolicyModal').length);
            console.log('🔄 Calling renewPolicy function...');
            try {
                await renewPolicy(policyId);
                console.log('✅ renewPolicy function completed');
            } catch (error) {
                console.error('❌ Error in renewPolicy:', error);
                showNotification('Failed to open renew modal', 'error');
            }
        });
        
        $('#deletePolicyBtn').off('click').on('click', async () => {
            console.log('🗑️ Delete button clicked for policy:', policyId);
            if (confirm('Are you sure you want to delete this policy? This will delete all documents and history.')) {
                try {
                    await deletePolicy(policyId);
                    showNotification('Policy deleted successfully', 'success');
                    // Redirect back to policies page after 1 second
                    setTimeout(() => {
                        window.location.href = '/policies';
                    }, 1000);
                } catch (error) {
                    console.error('Failed to delete policy:', error);
                    showNotification('Failed to delete policy', 'error');
                }
            }
        });
        
        // Contact info
        $('#currentPhone').text(policy.phone || 'Not provided');
        $('#currentEmail').text(policy.email || 'Not provided');
        $('#currentAgent').text(policy.agentName || policy.agent_name || 'Self');
        
        // Vehicle info (if Motor)
        if (policy.policyType === 'Motor' || policy.policy_type === 'Motor') {
            $('#currentVehicleInfo').show();
            $('#currentVehicleNumber').text(policy.vehicleNumber || policy.vehicle_number || 'N/A');
        } else {
            $('#currentVehicleInfo').hide();
        }
        
        // Store policy ID for action buttons
        window.currentViewPolicyId = policy.id;
        
        // Populate current documents
        populateCurrentDocuments(policy);
        
        // Populate history table
        populatePolicyHistory(versions);
        
        // Hide loading, show data
        $('#policyViewLoading').hide();
        $('#policyViewData').show();
        
    } catch (error) {
        console.error('❌ Failed to load policy:', error);
        $('#policyViewLoading').hide();
        $('#policyViewError').show();
    }
};

// Populate Current Documents
const populateCurrentDocuments = (policy) => {
    const documentsContainer = $('#currentDocuments');
    documentsContainer.empty();
    
    const documents = [
        { type: 'policy', label: 'Policy Copy', path: policy.policy_copy_path },
        { type: 'rc', label: 'RC Copy', path: policy.rc_copy_path },
        { type: 'aadhar', label: 'Aadhar Copy', path: policy.aadhar_copy_path },
        { type: 'pan', label: 'PAN Copy', path: policy.pan_copy_path }
    ];
    
    let hasDocuments = false;
    documents.forEach(doc => {
        if (doc.path) {
            hasDocuments = true;
            const docCard = $(`
                <div class="document-card" onclick="downloadDocument(${policy.id}, '${doc.type}')">
                    <i class="fas fa-file-pdf"></i>
                    <div>
                        <strong>${doc.label}</strong>
                        <small style="display: block; color: #666;">Click to download</small>
                    </div>
                </div>
            `);
            documentsContainer.append(docCard);
        }
    });
    
    if (!hasDocuments) {
        documentsContainer.html('<p style="color: #666; text-align: center; padding: 20px;">No documents uploaded</p>');
    }
};

// Populate Policy History Table
const populatePolicyHistory = (versions) => {
    const container = $('#policyHistoryTable');
    
    if (!versions || versions.length === 0) {
        container.hide();
        $('#noHistoryMessage').show();
        return;
    }
    
    $('#noHistoryMessage').hide();
    container.show();
    
    let html = `
        <table class="history-table">
            <thead>
                <tr>
                    <th>Year</th>
                    <th>Issue Date</th>
                    <th>Coverage Period</th>
                    <th>Company</th>
                    <th>Premium</th>
                    <th>Revenue</th>
                    <th>Documents</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
    `;
    
    versions.forEach((version, index) => {
        const issueDate = version.policy_issue_date || 'Not set';
        const startDate = version.start_date || '';
        const endDate = version.end_date || '';
        const yearRange = startDate && endDate ? `${formatDate(startDate).slice(-4)}-${formatDate(endDate).slice(-4)}` : 'Unknown';
        
        html += `<tr>`;
        html += `<td><strong>${yearRange}</strong></td>`;
        html += `<td>${issueDate ? formatDate(issueDate) : 'Not set'}</td>`;
        html += `<td>${formatDate(startDate)} to ${formatDate(endDate)}</td>`;
        html += `<td>${version.company_name || 'Unknown'}</td>`;
        html += `<td>₹${(version.premium || 0).toLocaleString('en-IN')}</td>`;
        html += `<td>₹${(version.revenue || 0).toLocaleString('en-IN')}</td>`;
        html += `<td style="white-space: nowrap;">`;
        
        // Only show document buttons (don't attempt download - will show errors)
        // User can try to download, and if file doesn't exist, they'll get a clear error message
        let hasAnyDocs = false;
        if (version.policy_copy_path) {
            html += `<button class="download-doc-btn" onclick="downloadVersionDocument(${version.id}, 'policy')" title="Download Policy Copy">
                <i class="fas fa-file-pdf"></i> Policy
            </button> `;
            hasAnyDocs = true;
        }
        if (version.rc_copy_path) {
            html += `<button class="download-doc-btn" onclick="downloadVersionDocument(${version.id}, 'rc')" title="Download RC Copy">
                <i class="fas fa-file-pdf"></i> RC
            </button> `;
            hasAnyDocs = true;
        }
        if (version.aadhar_copy_path) {
            html += `<button class="download-doc-btn" onclick="downloadVersionDocument(${version.id}, 'aadhar')" title="Download Aadhar Copy">
                <i class="fas fa-id-card"></i> Aadhar
            </button> `;
            hasAnyDocs = true;
        }
        if (version.pan_copy_path) {
            html += `<button class="download-doc-btn" onclick="downloadVersionDocument(${version.id}, 'pan')" title="Download PAN Copy">
                <i class="fas fa-id-card"></i> PAN
            </button> `;
            hasAnyDocs = true;
        }
        
        if (!hasAnyDocs) {
            html += `<span style="color: #999;">No documents</span>`;
        }
        
        html += `</td>`;
        html += `<td>`;
        html += `<button class="action-btn delete" onclick="deleteVersionHistory(${version.id})" title="Delete this history" style="color: #ef4444; padding: 6px 12px; border: 1px solid #ef4444; border-radius: 4px; background: white; cursor: pointer;">
            <i class="fas fa-trash"></i>
        </button>`;
        html += `</td>`;
        html += `</tr>`;
    });
    
    html += `
            </tbody>
        </table>
    `;
    
    container.html(html);
};

// Download Document for a Policy (used by dashboard + view policy modal)
const downloadDocument = (policyId, documentType) => {
    console.log('📥 downloadDocument called (raw):', { policyId, documentType });

    // Robustly resolve policyId if not passed correctly
    if (!policyId) {
        const modalPolicyId = $('#viewPolicyModal').data('policy-id');
        console.log('📥 Fallback modal policy-id:', modalPolicyId);
        
        if (modalPolicyId) {
            policyId = modalPolicyId;
        } else if (window.currentViewingPolicyId) {
            console.log('📥 Fallback currentViewingPolicyId:', window.currentViewingPolicyId);
            policyId = window.currentViewingPolicyId;
        }
    }

    if (!policyId) {
        console.error('❌ Policy ID not resolved');
        showNotification('Policy ID not found for download', 'error');
        return;
    }

    if (!documentType) {
        console.error('❌ Document type not provided');
        showNotification('Document type not specified', 'error');
        return;
    }

    // Create download URL (with cache-busting query)
    const downloadUrl = `/api/policies/${policyId}/download/${documentType}?_=${Date.now()}`;
    console.log('📥 Download URL:', downloadUrl);

    // Show loading notification
    const readableType = documentType === 'policy'
        ? 'policy'
        : documentType.toUpperCase();
    showNotification(`Downloading ${readableType} document...`, 'info');

    // Use fetch so we can surface server error messages cleanly
    fetch(downloadUrl)
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => {
                    throw new Error(data.message || 'Download failed');
                });
            }

            // Get filename from Content-Disposition header
            const contentDisposition = response.headers.get('Content-Disposition');
            let filename = `${documentType}_document.pdf`; // Default fallback

            if (contentDisposition) {
                // Handle both quoted and unquoted filenames, and different formats
                const filenameMatch = contentDisposition.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
                if (filenameMatch && filenameMatch[1]) {
                    filename = filenameMatch[1].replace(/['"]/g, '');
                } else {
                    const altMatch = contentDisposition.match(/filename\*?=([^;]+)/);
                    if (altMatch && altMatch[1]) {
                        filename = altMatch[1].replace(/['"]/g, '');
                    } else {
                        const endMatch = contentDisposition.match(/filename[^=]*=([^;]+)/);
                        if (endMatch && endMatch[1]) {
                            filename = endMatch[1].replace(/['"]/g, '');
                        }
                    }
                }
            }

            return response.blob().then(blob => ({ blob, filename }));
        })
        .then(({ blob, filename }) => {
            // Trigger browser download
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            window.URL.revokeObjectURL(url);

            showNotification(`${readableType} document downloaded successfully!`, 'success');
        })
        .catch(error => {
            console.error('Download error:', error);
            showNotification(error.message || 'Download failed', 'error');
        });
};

// Delete Policy Version History
const deleteVersionHistory = async (versionId) => {
    if (!confirm('Are you sure you want to delete this policy history? This will also delete all associated documents for this version.')) {
        return;
    }
    
    console.log('🗑️ Deleting version history:', versionId);
    
    try {
        const response = await apiCall(`/api/policy-versions/${versionId}`, {
            method: 'DELETE'
        });
        
        console.log('✅ Version deleted:', response);
        showNotification('Policy history deleted successfully', 'success');
        
        // Reload the page to refresh the history table
        setTimeout(() => {
            window.location.reload();
        }, 1000);
    } catch (error) {
        console.error('❌ Failed to delete version:', error);
        showNotification('Failed to delete policy history', 'error');
    }
};

// Download Document for Policy Version
const downloadVersionDocument = async (versionId, documentType) => {
    console.log('📥 Downloading version document:', { versionId, documentType });
    
    const downloadUrl = `/api/policy-versions/${versionId}/download/${documentType}`;
    
    try {
        const response = await fetch(downloadUrl);
        
        if (!response.ok) {
            // Check if it's a 404 (document not found)
            if (response.status === 404) {
                const errorData = await response.json().catch(() => ({ message: 'Document not found' }));
                const helpMessage = errorData.help || 'Please re-upload the document on the current policy.';
                showNotification(`⚠️ ${errorData.message}\n\n💡 ${helpMessage}`, 'error');
                console.error('❌ Document not found:', errorData);
                console.error('📍 Version path:', errorData.version_path);
                console.error('📍 Policy path checked:', errorData.policy_path_checked);
                return;
            }
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        // Get filename from Content-Disposition header or create default
        const contentDisposition = response.headers.get('Content-Disposition');
        let filename = `version_${versionId}_${documentType}.pdf`;
        
        if (contentDisposition) {
            const filenameMatch = contentDisposition.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
            if (filenameMatch && filenameMatch[1]) {
                filename = filenameMatch[1].replace(/['"]/g, '');
            }
        }
        
        // Download the file
        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
        
        showNotification('Document downloaded successfully', 'success');
        console.log('✅ Document downloaded:', filename);
    } catch (error) {
        console.error('❌ Download failed:', error);
        showNotification('Failed to download document', 'error');
    }
};

// Expose functions to global scope for onclick handlers in HTML
window.renewPolicy = renewPolicy;
window.editPolicy = editPolicy;
window.deletePolicy = deletePolicy;
// Map generic viewPolicy() from Blade templates to the detailed view handler
window.viewPolicy = viewPolicyDetails;
window.downloadDocument = downloadDocument;
window.removeDocument = removeDocument;
window.deleteVersionHistory = deleteVersionHistory;
window.downloadVersionDocument = downloadVersionDocument;
window.showNotification = showNotification;
window.openPolicyModal = openPolicyModal;
window.openPolicyHistoryModal = openPolicyHistoryModal;