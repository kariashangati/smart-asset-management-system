@extends('layouts.app')

@section('title', 'Assets')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header with Alerts -->
    @include('components.alert-header')

    <!-- Page Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h1 class="text-4xl font-bold text-white">Assets Management</h1>
            <p class="text-slate-400 mt-2">Track and manage all your organizational assets</p>
        </div>
        <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3 w-full md:w-auto">
            <button onclick="openBulkImportModal()" class="px-6 py-2 bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white rounded-lg font-medium transition flex items-center justify-center space-x-2 shadow-lg">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3v-6" />
                </svg>
                <span>Bulk Import CSV</span>
            </button>
            <a href="#" class="px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg font-medium transition flex items-center justify-center space-x-2 shadow-lg">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>New Asset</span>
            </a>
        </div>
    </div>

    <!-- Search & Filter Section -->
    <div class="bg-slate-800 rounded-lg border border-slate-700 p-6 mb-6 shadow-lg">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Search Assets</label>
                <input 
                    type="text" 
                    id="searchAssets"
                    placeholder="Asset name, serial number..." 
                    class="w-full px-4 py-2 rounded-lg bg-slate-700 border border-slate-600 text-white placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition"
                >
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Status</label>
                <select id="statusFilter" class="w-full px-4 py-2 rounded-lg bg-slate-700 border border-slate-600 text-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="maintenance">Maintenance</option>
                    <option value="retired">Retired</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Department</label>
                <select id="departmentFilter" class="w-full px-4 py-2 rounded-lg bg-slate-700 border border-slate-600 text-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition">
                    <option value="">All Departments</option>
                    <option value="IT">IT</option>
                    <option value="HR">HR</option>
                    <option value="Finance">Finance</option>
                    <option value="Operations">Operations</option>
                </select>
            </div>
            <div class="flex items-end">
                <button onclick="filterAssets()" class="w-full px-4 py-2 bg-slate-700 hover:bg-slate-600 border border-slate-600 text-white rounded-lg transition font-medium flex items-center justify-center space-x-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <span>Filter</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Assets Table -->
    <div class="bg-slate-800 rounded-lg border border-slate-700 shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-slate-700 to-slate-600 border-b border-slate-600">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-200 uppercase tracking-wider">Asset Name</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-200 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-200 uppercase tracking-wider">Serial #</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-200 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-200 uppercase tracking-wider">Department</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-200 uppercase tracking-wider">Value</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-200 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700" id="assetsTableBody">
                    <tr class="hover:bg-slate-700/30 transition">
                        <td colspan="7" class="px-6 py-8 text-center text-slate-400">
                            <div class="flex flex-col items-center justify-center space-y-2">
                                <svg class="h-12 w-12 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                                <p>No assets found. Start by uploading assets via CSV.</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Bulk Import Modal -->
<div id="bulkImportModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4 animate-fadeIn">
    <div class="bg-slate-800 rounded-lg border border-slate-700 p-8 max-w-md w-full shadow-2xl animate-slideUp">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-2xl font-bold text-white">Bulk Import Assets</h3>
            <button onclick="closeBulkImportModal()" class="text-slate-400 hover:text-white transition">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        @if ($errors->has('csv_file'))
            <div class="mb-4 p-4 bg-red-900/20 border border-red-700 rounded-lg text-red-200 text-sm">
                {{ $errors->first('csv_file') }}
            </div>
        @endif

        <form id="bulkImportForm" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-slate-200 mb-3">Upload CSV File</label>
                <div id="dropZone" class="relative border-2 border-dashed border-slate-600 rounded-lg p-8 text-center hover:border-blue-500 transition cursor-pointer bg-slate-700/30">
                    <input type="file" id="csvFile" name="csv_file" accept=".csv" required class="hidden">
                    <svg class="h-12 w-12 text-slate-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3v-6" />
                    </svg>
                    <p class="text-slate-300 font-medium">Drop CSV file here or click to select</p>
                    <p class="text-slate-400 text-xs mt-2">Max 10MB</p>
                </div>
                <p class="text-xs text-slate-400 mt-3 font-mono bg-slate-700 p-3 rounded">Format: Asset Name | Type | Serial Number | Department | Status</p>
            </div>
            <div class="flex space-x-3 justify-end pt-4 border-t border-slate-700">
                <button type="button" onclick="closeBulkImportModal()" class="px-6 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg transition font-medium">
                    Cancel
                </button>
                <button type="submit" class="px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg transition font-medium flex items-center space-x-2">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3v-6" />
                    </svg>
                    <span>Import</span>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    @keyframes slideUp {
        from {
            transform: translateY(20px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .animate-fadeIn {
        animation: fadeIn 0.2s ease-out;
    }

    .animate-slideUp {
        animation: slideUp 0.3s ease-out;
    }
</style>

<script>
    // Modal Functions
    function openBulkImportModal() {
        document.getElementById('bulkImportModal').classList.remove('hidden');
    }

    function closeBulkImportModal() {
        document.getElementById('bulkImportModal').classList.add('hidden');
    }

    // Drag & Drop
    const dropZone = document.getElementById('dropZone');
    const csvFileInput = document.getElementById('csvFile');

    dropZone.addEventListener('click', () => csvFileInput.click());
    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('border-blue-500', 'bg-blue-500/10');
    });
    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('border-blue-500', 'bg-blue-500/10');
    });
    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('border-blue-500', 'bg-blue-500/10');
        csvFileInput.files = e.dataTransfer.files;
    });

    // Form Submission
    document.getElementById('bulkImportForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<svg class="h-4 w-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" /></svg><span>Importing...</span>';
        
        fetch('/api/assets/import', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(res => res.json())
        .then(data => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3v-6" /></svg><span>Import</span>';
            
            if (data.success) {
                alert('✅ ' + data.message);
                closeBulkImportModal();
                location.reload();
            } else {
                alert('❌ ' + (data.message || 'Import failed'));
            }
        })
        .catch(err => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3v-6" /></svg><span>Import</span>';
            alert('❌ Error: ' + err.message);
        });
    });

    // Filter function
    function filterAssets() {
        const search = document.getElementById('searchAssets').value;
        const status = document.getElementById('statusFilter').value;
        const department = document.getElementById('departmentFilter').value;
        
        fetch(`/api/assets?search=${search}&status=${status}&department=${department}`)
            .then(res => res.json())
            .then(data => {
                // Update table with filtered results
                console.log('Filtered assets:', data);
            })
            .catch(err => console.error('Filter error:', err));
    }

    // Close modal on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeBulkImportModal();
        }
    });
</script>
@endsection
