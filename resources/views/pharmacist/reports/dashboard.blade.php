@include('pharmacist.header')
@include('pharmacist.sidebar')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Pharmacy Reports</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ url('pharmacist_index') }}">Dashboard</a></div>
                <div class="breadcrumb-item">Reports</div>
            </div>
        </div>
        <div class="section-body">

            <div class="row">
                <div class="col-md-3">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-primary"><i class="fas fa-pills"></i></div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Total Medicines</h4>
                            </div>
                            <div class="card-body">{{ $stats['total_medicines'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-danger"><i class="fas fa-exclamation-triangle"></i></div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Low Stock</h4>
                            </div>
                            <div class="card-body">{{ $stats['low_stock'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-warning"><i class="fas fa-calendar-times"></i></div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Expiring Soon</h4>
                            </div>
                            <div class="card-body">{{ $stats['expiring_soon'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-success"><i class="fas fa-prescription-bottle-alt"></i></div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Dispensed Today</h4>
                            </div>
                            <div class="card-body">{{ $stats['dispensed_today'] }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h4>Prescriptions This Year</h4>
                        </div>
                        <div class="card-body">
                            <canvas id="prescriptionsChart" height="120"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h4>Prescription Status</h4>
                        </div>
                        <div class="card-body">
                            <canvas id="prescriptionStatusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4>Top Prescribed Medicines</h4>
                        </div>
                        <div class="card-body">
                            <canvas id="topMedicinesChart" height="150"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4>Expiring Medicines</h4>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Medicine</th>
                                        <th>Stock</th>
                                        <th>Expires</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($expiringMedicines as $med)
                                    <tr>
                                        <td>{{ $med->name }}</td>
                                        <td>
                                            <span class="{{ $med->stock < 10 ? 'text-danger font-weight-bold' : '' }}">
                                                {{ $med->stock }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-warning">
                                                {{ \Carbon\Carbon::parse($med->expiry_date)->format('d M Y') }}
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
$monthlyPrescriptionsJson     = json_encode($monthlyPrescriptions);
$prescriptionStatusLabelsJson = json_encode(array_keys($prescriptionStatuses));
$prescriptionStatusValuesJson = json_encode(array_values($prescriptionStatuses));
$topMedicineNamesJson         = json_encode($topMedicines->pluck('name'));
$topMedicineCountsJson        = json_encode($topMedicines->pluck('prescriptions_count'));
?>
<script>
    var monthlyPrescriptions = <?php echo $monthlyPrescriptionsJson; ?>;
    var prescriptionStatusLabels = <?php echo $prescriptionStatusLabelsJson; ?>;
    var prescriptionStatusValues = <?php echo $prescriptionStatusValuesJson; ?>;
    var topMedicineNames = <?php echo $topMedicineNamesJson; ?>;
    var topMedicineCounts = <?php echo $topMedicineCountsJson; ?>;

    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    new Chart(document.getElementById('prescriptionsChart'), {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'Prescriptions',
                data: monthlyPrescriptions,
                borderColor: '#28a745',
                backgroundColor: 'rgba(40,167,69,0.1)',
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

    new Chart(document.getElementById('prescriptionStatusChart'), {
        type: 'doughnut',
        data: {
            labels: prescriptionStatusLabels,
            datasets: [{
                data: prescriptionStatusValues,
                backgroundColor: ['#ffc107', '#28a745', '#dc3545']
            }]
        },
        options: {
            responsive: true
        }
    });

    new Chart(document.getElementById('topMedicinesChart'), {
        type: 'bar',
        data: {
            labels: topMedicineNames,
            datasets: [{
                label: 'Times Prescribed',
                data: topMedicineCounts,
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

@include('pharmacist.footer')