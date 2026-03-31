@include('admin.header')
<!-- Sidebar -->
@include('admin.menusidebar')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Update Doctor Information</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="#">Doctors</a></div>
                <div class="breadcrumb-item">Update Doctor</div>
            </div>
        </div>

        <div class="section-body">
            <h2 class="section-title">Update Doctor Information</h2>
            <p class="section-lead">Edit the details of the selected doctor below.</p>

            <!-- Adjusted form container -->
            <div class="col-12 col-md-8 col-lg-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h4>Edit Doctor</h4>
                    </div>
                    <div class="card-body">
                        @if(session()->has('message'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session()->get('message') }}
                            <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <script>
                            setTimeout(function() {
                                document.querySelector('.alert').style.display = 'none';
                            }, 5000); // Hide after 5 seconds
                        </script>
                        @endif

                        <form method="POST" action="{{ url('edit_doctor', $data->id) }}" enctype="multipart/form-data">
                            @csrf


                            <div class="form-group">
                                <label>Doctor Name</label>
                                <input type="text" value="{{ $data->name }}" class="form-control" name="name" required>
                            </div>

                            <div class="form-group">
                                <label>Phone Number</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fas fa-phone"></i>
                                        </div>
                                    </div>
                                    <input type="text" value="{{ $data->number }}" class="form-control phone-number" name="number" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Doctor Speciality</label>
                                <select name="speciality" class="form-control" required>
                                    <option value="skin" {{ $data->speciality == 'skin' ? 'selected' : '' }}>Skin</option>
                                    <option value="heart" {{ $data->speciality == 'heart' ? 'selected' : '' }}>Heart</option>
                                    <option value="eye" {{ $data->speciality == 'eye' ? 'selected' : '' }}>Eye</option>
                                    <option value="nose" {{ $data->speciality == 'nose' ? 'selected' : '' }}>Nose</option>
                                    <option value="dentist" {{ $data->speciality == 'dentist' ? 'selected' : '' }}>Dentist</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Doctor Room No</label>
                                <input type="text" value="{{ $data->room }}" class="form-control" name="room" required>
                            </div>

                            <div class="form-group">
                                <label>Doctor Location</label>
                                <input type="text" value="{{ $data->location }}" class="form-control" name="location" required>
                            </div>

                            <div class="form-group">
                                <label>Doctor Image</label>
                                <input type="file" class="form-control" name="file">
                                <!-- Show current image -->
                                @if($data->image)
                                <img src="{{ url('doctorimage/'.$data->image) }}" alt="Doctor Image" style="height: 100px; width: 100px; border-radius: 50%; margin-top: 10px;">
                                @endif
                            </div>

                            <div class="card-footer text-right">
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@include('admin.footer')