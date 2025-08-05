<div class="modal" id="scheduleNotificationModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Schedule Notification</h2>
            <button class="modal-close" id="closeScheduleModal">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="schedule-form">
                <div class="form-section">
                    <h3><i class="fas fa-calendar"></i> Schedule Details</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="scheduleDate">Date</label>
                            <input type="date" id="scheduleDate" required>
                        </div>
                        <div class="form-group">
                            <label for="scheduleTime">Time</label>
                            <input type="time" id="scheduleTime" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="scheduleType">Schedule Type</label>
                        <select id="scheduleType">
                            <option value="once">Send Once</option>
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                            <option value="custom">Custom</option>
                        </select>
                    </div>
                    <div class="form-group" id="recurringOptions" style="display: none;">
                        <label for="recurringDays">Recurring Days</label>
                        <div class="checkbox-group">
                            <label><input type="checkbox" value="monday"> Monday</label>
                            <label><input type="checkbox" value="tuesday"> Tuesday</label>
                            <label><input type="checkbox" value="wednesday"> Wednesday</label>
                            <label><input type="checkbox" value="thursday"> Thursday</label>
                            <label><input type="checkbox" value="friday"> Friday</label>
                            <label><input type="checkbox" value="saturday"> Saturday</label>
                            <label><input type="checkbox" value="sunday"> Sunday</label>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="fas fa-users"></i> Recipients</h3>
                    <div class="form-group">
                        <label for="scheduleFilterType">Filter by:</label>
                        <select id="scheduleFilterType">
                            <option value="expiring">Policies Expiring</option>
                            <option value="renewals">Pending Renewals</option>
                            <option value="followups">Follow-ups Due</option>
                            <option value="custom">Custom Selection</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="scheduleDaysRange">Days Range:</label>
                        <input type="number" id="scheduleDaysRange" value="7" min="1" max="90">
                    </div>
                    <div class="recipients-preview">
                        <h4>Recipients (<span id="scheduleRecipientsCount">0</span> customers)</h4>
                        <div class="recipients-list" id="scheduleRecipientsList">
                            <!-- Dynamic list of recipients -->
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="fas fa-bell"></i> Notification Settings</h3>
                    <div class="notification-channels">
                        <div class="channel-option">
                            <label>
                                <input type="checkbox" id="scheduleEmail" checked>
                                <i class="fas fa-envelope"></i> Email
                            </label>
                        </div>
                        <div class="channel-option">
                            <label>
                                <input type="checkbox" id="scheduleWhatsApp">
                                <i class="fab fa-whatsapp"></i> WhatsApp
                            </label>
                        </div>
                        <div class="channel-option">
                            <label>
                                <input type="checkbox" id="scheduleSMS">
                                <i class="fas fa-sms"></i> SMS
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="scheduleTemplate">Template</label>
                        <select id="scheduleTemplate">
                            <option value="policy_renewal">Policy Renewal Reminder</option>
                            <option value="followup">Follow-up Reminder</option>
                            <option value="commission">Commission Alert</option>
                            <option value="custom">Custom Template</option>
                        </select>
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="fas fa-eye"></i> Preview</h3>
                    <div class="message-preview">
                        <div class="preview-tabs">
                            <button class="preview-tab active" data-tab="email">Email</button>
                            <button class="preview-tab" data-tab="whatsapp">WhatsApp</button>
                            <button class="preview-tab" data-tab="sms">SMS</button>
                        </div>
                        <div class="preview-content">
                            <div class="preview-panel active" id="scheduleEmailPreview">
                                <div class="email-preview">
                                    <div class="email-header">
                                        <strong>Subject:</strong> Your [POLICY_TYPE] policy expires on [EXPIRY_DATE]
                                    </div>
                                    <div class="email-body">
                                        Dear [CUSTOMER_NAME],<br><br>
                                        Your [POLICY_TYPE] policy expires on [EXPIRY_DATE]. 
                                        Please renew to maintain continuous coverage.<br><br>
                                        Contact: [CONTACT_PHONE]<br><br>
                                        Best regards,<br>
                                        [COMPANY_NAME] Team
                                    </div>
                                </div>
                            </div>
                            <div class="preview-panel" id="scheduleWhatsAppPreview">
                                <div class="whatsapp-preview">
                                    <div class="whatsapp-message">
                                        Hi [CUSTOMER_NAME], your [POLICY_TYPE] policy expires on [EXPIRY_DATE]. 
                                        Renew now to avoid coverage lapse. Call [CONTACT_PHONE] for assistance.
                                    </div>
                                </div>
                            </div>
                            <div class="preview-panel" id="scheduleSmsPreview">
                                <div class="sms-preview">
                                    <div class="sms-message">
                                        [COMPANY_NAME]: Your [POLICY_TYPE] policy expires on [EXPIRY_DATE]. 
                                        Call [CONTACT_PHONE] to renew.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" id="cancelSchedule">Cancel</button>
            <button class="btn btn-primary" id="saveSchedule">Schedule Notification</button>
        </div>
    </div>
</div> 