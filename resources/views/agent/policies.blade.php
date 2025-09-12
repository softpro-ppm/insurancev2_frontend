@extends('layouts.agent')

@section('title', 'My Policies - Agent Dashboard')

@section('content')
<div class="page active" id="agent-policies">
    <div class="page-header">
        <h1>My Policies</h1>
        <p class="text-gray-600">Agent: {{ Auth::guard('agent')->user()->name }}</p>
    </div>
    
    <div class="page-content">
        <div class="table-container">
            <div class="table-header">
                <h3>Policies ({{ $policies->total() }} total)</h3>
                <div class="table-controls">
                    <input type="text" id="searchInput" placeholder="Search policies...">
                </div>
            </div>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Policy ID</th>
                        <th>Customer Name</th>
                        <th>Phone</th>
                        <th>Policy Type</th>
                        <th>Company</th>
                        <th>Premium</th>
                        <th>Status</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($policies as $policy)
                    <tr>
                        <td>#{{ str_pad($policy->id, 3, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $policy->customerName }}</td>
                        <td>{{ $policy->phone }}</td>
                        <td>{{ $policy->policyType }}</td>
                        <td>{{ $policy->companyName }}</td>
                        <td>₹{{ number_format($policy->premium) }}</td>
                        <td>
                            <span class="status-badge {{ strtolower($policy->status) }}">
                                {{ $policy->status }}
                            </span>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($policy->startDate)->format('d M Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($policy->endDate)->format('d M Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center">No policies found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            
            <div class="pagination">
                {{ $policies->links() }}
            </div>
        </div>
    </div>
</div>

<style>
.table-container {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    padding: 1.5rem;
    backdrop-filter: blur(10px);
    overflow-x: auto;
}

.table-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.table-header h3 {
    margin: 0;
    color: #374151;
    font-size: 1.125rem;
    font-weight: 600;
}

.table-controls input {
    padding: 0.5rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    background: white;
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

.pagination {
    margin-top: 1rem;
    display: flex;
    justify-content: center;
}
</style>
@endsection
