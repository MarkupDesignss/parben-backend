@extends('admin.layouts.main')
@section('content')
<div class="container-fluid">
    <div class="row g-4">
        <div class="col-lg-12">
            <div class="card bg-white border-0 shadow-sm rounded-3 p-2">
                {{-- Filters and Search --}}
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.cases.index') }}" id="filterForm">
                        <div class="d-flex align-items-center justify-content-end flex-wrap gap-2">
                            {{-- Search Box --}}
                            <div class="search-bx position-relative me-3 flex-grow-1" style="max-width: 400px;">
                                <div class="input-group">
                                    <button class="btn-outline-secondary border-0" type="submit">
                                        <i class="bi bi-search"></i>
                                    </button>
                                    <input class="form-control border rounded-pill" 
                                           name="search" 
                                           type="search" 
                                           value="{{ request('search') }}" 
                                           placeholder="Search by Case ID, Router ID, Investigator..." />
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                {{-- Filter Toggle --}}
                                <button type="button" class="btn btn-primary rounded-circle" id="toggleFilter">
                                    <img src="{{ config('app.url') }}/assets/images/filter.svg" alt="Filter" />
                                </button>

                                {{-- Reset Button --}}
                                <a href="{{ route('admin.cases.index') }}" class="btn btn-secondary rounded-pill">
                                    Reset
                                </a>
                            </div>
                        </div>

                        {{-- Filter Box --}}
                        <div class="row g-3 mt-3" id="filterBox" style="display: none;">
                            <div class="col-md-12 d-flex flex-wrap gap-3">
                                <div>
                                    <label class="form-label">From Date</label>
                                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                                </div>
                                <div>
                                    <label class="form-label">To Date</label>
                                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                                </div>
                                <div>
                                    <label class="form-label">Router Model</label>
                                    <select name="router_model" class="form-select">
                                        <option value="">All</option>
                                        @foreach ($uniqueRouterModels ?? [] as $model)
                                            <option value="{{ $model }}" {{ request('router_model') == $model ? 'selected' : '' }}>
                                                {{ $model }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="form-label">Investigator</label>
                                    <select name="investigator" class="form-select">
                                        <option value="">All</option>
                                        @foreach ($uniqueInvestigators ?? [] as $inv)
                                            <option value="{{ $inv->id }}" {{ request('investigator') == $inv->id ? 'selected' : '' }}>
                                                {{ $inv->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="align-self-end">
                                    <button type="submit" class="btn btn-primary rounded-pill">Apply Filters</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Table --}}
                <div class="card-body pb-0">
                    {{-- Bulk Actions Bar --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <span class="badge bg-primary">Total: {{ $logs->total() }}</span>
                        </div>
                        <div>
                            <button class="btn btn-danger btn-sm" id="bulkDeleteBtn" style="display:none">
                                <i class="bi bi-trash"></i> Delete Selected
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th class="p-3 bg-tile text-white text-center" style="border-radius: 6px 0 0 6px; min-width: 50px;">
                                        <input type="checkbox" id="selectAll" onchange="toggleAllCheckboxes(this)">
                                    </th>
                                    <th class="p-3 bg-tile text-white text-center">Sr.No.</th>
                                    <th class="p-3 bg-tile text-white text-center">Case ID</th>
                                    <th class="p-3 bg-tile text-white text-center">Router ID</th>
                                    <th class="p-3 bg-tile text-white text-center">Model</th>
                                    <th class="p-3 bg-tile text-white text-center">Investigator</th>
                                    <th class="p-3 bg-tile text-white text-center">Log Time</th>
                                    <th class="p-3 bg-tile text-white text-center" style="border-radius: 0 6px 6px 0;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($logs as $index => $log)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" class="log-checkbox" value="{{ $log->id }}" onchange="updateBulkDelete()">
                                </td>
                                <td class="text-center">{{ $logs->firstItem() + $index }}</td>
                                <td class="text-center">{{ $log->case_id ?? 'N/A' }}</td>
                                <td class="text-center">{{ $log->router_id }}</td>
                                <td class="text-center">{{ $log->router_model ?? 'N/A' }}</td>
                                <td class="text-center">{{ $log->investigator_name ?? 'N/A' }}</td>
                                <td class="text-center">
                                    {{ \Carbon\Carbon::parse($log->log_generated_at)->format('H:i:s') }}<br />
                                    <small>{{ \Carbon\Carbon::parse($log->log_generated_at)->format('d/m/Y') }}</small>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center align-items-center gap-1">
                                        {{-- View Button --}}
                                        <button class="btn btn-sm btn-outline-primary view-log-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#logDetailModal"
                                            data-case-id="{{ $log->case_id }}"
                                            data-router-id="{{ $log->router_id }}"
                                            data-router-model="{{ $log->router_model }}"
                                            data-investigator="{{ $log->investigator_name ?? 'N/A' }}"
                                            data-log-time="{{ \Carbon\Carbon::parse($log->log_generated_at)->format('H:i:s d/m/Y') }}"
                                            data-file-url="{{ asset('storage/' . $log->file_path) }}"
                                            data-log-content="{{ Str::limit($log->log_content, 200) }}">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        
                                        {{-- PDF with Hash --}}
                                        <a href="{{ route('admin.logs.export-pdf', $log->id) }}" 
                                           class="btn btn-sm btn-success"
                                           title="Download as PDF with Hash">
                                            <i class="bi bi-file-pdf"></i>
                                        </a>

                                        {{-- ✅ NEW — Delete Button --}}
                                        <form action="{{ route('admin.cases.destroy', $log->id) }}"
                                              method="POST"
                                              style="display:inline-block; margin: 0;"
                                              onsubmit="return confirm('Are you sure you want to delete this log ({{ $log->router_id }})?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Log">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">No router logs found.</td>
                            </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Pagination --}}
                <div class="card-body">
                    <div class="d-flex justify-content-center">
                        {{ $logs->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Log Detail Modal --}}
<div class="modal fade" id="logDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title">Log Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><strong>Case ID:</strong> <span id="modal-case-id"></span></li>
                    <li class="list-group-item"><strong>Router ID:</strong> <span id="modal-router-id"></span></li>
                    <li class="list-group-item"><strong>Router Model:</strong> <span id="modal-router-model"></span></li>
                    <li class="list-group-item"><strong>Investigator:</strong> <span id="modal-investigator"></span></li>
                    <li class="list-group-item"><strong>Log Time:</strong> <span id="modal-log-time"></span></li>
                    <li class="list-group-item">
                        <strong>Log Preview:</strong>
                        <pre id="modal-log-preview" class="mt-2 p-2 bg-light rounded" style="max-height: 200px; overflow: auto;"></pre>
                    </li>
                </ul>
            </div>
            <div class="modal-footer border-0">
                <a id="modal-download-btn" class="btn btn-primary rounded-pill" href="#" download>
                    <i class="bi bi-file-earmark-text me-2"></i> Download Full Log
                </a>
            </div>
        </div>
    </div>
</div>

@stop

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle filter box
    document.getElementById('toggleFilter').addEventListener('click', function() {
        const box = document.getElementById('filterBox');
        box.style.display = box.style.display === 'none' ? 'flex' : 'none';
    });

    // Log detail modal
    const modal = document.getElementById('logDetailModal');
    modal.addEventListener('show.bs.modal', function(event) {
        const btn = event.relatedTarget;
        document.getElementById('modal-case-id').textContent = btn.dataset.caseId || 'N/A';
        document.getElementById('modal-router-id').textContent = btn.dataset.routerId;
        document.getElementById('modal-router-model').textContent = btn.dataset.routerModel || 'N/A';
        document.getElementById('modal-investigator').textContent = btn.dataset.investigator || 'N/A';
        document.getElementById('modal-log-time').textContent = btn.dataset.logTime;
        document.getElementById('modal-log-preview').textContent = btn.dataset.logContent || 'No content preview.';
        document.getElementById('modal-download-btn').href = btn.dataset.fileUrl || '#';
    });

    // ============================================================
    // ✅ BULK DELETE LOGIC (NEW)
    // ============================================================
    window.toggleAllCheckboxes = function(master) {
        document.querySelectorAll('.log-checkbox').forEach(function(cb) {
            cb.checked = master.checked;
        });
        window.updateBulkDelete();
    };

    window.updateBulkDelete = function() {
        var checked = document.querySelectorAll('.log-checkbox:checked').length;
        var btn = document.getElementById('bulkDeleteBtn');
        if (btn) btn.style.display = (checked > 0) ? 'inline-block' : 'none';
    };

    var bulkBtn = document.getElementById('bulkDeleteBtn');
    if (bulkBtn) {
        bulkBtn.addEventListener('click', function() {
            var ids = Array.from(document.querySelectorAll('.log-checkbox:checked')).map(function(cb) {
                return cb.value;
            });
            if (ids.length === 0) return;
            if (confirm('Are you sure you want to delete ' + ids.length + ' logs?')) {
                fetch('{{ route("admin.cases.bulk-delete") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ ids: ids })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Error deleting logs');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting logs');
                });
            }
        });
    }
});
</script>
@endpush