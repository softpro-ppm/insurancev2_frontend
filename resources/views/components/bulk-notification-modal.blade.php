<!-- Bulk Notification Modal -->
<div class="modal" id="bulkNotificationModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Send Bulk Notifications</h2>
            <button class="modal-close" id="closeBulkModal">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="notification-filters">
                <div class="form-group">
                    <label>Filter by:</label>
                    <select id="bulkFilterType">
                        <option value="expiring">Policies Expiring</option>
                        <option value="renewals">Pending Renewals</option>
                        <option value="followups">Follow-ups Due</option>
                        <option value="custom">Custom Selection</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Days Range:</label>
                    <input type="number" id="bulkDaysRange" value="7" min="1" max="90">
                </div>
            </div>
            
            <div class="recipients-preview">
                <h4>Recipients (<span id="recipientsCount">0</span> customers)</h4>
                <div class="recipients-list" id="recipientsList">
                    <!-- Dynamic list of recipients -->
                </div>
            </div>
            
            <div class="notification-channels">
                <h4>Notification Channels</h4>
                <div class="channel-options">
                    <div class="channel-option">
                        <label>
                            <input type="checkbox" id="bulkEmail" checked>
                            <i class="fas fa-envelope"></i> Email
                        </label>
                    </div>
                    <div class="channel-option">
                        <label>
                            <input type="checkbox" id="bulkWhatsApp">
                            <i class="fab fa-whatsapp"></i> WhatsApp
                        </label>
                    </div>
                    <div class="channel-option">
                        <label>
                            <input type="checkbox" id="bulkSMS">
                            <i class="fas fa-sms"></i> SMS
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" id="cancelBulk">Cancel</button>
            <button class="btn btn-primary" id="sendBulkNotifications">Send Notifications</button>
        </div>
    </div>
</div> 