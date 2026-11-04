@extends('admin.layouts.main')

@section('content')
<style>
    .device-row.collapsed {
        display: none;
    }
    .group-header {
        cursor: pointer;
    }
    .group-header:hover {
        background-color: #e9ecef;
    }
</style>

<div class="container-fluid">
    <div class="row g-4">
        <div class="col-lg-12">
            <div class="card bg-white border-0 shadow-sm rounded-3 p-2">
                {{-- Filters and Search --}}
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.router-devices.index') }}" id="filterForm">
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
                                           placeholder="Search by MAC, Device Name, Router ID..." />
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                {{-- Filter Toggle --}}
                                <button type="button" class="btn btn-primary rounded-circle" id="toggleFilter">
                                    <img src="{{ config('app.url') }}/assets/images/filter.svg" alt="Filter" />
                                </button>
                                {{-- Reset Button --}}
                                <a href="{{ route('admin.router-devices.index') }}" class="btn btn-secondary rounded-pill">
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
                                    <label class="form-label">Device Type</label>
                                    <select name="device_type" class="form-select">
                                        <option value="">All</option>
                                        @foreach ($deviceTypes ?? [] as $type)
                                            <option value="{{ $type }}" {{ request('device_type') == $type ? 'selected' : '' }}>
                                                {{ $type }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="form-label">Band</label>
                                    <select name="band" class="form-select">
                                        <option value="">All</option>
                                        <option value="2.4G" {{ request('band') == '2.4G' ? 'selected' : '' }}>2.4G</option>
                                        <option value="5G" {{ request('band') == '5G' ? 'selected' : '' }}>5G</option>
                                        <option value="6G" {{ request('band') == '6G' ? 'selected' : '' }}>6G</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="form-label">Status</label>
                                    <select name="is_blocked" class="form-select">
                                        <option value="">All</option>
                                        <option value="1" {{ request('is_blocked') == '1' ? 'selected' : '' }}>Blocked</option>
                                        <option value="0" {{ request('is_blocked') == '0' ? 'selected' : '' }}>Active</option>
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
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <span class="badge bg-primary">Total Devices: {{ $groupedDevices->sum(fn($g) => $g->count()) }}</span>
                        </div>
                        <div>
                            <button class="btn btn-outline-secondary btn-sm me-2" onclick="expandAll()">
                                <i class="bi bi-arrows-expand"></i> Expand All
                            </button>
                            <button class="btn btn-outline-secondary btn-sm me-2" onclick="collapseAll()">
                                <i class="bi bi-arrows-collapse"></i> Collapse All
                            </button>
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
                                    <th class="p-3 bg-tile text-white text-center" style="min-width: 60px;">Sr.No.</th>
                                    <th class="p-3 bg-tile text-white text-center" style="min-width: 100px;">User</th>
                                    <th class="p-3 bg-tile text-white text-center" style="min-width: 120px;">Router ID</th>
                                    <th class="p-3 bg-tile text-white text-center" style="min-width: 120px;">Device Name</th>
                                    <th class="p-3 bg-tile text-white text-center" style="min-width: 150px;">MAC Address</th>
                                    <th class="p-3 bg-tile text-white text-center" style="min-width: 120px;">IP Address</th>
                                    <th class="p-3 bg-tile text-white text-center" style="min-width: 100px;">Type</th>
                                    <th class="p-3 bg-tile text-white text-center" style="min-width: 80px;">Band</th>
                                    <th class="p-3 bg-tile text-white text-center" style="min-width: 100px;">Duration</th>
                                    <th class="p-3 bg-tile text-white text-center" style="min-width: 100px;">Status</th>
                                    <th class="p-3 bg-tile text-white text-center" style="border-radius: 0 6px 6px 0; min-width: 120px;">Actions</th>
                                </tr>
                            </thead>

                            @forelse($groupedDevices as $date => $devices)
                                @php
                                    $isFirst = $loop->first;
                                @endphp
                                <tbody class="date-group" data-date="{{ $date }}">
                                    {{-- Clickable date header --}}
                                    <tr class="table-secondary group-header" onclick="toggleGroup(this)">
                                        <td colspan="13" class="fw-bold">
                                            <i class="bi bi-chevron-{{ $isFirst ? 'down' : 'right' }} me-2 toggle-icon"></i>
                                            {{ \Carbon\Carbon::parse($date)->format('l, F j, Y') }}
                                            <span class="badge bg-primary ms-2">{{ count($devices) }} device(s)</span>
                                        </td>
                                    </tr>
                                    {{-- Device rows --}}
                                    @foreach($devices as $index => $device)
                                    <tr class="device-row {{ $isFirst ? '' : 'collapsed' }}">
                                        <td class="text-center">
                                            <input type="checkbox" class="device-checkbox" value="{{ $device->id }}" onchange="updateBulkDelete()">
                                        </td>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td class="text-center">{{ $device->user->name ?? 'N/A' }}</td>
                                        <td class="text-center"><code>{{ $device->router_id }}</code></td>
                                        <td class="text-center">{{ $device->device_name }}</td>
                                        <td class="text-center"><code class="bg-light p-1 rounded">{{ $device->mac_address }}</code></td>
                                        <td class="text-center">{{ $device->ip_address }}</td>
                                        <td class="text-center"><span class="badge bg-info">{{ $device->device_type }}</span></td>
                                        <td class="text-center"><span class="badge bg-warning">{{ $device->band }}</span></td>
                                        <td class="text-center">{{ $device->duration }}</td>
                                        <td class="text-center">
                                            @if($device->is_blocked)
                                                <span class="badge bg-danger">Blocked</span>
                                            @else
                                                <span class="badge bg-success">Active</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center align-items-center gap-1">
                                                {{-- View Button --}}
                                                <button class="btn btn-sm btn-outline-primary view-device-btn"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deviceDetailModal"
                                                    data-id="{{ $device->id }}"
                                                    data-user="{{ $device->user->name ?? 'N/A' }}"
                                                    data-router-id="{{ $device->router_id }}"
                                                    data-router-name="{{ $device->router_name }}"
                                                    data-device-name="{{ $device->device_name }}"
                                                    data-mac="{{ $device->mac_address }}"
                                                    data-ip="{{ $device->ip_address }}"
                                                    data-type="{{ $device->device_type }}"
                                                    data-band="{{ $device->band }}"
                                                    data-tx="{{ $device->tx_rate }}"
                                                    data-rx="{{ $device->rx_rate }}"
                                                    data-duration="{{ $device->duration }}"
                                                    data-status="{{ $device->is_blocked ? 'Blocked' : 'Active' }}"
                                                    data-created="{{ $device->created_at->format('H:i:s d/m/Y') }}"
                                                    data-updated="{{ $device->updated_at->format('H:i:s d/m/Y') }}"
                                                    title="View Details">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                {{-- Delete Form --}}
                                                <form action="{{ route('admin.router-devices.destroy', $device->id) }}" 
                                                      method="POST" 
                                                      style="display:inline-block; margin: 0;"
                                                      onsubmit="return confirm('Are you sure you want to delete this device ({{ $device->mac_address }})?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Device">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            @empty
                                <tbody>
                                    <tr>
                                        <td colspan="13" class="text-center py-4">No router devices found.</td>
                                    </tr>
                                </tbody>
                            @endforelse
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Device Detail Modal --}}
<div class="modal fade" id="deviceDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title">
                    <i class="bi bi-router text-primary me-2"></i>
                    Device Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><strong>Device ID:</strong> <span id="modal-id"></span></li>
                            <li class="list-group-item"><strong>User:</strong> <span id="modal-user"></span></li>
                            <li class="list-group-item"><strong>Router ID:</strong> <span id="modal-router-id"></span></li>
                            <li class="list-group-item"><strong>Router Name:</strong> <span id="modal-router-name"></span></li>
                            <li class="list-group-item"><strong>Device Name:</strong> <span id="modal-device-name"></span></li>
                            <li class="list-group-item"><strong>MAC Address:</strong> <code id="modal-mac"></code></li>
                            <li class="list-group-item"><strong>IP Address:</strong> <span id="modal-ip"></span></li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><strong>Device Type:</strong> <span id="modal-type"></span></li>
                            <li class="list-group-item"><strong>Band:</strong> <span id="modal-band"></span></li>
                            <li class="list-group-item"><strong>TX Rate:</strong> <span id="modal-tx"></span></li>
                            <li class="list-group-item"><strong>RX Rate:</strong> <span id="modal-rx"></span></li>
                            <li class="list-group-item"><strong>Duration:</strong> <span id="modal-duration"></span></li>
                            <li class="list-group-item"><strong>Status:</strong> <span id="modal-status"></span></li>
                            <li class="list-group-item"><strong>Created:</strong> <span id="modal-created"></span></li>
                            <li class="list-group-item"><strong>Updated:</strong> <span id="modal-updated"></span></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- JavaScript (inline) --}}
