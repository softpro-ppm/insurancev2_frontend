@extends('layouts.agent')

@section('title', 'Agent Dashboard - Insurance Management System')

@section('content')
<div class="page active" id="agent-dashboard">
    <div class="page-header">
        <h1>Welcome, {{ Auth::guard('agent')->user()->name }}</h1>
        <p class="text-gray-600">Agent Dashboard</p>
    </div>
    
    <div class="page-content">
        <!-- Agent Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card glass-effect">
                <div class="stat-icon">
                    <i class="fas fa-file-contract"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Policies</h3>
                    <p class="stat-value">{{ $totalPolicies }}</p>
                </div>
            </div>
            
            <div class="stat-card glass-effect">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3>Active Policies</h3>
                    <p class="stat-value">{{ $activePolicies }}</p>
                </div>
            </div>
            
            <div class="stat-card glass-effect">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <h3>Expiring Soon</h3>
                    <p class="stat-value">{{ $expiringSoon }}</p>
                </div>
            </div>
            
            <div class="stat-card glass-effect">
                <div class="stat-icon">
                    <i class="fas fa-rupee-sign"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Premium</h3>
                    <p class="stat-value">₹{{ number_format($totalPremium) }}</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <h2>Quick Actions</h2>
            <div class="action-buttons">
                <a href="{{ route('agent.policies') }}" class="action-btn">
                    <i class="fas fa-file-contract"></i>
                    <span>View Policies</span>
                </a>
                <a href="{{ route('agent.renewals') }}" class="action-btn">
                    <i class="fas fa-sync-alt"></i>
                    <span>View Renewals</span>
                </a>
                <a href="{{ route('agent.followups') }}" class="action-btn">
                    <i class="fas fa-bell"></i>
                    <span>View Follow-ups</span>
                </a>
            </div>
        </div>

        <!-- Recent Policies -->
        <div class="recent-section">
            <h2>Recent Policies</h2>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Policy ID</th>
                            <th>Customer Name</th>
                            <th>Policy Type</th>
                            <th>Premium</th>
                            <th>Status</th>
                            <th>End Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($policies->take(10) as $policy)
                        <tr>
                            <td>#{{ str_pad($policy->id, 3, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $policy->customerName }}</td>
                            <td>{{ $policy->policyType }}</td>
                            <td>₹{{ number_format($policy->premium) }}</td>
                            <td>
                                <span class="status-badge {{ strtolower($policy->status) }}">
                                    {{ $policy->status }}
                                </span>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($policy->endDate)->format('d M Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No policies found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    padding: 1.5rem;
    border-radius: 12px;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: transform 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.stat-content h3 {
    margin: 0 0 0.5rem 0;
    color: #6b7280;
    font-size: 0.875rem;
    font-weight: 600;
}

.stat-value {
    margin: 0;
    font-size: 1.875rem;
    font-weight: 700;
    color: #111827;
}

.quick-actions {
    margin-bottom: 2rem;
}

.quick-actions h2 {
    margin-bottom: 1rem;
    color: #374151;
    font-size: 1.25rem;
    font-weight: 700;
}

.action-buttons {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.action-btn {
    padding: 1rem;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    text-decoration: none;
    color: #374151;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}

.action-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
}

.action-btn i {
    font-size: 1.25rem;
    color: #667eea;
}

.recent-section h2 {
    margin-bottom: 1rem;
    color: #374151;
    font-size: 1.25rem;
    font-weight: 700;
}

.table-container {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    padding: 1.5rem;
    backdrop-filter: blur(10px);
    overflow-x: auto;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 8px;
    overflow: hidden;
}

.data-table th {
    background: #f9fafb;
    padding: 1rem;
    text-align: left;
    font-weight: 600;
    color: #374151;
    border-bottom: 1px solid #e5e7eb;
}

.data-table td {
    padding: 1rem;
    border-bottom: 1px solid #e5e7eb;
    color: #374151;
}

.data-table tr:hover {
    background: #f9fafb;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-badge.active {
    background: #d1fae5;
    color: #065f46;
}

.status-badge.expired {
    background: #fee2e2;
    color: #991b1b;
}

.status-badge.pending {
    background: #fef3c7;
    color: #92400e;
}

.text-center {
    text-align: center;
}
</style>
@endsection
