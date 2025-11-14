<!-- Add/Edit Follow-up Modal -->
<div class="modal" id="followupModal">
    <div class="modal-content modal-large">
        <div class="modal-header">
            <h2 id="followupModalTitle">Add Follow Up</h2>
            <button class="modal-close" id="closeFollowupModal">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="followupForm" method="POST" action="{{ route('followups.store') }}">
            @csrf
            <div class="modal-body">
                <div class="form-section">
                    <h3><i class="fas fa-user"></i> Customer Information</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="followupCustomerName">Customer Name *</label>
                            <input type="text" id="followupCustomerName" name="customerName" required>
                        </div>
                        <div class="form-group">
                            <label for="followupPhone">Phone Number *</label>
                            <input type="tel" id="followupPhone" name="phone" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="followupEmail">Email</label>
                            <input type="email" id="followupEmail" name="email" placeholder="example@domain.com">
                        </div>
                        <div class="form-group">
                            <label for="followupPolicyId">Policy (Optional)</label>
                            <select id="followupPolicyId" name="policyId">
                                <option value="">Select Policy (Optional)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="fas fa-phone"></i> Follow-up Details</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="followupType">Follow-up Type *</label>
                            <select id="followupType" name="followupType" required>
                                <option value="">Select Type</option>
                                <option value="Renewal">Renewal</option>
                                <option value="New Policy">New Policy</option>
                                <option value="Claim">Claim</option>
                                <option value="General">General</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="followupStatus">Status *</label>
                            <select id="followupStatus" name="status" required>
                                <option value="">Select Status</option>
                                <option value="Pending">Pending</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Completed">Completed</option>
                                <option value="No Response">No Response</option>
                                <option value="Not Interested">Not Interested</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="followupAssignedTo">Assigned To</label>
                            <select id="followupAssignedTo" name="assignedTo">
                                <option value="">Select Telecaller</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="followupPriority">Priority</label>
                            <select id="followupPriority" name="priority">
                                <option value="">Select Priority</option>
                                <option value="High">High</option>
                                <option value="Medium">Medium</option>
                                <option value="Low">Low</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="followupNextDate">Next Follow-up Date</label>
                            <input type="date" id="followupNextDate" name="nextFollowupDate">
                        </div>
                        <div class="form-group">
                            <label for="followupReminderTime">Reminder Time</label>
                            <input type="time" id="followupReminderTime" name="reminderTime">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="followupNote">Add Note *</label>
                        <textarea id="followupNote" name="notes" rows="4" placeholder="Enter the conversation details, customer response, and any important information..." required></textarea>
                    </div>
                </div>

                <div class="form-section" id="previousNotesSection" style="display:none;">
                    <h3><i class="fas fa-clipboard-list"></i> Previous Notes</h3>
                    <div id="previousNotesContainer"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="cancelFollowup">Cancel</button>
                <button type="submit" id="saveFollowupBtn" class="btn btn-primary">Save Follow-up</button>
            </div>
        </form>
    </div>
</div> 