<script>
    // Toggle filter box
    document.getElementById('toggleFilter').addEventListener('click', function() {
        var box = document.getElementById('filterBox');
        box.style.display = (box.style.display === 'none') ? 'flex' : 'none';
    });

    // Toggle a single date group
    function toggleGroup(headerRow) {
        var tbody = headerRow.closest('tbody');
        var rows = tbody.querySelectorAll('.device-row');
        var icon = headerRow.querySelector('.toggle-icon');
        // If first row is collapsed, expand; else collapse
        var isCollapsed = rows[0]?.classList.contains('collapsed');
        rows.forEach(function(row) {
            if (isCollapsed) {
                row.classList.remove('collapsed');
            } else {
                row.classList.add('collapsed');
            }
        });
        // Update icon
        icon.classList.remove('bi-chevron-right', 'bi-chevron-down');
        icon.classList.add(isCollapsed ? 'bi-chevron-down' : 'bi-chevron-right');
    }

    function expandAll() {
        document.querySelectorAll('.device-row').forEach(function(row) {
            row.classList.remove('collapsed');
        });
        document.querySelectorAll('.toggle-icon').forEach(function(icon) {
            icon.classList.remove('bi-chevron-right');
            icon.classList.add('bi-chevron-down');
        });
    }

    function collapseAll() {
        document.querySelectorAll('.device-row').forEach(function(row) {
            row.classList.add('collapsed');
        });
        document.querySelectorAll('.toggle-icon').forEach(function(icon) {
            icon.classList.remove('bi-chevron-down');
            icon.classList.add('bi-chevron-right');
        });
    }

    // Select All
    function toggleAllCheckboxes(master) {
        document.querySelectorAll('.device-checkbox').forEach(function(cb) {
            cb.checked = master.checked;
        });
        updateBulkDelete();
    }

    // Update bulk delete button visibility
    function updateBulkDelete() {
        var checked = document.querySelectorAll('.device-checkbox:checked').length;
        document.getElementById('bulkDeleteBtn').style.display = (checked > 0) ? 'inline-block' : 'none';
    }

    // Bulk Delete
    document.getElementById('bulkDeleteBtn').addEventListener('click', function() {
        var ids = Array.from(document.querySelectorAll('.device-checkbox:checked')).map(function(cb) {
            return cb.value;
        });
        if (ids.length === 0) return;
        if (confirm('Are you sure you want to delete ' + ids.length + ' records?')) {
            fetch('{{ route("admin.router-devices.bulk-delete") }}', {
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
                    alert(data.message || 'Error deleting devices');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting devices');
            });
        }
    });

    // Device Detail Modal (Bootstrap 5)
    var modal = document.getElementById('deviceDetailModal');
    modal.addEventListener('show.bs.modal', function(event) {
        var btn = event.relatedTarget;
        document.getElementById('modal-id').textContent = btn.dataset.id || 'N/A';
        document.getElementById('modal-user').textContent = btn.dataset.user || 'N/A';
        document.getElementById('modal-router-id').textContent = btn.dataset.routerId || 'N/A';
        document.getElementById('modal-router-name').textContent = btn.dataset.routerName || 'N/A';
        document.getElementById('modal-device-name').textContent = btn.dataset.deviceName || 'N/A';
        document.getElementById('modal-mac').textContent = btn.dataset.mac || 'N/A';
        document.getElementById('modal-ip').textContent = btn.dataset.ip || 'N/A';
        document.getElementById('modal-type').textContent = btn.dataset.type || 'N/A';
        document.getElementById('modal-band').textContent = btn.dataset.band || 'N/A';
        document.getElementById('modal-tx').textContent = btn.dataset.tx || '0';
        document.getElementById('modal-rx').textContent = btn.dataset.rx || '0';
        document.getElementById('modal-duration').textContent = btn.dataset.duration || 'N/A';
        var status = btn.dataset.status || 'Active';
        document.getElementById('modal-status').innerHTML = (status === 'Blocked') 
            ? '<span class="badge bg-danger">Blocked</span>' 
            : '<span class="badge bg-success">Active</span>';
        document.getElementById('modal-created').textContent = btn.dataset.created || 'N/A';
        document.getElementById('modal-updated').textContent = btn.dataset.updated || 'N/A';
    });
</script>

@stop