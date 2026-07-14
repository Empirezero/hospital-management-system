

<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Dashboard</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active">Dashboard</div>
            </div>
        </div>

        <div class="section-body">
            @if(session('message'))
            <div class="alert alert-success">{{ session('message') }}</div>
            @endif

            <div class="row">
                <div class="col-12 col-sm-6 col-md-6">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-primary">
                            <i class="fas fa-user-injured"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Total Patients</h4>
                            </div>
                            <div class="card-body">
                                {{ $totalPatients }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-6">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-success">
                            <i class="fas fa-calendar-plus"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Registered Today</h4>
                            </div>
                            <div class="card-body">
                                {{ $registeredToday }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4>Quick Actions</h4>
                </div>
                <div class="card-body">
                    <a href="{{ route('receptionist.add_patient') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Register New Patient
                    </a>
                    <a href="{{ route('receptionist.index') }}" class="btn btn-secondary">
                        <i class="fas fa-list"></i> View All Patients
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>
