@php
$role = auth()->user()->role;
$header = match($role) {
'admin' => 'admin.header',
'doctor' => 'doctor.header',
'patient' => 'patient.header',
'lab_technician' => 'lab.header',
default => 'admin.header',
};
$sidebar = match($role) {
'admin' => 'admin.menusidebar',
'doctor' => 'doctor.sidebar',
'patient' => 'patient.sidebar',
'lab_technician' => 'lab.menusidebar',
default => 'admin.menusidebar',
};
$footer = match($role) {
'admin' => 'admin.footer',
'doctor' => 'doctor.footer',
'patient' => 'patient.footer',
'lab_technician' => 'lab.footer',
default => 'admin.footer',
};
@endphp

@include($header)
@include($sidebar)

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Lab Result</h1>
            <div class="section-header-breadcrumb">
                <a href="{{ url()->previous() }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
        <div class="section-body">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h4>{{ $labRequest->labTest?->name }} — Result</h4>
                        <span class="badge badge-success float-right mt-1">
                            {{ ucfirst($labRequest->status) }}
                        </span>
                    </div>
                    <div class="card-body">

                        <table class="table table-bordered mb-4">
                            <tr>
                                <td class="text-muted" width="35%">Patient</td>
                                <td><strong>{{ $labRequest->patient_name }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Test</td>
                                <td>
                                    {{ $labRequest->labTest?->name }}
                                    <span class="badge badge-primary ml-1">{{ $labRequest->labTest?->code }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Requested By</td>
                                <td>Dr. {{ $labRequest->doctor?->name }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Requested On</td>
                                <td>{{ $labRequest->requested_at->format('d M Y') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Status</td>
                                <td>
                                    <span class="badge badge-success">
                                        {{ ucfirst($labRequest->status) }}
                                    </span>
                                </td>
                            </tr>

                            @if($isAdmin)
                            {{-- Admin: cost + status only, no clinical details --}}
                            <tr>
                                <td class="text-muted">Cost</td>
                                <td>Ksh {{ number_format($labRequest->labTest?->price ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <div class="alert alert-info mb-0">
                                        <i class="fas fa-info-circle"></i>
                                        Clinical details are restricted to medical staff only.
                                    </div>
                                </td>
                            </tr>

                            @else
                            {{-- Doctor, Lab Tech, Patient: full clinical details --}}
                            @if($labRequest->completed_at)
                            <tr>
                                <td class="text-muted">Completed On</td>
                                <td>{{ $labRequest->completed_at->format('d M Y') }}</td>
                            </tr>
                            @endif
                            @if($labRequest->result_notes)
                            <tr>
                                <td class="text-muted">Result Notes</td>
                                <td>{{ $labRequest->result_notes }}</td>
                            </tr>
                            @endif
                            @if($labRequest->released_to_patient)
                            <tr>
                                <td class="text-muted">Released to Patient</td>
                                <td>{{ $labRequest->released_at?->format('d M Y') }}</td>
                            </tr>
                            @endif
                            @endif

                        </table>

                        {{-- Result file: hidden from admin --}}
                        @if(!$isAdmin && $labRequest->result_file)
                        @php $ext = strtolower(pathinfo($labRequest->result_file, PATHINFO_EXTENSION)); @endphp

                        @if(in_array($ext, ['jpg', 'jpeg', 'png']))
                        <div class="text-center mb-3">
                            <img src="{{ asset('labresults/' . $labRequest->result_file) }}"
                                class="img-fluid rounded" style="max-height:600px;">
                        </div>
                        @elseif($ext === 'pdf')
                        <div class="embed-responsive embed-responsive-16by9 mb-3">
                            <iframe class="embed-responsive-item"
                                src="{{ asset('labresults/' . $labRequest->result_file) }}">
                            </iframe>
                        </div>
                        @endif

                        <div class="text-center">
                            <a href="{{ asset('labresults/' . $labRequest->result_file) }}"
                                class="btn btn-success" download>
                                <i class="fas fa-download"></i> Download Result
                            </a>
                        </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@include($footer)