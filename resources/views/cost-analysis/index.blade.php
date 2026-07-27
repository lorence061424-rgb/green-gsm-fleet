@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="page-header-title">Transport Cost Analysis & Optimization (TCAO)</h2>
        <p class="page-header-subtitle">Analyze fleet operational costs per kilometer, driver cost efficiency, and AI savings recommendations.</p>
    </div>
    <div>
        <span class="badge bg-primary px-3 py-2 fs-6 rounded-pill"><i class="bi bi-cpu me-1"></i> AI Optimization Active</span>
    </div>
</div>

<!-- TCAO KPI Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card premium-card p-3 border-0">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-bold text-uppercase">Total Fleet Operating Cost</span>
                    <h3 class="fw-bold my-1 text-primary">₱{{ number_format($totalOperationalCost, 2) }}</h3>
                    <small class="text-muted">Fuel + Maintenance</small>
                </div>
                <div class="bg-primary-subtle text-primary p-3 rounded-4 fs-4">
                    <i class="bi bi-wallet2"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card premium-card p-3 border-0">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-bold text-uppercase">Average Cost per KM</span>
                    <h3 class="fw-bold my-1 text-dark">₱{{ $costPerKm }} <span class="fs-6 text-muted">/ km</span></h3>
                    <small class="text-muted">Fleetwide Metric</small>
                </div>
                <div class="bg-info-subtle text-info p-3 rounded-4 fs-4">
                    <i class="bi bi-speedometer2"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card premium-card p-3 border-0">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-bold text-uppercase">Fuel Expense Share</span>
                    <h3 class="fw-bold my-1 text-warning">₱{{ number_format($totalFuelCost, 2) }}</h3>
                    <small class="text-muted">₱{{ $fuelCostPerKm }} / km</small>
                </div>
                <div class="bg-warning-subtle text-warning p-3 rounded-4 fs-4">
                    <i class="bi bi-fuel-pump"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card premium-card p-3 border-0">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-bold text-uppercase">Maintenance Cost</span>
                    <h3 class="fw-bold my-1 text-danger">₱{{ number_format($totalMaintenanceCost, 2) }}</h3>
                    <small class="text-muted">₱{{ $maintCostPerKm }} / km</small>
                </div>
                <div class="bg-danger-subtle text-danger p-3 rounded-4 fs-4">
                    <i class="bi bi-wrench"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- AI Optimization Insights & Recommendations -->
<div class="card premium-card border-0 p-4 mb-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-lightbulb-fill text-warning me-2"></i> AI Cost Optimization Insights</h5>
        <span class="badge bg-success-subtle text-success px-3 py-1 rounded-pill">Smart Cost Reduction</span>
    </div>
    <div class="row g-3">
        @foreach($optimizationInsights as $insight)
        <div class="col-md-4">
            <div class="p-3 rounded-4 border h-100 bg-light">
                <div class="d-flex align-items-center mb-2">
                    @if($insight['type'] == 'warning')
                        <i class="bi bi-exclamation-triangle-fill text-warning fs-5 me-2"></i>
                    @elseif($insight['type'] == 'success')
                        <i class="bi bi-check-circle-fill text-success fs-5 me-2"></i>
                    @else
                        <i class="bi bi-info-circle-fill text-info fs-5 me-2"></i>
                    @endif
                    <h6 class="fw-bold mb-0 text-dark">{{ $insight['title'] }}</h6>
                </div>
                <p class="small text-muted mb-2">{{ $insight['description'] }}</p>
                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                    <span class="small text-muted fw-semibold">Est. Monthly Savings:</span>
                    <span class="badge bg-success text-white px-2 py-1">{{ $insight['potential_savings'] }}</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<div class="row g-4">
    <!-- Vehicle Cost & Efficiency Breakdown -->
    <div class="col-lg-7">
        <div class="card premium-card border-0 p-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-truck me-2 text-primary"></i> Vehicle Cost-per-KM Breakdown</h5>
            <div class="table-responsive">
                <table class="table align-middle table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Vehicle</th>
                            <th>Distance</th>
                            <th>Fuel Cost</th>
                            <th>Maint. Cost</th>
                            <th>Cost / KM</th>
                            <th>Efficiency</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vehicles as $veh)
                        <tr>
                            <td>
                                <strong class="d-block text-dark">{{ $veh['license_plate'] }}</strong>
                                <small class="text-muted">{{ $veh['model'] }}</small>
                            </td>
                            <td>{{ $veh['distance_km'] }} km</td>
                            <td>₱{{ number_format($veh['fuel_cost'], 2) }}</td>
                            <td>₱{{ number_format($veh['maintenance_cost'], 2) }}</td>
                            <td>
                                <strong class="text-dark">₱{{ $veh['cost_per_km'] }}</strong>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                        <div class="progress-bar bg-{{ $veh['efficiency_score'] >= 80 ? 'success' : ($veh['efficiency_score'] >= 65 ? 'warning' : 'danger') }}" 
                                             style="width: {{ $veh['efficiency_score'] }}%"></div>
                                    </div>
                                    <small class="fw-bold">{{ $veh['efficiency_score'] }}%</small>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Driver Cost & Safety Performance -->
    <div class="col-lg-5">
        <div class="card premium-card border-0 p-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-person-badge me-2 text-primary"></i> Driver Fuel Cost Efficiency</h5>
            <div class="table-responsive">
                <table class="table align-middle table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Driver</th>
                            <th>Safety Score</th>
                            <th>Fuel / KM</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($drivers as $drv)
                        <tr>
                            <td>
                                <strong class="d-block text-dark">{{ $drv['name'] }}</strong>
                                <small class="text-muted">{{ $drv['license'] }}</small>
                            </td>
                            <td>
                                <span class="badge bg-{{ $drv['safety_score'] >= 85 ? 'success' : ($drv['safety_score'] >= 70 ? 'warning' : 'danger') }}-subtle text-{{ $drv['safety_score'] >= 85 ? 'success' : ($drv['safety_score'] >= 70 ? 'warning' : 'danger') }} px-2 py-1 fw-bold">
                                    {{ $drv['safety_score'] }} / 100
                                </span>
                            </td>
                            <td>
                                <strong>₱{{ $drv['cost_per_km'] }}</strong> / km
                            </td>
                            <td>
                                <span class="small text-muted fw-semibold">{{ $drv['efficiency_tier'] }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
