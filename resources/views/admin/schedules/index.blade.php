@include('admin.header')
@include('admin.menusidebar')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Doctor Schedules</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ url('index') }}">Dashboard</a></div>
                <div class="breadcrumb-item">Schedules</div>
            </div>
        </div>

        <div class="section-body">

            @if(session('message'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('message') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h4>All Doctor Schedules</h4>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Doctor</th>
                                    <th>Speciality</th>
                                    <th>Working Days</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($doctors as $doctor)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($doctor->image)
                                            <img src="{{ asset('doctorimage/' . $doctor->image) }}"
                                                class="rounded-circle mr-2"
                                                style="height:35px; width:35px; object-fit:cover;">
                                            @endif
                                            {{ $doctor->name }}
                                        </div>
                                    </td>
                                    <td>{{ $doctor->speciality }}</td>
                                    <td>
                                        @forelse($doctor->schedules->where('is_active', true) as $schedule)
                                        <span class="badge badge-success mr-1">
                                            {{ ucfirst($schedule->day) }}
                                            {{ \Carbon\Carbon::createFromTimeString($schedule->start_time)->format('g:i A') }}
                                            -
                                            {{ \Carbon\Carbon::createFromTimeString($schedule->end_time)->format('g:i A') }}
                                        </span>
                                        @empty
                                        <span class="text-muted">No schedule set</span>
                                        @endforelse
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.schedules.manage', $doctor->id) }}"
                                            class="btn btn-primary btn-sm">
                                            <i class="fas fa-calendar-alt"></i> Manage Schedule
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No doctors found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@include('admin.footer')