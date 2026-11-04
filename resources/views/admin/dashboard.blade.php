@extends('admin.layouts.main')

@section('content')
<div class="container-fluid">
    <div class="row g-4">
        {{-- Stats Cards --}}
        <div class="col-lg-4">
            <div class="bg-purple text-dark border-0 rounded-3 p-3">
                <h4 class="text20 mb-3 fw-semibold">Total Logs</h4>
                <ul>
                    <li class="list-group-item p-0 d-flex justify-content-between align-items-center">
                        <div class="me-auto">{{ $totalLogs }}</div>
                        <span><img src="{{ asset('assets/images/arrow-up.svg') }}" alt="" /></span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="bg-light-blue text-dark border-0 rounded-3 p-3">
                <h4 class="text20 mb-3 fw-semibold">Unique Routers</h4>
                <ul>
                    <li class="list-group-item p-0 d-flex justify-content-between align-items-center">
                        <div class="me-auto">{{ $totalUniqueRouters }}</div>
                        <span><img src="{{ asset('assets/images/arrow-up.svg') }}" alt="" /></span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="bg-success bg-opacity-10 text-dark border-0 rounded-3 p-3">
                <h4 class="text20 mb-3 fw-semibold">Unique Cases</h4>
                <ul>
                    <li class="list-group-item p-0 d-flex justify-content-between align-items-center">
                        <div class="me-auto">{{ $totalUniqueCases }}</div>
                        <span><img src="{{ asset('assets/images/arrow-up.svg') }}" alt="" /></span>
                    </li>
                </ul>
            </div>
        </div>
        <!--<div class="col-lg-3">-->
        <!--    <div class="bg-warning bg-opacity-10 text-dark border-0 rounded-3 p-3">-->
        <!--        <h4 class="text20 mb-3 fw-semibold">Total Contacts</h4>-->
        <!--        <ul>-->
        <!--            <li class="list-group-item p-0 d-flex justify-content-between align-items-center">-->
        <!--                <div class="me-auto">{{ $totalContacts }}</div>-->
        <!--                <span><img src="{{ asset('assets/images/arrow-up.svg') }}" alt="" /></span>-->
        <!--            </li>-->
        <!--        </ul>-->
        <!--    </div>-->
        <!--</div>-->
    </div>

    {{-- Recent Logs Table with PDF Download --}}
    <div class="row g-4 mt-2">
        <div class="col-lg-12">
            <div class="card bg-white border-0 shadow-sm rounded-3 p-2">
                <div class="card-body pb-0">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Recent Router Logs</h5>
                        <a href="{{ route('admin.cases.index') }}" class="btn btn-sm btn-primary">
                            View All Logs
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th class="p-3 bg-tile text-white text-center">Sr.No.</th>
                                    <th class="p-3 bg-tile text-white text-center">Case ID</th>
                                    <th class="p-3 bg-tile text-white text-center">Router ID</th>
                                    <th class="p-3 bg-tile text-white text-center">Router Model</th>
                                    <th class="p-3 bg-tile text-white text-center">Investigator</th>
                                    <th class="p-3 bg-tile text-white text-center">Log Time</th>
                                    <th class="p-3 bg-tile text-white text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentLogs as $index => $log)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td class="text-center">{{ $log->case_id ?? 'N/A' }}</td>
                                    <td class="text-center">{{ $log->router_id }}</td>
                                    <td class="text-center">{{ $log->router_model ?? 'N/A' }}</td>
                                    <td class="text-center">{{ $log->user->name ?? 'N/A' }}</td>
                                    <td class="text-center">
                                        {{ $log->log_generated_at ? \Carbon\Carbon::parse($log->log_generated_at)->format('H:i:s d/m/Y') : 'N/A' }}
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            {{-- PDF Download with Hash --}}
                                            <a href="{{ route('admin.logs.export-pdf', $log->id) }}" 
                                               class="btn btn-sm btn-success" 
                                               title="Download PDF with Hash">
                                                <i class="bi bi-file-pdf"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">No router logs found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart for logs per day --}}
    @if(isset($logsPerDay) && $logsPerDay->count())
    <div class="row g-4 mt-2">
        <div class="col-lg-12">
            <div class="card bg-white border-0 shadow-sm rounded-3 p-3">
                <h5 class="mb-3">Logs per Day (Last 7 Days)</h5>
                <canvas id="logsChart" height="150"></canvas>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('logsChart').getContext('2d');
    const logsData = @json($logsPerDay);
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: logsData.map(item => item.date),
            datasets: [{
                label: 'Number of Logs',
                data: logsData.map(item => item.total),
                borderColor: '#6f42c1',
                backgroundColor: 'rgba(111, 66, 193, 0.1)',
                tension: 0.1,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
</script>
@endpush

@stop