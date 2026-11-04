@extends('admin.layouts.main')

@section('content')
<div class="container-fluid px-4">

    {{-- Header: Title + Back Button --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 fw-bold">Create Content</h2>
        <a href="{{ route('admin.contents.index') }}" class="btn btn-secondary">
            Back
        </a>
    </div>

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form --}}
    <form action="{{ route('admin.contents.store') }}"
          method="POST"
          enctype="multipart/form-data">
        @csrf

        {{-- Type --}}
        <div class="mb-3">
            <label class="form-label fw-semibold">Type <span class="text-danger">*</span></label>
            <select name="type" class="form-select" required>
                <option value="">-- Select type --</option>
                @foreach($types as $key => $label)
                    <option value="{{ $key }}" @selected(old('type') === $key)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Title --}}
        <div class="mb-3">
            <label class="form-label fw-semibold">Title</label>
            <input type="text"
                   name="title"
                   value="{{ old('title') }}"
                   class="form-control">
        </div>

        {{-- Text --}}
        <div class="mb-3">
            <label class="form-label fw-semibold">Text</label>
            <textarea name="text"
                      rows="5"
                      class="form-control">{{ old('text') }}</textarea>
        </div>

        {{-- Image --}}
        <div class="mb-3">
            <label class="form-label fw-semibold">Image (logo / banner / image)</label>
            <input type="file"
                   name="image"
                   accept="image/*"
                   class="form-control">
            <small class="text-muted">Recommended size: 1200x630px</small>
        </div>

        {{-- Buttons --}}
        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-warning text-white fw-semibold px-4">
                Save
            </button>
            <a href="{{ route('admin.contents.index') }}" class="btn btn-outline-secondary px-4">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection