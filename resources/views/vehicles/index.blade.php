@extends('layouts.app')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col">
        <h2 class="page-header-title">Fleet and Vehicle Management</h2>
        <p class="page-header-subtitle">Manage VinFast All-Electric EV vehicle inventory, battery storage, and active fleet statuses.</p>
    </div>
    <div class="col-auto d-flex gap-2 flex-wrap">
        <button class="btn btn-outline-success rounded-3" onclick="exportVehiclesToCSV();">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export CSV
        </button>
        <button class="btn btn-outline-primary rounded-3" data-bs-toggle="modal" data-bs-target="#importVehiclesCsvModal">
            <i class="bi bi-file-earmark-arrow-up me-1"></i> Import CSV
        </button>
        <button class="btn btn-outline-dark rounded-3" onclick="window.print();">
            <i class="bi bi-printer me-1"></i> Print / PDF
        </button>
        <button class="btn btn-premium d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#addVehicleModal">
            <i class="bi bi-plus-circle me-1"></i> Register VinFast EV
        </button>
    </div>
</div>

<!-- Registry Tabs -->
<div class="card premium-card p-4">
    <ul class="nav nav-tabs border-bottom mb-4" id="registryTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold text-dark border-0 border-bottom border-primary" id="vehicles-tab" data-bs-toggle="tab" data-bs-target="#vehicles-pane" type="button" role="tab" aria-controls="vehicles-pane" aria-selected="true">
                <i class="bi bi-truck me-1"></i> VinFast EV Units ({{ $vehicles->count() }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold text-muted border-0" id="drivers-tab" data-bs-toggle="tab" data-bs-target="#drivers-pane" type="button" role="tab" aria-controls="drivers-pane" aria-selected="false">
                <i class="bi bi-people me-1"></i> Driver Directory (Synced from Team 9)
            </button>
        </li>
    </ul>

    <div class="tab-content" id="registryTabsContent">
        <!-- Vehicles List Tab -->
        <div class="tab-pane fade show active" id="vehicles-pane" role="tabpanel" aria-labelledby="vehicles-tab" tabindex="0">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="vehiclesTable">
                    <thead>
                        <tr class="text-muted" style="font-size: 13px;">
                            <th>VINFAST EV MODEL</th>
                            <th>PLATE NUMBER</th>
                            <th>YEAR</th>
                            <th>BATTERY CAPACITY</th>
                            <th>STATUS</th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vehicles as $vehicle)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-success bg-opacity-10 p-2 rounded-3 me-2">
                                            <i class="bi bi-ev-front-fill text-success fs-5"></i>
                                        </div>
                                        <div>
                                            <span class="fw-bold d-block text-dark">{{ $vehicle->make }} {{ $vehicle->model }}</span>
                                            <small class="badge bg-secondary" style="font-size: 10px;">{{ $vehicle->type }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="fw-bold text-dark">{{ $vehicle->license_plate }}</td>
                                <td>{{ $vehicle->year }}</td>
                                <td class="fw-bold text-success">
                                    <i class="bi bi-battery-charging me-1"></i> {{ $vehicle->fuel_capacity }} kWh
                                </td>
                                <td>
                                    <span class="badge rounded-pill {{ $vehicle->status === 'active' ? 'bg-success' : ($vehicle->status === 'maintenance' ? 'bg-warning text-dark' : 'bg-secondary') }}">
                                        {{ ucfirst($vehicle->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button class="btn btn-sm btn-primary rounded-2 px-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#editVehicleModal{{ $vehicle->id }}" title="Edit Vehicle">
                                            <i class="bi bi-pencil-fill me-1"></i> Edit
                                        </button>
                                        <form action="{{ route('vehicles.destroy', $vehicle) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this vehicle?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-2 px-2 shadow-sm" title="Delete Vehicle">
                                                <i class="bi bi-trash-fill me-1"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <!-- Edit Vehicle Modal -->
                            <div class="modal fade" id="editVehicleModal{{ $vehicle->id }}" tabindex="-1" aria-labelledby="editVehicleModalLabel{{ $vehicle->id }}" aria-hidden="true">
                                <div class="modal-dialog rounded-4 overflow-hidden">
                                    <div class="modal-content border-0">
                                        <form action="{{ route('vehicles.update', $vehicle) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header bg-secondary text-white border-0">
                                                <h5 class="modal-title fw-bold" id="editVehicleModalLabel{{ $vehicle->id }}">Edit VinFast EV Details</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label" style="font-weight: 500;">Make (Brand)</label>
                                                        <input type="text" name="make" value="{{ $vehicle->make }}" class="form-control rounded-3" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label" style="font-weight: 500;">Model (Name)</label>
                                                        <input type="text" name="model" value="{{ $vehicle->model }}" class="form-control rounded-3" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label" style="font-weight: 500;">License Plate</label>
                                                        <input type="text" name="license_plate" value="{{ $vehicle->license_plate }}" class="form-control rounded-3" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label" style="font-weight: 500;">Year</label>
                                                        <input type="number" name="year" value="{{ $vehicle->year }}" class="form-control rounded-3" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label" style="font-weight: 500;">Vehicle Category</label>
                                                        <select name="type" class="form-select rounded-3" required>
                                                            <option value="Sedan" {{ $vehicle->type === 'Sedan' ? 'selected' : '' }}>Sedan (Nerio Green)</option>
                                                            <option value="SUV" {{ $vehicle->type === 'SUV' ? 'selected' : '' }}>SUV (VF 8 / VF 9)</option>
                                                            <option value="Crossover" {{ $vehicle->type === 'Crossover' ? 'selected' : '' }}>Crossover (VF e34)</option>
                                                            <option value="Hatchback" {{ $vehicle->type === 'Hatchback' ? 'selected' : '' }}>Compact (VF 5)</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label" style="font-weight: 500;">Battery Storage (kWh)</label>
                                                        <input type="number" name="fuel_capacity" value="{{ $vehicle->fuel_capacity }}" step="0.1" class="form-control rounded-3" required>
                                                    </div>
                                                    <div class="col-12">
                                                        <label class="form-label" style="font-weight: 500;">Status</label>
                                                        <select name="status" class="form-select rounded-3" required>
                                                            <option value="active" {{ $vehicle->status === 'active' ? 'selected' : '' }}>Active (Available)</option>
                                                            <option value="maintenance" {{ $vehicle->status === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                                            <option value="offline" {{ $vehicle->status === 'offline' ? 'selected' : '' }}>Offline</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-0 p-3 bg-light">
                                                <button type="button" class="btn btn-outline-secondary rounded-3" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-premium rounded-3">Update Vehicle</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">No fleet vehicles registered yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Drivers Tab (Team 9 Synced Read-Only View) -->
        <div class="tab-pane fade" id="drivers-pane" role="tabpanel" aria-labelledby="drivers-tab" tabindex="0">
            <div class="alert alert-info border-0 rounded-3 small mb-3" role="alert">
                <i class="bi bi-info-circle-fill me-2"></i> <strong>Note:</strong> Driver Registration & Account Management is handled by <strong>Team 9 (TNVS Operations & Driver Management System)</strong>. The roster below is a read-only directory synced via API.
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr class="text-muted" style="font-size: 13px;">
                            <th>DRIVER NAME</th>
                            <th>EMAIL</th>
                            <th>LICENSE NUMBER</th>
                            <th>OPERATIONAL STATUS</th>
                            <th>SAFETY SCORE</th>
                            <th>TOTAL DISTANCE</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($drivers as $driver)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-secondary bg-opacity-10 p-2 rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                            <i class="bi bi-person-fill text-secondary fs-5"></i>
                                        </div>
                                        <span class="fw-bold">{{ $driver->user->name }}</span>
                                    </div>
                                </td>
                                <td>{{ $driver->user->email }}</td>
                                <td class="fw-bold text-uppercase">{{ $driver->license_number }}</td>
                                <td>
                                    <span class="badge rounded-pill {{ $driver->status === 'available' ? 'bg-success' : ($driver->status === 'on_trip' ? 'bg-info' : 'bg-secondary') }}">
                                        {{ $driver->status === 'on_trip' ? 'On Trip' : ucfirst($driver->status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge rounded-pill {{ $driver->performance_score >= 90 ? 'bg-success' : ($driver->performance_score >= 80 ? 'bg-warning text-dark' : 'bg-danger') }}">
                                        {{ $driver->performance_score }}%
                                    </span>
                                </td>
                                <td>{{ number_format($driver->total_distance_km, 1) }} km</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">No drivers registered yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Vehicle Modal -->
<div class="modal fade" id="addVehicleModal" tabindex="-1" aria-labelledby="addVehicleModalLabel" aria-hidden="true">
    <div class="modal-dialog rounded-4 overflow-hidden">
        <div class="modal-content border-0">
            <form action="{{ route('vehicles.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-success text-white border-0">
                    <h5 class="modal-title fw-bold" id="addVehicleModalLabel"><i class="bi bi-ev-front me-2"></i> Register New VinFast EV</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" style="font-weight: 500;">Make (Brand)</label>
                            <input type="text" name="make" value="VinFast" class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-weight: 500;">Model (Name)</label>
                            <input type="text" name="model" placeholder="e.g. Nerio Green / VF 8" class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-weight: 500;">License Plate</label>
                            <input type="text" name="license_plate" placeholder="e.g. NGA-1029" class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-weight: 500;">Year</label>
                            <input type="number" name="year" value="2026" class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-weight: 500;">Vehicle Category</label>
                            <select name="type" class="form-select rounded-3" required>
                                <option value="Sedan" selected>Sedan (Nerio Green)</option>
                                <option value="SUV">SUV (VF 8 / VF 9)</option>
                                <option value="Crossover">Crossover (VF e34)</option>
                                <option value="Hatchback">Compact (VF 5)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-weight: 500;">Battery Storage (kWh)</label>
                            <input type="number" name="fuel_capacity" placeholder="e.g. 42.0" step="0.1" class="form-control rounded-3" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label" style="font-weight: 500;">Initial Status</label>
                            <select name="status" class="form-select rounded-3" required>
                                <option value="active" selected>Active (Available)</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="offline">Offline</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="button" class="btn btn-outline-secondary rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-3"><i class="bi bi-plus-circle me-1"></i> Register Vehicle</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function exportVehiclesToCSV() {
    let csv = [];
    const rows = document.querySelectorAll("#vehiclesTable tr");
    
    for (let i = 0; i < rows.length; i++) {
        let row = [], cols = rows[i].querySelectorAll("td, th");
        for (let j = 0; j < cols.length - 1; j++) // exclude ACTIONS column
            row.push('"' + cols[j].innerText.replace(/"/g, '""').trim() + '"');
        csv.push(row.join(","));
    }

    const csvFile = new Blob([csv.join("\n")], {type: "text/csv"});
    const downloadLink = document.createElement("a");
    downloadLink.download = "Green_GSM_VinFast_EV_Fleet_Inventory.csv";
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = "none";
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}
}
</script>

<!-- Import Vehicles CSV Modal -->
<div class="modal fade" id="importVehiclesCsvModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog rounded-4 overflow-hidden">
        <div class="modal-content border-0">
            <form action="{{ route('import.csv') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="module_type" value="vehicles">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title fw-bold"><i class="bi bi-file-earmark-arrow-up me-2"></i> Import Fleet Inventory (CSV)</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 500;">Select CSV File (.csv)</label>
                        <input type="file" name="csv_file" accept=".csv, .txt" class="form-control rounded-3" required>
                        <small class="text-muted mt-1 d-block">Expected columns: Make, Model, License Plate, Year, Category, Battery Capacity (kWh).</small>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="button" class="btn btn-outline-secondary rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-3"><i class="bi bi-cloud-upload me-1"></i> Import Fleet CSV</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
