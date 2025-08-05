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
                            <input type="text" id="followupCustomerName" name="customer_name" required>
                        </div>
                        <div class="form-group">
                            <label for="followupPhone">Phone Number *</label>
                            <input type="tel" id="followupPhone" name="phone" required>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="fas fa-phone"></i> Follow-up Details</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="followupType">Follow-up Type *</label>
                            <select id="followupType" name="followup_type" required>
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
                    <div class="form-group">
                        <label for="followupNote">Add Note *</label>
                        <textarea id="followupNote" name="note" rows="4" placeholder="Enter the conversation details, customer response, and any important information..." required></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="cancelFollowup">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Follow-up</button>
            </div>
        </form>
    </div>
</div> 