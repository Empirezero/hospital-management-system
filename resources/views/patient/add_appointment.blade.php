@include('patient.header')
<!-- Sidebar -->
@include('patient.sidebar')

<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Book Appointment</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="#">Appointments</a></div>
                <div class="breadcrumb-item">Book Appointment</div>
            </div>
        </div>

        <div class="section-body">
            <h2 class="section-title text-center">Appointment Booking Form</h2>
            <p class="section-lead text-center">Please fill in the details below to book your appointment.</p>

            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h4>Appointment Details</h4>
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
                            <form action="{{ url('appointment') }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label>Select Doctor</label>
                                    <select class="form-control @error('doctor') is-invalid @enderror" name="doctor" required>
                                        <option value="" disabled selected>Select a Doctor</option>
                                        @foreach($doctors as $doctor)
                                        <option value="{{ $doctor->id }}">{{ $doctor->name }} - Speciality: {{ $doctor->speciality }}</option>
                                        @endforeach
                                    </select>
                                    @error('doctor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Appointment Date</label>
                                    <input type="date" class="form-control @error('appointment_date') is-invalid @enderror" name="date" value="{{ old('appointment_date') }}" required>
                                    @error('appointment_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Your Name</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Your Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Your Phone</label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" name="number" value="{{ old('phone') }}" required>
                                    @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Additional Message</label>
                                    <textarea class="form-control @error('message') is-invalid @enderror" name="message" rows="5">{{ old('message') }}</textarea>
                                    @error('message')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group text-center">
                                    <button type="submit" class="btn btn-primary">Book Appointment</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@include('patient.footer')