@include('doctor.header')
@include('doctor.sidebar')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Add Prescription</h1>
        </div>
        <div class="section-body">

            @if(session('message'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('message') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
            @endif

            <div class="row">

                {{-- Add Medicine Form --}}
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4>Prescribe Medicine</h4>
                            Patient: <strong>{{ $encounter->appointment?->name ?? $encounter->patient?->name ?? 'N/A' }}</strong>
                        </div>
                        <div class="card-body">

                            @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif

                            <form action="{{ route('doctor.prescriptions.store', $encounter->id) }}" method="POST">
                                @csrf

                                <div class="form-group">
                                    <label>Medicine</label>
                                    <select name="medicine_id" class="form-control" required>
                                        <option value="">-- Select Medicine --</option>
                                        @foreach($medicines as $medicine)
                                        <option value="{{ $medicine->id }}">{{ $medicine->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Dosage</label>
                                    <input type="text" name="dosage" class="form-control"
                                        placeholder="e.g. 500mg" required>
                                </div>

                                <div class="form-group">
                                    <label>Frequency</label>
                                    <select name="frequency" class="form-control" required>
                                        <option value="once daily">Once Daily</option>
                                        <option value="twice daily">Twice Daily</option>
                                        <option value="three times daily">Three Times Daily</option>
                                        <option value="four times daily">Four Times Daily</option>
                                        <option value="every 6 hours">Every 6 Hours</option>
                                        <option value="every 8 hours">Every 8 Hours</option>
                                        <option value="as needed">As Needed</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Duration (days)</label>
                                    <input type="number" name="duration_days" class="form-control"
                                        min="1" placeholder="e.g. 7" required>
                                </div>

                                <div class="form-group">
                                    <label>Instructions</label>
                                    <textarea name="instructions" class="form-control"
                                        rows="2" placeholder="e.g. Take after meals"></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Add Medicine
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Current Prescription List --}}
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4>Current Prescription</h4>
                        </div>
                        <div class="card-body p-0">
                            @if($encounter->prescriptions->isEmpty())
                            <div class="p-4 text-center text-muted">
                                No medicines added yet.
                            </div>
                            @else
                            <div class="table-responsive">
                                <table class="table table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th>Medicine</th>
                                            <th>Dosage</th>
                                            <th>Frequency</th>
                                            <th>Days</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($encounter->prescriptions as $prescription)
                                        <tr>
                                            <td>{{ $prescription->medicine?->name ?? 'N/A' }}</td>
                                            <td>{{ $prescription->dosage }}</td>
                                            <td>{{ $prescription->frequency }}</td>
                                            <td>{{ $prescription->duration_days }}</td>
                                            <td>
                                                <span class="badge
                                                        {{ $prescription->status == 'pending'   ? 'badge-warning'   : '' }}
                                                        {{ $prescription->status == 'dispensed' ? 'badge-success'   : '' }}
                                                        {{ $prescription->status == 'cancelled' ? 'badge-danger'    : '' }}">
                                                    {{ ucfirst($prescription->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @endif
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('doctor.encounter.close', $encounter->id) }}"
                                class="btn btn-success"
                                onclick="return confirm('Close this encounter? No more medicines can be added.')">
                                <i class="fas fa-lock"></i> Close Encounter
                            </a>
                            <a href="{{ route('doctor.encounters') }}" class="btn btn-secondary">
                                Back to Encounters
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
</div>

@include('doctor.footer')