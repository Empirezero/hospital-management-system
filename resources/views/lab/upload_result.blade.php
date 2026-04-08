@include('lab.header')
@include('lab.menusidebar')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Upload Result</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('lab.home') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('lab.queue') }}">Queue</a></div>
                <div class="breadcrumb-item">Upload Result</div>
            </div>
        </div>
        <div class="section-body">
            <div class="row">

                {{-- Request Info --}}
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h4>Request Details</h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="text-muted">Patient</td>
                                    <td><strong>{{ $labRequest->patient_name }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Phone</td>
                                    <td>{{ $labRequest->patient_phone ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Test</td>
                                    <td><strong>{{ $labRequest->labTest?->name }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Code</td>
                                    <td><span class="badge badge-primary">{{ $labRequest->labTest?->code }}</span></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Doctor</td>
                                    <td>Dr. {{ $labRequest->doctor?->name }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Requested</td>
                                    <td>{{ $labRequest->requested_at->format('d M Y') }}</td>
                                </tr>
                                @if($labRequest->notes)
                                <tr>
                                    <td class="text-muted">Notes</td>
                                    <td><em>{{ $labRequest->notes }}</em></td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Upload Form --}}
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h4>Upload Result File</h4>
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

                            <form action="{{ route('lab.store_result', $labRequest->id) }}"
                                method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="form-group">
                                    <label>Result File <span class="text-danger">*</span></label>
                                    <input type="file" name="result_file"
                                        class="form-control-file"
                                        accept=".pdf,.jpg,.jpeg,.png" required>
                                    <small class="text-muted">Accepted: PDF, JPG, PNG. Max 5MB.</small>
                                </div>

                                <div class="form-group">
                                    <label>Result Notes <small class="text-muted">(optional)</small></label>
                                    <textarea name="result_notes" class="form-control" rows="4"
                                        placeholder="Summary of findings, abnormal values...">{{ old('result_notes') }}</textarea>
                                </div>

                                <div class="form-group">
                                    <label>Status <span class="text-danger">*</span></label>
                                    <select name="status" class="form-control" required>
                                        <option value="in_progress">In Progress</option>
                                        <option value="completed" selected>Completed</option>
                                    </select>
                                </div>

                                <div class="text-right">
                                    <a href="{{ route('lab.queue') }}" class="btn btn-secondary mr-2">Cancel</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-upload"></i> Upload Result
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

@include('lab.footer')