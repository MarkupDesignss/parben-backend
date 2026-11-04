@extends('admin.layouts.main')

@section('content')
<div class="container-fluid">
    <div class="row g-4">
        <div class="col-lg-8 mx-auto">
            <div class="card bg-white shadow-sm border-0 rounded-3">
                <div class="card-header bg-white border-bottom-0">
                    <h5 class="mb-0">Create New User</h5>
                </div>
                <div class="card-body">
                    <!-- Display all validation errors at top -->
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Please fix the following errors:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.users.store') }}" method="POST">
                        @csrf
                        
                        <!-- Hidden role field -->
                        <input type="hidden" name="role" value="investigator">

                        <div class="mb-3">
                            <label for="name" class="form-label fw-medium">Full Name</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" value="{{ old('name') }}" required placeholder="Enter full name">
                            @error('name') 
                                <div class="invalid-feedback">{{ $message }}</div> 
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-medium">Email Address</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" value="{{ old('email') }}" required placeholder="Enter email address">
                            @error('email') 
                                <div class="invalid-feedback">{{ $message }}</div> 
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-medium">Password</label>
                            <div class="position-relative">
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" required placeholder="Enter password (min 6 characters)">
                                <i class="fas fa-eye position-absolute" style="right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #6c757d;" 
                                   onclick="togglePassword('password', this)"></i>
                            </div>
                            @error('password') 
                                <div class="invalid-feedback d-block">{{ $message }}</div> 
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label fw-medium">Confirm Password</label>
                            <div class="position-relative">
                                <input type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" 
                                       id="password_confirmation" required placeholder="Confirm your password">
                                <i class="fas fa-eye position-absolute" style="right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #6c757d;" 
                                   onclick="togglePassword('password_confirmation', this)"></i>
                            </div>
                            @error('password_confirmation') 
                                <div class="invalid-feedback d-block">{{ $message }}</div> 
                            @enderror
                        </div>

                        <div class="text-end">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-light">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4">Create User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function togglePassword(fieldId, iconElement) {
        const field = document.getElementById(fieldId);
        if (field.type === "password") {
            field.type = "text";
            iconElement.classList.remove('fa-eye');
            iconElement.classList.add('fa-eye-slash');
        } else {
            field.type = "password";
            iconElement.classList.remove('fa-eye-slash');
            iconElement.classList.add('fa-eye');
        }
    }
</script>
@endpush