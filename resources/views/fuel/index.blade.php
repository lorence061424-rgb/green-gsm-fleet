@extends('layouts.app')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col">
        <h2 class="page-header-title">Fuel Usage & AI Consumption Prediction</h2>
        <p class="page-header-subtitle">Log refuels, monitor efficiency metrics, and manage model parameters.</p>
    </div>
    <div class="col-auto">
        <button class="btn btn-premium d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#logFuelModal">
            <i class="bi bi-plus-circle me-1"></i> Log Fuel Refill
        </button>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Active Weights Info -->
    <div class="col-md-4">
        <div class="card premium-card p-4 h-100 bg-secondary text-white border-0">
            <h5 class="fw-bold mb-3 text-info"><i class="bi bi-cpu-fill me-1"></i> Active AI Model Weights</h5>
            <p class="text-white-50" style="font-size: 13px;">
                These coefficients represent the weights trained by the Ordinary Least Squares (OLS) / Gradient Descent regression engine using historical completed trip logs.
            </p>
            <div class="mt-3">
                <div class="d-flex justify-content-between border-bottom border-dark py-2">
                    <span class="text-white-50">Intercept (&beta;<sub>0</sub>)</span>
                    <span class="fw-bold text-info">{{ number_format($weights['intercept'] ?? 0.5, 4) }}</span>
                </div>
                <div class="d-flex justify-content-between border-bottom border-dark py-2">
                    <span class="text-white-50">Distance Cost (&beta;<sub>1</sub> per km)</span>
                    <span class="fw-bold text-info">{{ number_format($weights['distance'] ?? 0.08, 4) }}</span>
                </div>
                <div class="d-flex justify-content-between py-2 mb-3">
                    <span class="text-white-50">Speed Penalty (&beta;<sub>2</sub> per km/h)</span>
                    <span class="fw-bold text-info">{{ number_format($weights['speed'] ?? 0.0005, 5) }}</span>
                </div>
            </div>

            <form action="{{ route('fuel.train') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-info w-100 rounded-3 text-dark fw-bold">
                    <i class="bi bi-gear-fill me-1"></i> Re-Train AI Model
                </button>
            </form>
        </div>
    </div>

    <!-- AI Fuel Consumption Test Simulator Panel -->
    <div class="col-md-8">
        <div class="card premium-card p-4 h-100">
            <h5 class="fw-bold mb-3"><i class="bi bi-calculator text-primary me-2"></i> AI Fuel Predictor Test Panel</h5>
            
            <div class="row">
                <!-- Form Inputs -->
                <div class="col-md-6 border-end">
                    <form id="aiPredictForm" onsubmit="runPredictionTest(event);">
                        <div class="mb-3">
                            <label class="form-label" style="font-weight: 500;">Planned Distance (km)</label>
                            <input type="number" step="0.1" id="testDistance" class="form-control rounded-3" placeholder="e.g. 15.2" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" style="font-weight: 500;">Estimated Avg Speed (km/h)</label>
                            <input type="number" id="testSpeed" class="form-control rounded-3" placeholder="e.g. 45" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" style="font-weight: 500;">Vehicle Class</label>
                            <select id="testVehicleType" class="form-select rounded-3" required>
                                <option value="Hatchback">Hatchback (Multiplier: 0.8)</option>
                                <option value="Sedan" selected>Sedan (Multiplier: 1.0)</option>
                                <option value="SUV">SUV (Multiplier: 1.3)</option>
                                <option value="Van">Van (Multiplier: 1.6)</option>
                                <option value="Truck">Truck (Multiplier: 2.2)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" style="font-weight: 500;">Actual Fuel Used (Optional, for route analysis)</label>
                            <input type="number" step="0.1" id="testActualFuel" class="form-control rounded-3" placeholder="e.g. 1.8">
                        </div>
                        <button type="submit" class="btn btn-premium w-100 rounded-3">
                            <i class="bi bi-cpu me-1"></i> Predict Consumption
                        </button>
                    </form>
                </div>

                <!-- Live Results Output -->
                <div class="col-md-6 bg-light bg-opacity-50 p-3 rounded-4 d-flex align-items-center justify-content-center" id="predictResultContainer">
                    <div class="text-center text-muted" id="placeholderResult">
                        <i class="bi bi-cpu fs-1 mb-2 text-primary"></i>
                        <h6>Estimation Results</h6>
                        <p class="mb-0 small">Enter path parameters on the left to estimate consumption outputs.</p>
                    </div>

                    <div id="actualResultPanel" class="w-100 d-none">
                        <div class="mb-3 text-center">
                            <span class="text-muted text-uppercase fw-bold" style="font-size: 11px;">Estimated Fuel consumption</span>
                            <h2 class="fw-bold text-success mt-1 mb-0" id="resultPredictedLiters">0.00 L</h2>
                            <small class="text-muted">Estimated cost: <strong class="text-dark" id="resultCost">₱0.00</strong></small>
                        </div>
                        
                        <div class="border-top pt-2">
                            <h6 class="fw-bold text-dark mb-1" style="font-size: 13px;">Route Analysis:</h6>
                            <p class="text-muted mb-3" style="font-size: 12px;" id="resultEfficiencyInsights">
                                Optimal cruise.
                            </p>
                            
                            <h6 class="fw-bold text-dark mb-1" style="font-size: 13px;">Eco Recommendations:</h6>
                            <ul class="ps-3 mb-0 text-muted small" id="resultRecommendations" style="font-size: 11.5px;">
                                <!-- Recommendations elements -->
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Refuels log grid -->
<div class="card premium-card p-4">
    <h5 class="fw-bold mb-4"><i class="bi bi-file-earmark-spreadsheet text-primary me-2"></i> Fuel Transaction Logs</h5>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr class="text-muted" style="font-size: 13px;">
                    <th>VEHICLE</th>
                    <th>DATE</th>
                    <th>LITERS ADDED</th>
                    <th>TRANSACTION COST</th>
                    <th>ODOMETER (MILEAGE)</th>
                    <th>FUEL TYPE</th>
                    <th>TRIP LOG</th>
                </tr>
            </thead>
            <tbody>
                @forelse($fuelLogs as $log)
                    <tr>
                        <td>
                            <span class="fw-bold">{{ $log->vehicle->make }} {{ $log->vehicle->model }}</span>
                            <small class="badge bg-secondary ms-1">{{ $log->vehicle->license_plate }}</small>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($log->date)->toFormattedDateString() }}</td>
                        <td class="fw-bold text-success">{{ $log->amount_liters }} L</td>
                        <td class="fw-bold">₱{{ number_format($log->cost, 2) }}</td>
                        <td>{{ number_format($log->odometer_reading, 1) }} km</td>
                        <td>{{ $log->fuel_type }}</td>
                        <td>
                            @if($log->trip_id)
                                <span class="badge bg-info text-dark">Trip #{{ $log->trip_id }}</span>
                            @else
                                <span class="badge bg-secondary">Manual refill</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">No fuel transactions logged yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Log Fuel Modal -->
