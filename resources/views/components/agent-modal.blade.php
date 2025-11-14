<!-- Agent Modal -->
<div class="modal" id="agentModal">
    <div class="modal-content glass-effect">
        <div class="modal-header">
            <h2 id="agentModalTitle">Add New Agent</h2>
            <button class="modal-close" id="closeAgentModal">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="agentForm">
                <div class="form-row">
                    <div class="form-group">
                        <label for="agentNameInput">Agent Name *</label>
                        <input type="text" id="agentNameInput" name="name" required placeholder="Enter agent name">
                    </div>
                    <div class="form-group">
                        <label for="agentPhone">Phone Number *</label>
                        <input type="tel" id="agentPhone" name="phone" required placeholder="+91 98765 43210">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="agentEmail">Email Address *</label>
                        <input type="email" id="agentEmail" name="email" required placeholder="agent@example.com">
                    </div>
                    <div class="form-group">
                        <label for="agentUserId">User ID</label>
                        <input type="text" id="agentUserId" name="user_id" readonly placeholder="Auto-generated">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="agentStatus">Status</label>
                        <select id="agentStatus" name="status">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="agentPassword">Password *</label>
                        <input type="password" id="agentPassword" name="password" required placeholder="Enter password">
                    </div>
                </div>
                <div class="form-group">
                    <label for="agentAddress">Address</label>
                    <textarea id="agentAddress" name="address" rows="3" placeholder="Enter agent address"></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" id="cancelAgent">Cancel</button>
            <button type="button" class="btn btn-primary" id="saveAgentBtn">Add Agent</button>
        </div>
    </div>
</div>

<!-- View Agent Modal -->
<div class="modal" id="viewAgentModal">
    <div class="modal-content glass-effect">
        <div class="modal-header">
            <h2>Agent Details</h2>
            <button class="modal-close" id="closeViewAgentModal">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="agent-details">
                <div class="detail-row">
                    <div class="detail-group">
                        <label>Agent Name:</label>
                        <span id="viewAgentName"></span>
                    </div>
                    <div class="detail-group">
                        <label>Phone Number:</label>
                        <span id="viewAgentPhone"></span>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-group">
                        <label>Email Address:</label>
                        <span id="viewAgentEmail"></span>
                    </div>
                    <div class="detail-group">
                        <label>User ID:</label>
                        <span id="viewAgentUserId"></span>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-group">
                        <label>Status:</label>
                        <span id="viewAgentStatus"></span>
                    </div>
                    <div class="detail-group">
                        <label>Policies:</label>
                        <span id="viewAgentPolicies"></span>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-group">
                        <label>Performance:</label>
                        <span id="viewAgentPerformance"></span>
                    </div>
                    <div class="detail-group">
                        <label>Address:</label>
                        <span id="viewAgentAddress"></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" id="closeViewAgent">Close</button>
        </div>
    </div>
</div>

<style>
/* Agent Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(5px);
}

.modal.show {
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-content {
    background: white;
    border-radius: 16px;
    width: 90%;
    max-width: 600px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    animation: modalSlideIn 0.3s ease-out;
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-50px) scale(0.9);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 24px 24px 0 24px;
    border-bottom: 1px solid #E5E7EB;
    margin-bottom: 24px;
}

.modal-header h2 {
    font-size: 20px;
    font-weight: 600;
    color: #1F2937;
    margin: 0;
}

.modal-close {
    background: none;
    border: none;
    font-size: 18px;
    color: #6B7280;
    cursor: pointer;
    padding: 8px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.modal-close:hover {
    background: #F3F4F6;
    color: #374151;
}

.modal-body {
    padding: 0 24px 24px 24px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin-bottom: 16px;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group label {
    font-size: 14px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
}

.form-group input,
.form-group select,
.form-group textarea {
    padding: 12px 16px;
    border: 2px solid #E5E7EB;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.3s ease;
    background: white;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #4F46E5;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

.form-group input[readonly] {
    background: #F9FAFB;
    color: #6B7280;
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    padding: 24px;
    border-top: 1px solid #E5E7EB;
    background: #F9FAFB;
    border-radius: 0 0 16px 16px;
}

/* Agent Details Styles */
.agent-details {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.detail-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.detail-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.detail-group label {
    font-size: 14px;
    font-weight: 600;
    color: #6B7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.detail-group span {
    font-size: 16px;
    color: #1F2937;
    font-weight: 500;
    padding: 8px 12px;
    background: #F9FAFB;
    border-radius: 6px;
    border: 1px solid #E5E7EB;
}

/* Responsive Design */
@media (max-width: 768px) {
    .modal-content {
        width: 95%;
        margin: 20px;
    }
    
    .form-row,
    .detail-row {
        grid-template-columns: 1fr;
    }
    
    .modal-header,
    .modal-body,
    .modal-footer {
        padding: 16px;
    }
}
</style> 