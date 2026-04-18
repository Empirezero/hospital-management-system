@include('lab.header')
@include('lab.menusidebar')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Lab Reports</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('lab.home') }}">Dashboard</a></div>
                <div class="breadcrumb-item">Reports</div>
            </div>
        </div>
        <div class="section-body">

            {{-- Stats --}}
            <div class="row">
                <div class="col-md-3">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-primary"><i class="fas fa-flask"></i></div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Total Requests</h4>
                            </div>
                            <div class="card-body">{{ $stats['total_requests'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-warning"><i class="fas fa-clock"></i></div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Pending</h4>
                            </div>
                            <div class="card-body">{{ $stats['pending'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-success"><i class="fas fa-check-circle"></i></div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Completed Today</h4>
                            </div>
                            <div class="card-body">{{ $stats['completed_today'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-info"><i class="fas fa-share"></i></div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Released to Patients</h4>
                            </div>
                            <div class="card-body">{{ $stats['released'] }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Charts --}}
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h4>Lab Requests This Year</h4>
                        </div>
                        <div class="card-body">
                            <canvas id="requestsChart" height="120"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h4>Request Status</h4>
                        </div>
                        <div class="card-body">
                            <canvas id="requestStatusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4>Top Requested Tests</h4>
                        </div>
                        <div class="card-body">
                            <canvas id="topTestsChart" height="150"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4>Recent Requests</h4>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Patient</th>
                                        <th>Test</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentRequests as $req)
                                    <tr>
                                        <td>{{ $req->patient_name }}</td>
                                        <td>{{ $req->labTest?->name }}</td>
                                        <td>
                                            <span class="badge
                                                {{ $req->status == 'requested'   ? 'badge-warning' : '' }}
                                                {{ $req->status == 'in_progress' ? 'badge-info'    : '' }}
                                                {{ $req->status == 'completed'   ? 'badge-success' : '' }}">
                                                {{ ucfirst(str_replace('_', ' ', $req->status)) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<?php
$monthlyRequestsJson     = json_encode($monthlyRequests);
$requestStatusLabelsJson = json_encode(array_keys($requestStatuses));
$requestStatusValuesJson = json_encode(array_values($requestStatuses));
$topTestNamesJson        = json_encode($topTests->pluck('name'));
$topTestCountsJson       = json_encode($topTests->pluck('requests_count'));
?>
<script>
    var monthlyRequests = <?php echo $monthlyRequestsJson; ?>;
    var requestStatusLabels = <?php echo $requestStatusLabelsJson; ?>;
    var requestStatusValues = <?php echo $requestStatusValuesJson; ?>;
    var topTestNames = <?php echo $topTestNamesJson; ?>;
    var topTestCounts = <?php echo $topTestCountsJson; ?>;

    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    new Chart(document.getElementById('requestsChart'), {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'Lab Requests',
                data: monthlyRequests,
                borderColor: '#17a2b8',
                backgroundColor: 'rgba(23,162,184,0.1)',
                fill: true,
                tension: 0.4
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

    new Chart(document.getElementById('requestStatusChart'), {
        type: 'doughnut',
        data: {
            labels: requestStatusLabels,
            datasets: [{
                data: requestStatusValues,
                backgroundColor: ['#ffc107', '#17a2b8', '#28a745', '#dc3545']
            }]
        },
        options: {
            responsive: true
        }
    });

    new Chart(document.getElementById('topTestsChart'), {
        type: 'bar',
        data: {
            labels: topTestNames,
            datasets: [{
                label: 'Requests',
                data: topTestCounts,
                backgroundColor: '#17a2b8'
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
</script>

@include('lab.footer')