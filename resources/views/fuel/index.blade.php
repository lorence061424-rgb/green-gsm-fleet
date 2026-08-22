@extends('layouts.app')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col">
        <h2 class="page-header-title">Fuel & EV Energy Management System</h2>
        <p class="page-header-subtitle">Log EV battery charging, monitor kWh energy metrics, and test AI prediction models.</p>
    </div>
    <div class="col-auto d-flex gap-2 flex-wrap">
        <button class="btn btn-outline-success rounded-3" onclick="exportFuelTableToCSV();">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export CSV
        </button>
        <button class="btn btn-outline-primary rounded-3" data-bs-toggle="modal" data-bs-target="#importFuelCsvModal">
            <i class="bi bi-file-earmark-arrow-up me-1"></i> Import CSV
        </button>
        <button class="btn btn-outline-dark rounded-3" onclick="window.print();">
            <i class="bi bi-printer me-1"></i> Print / PDF
        <button class="btn btn-premium d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#logFuelModal">
            <i class="bi bi-plus-circle me-1"></i> Log EV Charging (kWh)
        </button>
    </div>
</div>

<!-- Inter-System Integration Connections Badge Banner -->
<div class="alert alert-dark bg-dark text-white border-0 rounded-4 p-3 mb-4 shadow-sm">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center">
            <i class="bi bi-diagram-3-fill text-success fs-4 me-2"></i>
            <div>
                <span class="fw-bold d-block text-white small">INTER-SYSTEM INTEGRATION PIPELINE (TEAM 7 &bull; FMS)</span>
                <span class="text-white fw-medium" style="font-size: 11px;">Connected to peer enterprise systems for EV charging expense exports, station bay reservations, and driver energy logs.</span>
            </div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <span class="badge bg-info text-dark fw-bold px-3 py-2"><i class="bi bi-cash-coin me-1"></i> Team 5: Financials Charging AP/GL</span>
            <span class="badge bg-success text-white fw-bold px-3 py-2"><i class="bi bi-ev-station me-1"></i> Team 8: Facilities Charging Bays</span>
            <span class="badge bg-warning text-dark fw-bold px-3 py-2"><i class="bi bi-person-badge me-1"></i> Team 9: Ops Driver Energy Logs</span>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Active Weights Info -->
    <div class="col-md-4">
        <div class="card premium-card p-4 h-100 bg-secondary text-white border-0">
            <h5 class="fw-bold mb-3 text-info"><i class="bi bi-cpu-fill me-1"></i> Active AI Model Weights</h5>
            <p class="text-white-50" style="font-size: 13px;">
                Coefficients trained by Gradient Descent regression engine on historical VinFast EV trip logs.
            </p>
            <div class="mt-3">
                <div class="d-flex justify-content-between border-bottom border-dark py-2">
                    <span class="text-white-50">Intercept (&beta;<sub>0</sub>)</span>
                    <span class="fw-bold text-info">{{ number_format($weights['intercept'] ?? 0.5, 4) }}</span>
                </div>
                <div class="d-flex justify-content-between border-bottom border-dark py-2">
                    <span class="text-white-50">Distance Cost (&beta;<sub>1</sub> per km)</span>
                    <span class="fw-bold text-info">{{ number_format($weights['distance'] ?? 0.12, 4) }}</span>
                </div>
                <div class="d-flex justify-content-between py-2 mb-3">
                    <span class="text-white-50">Speed Penalty (&beta;<sub>2</sub> per km/h)</span>
                    <span class="fw-bold text-info">{{ number_format($weights['speed'] ?? 0.0005, 5) }}</span>
                </div>
            </div>

            <form action="{{ route('fuel.train') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-info w-100 rounded-3 text-dark fw-bold">
                    <i class="bi bi-gear-fill me-1"></i> Re-Train AI EV Model
                </button>
            </form>
        </div>
    </div>

    <!-- AI Energy Consumption Test Simulator Panel -->
    <div class="col-md-8">
        <div class="card premium-card p-4 h-100">
            <h5 class="fw-bold mb-3"><i class="bi bi-calculator text-primary me-2"></i> AI EV Energy Predictor Test Panel</h5>
            
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
                            <label class="form-label" style="font-weight: 500;">VinFast EV Model</label>
                            <select id="testVehicleType" class="form-select rounded-3" required>
                                <option value="Nerio Green" selected>VinFast Nerio Green (EV Sedan - 42 kWh)</option>
                                <option value="VF 8">VinFast VF 8 (EV SUV - 87.7 kWh)</option>
                                <option value="VF e34">VinFast VF e34 (EV Crossover - 42 kWh)</option>
                                <option value="VF 5">VinFast VF 5 (EV Compact - 37.2 kWh)</option>
                                <option value="VF 9">VinFast VF 9 (EV Premium SUV - 92 kWh)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" style="font-weight: 500;">Actual Energy Used (kWh, Optional)</label>
                            <input type="number" step="0.1" id="testActualFuel" class="form-control rounded-3" placeholder="e.g. 1.8">
                        </div>
                        <button type="submit" class="btn btn-premium w-100 rounded-3">
                            <i class="bi bi-cpu me-1"></i> Predict EV kWh Consumption
                        </button>
                    </form>
                </div>

                <!-- Live Results Output -->
                <div class="col-md-6 bg-light bg-opacity-50 p-3 rounded-4 d-flex align-items-center justify-content-center" id="predictResultContainer">
                    <div class="text-center text-muted" id="placeholderResult">
                        <i class="bi bi-lightning-charge fs-1 mb-2 text-success"></i>
                        <h6>Energy Estimation Results</h6>
                        <p class="mb-0 small">Enter trip parameters on the left to estimate battery energy outputs.</p>
                    </div>

                    <div id="actualResultPanel" class="w-100 d-none">
                        <div class="mb-3 text-center">
                            <span class="text-muted text-uppercase fw-bold" style="font-size: 11px;">Estimated Battery Energy</span>
                            <h2 class="fw-bold text-success mt-1 mb-0" id="resultPredictedLiters">0.00 kWh</h2>
                            <small class="text-muted">Charging expense: <strong class="text-dark" id="resultCost">₱0.00</strong></small>
                        </div>
                        
                        <div class="border-top pt-2">
                            <h6 class="fw-bold small text-dark mb-1">AI Route Insights:</h6>
                            <ul class="ps-3 mb-2 small text-muted" id="resultInsightsList"></ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SECTION 2: Enterprise EV Energy Analytics (Tariff Optimizer, Depot Load Balancing & ESG Carbon Avoidance) -->
