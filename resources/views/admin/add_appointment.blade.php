@include('admin.header')
@include('admin.menusidebar')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Book Appointment</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ url('index') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ url('show_appointment') }}">Appointments</a></div>
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

                            @if(session('message'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('message') }}
                                <button type="button" class="close" data-dismiss="alert">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            @endif

                            @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif

                            <form action="{{ url('appointment') }}" method="POST">
                                @csrf

                                {{-- Doctor Select --}}
                                <div class="form-group">
                                    <label>Select Doctor</label>
                                    <select class="form-control @error('doctor') is-invalid @enderror"
                                        name="doctor" id="doctor-select" required>
                                        <option value="" disabled selected>Select a Doctor</option>
                                        @foreach($doctors as $doctor)
                                        <option value="{{ $doctor->id }}"
                                            {{ old('doctor') == $doctor->id ? 'selected' : '' }}>
                                            {{ $doctor->name }} — {{ $doctor->speciality }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('doctor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Date --}}
                                <div class="form-group">
                                    <label>Appointment Date</label>
                                    <input type="date"
                                        class="form-control @error('date') is-invalid @enderror"
                                        name="date" id="date-input"
                                        value="{{ old('date') }}"
                                        min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                        required>
                                    @error('date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Available Time Slots --}}
                                <div class="form-group" id="slots-container" style="display:none;">
                                    <label>Available Time Slots</label>

                                    <div id="slots-loading" style="display:none;">
                                        <i class="fas fa-spinner fa-spin"></i> Loading available slots...
                                    </div>

                                    <div id="slots-unavailable" class="alert alert-warning" style="display:none;">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        Doctor is not available on this day.
                                    </div>

                                    <div id="slots-list" class="d-flex flex-wrap"></div>

                                    <input type="hidden" name="time_slot" id="selected-slot"
                                        value="{{ old('time_slot') }}">

                                    <div id="slot-error" class="text-danger mt-1" style="display:none;">
                                        Please select a time slot.
                                    </div>
                                </div>

                                {{-- Name --}}
                                <div class="form-group">
                                    <label>Your Name</label>
                                    <input type="text"
                                        class="form-control @error('name') is-invalid @enderror"
                                        name="name" value="{{ old('name', auth()->user()?->name) }}" required>
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Email --}}
                                <div class="form-group">
                                    <label>Your Email</label>
                                    <input type="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        name="email" value="{{ old('email', auth()->user()?->email) }}" required>
                                    @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Phone --}}
                                <div class="form-group">
                                    <label>Your Phone</label>
                                    <input type="tel"
                                        class="form-control @error('number') is-invalid @enderror"
                                        name="number" value="{{ old('number') }}" required>
                                    @error('number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Message --}}
                                <div class="form-group">
                                    <label>Additional Message <small class="text-muted">(optional)</small></label>
                                    <textarea class="form-control @error('message') is-invalid @enderror"
                                        name="message" rows="3">{{ old('message') }}</textarea>
                                    @error('message')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group text-center">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-calendar-check mr-1"></i> Book Appointment
                                    </button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    var slotsUrl = "{{ route('schedules.slots') }}";

    function loadSlots() {
        var doctorId = document.getElementById('doctor-select').value;
        var date = document.getElementById('date-input').value;

        if (!doctorId || !date) return;

        var container = document.getElementById('slots-container');
        var loading = document.getElementById('slots-loading');
        var unavailable = document.getElementById('slots-unavailable');
        var slotsList = document.getElementById('slots-list');
        var slotError = document.getElementById('slot-error');

        container.style.display = 'block';
        loading.style.display = 'block';
        unavailable.style.display = 'none';
        slotsList.innerHTML = '';
        slotError.style.display = 'none';
        document.getElementById('selected-slot').value = '';

        fetch(slotsUrl + '?doctor_id=' + doctorId + '&date=' + date)
            .then(function(res) {
                return res.json();
            })
            .then(function(data) {
                loading.style.display = 'none';

                if (!data.available) {
                    unavailable.style.display = 'block';
                    return;
                }

                data.slots.forEach(function(slot) {
                    var btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'btn btn-sm mr-1 mb-2 ' +
                        (slot.available ? 'btn-outline-primary' : 'btn-outline-secondary disabled');
                    btn.textContent = slot.time;
                    btn.disabled = !slot.available;

                    if (slot.available) {
                        btn.addEventListener('click', function() {
                            // Deselect all
                            document.querySelectorAll('#slots-list button').forEach(function(b) {
                                b.classList.remove('btn-primary');
                                b.classList.add('btn-outline-primary');
                            });
                            // Select clicked
                            btn.classList.remove('btn-outline-primary');
                            btn.classList.add('btn-primary');
                            document.getElementById('selected-slot').value = slot.time;
                            document.getElementById('slot-error').style.display = 'none';
                        });
                    }

                    slotsList.appendChild(btn);
                });

                // Re-select old slot on validation error
                var oldSlot = "{{ old('time_slot') }}";
                if (oldSlot) {
                    document.querySelectorAll('#slots-list button').forEach(function(b) {
                        if (b.textContent === oldSlot) {
                            b.classList.remove('btn-outline-primary');
                            b.classList.add('btn-primary');
                            document.getElementById('selected-slot').value = oldSlot;
                        }
                    });
                }
            })
            .catch(function() {
                loading.style.display = 'none';
            });
    }

    document.getElementById('doctor-select').addEventListener('change', loadSlots);
    document.getElementById('date-input').addEventListener('change', loadSlots);

    // Validate slot selected before submit
    document.querySelector('form').addEventListener('submit', function(e) {
        var slot = document.getElementById('selected-slot').value;
        var container = document.getElementById('slots-container');

        if (container.style.display !== 'none' && !slot) {
            e.preventDefault();
            document.getElementById('slot-error').style.display = 'block';
            document.getElementById('slots-container').scrollIntoView({
                behavior: 'smooth'
            });
        }
    });

    // Load slots on page load if old values exist (after validation error)
    window.addEventListener('load', function() {
        var doctorId = document.getElementById('doctor-select').value;
        var date = document.getElementById('date-input').value;
        if (doctorId && date) loadSlots();
    });
</script>

@include('admin.footer')