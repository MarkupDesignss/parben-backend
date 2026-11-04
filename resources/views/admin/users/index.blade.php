@extends('admin.layouts.main')

@section('content')
<div class="container-fluid">
    <div class="row g-4">
        <div class="col-lg-12">
            <div class="card bg-white border-0 shadow-sm rounded-3 p-2">
                <div class="card-body">
                    <div class="my-md-4">
                        <div class="row g-4">
                            <div class="col-lg-6 col-md-12">
                                <div class="add-user-btn">
                                    <a class="btn btn-primary rounded-pill" href="{{ route('admin.users.create') }}">
                                        <img class="me-2" src="{{ asset('assets/images/icon-plus.svg') }}" />Add User
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12 me-auto">
                                <div class="case-search-bx d-flex align-items-center justify-content-end">
                                    <form method="GET" action="{{ route('admin.users.index') }}" class="d-flex w-100 justify-content-end">
                                        <div class="search-bx position-relative me-3 w-100">
                                            <div class="input-group">
                                                <button class="btn-outline-secondary border-0" type="submit">
                                                    <i class="bi bi-search"></i>
                                                </button>
                                                <input class="form-control border rounded-pill" type="search" value="{{ request('search') }}" name="search" placeholder="Search..." />
                                            </div>
                                        </div>
                                    </form>
                                    <div class="case-filter-bx">
                                        <div class="case-export">
                                            <a class="btn btn-primary rounded-pill" href="{{ route('admin.users.export', request()->query()) }}">
                                                <img class="me-2" src="{{ asset('assets/images/import-export.svg') }}" />Export
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body pb-0">
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
                                    <th width="10%" class="p-3 bg-tile text-white fw-medium text-center">Image</th>
                                    <th width="17%" class="p-3 bg-tile text-white fw-medium text-center">User Name</th>
                                    <th width="18%" class="p-3 bg-tile text-white fw-medium text-center">Email</th>
                                    <th width="12%" class="p-3 bg-tile text-white fw-medium text-center">Phone</th>
                                    <th width="15%" class="p-3 bg-tile text-white fw-medium text-center">Organization</th>
                                    <th width="18%" class="p-3 bg-tile text-white fw-medium text-center" style="border-radius: 0 6px 6px 0;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $index => $user)
                                    <tr>
                                        <td align="center">
                                            <input type="checkbox" class="user-checkbox" value="{{ $user->id }}" onchange="updateBulkDelete()">
                                        </td>
                                        
                                        <td align="center">
                                            {{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}
                                        </td>
                                        
                                        <td align="center">
                                            @if($user->image && file_exists(public_path('storage/' . $user->image)))
                                                <img src="{{ asset('storage/' . $user->image) }}" 
                                                     alt="{{ $user->name }}" 
                                                     class="profile-image-clickable"
                                                     data-image="{{ asset('storage/' . $user->image) }}"
                                                     data-name="{{ $user->name }}"
                                                     style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid #ddd; cursor: pointer; transition: transform 0.2s;"
                                                     onmouseover="this.style.transform='scale(1.1)'"
                                                     onmouseout="this.style.transform='scale(1)'"
                                                     onclick="openImagePreview(this)">
                                            @else
                                                <span class="text-muted" style="font-size: 12px;">No Image</span>
                                            @endif
                                        </td>
                                        
                                        <td align="center">{{ $user->name }}</td>
                                        <td align="center"><a href="mailto:{{ $user->email }}">{{ $user->email }}</a></td>
                                        <td align="center">
                                            @if($user->phone)
                                                <a href="tel:{{ $user->phone }}">{{ $user->phone }}</a>
                                            @else
                                                <span class="text-muted">NA</span>
                                            @endif
                                        </td>
                                        <td align="center">
                                            @if($user->organization)
                                                {{ $user->organization }}
                                            @else
                                                <span class="text-muted">NA</span>
                                            @endif
                                        </td>
                                        
                                        <td align="center">
                                            <a href="{{ route('admin.users.edit', $user->id) }}" class="text-primary me-2" title="Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure to delete this user?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-link text-danger p-0 m-0 align-baseline" title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No users found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($users instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        <div class="d-flex justify-content-center mt-3">
                            {{ $users->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================ --}}
{{-- PERFECT IMAGE PREVIEW MODAL - CUSTOM DESIGN --}}
{{-- ============================================ --}}
<div id="imagePreviewModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.85); z-index: 99999; justify-content: center; align-items: center; padding: 20px;">
    <div style="position: relative; max-width: 90%; max-height: 90%; background: white; border-radius: 12px; padding: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.5);">
        
        {{-- Close Button --}}
        <button onclick="closeImagePreview()" 
                style="position: absolute; top: -15px; right: -15px; width: 40px; height: 40px; background: #dc3545; border: 3px solid white; border-radius: 50%; color: white; font-size: 24px; font-weight: bold; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.3); transition: all 0.3s; z-index: 10;"
                onmouseover="this.style.transform='scale(1.1)'; this.style.background='#c82333';"
                onmouseout="this.style.transform='scale(1)'; this.style.background='#dc3545';">
            ✕
        </button>
        
        {{-- Image Container --}}
        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; max-width: 100%; max-height: 80vh;">
            <img id="previewImage" src="" alt="Profile Image" 
                 style="max-width: 100%; max-height: 65vh; width: auto; height: auto; object-fit: contain; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
            
            {{-- User Info --}}
            <div style="margin-top: 15px; text-align: center; width: 100%; border-top: 1px solid #eee; padding-top: 15px;">
                <p style="font-size: 18px; font-weight: 600; color: #333; margin: 0 0 10px 0;">
                    <i class="bi bi-person-circle me-2" style="color: #667eea;"></i>
                    <span id="userNameDisplay">User</span>
                </p>
                <button onclick="downloadImage()" 
                        style="padding: 8px 25px; background: #667eea; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; transition: all 0.3s;"
                        onmouseover="this.style.background='#5a67d8'"
                        onmouseout="this.style.background='#667eea'">
                    <i class="bi bi-download me-1"></i> Download
                </button>
                <button onclick="closeImagePreview()" 
                        style="padding: 8px 25px; background: #6c757d; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; margin-left: 10px; transition: all 0.3s;"
                        onmouseover="this.style.background='#5a6268'"
                        onmouseout="this.style.background='#6c757d'">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Select All toggle
    function toggleAllCheckboxes(master) {
        document.querySelectorAll('.user-checkbox').forEach(function(cb) {
            cb.checked = master.checked;
        });
        updateBulkDelete();
    }

    // Show/hide bulk delete button
    function updateBulkDelete() {
        var checked = document.querySelectorAll('.user-checkbox:checked').length;
        document.getElementById('bulkDeleteBtn').style.display = (checked > 0) ? 'inline-block' : 'none';
    }

    // Bulk delete AJAX
    document.getElementById('bulkDeleteBtn').addEventListener('click', function() {
        var ids = Array.from(document.querySelectorAll('.user-checkbox:checked')).map(function(cb) {
            return cb.value;
        });
        if (ids.length === 0) return;
        if (confirm('Are you sure you want to delete ' + ids.length + ' user(s)?')) {
            fetch('{{ route("admin.users.bulk-delete") }}', {
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
                    alert(data.message || 'Error deleting users');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting users');
            });
        }
    });

    // ============================================
    // IMAGE PREVIEW FUNCTIONS - PERFECT WORKING
    // ============================================

    // Open image preview
    function openImagePreview(element) {
        var imageSrc = element.getAttribute('data-image') || element.src;
        var userName = element.getAttribute('data-name') || 'User';
        
        // Check if image exists
        if (!imageSrc || imageSrc === '') {
            return false;
        }
        
        // Set image and user name
        document.getElementById('previewImage').src = imageSrc;
        document.getElementById('userNameDisplay').textContent = userName;
        document.getElementById('previewImage').setAttribute('data-current-src', imageSrc);
        
        // Show modal
        document.getElementById('imagePreviewModal').style.display = 'flex';
        document.body.style.overflow = 'hidden'; // Prevent scroll
    }

    // Close image preview
    function closeImagePreview() {
        document.getElementById('imagePreviewModal').style.display = 'none';
        document.body.style.overflow = 'auto'; // Enable scroll
    }

    // Download image
    function downloadImage() {
        var imageSrc = document.getElementById('previewImage').getAttribute('data-current-src') || document.getElementById('previewImage').src;
        var fileName = document.getElementById('userNameDisplay').textContent.trim() + '_profile.jpg';
        
        fetch(imageSrc)
            .then(response => response.blob())
            .then(blob => {
                var link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = fileName;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                URL.revokeObjectURL(link.href);
            })
            .catch(() => {
                // Fallback: direct download
                var link = document.createElement('a');
                link.href = imageSrc;
                link.download = fileName;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            });
    }

    // ============================================
    // CLOSE MODAL - MULTIPLE WAYS
    // ============================================

    // 1. Close on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeImagePreview();
        }
    });

    // 2. Close on click outside the modal content
    document.getElementById('imagePreviewModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeImagePreview();
        }
    });

    // 3. Close on window resize (optional)
    window.addEventListener('resize', function() {
        // Just keep modal responsive
    });

    console.log('Image Preview Modal loaded successfully!');
</script>
@endpush