<div class="row g-4 mb-4">
    <!-- Peak vs Off-Peak Electricity Tariff Analyzer -->
    <div class="col-md-3">
        <div class="card premium-card p-3 h-100 border-0 bg-white shadow-sm">
            <div class="d-flex align-items-center mb-2">
                <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-3 me-2">
                    <i class="bi bi-clock-history fs-5"></i>
                </div>
                <div>
                    <span class="fw-bold d-block text-dark small">Grid Tariff Optimizer</span>
                    <small class="text-muted" style="font-size: 10px;">MERALCO Off-Peak Charging</small>
                </div>
            </div>
            <div class="my-2 border-top border-bottom py-2">
                <div class="d-flex justify-content-between small mb-1">
                    <span class="text-muted">Peak Daytime Rate</span>
                    <strong class="text-danger">₱13.20 / kWh</strong>
                </div>
                <div class="d-flex justify-content-between small">
                    <span class="text-muted">Off-Peak Overnight Rate</span>
                    <strong class="text-success">₱8.50 / kWh</strong>
                </div>
            </div>
            <div class="mt-1">
                <small class="text-muted d-block" style="font-size: 11px;">ESTIMATED MONTHLY SAVINGS</small>
                <strong class="text-success fs-5">₱18,450.00</strong>
                <small class="text-success d-block" style="font-size: 10px;"><i class="bi bi-arrow-down-right-circle me-1"></i> 35.6% Smart Tariff Shift</small>
            </div>
        </div>
    </div>

    <!-- Depot Load Balancing & Power Allocation -->
    <div class="col-md-3">
        <div class="card premium-card p-3 h-100 border-0 bg-white shadow-sm">
            <div class="d-flex align-items-center mb-2">
                <div class="bg-warning bg-opacity-10 text-warning p-2 rounded-3 me-2">
                    <i class="bi bi-lightning-charge fs-5"></i>
                </div>
                <div>
                    <span class="fw-bold d-block text-dark small">Depot Power Load Balancer</span>
                    <small class="text-muted" style="font-size: 10px;">Active Charging Bays</small>
                </div>
            </div>
            <div class="my-2">
                <div class="d-flex justify-content-between small mb-1">
                    <span class="text-muted">Current Power Draw</span>
                    <strong class="text-dark">142 kW / 200 kW</strong>
                </div>
                <div class="progress rounded-pill mb-2" style="height: 8px;">
                    <div class="progress-bar bg-warning" style="width: 71%;"></div>
                </div>
                <div class="d-flex justify-content-between text-muted" style="font-size: 11px;">
                    <span>AC Slow: 22 kW</span>
                    <span>DC Fast: 120 kW</span>
                </div>
            </div>
            <span class="badge bg-success text-white mt-1 small"><i class="bi bi-shield-check me-1"></i> Peak Demand Surge Avoided</span>
        </div>
    </div>

    <!-- Battery Degradation & State of Health Forecaster -->
    <div class="col-md-3">
        <div class="card premium-card p-3 h-100 border-0 bg-white shadow-sm">
            <div class="d-flex align-items-center mb-2">
                <div class="bg-success bg-opacity-10 text-success p-2 rounded-3 me-2">
                    <i class="bi bi-battery-charging fs-5"></i>
                </div>
                <div>
                    <span class="fw-bold d-block text-dark small">Battery Lifecycle Forecaster</span>
                    <small class="text-muted" style="font-size: 10px;">State-of-Health Curve</small>
                </div>
            </div>
            <div class="my-2 border-top border-bottom py-2">
                <div class="d-flex justify-content-between small mb-1">
                    <span class="text-muted">Current Pack SoH</span>
                    <strong class="text-success">98.4% Health</strong>
                </div>
                <div class="d-flex justify-content-between small">
                    <span class="text-muted">Charge Cycle Count</span>
                    <strong class="text-dark">342 / 1,500 Cycles</strong>
                </div>
            </div>
            <div class="mt-1">
                <small class="text-muted d-block" style="font-size: 11px;">ESTIMATED REMAINING LIFE</small>
                <strong class="text-primary fs-6">6.8 Years (185,000 km)</strong>
            </div>
        </div>
    </div>

    <!-- ESG Carbon Avoidance & Sustainability Scorecard -->
    <div class="col-md-3">
        <div class="card premium-card p-3 h-100 border-0 bg-white shadow-sm">
            <div class="d-flex align-items-center mb-2">
                <div class="bg-info bg-opacity-10 text-info p-2 rounded-3 me-2">
                    <i class="bi bi-tree fs-5 text-success"></i>
                </div>
                <div>
                    <span class="fw-bold d-block text-dark small">ESG CO₂ Sustainability</span>
                    <small class="text-muted" style="font-size: 10px;">Emissions Offset Metrics</small>
                </div>
            </div>
            <div class="my-2">
                <small class="text-muted d-block" style="font-size: 11px;">CO₂ EMISSIONS AVOIDED</small>
                <h3 class="fw-bold text-success my-1">12.4 <small class="fs-6 text-dark">Tons CO₂</small></h3>
                <small class="text-muted" style="font-size: 11px;">vs ICE Gasoline Fleet</small>
            </div>
            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-30 small mt-1">
                <i class="bi bi-tree-fill me-1"></i> Equivalent to 540 Trees Planted
            </span>
        </div>
    </div>
