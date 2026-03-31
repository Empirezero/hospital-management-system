    @include('admin.header')
    <!-- Sidebar -->
    @include('admin.menusidebar')

    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Advanced Forms</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="#">Forms</a></div>
                    <div class="breadcrumb-item">Advanced Forms</div>
                </div>
            </div>

            <div class="section-body">
                <h2 class="section-title">Advanced Forms</h2>
                <p class="section-lead">We provide advanced input fields, such as date picker, color picker, and so on.</p>

                <!-- Adjusted form container -->
                <div class="col-12 col-md-8 col-lg-8 mx-auto">
                    <div class="card">
                        <div class="card-header">
                            <h4>Input Text</h4>
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

                            <form method="POST" action="{{('upload_doctor')}}" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label>Doctor Name</label>
                                    <input type="text" placeholder="Write doctor's name" class="form-control" name="name" required>
                                </div>
                                <div class="form-group">
                                    <label>Phone Number</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="fas fa-phone"></i>
                                            </div>
                                        </div>
                                        <input type="text" class="form-control phone-number" name="number" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Doctor Speciality</label>
                                    <select name="speciality" class="form-control" required>
                                        <option value="">Select Specialty</option>
                                        <option value="skin">Skin</option>
                                        <option value="heart">Heart</option>
                                        <option value="eye">Eye</option>
                                        <option value="nose">Nose</option>
                                        <option value="dentist">Dentist</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Doctor Room No</label>
                                    <input type="text" placeholder="Write room no" class="form-control" name="room" required>
                                </div>
                                <div class="form-group">
                                    <label>Doctor Location</label>
                                    <input type="text" placeholder="Write doctor's location" class="form-control" name="location" required>
                                </div>
                                <div class="form-group">
                                    <label>Doctor Image</label>
                                    <input type="file" class="form-control" name="file" required>
                                </div>

                                <div class="card-footer text-right">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    @include('admin.footer')