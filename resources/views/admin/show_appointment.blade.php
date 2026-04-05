    @include('admin.header')
    <!-- Sidebar -->
    @include('admin.menusidebar')

    <!-- Main Content -->
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>My Appointments</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="#">Appointments</a></div>
                    <div class="breadcrumb-item">My Appointments</div>
                </div>
            </div>

            <div class="section-body">
                <h2 class="section-title">My Appointments</h2>
                <p class="section-lead">Below are your recent appointments.</p>

                <div class="row justify-content-center">
                    <div class="col-12 col-md-8 col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Appointment List</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive text-center">
                                    <table class="table table-bordered table-md">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Patient Name</th>
                                                <th>patient phone</th>
                                                <th>Patient Email</th>
                                                <th>Doctor Name</th>
                                                <th>Date</th>
                                                <th>Message</th>
                                                <th>Status</th>
                                                <th>Action</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($data as $index => $appointment)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $appointment->name}}</td>
                                                <td>{{ $appointment->number}}</td>
                                                <td>{{ $appointment->email}}</td>
                                                <td>{{ $appointment->doctor?->name ?? 'N/A' }}</td>
                                                <td>{{ $appointment->scheduled_at ? \Carbon\Carbon::parse($appointment->scheduled_at)->format('d M Y') : 'N/A' }}</td>
                                                <td>{{ $appointment->message }}</td>
                                                <td>
                                                    <span class="badge 
        @if($appointment->status == 'confirmed') badge-success
        @elseif($appointment->status == 'pending') badge-warning
        @elseif($appointment->status == 'cancelled') badge-danger
        @elseif($appointment->status == 'completed') badge-info
        @elseif($appointment->status == 'no_show') badge-secondary
        @endif">
                                                        {{ ucfirst($appointment->status) }}
                                                    </span>
                                                </td>

                                                <td>
                                                    <a href="#" class="btn btn-secondary btn-sm">
                                                        <i class="bi bi-eye"></i> View
                                                    </a>
                                                    <a href="{{url('update_appoint',$appointment->id)}}" class="btn btn-warning btn-sm">
                                                        <i class="bi bi-pencil"></i> Update
                                                    </a>
                                                    <a href="{{url('delete_appoint',$appointment->id)}}" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to Delete this appointment?')">
                                                        <i class="bi bi-trash"></i> Delete
                                                    </a>
                                                </td>

                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer text-right">
                                <nav class="d-inline-block">
                                    <ul class="pagination mb-0">
                                        <li class="page-item disabled">
                                            <a class="page-link" href="#" tabindex="-1"><i class="fas fa-chevron-left"></i></a>
                                        </li>
                                        <li class="page-item active"><a class="page-link" href="#">1 <span class="sr-only">(current)</span></a></li>
                                        <li class="page-item">
                                            <a class="page-link" href="#">2</a>
                                        </li>
                                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                                        <li class="page-item">
                                            <a class="page-link" href="#"><i class="fas fa-chevron-right"></i></a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>


    </div>
    @include('admin.footer')