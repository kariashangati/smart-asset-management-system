@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-white">Assets</h1>
        <div class="flex space-x-3">
            <button onclick="document.getElementById('bulkImportModal').classList.remove('hidden')" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition flex items-center space-x-2">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Bulk Import</span>
            </button>
            <a href="#" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition flex items-center space-x-2">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>New Asset</span>
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <input type="text" placeholder="Search assets..." class="px-4 py-2 rounded-lg bg-slate-700 border border-slate-600 text-white placeholder-slate-400 focus:outline-none focus:border-blue-500">
        <select class="px-4 py-2 rounded-lg bg-slate-700 border border-slate-600 text-white focus:outline-none focus:border-blue-500">
            <option value="">Filter by Status</option>
            <option value="active">Active</option>
            <option value="maintenance">Maintenance</option>
            <option value="retired">Retired</option>
        </select>
        <select class="px-4 py-2 rounded-lg bg-slate-700 border border-slate-600 text-white focus:outline-none focus:border-blue-500">
            <option value="">Filter by Department</option>
        </select>
        <button class="px-4 py-2 bg-slate-700 hover:bg-slate-600 border border-slate-600 text-white rounded-lg transition">Search</button>
    </div>

    <!-- Assets Table -->
    <div class="bg-slate-800 rounded-lg border border-slate-700 shadow-lg overflow-hidden">
        <table class="w-full">
            <thead class="bg-slate-700 border-b border-slate-600">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Asset</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Department</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Value</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700">
                <tr class="hover:bg-slate-700/50 transition">
                    <td class="px-6 py-4 text-sm text-white">No assets found</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Bulk Import Modal -->
<div id="bulkImportModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-slate-800 rounded-lg border border-slate-700 p-6 max-w-md w-full mx-4">
        <h3 class="text-xl font-bold text-white mb-4">Bulk Import Assets</h3>
        <form id="bulkImportForm" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Upload CSV File</label>
                <input type="file" name="csv_file" accept=".csv" required class="w-full px-4 py-2 rounded-lg bg-slate-700 border border-slate-600 text-white focus:outline-none focus:border-blue-500">
                <p class="text-xs text-slate-400 mt-2">CSV format: Asset Name, Type, Serial Number, Department, Status</p>
            </div>
            <div class="flex space-x-3 justify-end">
                <button type="button" onclick="document.getElementById('bulkImportModal').classList.add('hidden')" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg transition">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    Import
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('bulkImportForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch('/api/assets/import', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            if (data.success) {
                document.getElementById('bulkImportModal').classList.add('hidden');
                location.reload();
            }
        })
        .catch(err => alert('Error: ' + err.message));
    });
</script>
@endsection
