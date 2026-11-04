@extends('admin.layouts.main')

@section('content')
<div class="container-fluid">
    <div class="row g-4">
        <div class="col-lg-12">
            <div class="card bg-white border-0 shadow-sm rounded-3 p-2">
                <div class="card-body pb-0">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

        

                    {{-- Bulk Actions Toolbar --}}
                    <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <!--<div class="form-check">-->
                            <!--    <input type="checkbox" class="form-check-input" id="selectAll">-->
                            <!--    <label class="form-check-label" for="selectAll">Select All</label>-->
                            <!--</div>-->
                            <!--<span class="text-muted mx-2">|</span>-->
                            <!--<span id="selectedCount" class="text-muted small">0 selected</span>-->
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-danger btn-sm" id="bulkDeleteBtn" disabled>
                                <i class="bi bi-trash"></i> Delete Selected
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th width="3%" class="p-3 bg-tile text-white fw-medium text-center" style="border-radius: 6px 0 0 6px;">
                                        <div class="form-check">
                                            <input type="checkbox" style="border:1px solid black" class="form-check-input" id="selectAllHeader">
                                        </div>
                                    </th>
                                    <th width="5%" class="p-3 bg-tile text-white fw-medium text-center">Sr.No.</th>
                                    <th width="12%" class="p-3 bg-tile text-white fw-medium text-center">Full Name</th>
                                    <th width="10%" class="p-3 bg-tile text-white fw-medium text-center">Email</th>
                                    <th width="12%" class="p-3 bg-tile text-white fw-medium text-center">Time &amp; Date</th>
                                    <th width="12%" class="p-3 bg-tile text-white fw-medium text-center">Issue</th>
                                    <th width="15%" class="p-3 bg-tile text-white fw-medium text-center">Message</th>
                                    <th width="7%" class="p-3 bg-tile text-white fw-medium text-center">Attachment</th>
                                    <th width="10%" class="p-3 bg-tile text-white fw-medium text-center" style="border-radius: 0 6px 6px 0;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($helps as $index => $help)
                                    <tr>
                                        <td class="text-center">
                                            <div class="form-check">
                                                <input type="checkbox" style="border:2px solid black" class="form-check-input row-checkbox" 
                                                       data-id="{{ $help->id }}" value="{{ $help->id }}">
                                            </div>
                                        </td>
                                        <td class="text-center">{{ $helps->firstItem() + $index }}</td>
                                        <td class="text-center">{{ $help->name }}</td>
                                        <td class="text-center"><a href="mailto:{{ $help->email }}">{{ $help->email }}</a></td>
                                        <td class="text-center">
                                            {{ \Carbon\Carbon::parse($help->created_at)->format('H:i:s') }}<br />
                                            {{ \Carbon\Carbon::parse($help->created_at)->format('d/m/Y') }}
                                        </td>
                                        <td class="text-center">{{ $help->issue }}</td>
                                        <td class="text-center fw-medium">{{ Str::limit($help->message, 50) }}</td>
                                        <td class="text-center">
                                            @if($help->attachment)
                                                <a class="text-primary" href="{{ asset('storage/' . $help->attachment) }}" download>
                                                    <i class="bi bi-file-earmark-zip" style="font-size: 24px;"></i>
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                       
                                        <td class="text-center">
                                            <div class="d-flex flex-column align-items-center gap-1">
                                                {{-- Status Badge --}}
                                                @php
                                                    $statusColors = [
                                                        'pending' => 'warning',
                                                        'in_progress' => 'primary',
                                                        'resolved' => 'success',
                                                        'closed' => 'secondary'
                                                    ];
                                                    $statusLabels = [
                                                        'pending' => 'Pending',
                                                        'in_progress' => 'In Progress',
                                                        'resolved' => 'Resolved',
                                                        'closed' => 'Closed'
                                                    ];
                                                    $badgeColor = $statusColors[$help->status] ?? 'secondary';
                                                    $statusLabel = $statusLabels[$help->status] ?? ucfirst(str_replace('_', ' ', $help->status));
                                                @endphp
                                                <span class="badge bg-{{ $badgeColor }} status-badge" style="font-size: 0.85rem; padding: 6px 12px; width: 100%;">
                                                    {{ $statusLabel }}
                                                </span>
                                                
                                                {{-- Status Dropdown - Only shows allowed options --}}
                                                <select class="form-select form-select-sm status-select w-100" 
                                                        style="min-width: 120px; margin-top: 4px;" 
                                                        data-id="{{ $help->id }}"
                                                        data-current-status="{{ $help->status }}"
                                                        data-url="{{ route('admin.help.updateStatus', $help->id) }}">
                                                    @php
                                                        $statusFlow = [
                                                            'pending' => ['pending', 'in_progress', 'resolved', 'closed'],
                                                            'in_progress' => ['in_progress', 'resolved', 'closed'],
                                                            'resolved' => ['resolved', 'closed'],
                                                            'closed' => ['closed']
                                                        ];
                                                        $allowedStatuses = $statusFlow[$help->status] ?? ['pending', 'in_progress', 'resolved', 'closed'];
                                                    @endphp
                                                    @foreach($allowedStatuses as $status)
                                                        <option value="{{ $status }}" {{ $help->status == $status ? 'selected' : '' }}>
                                                            {{ $statusLabels[$status] ?? ucfirst(str_replace('_', ' ', $status)) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">No help requests found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination with summary --}}
                    @if($helps->hasPages())
                        <div class="d-flex flex-wrap align-items-center justify-content-between mt-3">
                            <div class="text-muted small">
                                Showing {{ $helps->firstItem() }} to {{ $helps->lastItem() }} of {{ $helps->total() }} results
                            </div>
                            <div>
                                {{ $helps->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    @else
                        <div class="text-muted small mt-3">
                            Total: {{ $helps->total() }} results
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>

{{-- Bulk Delete Confirmation Modal --}}
<div class="modal fade" id="bulkDeleteModal" tabindex="-1" aria-labelledby="bulkDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkDeleteModalLabel">Confirm Bulk Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete <strong id="bulkDeleteCount">0</strong> selected help request(s)?
                <div class="alert alert-warning mt-2">
                    <i class="bi bi-exclamation-triangle"></i> This action cannot be undone!
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="bulkDeleteForm" method="POST" action="{{ route('admin.help.bulkDestroy') }}">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="ids" id="bulkDeleteIds" value="">
                    <button type="submit" class="btn btn-danger">Delete All Selected</button>
                </form>
            </div>
        </div>
    </div>
</div>
@stop

@push('scripts')
<script>
    $(document).ready(function() {
        // ===================== SELECT ALL FUNCTIONALITY =====================
        
        // Select All from header
        $('#selectAllHeader, #selectAll').on('change', function() {
            var isChecked = $(this).prop('checked');
            $('.row-checkbox').prop('checked', isChecked);
            updateBulkDeleteButton();
        });

        // Individual checkbox change
        $(document).on('change', '.row-checkbox', function() {
            var allChecked = $('.row-checkbox:checked').length === $('.row-checkbox').length;
            $('#selectAllHeader, #selectAll').prop('checked', allChecked);
            updateBulkDeleteButton();
        });

        // Update bulk delete button state
        function updateBulkDeleteButton() {
            var checkedCount = $('.row-checkbox:checked').length;
            $('#selectedCount').text(checkedCount + ' selected');
            
            if (checkedCount > 0) {
                $('#bulkDeleteBtn').prop('disabled', false);
            } else {
                $('#bulkDeleteBtn').prop('disabled', true);
            }
        }

        // ===================== BULK DELETE FUNCTIONALITY =====================
        
        $('#bulkDeleteBtn').on('click', function() {
            var selectedIds = [];
            $('.row-checkbox:checked').each(function() {
                selectedIds.push($(this).val());
            });
            
            if (selectedIds.length === 0) {
                toastr.warning('Please select at least one item to delete.');
                return;
            }
            
            $('#bulkDeleteCount').text(selectedIds.length);
            $('#bulkDeleteIds').val(selectedIds.join(','));
            $('#bulkDeleteModal').modal('show');
        });

        // ===================== STATUS CHANGE FUNCTIONALITY =====================
        
        $(document).on('change', '.status-select', function() {
            var select = $(this);
            var helpId = select.data('id');
            var newStatus = select.val();
            var currentStatus = select.data('current-status');
            var row = select.closest('tr');
            var badge = row.find('.status-badge');
            var url = select.data('url');
            
            // If new status is the same as current, do nothing
            if (newStatus === currentStatus) {
                return;
            }
            
            // Disable the select during processing
            select.prop('disabled', true);
            select.css('opacity', '0.6');
            
            // Define status labels for display
            var statusLabels = {
                'pending': 'Pending',
                'in_progress': 'In Progress',
                'resolved': 'Resolved',
                'closed': 'Closed'
            };
            
            // Status flow for validation (frontend check)
            var statusFlow = {
                'pending': ['in_progress', 'resolved', 'closed'],
                'in_progress': ['resolved', 'closed'],
                'resolved': ['closed'],
                'closed': [] // No further changes allowed
            };
            
            // Get readable status names
            var currentStatusText = statusLabels[currentStatus] || currentStatus.replace('_', ' ').replace(/\b\w/g, function(l) { 
                return l.toUpperCase(); 
            });
            var newStatusText = statusLabels[newStatus] || newStatus.replace('_', ' ').replace(/\b\w/g, function(l) { 
                return l.toUpperCase(); 
            });
            
            // Check if the new status is allowed from current status
            if (!statusFlow[currentStatus].includes(newStatus)) {
                // Revert to current status in dropdown
                select.find('option').each(function() {
                    if ($(this).val() === currentStatus) {
                        $(this).prop('selected', true);
                    }
                });
                
                // Show user-friendly error message
                var errorMessage = '';
                if (currentStatus === 'resolved' && newStatus === 'in_progress') {
                    errorMessage = 'Cannot revert from "Resolved" to "In Progress". Once resolved, you can only move to "Closed".';
                } else if (currentStatus === 'closed') {
                    errorMessage = 'Status is "Closed" and cannot be changed further.';
                } else if (currentStatus === 'resolved') {
                    errorMessage = 'Cannot change from "Resolved" to "' + newStatusText + '". Only "Closed" is allowed.';
                } else if (currentStatus === 'in_progress' && newStatus === 'pending') {
                    errorMessage = 'Cannot revert from "In Progress" to "Pending". You can only move to "Resolved" or "Closed".';
                } else {
                    errorMessage = 'Cannot change status from "' + currentStatusText + '" to "' + newStatusText + 
                                   '". Please follow the proper workflow: Pending → In Progress → Resolved → Closed';
                }
                
                toastr.error(errorMessage);
                
                select.prop('disabled', false);
                select.css('opacity', '1');
                return;
            }
            
            // Proceed with AJAX request
            $.ajax({
                url: url,
                type: 'PATCH',
                data: {
                    status: newStatus,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        // Update badge
                        var statusText = statusLabels[newStatus] || newStatus.replace('_', ' ').replace(/\b\w/g, function(l) { 
                            return l.toUpperCase(); 
                        });
                        var badgeColor = response.badge || 'secondary';
                        
                        // Remove all status classes
                        badge.removeClass('bg-warning bg-primary bg-success bg-secondary');
                        // Add new class
                        badge.addClass('bg-' + badgeColor);
                        // Update text
                        badge.text(statusText);
                        
                        // Update current status data attribute
                        select.data('current-status', newStatus);
                        
                        // Update the dropdown options to reflect new allowed statuses
                        // We need to rebuild the options based on new status
                        updateStatusOptions(select, newStatus);
                        
                        toastr.success(response.message);
                    } else {
                        // Handle backend validation failure
                        toastr.error(response.message || 'Failed to update status.');
                        
                        // Revert select to previous value
                        select.find('option').each(function() {
                            if ($(this).val() === currentStatus) {
                                $(this).prop('selected', true);
                            }
                        });
                    }
                },
                error: function(xhr) {
                    // Handle error
                    var errorMessage = 'Failed to update status. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    toastr.error(errorMessage);
                    
                    // Revert select to previous value
                    select.find('option').each(function() {
                        if ($(this).val() === currentStatus) {
                            $(this).prop('selected', true);
                        }
                    });
                },
                complete: function() {
                    select.prop('disabled', false);
                    select.css('opacity', '1');
                }
            });
        });
        
        // Function to update status options based on new status
        function updateStatusOptions(select, newStatus) {
            var statusFlow = {
                'pending': ['pending', 'in_progress', 'resolved', 'closed'],
                'in_progress': ['in_progress', 'resolved', 'closed'],
                'resolved': ['resolved', 'closed'],
                'closed': ['closed']
            };
            
            var statusLabels = {
                'pending': 'Pending',
                'in_progress': 'In Progress',
                'resolved': 'Resolved',
                'closed': 'Closed'
            };
            
            var allowedStatuses = statusFlow[newStatus] || ['pending', 'in_progress', 'resolved', 'closed'];
            
            // Clear existing options
            select.empty();
            
            // Add new options
            $.each(allowedStatuses, function(index, status) {
                var option = $('<option>', {
                    value: status,
                    text: statusLabels[status] || status.replace('_', ' ').replace(/\b\w/g, function(l) { 
                        return l.toUpperCase(); 
                    })
                });
                if (status === newStatus) {
                    option.prop('selected', true);
                }
                select.append(option);
            });
        }

        // ===================== TOASTR CONFIGURATION =====================
        
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "5000",
            "extendedTimeOut": "2000",
            "showMethod": "slideDown",
            "hideMethod": "slideUp",
            "preventDuplicates": true
        };

        // Initialize bulk delete button state
        updateBulkDeleteButton();
    });
</script>
@endpush