@include('admin.header')
@include('admin.menusidebar')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Admin Reports</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ url('index') }}">Dashboard</a></div>
                <div class="breadcrumb-item">Reports</div>
            </div>
        </div>
        <div class="section-body">

            {{-- Stats Row --}}
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-primary"><i class="fas fa-user-md"></i></div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Doctors</h4>
                            </div>
                            <div class="card-body">{{ $stats['total_doctors'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-success"><i class="fas fa-users"></i></div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Patients</h4>
                            </div>
                            <div class="card-body">{{ $stats['total_patients'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-warning"><i class="fas fa-calendar-check"></i></div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Appointments</h4>
                            </div>
                            <div class="card-body">{{ $stats['total_appointments'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-danger"><i class="fas fa-flask"></i></div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Lab Requests</h4>
                            </div>
                            <div class="card-body">{{ $stats['total_lab_requests'] }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-info"><i class="fas fa-prescription-bottle-alt"></i></div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Prescriptions</h4>
                            </div>
                            <div class="card-body">{{ $stats['total_prescriptions'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-warning"><i class="fas fa-clock"></i></div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Pending Appointments</h4>
                            </div>
                            <div class="card-body">{{ $stats['pending_appointments'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-danger"><i class="fas fa-exclamation-triangle"></i></div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Low Stock</h4>
                            </div>
                            <div class="card-body">{{ $stats['low_stock_medicines'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-info"><i class="fas fa-vials"></i></div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Pending Lab</h4>
                            </div>
                            <div class="card-body">{{ $stats['pending_lab'] }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Charts Row 1 --}}
            <div class="row">
                {{-- Monthly Appointments Line Chart --}}
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h4>Appointments This Year</h4>
                        </div>
                        <div class="card-body">
                            <canvas id="appointmentsChart" height="120"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Appointment Status Pie --}}
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

            {{-- Charts Row 2 --}}
            <div class="row">
                {{-- Monthly Prescriptions Bar Chart --}}
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4>Prescriptions This Year</h4>
                        </div>
                        <div class="card-body">
                            <canvas id="prescriptionsChart" height="150"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Lab Requests Doughnut --}}
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header">
                            <h4>Lab Request Status</h4>
                        </div>
                        <div class="card-body">
                            <canvas id="labStatusChart"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Users by Role Pie --}}
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header">
                            <h4>Users by Role</h4>
                        </div>
                        <div class="card-body">
                            <canvas id="usersRoleChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Charts Row 3 --}}
            <div class="row">
                {{-- Top Doctors Bar Chart --}}
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4>Top Doctors by Appointments</h4>
                        </div>
                        <div class="card-body">
                            <canvas id="topDoctorsChart" height="150"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Recent Activity --}}
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4>Recent Appointments</h4>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th>Patient</th>
                                            <th>Doctor</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentAppointments as $apt)
                                        <tr>
                                            <td>{{ $apt->name }}</td>
                                            <td>{{ $apt->doctor?->name ?? 'N/A' }}</td>
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
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<?php
$monthlyAppointmentsJson  = json_encode($monthlyAppointments);
$monthlyPrescriptionsJson = json_encode($monthlyPrescriptions);
$apptStatusLabelsJson     = json_encode(array_keys($appointmentStatuses));
$apptStatusValuesJson     = json_encode(array_values($appointmentStatuses));
$labStatusLabelsJson      = json_encode(array_keys($labStatuses));
$labStatusValuesJson      = json_encode(array_values($labStatuses));
$userRoleLabelsJson       = json_encode(array_keys($usersByRole));
$userRoleValuesJson       = json_encode(array_values($usersByRole));
$topDoctorNamesJson       = json_encode($topDoctors->pluck('name'));
$topDoctorCountsJson      = json_encode($topDoctors->pluck('appointments_count'));
?>
<script>
    var monthlyAppointments = <?php echo $monthlyAppointmentsJson; ?>;
    var monthlyPrescriptions = <?php echo $monthlyPrescriptionsJson; ?>;
    var apptStatusLabels = <?php echo $apptStatusLabelsJson; ?>;
    var apptStatusValues = <?php echo $apptStatusValuesJson; ?>;
    var labStatusLabels = <?php echo $labStatusLabelsJson; ?>;
    var labStatusValues = <?php echo $labStatusValuesJson; ?>;
    var userRoleLabels = <?php echo $userRoleLabelsJson; ?>;
    var userRoleValues = <?php echo $userRoleValuesJson; ?>;
    var topDoctorNames = <?php echo $topDoctorNamesJson; ?>;
    var topDoctorCounts = <?php echo $topDoctorCountsJson; ?>;

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
        type: 'pie',
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

    new Chart(document.getElementById('prescriptionsChart'), {
        type: 'bar',
        data: {
            labels: months,
            datasets: [{
                label: 'Prescriptions',
                data: monthlyPrescriptions,
                backgroundColor: '#28a745'
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

    new Chart(document.getElementById('labStatusChart'), {
        type: 'doughnut',
        data: {
            labels: labStatusLabels,
            datasets: [{
                data: labStatusValues,
                backgroundColor: ['#ffc107', '#17a2b8', '#28a745', '#dc3545']
            }]
        },
        options: {
            responsive: true
        }
    });

    new Chart(document.getElementById('usersRoleChart'), {
        type: 'pie',
        data: {
            labels: userRoleLabels,
            datasets: [{
                data: userRoleValues,
                backgroundColor: ['#6777ef', '#28a745', '#ffc107', '#17a2b8', '#dc3545', '#fd7e14', '#6c757d']
            }]
        },
        options: {
            responsive: true
        }
    });

    new Chart(document.getElementById('topDoctorsChart'), {
        type: 'bar',
        data: {
            labels: topDoctorNames,
            datasets: [{
                label: 'Appointments',
                data: topDoctorCounts,
                backgroundColor: '#6777ef'
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

@include('admin.footer')