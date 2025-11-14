@extends('layouts.agent')

@section('title', 'My Renewals - Agent Dashboard')

@section('content')
<div class="page active" id="agent-renewals">
    <div class="page-header">
        <h1>My Renewals</h1>
        <p class="text-gray-600">Agent: {{ Auth::guard('agent')->user()->name ?? 'Unknown Agent' }}</p>
    </div>
    
    <div class="page-content">
        <div class="table-container">
            <div class="table-header">
                <h3>Renewals ({{ $renewals->total() }} total)</h3>
                <div class="table-controls">
                    <input type="text" id="searchInput" placeholder="Search renewals...">
                </div>
            </div>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Policy ID</th>
                        <th>Customer Name</th>
                        <th>Policy Type</th>
                        <th>Company</th>
                        <th>Current End Date</th>
                        <th>Renewal Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($renewals as $renewal)
                    <tr>
                        <td>#{{ $renewal->policy_id ?? 'N/A' }}</td>
                        <td>{{ $renewal->customer_name ?? 'N/A' }}</td>
                        <td>
                            <span class="policy-type-badge">{{ $renewal->policy_type ?? 'N/A' }}</span>
                        </td>
                        <td>{{ $renewal->company ?? 'N/A' }}</td>
                        <td>{{ $renewal->current_end_date ? \Carbon\Carbon::parse($renewal->current_end_date)->format('d M Y') : 'N/A' }}</td>
                        <td>{{ $renewal->renewal_date ? \Carbon\Carbon::parse($renewal->renewal_date)->format('d M Y') : 'N/A' }}</td>
                        <td>
                            <span class="status-badge status-{{ strtolower($renewal->status ?? 'pending') }}">
                                {{ $renewal->status ?? 'Pending' }}
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn btn-sm btn-primary" onclick="viewRenewal({{ $renewal->id }})">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-success" onclick="processRenewal({{ $renewal->id }})">
                                    <i class="fas fa-check"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-gray-500 py-8">
                            <i class="fas fa-sync-alt text-4xl mb-4"></i>
                            <p>No renewals found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            
            @if($renewals->hasPages())
            <div class="pagination-container">
                {{ $renewals->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function viewRenewal(id) {
    // Implementation for viewing renewal details
    console.log('View renewal:', id);
}

function processRenewal(id) {
    // Implementation for processing renewal
    console.log('Process renewal:', id);
}
</script>
@endsection
