<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Viewer - Insurance System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .table-container { overflow-x: auto; }
        .table-container table { min-width: 100%; }
        .table-container th, .table-container td { 
            white-space: nowrap; 
            padding: 8px 12px;
            border: 1px solid #e5e7eb;
        }
        .table-container th { 
            background-color: #f3f4f6; 
            font-weight: 600;
        }
        .table-container tr:nth-child(even) { background-color: #f9fafb; }
        .table-container tr:hover { background-color: #f0f9ff; }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-6">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Database Viewer</h1>
                        <p class="text-gray-600">Browse and view your database tables</p>
                    </div>
                    <a href="{{ route('dashboard') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Tables List -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-sm border">
                        <div class="px-6 py-4 border-b">
                            <h2 class="text-lg font-semibold text-gray-900">Database Tables</h2>
                            <p class="text-sm text-gray-600">Click on a table to view data</p>
                        </div>
                        <div class="p-4">
                            @foreach($tables as $table)
                                <a href="{{ route('database.viewer', ['table' => $table['name']]) }}" 
                                   class="block p-3 rounded-lg mb-2 transition-colors {{ $selectedTable === $table['name'] ? 'bg-blue-100 border border-blue-300' : 'hover:bg-gray-50 border border-transparent' }}">
                                    <div class="flex justify-between items-center">
                                        <span class="font-medium text-gray-900">{{ $table['name'] }}</span>
                                        <span class="text-sm text-gray-500 bg-gray-100 px-2 py-1 rounded">{{ $table['count'] }}</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Table Data -->
                <div class="lg:col-span-3">
                    @if($selectedTable)
                        <div class="bg-white rounded-lg shadow-sm border">
                            <div class="px-6 py-4 border-b">
                                <h2 class="text-lg font-semibold text-gray-900">{{ $selectedTable }}</h2>
                                <p class="text-sm text-gray-600">Showing first 100 records</p>
                            </div>
                            
                            @if(isset($tableData['error']))
                                <div class="p-6">
                                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                        <p class="text-red-800">{{ $tableData['error'] }}</p>
                                    </div>
                                </div>
                            @else
                                <div class="p-6">
                                    @if(isset($tableData['columns']) && isset($tableData['data']))
                                        <!-- Table Structure -->
                                        <div class="mb-6">
                                            <h3 class="text-md font-semibold text-gray-900 mb-3">Table Structure</h3>
                                            <div class="table-container">
                                                <table class="w-full">
                                                    <thead>
                                                        <tr>
                                                            <th>Column</th>
                                                            <th>Type</th>
                                                            <th>Not Null</th>
                                                            <th>Default</th>
                                                            <th>Primary Key</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($tableData['columns'] as $column)
                                                            <tr>
                                                                <td>{{ $column->name }}</td>
                                                                <td>{{ $column->type }}</td>
                                                                <td>{{ $column->notnull ? 'Yes' : 'No' }}</td>
                                                                <td>{{ $column->dflt_value ?? '-' }}</td>
                                                                <td>{{ $column->pk ? 'Yes' : 'No' }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <!-- Table Data -->
                                        <div>
                                            <h3 class="text-md font-semibold text-gray-900 mb-3">Table Data</h3>
                                            @if(count($tableData['data']) > 0)
                                                <div class="table-container">
                                                    <table class="w-full">
                                                        <thead>
                                                            <tr>
                                                                @foreach($tableData['columns'] as $column)
                                                                    <th>{{ $column->name }}</th>
                                                                @endforeach
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($tableData['data'] as $row)
                                                                <tr>
                                                                    @foreach($tableData['columns'] as $column)
                                                                        <td class="text-sm">
                                                                            @if(is_string($row->{$column->name}) && strlen($row->{$column->name}) > 50)
                                                                                {{ substr($row->{$column->name}, 0, 50) }}...
                                                                            @else
                                                                                {{ $row->{$column->name} }}
                                                                            @endif
                                                                        </td>
                                                                    @endforeach
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                <div class="text-center py-8 text-gray-500">
                                                    No data found in this table
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="bg-white rounded-lg shadow-sm border p-8 text-center">
                            <div class="text-gray-400 mb-4">
                                <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Select a Table</h3>
                            <p class="text-gray-600">Choose a table from the left sidebar to view its structure and data</p>
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>
</body>
</html>
