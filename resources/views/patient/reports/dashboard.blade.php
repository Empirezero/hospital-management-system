@include('patient.header')
@include('patient.sidebar')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>My Health Summary</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                <div class="breadcrumb-item">Reports</div>
            </div>
        </div>
        <div class="section-body">

            {{-- Stats --}}
            <div class="row">
                <div class="col-md-3">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-primary"><i class="fas fa-calendar-check"></i></div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Total Appointments</h4>
                            </div>
                            <div class="card-body">{{ $stats['total_appointments'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-success"><i class="fas fa-check-circle"></i></div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Completed</h4>
                            </div>
                            <div class="card-body">{{ $stats['completed'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-info"><i class="fas fa-flask"></i></div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Lab Results</h4>
                            </div>
                            <div class="card-body">{{ $stats['lab_results'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-warning"><i class="fas fa-prescription-bottle-alt"></i></div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Prescriptions</h4>
                            </div>
                            <div class="card-body">{{ $stats['prescriptions'] }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Charts --}}
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h4>My Appointments This Year</h4>
                        </div>
                        <div class="card-body">
                            <canvas id="appointmentsChart" height="120"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h4>Appointment Status</h4>
                        </div>
                        <div class="card-body">
                            <canvas id="appointmentStatusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- Recent Appointments --}}
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4>Recent Appointments</h4>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Doctor</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentAppointments as $apt)
                                    <tr>
                                        <td>Dr. {{ $apt->doctor?->name ?? 'N/A' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($apt->scheduled_at)->format('d M Y') }}</td>
                                        <td>
                                            <span class="badge
                                                {{ $apt->status == 'pending'   ? 'badge-warning' : '' }}
                                                {{ $apt->status == 'confirmed' ? 'badge-success' : '' }}
                                                {{ $apt->status == 'completed' ? 'badge-info'    : '' }}
                                                {{ $apt->status == 'cancelled' ? 'badge-danger'  : '' }}">
                                                {{ ucfirst($apt->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">No appointments yet.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Recent Lab Results --}}
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4>Recent Lab Results</h4>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Test</th>
                                        <th>Doctor</th>
                                        <th>Released</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentLabResults as $req)
                                    <tr>
                                        <td>{{ $req->labTest?->name }}</td>
                                        <td>Dr. {{ $req->doctor?->name ?? 'N/A' }}</td>
                                        <td>{{ $req->released_at?->format('d M Y') }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">No lab results yet.</td>
                                    </tr>
                                    @endforelse
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
$monthlyAppointmentsJson = json_encode($monthlyAppointments);
$apptStatusLabelsJson    = json_encode(array_keys($appointmentStatuses));
$apptStatusValuesJson    = json_encode(array_values($appointmentStatuses));
?>
<script>
    var monthlyAppointments = <?php echo $monthlyAppointmentsJson; ?>;
    var apptStatusLabels = <?php echo $apptStatusLabelsJson; ?>;
    var apptStatusValues = <?php echo $apptStatusValuesJson; ?>;

    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    new Chart(document.getElementById('appointmentsChart'), {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'Appointments',
                data: monthlyAppointments,
                borderColor: '#6777ef',
                backgroundColor: 'rgba(103,119,239,0.1)',
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

    new Chart(document.getElementById('appointmentStatusChart'), {
        type: 'doughnut',
        data: {
            labels: apptStatusLabels,
            datasets: [{
                data: apptStatusValues,
                backgroundColor: ['#ffc107', '#28a745', '#17a2b8', '#dc3545', '#6c757d']
            }]
        },
        options: {
            responsive: true
        }
    });
</script>

@include('patient.footer')