@extends('layouts.insurance')

@section('title', 'Reports - Insurance Management System 2.0')

@section('content')
<div class="page" id="reports">
    <div class="page-header">
        <h1>Reports</h1>
        <p>Generate and view comprehensive reports</p>
    </div>
    <div class="page-content">
        <!-- Reports content will be populated by JavaScript -->
        <div class="reports-controls">
            <div class="controls-left">
                <button class="generate-report-btn" id="generateReportBtn">
                    <i class="fas fa-file-alt"></i>
                    Generate Report
                </button>
            </div>
        </div>

        <div class="reports-section">
            <h3>Available Reports</h3>
            <p>Select a report type to generate comprehensive analytics</p>
        </div>
    </div>
</div>
@endsection
