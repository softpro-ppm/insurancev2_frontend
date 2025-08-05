@extends('layouts.insurance')

@section('title', 'Settings - Insurance Management System')

@section('content')
<div class="page active" id="settings">
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
            <div class="settings-section glass-effect">
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

            <div class="settings-section glass-effect">
                <h3>Business Settings</h3>
                <div class="form-section">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="businessRegNo">Business Registration No.</label>
                            <input type="text" id="businessRegNo" value="REG123456789">
                        </div>
                        <div class="form-group">
                            <label for="gstNumber">GST Number</label>
                            <input type="text" id="gstNumber" value="22AAAAA0000A1Z5">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="timezone">Timezone</label>
                            <select id="timezone">
                                <option value="Asia/Kolkata" selected>India Standard Time (IST)</option>
                                <option value="UTC">Coordinated Universal Time (UTC)</option>
                                <option value="America/New_York">Eastern Time (ET)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="currency">Currency</label>
                            <select id="currency">
                                <option value="INR" selected>Indian Rupee (₹)</option>
                                <option value="USD">US Dollar ($)</option>
                                <option value="EUR">Euro (€)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="settings-actions">
                <button class="btn btn-primary">Save General Settings</button>
                <button class="btn btn-secondary">Reset to Defaults</button>
            </div>
        </div>

        <!-- Notifications Settings Tab -->
        <div class="settings-content" id="notificationsSettings">
            <div class="settings-section glass-effect">
                <h3>Email Configuration</h3>
                <div class="form-section">
                    <div class="form-group">
                        <label for="emailNotifications">
                            <input type="checkbox" id="emailNotifications" checked>
                            Enable Email Notifications
                        </label>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="smtpHost">SMTP Host</label>
                            <input type="text" id="smtpHost" value="smtp.gmail.com">
                        </div>
                        <div class="form-group">
                            <label for="smtpPort">SMTP Port</label>
                            <input type="number" id="smtpPort" value="587">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="smtpUsername">SMTP Username</label>
                            <input type="email" id="smtpUsername" value="your-email@gmail.com">
                        </div>
                        <div class="form-group">
                            <label for="smtpPassword">SMTP Password</label>
                            <input type="password" id="smtpPassword" value="********">
                        </div>
                    </div>
                </div>
            </div>

            <div class="settings-section glass-effect">
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

            <div class="settings-section glass-effect">
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

            <div class="settings-section glass-effect">
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

            <div class="settings-actions">
                <button class="btn btn-primary">Save Notification Settings</button>
                <button class="btn btn-secondary">Test Configuration</button>
            </div>
        </div>

        <!-- Email Templates Settings Tab -->
        <div class="settings-content" id="emailTemplatesSettings">
            <div class="settings-section glass-effect">
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

            <div class="settings-actions">
                <button class="btn btn-primary">Save Templates</button>
                <button class="btn btn-secondary">Reset to Defaults</button>
            </div>
        </div>

        <!-- Security Settings Tab -->
        <div class="settings-content" id="securitySettings">
            <div class="settings-section glass-effect">
                <h3>Password Policy</h3>
                <div class="form-section">
                    <div class="form-group">
                        <label for="minPasswordLength">Minimum Password Length</label>
                        <input type="number" id="minPasswordLength" value="8" min="6" max="32">
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

            <div class="settings-section glass-effect">
                <h3>Session Management</h3>
                <div class="form-section">
                    <div class="form-group">
                        <label for="sessionTimeout">Session Timeout (minutes)</label>
                        <input type="number" id="sessionTimeout" value="120" min="15" max="480">
                    </div>
                    <div class="form-group">
                        <label for="maxLoginAttempts">Maximum Login Attempts</label>
                        <input type="number" id="maxLoginAttempts" value="5" min="3" max="10">
                    </div>
                    <div class="form-group">
                        <label for="lockoutDuration">Lockout Duration (minutes)</label>
                        <input type="number" id="lockoutDuration" value="30" min="5" max="120">
                    </div>
                </div>
            </div>

            <div class="settings-actions">
                <button class="btn btn-primary">Save Security Settings</button>
                <button class="btn btn-secondary">Reset to Defaults</button>
            </div>
        </div>

        <!-- Appearance Settings Tab -->
        <div class="settings-content" id="appearanceSettings">
            <div class="settings-section glass-effect">
                <h3>Theme Settings</h3>
                <div class="form-section">
                    <div class="form-group">
                        <label for="themeMode">Theme Mode</label>
                        <select id="themeMode">
                            <option value="light" selected>Light Mode</option>
                            <option value="dark">Dark Mode</option>
                            <option value="auto">Auto (System Preference)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="primaryColor">Primary Color</label>
                        <input type="color" id="primaryColor" value="#3B82F6">
                    </div>
                </div>
            </div>

            <div class="settings-section glass-effect">
                <h3>Display Settings</h3>
                <div class="form-section">
                    <div class="form-group">
                        <label for="recordsPerPage">Default Records Per Page</label>
                        <select id="recordsPerPage">
                            <option value="10" selected>10 records</option>
                            <option value="25">25 records</option>
                            <option value="50">50 records</option>
                            <option value="100">100 records</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="dateFormat">Date Format</label>
                        <select id="dateFormat">
                            <option value="dd/mm/yyyy" selected>DD/MM/YYYY</option>
                            <option value="mm/dd/yyyy">MM/DD/YYYY</option>
                            <option value="yyyy-mm-dd">YYYY-MM-DD</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="settings-actions">
                <button class="btn btn-primary">Save Appearance Settings</button>
                <button class="btn btn-secondary">Reset to Defaults</button>
            </div>
        </div>

        <!-- Backup & Export Settings Tab -->
        <div class="settings-content" id="backupSettings">
            <div class="settings-section glass-effect">
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
                        <label for="backupLocation">Backup Location</label>
                        <select id="backupLocation">
                            <option value="local" selected>Local Storage</option>
                            <option value="cloud">Cloud Storage</option>
                            <option value="both">Both</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="settings-section glass-effect">
                <h3>Manual Backup & Export</h3>
                <div class="backup-actions">
                    <button class="btn btn-primary">
                        <i class="fas fa-download"></i> Create Full Backup
                    </button>
                    <button class="btn btn-secondary">
                        <i class="fas fa-file-export"></i> Export Policies Data
                    </button>
                    <button class="btn btn-secondary">
                        <i class="fas fa-file-export"></i> Export Renewals Data
                    </button>
                    <button class="btn btn-secondary">
                        <i class="fas fa-file-export"></i> Export Follow-ups Data
                    </button>
                </div>
            </div>

            <div class="settings-actions">
                <button class="btn btn-primary">Save Backup Settings</button>
            </div>
        </div>
    </div>
