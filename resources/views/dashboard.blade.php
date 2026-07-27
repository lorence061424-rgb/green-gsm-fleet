@extends('layouts.app')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col">
        <h2 class="page-header-title">Analytics Dashboard</h2>
        <p class="page-header-subtitle">Real-time performance metrics, fuel analytics, and vehicle summaries.</p>
    </div>
    <div class="col-auto">
        <button class="btn btn-premium d-flex align-items-center" onclick="window.location.reload();">
            <i class="bi bi-arrow-clockwise me-1"></i> Refresh Data
        </button>
    </div>
</div>

<!-- KPI Cards Grid -->
<div class="row mb-4">
    <!-- Active Fleet -->
    <div class="col-md-3">
        <div class="card premium-card p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted text-uppercase fw-bold" style="font-size: 11px;">Active Fleet</span>
                    <h3 class="fw-bold mt-1 mb-0 text-primary">{{ $activeVehicles }}<span class="fs-6 text-muted font-normal"> / {{ $totalVehicles }}</span></h3>
                </div>
                <div class="bg-primary bg-opacity-10 p-3 rounded-4">
                    <i class="bi bi-truck fs-3 text-primary"></i>
                </div>
            </div>
            <div class="mt-3">
                <span class="badge bg-success rounded-pill">{{ $activeVehicles }} Active</span>
                <span class="badge bg-warning text-dark rounded-pill">{{ $maintenanceVehicles }} Servicing</span>
            </div>
        </div>
    </div>

    <!-- Active Trips -->
    <div class="col-md-3">
        <div class="card premium-card p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted text-uppercase fw-bold" style="font-size: 11px;">Active Trips</span>
                    <h3 class="fw-bold mt-1 mb-0 text-info">{{ $activeTrips }}</h3>
                </div>
                <div class="bg-info bg-opacity-10 p-3 rounded-4">
                    <i class="bi bi-geo-alt fs-3 text-info"></i>
                </div>
            </div>
            <div class="mt-3 text-muted" style="font-size: 13px;">
                <span class="loader-pulse me-1"></span> <span class="fw-bold text-danger">Live</span> tracking on-going
            </div>
        </div>
    </div>

    <!-- Fuel Costs -->
    <div class="col-md-3">
        <div class="card premium-card p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted text-uppercase fw-bold" style="font-size: 11px;">Total Fuel Expense</span>
                    <h3 class="fw-bold mt-1 mb-0 text-success">₱{{ number_format($totalFuelCost, 2) }}</h3>
                </div>
                <div class="bg-success bg-opacity-10 p-3 rounded-4">
                    <i class="bi bi-cash-stack fs-3 text-success"></i>
                </div>
            </div>
            <div class="mt-3 text-muted" style="font-size: 13px;">
                Liters Consumed: <strong class="text-dark">{{ number_format($totalFuelLiters, 1) }} L</strong>
            </div>
        </div>
    </div>

    <!-- Odometer Avg Efficiency -->
    <div class="col-md-3">
        <div class="card premium-card p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted text-uppercase fw-bold" style="font-size: 11px;">Avg Fuel Efficiency</span>
                    <h3 class="fw-bold mt-1 mb-0 text-dark">{{ number_format($avgEfficiency, 2) }} <span class="fs-6 text-muted fw-normal">L/100km</span></h3>
                </div>
                <div class="bg-dark bg-opacity-10 p-3 rounded-4">
                    <i class="bi bi-speedometer2 fs-3 text-dark"></i>
                </div>
            </div>
            <div class="mt-3 text-muted" style="font-size: 13px;">
                Maintenance Cost: <strong class="text-danger">₱{{ number_format($totalMaintenanceCost, 2) }}</strong>
            </div>
        </div>
    </div>
</div>

