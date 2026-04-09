@include('pharmacist.header')
@include('pharmacist.sidebar')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Sales Records</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="#">Dashboard</a></div>
                <div class="breadcrumb-item active">Sales</div>
            </div>
        </div>

        <div class="section-body">

            @if(session('message'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle mr-2"></i>{{ session('message') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
            @endif

            {{-- Summary Cards --}}
            <div class="row mb-4">
                <div class="col-lg-4 col-md-6">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-success">
                            <i class="fas fa-money-bill"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Total Revenue</h4>
                            </div>
                            <div class="card-body">Ksh {{ number_format($totalRevenue, 2) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-primary">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Total Sales</h4>
                            </div>
                            <div class="card-body">{{ $sales->count() }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-warning">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Pending Insurance</h4>
                            </div>
                            <div class="card-body">{{ $sales->where('payment_status', 'pending')->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4>All Sales</h4>
                    <div class="card-header-action">
                        <a href="{{ route('pharmacist.sales.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> New Sale
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Medicine</th>
                                    <th>Qty</th>
                                    <th>Total</th>
                                    <th>Sale Type</th>
                                    <th>Payment</th>
                                    <th>Reference</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Edit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sales as $sale)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $sale->medicine->name ?? 'N/A' }}</td>
                                    <td>{{ $sale->quantity_sold }}</td>
                                    <td>Ksh {{ number_format($sale->total_price, 2) }}</td>
                                    <td>
                                        <span class="badge
                                            {{ $sale->sale_type === 'prescription' ? 'badge-info' :
                                               ($sale->sale_type === 'insurance'   ? 'badge-warning' : 'badge-secondary') }}">
                                            {{ ucfirst($sale->sale_type) }}
                                        </span>
                                    </td>
                                    <td>{{ ucfirst($sale->payment_method) }}</td>
                                    <td>{{ $sale->payment_reference ?? '—' }}</td>
                                    <td>
                                        <span class="badge {{ $sale->payment_status === 'paid' ? 'badge-success' : 'badge-danger' }}">
                                            {{ ucfirst($sale->payment_status) }}
                                        </span>
                                    </td>
                                    <td>{{ $sale->created_at->format('d M Y, h:i A') }}</td>
                                    <td>
                                        <a href="{{ route('pharmacist.sales.edit', $sale->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">No sales recorded yet.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

@include('pharmacist.footer')