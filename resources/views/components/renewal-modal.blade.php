<!-- Add/Edit Renewal Modal -->
<div class="modal" id="renewalModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="renewalModalTitle">Add Renewal Reminder</h2>
            <button class="modal-close" id="closeRenewalModal">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="renewalForm" method="POST" action="{{ route('renewals.store') }}">
            @csrf
            <div class="modal-body">
                <div class="form-section">
                    <h3><i class="fas fa-file-contract"></i> Policy Information</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="renewalPolicyId">Policy ID *</label>
                            <select id="renewalPolicyId" name="policy_id" required>
                                <option value="">Select Policy</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="renewalCustomerName">Customer Name</label>
                            <input type="text" id="renewalCustomerName" name="customer_name" readonly>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="renewalPolicyType">Policy Type</label>
                            <input type="text" id="renewalPolicyType" name="policy_type" readonly>
                        </div>
                        <div class="form-group">
                            <label for="renewalExpiryDate">Expiry Date</label>
                            <input type="date" id="renewalExpiryDate" name="expiry_date" readonly>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="fas fa-calendar-alt"></i> Renewal Details</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="renewalReminderDate">Reminder Date *</label>
                            <input type="date" id="renewalReminderDate" name="reminder_date" required>
                        </div>
                        <div class="form-group">
                            <label for="renewalPriority">Priority *</label>
                            <select id="renewalPriority" name="priority" required>
                                <option value="">Select Priority</option>
                                <option value="High">High</option>
                                <option value="Medium">Medium</option>
                                <option value="Low">Low</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="renewalStatus">Status</label>
                            <select id="renewalStatus" name="status">
                                <option value="">Select Status</option>
                                <option value="Pending" selected>Pending</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Completed">Completed</option>
                                <option value="Overdue">Overdue</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="renewalAssignedTo">Assigned To</label>
                            <select id="renewalAssignedTo" name="assigned_to">
                                <option value="">Unassigned</option>
                                <option value="Agent 1">Agent 1</option>
                                <option value="Agent 2">Agent 2</option>
                                <option value="Reception">Reception</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="renewalNotes">Notes</label>
                        <textarea id="renewalNotes" name="notes" rows="3" placeholder="Add any additional notes about this renewal..."></textarea>
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="fas fa-bell"></i> Notification Settings</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" id="renewalEmailNotification" name="email_notification">
                                <span>Email Notification</span>
                            </label>
                        </div>
                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" id="renewalSMSNotification" name="sms_notification">
                                <span>SMS Notification</span>
                            </label>
                        </div>
                        <div class="form-group">
                            <label for="renewalNotificationDays">Notify Before (days)</label>
                            <input type="number" id="renewalNotificationDays" name="notify_before_days" min="0" placeholder="7">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="cancelRenewal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Renewal</button>
            </div>
        </form>
    </div>
</div> 