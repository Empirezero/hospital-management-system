@php
$role = auth()->user()->role;
$header = $role . '.header';
$sidebar = match($role) {
'admin' => 'admin.menusidebar',
'doctor' => 'doctor.sidebar',
'nurse' => 'nurse.sidebar',
default => 'admin.menusidebar',
};
$footer = $role . '.footer';
@endphp

@include($header)
@include($sidebar)

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>{{ $selectedWard ? $selectedWard->name . ' Beds' : 'All Beds' }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ url('/home') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admin.beds.wards') }}">Wards</a></div>
                <div class="breadcrumb-item">Beds</div>
            </div>
        </div>
        <div class="section-body">

            @if(session('message'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('message') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
            @endif

            {{-- Ward Filter --}}
            <div class="card">
                <div class="card-header">
                    <h4>Filter by Ward</h4>
                    <div class="card-header-action">
                        <a href="{{ route('beds.index') }}"
                            class="btn btn-sm {{ !$selectedWard ? 'btn-primary' : 'btn-outline-primary' }} mr-1">
                            All Wards
                        </a>
                        @foreach($wards as $ward)
                        <a href="{{ route('beds.by_ward', $ward->id) }}"
                            class="btn btn-sm {{ $selectedWard?->id == $ward->id ? 'btn-primary' : 'btn-outline-primary' }} mr-1">
                            {{ $ward->name }}
                            <span class="badge badge-light ml-1">{{ $ward->beds->count() }}</span>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Beds Table --}}
            <div class="card">
                <div class="card-header">
                    <h4>Beds</h4>
                    <div class="card-header-action">
                        <a href="{{ route('beds.admit') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> Admit Patient
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Bed Number</th>
                                    <th>Ward</th>
                                    <th>Status</th>
                                    <th>Current Patient</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($beds as $bed)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><strong>{{ $bed->bed_number }}</strong></td>
                                    <td>{{ $bed->ward?->name }}</td>
                                    <td>
                                        <span class="badge
                                            {{ $bed->status == 'available'   ? 'badge-success' : '' }}
                                            {{ $bed->status == 'occupied'    ? 'badge-danger'  : '' }}
                                            {{ $bed->status == 'maintenance' ? 'badge-warning' : '' }}">
                                            {{ ucfirst($bed->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $bed->currentAdmission?->patient_name ?? '—' }}</td>
                                    <td>
                                        @if($bed->status == 'available')
                                        <a href="{{ route('beds.admit', $bed->id) }}"
                                            class="btn btn-success btn-sm">
                                            <i class="fas fa-user-plus"></i> Admit
                                        </a>
                                        @elseif($bed->status == 'occupied')
                                        <a href="{{ route('beds.discharge', $bed->currentAdmission?->id) }}"
                                            class="btn btn-warning btn-sm"
                                            onclick="return confirm('Discharge this patient?')">
                                            <i class="fas fa-sign-out-alt"></i> Discharge
                                        </a>
                                        @endif
                                        <form action="{{ route('beds.status', $bed->id) }}"
                                            method="POST" class="d-inline ml-1">
                                            @csrf
                                            <select name="status"
                                                class="form-control-sm"
                                                onchange="this.form.submit()">
                                                <option value="available" {{ $bed->status == 'available'   ? 'selected' : '' }}>Available</option>
                                                <option value="occupied" {{ $bed->status == 'occupied'    ? 'selected' : '' }}>Occupied</option>
                                                <option value="maintenance" {{ $bed->status == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                            </select>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No beds found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Pagination inside the card --}}
                @if($beds->hasPages())
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            Showing {{ $beds->firstItem() }} to {{ $beds->lastItem() }}
                            of {{ $beds->total() }} beds
                        </small>
                        {{ $beds->links() }}
                    </div>
                </div>
                @endif

            </div>

        </div>
    </section>
</div>

@include($footer)