</div>

<!-- Recent Charging & Energy Logs Table -->
<div class="card premium-card p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0"><i class="bi bi-lightning-charge-fill text-success me-2"></i> EV Charging & Energy Logs</h5>
        <span class="badge bg-success rounded-pill px-3 py-1">Metro Manila Grid Rate: ₱11.50/kWh</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle" id="fuelLogsTable">
            <thead>
                <tr class="text-muted" style="font-size: 12px; font-weight: 700;">
                    <th>DATE</th>
                    <th>VINFAST EV UNIT</th>
                    <th>CHARGED BY DRIVER</th>
                    <th>ENERGY ADDED (kWh)</th>
                    <th>CHARGING COST (₱)</th>
                    <th>ODOMETER (KM)</th>
                    <th>CHARGING TYPE</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td style="font-size: 13px;">{{ \Carbon\Carbon::parse($log->date)->toFormattedDateString() }}</td>
                        <td>
                            <strong class="text-dark d-block" style="font-size: 14px;">{{ $log->vehicle->make }} {{ $log->vehicle->model }}</strong>
                            <small class="badge bg-dark" style="font-size: 10px;">{{ $log->vehicle->license_plate }}</small>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 text-primary fw-bold rounded-circle p-2 me-2 d-flex align-items-center justify-content-center" style="width:32px; height:32px; font-size:12px;">
                                    {{ strtoupper(substr($log->trip->driver->user->name ?? 'J', 0, 1)) }}
                                </div>
                                <div>
                                    <strong class="d-block text-dark small">{{ $log->trip->driver->user->name ?? 'Juan Dela Cruz' }}</strong>
                                    <small class="text-muted" style="font-size: 11px;">License: {{ $log->trip->driver->license_number ?? 'N01-18-99201' }}</small>
                                </div>
                            </div>
                        </td>
                        <td class="fw-bold text-success" style="font-size: 14px;">
                            <i class="bi bi-lightning-charge me-1"></i> {{ number_format($log->amount_liters, 2) }} kWh
                        </td>
                        <td class="fw-bold text-dark" style="font-size: 14px;">₱{{ number_format($log->cost, 2) }}</td>
                        <td style="font-size: 13px;">{{ number_format($log->odometer_reading, 1) }} km</td>
                        <td>
                            <span class="badge bg-primary text-white px-3 py-2 rounded-3 shadow-sm fw-bold">
                                <i class="bi bi-ev-station me-1"></i> {{ $log->fuel_type ?: 'DC Fast Charge (kWh)' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No charging logs recorded.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Log Refill -->
<div class="modal fade" id="logFuelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog rounded-4 overflow-hidden">
        <div class="modal-content border-0">
            <form action="{{ route('fuel.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-success text-white border-0">
                    <h5 class="modal-title fw-bold"><i class="bi bi-lightning-charge-fill me-2"></i> Log EV Charging Event</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 500;">Select VinFast EV Unit</label>
                        <select name="vehicle_id" class="form-select rounded-3" required>
                            <option value="" disabled selected>-- Select Vehicle --</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}">{{ $vehicle->make }} {{ $vehicle->model }} ({{ $vehicle->license_plate }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 500;">Charged By Driver (Team 9 Synced)</label>
                        <select name="driver_id" class="form-select rounded-3">
                            @foreach($drivers as $drv)
                                <option value="{{ $drv->id }}">{{ $drv->user->name ?? 'Driver' }} (License: {{ $drv->license_number }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 500;">Date</label>
                        <input type="date" name="date" class="form-control rounded-3" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-weight: 500;">Energy Added (kWh)</label>
                            <input type="number" step="0.01" name="amount_liters" class="form-control rounded-3" placeholder="e.g. 35.5" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-weight: 500;">Charging Cost (₱)</label>
                            <input type="number" step="0.01" name="cost" class="form-control rounded-3" placeholder="e.g. 408.25" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 500;">Odometer Reading (km)</label>
                        <input type="number" step="0.1" name="odometer_reading" class="form-control rounded-3" placeholder="e.g. 12580" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 500;">Fuel / Energy Station Type</label>
                        <select name="fuel_type" class="form-select rounded-3" required>
                            <option value="Gasoline Refueling" selected>⛽ Gasoline Refueling (Hirna Station)</option>
                            <option value="Diesel Refueling">🛢️ Diesel Refueling (Hirna Station)</option>
                            <option value="Fast DC EV Charging">⚡ Fast DC EV Charging (Hirna Depot)</option>
                            <option value="AC Level 2 Depot Charging">🔌 AC Level 2 Depot Charging</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="submit" class="btn btn-success w-100 rounded-3">
                        <i class="bi bi-save me-1"></i> Save Charging Record
                    </button>
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
    const type = document.getElementById('testVehicleType').value;
    const actualFuel = document.getElementById('testActualFuel').value;

    fetch("{{ route('fuel.predict') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ distance, speed, vehicle_type: type, actual_fuel: actualFuel })
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById('placeholderResult').classList.add('d-none');
        document.getElementById('actualResultPanel').classList.remove('d-none');

        const kWh = data.predicted_fuel;
        document.getElementById('resultPredictedLiters').innerText = kWh.toFixed(2) + " kWh";
        document.getElementById('resultCost').innerText = "₱" + (kWh * 11.50).toFixed(2);

        const list = document.getElementById('resultInsightsList');
        list.innerHTML = "";
        data.insights.forEach(insight => {
            list.innerHTML += `<li>${insight}</li>`;
        });
    })
    .catch(err => console.error(err));
}

function exportFuelTableToCSV() {
    let csv = [];
    const rows = document.querySelectorAll("#fuelLogsTable tr");
    
    for (let i = 0; i < rows.length; i++) {
        let row = [], cols = rows[i].querySelectorAll("td, th");
        for (let j = 0; j < cols.length; j++) 
            row.push('"' + cols[j].innerText.replace(/"/g, '""').trim() + '"');
        csv.push(row.join(","));
    }

    const csvFile = new Blob([csv.join("\n")], {type: "text/csv"});
    const downloadLink = document.createElement("a");
    downloadLink.download = "Green_GSM_EV_Energy_Logs.csv";
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = "none";
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}
</script>

<!-- Import EV Fuel Logs CSV Modal -->
<div class="modal fade" id="importFuelCsvModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog rounded-4 overflow-hidden">
        <div class="modal-content border-0">
            <form action="{{ route('import.csv') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="module_type" value="fuel">
                <div class="modal-header bg-success text-white border-0">
                    <h5 class="modal-title fw-bold"><i class="bi bi-file-earmark-arrow-up me-2"></i> Import EV Charging Logs (CSV)</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 500;">Select CSV File (.csv)</label>
                        <input type="file" name="csv_file" accept=".csv, .txt" class="form-control rounded-3" required>
                        <small class="text-muted mt-1 d-block">Expected columns: Date, Vehicle Plate, Energy (kWh), Cost (₱), Odometer (km), Charging Type.</small>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="button" class="btn btn-outline-secondary rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-3"><i class="bi bi-cloud-upload me-1"></i> Import Charging CSV</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
