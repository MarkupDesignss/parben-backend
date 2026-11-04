@extends('admin.layouts.main')

@section('content')
<style>
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
                    <form method="GET" action="{{ route('admin.unsupported-devices.index') }}" id="filterForm">
                        <div class="d-flex align-items-center justify-content-end flex-wrap gap-2">
                            <div class="search-bx position-relative me-3 flex-grow-1" style="max-width: 400px;">
                                <div class="input-group">
                                    <button class="btn-outline-secondary border-0" type="submit">
                                        <i class="bi bi-search"></i>
                                    </button>
                                    <input class="form-control border rounded-pill"
                                           name="search"
                                           type="search"
                                           value="{{ request('search') }}"
                                           placeholder="Search by brand, model, router ID, IP..." />
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <a href="{{ route('admin.unsupported-devices.export', request()->query()) }}" class="btn btn-success rounded-pill">
                                    <i class="bi bi-download me-1"></i> Export
                                </a>
                                <a href="{{ route('admin.unsupported-devices.index') }}" class="btn btn-secondary rounded-pill">
                                    Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Table Header Actions --}}
                <div class="card-body pb-0">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <span class="badge bg-primary">Total: {{ $reports->total() }}</span>
                        </div>
                        <div>
                            <button class="btn btn-danger btn-sm" id="bulkDeleteBtn" style="display:none">
                                <i class="bi bi-trash"></i> Delete Selected
                            </button>
                        </div>
                    </div>

                    {{-- Table --}}
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th class="p-3 bg-tile text-white text-center" style="border-radius: 6px 0 0 6px; min-width: 50px;">
                                        <input type="checkbox" id="selectAll" onchange="toggleAllCheckboxes(this)">
                                    </th>
                                    <th class="p-3 bg-tile text-white text-center" style="min-width: 60px;">Sr.No.</th>
                                    <th class="p-3 bg-tile text-white text-center" style="min-width: 100px;">User</th>
                                    <th class="p-3 bg-tile text-white text-center" style="min-width: 150px;">Router</th>
                                    <th class="p-3 bg-tile text-white text-center" style="min-width: 120px;">Router ID</th>
                                    <th class="p-3 bg-tile text-white text-center" style="min-width: 120px;">Router IP</th>
                                    <th class="p-3 bg-tile text-white text-center" style="min-width: 100px;">Platform</th>
                                    <th class="p-3 bg-tile text-white text-center" style="min-width: 80px;">App Ver.</th>
                                    <th class="p-3 bg-tile text-white text-center" style="min-width: 180px;">Failure Reason</th>
                                    <th class="p-3 bg-tile text-white text-center" style="min-width: 90px;">Email</th>
                                    <th class="p-3 bg-tile text-white text-center" style="min-width: 130px;">Date</th>
                                    <th class="p-3 bg-tile text-white text-center" style="border-radius: 0 6px 6px 0; min-width: 120px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reports as $index => $report)
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox" class="report-checkbox" value="{{ $report->id }}" onchange="updateBulkDelete()">
                                    </td>
                                    <td class="text-center">{{ $reports->firstItem() + $loop->index }}</td>
                                    <td class="text-center">{{ $report->user->name ?? 'N/A' }}</td>
                                    <td class="text-center">
                                        <strong>{{ $report->router_brand }}</strong><br>
                                        <small class="text-muted">{{ $report->router_model }}</small>
                                    </td>
                                    <td class="text-center"><code>{{ $report->router_id }}</code></td>
                                    <td class="text-center"><code class="bg-light p-1 rounded">{{ $report->router_ip }}</code></td>
                                    <td class="text-center">
                                        @if($report->platform == 'iOS')
                                            <span class="badge bg-dark"><i class="bi bi-apple me-1"></i>{{ $report->platform }}</span>
                                        @else
                                            <span class="badge bg-success"><i class="bi bi-android2 me-1"></i>{{ $report->platform }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center"><span class="badge bg-secondary">{{ $report->app_version }}</span></td>
                                    <td class="text-center">
                                        <span title="{{ $report->failure_reason }}">
                                            {{ \Illuminate\Support\Str::limit($report->failure_reason ?? 'N/A', 40) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($report->email_sent)
                                            <span class="badge bg-success"><i class="bi bi-check-circle"></i> Sent</span>
                                        @else
                                            <span class="badge bg-secondary"><i class="bi bi-x-circle"></i> Not Sent</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        {{ $report->created_at->format('d M Y') }}<br>
                                        <small class="text-muted">{{ $report->created_at->format('H:i A') }}</small>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center align-items-center gap-1">
                                            <button type="button"
                                                class="btn btn-sm btn-outline-primary view-report-btn"
                                                data-id="{{ $report->id }}"
                                                data-user="{{ $report->user->name ?? 'N/A' }}"
                                                data-user-email="{{ $report->user->email ?? 'N/A' }}"
                                                data-brand="{{ $report->router_brand }}"
                                                data-model="{{ $report->router_model }}"
                                                data-router-id="{{ $report->router_id }}"
                                                data-router-ip="{{ $report->router_ip }}"
                                                data-platform="{{ $report->platform }}"
                                                data-app-version="{{ $report->app_version }}"
                                                data-os-version="{{ $report->os_version }}"
                                                data-capture-failed="{{ $report->capture_failed ? 'Yes' : 'No' }}"
                                                data-failure-reason="{{ $report->failure_reason ?? 'N/A' }}"
                                                data-router-page="{{ $report->router_page ?? 'N/A' }}"
                                                data-diagnostic-ts="{{ $report->diagnostic_timestamp ?? 'N/A' }}"
                                                data-email-sent="{{ $report->email_sent ? 'Yes' : 'No' }}"
                                                data-created="{{ $report->created_at->format('H:i:s d/m/Y') }}"
                                                title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <form action="{{ route('admin.unsupported-devices.destroy', $report->id) }}"
                                                  method="POST"
                                                  style="display:inline-block; margin: 0;"
                                                  onsubmit="return confirm('Are you sure you want to delete this report (#{{ $report->id }})?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Report">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="12" class="text-center py-4">No unsupported device reports found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-3">
                        {{ $reports->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Report Detail Modal --}}
<div class="modal fade" id="reportDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle text-danger me-2"></i>
                    Unsupported Device Report
                </h5>
                <button type="button" class="close btn-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary mb-2">User Info</h6>
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item"><strong>User:</strong> <span id="modal-user"></span></li>
                            <li class="list-group-item"><strong>Email:</strong> <span id="modal-user-email"></span></li>
                        </ul>

                        <h6 class="text-primary mb-2">Router Info</h6>
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item"><strong>Brand:</strong> <span id="modal-brand"></span></li>
                            <li class="list-group-item"><strong>Model:</strong> <span id="modal-model"></span></li>
                            <li class="list-group-item"><strong>Router ID:</strong> <code id="modal-router-id"></code></li>
                            <li class="list-group-item"><strong>Router IP:</strong> <span id="modal-router-ip"></span></li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary mb-2">Device Info</h6>
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item"><strong>Platform:</strong> <span id="modal-platform"></span></li>
                            <li class="list-group-item"><strong>App Version:</strong> <span id="modal-app-version"></span></li>
                            <li class="list-group-item"><strong>OS Version:</strong> <span id="modal-os-version"></span></li>
                        </ul>

                        <h6 class="text-primary mb-2">Diagnostics</h6>
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item"><strong>Capture Failed:</strong> <span id="modal-capture-failed"></span></li>
                            <li class="list-group-item"><strong>Failure Reason:</strong> <span id="modal-failure-reason"></span></li>
                            <li class="list-group-item"><strong>Router Page:</strong> <span id="modal-router-page"></span></li>
                            <li class="list-group-item"><strong>Timestamp:</strong> <span id="modal-diagnostic-ts"></span></li>
                        </ul>
                    </div>
                </div>

                <div class="border-top pt-3 mt-2">
                    <small class="text-muted d-block">
                        <strong>Email:</strong> <span id="modal-email-sent"></span>
                    </small>
                    <small class="text-muted d-block mt-2">
                        <strong>Created:</strong> <span id="modal-created"></span>
                    </small>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary rounded-pill" data-dismiss="modal" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- JavaScript --}}
<script>
    // ---------------- SELECT ALL / BULK DELETE ----------------
    function toggleAllCheckboxes(master) {
        document.querySelectorAll('.report-checkbox').forEach(function(cb) {
            cb.checked = master.checked;
        });
        updateBulkDelete();
    }

    function updateBulkDelete() {
        var checked = document.querySelectorAll('.report-checkbox:checked').length;
        document.getElementById('bulkDeleteBtn').style.display = (checked > 0) ? 'inline-block' : 'none';
    }

    document.getElementById('bulkDeleteBtn').addEventListener('click', function() {
        var ids = Array.from(document.querySelectorAll('.report-checkbox:checked')).map(function(cb) {
            return cb.value;
        });
        if (ids.length === 0) return;
        if (confirm('Are you sure you want to delete ' + ids.length + ' reports?')) {
            fetch('{{ route("admin.unsupported-devices.bulk-delete") }}', {
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
                    alert(data.message || 'Error deleting reports');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting reports');
            });
        }
    });

    // ---------------- MODAL HELPERS (Bootstrap 4 / jQuery) ----------------
    function openModalSafe() {
        var modalEl = document.getElementById('reportDetailModal');

        // Preferred: Bootstrap 4 via jQuery
        if (typeof jQuery !== 'undefined' && jQuery.fn.modal) {
            jQuery(modalEl).modal('show');
            return;
        }

        // Pure manual fallback
        modalEl.classList.add('show');
        modalEl.style.display = 'block';
        modalEl.style.backgroundColor = 'rgba(0,0,0,0.5)';
        modalEl.removeAttribute('aria-hidden');
        document.body.classList.add('modal-open');

        if (!document.getElementById('manualBackdrop')) {
            var backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            backdrop.id = 'manualBackdrop';
            document.body.appendChild(backdrop);
        }
    }

    function closeModalSafe() {
        var modalEl = document.getElementById('reportDetailModal');

        // Bootstrap 4 via jQuery
        if (typeof jQuery !== 'undefined' && jQuery.fn.modal) {
            jQuery(modalEl).modal('hide');
        }

        // Force cleanup (safe for both paths)
        modalEl.classList.remove('show');
        modalEl.style.display = 'none';
        modalEl.style.backgroundColor = '';
        modalEl.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';

        document.querySelectorAll('.modal-backdrop').forEach(function(el) { el.remove(); });
    }

    // ---------------- VIEW BUTTON CLICK ----------------
    document.querySelectorAll('.view-report-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var b = this;

            document.getElementById('modal-user').textContent        = b.dataset.user || 'N/A';
            document.getElementById('modal-user-email').textContent  = b.dataset.userEmail || 'N/A';
            document.getElementById('modal-brand').textContent       = b.dataset.brand || 'N/A';
            document.getElementById('modal-model').textContent       = b.dataset.model || 'N/A';
            document.getElementById('modal-router-id').textContent   = b.dataset.routerId || 'N/A';
            document.getElementById('modal-router-ip').textContent   = b.dataset.routerIp || 'N/A';
            document.getElementById('modal-platform').textContent    = b.dataset.platform || 'N/A';
            document.getElementById('modal-app-version').textContent = b.dataset.appVersion || 'N/A';
            document.getElementById('modal-os-version').textContent  = b.dataset.osVersion || 'N/A';

            document.getElementById('modal-capture-failed').innerHTML = (b.dataset.captureFailed === 'Yes')
                ? '<span class="badge bg-danger">Yes</span>'
                : '<span class="badge bg-success">No</span>';

            document.getElementById('modal-failure-reason').textContent = b.dataset.failureReason || 'N/A';
            document.getElementById('modal-router-page').textContent    = b.dataset.routerPage || 'N/A';
            document.getElementById('modal-diagnostic-ts').textContent  = b.dataset.diagnosticTs || 'N/A';

            document.getElementById('modal-email-sent').innerHTML = (b.dataset.emailSent === 'Yes')
                ? '<span class="badge bg-success">Sent</span>'
                : '<span class="badge bg-secondary">Not Sent</span>';

            document.getElementById('modal-created').textContent = b.dataset.created || 'N/A';

            openModalSafe();
        });
    });

    // ---------------- CLOSE HANDLERS ----------------
    document.querySelectorAll('#reportDetailModal [data-dismiss="modal"], #reportDetailModal [data-bs-dismiss="modal"], #reportDetailModal .btn-close, #reportDetailModal .close').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            closeModalSafe();
        });
    });

    // Backdrop click closes modal
    document.getElementById('reportDetailModal').addEventListener('click', function(e) {
        if (e.target === this) closeModalSafe();
    });

    // ESC key closes modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' || e.key === 'Esc') {
            var modalEl = document.getElementById('reportDetailModal');
            if (modalEl.classList.contains('show') || modalEl.style.display === 'block') {
                closeModalSafe();
            }
        }
    });
</script>

@stop