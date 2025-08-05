@extends('layouts.admin')

@section('title', 'Notifications - Insurance Management System 2.0')

@section('content')
<div class="page" id="notifications">
    <div class="page-header">
        <h1>Notifications</h1>
        <p>Manage system notifications and alerts</p>
    </div>
    <div class="page-content">
        <!-- Notifications content will be populated by JavaScript -->
        <div class="notifications-controls">
            <div class="controls-left">
                <button class="mark-all-read-btn" id="markAllReadBtn">
                    <i class="fas fa-check-double"></i>
                    Mark All Read
                </button>
            </div>
        </div>

        <div class="notifications-section">
            <h3>Recent Notifications</h3>
            <div class="notifications-list" id="notificationsList">
                <!-- Notifications will be populated by JavaScript -->
            </div>
        </div>
    </div>
</div>
@endsection
