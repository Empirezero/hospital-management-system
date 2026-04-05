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
                }, 5000);
            </script>
            @endif

            <form action="{{ url('update_appointment', $appointment->id) }}" method="POST">
                @csrf
                <div class="row">

                    <!-- Patient Name -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Patient Name</label>
                            <input type="text" name="name" class="form-control"
                                value="{{ $appointment->name }}" required>
                        </div>
                    </div>

                    <!-- Patient Phone -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Patient Phone</label>
                            <input type="text" name="number" class="form-control"
                                value="{{ $appointment->number }}" required>
                        </div>
                    </div>

                    <!-- Patient Email -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Patient Email</label>
                            <input type="email" name="email" class="form-control"
                                value="{{ $appointment->email }}" required>
                        </div>
                    </div>

                    <!-- Doctor Name — read only, doctor cannot change this -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Doctor Name</label>
                            <input type="text" class="form-control"
                                value="{{ $appointment->doctor?->name ?? 'N/A' }}"
                                disabled>
                            {{-- Keep the doctor_id in the form so it's not lost on submit --}}
                            <input type="hidden" name="doctor_id" value="{{ $appointment->doctor_id }}">
                        </div>
                    </div>

                    <!-- Date -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Appointment Date</label>
                            <input type="date" name="scheduled_at" class="form-control"
                                value="{{ \Carbon\Carbon::parse($appointment->scheduled_at)->format('Y-m-d') }}"
                                required>
                        </div>
                    </div>

                    <!-- Message -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Message</label>
                            <textarea name="message" class="form-control">{{ $appointment->message }}</textarea>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="pending" {{ $appointment->status == 'pending'    ? 'selected' : '' }}>Pending</option>
                                <option value="confirmed" {{ $appointment->status == 'confirmed'  ? 'selected' : '' }}>Confirmed</option>
                                <option value="completed" {{ $appointment->status == 'completed'  ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ $appointment->status == 'cancelled'  ? 'selected' : '' }}>Cancelled</option>
                                <option value="no_show" {{ $appointment->status == 'no_show'    ? 'selected' : '' }}>No Show</option>
                            </select>
                        </div>
                    </div>

                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Update Appointment</button>
                    <a href="{{ url('doctor_appointment') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </section>
</div>

@include('doctor.footer')