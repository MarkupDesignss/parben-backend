@extends('admin.layouts.main')

@section('content')
<div class="container-fluid">
    <div class="row g-4">
        <div class="col-lg-12">
            <div class="card bg-white border-0 shadow-sm rounded-3 p-2">
                <div class="card-body pb-0">

                    {{-- Bulk Delete Button --}}
                    <div class="d-flex justify-content-end mb-3">
                        <button class="btn btn-danger btn-sm" id="bulkDeleteBtn" style="display:none">
                            <i class="bi bi-trash"></i> Delete Selected
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th width="5%" class="p-3 bg-tile text-white fw-medium text-center" style="border-radius: 6px 0 0 6px;">
                                        <input type="checkbox" id="selectAll" onchange="toggleAllCheckboxes(this)">
                                    </th>
                                    <th width="5%" class="p-3 bg-tile text-white fw-medium text-center">Sr.No.</th>
                                    <th width="20%" class="p-3 bg-tile text-white fw-medium text-center">Full Name</th>
                                    <th width="20%" class="p-3 bg-tile text-white fw-medium text-center">Email</th>
                                    <th width="20%" class="p-3 bg-tile text-white fw-medium text-center">Phone</th>
                                    <th width="20%" class="p-3 bg-tile text-white fw-medium text-center">Subject</th>
                                    <th width="30%" class="p-3 bg-tile text-white fw-medium text-center" style="border-radius: 0 6px 6px 0;">Message</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($contacts as $index => $contact)
                                    <tr>
                                        <td class="text-center">
                                            <input type="checkbox" class="contact-checkbox" value="{{ $contact->id }}" onchange="updateBulkDelete()">
                                        </td>
                                        <td class="text-center">
                                            {{ $contacts->firstItem() + $index }}
                                        </td>
                                        <td class="text-center">{{ $contact->name }}</td>
                                        <td class="text-center">
                                            <a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a>
                                        </td>
                                        <td class="text-center">
                                           <a href="tel:{{ $contact->phone }}">{{ $contact->phone }}</a>
                                        </td>
                                        <td class="text-center">{{ $contact->subject }}</td>
                                        <td class="text-center">{{ Str::limit($contact->message, 50) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No contact entries found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if($contacts->hasPages())
                        <div class="d-flex flex-wrap align-items-center justify-content-between mt-3">
                            <div class="text-muted small">
                                Showing {{ $contacts->firstItem() }} to {{ $contacts->lastItem() }} of {{ $contacts->total() }} results
                            </div>
                            <div>
                                {{ $contacts->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    @else
                        <div class="text-muted small mt-3">
                            Total: {{ $contacts->total() }} results
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Select All toggle
    function toggleAllCheckboxes(master) {
        document.querySelectorAll('.contact-checkbox').forEach(function(cb) {
            cb.checked = master.checked;
        });
        updateBulkDelete();
    }

    // Show/hide bulk delete button
    function updateBulkDelete() {
        var checked = document.querySelectorAll('.contact-checkbox:checked').length;
        document.getElementById('bulkDeleteBtn').style.display = (checked > 0) ? 'inline-block' : 'none';
    }

    // ===== FIXED: Bulk Delete Handler =====
    document.getElementById('bulkDeleteBtn').addEventListener('click', function() {
        // Collect selected IDs
        var ids = Array.from(document.querySelectorAll('.contact-checkbox:checked')).map(function(cb) {
            return parseInt(cb.value);
        });

        if (ids.length === 0) return;

        if (!confirm('Are you sure you want to delete ' + ids.length + ' contact(s)?')) {
            return;
        }

        // Get CSRF token safely
        var tokenElement = document.querySelector('meta[name="csrf-token"]');
        var token = tokenElement ? tokenElement.getAttribute('content') : '';
        // Fallback to Laravel's @csrf if meta tag is missing (should not happen with layout)
        if (!token) {
            token = '{{ csrf_token() }}';
        }

        fetch('{{ route("admin.contacts.bulk-delete") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ ids: ids })
        })
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => {
                    throw new Error(text || 'Server error');
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Error deleting contacts');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting contacts. Please try again.');
        });
    });
</script>
@endsection