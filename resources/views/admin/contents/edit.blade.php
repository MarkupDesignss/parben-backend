@extends('admin.layouts.main')

@section('content')
<div class="container-fluid px-4">

    {{-- Header: Title + Back Button --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 fw-bold">Edit Content</h2>
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
    <form action="{{ route('admin.contents.update', $content) }}"
          method="POST"
          enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Type --}}
        <div class="mb-3">
            <label class="form-label fw-semibold">Type <span class="text-danger">*</span></label>
            <select name="type" class="form-select" required>
                @foreach($types as $key => $label)
                    <option value="{{ $key }}" @selected(old('type', $content->type) === $key)>
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
                   value="{{ old('title', $content->title) }}"
                   class="form-control">
        </div>

        {{-- Text --}}
        <div class="mb-3">
            <label class="form-label fw-semibold">Text</label>
            <textarea name="text"
                      rows="5"
                      class="form-control">{{ old('text', $content->text) }}</textarea>
        </div>

        {{-- Image --}}
        <div class="mb-3">
            <label class="form-label fw-semibold">Image</label>
            <input type="file"
                   name="image"
                   accept="image/*"
                   class="form-control">
            <small class="text-muted">Recommended size: 1200x630px</small>

            @if($content->image)
                <div class="mt-2">
                    <img src="{{ asset('storage/'.$content->image) }}"
                         class="rounded border"
                         style="height: 100px;">
                </div>
            @endif
        </div>

        {{-- Buttons --}}
        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-warning text-white fw-semibold px-4">
                Update
            </button>
            <a href="{{ route('admin.contents.index') }}" class="btn btn-outline-secondary px-4">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection