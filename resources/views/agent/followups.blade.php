@extends('layouts.agent')

@section('title', 'My Follow-ups - Agent Dashboard')

@section('content')
<div class="page active" id="agent-followups">
    <div class="page-header">
        <h1>My Follow-ups</h1>
        <p class="text-gray-600">Agent: {{ Auth::guard('agent')->user()->name ?? 'Unknown Agent' }}</p>
    </div>
    
    <div class="page-content">
        <div class="table-container">
            <div class="table-header">
                <h3>Follow-ups ({{ $followups->total() }} total)</h3>
                <div class="table-controls">
                    <input type="text" id="searchInput" placeholder="Search follow-ups...">
                </div>
            </div>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Policy ID</th>
                        <th>Customer Name</th>
                        <th>Phone</th>
                        <th>Follow-up Date</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Notes</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($followups as $followup)
                    <tr>
                        <td>#{{ $followup->policy_id ?? 'N/A' }}</td>
                        <td>{{ $followup->customer_name ?? 'N/A' }}</td>
                        <td>{{ $followup->phone ?? 'N/A' }}</td>
                        <td>{{ $followup->followup_date ? \Carbon\Carbon::parse($followup->followup_date)->format('d M Y') : 'N/A' }}</td>
                        <td>
                            <span class="followup-type-badge">{{ $followup->type ?? 'General' }}</span>
                        </td>
                        <td>
                            <span class="status-badge status-{{ strtolower($followup->status ?? 'pending') }}">
                                {{ $followup->status ?? 'Pending' }}
                            </span>
                        </td>
                        <td>{{ Str::limit($followup->notes ?? 'No notes', 50) }}</td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn btn-sm btn-primary" onclick="viewFollowup({{ $followup->id }})">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-success" onclick="completeFollowup({{ $followup->id }})">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button class="btn btn-sm btn-warning" onclick="rescheduleFollowup({{ $followup->id }})">
                                    <i class="fas fa-calendar"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-gray-500 py-8">
                            <i class="fas fa-bell text-4xl mb-4"></i>
                            <p>No follow-ups found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            
            @if($followups->hasPages())
            <div class="pagination-container">
                {{ $followups->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function viewFollowup(id) {
    // Implementation for viewing follow-up details
    console.log('View follow-up:', id);
}

function completeFollowup(id) {
    // Implementation for completing follow-up
    console.log('Complete follow-up:', id);
}

function rescheduleFollowup(id) {
    // Implementation for rescheduling follow-up
    console.log('Reschedule follow-up:', id);
}
</script>
@endsection
