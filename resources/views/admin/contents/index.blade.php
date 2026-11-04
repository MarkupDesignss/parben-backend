@extends('admin.layouts.main')

@section('content')
<div class="container-fluid px-4">
    {{-- Card Wrapper --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-4">

            {{-- Header: Title + Add Button --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0 fw-semibold">Contents</h4>
                <a href="{{ route('admin.contents.create') }}" class="btn btn-warning text-white fw-semibold d-flex align-items-center gap-1 px-3 py-2">
                    <i class="bi bi-plus-circle"></i>
                    <span>Add Content</span>
                </a>
            </div>

            {{-- Success Message --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr style="background-color: #0e7c7b; color: #fff;">
                            <th class="py-3 px-3 bg-tile text-white" style="border-top-left-radius: 8px;">Sr.No.</th>
                            <th class="py-3 px-3  bg-tile text-white">Type</th>
                            <th class="py-3 px-3  bg-tile text-white">Title</th>
                            <!--<th class="py-3 px-3  bg-tile text-white">Text</th>-->
                            <th class="py-3 px-3  bg-tile text-white">Image</th>
                            <th class="py-3 px-3  bg-tile text-white" style="border-top-right-radius: 8px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contents as $index => $content)
                            <tr style="background-color: #f1f1f1;">
                                <td class="px-3 py-3">{{ $index + 1 }}</td>
                                <td class="px-3 py-3 text-capitalize">{{ $content->type }}</td>
                                <td class="px-3 py-3">{{ $content->title ?? '—' }}</td>
                                <!--<td class="px-3 py-3">{{ \Illuminate\Support\Str::limit($content->text, 40) ?? '—' }}</td>-->
                                <td class="px-3 py-3">
                                    @if($content->image)
                                        <img src="{{ asset('storage/'.$content->image) }}"
                                             class="rounded"
                                             style="height: 45px; width: 45px; object-fit: cover;">
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="px-3 py-3">
                                    <a href="{{ route('admin.contents.edit', $content) }}"
                                       class="btn btn-sm btn-outline-warning"
                                       title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    No contents found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer: Total Results + Pagination --}}
            <div class="d-flex justify-content-between align-items-center mt-4">
                <small class="text-muted">Total: {{ $contents->total() }} results</small>
                <div>
                    {{ $contents->links() }}
                </div>
            </div>

        </div>
    </div>
</div>
@endsection