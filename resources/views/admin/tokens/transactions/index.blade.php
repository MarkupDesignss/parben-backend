@extends('admin.layouts.main')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Token Transactions</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    {{-- Filter Form --}}
                    <form method="GET" action="{{ route('admin.tokens.transactions.index') }}" class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label for="investigator_id" class="form-label">Investigator</label>
                            <select name="investigator_id" id="investigator_id" class="form-select">
                                <option value="">All</option>
                                @foreach($investigators as $user)
                                    <option value="{{ $user->id }}" {{ request('investigator_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="type" class="form-label">Type</label>
                            <select name="type" id="type" class="form-select">
                                <option value="">All</option>
                                <option value="purchase" {{ request('type') == 'purchase' ? 'selected' : '' }}>Purchase</option>
                                <option value="download" {{ request('type') == 'download' ? 'selected' : '' }}>Download</option>
                                <!--<option value="admin_adjustment" {{ request('type') == 'admin_adjustment' ? 'selected' : '' }}>Admin Adjustment</option>-->
                                <!--<option value="refund" {{ request('type') == 'refund' ? 'selected' : '' }}>Refund</option>-->
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">Filter</button>
                            <a href="{{ route('admin.tokens.transactions.index') }}" class="btn btn-secondary">Reset</a>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th class="bg-tile text-white p-3 text-center" style="border-radius: 6px 0 0 0;">ID</th>
                                    <th class="bg-tile text-white p-3 text-center">Investigator</th>
                                    <th class="bg-tile text-white p-3 text-center">Amount</th>
                                    <th class="bg-tile text-white p-3 text-center">Type</th>
                                    <th class="bg-tile text-white p-3 text-center">Pack</th>
                                    <th class="bg-tile text-white p-3 text-center">Description</th>
                                    <th class="bg-tile text-white p-3 text-center">Balance After</th>
                                    <th class="bg-tile text-white p-3 text-center" style="border-radius: 0 6px 0 0;">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $txn)
                                    <tr>
                                        <td class="text-center">{{ $txn->id }}</td>
                                        <td class="text-center">{{ $txn->investigator->name ?? 'N/A' }}</td>
                                        <td class="text-center">{{ $txn->amount }}</td>
                                        <td class="text-center">
                                            @php
                                                $badgeClass = match($txn->type) {
                                                    'purchase' => 'bg-success',
                                                    'download' => 'bg-danger',
                                                    'admin_adjustment' => 'bg-info',
                                                    'refund' => 'bg-warning',
                                                    default => 'bg-secondary'
                                                };
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">{{ ucfirst(str_replace('_', ' ', $txn->type)) }}</span>
                                        </td>
                                        <td class="text-center">{{ $txn->pack->name ?? '-' }}</td>
                                        <td class="text-center">{{ $txn->description ?? '-' }}</td>
                                        <td class="text-center">{{ $txn->balance_after }}</td>
                                        <td class="text-center">{{ $txn->created_at->format('Y-m-d H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">No transactions found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination Summary --}}
                    @if($transactions->hasPages())
                        <div class="d-flex flex-wrap align-items-center justify-content-between mt-3">
                            <div class="text-muted small">
                                Showing {{ $transactions->firstItem() }} to {{ $transactions->lastItem() }} of {{ $transactions->total() }} results
                            </div>
                            <div>
                                {{ $transactions->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    @else
                        <div class="text-muted small mt-3">
                            Total: {{ $transactions->total() }} results
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection