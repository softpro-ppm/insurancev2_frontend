@extends('layouts.insurance')

@section('title', 'Settings - Insurance Management System 2.0')

@section('content')
<div class="page-header">
    <h1>Settings</h1>
    <p>System configuration and preferences</p>
</div>
<div class="page-content">
    <!-- Settings Tabs -->
    <div class="settings-tabs">
        <button class="settings-tab-btn active" data-tab="general">
            <i class="fas fa-cog"></i>
            General
        </button>
        <button class="settings-tab-btn" data-tab="notifications">
            <i class="fas fa-bell"></i>
            Notifications
        </button>
        <button class="settings-tab-btn" data-tab="emailTemplates">
            <i class="fas fa-envelope"></i>
            Email Templates
        </button>
        <button class="settings-tab-btn" data-tab="security">
            <i class="fas fa-shield-alt"></i>
            Security
        </button>
        <button class="settings-tab-btn" data-tab="appearance">
            <i class="fas fa-palette"></i>
            Appearance
        </button>
        <button class="settings-tab-btn" data-tab="backup">
            <i class="fas fa-database"></i>
            Backup & Export
        </button>
    </div>

    <!-- General Settings Tab -->
    <div class="settings-content active" id="generalSettings">
        <div class="settings-section">
            <h3>Company Information</h3>
            <div class="form-section">
                <div class="form-row">
                    <div class="form-group">
                        <label for="companyName">Company Name</label>
                        <input type="text" id="companyName" value="Insurance Management System">
                    </div>
                    <div class="form-group">
                        <label for="companyEmail">Company Email</label>
                        <input type="email" id="companyEmail" value="info@insurance.com">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="companyPhone">Company Phone</label>
                        <input type="tel" id="companyPhone" value="+91 98765 43210">
                    </div>
                    <div class="form-group">
                        <label for="companyAddress">Company Address</label>
                        <textarea id="companyAddress" rows="3">123 Insurance Street, Business District, City - 123456</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="settings-section">
            <h3>System Preferences</h3>
            <div class="form-section">
                <div class="form-row">
                    <div class="form-group">
                        <label for="defaultCurrency">Default Currency</label>
                        <select id="defaultCurrency">
                            <option value="INR" selected>Indian Rupee (₹)</option>
                            <option value="USD">US Dollar ($)</option>
                            <option value="EUR">Euro (€)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="dateFormat">Date Format</label>
                        <select id="dateFormat">
                            <option value="DD/MM/YYYY" selected>DD/MM/YYYY</option>
                            <option value="MM/DD/YYYY">MM/DD/YYYY</option>
                            <option value="YYYY-MM-DD">YYYY-MM-DD</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="timezone">Timezone</label>
                        <select id="timezone">
                            <option value="Asia/Kolkata" selected>Asia/Kolkata (IST)</option>
                            <option value="UTC">UTC</option>
                            <option value="America/New_York">America/New_York (EST)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="language">Language</label>
                        <select id="language">
                            <option value="en" selected>English</option>
                            <option value="hi">Hindi</option>
                            <option value="es">Spanish</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="settings-section">
            <h3>Policy Settings</h3>
            <div class="form-section">
                <div class="form-row">
                    <div class="form-group">
                        <label for="defaultPolicyDuration">Default Policy Duration (Days)</label>
                        <input type="number" id="defaultPolicyDuration" value="365" min="30" max="1095">
                    </div>
                    <div class="form-group">
                        <label for="renewalReminderDays">Renewal Reminder (Days Before)</label>
                        <input type="number" id="renewalReminderDays" value="30" min="1" max="90">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="maxPoliciesPerAgent">Max Policies Per Agent</label>
                        <input type="number" id="maxPoliciesPerAgent" value="100" min="1" max="1000">
                    </div>
                    <div class="form-group">
                        <label for="commissionRate">Default Commission Rate (%)</label>
                        <input type="number" id="commissionRate" value="10" min="0" max="50" step="0.1">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notifications Settings Tab -->
    <div class="settings-content" id="notificationsSettings">
        <div class="settings-section">
            <h3>Email Notifications</h3>
            <div class="form-section">
                <div class="form-group">
                    <label for="emailNotifications">
                        <input type="checkbox" id="emailNotifications" checked>
                        Enable Email Notifications
                    </label>
                </div>
                <div class="form-group">
                    <label for="smtpServer">SMTP Server</label>
                    <input type="text" id="smtpServer" value="smtp.gmail.com">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="smtpPort">SMTP Port</label>
                        <input type="number" id="smtpPort" value="587">
                    </div>
                    <div class="form-group">
                        <label for="smtpUsername">SMTP Username</label>
                        <input type="email" id="smtpUsername" value="noreply@insurance.com">
                    </div>
                </div>
                <div class="form-group">
                    <label for="smtpPassword">SMTP Password</label>
                    <input type="password" id="smtpPassword" value="********">
                </div>
            </div>
        </div>

        <div class="settings-section">
            <h3>SMS Notifications</h3>
            <div class="form-section">
                <div class="form-group">
                    <label for="smsNotifications">
                        <input type="checkbox" id="smsNotifications" checked>
                        Enable SMS Notifications
                    </label>
                </div>
                <div class="form-group">
                    <label for="smsProvider">SMS Provider</label>
                    <select id="smsProvider">
                        <option value="twilio" selected>Twilio</option>
                        <option value="msg91">MSG91</option>
                        <option value="custom">Custom Provider</option>
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="smsApiKey">SMS API Key</label>
                        <input type="text" id="smsApiKey" value="your_api_key_here">
                    </div>
                    <div class="form-group">
                        <label for="smsSenderId">Sender ID</label>
                        <input type="text" id="smsSenderId" value="INSURANCE" maxlength="6">
                    </div>
                </div>
            </div>
        </div>

        <div class="settings-section">
            <h3>WhatsApp Business Integration</h3>
            <div class="form-section">
                <div class="form-group">
                    <label for="whatsappEnabled">
                        <input type="checkbox" id="whatsappEnabled">
                        Enable WhatsApp Notifications
                    </label>
                </div>
                <div class="form-group">
                    <label for="whatsappBusinessId">WhatsApp Business ID</label>
                    <input type="text" id="whatsappBusinessId" placeholder="Your Business ID">
                </div>
                <div class="form-group">
                    <label for="whatsappAccessToken">Access Token</label>
                    <input type="password" id="whatsappAccessToken" placeholder="Your Access Token">
                </div>
                <div class="form-group">
                    <label for="whatsappPhoneNumber">Business Phone Number</label>
                    <input type="tel" id="whatsappPhoneNumber" placeholder="+91 98765 43210">
                </div>
                <div class="form-group">
                    <label for="whatsappWebhookUrl">Webhook URL</label>
                    <input type="url" id="whatsappWebhookUrl" placeholder="https://your-domain.com/webhook">
                </div>
            </div>
        </div>

        <div class="settings-section">
            <h3>Notification Preferences</h3>
            <div class="form-section">
                <div class="form-group">
                    <label for="policyExpiryAlerts">
                        <input type="checkbox" id="policyExpiryAlerts" checked>
                        Policy Expiry Alerts
                    </label>
                </div>
                <div class="form-group">
                    <label for="renewalReminders">
                        <input type="checkbox" id="renewalReminders" checked>
                        Renewal Reminders
                    </label>
                </div>
                <div class="form-group">
                    <label for="followupAlerts">
                        <input type="checkbox" id="followupAlerts" checked>
                        Follow-up Alerts
                    </label>
                </div>
                <div class="form-group">
                    <label for="commissionAlerts">
                        <input type="checkbox" id="commissionAlerts" checked>
                        Commission Alerts
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- Email Templates Settings Tab -->
    <div class="settings-content" id="emailTemplatesSettings">
        <div class="settings-section">
            <h3>Email Templates</h3>
            <div class="template-list">
                <div class="template-item">
                    <div class="template-header">
                        <h4>Policy Renewal Reminder</h4>
                        <button class="btn btn-primary btn-sm" onclick="editEmailTemplate('policy_renewal')">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                    </div>
                    <div class="template-preview">
                        <div class="preview-subject">
                            <strong>Subject:</strong> Your [POLICY_TYPE] policy expires on [EXPIRY_DATE]
                        </div>
                        <div class="preview-body">
                            <strong>Body:</strong> Dear [CUSTOMER_NAME],<br><br>
                            Your [POLICY_TYPE] policy (Policy ID: [POLICY_ID]) expires on [EXPIRY_DATE]. 
                            To ensure continuous coverage, please renew your policy before the expiry date.<br><br>
                            <strong>Policy Details:</strong><br>
                            • Policy Type: [POLICY_TYPE]<br>
                            • Vehicle: [VEHICLE_NUMBER]<br>
                            • Premium Amount: ₹[PREMIUM_AMOUNT]<br><br>
                            <strong>Contact us:</strong><br>
                            Phone: [CONTACT_PHONE]<br>
                            Email: [CONTACT_EMAIL]<br><br>
                            Best regards,<br>
                            [COMPANY_NAME] Team
                        </div>
                    </div>
                    <div class="template-variables">
                        <span class="variable">[CUSTOMER_NAME]</span>
                        <span class="variable">[POLICY_TYPE]</span>
                        <span class="variable">[POLICY_ID]</span>
                        <span class="variable">[EXPIRY_DATE]</span>
                        <span class="variable">[VEHICLE_NUMBER]</span>
                        <span class="variable">[PREMIUM_AMOUNT]</span>
                        <span class="variable">[CONTACT_PHONE]</span>
                        <span class="variable">[CONTACT_EMAIL]</span>
                        <span class="variable">[COMPANY_NAME]</span>
                    </div>
                </div>

                <div class="template-item">
                    <div class="template-header">
                        <h4>Follow-up Reminder</h4>
                        <button class="btn btn-primary btn-sm" onclick="editEmailTemplate('followup')">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                    </div>
                    <div class="template-preview">
                        <div class="preview-subject">
                            <strong>Subject:</strong> Follow-up Required: [CUSTOMER_NAME] - [POLICY_TYPE]
                        </div>
                        <div class="preview-body">
                            <strong>Body:</strong> Dear [AGENT_NAME],<br><br>
                            A follow-up is required for customer [CUSTOMER_NAME] regarding their [POLICY_TYPE] policy.<br><br>
                            <strong>Customer Details:</strong><br>
                            • Name: [CUSTOMER_NAME]<br>
                            • Phone: [CUSTOMER_PHONE]<br>
                            • Email: [CUSTOMER_EMAIL]<br>
                            • Policy ID: [POLICY_ID]<br><br>
                            <strong>Follow-up Notes:</strong><br>
                            [FOLLOWUP_NOTES]<br><br>
                            Please contact the customer at your earliest convenience.<br><br>
                            Best regards,<br>
                            [COMPANY_NAME] Team
                        </div>
                    </div>
                    <div class="template-variables">
                        <span class="variable">[AGENT_NAME]</span>
                        <span class="variable">[CUSTOMER_NAME]</span>
                        <span class="variable">[POLICY_TYPE]</span>
                        <span class="variable">[CUSTOMER_PHONE]</span>
                        <span class="variable">[CUSTOMER_EMAIL]</span>
                        <span class="variable">[POLICY_ID]</span>
                        <span class="variable">[FOLLOWUP_NOTES]</span>
                        <span class="variable">[COMPANY_NAME]</span>
                    </div>
                </div>

                <div class="template-item">
                    <div class="template-header">
                        <h4>Commission Alert</h4>
                        <button class="btn btn-primary btn-sm" onclick="editEmailTemplate('commission')">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                    </div>
                    <div class="template-preview">
                        <div class="preview-subject">
                            <strong>Subject:</strong> Commission Earned: ₹[COMMISSION_AMOUNT] for [POLICY_TYPE]
                        </div>
                        <div class="preview-body">
                            <strong>Body:</strong> Dear [AGENT_NAME],<br><br>
                            Congratulations! You have earned a commission for successfully closing a [POLICY_TYPE] policy.<br><br>
                            <strong>Commission Details:</strong><br>
                            • Policy Type: [POLICY_TYPE]<br>
                            • Customer: [CUSTOMER_NAME]<br>
                            • Policy ID: [POLICY_ID]<br>
                            • Commission Amount: ₹[COMMISSION_AMOUNT]<br>
                            • Commission Rate: [COMMISSION_RATE]%<br><br>
                            The commission will be processed in the next payment cycle.<br><br>
                            Keep up the great work!<br><br>
                            Best regards,<br>
                            [COMPANY_NAME] Team
                        </div>
                    </div>
                    <div class="template-variables">
                        <span class="variable">[AGENT_NAME]</span>
                        <span class="variable">[POLICY_TYPE]</span>
                        <span class="variable">[CUSTOMER_NAME]</span>
                        <span class="variable">[POLICY_ID]</span>
                        <span class="variable">[COMMISSION_AMOUNT]</span>
                        <span class="variable">[COMMISSION_RATE]</span>
                        <span class="variable">[COMPANY_NAME]</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Security Settings Tab -->
    <div class="settings-content" id="securitySettings">
        <div class="settings-section">
            <h3>Password Policy</h3>
            <div class="form-section">
                <div class="form-group">
                    <label for="minPasswordLength">Minimum Password Length</label>
                    <input type="number" id="minPasswordLength" value="8" min="6" max="20">
                </div>
                <div class="form-group">
                    <label for="requireUppercase">
                        <input type="checkbox" id="requireUppercase" checked>
                        Require Uppercase Letters
                    </label>
                </div>
                <div class="form-group">
                    <label for="requireLowercase">
                        <input type="checkbox" id="requireLowercase" checked>
                        Require Lowercase Letters
                    </label>
                </div>
                <div class="form-group">
                    <label for="requireNumbers">
                        <input type="checkbox" id="requireNumbers" checked>
                        Require Numbers
                    </label>
                </div>
                <div class="form-group">
                    <label for="requireSpecialChars">
                        <input type="checkbox" id="requireSpecialChars">
                        Require Special Characters
                    </label>
                </div>
            </div>
        </div>

        <div class="settings-section">
            <h3>Session Management</h3>
            <div class="form-section">
                <div class="form-group">
                    <label for="sessionTimeout">Session Timeout (Minutes)</label>
                    <input type="number" id="sessionTimeout" value="30" min="5" max="480">
                </div>
                <div class="form-group">
                    <label for="maxLoginAttempts">Maximum Login Attempts</label>
                    <input type="number" id="maxLoginAttempts" value="5" min="3" max="10">
                </div>
                <div class="form-group">
                    <label for="lockoutDuration">Account Lockout Duration (Minutes)</label>
                    <input type="number" id="lockoutDuration" value="15" min="5" max="1440">
                </div>
            </div>
        </div>

        <div class="settings-section">
            <h3>Two-Factor Authentication</h3>
            <div class="form-section">
                <div class="form-group">
                    <label for="enable2FA">
                        <input type="checkbox" id="enable2FA">
                        Enable Two-Factor Authentication
                    </label>
                </div>
                <div class="form-group">
                    <label for="twoFactorMethod">2FA Method</label>
                    <select id="twoFactorMethod">
                        <option value="sms">SMS</option>
                        <option value="email">Email</option>
                        <option value="authenticator">Authenticator App</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Appearance Settings Tab -->
    <div class="settings-content" id="appearanceSettings">
        <div class="settings-section">
            <h3>Theme Settings</h3>
            <div class="form-section">
                <div class="form-group">
                    <label for="themeMode">Theme Mode</label>
                    <select id="themeMode">
                        <option value="light">Light Theme</option>
                        <option value="dark">Dark Theme</option>
                        <option value="auto" selected>Auto (System)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="primaryColor">Primary Color</label>
                    <input type="color" id="primaryColor" value="#4F46E5">
                </div>
                <div class="form-group">
                    <label for="secondaryColor">Secondary Color</label>
                    <input type="color" id="secondaryColor" value="#10B981">
                </div>
            </div>
        </div>

        <div class="settings-section">
            <h3>Display Settings</h3>
            <div class="form-section">
                <div class="form-group">
                    <label for="defaultRowsPerPage">Default Rows Per Page</label>
                    <select id="defaultRowsPerPage">
                        <option value="10" selected>10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="showAnimations">
                        <input type="checkbox" id="showAnimations" checked>
                        Show Animations
                    </label>
                </div>
                <div class="form-group">
                    <label for="compactMode">
                        <input type="checkbox" id="compactMode">
                        Compact Mode
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- Backup & Export Settings Tab -->
    <div class="settings-content" id="backupSettings">
        <div class="settings-section">
            <h3>Automatic Backup</h3>
            <div class="form-section">
                <div class="form-group">
                    <label for="autoBackup">
                        <input type="checkbox" id="autoBackup" checked>
                        Enable Automatic Backup
                    </label>
                </div>
                <div class="form-group">
                    <label for="backupFrequency">Backup Frequency</label>
                    <select id="backupFrequency">
                        <option value="daily" selected>Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="backupTime">Backup Time</label>
                    <input type="time" id="backupTime" value="02:00">
                </div>
                <div class="form-group">
                    <label for="retainBackups">Retain Backups (Days)</label>
                    <input type="number" id="retainBackups" value="30" min="7" max="365">
                </div>
            </div>
        </div>

        <div class="settings-section">
            <h3>Manual Export</h3>
            <div class="form-section">
                <div class="form-group">
                    <label for="exportFormat">Export Format</label>
                    <select id="exportFormat">
                        <option value="csv" selected>CSV</option>
                        <option value="excel">Excel</option>
                        <option value="json">JSON</option>
                        <option value="pdf">PDF</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="includeDeleted">
                        <input type="checkbox" id="includeDeleted">
                        Include Deleted Records
                    </label>
                </div>
                <div class="form-group">
                    <label for="exportDateRange">Export Date Range</label>
                    <select id="exportDateRange">
                        <option value="all" selected>All Data</option>
                        <option value="last30">Last 30 Days</option>
                        <option value="last90">Last 90 Days</option>
                        <option value="last365">Last Year</option>
                        <option value="custom">Custom Range</option>
                    </select>
                </div>
                <button class="btn btn-primary" id="exportAllData">
                    <i class="fas fa-download"></i>
                    Export All Data
                </button>
            </div>
        </div>

        <div class="settings-section">
            <h3>Data Management</h3>
            <div class="form-section">
                <div class="form-group">
                    <label for="dataRetention">Data Retention Period (Days)</label>
                    <input type="number" id="dataRetention" value="2555" min="365" max="10950">
                    <small>Minimum 1 year, Maximum 30 years</small>
                </div>
                <div class="form-group">
                    <label for="autoArchive">
                        <input type="checkbox" id="autoArchive" checked>
                        Auto-archive Old Data
                    </label>
                </div>
                <button class="btn btn-warning" id="clearCache">
                    <i class="fas fa-broom"></i>
                    Clear Cache
                </button>
                <button class="btn btn-danger" id="resetSettings">
                    <i class="fas fa-undo"></i>
                    Reset to Defaults
                </button>
            </div>
        </div>
    </div>

    <!-- Settings Actions -->
    <div class="settings-actions">
        <button class="btn btn-secondary" id="cancelSettings">Cancel</button>
        <button class="btn btn-primary" id="saveSettings">Save Settings</button>
    </div>
</div>
@endsection
