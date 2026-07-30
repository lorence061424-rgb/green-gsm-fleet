@extends('layouts.app')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col">
        <h2 class="page-header-title">Route Planning and Optimization</h2>
        <p class="page-header-subtitle">Plan eco-friendly VinFast EV routes, analyze Metro Manila traffic delays, and minimize kWh energy consumption.</p>
    </div>
    <div class="col-auto d-flex gap-2 flex-wrap">
        <button class="btn btn-outline-success rounded-3" onclick="exportRoutesToCSV();">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export CSV
        </button>
        <button class="btn btn-outline-primary rounded-3" data-bs-toggle="modal" data-bs-target="#importRoutesCsvModal">
            <i class="bi bi-file-earmark-arrow-up me-1"></i> Import CSV
        </button>
        <button class="btn btn-outline-dark rounded-3" onclick="window.print();">
            <i class="bi bi-printer me-1"></i> Print / PDF Report
        </button>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Left Panel: Interactive Route Planner -->
    <div class="col-lg-5">
        <div class="card premium-card p-4 h-100">
            <h5 class="fw-bold mb-3"><i class="bi bi-compass-fill text-primary me-2"></i> VinFast EV Route Planner</h5>
            <form id="routePlannerForm" onsubmit="calculateOptimizedRoutes(event);">
                <div class="mb-3">
                    <label class="form-label fw-medium">Origin Hub / Location</label>
                    <select id="routeStart" class="form-select rounded-3" required>
                        @foreach($hubs as $hubName => $coords)
                            <option value="{{ $hubName }}" {{ $loop->first ? 'selected' : '' }}>{{ $hubName }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-medium">Destination Hub / Location</label>
                    <select id="routeEnd" class="form-select rounded-3" required>
                        @foreach($hubs as $hubName => $coords)
                            <option value="{{ $hubName }}" {{ $loop->iteration == 2 ? 'selected' : '' }}>{{ $hubName }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-medium">Select VinFast EV Unit</label>
                    <select id="routeVehicleType" class="form-select rounded-3" required>
                        <option value="Nerio Green" selected>VinFast Nerio Green (EV Sedan - 42 kWh)</option>
                        <option value="VF 8">VinFast VF 8 (EV SUV - 87.7 kWh)</option>
                        <option value="VF e34">VinFast VF e34 (EV Crossover - 42 kWh)</option>
                        <option value="VF 5">VinFast VF 5 (EV Compact - 37.2 kWh)</option>
                        <option value="VF 9">VinFast VF 9 (EV Premium SUV - 92 kWh)</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-premium w-100 py-3 rounded-3 fw-bold">
                    <i class="bi bi-lightning-charge-fill me-1"></i> Calculate Optimized Eco-Routes
                </button>
            </form>

            <div class="mt-4 pt-3 border-top">
                <h6 class="fw-bold small text-dark mb-2">Supported Metro Manila Hubs:</h6>
                <div class="d-flex flex-wrap gap-1">
                    @foreach($hubs as $name => $c)
                        <span class="badge bg-secondary bg-opacity-10 text-dark border px-2 py-1" style="font-size: 11px;">
                            <i class="bi bi-geo-alt-fill text-success me-1"></i>{{ $name }}
                        </span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Right Panel: Interactive Route Map & Optimization Options -->
    <div class="col-lg-7">
        <div class="card premium-card p-4 h-100">
            <h5 class="fw-bold mb-3"><i class="bi bi-map-fill text-success me-2"></i> Live OpenStreetMap Route Visualizer</h5>
            
            <div class="card border-0 rounded-4 overflow-hidden shadow-sm mb-3" style="height: 320px; position: relative;">
                <div id="routeVisualizerMap" style="width: 100%; height: 100%; z-index: 1;"></div>
                <div class="position-absolute top-0 end-0 m-2 bg-dark bg-opacity-80 text-white px-3 py-1 rounded-pill small shadow-sm" style="z-index: 10; font-size: 11px; backdrop-filter: blur(4px);">
                    <span class="spinner-grow spinner-grow-sm text-success me-1" role="status"></span>
                    <span class="fw-bold text-success">METRO MANILA ECO-ROUTING</span>
                </div>
            </div>

            <!-- Route Comparison Options Container -->
            <div id="routeResultsContainer">
                <div class="alert alert-light text-center py-4 border rounded-3 mb-0">
                    <i class="bi bi-compass fs-1 text-primary mb-2 d-block"></i>
                    <h6 class="fw-bold">Ready to Optimize Routes</h6>
                    <p class="small text-muted mb-0">Select an origin and destination on the left to display optimized route options and kWh energy predictions.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Metro Manila Hub Distance Matrix Table -->
<div class="card premium-card p-4">
    <h5 class="fw-bold mb-3"><i class="bi bi-grid-3x3-gap-fill text-info me-2"></i> Metro Manila Hub Distance Matrix</h5>
    <div class="table-responsive">
        <table class="table table-hover align-middle text-center" id="distanceMatrixTable">
            <thead>
                <tr class="text-muted" style="font-size: 12px;">
                    <th class="text-start">HUB LOCATION</th>
                    <th>MANILA</th>
                    <th>MAKATI</th>
                    <th>BGC</th>
                    <th>PASAY</th>
                    <th>NAIA</th>
                    <th>QUEZON CITY</th>
                    <th>ORTIGAS</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-start fw-bold">Manila Hub</td>
                    <td><span class="badge bg-light text-dark">0 km</span></td>
                    <td>10.5 km</td>
                    <td>12.8 km</td>
                    <td>8.2 km</td>
                    <td>11.0 km</td>
                    <td>9.4 km</td>
                    <td>11.2 km</td>
                </tr>
                <tr>
                    <td class="text-start fw-bold">Makati Hub</td>
                    <td>10.5 km</td>
                    <td><span class="badge bg-light text-dark">0 km</span></td>
                    <td>4.2 km</td>
                    <td>5.8 km</td>
                    <td>7.5 km</td>
                    <td>14.1 km</td>
                    <td>6.3 km</td>
                </tr>
                <tr>
                    <td class="text-start fw-bold">BGC Hub</td>
                    <td>12.8 km</td>
                    <td>4.2 km</td>
                    <td><span class="badge bg-light text-dark">0 km</span></td>
                    <td>7.9 km</td>
                    <td>9.1 km</td>
                    <td>15.0 km</td>
                    <td>5.4 km</td>
                </tr>
                <tr>
                    <td class="text-start fw-bold">Quezon City Hub</td>
                    <td>9.4 km</td>
                    <td>14.1 km</td>
                    <td>15.0 km</td>
                    <td>16.5 km</td>
                    <td>18.2 km</td>
                    <td><span class="badge bg-light text-dark">0 km</span></td>
                    <td>8.8 km</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Import Routes CSV Modal -->
<div class="modal fade" id="importRoutesCsvModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog rounded-4 overflow-hidden">
        <div class="modal-content border-0">
            <form action="{{ route('import.csv') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="module_type" value="trips">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title fw-bold"><i class="bi bi-file-earmark-arrow-up me-2"></i> Import Route Plans (CSV)</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 500;">Select CSV File (.csv)</label>
                        <input type="file" name="csv_file" accept=".csv, .txt" class="form-control rounded-3" required>
                        <small class="text-muted mt-1 d-block">Expected columns: Origin Hub, Destination Hub, Distance (km), Estimated kWh, ETA (min).</small>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="button" class="btn btn-outline-secondary rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-3"><i class="bi bi-cloud-upload me-1"></i> Import Route CSV</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const hubCoords = {
        'Manila Hub (Port Area)': [14.5995, 120.9842],
        'Makati Hub (Ayala Ave)': [14.5547, 121.0244],
        'BGC Hub (Market Market)': [14.5492, 121.0558],
        'Quezon City Hub (Cubao)': [14.6178, 121.0572],
        'Pasay Hub (MOA Complex)': [14.5352, 120.9823],
        'NAIA Terminal 3 Hub': [14.5204, 121.0134],
        'Alabang Hub (Filinvest)': [14.4172, 121.0408],
        'Ortigas Hub (Ortigas Center)': [14.5869, 121.0614]
    };

    let map = null;
    let startMarker = null;
    let endMarker = null;
    let polyline = null;

    document.addEventListener("DOMContentLoaded", function() {
        map = L.map('routeVisualizerMap', { zoomControl: false }).setView([14.5995, 120.9842], 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap &bull; Green GSM Eco-Routing'
        }).addTo(map);
    });

    function calculateOptimizedRoutes(e) {
        e.preventDefault();
        const start = document.getElementById('routeStart').value;
        const end = document.getElementById('routeEnd').value;
        const type = document.getElementById('routeVehicleType').value;

        if (start === end) {
            alert('Please select different Origin and Destination hubs!');
            return;
        }

        fetch("{{ route('routes.plan') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ start, end, vehicle_type: type })
        })
        .then(res => res.json())
        .then(data => {
            renderRouteVisualizer(start, end);
            renderRouteOptionsList(data.routes);
        })
        .catch(err => console.error(err));
    }

    function renderRouteVisualizer(startName, endName) {
        const startLatLng = hubCoords[startName] || [14.5995, 120.9842];
        const endLatLng = hubCoords[endName] || [14.5547, 121.0244];

        if (startMarker) map.removeLayer(startMarker);
        if (endMarker) map.removeLayer(endMarker);
        if (polyline) map.removeLayer(polyline);

        startMarker = L.circleMarker(startLatLng, { color: '#10B981', radius: 9, fillColor: '#10B981', fillOpacity: 0.9 }).addTo(map).bindPopup("<b>Origin Hub:</b> " + startName);
        endMarker = L.circleMarker(endLatLng, { color: '#EF4444', radius: 9, fillColor: '#EF4444', fillOpacity: 0.9 }).addTo(map).bindPopup("<b>Destination:</b> " + endName);

        polyline = L.polyline([startLatLng, endLatLng], { color: '#10B981', weight: 5, opacity: 0.85, dashArray: '6, 6' }).addTo(map);
        map.fitBounds(polyline.getBounds(), { padding: [40, 40] });
    }

    function renderRouteOptionsList(routes) {
        const container = document.getElementById('routeResultsContainer');
        container.innerHTML = `<h6 class="fw-bold mb-3 text-dark">Optimized Eco-Route Comparison Options:</h6>`;

        routes.forEach((r, idx) => {
            const isBest = idx === 0;
            container.innerHTML += `
                <div class="card border rounded-3 p-3 mb-2 ${isBest ? 'border-success bg-success bg-opacity-10' : ''}">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-bold text-dark">${r.name}</span>
                        <span class="badge ${isBest ? 'bg-success' : 'bg-secondary'}">${r.tag}</span>
                    </div>
                    <div class="row text-center my-2">
                        <div class="col-4">
                            <small class="text-muted d-block" style="font-size: 11px;">DISTANCE</small>
                            <strong class="text-dark">${r.distance_km} km</strong>
                        </div>
                        <div class="col-4">
                            <small class="text-muted d-block" style="font-size: 11px;">ESTIMATED ETA</small>
                            <strong class="text-dark">${r.duration_minutes} mins</strong>
                        </div>
                        <div class="col-4">
                            <small class="text-muted d-block" style="font-size: 11px;">ESTIMATED ENERGY</small>
                            <strong class="text-success">${r.predicted_kwh} kWh (₱${r.charging_cost_php})</strong>
                        </div>
                    </div>
                    <small class="text-muted" style="font-size: 11px;"><i class="bi bi-info-circle me-1"></i> ${r.description}</small>
                </div>
            `;
        });
    }

    function exportRoutesToCSV() {
        let csv = [];
        const rows = document.querySelectorAll("table tr");
        for (let i = 0; i < rows.length; i++) {
            let row = [], cols = rows[i].querySelectorAll("td, th");
            for (let j = 0; j < cols.length; j++) 
                row.push('"' + cols[j].innerText.replace(/"/g, '""').trim() + '"');
            csv.push(row.join(","));
        }

        const csvFile = new Blob([csv.join("\n")], {type: "text/csv"});
        const downloadLink = document.createElement("a");
        downloadLink.download = "Green_GSM_Route_Planning_Matrix.csv";
        downloadLink.href = window.URL.createObjectURL(csvFile);
        downloadLink.style.display = "none";
        document.body.appendChild(downloadLink);
        downloadLink.click();
        document.body.removeChild(downloadLink);
    }
</script>
@endsection