<!-- Graphs and Data Lists -->
<div class="row mb-4">
    <!-- Chart 1: Fuel Expense Trend -->
    <div class="col-md-8">
        <div class="card premium-card p-4 h-100">
            <h5 class="fw-bold mb-3"><i class="bi bi-graph-up text-primary me-2"></i> Fuel Transaction Expense Trend</h5>
            <div style="position: relative; height: 300px;">
                <canvas id="costHistoryChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Chart 2: Fuel Consumption by Type -->
    <div class="col-md-4">
        <div class="card premium-card p-4 h-100">
            <h5 class="fw-bold mb-3"><i class="bi bi-pie-chart text-primary me-2"></i> Fuel by Vehicle Class</h5>
            <div style="position: relative; height: 220px;" class="d-flex align-items-center justify-content-center">
                <canvas id="fuelTypeChart"></canvas>
            </div>
            <div class="mt-3 text-center text-muted" style="font-size: 12px;">
                Displays the cumulative liters consumed per vehicle class based on trip fuel logs.
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Drivers Standings -->
    <div class="col-md-6">
        <div class="card premium-card p-4 h-100">
            <h5 class="fw-bold mb-4"><i class="bi bi-star-fill text-warning me-2"></i> Top Driver Standings & Safety Scores</h5>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr class="text-muted" style="font-size: 12px; font-weight: 600;">
                            <th>DRIVER</th>
                            <th>LICENSE</th>
                            <th>COMPLETED TRIPS</th>
                            <th>SAFETY SCORE</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topDrivers as $driver)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-secondary bg-opacity-10 p-2 rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                            <i class="bi bi-person-fill text-secondary"></i>
                                        </div>
                                        <span class="fw-bold" style="font-size: 14px;">{{ $driver->user->name }}</span>
                                    </div>
                                </td>
                                <td style="font-size: 13px;">{{ $driver->license_number }}</td>
                                <td style="font-size: 13px;" class="fw-bold">{{ $driver->total_trips }}</td>
                                <td>
                                    <span class="badge rounded-pill {{ $driver->performance_score >= 90 ? 'bg-success' : ($driver->performance_score >= 80 ? 'bg-warning text-dark' : 'bg-danger') }}">
                                        {{ $driver->performance_score }}%
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">No drivers registered.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Active Alerts / Service Warnings -->
    <div class="col-md-6">
        <div class="card premium-card p-4 h-100">
            <h5 class="fw-bold mb-4 text-danger"><i class="bi bi-bell-fill me-2"></i> Maintenance Alerts & Alerts Panel</h5>
            @if(count($pendingMaintenance) > 0)
                <div class="list-group list-group-flush">
                    @foreach($pendingMaintenance as $record)
                        <div class="list-group-item px-0 py-3 border-0 border-bottom">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="fw-bold mb-1 text-dark">{{ $record->service_type }}</h6>
                                    <p class="mb-0 text-muted" style="font-size: 13px;">
                                        Vehicle: <strong>{{ $record->vehicle->make }} {{ $record->vehicle->model }} ({{ $record->vehicle->license_plate }})</strong>
                                    </p>
                                    <span class="text-danger" style="font-size: 12px; font-weight: 500;">
                                        <i class="bi bi-calendar-event me-1"></i> Scheduled Date: {{ \Carbon\Carbon::parse($record->scheduled_date)->toFormattedDateString() }}
                                    </span>
                                </div>
                                <span class="badge bg-danger rounded-pill">Urgent PMS</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <div class="bg-success bg-opacity-10 p-3 rounded-circle d-inline-flex mb-3">
                        <i class="bi bi-check-circle-fill text-success fs-3"></i>
                    </div>
                    <h6 class="fw-bold text-dark">All Vehicles Clear</h6>
                    <p class="text-muted mb-0" style="font-size: 13px;">No vehicles scheduled for PMS in the next 3 days.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // 1. Chart 1: Cost History Chart (Line Chart)
    const costCtx = document.getElementById('costHistoryChart').getContext('2d');
    
    const costDates = {!! json_encode($costHistory->pluck('date')) !!};
    const costData = {!! json_encode($costHistory->pluck('daily_cost')) !!};
    const litersData = {!! json_encode($costHistory->pluck('daily_liters')) !!};

    new Chart(costCtx, {
        type: 'line',
        data: {
            labels: costDates,
            datasets: [
                {
                    label: 'Fuel Spend (₱)',
                    data: costData,
                    borderColor: '#4F46E5',
                    backgroundColor: 'rgba(79, 70, 229, 0.05)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.3,
                    yAxisID: 'y'
                },
                {
                    label: 'Liters Consumed (L)',
                    data: litersData,
                    borderColor: '#06B6D4',
                    backgroundColor: 'transparent',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    fill: false,
                    tension: 0.3,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        font: {
                            family: 'Outfit'
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            family: 'Outfit'
                        }
                    }
                },
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    },
                    ticks: {
                        font: {
                            family: 'Outfit'
                        }
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: {
                        drawOnChartArea: false // prevent grid lines overlapping
                    },
                    ticks: {
                        font: {
                            family: 'Outfit'
                        }
                    }
                }
            }
        }
    });

    // 2. Chart 2: Fuel Type Chart (Doughnut Chart)
    const typeCtx = document.getElementById('fuelTypeChart').getContext('2d');
    
    const typeLabels = {!! json_encode($fuelByType->pluck('type')) !!};
    const typeData = {!! json_encode($fuelByType->pluck('total_liters')) !!};

    new Chart(typeCtx, {
        type: 'doughnut',
        data: {
            labels: typeLabels,
            datasets: [{
                data: typeData,
                backgroundColor: [
                    '#4F46E5', // Sedan
                    '#06B6D4', // SUV
                    '#10B981', // Van
                    '#F59E0B', // Hatchback
                    '#EF4444'  // Truck
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        font: {
                            family: 'Outfit'
                        },
                        boxWidth: 12
                    }
                }
            },
            cutout: '65%'
        }
    });
</script>
@endsection
