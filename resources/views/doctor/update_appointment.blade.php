    @include('doctor.header')
    <!-- Sidebar -->
    @include('doctor.sidebar')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Edit Appointment</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="#">Appointments</a></div>
                    <div class="breadcrumb-item">Edit Appointment</div>
                </div>
            </div>

            <div class="section-body">
                <h2 class="section-title">Edit Appointment</h2>

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

                <form action="{{ url('update_appointment', $appointment->id) }}" method="POST">
                    @csrf
                    <div class="row">
                        <!-- Patient Name -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Patient Name</label>
                                <input type="text" name="name" class="form-control" value="{{ $appointment->name }}" required>
                            </div>
                        </div>

                        <!-- Patient Phone -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="number">Patient Phone</label>
                                <input type="text" name="number" class="form-control" value="{{ $appointment->number }}" required>
                            </div>
                        </div>

                        <!-- Patient Email -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Patient Email</label>
                                <input type="email" name="email" class="form-control" value="{{ $appointment->email }}" required>
                            </div>
                        </div>

                        <!-- Doctor Name -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="doctor">Doctor Name</label>
                                <input type="text" name="doctor" class="form-control" value="{{ $appointment->doctor }}" required>
                            </div>
                        </div>

                        <!-- Date -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date">Appointment Date</label>
                                <input type="date" name="date" class="form-control" value="{{ $appointment->date }}" required>
                            </div>
                        </div>

                        <!-- Message -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="message">Message</label>
                                <textarea name="message" class="form-control" required>{{ $appointment->message }}</textarea>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select id="status" name="status" class="form-control">
                                    <option value="Pending" {{ ( $appointment->status) == 'Pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="Approved" {{( $appointment->status) == 'Approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="Cancelled" {{ ($appointment->status) == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Update Appointment</button>
                    </div>
                </form>
            </div>
        </section>
    </div>

    @include('doctor.footer')