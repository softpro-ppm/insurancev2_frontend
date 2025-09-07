@extends('layouts.insurance')

@section('title', 'Notifications - Insurance Management System')

@section('content')
<div class="page active" id="notifications">
    <div class="page-header">
        <h1>Notifications</h1>
    </div>
    <div class="page-content">
        <!-- Notification Statistics -->
        <div class="notifications-stats">
            <div class="stat-card glass-effect">
                <div class="stat-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-content">
                    <h3 id="urgentCount">15</h3>
                    <p>Urgent Notifications</p>
                </div>
            </div>
            <div class="stat-card glass-effect">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <h3 id="scheduledCount">8</h3>
                    <p>Scheduled Notifications</p>
                </div>
            </div>
            <div class="stat-card glass-effect">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3 id="completedToday">42</h3>
                    <p>Completed Today</p>
                </div>
            </div>
            <div class="stat-card glass-effect">
                <div class="stat-icon">
                    <i class="fas fa-paper-plane"></i>
                </div>
                <div class="stat-content">
                    <h3 id="sentToday">156</h3>
                    <p>Notifications Sent Today</p>
                </div>
            </div>
        </div>

        <!-- Notification Controls -->
        <div class="notification-controls">
            <div class="controls-left">
                <button class="btn btn-primary" id="sendBulkNotifications">
                    <i class="fas fa-paper-plane"></i> Send Bulk Notifications
                </button>
                <button class="btn btn-secondary" id="scheduleNotifications">
                    <i class="fas fa-calendar-alt"></i> Schedule Notifications
                </button>
                <button class="btn btn-info" id="viewAnalytics">
                    <i class="fas fa-chart-line"></i> View Analytics
                </button>
                <button class="btn btn-warning" id="manageTemplates">
                    <i class="fas fa-edit"></i> Manage Templates
                </button>
            </div>
            <div class="controls-right">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="notificationSearch" placeholder="Search notifications...">
                </div>
                <select id="notificationFilter">
                    <option value="all">All Types</option>
                    <option value="expiring">Expiring Policies</option>
                    <option value="renewals">Renewals</option>
                    <option value="followups">Follow-ups</option>
                    <option value="custom">Custom</option>
                </select>
            </div>
        </div>

        <!-- Analytics Overview -->
        <div class="analytics-overview">
            <h3>Performance Analytics</h3>
            <div class="analytics-grid">
                <div class="analytics-card glass-effect">
                    <div class="analytics-header">
                        <h4>Delivery Rate</h4>
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="analytics-value">95.2%</div>
                    <div class="analytics-change positive">+2.1%</div>
                    <div class="analytics-chart">
                        <canvas id="deliveryRateChart" width="200" height="60"></canvas>
                    </div>
                </div>
                <div class="analytics-card glass-effect">
                    <div class="analytics-header">
                        <h4>Open Rate</h4>
                        <i class="fas fa-envelope-open"></i>
                    </div>
                    <div class="analytics-value">67.8%</div>
                    <div class="analytics-change positive">+5.3%</div>
                    <div class="analytics-chart">
                        <canvas id="openRateChart" width="200" height="60"></canvas>
                    </div>
                </div>
                <div class="analytics-card glass-effect">
                    <div class="analytics-header">
                        <h4>Response Rate</h4>
                        <i class="fas fa-reply"></i>
                    </div>
                    <div class="analytics-value">12.1%</div>
                    <div class="analytics-change negative">-1.2%</div>
                    <div class="analytics-chart">
                        <canvas id="responseRateChart" width="200" height="60"></canvas>
                    </div>
                </div>
                <div class="analytics-card glass-effect">
                    <div class="analytics-header">
                        <h4>Channel Performance</h4>
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <div class="channel-stats">
                        <div class="channel-stat">
                            <span class="channel-name">Email</span>
                            <span class="channel-rate">98.5%</span>
                        </div>
                        <div class="channel-stat">
                            <span class="channel-name">WhatsApp</span>
                            <span class="channel-rate">87.2%</span>
                        </div>
                        <div class="channel-stat">
                            <span class="channel-name">SMS</span>
                            <span class="channel-rate">92.1%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scheduled Notifications -->
        <div class="scheduled-notifications">
            <h3>Upcoming Scheduled Notifications</h3>
            <div class="schedule-grid">
                <div class="schedule-card">
                    <div class="schedule-header">
                        <div class="schedule-time">
                            <i class="fas fa-clock"></i>
                            <span>Tomorrow, 10:00 AM</span>
                        </div>
                        <div class="schedule-status pending">Pending</div>
                    </div>
                    <div class="schedule-content">
                        <h4>Policy Renewal Reminders</h4>
                        <p>Send renewal reminders to 23 customers with policies expiring next week</p>
                        <div class="schedule-details">
                            <span class="schedule-recipients">23 recipients</span>
                            <span class="schedule-channels">Email, WhatsApp</span>
                        </div>
                    </div>
                    <div class="schedule-actions">
                        <button class="btn btn-sm btn-primary">Edit</button>
                        <button class="btn btn-sm btn-secondary">Cancel</button>
                    </div>
                </div>
                <div class="schedule-card">
                    <div class="schedule-header">
                        <div class="schedule-time">
                            <i class="fas fa-clock"></i>
                            <span>Dec 20, 2:30 PM</span>
                        </div>
                        <div class="schedule-status recurring">Recurring</div>
                    </div>
                    <div class="schedule-content">
                        <h4>Weekly Follow-up Reminders</h4>
                        <p>Weekly follow-up reminders for pending customer inquiries</p>
                        <div class="schedule-details">
                            <span class="schedule-recipients">15 recipients</span>
                            <span class="schedule-channels">WhatsApp</span>
                        </div>
                    </div>
                    <div class="schedule-actions">
                        <button class="btn btn-sm btn-primary">Edit</button>
                        <button class="btn btn-sm btn-secondary">Cancel</button>
                    </div>
                </div>
                <div class="schedule-card">
                    <div class="schedule-header">
                        <div class="schedule-time">
                            <i class="fas fa-clock"></i>
                            <span>Dec 25, 9:00 AM</span>
                        </div>
                        <div class="schedule-status pending">Pending</div>
                    </div>
                    <div class="schedule-content">
                        <h4>Commission Alerts</h4>
                        <p>Send commission notifications to agents for December earnings</p>
                        <div class="schedule-details">
                            <span class="schedule-recipients">8 recipients</span>
                            <span class="schedule-channels">Email</span>
                        </div>
                    </div>
                    <div class="schedule-actions">
                        <button class="btn btn-sm btn-primary">Edit</button>
                        <button class="btn btn-sm btn-secondary">Cancel</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Notifications -->
        <div class="notifications-section">
            <h3>Active Notifications</h3>
            <div class="notification-list">
                <div class="notification-item urgent">
                    <div class="notification-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="notification-content">
                        <h4>15 Policies Expiring Tomorrow</h4>
                        <p>Send renewal reminders via email & WhatsApp to prevent policy lapses</p>
                        <div class="notification-meta">
                            <span class="notification-time">2 hours ago</span>
                            <span class="notification-priority">High Priority</span>
                        </div>
                        <div class="notification-actions">
                            <button class="btn btn-sm btn-success" onclick="sendBulkEmail('expiring')">
                                <i class="fas fa-envelope"></i> Send Email
                            </button>
                            <button class="btn btn-sm btn-success" onclick="sendBulkWhatsApp('expiring')">
                                <i class="fab fa-whatsapp"></i> Send WhatsApp
                            </button>
                            <button class="btn btn-sm btn-info" onclick="viewNotificationDetails('expiring')">
                                <i class="fas fa-eye"></i> View Details
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="notification-item warning">
                    <div class="notification-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="notification-content">
                        <h4>8 Follow-ups Due Today</h4>
                        <p>Telecallers need to contact customers for pending follow-ups</p>
                        <div class="notification-meta">
                            <span class="notification-time">4 hours ago</span>
                            <span class="notification-priority">Medium Priority</span>
                        </div>
                        <div class="notification-actions">
                            <button class="btn btn-sm btn-warning" onclick="sendBulkEmail('followups')">
                                <i class="fas fa-envelope"></i> Notify Agents
                            </button>
                            <button class="btn btn-sm btn-warning" onclick="sendBulkWhatsApp('followups')">
                                <i class="fab fa-whatsapp"></i> Notify Agents
                            </button>
                            <button class="btn btn-sm btn-info" onclick="viewNotificationDetails('followups')">
                                <i class="fas fa-eye"></i> View Details
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="notification-item info">
                    <div class="notification-icon">
                        <i class="fas fa-sync-alt"></i>
                    </div>
                    <div class="notification-content">
                        <h4>23 Renewals Due This Week</h4>
                        <p>Send renewal reminders to customers with policies expiring this week</p>
                        <div class="notification-meta">
                            <span class="notification-time">1 day ago</span>
                            <span class="notification-priority">Normal Priority</span>
                        </div>
                        <div class="notification-actions">
                            <button class="btn btn-sm btn-info" onclick="sendBulkEmail('renewals')">
                                <i class="fas fa-envelope"></i> Send Email
                            </button>
                            <button class="btn btn-sm btn-info" onclick="sendBulkWhatsApp('renewals')">
                                <i class="fab fa-whatsapp"></i> Send WhatsApp
                            </button>
                            <button class="btn btn-sm btn-info" onclick="viewNotificationDetails('renewals')">
                                <i class="fas fa-eye"></i> View Details
                            </button>
                        </div>
                    </div>
                </div>

                <div class="notification-item success">
                    <div class="notification-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="notification-content">
                        <h4>Commission Alerts Sent</h4>
                        <p>Successfully sent commission notifications to 12 agents</p>
                        <div class="notification-meta">
                            <span class="notification-time">3 hours ago</span>
                            <span class="notification-status">Completed</span>
                        </div>
                        <div class="notification-actions">
                            <button class="btn btn-sm btn-secondary" onclick="viewNotificationHistory('commission')">
                                <i class="fas fa-history"></i> View History
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notification History -->
        <div class="notifications-section">
            <h3>Recent Notification History</h3>
            <div class="data-table-container">
                <table class="data-table" id="notificationHistoryTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Recipients</th>
                            <th>Channels</th>
                            <th>Status</th>
                            <th>Success Rate</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="notificationHistoryBody">
                        <tr>
                            <td>2025-02-01 10:30</td>
                            <td>Policy Renewal</td>
                            <td>23</td>
                            <td>Email, WhatsApp</td>
                            <td><span class="status-badge completed">Completed</span></td>
                            <td>95.7%</td>
                            <td>
                                <button class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>2025-02-01 14:15</td>
                            <td>Follow-up</td>
                            <td>8</td>
                            <td>WhatsApp</td>
                            <td><span class="status-badge completed">Completed</span></td>
                            <td>100%</td>
                            <td>
                                <button class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
/* Notifications specific styles */
.notifications-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 32px;
}

.notification-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 16px;
}

.controls-left {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.controls-right {
    display: flex;
    gap: 12px;
    align-items: center;
}

/* Analytics Overview */
.analytics-overview {
    margin-bottom: 32px;
}

.analytics-overview h3 {
    margin-bottom: 20px;
    color: #1F2937;
    font-size: 18px;
    font-weight: 600;
}

.analytics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
}

.analytics-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
}

.analytics-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}

.analytics-header h4 {
    font-size: 14px;
    color: #6B7280;
    font-weight: 600;
}

.analytics-header i {
    color: #3B82F6;
    font-size: 20px;
}

.analytics-value {
    font-size: 28px;
    font-weight: 700;
    color: #1F2937;
    margin-bottom: 8px;
}

.analytics-change {
    font-size: 12px;
    font-weight: 600;
    padding: 2px 6px;
    border-radius: 4px;
    margin-bottom: 16px;
    display: inline-block;
}

.analytics-change.positive {
    background: rgba(16, 185, 129, 0.1);
    color: #10B981;
}

.analytics-change.negative {
    background: rgba(239, 68, 68, 0.1);
    color: #EF4444;
}

.channel-stats {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.channel-stat {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 6px 0;
}

.channel-name {
    font-size: 14px;
    color: #6B7280;
}

.channel-rate {
    font-size: 14px;
    font-weight: 600;
    color: #1F2937;
}

/* Scheduled Notifications */
.scheduled-notifications {
    margin-bottom: 32px;
}

.scheduled-notifications h3 {
    margin-bottom: 20px;
    color: #1F2937;
    font-size: 18px;
    font-weight: 600;
}

.schedule-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 20px;
}

