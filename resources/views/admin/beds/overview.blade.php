@include('admin.header')
@include('admin.menusidebar')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Bed Overview</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ url('index') }}">Dashboard</a></div>
                <div class="breadcrumb-item">Bed Overview</div>
            </div>
        </div>
        <div class="section-body">

            @if(session('message'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('message') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
            @endif

            {{-- Stats --}}
            <div class="row">
                <div class="col-md-2">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-primary"><i class="fas fa-bed"></i></div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Total Beds</h4>
                            </div>
                            <div class="card-body">{{ $stats['total_beds'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-success"><i class="fas fa-check-circle"></i></div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Available</h4>
                            </div>
                            <div class="card-body">{{ $stats['available'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-danger"><i class="fas fa-user-injured"></i></div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Occupied</h4>
                            </div>
                            <div class="card-body">{{ $stats['occupied'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-warning"><i class="fas fa-tools"></i></div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Maintenance</h4>
                            </div>
                            <div class="card-body">{{ $stats['maintenance'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-info"><i class="fas fa-sign-in-alt"></i></div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Admitted Today</h4>
                            </div>
                            <div class="card-body">{{ $stats['admitted_today'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-secondary"><i class="fas fa-sign-out-alt"></i></div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Discharged Today</h4>
                            </div>
                            <div class="card-body">{{ $stats['discharged_today'] }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Ward Bed Grid --}}
            @foreach($wards as $ward)
            <div class="card">
                <div class="card-header">
                    <h4>
                        <span class="badge badge-{{
                            $ward->type == 'icu'       ? 'danger'  :
                            ($ward->type == 'emergency' ? 'warning' :
                            ($ward->type == 'private'   ? 'info'    : 'primary'))
                        }} mr-2">{{ ucfirst($ward->type) }}</span>
                        {{ $ward->name }}
                    </h4>
                    <div class="card-header-action">
                        <small class="text-muted mr-3">
                            {{ $ward->beds->where('status', 'available')->count() }} available /
                            {{ $ward->beds->count() }} total
                        </small>
                        <a href="{{ route('admin.beds.by_ward', $ward->id) }}"
                            class="btn btn-sm btn-primary">
                            <i class="fas fa-list"></i> Manage
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap">
                        @foreach($ward->beds as $bed)
                        <div class="m-1 text-center" style="width:70px;">
                            <div class="p-2 rounded border
                                {{ $bed->status == 'available'   ? 'border-success bg-light'   : '' }}
                                {{ $bed->status == 'occupied'    ? 'border-danger'              : '' }}
                                {{ $bed->status == 'maintenance' ? 'border-warning bg-light'   : '' }}"
                                style="cursor:pointer;"
                                title="{{ $bed->bed_number }} — {{ ucfirst($bed->status) }}
                                    {{ $bed->status == 'occupied' ? '— ' . ($bed->currentAdmission?->patient_name ?? '') : '' }}">
                                <i class="fas fa-bed fa-lg
                                    {{ $bed->status == 'available'   ? 'text-success' : '' }}
                                    {{ $bed->status == 'occupied'    ? 'text-danger'  : '' }}
                                    {{ $bed->status == 'maintenance' ? 'text-warning' : '' }}">
                                </i>
                                <small class="d-block mt-1" style="font-size:10px;">
                                    {{ $bed->bed_number }}
                                </small>
                                @if($bed->status == 'available')
                                <a href="{{ route('admin.beds.admit', $bed->id) }}"
                                    class="btn btn-xs btn-success mt-1"
                                    style="font-size:9px; padding:1px 4px;">
                                    Admit
                                </a>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- Legend --}}
                    <div class="mt-3">
                        <span class="mr-3"><i class="fas fa-bed text-success"></i> Available</span>
                        <span class="mr-3"><i class="fas fa-bed text-danger"></i> Occupied</span>
                        <span><i class="fas fa-bed text-warning"></i> Maintenance</span>
                    </div>
                </div>
            </div>
            @endforeach

        </div>
    </section>
</div>

@include('admin.footer')