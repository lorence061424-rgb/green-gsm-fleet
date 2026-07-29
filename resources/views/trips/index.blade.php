@extends('layouts.app')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col">
        <h2 class="page-header-title">Scheduling & Live Tracking Dispatch</h2>
        <p class="page-header-subtitle">Auto-dispatch available units, plan optimized eco-routes, and monitor live trips.</p>
    </div>
</div>

<div class="row g-4">
    <!-- Left Panel: Trip Planner Form -->
    <div class="col-md-5">
        <div class="card premium-card p-4 h-100">
            <h5 class="fw-bold mb-3"><i class="bi bi-compass-fill text-primary me-2"></i> Dispatch & Schedule Trip</h5>
            
            <form action="{{ route('trips.store') }}" method="POST" id="tripForm">
                @csrf
                <div class="mb-3">
                    <label class="form-label" style="font-weight: 500;">Start Location Hub</label>
                    <select name="start_location" id="start_location" class="form-select rounded-3" required>
                        <option value="" disabled selected>-- Select Origin --</option>
                        @foreach($hubs as $name => $coords)
                            <option value="{{ $name }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label" style="font-weight: 500;">Destination Location Hub</label>
                    <select name="end_location" id="end_location" class="form-select rounded-3" required>
                        <option value="" disabled selected>-- Select Destination --</option>
                        @foreach($hubs as $name => $coords)
                            <option value="{{ $name }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label" style="font-weight: 500;">VinFast EV Category (AI Energy Estimation)</label>
                    <select name="vehicle_type" id="vehicle_type" class="form-select rounded-3" required>
                        <option value="Sedan" selected>Nerio Green (EV Sedan)</option>
                        <option value="SUV">VinFast VF 8 / VF 9 (EV SUV)</option>
                        <option value="Crossover">VinFast VF e34 (EV Crossover)</option>
                        <option value="Hatchback">VinFast VF 5 (EV Compact)</option>
                    </select>
                </div>

                <!-- Hidden inputs populated by AJAX route planning preview -->
                <input type="hidden" name="distance_km" id="distance_km">
                <input type="hidden" name="estimated_duration_minutes" id="estimated_duration_minutes">
                <input type="hidden" name="estimated_fuel_liters" id="estimated_fuel_liters">

                <!-- Route Optimization Preview Card (Dynamically updated via AJAX) -->
                <div id="routePreviewCard" class="card border-0 rounded-4 bg-light p-3 mb-3 d-none">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-uppercase text-muted fw-bold" style="font-size: 11px;">Green GSM Optimized Path</span>
                        <span id="ecoBadge" class="badge bg-success rounded-pill d-none"><i class="bi bi-leaf-fill"></i> Zero-Emission Route</span>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <small class="text-muted d-block" style="font-size: 11px;">Distance</small>
                            <span class="fw-bold text-dark" id="previewDistance">0.0 km</span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block" style="font-size: 11px;">Estimated Energy</small>
                            <span class="fw-bold text-success" id="previewFuel">0.0 kWh</span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block" style="font-size: 11px;">Estimated Duration</small>
                            <span class="fw-bold text-dark" id="previewDuration">0 mins</span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block" style="font-size: 11px;">Congestion Level</small>
                            <span class="fw-bold text-danger" id="previewCongestion">Clear</span>
                        </div>
                    </div>
                    <div class="border-top pt-2" id="routingSuggestions" style="font-size: 12px; color: var(--text-light);">
                        <!-- Eco advice generated here -->
                    </div>
                </div>

                <!-- Assignment Section -->
                <div class="border-top pt-3 mt-3">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" role="switch" id="autoAssignSwitch" name="auto_assign" value="1" checked>
                        <label class="form-check-label fw-bold text-primary" for="autoAssignSwitch">Auto-Assign Vehicle & Driver</label>
                    </div>

                    <div id="manualAssignmentFields" class="d-none">
                        <div class="mb-3">
                            <label class="form-label" style="font-weight: 500;">Assign Vehicle</label>
                            <select name="vehicle_id" class="form-select rounded-3">
                                <option value="" selected>-- Select Vehicle --</option>
                                @foreach($vehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}">{{ $vehicle->make }} {{ $vehicle->model }} ({{ $vehicle->license_plate }} - {{ $vehicle->type }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" style="font-weight: 500;">Assign Driver</label>
                            <select name="driver_id" class="form-select rounded-3">
                                <option value="" selected>-- Select Driver --</option>
                                @foreach($drivers as $driver)
                                    <option value="{{ $driver->id }}">{{ $driver->user->name }} (Score: {{ $driver->performance_score }}%)</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-premium w-100 rounded-3 mt-2" id="btnSubmitTrip">
                    <i class="bi bi-calendar-check me-1"></i> Dispatch Trip
                </button>
            </form>
        </div>
    </div>

    <!-- Right Panel: Active and Scheduled Trips -->
    <div class="col-md-7">
        <div class="card premium-card p-4 h-100">
            <h5 class="fw-bold mb-4"><i class="bi bi-list-task text-primary me-2"></i> Trips Management Registry</h5>
            
            <div class="list-group list-group-flush" style="max-height: 520px; overflow-y: auto;">
                @forelse($trips as $trip)
                    <div class="list-group-item px-0 py-3 border-0 border-bottom">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="badge bg-secondary mb-2" style="font-size: 10px;">{{ $trip->booking_reference_id }}</span>
                                <h6 class="fw-bold mb-1 text-dark">
                                    {{ $trip->start_location }} <i class="bi bi-arrow-right mx-1 text-primary"></i> {{ $trip->end_location }}
                                </h6>
                                <p class="mb-0 text-muted" style="font-size: 13px;">
                                    Distance: <strong>{{ $trip->distance_km }} km</strong> | Est. Fuel: <strong class="text-success">{{ $trip->estimated_fuel_liters }} L</strong>
                                </p>
                                <p class="mb-0 text-muted" style="font-size: 12px;">
                                    Vehicle: {{ $trip->vehicle ? $trip->vehicle->license_plate . ' (' . $trip->vehicle->type . ')' : 'None' }} | Driver: {{ $trip->driver->user->name ?? 'None' }}
                                </p>
                            </div>

                            <div>
                                @if($trip->status === 'scheduled')
                                    <form action="{{ route('trips.start', $trip) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success rounded-3 px-3">
                                            <i class="bi bi-play-fill me-1"></i> Start Trip
                                        </button>
                                    </form>
                                @elseif($trip->status === 'active')
                                    <button class="btn btn-sm btn-danger rounded-3 px-3 animate-pulse" onclick="launchTelemetrySimulator({{ $trip->id }}, '{{ $trip->start_location }}', '{{ $trip->end_location }}', {{ json_encode($trip->vehicle->type ?? 'Sedan') }}, {{ $trip->distance_km }}, {{ $trip->estimated_fuel_liters }});">
                                        <i class="bi bi-radar me-1"></i> Live Simulator
                                    </button>
                                @elseif($trip->status === 'completed')
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2">
                                        <i class="bi bi-check-circle-fill me-1"></i> Completed
                                    </span>
                                    <small class="d-block text-muted text-end mt-1" style="font-size: 11px;">Final: {{ $trip->actual_fuel_liters }} L</small>
                                @else
                                    <span class="badge bg-secondary rounded-pill">{{ ucfirst($trip->status) }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="bi bi-geo-alt-fill text-muted fs-1 mb-2"></i>
                        <p class="text-muted">No scheduled trips. Set up a route on the left to begin.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Telemetry Simulation & GPS Tracking Widget Modal -->
<div class="modal fade" id="telemetrySimulatorModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="telemetryLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg rounded-4 overflow-hidden">
        <div class="modal-content border-0">
            <div class="modal-header bg-dark text-white border-0">
                <h5 class="modal-title fw-bold" id="telemetryLabel"><i class="bi bi-compass-fill text-info me-2"></i> Live Fleet Telemetry Simulator</h5>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-danger rounded-pill px-3 py-1">GPS Broadcast Live</span>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>

            <div class="modal-body p-4 bg-light">
                <div class="row g-3">
                    <!-- Interactive Leaflet Live GPS Map -->
                    <div class="col-12">
                        <div class="card border-0 rounded-4 overflow-hidden shadow-sm" style="height: 230px; position: relative;">
                            <div id="liveGpsMap" style="width: 100%; height: 100%; z-index: 1; min-height: 230px;"></div>
                            <div class="position-absolute top-0 end-0 m-2 bg-dark bg-opacity-80 text-white px-3 py-1 rounded-pill small shadow-sm" style="z-index: 10; font-size: 11px; backdrop-filter: blur(4px);">
                                <span class="spinner-grow spinner-grow-sm text-success me-1" role="status"></span>
                                <span class="fw-bold text-success">LIVE METRO MANILA GPS</span>
                            </div>
                        </div>
                    </div>

                    <!-- Progress and Speedometers -->
                    <div class="col-md-7">
                        <div class="card border-0 rounded-4 p-3 shadow-sm mb-3">
                            <h6 class="fw-bold mb-3"><i class="bi bi-graph-up-arrow me-1"></i> Telemetry & Speed Monitor</h6>
                            <div class="row text-center mb-3">
                                <div class="col-4 border-end">
                                    <small class="text-muted d-block" style="font-size: 11px;">Current Speed</small>
                                    <h4 class="fw-bold mb-0 text-primary" id="simSpeed">0 <span class="fs-6 fw-normal">km/h</span></h4>
                                </div>
                                <div class="col-4 border-end">
                                    <small class="text-muted d-block" style="font-size: 11px;">Idle Duration</small>
                                    <h4 class="fw-bold mb-0 text-warning" id="simIdle">0s</h4>
                                </div>
                                <div class="col-4">
                                    <small class="text-muted d-block" style="font-size: 11px;">Safety Rating</small>
                                    <h4 class="fw-bold mb-0 text-success" id="simSafetyScore">100%</h4>
                                </div>
                            </div>

                            <label class="form-label text-muted" style="font-size: 12px; font-weight: 500;">Route Completion Progress</label>
                            <div class="progress mb-2 rounded-pill" style="height: 12px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%" id="simProgressBar"></div>
                            </div>
                            <div class="d-flex justify-content-between text-muted" style="font-size: 11px;">
                                <span id="simStartHub">Start</span>
                                <span id="simEndHub">End</span>
                            </div>
                        </div>

                        <!-- Safety events and Alerts logs -->
                        <div class="card border-0 rounded-4 p-3 shadow-sm" style="height: 180px; overflow-y: auto;">
                            <h6 class="fw-bold mb-2 text-danger"><i class="bi bi-exclamation-triangle me-1"></i> Harsh Events & Driver Behavior Feed</h6>
                            <div class="list-group list-group-flush" id="telemetryFeed" style="font-size: 12.5px;">
                                <span class="text-muted text-center py-4">Waiting for telemetry logs to stream...</span>
                            </div>
                        </div>
                    </div>

                    <!-- AI Fuel Estimator comparison during ride -->
                    <div class="col-md-5">
                        <div class="card border-0 rounded-4 p-3 shadow-sm h-100 bg-secondary text-white">
                            <h6 class="fw-bold mb-3"><i class="bi bi-cpu me-1"></i> AI Consumption Estimator</h6>
                            <div class="mb-3">
                                <small class="text-white-50 d-block" style="font-size: 11px;">AI Predicted Fuel</small>
                                <h3 class="fw-bold text-info" id="simPredictedFuel">0.0 Liters</h3>
                            </div>
                            <div class="mb-3">
                                <small class="text-white-50 d-block" style="font-size: 11px;">Estimated Actual Consumption</small>
                                <h3 class="fw-bold text-warning" id="simRealtimeFuel">0.0 Liters</h3>
                            </div>
                            <div class="border-top border-secondary pt-3 mt-3">
                                <h6 class="fw-bold" style="font-size: 13px;">Safety Recommendations:</h6>
                                <ul class="ps-3 mb-0 text-white-50" style="font-size: 11.5px;">
                                    <li>Optimal driving speed: 60-80 km/h.</li>
                                    <li>Severe engine idling wastes 0.05L/min.</li>
                                    <li>Aggressive driving reduces safety score.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-3 bg-light d-flex justify-content-between">
                <div>
                    <button class="btn btn-outline-danger btn-sm rounded-3 me-2" id="btnTriggerAggressive" onclick="triggerAggressiveAction();" disabled>
                        <i class="bi bi-lightning-charge-fill"></i> Trigger Aggressive Speeding
                    </button>
                    <button class="btn btn-outline-warning btn-sm rounded-3" id="btnTriggerHarshBrake" onclick="triggerHarshBrakeAction();" disabled>
                        <i class="bi bi-x-circle-fill"></i> Trigger Harsh Brake
                    </button>
                </div>
                <button class="btn btn-premium rounded-3" id="btnSimulateControl" onclick="startGpsStreaming();">
                    <i class="bi bi-play-circle-fill me-1"></i> Run Telemetry Sim
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal complete trip forms -->
<div class="modal fade" id="completeTripModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog rounded-4 overflow-hidden">
        <div class="modal-content border-0">
            <form id="completeTripForm" method="POST">
                @csrf
                <div class="modal-header bg-success text-white border-0">
                    <h5 class="modal-title fw-bold"><i class="bi bi-check-circle-fill me-2"></i> Finalize and Complete Ride</h5>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted" style="font-size: 13.5px;">
                        The ride has successfully completed the simulated coordinate path. Please review and log the final fuel details.
                    </p>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 500;">Actual Fuel Consumed (Liters)</label>
                        <input type="number" step="0.01" name="actual_fuel_liters" id="finalFuelLiters" class="form-control rounded-3" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 500;">Actual Duration (Minutes)</label>
                        <input type="number" name="actual_duration_minutes" id="finalDurationMinutes" class="form-control rounded-3" required>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="submit" class="btn btn-success w-100 rounded-3">
                        <i class="bi bi-flag-fill me-1"></i> Save Log & Complete Trip
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Toggle manual and auto assignment forms
    document.getElementById('autoAssignSwitch').addEventListener('change', function() {
        const manualFields = document.getElementById('manualAssignmentFields');
        if (this.checked) {
            manualFields.classList.add('d-none');
            document.querySelectorAll('#manualAssignmentFields select').forEach(select => select.removeAttribute('required'));
        } else {
            manualFields.classList.remove('d-none');
            document.querySelectorAll('#manualAssignmentFields select').forEach(select => select.setAttribute('required', 'required'));
        }
    });

    // Handle AJAX preview of routes and fuel prediction
    const startSelect = document.getElementById('start_location');
    const endSelect = document.getElementById('end_location');
    const typeSelect = document.getElementById('vehicle_type');

    function checkRoutePreview() {
        const start = startSelect.value;
        const end = endSelect.value;
        const type = typeSelect.value;

        if (start && end) {
            if (start === end) {
                alert("Start and destination hubs cannot be the same.");
                endSelect.value = "";
                return;
            }

            // Immediately enable Dispatch button & set fallback metrics
            document.getElementById('distance_km').value = "14.5";
            document.getElementById('estimated_duration_minutes').value = "28";
            document.getElementById('estimated_fuel_liters').value = "2.4";
            document.getElementById('btnSubmitTrip').removeAttribute('disabled');

            // Perform fetch call to get preview path and AI estimation
            fetch("{{ route('trips.plan-preview') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ start, end, vehicle_type: type })
            })
            .then(res => res.json())
            .then(data => {
                if (data.routes && data.routes.length > 0) {
                    const bestRoute = data.routes[0]; // sorted by fuel usage

                    // Update form values
                    document.getElementById('distance_km').value = bestRoute.distance_km;
                    document.getElementById('estimated_duration_minutes').value = bestRoute.duration_minutes;
                    document.getElementById('estimated_fuel_liters').value = bestRoute.estimated_fuel;

                    // Display Preview Details
                    document.getElementById('previewDistance').innerText = bestRoute.distance_km + " km";
                    document.getElementById('previewFuel').innerText = bestRoute.estimated_fuel + " kWh";
                    document.getElementById('previewDuration').innerText = bestRoute.duration_minutes + " mins";
                    
                    const congestionElement = document.getElementById('previewCongestion');
                    congestionElement.innerText = bestRoute.congestion;
                    if (bestRoute.congestion === 'Heavy') {
                        congestionElement.className = "fw-bold text-danger";
                    } else if (bestRoute.congestion === 'Moderate') {
                        congestionElement.className = "fw-bold text-warning";
                    } else {
                        congestionElement.className = "fw-bold text-success";
                    }

                    // Eco badge
                    const ecoBadge = document.getElementById('ecoBadge');
                    if (bestRoute.is_eco) {
                        ecoBadge.classList.remove('d-none');
                    } else {
                        ecoBadge.classList.add('d-none');
                    }

                    // Suggestions text
                    const suggestionsPanel = document.getElementById('routingSuggestions');
                    suggestionsPanel.innerHTML = `<strong>Route Selected:</strong> ${bestRoute.name}<br>`;
                    if (bestRoute.congestion === 'Heavy') {
                        suggestionsPanel.innerHTML += `<span class="text-danger"><i class="bi bi-info-circle"></i> High idling risk. AI estimates 15% battery energy spike due to traffic.</span>`;
                    } else {
                        suggestionsPanel.innerHTML += `<span class="text-success"><i class="bi bi-check-circle"></i> Clear road. Drivers can cruise at 70 km/h for optimal EV efficiency.</span>`;
                    }

                    document.getElementById('routePreviewCard').classList.remove('d-none');
                    document.getElementById('btnSubmitTrip').removeAttribute('disabled');
                }
            })
            .catch(err => console.error(err));
        }
    }

    startSelect.addEventListener('change', checkRoutePreview);
    endSelect.addEventListener('change', checkRoutePreview);
    typeSelect.addEventListener('change', checkRoutePreview);


    // ==========================================
    // GPS / Telemetry Live Simulator Logic
    // ==========================================
    let simTripId = null;
    let simStart = "";
    let simEnd = "";
    let simDistance = 0.0;
    let simPredictedFuel = 0.0;
    let simVehicleType = "";

    // Leaflet Live GPS Map Objects
    let leafletMap = null;
    let carMarker = null;
    let polylineTrail = null;

    const hubCoords = {
        'Manila Hub (Port Area)': [14.5995, 120.9842],
        'Makati Hub (Ayala Ave)': [14.5547, 121.0244],
        'BGC Taguig Hub (9th Ave)': [14.5515, 121.0510],
        'Pasay Hub (MOA Complex)': [14.5352, 120.9820],
        'Quezon City Hub (Cubao)': [14.6178, 121.0572]
    };

    // Simulated path arrays (simple latitude/longitude interpolation arrays)
    let simulatedPath = [];
    let currentStepIndex = 0;
    let activeSimulationInterval = null;
    let safetyScoreTracker = 100;
    
    // Telemetry tracking stats
    let totalIdleSeconds = 0;
    let computedTripFuel = 0.0;
    let currentVehicleSpeed = 50; 
    let aggressiveSpeedTrigger = false;
    let harshBrakeTrigger = false;

    function initLeafletGpsMap(startName, endName) {
        const mapContainer = document.getElementById('liveGpsMap');
        if (!mapContainer) return;

        const startLatLng = hubCoords[startName] || [14.5995, 120.9842];
        const endLatLng = hubCoords[endName] || [14.5547, 121.0244];

        if (!leafletMap) {
            leafletMap = L.map('liveGpsMap', { zoomControl: false }).setView(startLatLng, 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap &bull; Green GSM Telemetry'
            }).addTo(leafletMap);
        } else {
            leafletMap.setView(startLatLng, 13);
        }

        // Clear existing markers & lines if present
        if (carMarker) leafletMap.removeLayer(carMarker);
        if (polylineTrail) leafletMap.removeLayer(polylineTrail);

        // Start & Destination Hub Markers
        L.circleMarker(startLatLng, { color: '#10B981', radius: 8, fillColor: '#10B981', fillOpacity: 0.9 }).addTo(leafletMap).bindPopup("<b>Start Hub:</b> " + startName);
        L.circleMarker(endLatLng, { color: '#EF4444', radius: 8, fillColor: '#EF4444', fillOpacity: 0.9 }).addTo(leafletMap).bindPopup("<b>Destination:</b> " + endName);

        // Polyline Trail
        polylineTrail = L.polyline([startLatLng], { color: '#10B981', weight: 4, opacity: 0.85, dashArray: '6, 6' }).addTo(leafletMap);

        // Custom Leaflet EV Icon
        const carIcon = L.divIcon({
            className: 'custom-ev-marker',
            html: `<div style="background:#064E3B; border:2px solid #10B981; border-radius:50%; width:32px; height:32px; display:flex; align-items:center; justify-content:center; box-shadow:0 0 12px rgba(16,185,129,0.8);"><i class="bi bi-ev-front-fill text-white fs-6"></i></div>`,
            iconSize: [32, 32],
            iconAnchor: [16, 16]
        });

        carMarker = L.marker(startLatLng, { icon: carIcon }).addTo(leafletMap).bindPopup("<b>VinFast EV Live Position</b>");

        setTimeout(() => { if (leafletMap) leafletMap.invalidateSize(); }, 350);
    }

    function launchTelemetrySimulator(tripId, start, end, type, distance, predictedFuel) {
        simTripId = tripId;
        simStart = start;
        simEnd = end;
        simDistance = distance;
        simPredictedFuel = predictedFuel;
        simVehicleType = type;

        // Reset variables
        currentStepIndex = 0;
        totalIdleSeconds = 0;
        computedTripFuel = 0.0;
        safetyScoreTracker = 100;
        aggressiveSpeedTrigger = false;
        harshBrakeTrigger = false;

        document.getElementById('simStartHub').innerText = start;
        document.getElementById('simEndHub').innerText = end;
        document.getElementById('simProgressBar').style.width = "0%";
        document.getElementById('simPredictedFuel').innerText = predictedFuel.toFixed(2) + " kWh";
        document.getElementById('simRealtimeFuel').innerText = "0.00 kWh";
        document.getElementById('simSpeed').innerHTML = "0 <span class='fs-6 fw-normal'>km/h</span>";
        document.getElementById('simIdle').innerText = "0s";
        document.getElementById('simSafetyScore').innerText = "100%";
        document.getElementById('telemetryFeed').innerHTML = "<span class='text-muted text-center py-4'>Ready to run simulation stream.</span>";

        // Generate coordinate path
        generateSimulatedPathCoordinates(start, end);

        // Move modal directly to document.body to prevent any stacking context issues
        const simModalElement = document.getElementById('telemetrySimulatorModal');
        if (simModalElement && simModalElement.parentNode !== document.body) {
            document.body.appendChild(simModalElement);
        }
        const simModal = bootstrap.Modal.getOrCreateInstance(simModalElement);
        simModal.show();

        simModalElement.addEventListener('shown.bs.modal', function () {
            initLeafletGpsMap(start, end);
        }, { once: true });
    }

    function generateSimulatedPathCoordinates(startName, endName) {
        simulatedPath = [];
        const startLatLng = hubCoords[startName] || [14.5995, 120.9842];
        const endLatLng = hubCoords[endName] || [14.5547, 121.0244];
        
        const points = 14; // 14 real-time GPS updates

        for (let i = 0; i <= points; i++) {
            let f = i / points;
            simulatedPath.push({
                lat: startLatLng[0] + (endLatLng[0] - startLatLng[0]) * f + (Math.random() - 0.5) * 0.0015,
                lng: startLatLng[1] + (endLatLng[1] - startLatLng[1]) * f + (Math.random() - 0.5) * 0.0015
            });
        }
    }

    function startGpsStreaming() {
        const btn = document.getElementById('btnSimulateControl');
        if (activeSimulationInterval) {
            // Pause
            clearInterval(activeSimulationInterval);
            activeSimulationInterval = null;
            btn.innerHTML = "<i class='bi bi-play-circle-fill me-1'></i> Resume Sim";
            document.getElementById('btnTriggerAggressive').setAttribute('disabled', 'disabled');
            document.getElementById('btnTriggerHarshBrake').setAttribute('disabled', 'disabled');
        } else {
            // Start
            btn.innerHTML = "<i class='bi bi-pause-circle-fill me-1'></i> Pause Sim";
            document.getElementById('btnTriggerAggressive').removeAttribute('disabled');
            document.getElementById('btnTriggerHarshBrake').removeAttribute('disabled');
            
            document.getElementById('telemetryFeed').innerHTML = "";

            activeSimulationInterval = setInterval(streamTelemetryStep, 1500); // send GPS logs every 1.5s
        }
    }

    function triggerAggressiveAction() {
        aggressiveSpeedTrigger = true;
        addSimFeedEvent("Dispatcher triggered: Aggressive Throttle (Speeding Alert)", "danger");
    }

    function triggerHarshBrakeAction() {
        harshBrakeTrigger = true;
        addSimFeedEvent("Dispatcher triggered: Harsh Braking Event", "warning");
    }

    function addSimFeedEvent(message, statusClass) {
        const feed = document.getElementById('telemetryFeed');
        const timeString = new Date().toLocaleTimeString();
        const icon = statusClass === 'danger' ? 'bi-exclamation-triangle-fill' : (statusClass === 'warning' ? 'bi-x-octagon' : 'bi-info-circle');
        const item = `
            <div class="list-group-item text-${statusClass} px-0 border-0 border-bottom d-flex justify-content-between py-2">
                <span><i class="bi ${icon} me-1"></i> ${message}</span>
                <small class="text-muted">${timeString}</small>
            </div>
        `;
        feed.insertAdjacentHTML('afterbegin', item);
    }

    function streamTelemetryStep() {
        if (currentStepIndex >= simulatedPath.length) {
            // Finished path
            clearInterval(activeSimulationInterval);
            activeSimulationInterval = null;
            document.getElementById('btnSimulateControl').setAttribute('disabled', 'disabled');
            
            addSimFeedEvent("Simulated ride path completed. Preparing ride summary reports.", "success");
            
            // Redirect to final completes
            setTimeout(showCompleteFormModal, 1500);
            return;
        }

        const point = simulatedPath[currentStepIndex];
        
        // Update Leaflet Map Marker Position & Polyline Breadcrumb Trail
        if (carMarker) {
            carMarker.setLatLng([point.lat, point.lng]);
        }
        if (polylineTrail) {
            polylineTrail.addLatLng([point.lat, point.lng]);
        }
        if (leafletMap) {
            leafletMap.panTo([point.lat, point.lng], { animate: true, duration: 0.8 });
        }
        
        // Randomize speed based on triggers
        let speed = 40 + Math.floor(Math.random() * 20); // standard
        let idleSec = 0;
        let isHarsh = false;

        if (aggressiveSpeedTrigger) {
            speed = 95 + Math.floor(Math.random() * 15); // speeding
            aggressiveSpeedTrigger = false; 
        } else if (harshBrakeTrigger) {
            speed = 10; // braking
            isHarsh = true;
            harshBrakeTrigger = false;
        } else if (Math.random() > 0.85) {
            // Idle event
            speed = 0;
            idleSec = 15;
            totalIdleSeconds += 15;
        }

        currentVehicleSpeed = speed;

        // Cumulative fuel estimation calculation on UI
        // physical factor estimate
        let baseBurnRate = 0.08; 
        let speedDragMultiplier = 1.0;
        if (speed > 90) speedDragMultiplier = 1.25;
        if (speed === 0) {
            computedTripFuel += 0.015; // idle fuel burn
        } else {
            computedTripFuel += ((simDistance / simulatedPath.length) * baseBurnRate) * speedDragMultiplier;
        }

        // Display updates
        document.getElementById('simSpeed').innerHTML = `${speed} <span class="fs-6 fw-normal">km/h</span>`;
        document.getElementById('simIdle').innerText = `${totalIdleSeconds}s`;
        document.getElementById('simRealtimeFuel').innerText = computedTripFuel.toFixed(2) + " kWh";

        // Progress update
        const pct = Math.round((currentStepIndex / (simulatedPath.length - 1)) * 100);
        document.getElementById('simProgressBar').style.width = pct + "%";

        // Call backend API to record logs dynamically
        fetch(`/trips/${simTripId}/simulate-gps`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                lat: point.lat,
                lng: point.lng,
                speed: speed,
                idle_seconds: idleSec,
                is_harsh_braking: isHarsh
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                safetyScoreTracker = data.current_safety_score;
                document.getElementById('simSafetyScore').innerText = safetyScoreTracker + "%";

                if (speed > 80) {
                    addSimFeedEvent(`Speed Violation: ${speed} km/h recorded. Safety score decreased to ${safetyScoreTracker}%.`, "danger");
                } else if (isHarsh) {
                    addSimFeedEvent(`Safety Trigger: Harsh Braking detected. Safety score decreased to ${safetyScoreTracker}%.`, "warning");
                } else if (speed === 0) {
                    addSimFeedEvent(`Idle State: Vehicle idling at traffic intersection. Fuel burning.`, "warning");
                } else {
                    addSimFeedEvent(`GPS broadcast: Lat ${point.lat.toFixed(4)}, Lng ${point.lng.toFixed(4)}. Cruising smoothly.`, "secondary");
                }
            }
        })
        .catch(err => console.error(err));

        currentStepIndex++;
    }

    function showCompleteFormModal() {
        // Hide Telemetry Modal
        const telemetryModal = bootstrap.Modal.getInstance(document.getElementById('telemetrySimulatorModal'));
        if (telemetryModal) telemetryModal.hide();

        // Populate and open complete trip modal
        const finalFuel = Math.max(0.2, computedTripFuel);
        const finalDuration = Math.round((simDistance / 45) * 60) + Math.round(totalIdleSeconds / 60);

        document.getElementById('finalFuelLiters').value = finalFuel.toFixed(2);
        document.getElementById('finalDurationMinutes').value = finalDuration;

        // Set action route on form dynamically
        const form = document.getElementById('completeTripForm');
        form.action = `/trips/${simTripId}/complete`;

        const completeModalElement = document.getElementById('completeTripModal');
        if (completeModalElement && completeModalElement.parentNode !== document.body) {
            document.body.appendChild(completeModalElement);
        }
        const completeModal = bootstrap.Modal.getOrCreateInstance(completeModalElement);
        completeModal.show();
    }
</script>
@endsection
