@extends('admin.layouts.main')

@section('content')
<style>
    .image-wrapper {
        position: relative;
        display: inline-block;
        line-height: 0;
    }

    .image-wrapper img {
        display: block;
        border-radius: 6px;
        border: 1px solid #dee2e6;
    }

    .image-remove-btn {
        position: absolute;
        top: -8px;
        right: -8px;
        width: 26px;
        height: 26px;
        padding: 0;
        border-radius: 50%;
        background: #dc3545;
        color: #fff;
        border: 2px solid #fff;
        font-size: 16px;
        font-weight: bold;
        line-height: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 1px 4px rgba(0,0,0,0.3);
        z-index: 10;
        transition: transform 0.15s ease;
    }

    .image-remove-btn:hover {
        background: #b02a37;
        transform: scale(1.1);
    }
</style>

<div class="container py-5">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>{{ isset($page) ? 'Edit' : 'Create' }} Page</h4>
        <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary">Back</a>
    </div>

    <form action="{{ isset($page) ? route('admin.pages.update', $page) : route('admin.pages.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if(isset($page)) @method('PUT') @endif

        <div class="mb-3">
            <label for="title" class="form-label fw-semibold">Title</label>
            <input type="text" id="title" name="title" value="{{ old('title', $page->title ?? '') }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="slug" class="form-label fw-semibold">Slug</label>
            <input type="text" id="slug" name="slug" value="{{ old('slug', $page->slug ?? '') }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="content" class="form-label fw-semibold">Content</label>
            <textarea name="content" id="editor">{{ old('content', $page->content ?? '') }}</textarea>
        </div>

        <!-- Main Image Field -->
        <div class="mb-4">
            <label class="form-label fw-semibold">Main Page Image</label><br>
            @if (!empty($page->image))
                <div class="image-wrapper mb-2" id="current-image-container">
                    <img src="{{ asset('storage/' . $page->image) }}" alt="Page Image" width="150">
                    <button type="button" class="image-remove-btn" id="remove-image-btn" title="Remove image">&times;</button>
                </div>
                <input type="hidden" name="remove_image" id="remove_image" value="0">
            @endif
            <input type="file" name="image" class="form-control mt-2">
            <small class="text-muted">Recommended size: 1200x630px</small>
        </div>

        <!-- Banner Image Field -->
        <div class="mb-4">
            <label class="form-label fw-semibold">Description Image</label><br>
            @if (!empty($page->desc_image))
                <div class="image-wrapper mb-2" id="current-desc-image-container">
                    <img src="{{ asset('storage/' . $page->desc_image) }}" alt="Banner Image" width="150">
                    <button type="button" class="image-remove-btn" id="remove-desc-image-btn" title="Remove image">&times;</button>
                </div>
                <input type="hidden" name="remove_desc_image" id="remove_desc_image" value="0">
            @endif
            <input type="file" name="desc_image" class="form-control mt-2">
            <small class="text-muted">Recommended size: 1920x500px</small>
        </div>

        <!-- Partner Cards Section -->
        @if(isset($page) && !empty($page->partner_cards))
            <div class="mb-4">
                <label class="form-label fw-semibold">Partner Cards</label>
                <div id="partner-cards-container">
                    @foreach($page->partner_cards as $index => $card)
                        <div class="card p-3 mb-3 partner-card" data-index="{{ $index }}">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-2">
                                        <label class="form-label">Card Image</label>
                                        @if(!empty($card['image']))
                                            <div class="image-wrapper mb-2" id="card-image-container-{{ $index }}">
                                                <img src="{{ asset('storage/' . $card['image']) }}" alt="Card Image" width="100">
                                                <button type="button"
                                                        class="image-remove-btn remove-card-image-btn"
                                                        data-index="{{ $index }}"
                                                        title="Remove image">&times;</button>
                                            </div>
                                            <input type="hidden" name="remove_partner_card_images[]" id="remove_partner_card_image_{{ $index }}" value="{{ $index }}" disabled>
                                        @endif
                                        <input type="file" name="partner_cards[{{ $index }}][image]" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-2">
                                        <label class="form-label">Card Title</label>
                                        <input type="text" name="partner_cards[{{ $index }}][title]" value="{{ $card['title'] ?? '' }}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-2">
                                        <label class="form-label">Card Description</label>
                                        <textarea name="partner_cards[{{ $index }}][description]" class="form-control" rows="2">{{ $card['description'] ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-success">Save</button>
            <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // CKEditor
        try {
            ClassicEditor
                .create(document.querySelector('#editor'), {
                    toolbar: {
                        items: [
                            'heading', '|',
                            'bold', 'italic', 'link',
                            'bulletedList', 'numberedList', '|',
                            'outdent', 'indent', '|',
                            'imageUpload', 'blockQuote',
                            'insertTable', 'mediaEmbed',
                            'undo', 'redo'
                        ]
                    },
                    language: 'en',
                    image: {
                        toolbar: ['imageTextAlternative', 'imageStyle:full', 'imageStyle:side']
                    },
                    table: {
                        contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells']
                    }
                })
                .catch(error => { console.error(error); });
        } catch(e) { console.error(e); }

        // Slug
        const titleInput = document.getElementById('title');
        const slugInput = document.getElementById('slug');
        if (titleInput && slugInput) {
            titleInput.addEventListener('input', function() {
                slugInput.value = this.value
                    .toLowerCase().trim()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-');
            });
        }

        // Remove handlers (event delegation)
        document.addEventListener('click', function(e) {
            // Main image
            if (e.target.closest('#remove-image-btn')) {
                e.preventDefault();
                const c = document.getElementById('current-image-container');
                const i = document.getElementById('remove_image');
                if (c) c.remove();
                if (i) i.value = '1';
            }

            // Desc image
            if (e.target.closest('#remove-desc-image-btn')) {
                e.preventDefault();
                const c = document.getElementById('current-desc-image-container');
                const i = document.getElementById('remove_desc_image');
                if (c) c.remove();
                if (i) i.value = '1';
            }

            // Partner card image
            const cardBtn = e.target.closest('.remove-card-image-btn');
            if (cardBtn) {
                e.preventDefault();
                const index = cardBtn.getAttribute('data-index');
                const c = document.getElementById('card-image-container-' + index);
                const i = document.getElementById('remove_partner_card_image_' + index);
                if (c) c.remove();
                if (i) { i.disabled = false; i.value = index; }
            }
        });
    });
</script>
@endpush