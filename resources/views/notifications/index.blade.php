@extends('layouts.insurance')

@section('title', 'Notifications - Insurance Management System 2.0')

@section('content')
<div class="page-header">
    <h1>Notification Center</h1>
    <p>Manage and send notifications to customers and agents</p>
</div>

<!-- Notification Stats -->
<div class="notification-stats">
    <div class="stat-card">
        <div class="stat-icon urgent">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="stat-content">
            <h3 id="expiringCount">15</h3>
            <p>Policies Expiring Tomorrow</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon warning">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-content">
            <h3 id="followupsCount">8</h3>
            <p>Follow-ups Due Today</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon info">
            <i class="fas fa-sync-alt"></i>
        </div>
        <div class="stat-content">
            <h3 id="renewalsCount">23</h3>
            <p>Renewals Due This Week</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon success">
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
        <div class="analytics-card">
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
        <div class="analytics-card">
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
        <div class="analytics-card">
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
        <div class="analytics-card">
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
                <!-- Dynamic content -->
            </tbody>
        </table>
    </div>
</div>
@endsection
