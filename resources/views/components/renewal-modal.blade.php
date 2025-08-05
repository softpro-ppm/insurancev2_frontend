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
                    <div class="form-group">
                        <label for="renewalNotes">Notes</label>
                        <textarea id="renewalNotes" name="notes" rows="3" placeholder="Add any additional notes about this renewal..."></textarea>
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