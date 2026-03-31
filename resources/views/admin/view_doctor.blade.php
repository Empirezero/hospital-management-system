@include('admin.header')
<!-- Sidebar -->
@include('admin.menusidebar')

<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Doctors</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                <div class="breadcrumb-item">Doctors</div>
            </div>
        </div>

        <div class="section-body">
            <h2 class="section-title">Doctors List</h2>
            <p class="section-lead">Manage your doctors below.</p>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Doctors Table</h4>
                            <div class="card-header-form">
                                <form>
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Search">
                                        <div class="input-group-btn">
                                            <button class="btn btn-primary"><i class="fas fa-search"></i></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Image</th>
                                            <th>Doctor Name</th>
                                            <th>Phone Number</th>
                                            <th>Speciality</th>
                                            <th>Room Number</th>
                                            <th>Location</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data as $doctor)
                                        <tr>
                                            <td>
                                                <img src="doctorimage/{{ $doctor->image }}" style="height: 100px; width: 100px; border-radius: 50%; margin: 10px 0;">
                                            </td>
                                            <td>{{ $doctor->name }}</td>
                                            <td>{{ $doctor->number }}</td>
                                            <td>{{ $doctor->speciality }}</td>
                                            <td>{{ $doctor->room }}</td>
                                            <td>{{ $doctor->location }}</td>

                                            <td>

                                                <a href="{{url('show_doctor',$doctor->id)}}" class="btn btn-warning btn-sm">
                                                    <i class="bi bi-pencil"></i> Update
                                                </a>
                                                <a href="{{url('delete_doctor',$doctor->id)}}" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to Delete this doctor?')">
                                                    <i class="bi bi-trash"></i> Delete
                                                </a>

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

@include('admin.footer')