<div class="modal fade" id="logFuelModal" tabindex="-1" aria-labelledby="logFuelLabel" aria-hidden="true">
    <div class="modal-dialog rounded-4 overflow-hidden">
        <div class="modal-content border-0">
            <form action="{{ route('fuel.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-success text-white border-0">
                    <h5 class="modal-title fw-bold" id="logFuelLabel">Log Fuel Transaction</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label" style="font-weight: 500;">Select Vehicle</label>
                            <select name="vehicle_id" class="form-select rounded-3" required>
                                <option value="" disabled selected>-- Choose Vehicle --</option>
                                @foreach($vehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}">{{ $vehicle->make }} {{ $vehicle->model }} ({{ $vehicle->license_plate }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-weight: 500;">Transaction Date</label>
                            <input type="date" name="date" class="form-control rounded-3" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-weight: 500;">Fuel Type</label>
                            <select name="fuel_type" class="form-select rounded-3" required>
                                <option value="Gasoline" selected>Gasoline</option>
                                <option value="Diesel">Diesel</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-weight: 500;">Amount Liters</label>
                            <input type="number" step="0.01" name="amount_liters" placeholder="e.g. 35.5" class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-weight: 500;">Total Cost (PHP)</label>
                            <input type="number" step="0.01" name="cost" placeholder="e.g. 2100" class="form-control rounded-3" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label" style="font-weight: 500;">Odometer Reading (km)</label>
                            <input type="number" step="0.1" name="odometer_reading" placeholder="e.g. 15200.5" class="form-control rounded-3" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="button" class="btn btn-outline-secondary rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-3">Save Log</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function runPredictionTest(e) {
        e.preventDefault();

        const distance = document.getElementById('testDistance').value;
        const speed = document.getElementById('testSpeed').value;
        const vehicle_type = document.getElementById('testVehicleType').value;
        const actual_fuel = document.getElementById('testActualFuel').value;

        fetch("{{ route('fuel.predict') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ distance, speed, vehicle_type, actual_fuel })
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById('placeholderResult').classList.add('d-none');
            
            // Populate
            document.getElementById('resultPredictedLiters').innerText = data.predicted_fuel.toFixed(2) + " L";
            document.getElementById('resultCost').innerText = "₱" + (data.predicted_fuel * 62.50).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
            
            // Insights list
            let insightsText = "";
            data.insights.forEach(insight => {
                insightsText += `• ${insight}<br>`;
            });
            document.getElementById('resultEfficiencyInsights').innerHTML = insightsText;

            // Recommendations
            let recsText = "";
            data.recommendations.forEach(rec => {
                recsText += `<li>${rec}</li>`;
            });
            document.getElementById('resultRecommendations').innerHTML = recsText;

            document.getElementById('actualResultPanel').classList.remove('d-none');
        })
        .catch(err => console.error(err));
    }
</script>
@endsection
