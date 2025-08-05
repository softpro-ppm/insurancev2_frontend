@extends('layouts.admin')

@section('title', 'Settings - Insurance Management System 2.0')

@section('content')
<div class="page" id="settings">
    <div class="page-header">
        <h1>Settings</h1>
        <p>Configure system settings and preferences</p>
    </div>
    <div class="page-content">
        <!-- Settings content will be populated by JavaScript -->
        <div class="settings-section">
            <h3>System Settings</h3>
            <p>Configure application settings and preferences</p>
            
            <div class="settings-cards">
                <div class="setting-card">
                    <h4>General Settings</h4>
                    <p>Basic application configuration</p>
                </div>
                
                <div class="setting-card">
                    <h4>User Management</h4>
                    <p>Manage user accounts and permissions</p>
                </div>
                
                <div class="setting-card">
                    <h4>Backup & Restore</h4>
                    <p>Data backup and restoration options</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
