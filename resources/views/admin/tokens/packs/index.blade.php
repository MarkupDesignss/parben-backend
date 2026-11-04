@extends('admin.layouts.main')

@section('content')
<div class="container-fluid">
    <div class="row g-4">
        <div class="col-lg-12">
            <div class="card bg-white border-0 shadow-sm rounded-3 p-2">
                {{-- Header with Add Pack button --}}
                <div class="card-body pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Token Packs</h5>
                    <a href="{{ route('admin.tokens.packs.create') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-circle"></i> Add New Pack
                    </a>
                </div>

                {{-- Table --}}
                <div class="card-body pb-0">
                    <div class="table-responsive">
                        <table class="table table-hover" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th width="10%" class="p-3 bg-tile text-white fw-medium text-center" style="border-radius: 6px 0 0 6px;">
                                        Sr.No.
                                    </th>
                                    <th width="25%" class="p-3 bg-tile text-white fw-medium text-center">Name</th>
                                    <th width="15%" class="p-3 bg-tile text-white fw-medium text-center">Tokens</th>
                                    <th width="15%" class="p-3 bg-tile text-white fw-medium text-center">Price ($)</th>
                                    <th width="15%" class="p-3 bg-tile text-white fw-medium text-center">Status</th>
                                    <th width="20%" class="p-3 bg-tile text-white fw-medium text-center" style="border-radius: 0 6px 6px 0;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($packs as $index => $pack)
                                    <tr>
                                        <td class="text-center">{{ $packs->firstItem() + $index }}</td>
                                        <td class="text-center">{{ $pack->name }}</td>
                                        <td class="text-center">{{ $pack->token_count }}</td>
                                        <td class="text-center">${{ number_format($pack->price, 2) }}</td>
                                        <td class="text-center">
                                            @if($pack->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center align-items-center gap-1">
                                                <a href="{{ route('admin.tokens.packs.edit', $pack->id) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <!--<form action="{{ route('admin.tokens.packs.destroy', $pack->id) }}" method="POST" style="display:inline-block; margin:0;" onsubmit="return confirm('Delete this pack?')">-->
                                                <!--    @csrf @method('DELETE')-->
                                                <!--    <button class="btn btn-sm btn-outline-danger" title="Delete">-->
                                                <!--        <i class="bi bi-trash"></i>-->
                                                <!--    </button>-->
                                                <!--</form>-->
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">No token packs found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination with summary --}}
                    @if($packs->hasPages())
                        <div class="d-flex flex-wrap align-items-center justify-content-between mt-3">
                            <div class="text-muted small">
                                Showing {{ $packs->firstItem() }} to {{ $packs->lastItem() }} of {{ $packs->total() }} results
                            </div>
                            <div>
                                {{ $packs->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    @else
                        <div class="text-muted small mt-3">
                            Total: {{ $packs->total() }} results
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection