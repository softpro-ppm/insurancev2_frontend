@extends('layouts.insurance')

@section('title', 'Profile - Insurance Management System')

@section('content')
<div class="page active" id="profile-page">
    <div class="page-header">
        <h1><i class="fas fa-user-circle"></i> Profile Settings</h1>
        <p style="font-size: 14px; color: #6B7280; margin-top: 8px;">Manage your account settings and preferences</p>
    </div>

    <div class="profile-container">
        <!-- Profile Information Card -->
        <div class="profile-card glass-effect">
            <div class="profile-card-header">
                <div>
                    <h2><i class="fas fa-user-edit"></i> Profile Information</h2>
                    <p>Update your account's profile information and email address</p>
                </div>
            </div>
            <div class="profile-card-body">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <!-- Update Password Card -->
        <div class="profile-card glass-effect">
            <div class="profile-card-header">
                <div>
                    <h2><i class="fas fa-lock"></i> Update Password</h2>
                    <p>Ensure your account is using a long, random password to stay secure</p>
                </div>
            </div>
            <div class="profile-card-body">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <!-- Delete Account Card -->
        <div class="profile-card glass-effect danger-card">
            <div class="profile-card-header">
                <div>
                    <h2><i class="fas fa-exclamation-triangle"></i> Delete Account</h2>
                    <p>Permanently delete your account and all associated data</p>
                </div>
            </div>
            <div class="profile-card-body">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.profile-container {
    display: flex;
    flex-direction: column;
    gap: 24px;
    max-width: 900px;
    margin: 0 auto;
}

.profile-card {
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 16px;
    overflow: hidden;
    transition: all 0.3s ease;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

.dark-theme .profile-card {
    background: rgba(30, 41, 59, 0.7);
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
}

.profile-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.profile-card.danger-card {
    border-color: rgba(239, 68, 68, 0.3);
}

.profile-card-header {
    padding: 24px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    background: linear-gradient(135deg, rgba(79, 70, 229, 0.05), rgba(99, 102, 241, 0.05));
}

.dark-theme .profile-card-header {
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    background: linear-gradient(135deg, rgba(79, 70, 229, 0.1), rgba(99, 102, 241, 0.1));
}

.profile-card.danger-card .profile-card-header {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.05), rgba(220, 38, 38, 0.05));
}

.profile-card-header h2 {
    font-size: 18px;
    font-weight: 600;
    color: #111827;
    margin: 0 0 8px 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.dark-theme .profile-card-header h2 {
    color: #F9FAFB;
}

.profile-card-header h2 i {
    color: #4F46E5;
    font-size: 20px;
}

.profile-card.danger-card .profile-card-header h2 i {
    color: #EF4444;
}

.profile-card-header p {
    font-size: 14px;
    color: #6B7280;
    margin: 0;
}

.dark-theme .profile-card-header p {
    color: #9CA3AF;
}

.profile-card-body {
    padding: 24px;
}

/* Form Styling */
.profile-card-body form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.profile-card-body label {
    display: block;
    font-size: 14px;
    font-weight: 500;
    color: #374151;
    margin-bottom: 8px;
}

.dark-theme .profile-card-body label {
    color: #D1D5DB;
}

.profile-card-body input[type="text"],
.profile-card-body input[type="email"],
.profile-card-body input[type="password"] {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid rgba(209, 213, 219, 0.5);
    border-radius: 10px;
    font-size: 14px;
    background: rgba(255, 255, 255, 0.8);
    transition: all 0.3s ease;
    color: #111827;
}

.dark-theme .profile-card-body input[type="text"],
.dark-theme .profile-card-body input[type="email"],
.dark-theme .profile-card-body input[type="password"] {
    background: rgba(30, 41, 59, 0.8);
    border-color: rgba(255, 255, 255, 0.1);
    color: #F9FAFB;
}

.profile-card-body input:focus {
    outline: none;
    border-color: #4F46E5;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    background: rgba(255, 255, 255, 1);
}

.dark-theme .profile-card-body input:focus {
    background: rgba(30, 41, 59, 1);
    border-color: #6366F1;
}

.profile-card-body button[type="submit"],
.profile-card-body .btn-primary {
    background: linear-gradient(135deg, #4F46E5, #6366F1);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3);
}

.profile-card-body button[type="submit"]:hover,
.profile-card-body .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(79, 70, 229, 0.4);
}

.danger-card button[type="submit"],
.danger-card .btn-danger {
    background: linear-gradient(135deg, #EF4444, #DC2626);
    box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
}

.danger-card button[type="submit"]:hover,
.danger-card .btn-danger:hover {
    box-shadow: 0 8px 25px rgba(239, 68, 68, 0.4);
}

/* Success Message */
.profile-card-body p[x-data] {
    color: #10B981;
    font-size: 14px;
    font-weight: 500;
}

/* Error Messages */
.profile-card-body .text-red-600,
.profile-card-body .text-sm.text-red-600 {
    color: #EF4444;
    font-size: 13px;
    margin-top: 4px;
}

@media (max-width: 768px) {
    .profile-container {
        gap: 16px;
    }
    
    .profile-card-header,
    .profile-card-body {
        padding: 16px;
    }
    
    .profile-card-header h2 {
        font-size: 16px;
    }
}
</style>
@endpush

@push('scripts')
<script>
console.log('Profile page loaded');
</script>
@endpush

@endsection
