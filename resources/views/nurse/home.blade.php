@include('nurse.header')
@include('nurse.sidebar')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Nurse Dashboard</h1>
        </div>
        <div class="section-body">

            {{-- Stats --}}
            <div class="row">
                <div class="col-md-2">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-primary"><i class="fas fa-procedures"></i></div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Current Admissions</h4>
                            </div>
                            <div class="card-body">{{ $stats['total_admissions'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-success"><i class="fas fa-sign-in-alt"></i></div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Admitted Today</h4>
                            </div>
                            <div class="card-body">{{ $stats['admissions_today'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-warning"><i class="fas fa-sign-out-alt"></i></div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Discharged Today</h4>
                            </div>
                            <div class="card-body">{{ $stats['discharges_today'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-info"><i class="fas fa-bed"></i></div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Available Beds</h4>
                            </div>
                            <div class="card-body">{{ $stats['available_beds'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-danger"><i class="fas fa-bed"></i></div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Occupied Beds</h4>
                            </div>
                            <div class="card-body">{{ $stats['occupied_beds'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-secondary"><i class="fas fa-calendar-check"></i></div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Pending Appointments</h4>
                            </div>
                            <div class="card-body">{{ $stats['pending_appointments'] }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- Current Admissions --}}
                <div class="col-md-7">
                    <div class="card">
                        <div class="card-header">
                            <h4>Current Admissions</h4>
                            <div class="card-header-action">
                                <a href="{{ route('nurse.admissions') }}" class="btn btn-primary btn-sm">
                                    View All
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th>Patient</th>
                                            <th>Ward</th>
                                            <th>Bed</th>
                                            <th>Doctor</th>
                                            <th>Since</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentAdmissions as $admission)
                                        <tr>
                                            <td><strong>{{ $admission->patient_name }}</strong></td>
                                            <td>{{ $admission->ward?->name }}</td>
                                            <td>{{ $admission->bed?->bed_number }}</td>
                                            <td>Dr. {{ $admission->doctor?->name ?? 'N/A' }}</td>
                                            <td>{{ $admission->admitted_at->diffForHumans() }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-3">
                                                No current admissions.
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Ward Bed Summary --}}
                <div class="col-md-5">
                    <div class="card">
                        <div class="card-header">
                            <h4>Ward Summary</h4>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th>Ward</th>
                                            <th>Type</th>
                                            <th>Available</th>
                                            <th>Occupied</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($wards as $ward)
                                        <tr>
                                            <td>{{ $ward->name }}</td>
                                            <td>
                                                <span class="badge badge-{{
                                                    $ward->type == 'icu'       ? 'danger'  :
                                                    ($ward->type == 'emergency' ? 'warning' :
                                                    ($ward->type == 'private'   ? 'info'    : 'primary'))
                                                }}">{{ ucfirst($ward->type) }}</span>
                                            </td>
                                            <td class="text-success">
                                                {{ $ward->beds->where('status', 'available')->count() }}
                                            </td>
                                            <td class="text-danger">
                                                {{ $ward->beds->where('status', 'occupied')->count() }}
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

@include('nurse.footer')