</div>

<style>
/* Settings specific styles */
.settings-tabs {
    display: flex;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 12px 12px 0 0;
    overflow-x: auto;
    margin-bottom: 0;
}

.settings-tab-btn {
    padding: 16px 24px;
    background: none;
    border: none;
    font-size: 14px;
    font-weight: 600;
    color: #6B7280;
    cursor: pointer;
    transition: all 0.3s ease;
    border-bottom: 3px solid transparent;
    white-space: nowrap;
    display: flex;
    align-items: center;
    gap: 8px;
}

.settings-tab-btn.active {
    color: #3B82F6;
    border-bottom-color: #3B82F6;
    background: rgba(59, 130, 246, 0.05);
}

.settings-content {
    display: none;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-top: none;
    border-radius: 0 0 12px 12px;
    padding: 32px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
}

.settings-content.active {
    display: block;
}

.settings-section {
    margin-bottom: 32px;
}

.settings-section h3 {
    font-size: 18px;
    font-weight: 600;
    color: #1F2937;
    margin-bottom: 20px;
    padding-bottom: 12px;
    border-bottom: 2px solid #E5E7EB;
}

.form-section {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-group label {
    font-size: 14px;
    font-weight: 600;
    color: #374151;
    display: flex;
    align-items: center;
    gap: 8px;
}

.form-group input,
.form-group select,
.form-group textarea {
    padding: 12px 16px;
    border: 1px solid #D1D5DB;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.3s ease;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #3B82F6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-group input[type="checkbox"] {
    width: auto;
    margin: 0;
}

.form-group input[type="color"] {
    width: 60px;
    height: 40px;
    padding: 4px;
    border-radius: 6px;
}

/* Template Styles */
.template-list {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.template-item {
    background: #F9FAFB;
    border: 1px solid #E5E7EB;
    border-radius: 12px;
    padding: 20px;
}

.template-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}

.template-header h4 {
    font-size: 16px;
    font-weight: 600;
    color: #1F2937;
}

.template-preview {
    background: white;
    border: 1px solid #E5E7EB;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 16px;
    font-size: 14px;
    line-height: 1.6;
}

.preview-subject {
    margin-bottom: 12px;
    padding-bottom: 12px;
    border-bottom: 1px solid #E5E7EB;
}

.template-variables {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.variable {
    background: #EBF5FF;
    color: #1E40AF;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-family: monospace;
}

/* Backup Actions */
.backup-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
}

/* Settings Actions */
.settings-actions {
    display: flex;
    gap: 16px;
    margin-top: 32px;
    padding-top: 24px;
    border-top: 1px solid #E5E7EB;
}

.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.btn-primary {
    background: linear-gradient(135deg, #3B82F6, #1D4ED8);
    color: white;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.btn-secondary {
    background: linear-gradient(135deg, #6B7280, #4B5563);
    color: white;
    box-shadow: 0 4px 12px rgba(107, 114, 128, 0.3);
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
}

.btn.btn-sm {
    padding: 8px 16px;
    font-size: 12px;
}

@media (max-width: 768px) {
    .settings-tabs {
        overflow-x: auto;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .settings-actions {
        flex-direction: column;
        align-items: stretch;
    }
    
    .backup-actions {
        flex-direction: column;
        align-items: stretch;
    }
}
</style>

</style>

@push('scripts')
<script>
    // Global variables
    let settings = {};
    let currentSettings = {};

    // Settings page initialization
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Settings page initialized');
        
        // Load settings data
        loadSettings();
        
        // Settings Tab Functionality
        const tabButtons = document.querySelectorAll('.settings-tab-btn');
        const tabContents = document.querySelectorAll('.settings-content');

        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const targetTab = this.getAttribute('data-tab');
                
                // Remove active class from all tabs and contents
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabContents.forEach(content => content.classList.remove('active'));
                
                // Add active class to clicked tab and corresponding content
                this.classList.add('active');
                document.getElementById(targetTab + 'Settings').classList.add('active');
            });
        });
        
        // Save settings functionality
        const saveButtons = document.querySelectorAll('.btn-primary');
        saveButtons.forEach(button => {
            button.addEventListener('click', function() {
                saveSettings();
            });
        });
        
        // Reset functionality
        const resetButtons = document.querySelectorAll('.btn-secondary');
        resetButtons.forEach(button => {
            button.addEventListener('click', function() {
                if (confirm('Are you sure you want to reset to defaults?')) {
                    resetSettings();
                }
            });
        });
        
        // Backup functionality
        const backupButtons = document.querySelectorAll('.backup-actions .btn');
        backupButtons.forEach(button => {
            button.addEventListener('click', function() {
                createBackup();
            });
        });
    });

    // Load settings from API
    function loadSettings() {
        fetch('/api/settings')
            .then(response => response.json())
            .then(data => {
                settings = data.settings || {};
                currentSettings = {...settings};
                populateSettingsForm();
            })
            .catch(error => {
                console.error('Error loading settings:', error);
                showNotification('Error loading settings. Please refresh the page.', 'error');
            });
    }

    // Populate settings form
    function populateSettingsForm() {
        // Company Information
        if (settings.company_name) document.getElementById('companyName').value = settings.company_name;
        if (settings.company_email) document.getElementById('companyEmail').value = settings.company_email;
        if (settings.company_phone) document.getElementById('companyPhone').value = settings.company_phone;
        if (settings.company_address) document.getElementById('companyAddress').value = settings.company_address;
        
        // Business Settings
        if (settings.business_reg_no) document.getElementById('businessRegNo').value = settings.business_reg_no;
        if (settings.gst_number) document.getElementById('gstNumber').value = settings.gst_number;
        if (settings.timezone) document.getElementById('timezone').value = settings.timezone;
        if (settings.currency) document.getElementById('currency').value = settings.currency;
        
        // Notification Settings
        if (settings.email_notifications) document.getElementById('emailNotifications').checked = settings.email_notifications;
        if (settings.sms_notifications) document.getElementById('smsNotifications').checked = settings.sms_notifications;
        if (settings.whatsapp_notifications) document.getElementById('whatsappNotifications').checked = settings.whatsapp_notifications;
        
        // Security Settings
        if (settings.session_timeout) document.getElementById('sessionTimeout').value = settings.session_timeout;
        if (settings.password_expiry) document.getElementById('passwordExpiry').value = settings.password_expiry;
        if (settings.two_factor_auth) document.getElementById('twoFactorAuth').checked = settings.two_factor_auth;
    }

    // Save settings
    function saveSettings() {
        const formData = new FormData();
        
        // Collect all form data
        formData.append('company_name', document.getElementById('companyName').value);
        formData.append('company_email', document.getElementById('companyEmail').value);
        formData.append('company_phone', document.getElementById('companyPhone').value);
        formData.append('company_address', document.getElementById('companyAddress').value);
        formData.append('business_reg_no', document.getElementById('businessRegNo').value);
        formData.append('gst_number', document.getElementById('gstNumber').value);
        formData.append('timezone', document.getElementById('timezone').value);
        formData.append('currency', document.getElementById('currency').value);
        
        // Notification settings
        formData.append('email_notifications', document.getElementById('emailNotifications').checked);
        formData.append('sms_notifications', document.getElementById('smsNotifications').checked);
        formData.append('whatsapp_notifications', document.getElementById('whatsappNotifications').checked);
        
        // Security settings
        formData.append('session_timeout', document.getElementById('sessionTimeout').value);
        formData.append('password_expiry', document.getElementById('passwordExpiry').value);
        formData.append('two_factor_auth', document.getElementById('twoFactorAuth').checked);

        fetch('/api/settings', {
            method: 'POST',
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
                currentSettings = {...settings};
            } else if (data.errors) {
                showNotification('Please check the form and try again.', 'error');
            }
        })
        .catch(error => {
            console.error('Error saving settings:', error);
            showNotification('Error saving settings. Please try again.', 'error');
        });
    }

    // Reset settings
    function resetSettings() {
        fetch('/api/settings/reset', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            showNotification(data.message, 'success');
            loadSettings(); // Reload settings
        })
        .catch(error => {
            console.error('Error resetting settings:', error);
            showNotification('Error resetting settings. Please try again.', 'error');
        });
    }

    // Create backup
    function createBackup() {
        fetch('/api/settings/backup', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            showNotification(data.message, 'success');
        })
        .catch(error => {
            console.error('Error creating backup:', error);
            showNotification('Error creating backup. Please try again.', 'error');
        });
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
</script>
@endpush

@endsection