.schedule-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
}

.schedule-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.schedule-time {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #6B7280;
    font-size: 14px;
}

.schedule-status {
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.schedule-status.pending {
    background: rgba(245, 158, 11, 0.1);
    color: #F59E0B;
}

.schedule-status.recurring {
    background: rgba(59, 130, 246, 0.1);
    color: #3B82F6;
}

.schedule-content h4 {
    font-size: 16px;
    font-weight: 600;
    color: #1F2937;
    margin-bottom: 8px;
}

.schedule-content p {
    font-size: 14px;
    color: #6B7280;
    margin-bottom: 12px;
}

.schedule-details {
    display: flex;
    gap: 16px;
    font-size: 12px;
    color: #6B7280;
    margin-bottom: 16px;
}

.schedule-actions {
    display: flex;
    gap: 8px;
}

/* Notification Items */
.notification-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.notification-item {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    padding: 20px;
    display: flex;
    gap: 16px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
    border-left: 4px solid;
}

.notification-item.urgent {
    border-left-color: #EF4444;
}

.notification-item.warning {
    border-left-color: #F59E0B;
}

.notification-item.info {
    border-left-color: #3B82F6;
}

.notification-item.success {
    border-left-color: #10B981;
}

.notification-icon {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: white;
    flex-shrink: 0;
}

.notification-item.urgent .notification-icon {
    background: linear-gradient(135deg, #EF4444, #DC2626);
}

.notification-item.warning .notification-icon {
    background: linear-gradient(135deg, #F59E0B, #D97706);
}

.notification-item.info .notification-icon {
    background: linear-gradient(135deg, #3B82F6, #1D4ED8);
}

.notification-item.success .notification-icon {
    background: linear-gradient(135deg, #10B981, #059669);
}

.notification-content {
    flex: 1;
}

.notification-content h4 {
    font-size: 16px;
    font-weight: 600;
    color: #1F2937;
    margin-bottom: 8px;
}

.notification-content p {
    font-size: 14px;
    color: #6B7280;
    margin-bottom: 12px;
}

.notification-meta {
    display: flex;
    gap: 16px;
    font-size: 12px;
    color: #6B7280;
    margin-bottom: 16px;
}

.notification-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.btn {
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: all 0.3s ease;
}

.btn.btn-sm {
    padding: 6px 12px;
    font-size: 11px;
}

.btn-primary {
    background: linear-gradient(135deg, #3B82F6, #1D4ED8);
    color: white;
}

.btn-secondary {
    background: linear-gradient(135deg, #6B7280, #4B5563);
    color: white;
}

.btn-success {
    background: linear-gradient(135deg, #10B981, #059669);
    color: white;
}

.btn-warning {
    background: linear-gradient(135deg, #F59E0B, #D97706);
    color: white;
}

.btn-info {
    background: linear-gradient(135deg, #0EA5E9, #0284C7);
    color: white;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

/* Notifications specific styles */
.notification-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 16px;
}

.controls-left {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.controls-right {
    display: flex;
    gap: 12px;
    align-items: center;
}
</style>

<!-- Include Modals -->
@include('components.bulk-notification-modal')
@include('components.schedule-notification-modal')

@push('scripts')
<script>
    // Global variables
    let notifications = [];
    let filteredNotifications = [];
    let currentPage = 1;
    let rowsPerPage = 10;

    // Notifications page initialization
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Notifications page initialized');
        
    // Load notifications data
    loadNotifications();
        
        // Initialize analytics charts if Chart.js is available
        if (typeof Chart !== 'undefined') {
            // Delivery Rate Chart
            const deliveryRateCtx = document.getElementById('deliveryRateChart');
            if (deliveryRateCtx) {
                new Chart(deliveryRateCtx, {
                    type: 'line',
                    data: {
                        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                        datasets: [{
                            label: 'Delivery Rate',
                            data: [92, 94, 95, 96, 95, 97, 95],
                            borderColor: '#10B981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            tension: 0.4,
                            fill: true
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
                            y: {
                                beginAtZero: true,
                                max: 100
                            }
                        }
                    }
                });
            }

            // Open Rate Chart
            const openRateCtx = document.getElementById('openRateChart');
            if (openRateCtx) {
                new Chart(openRateCtx, {
                    type: 'line',
                    data: {
                        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                        datasets: [{
                            label: 'Open Rate',
                            data: [65, 68, 70, 72, 69, 71, 68],
                            borderColor: '#3B82F6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.4,
                            fill: true
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
                            y: {
                                beginAtZero: true,
                                max: 100
                            }
                        }
                    }
                });
            }

            // Response Rate Chart
            const responseRateCtx = document.getElementById('responseRateChart');
            if (responseRateCtx) {
                new Chart(responseRateCtx, {
                    type: 'line',
                    data: {
                        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                        datasets: [{
                            label: 'Response Rate',
                            data: [12, 14, 13, 15, 12, 11, 13],
                            borderColor: '#F59E0B',
                            backgroundColor: 'rgba(245, 158, 11, 0.1)',
                            tension: 0.4,
                            fill: true
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
                            y: {
                                beginAtZero: true,
                                max: 20
                            }
                        }
                    }
                });
            }
        }
        
        // Button functionality
        const sendBulkBtn = document.getElementById('sendBulkNotifications');
        if (sendBulkBtn) {
            sendBulkBtn.addEventListener('click', function() {
                const bulkModal = document.getElementById('bulkNotificationModal');
                if (bulkModal) {
                    bulkModal.classList.add('show');
                }
            });
        }
        
        const scheduleBtn = document.getElementById('scheduleNotifications');
        if (scheduleBtn) {
            scheduleBtn.addEventListener('click', function() {
                const scheduleModal = document.getElementById('scheduleNotificationModal');
                if (scheduleModal) {
                    scheduleModal.classList.add('show');
                }
            });
        }
        
        const analyticsBtn = document.getElementById('viewAnalytics');
        if (analyticsBtn) {
            analyticsBtn.addEventListener('click', function() {
                console.log('Viewing analytics...');
                // Add analytics view functionality here
            });
        }
        
        const templatesBtn = document.getElementById('manageTemplates');
        if (templatesBtn) {
            templatesBtn.addEventListener('click', function() {
                console.log('Managing templates...');
                // Add template management functionality here
            });
        }

        // Search and filter
        const searchInput = document.getElementById('notificationSearch');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                applyFilters();
                renderNotificationsTable();
                updateStatistics();
            });
        }
        const filterSelect = document.getElementById('notificationFilter');
        if (filterSelect) {
            filterSelect.addEventListener('change', function() {
                applyFilters();
                renderNotificationsTable();
                updateStatistics();
            });
        }
    });

    // Load notifications from API
    function loadNotifications() {
        fetch('/api/notifications', { method: 'GET', credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
            .then(response => response.ok ? response.json() : Promise.reject(new Error(`${response.status} ${response.statusText}`)))
            .then(data => {
                notifications = data.notifications || [];
                // Default filtered list
                filteredNotifications = [...notifications];
                applyFilters();
                renderNotificationsTable();
                updateStatistics();
            })
            .catch(error => {
                console.error('Error loading notifications:', error);
                showNotification('Error loading notifications. Please refresh the page.', 'error');
            });
    }

    function applyFilters() {
        const q = (document.getElementById('notificationSearch')?.value || '').toLowerCase();
        const filter = (document.getElementById('notificationFilter')?.value || 'all').toLowerCase();
        filteredNotifications = (notifications || []).filter(n => {
            const hay = `${n.title || ''} ${n.message || ''} ${n.type || ''} ${n.recipient || ''}`.toLowerCase();
            const matchesQuery = !q || hay.includes(q);
            let matchesFilter = true;
            if (filter !== 'all') {
                // Map business filters to text search to avoid layout changes
                matchesFilter = hay.includes(filter);
            }
            return matchesQuery && matchesFilter;
        });
    }

    // Render notifications table
    function renderNotificationsTable() {
        const tableBody = document.getElementById('notificationHistoryBody');
        if (!tableBody) return;

        const startIndex = (currentPage - 1) * rowsPerPage;
        const endIndex = startIndex + rowsPerPage;
        const pageData = filteredNotifications.slice(startIndex, endIndex);

        tableBody.innerHTML = pageData.map(n => {
            const created = n.createdAt || '—';
            const type = n.type || '—';
            const recipients = n.recipient ? 1 : 0; // backend has single recipient field
            const channels = type; // show channel as the notification type (SMS/Email/Push)
            const status = n.status || 'Pending';
            const successRate = status === 'Sent' ? '100%' : (status === 'Failed' ? '0%' : '—');
            return `
            <tr>
                <td>${created}</td>
                <td>${n.title || '—'}</td>
                <td>${recipients}</td>
                <td>${channels}</td>
                <td><span class="status-badge ${status.toLowerCase()}">${status}</span></td>
                <td>${successRate}</td>
                <td>
                    <button class="btn btn-sm btn-info" onclick="viewNotification(${n.id})">
                        <i class="fas fa-eye"></i>
                    </button>
                </td>
            </tr>`;
        }).join('');
    }

    // Update statistics
    function updateStatistics() {
        const now = new Date();
        const todayStr = now.toISOString().slice(0,10);

        // Urgent: Pending and scheduled within next 24h (or overdue)
        const urgent = filteredNotifications.filter(n => {
            if ((n.status || '').toLowerCase() !== 'pending') return false;
            const sd = n.scheduledDate ? new Date(n.scheduledDate) : null;
            if (!sd) return false;
            const diffHrs = (sd - now) / (1000*60*60);
            return diffHrs <= 24; // includes overdue
        }).length;

        // Scheduled: Pending scheduled in future beyond 24h
        const scheduled = filteredNotifications.filter(n => {
            if ((n.status || '').toLowerCase() !== 'pending') return false;
            const sd = n.scheduledDate ? new Date(n.scheduledDate) : null;
            if (!sd) return false;
            const diffHrs = (sd - now) / (1000*60*60);
            return diffHrs > 24;
        }).length;

        // Completed today and Sent today (same metric from sentDate)
        const sentToday = filteredNotifications.filter(n => {
            return (n.status === 'Sent') && (n.sentDate || '').slice(0,10) === todayStr;
        }).length;

        // Update stat cards
        const setText = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = String(val); };
        setText('urgentCount', urgent);
        setText('scheduledCount', scheduled);
        setText('completedToday', sentToday);
        setText('sentToday', sentToday);
    }

    // Global functions
    window.viewNotification = function(id) {
        const notification = notifications.find(n => n.id === id);
        if (notification) {
            console.log('Viewing notification:', notification);
            // Add view modal functionality here
        }
    };

    // Stubs to avoid errors from HTML onclick attributes
    window.sendBulkEmail = function(kind) { console.log('sendBulkEmail', kind); };
    window.sendBulkWhatsApp = function(kind) { console.log('sendBulkWhatsApp', kind); };
    window.viewNotificationDetails = function(kind) { console.log('viewNotificationDetails', kind); };
    window.viewNotificationHistory = function(kind) { console.log('viewNotificationHistory', kind); };

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
