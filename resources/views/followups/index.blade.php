@extends('layouts.admin')

@section('title', 'Follow Ups - Insurance Management System 2.0')

@section('content')
<div class="page" id="followups">
    <div class="page-header">
        <h1>Follow Ups</h1>
        <p>Manage customer follow-ups and track interactions</p>
    </div>
    <div class="page-content">
        <!-- Follow-ups content will be populated by JavaScript -->
        <div class="followups-controls">
            <div class="controls-left">
                <button class="add-followup-btn" id="addFollowupBtn">
                    <i class="fas fa-plus"></i>
                    Add Follow Up
                </button>
            </div>
            <div class="controls-right">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search follow-ups..." id="followupsSearch">
                </div>
            </div>
        </div>

        <div class="data-table-container">
            <div class="table-header">
                <h3>Follow Ups Management</h3>
                <div class="table-controls">
                    <select class="rows-per-page" id="followupsRowsPerPage">
                        <option value="10">10 rows</option>
                        <option value="25">25 rows</option>
                        <option value="50">50 rows</option>
                        <option value="100">100 rows</option>
                    </select>
                </div>
            </div>
            <div class="table-wrapper">
                <table class="data-table" id="followupsTable">
                    <thead>
                        <tr>
                            <th data-sort="id">Sl. No <i class="fas fa-sort"></i></th>
                            <th data-sort="customerName">Customer Name <i class="fas fa-sort"></i></th>
                            <th data-sort="type">Type <i class="fas fa-sort"></i></th>
                            <th data-sort="status">Status <i class="fas fa-sort"></i></th>
                            <th data-sort="dueDate">Due Date <i class="fas fa-sort"></i></th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="followupsTableBody">
                        <!-- Table data will be populated by JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
