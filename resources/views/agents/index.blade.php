@extends('layouts.admin')

@section('title', 'Agents - Insurance Management System 2.0')

@section('content')
<div class="page" id="agents">
    <div class="page-header">
        <h1>Agents</h1>
        <p>Manage insurance agents and their performance</p>
    </div>
    <div class="page-content">
        <!-- Agents content will be populated by JavaScript -->
        <div class="agents-controls">
            <div class="controls-left">
                <button class="add-agent-btn" id="addAgentBtn">
                    <i class="fas fa-plus"></i>
                    Add New Agent
                </button>
            </div>
            <div class="controls-right">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search agents..." id="agentsSearch">
                </div>
            </div>
        </div>

        <div class="data-table-container">
            <div class="table-header">
                <h3>Agents Management</h3>
                <div class="table-controls">
                    <select class="rows-per-page" id="agentsRowsPerPage">
                        <option value="10">10 rows</option>
                        <option value="25">25 rows</option>
                        <option value="50">50 rows</option>
                        <option value="100">100 rows</option>
                    </select>
                </div>
            </div>
            <div class="table-wrapper">
                <table class="data-table" id="agentsTable">
                    <thead>
                        <tr>
                            <th data-sort="id">Sl. No <i class="fas fa-sort"></i></th>
                            <th data-sort="name">Agent Name <i class="fas fa-sort"></i></th>
                            <th data-sort="email">Email <i class="fas fa-sort"></i></th>
                            <th data-sort="phone">Phone <i class="fas fa-sort"></i></th>
                            <th data-sort="policies">Policies <i class="fas fa-sort"></i></th>
                            <th data-sort="status">Status <i class="fas fa-sort"></i></th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="agentsTableBody">
                        <!-- Table data will be populated by JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
