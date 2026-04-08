@include('admin.header')
@include('admin.menusidebar')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Manage Schedule</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ url('index') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admin.schedules.index') }}">Schedules</a></div>
                <div class="breadcrumb-item">{{ $doctor->name }}</div>
            </div>
        </div>

        <div class="section-body">

            @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h4>
                        @if($doctor->image)
                        <img src="{{ asset('doctorimage/' . $doctor->image) }}"
                            class="rounded-circle mr-2"
                            style="height:35px; width:35px; object-fit:cover;">
                        @endif
                        Dr. {{ $doctor->name }} — {{ $doctor->speciality }}
                    </h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.schedules.save', $doctor->id) }}" method="POST">
                        @csrf

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="50">Active</th>
                                        <th>Day</th>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                        <th>Slot Duration</th>
                                        <th>Slots Preview</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($days as $day)
                                    @php $schedule = $schedules[$day] ?? null; @endphp
                                    <tr>
                                        <td class="text-center">
                                            <input type="checkbox"
                                                name="days[]"
                                                value="{{ $day }}"
                                                class="day-checkbox"
                                                data-day="{{ $day }}"
                                                {{ ($schedule && $schedule->is_active) ? 'checked' : '' }}>
                                        </td>
                                        <td><strong>{{ ucfirst($day) }}</strong></td>
                                        <td>
                                            <input type="time"
                                                name="start_time[{{ $day }}]"
                                                class="form-control form-control-sm day-input-{{ $day }}"
                                                value="{{ $schedule?->start_time ?? '08:00' }}"
                                                {{ (!$schedule || !$schedule->is_active) ? 'disabled' : '' }}>
                                        </td>
                                        <td>
                                            <input type="time"
                                                name="end_time[{{ $day }}]"
                                                class="form-control form-control-sm day-input-{{ $day }}"
                                                value="{{ $schedule?->end_time ?? '17:00' }}"
                                                {{ (!$schedule || !$schedule->is_active) ? 'disabled' : '' }}>
                                        </td>
                                        <td>
                                            <select name="slot_duration[{{ $day }}]"
                                                class="form-control form-control-sm day-input-{{ $day }}"
                                                {{ (!$schedule || !$schedule->is_active) ? 'disabled' : '' }}>
                                                @foreach([15 => '15 min', 20 => '20 min', 30 => '30 min', 45 => '45 min', 60 => '1 hour'] as $val => $label)
                                                <option value="{{ $val }}"
                                                    {{ ($schedule?->slot_duration ?? 30) == $val ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <small class="text-muted slots-preview-{{ $day }}">
                                                @if($schedule && $schedule->is_active)
                                                {{ count($schedule->getSlots()) }} slots
                                                @else
                                                —
                                                @endif
                                            </small>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="text-right mt-3">
                            <a href="{{ route('admin.schedules.index') }}" class="btn btn-secondary mr-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Schedule
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    document.querySelectorAll('.day-checkbox').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            var day = this.dataset.day;
            var inputs = document.querySelectorAll('.day-input-' + day);
            var preview = document.querySelector('.slots-preview-' + day);

            inputs.forEach(function(input) {
                input.disabled = !checkbox.checked;
            });

            preview.textContent = checkbox.checked ? 'Set times to calculate' : '—';
        });
    });
</script>

@include('admin.footer')