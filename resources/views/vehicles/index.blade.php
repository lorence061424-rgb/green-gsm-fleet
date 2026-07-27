@extends('layouts.app')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col">
        <h2 class="page-header-title">Fleet & Drivers Registry</h2>
        <p class="page-header-subtitle">Manage vehicle records, driver registrations, and active fleet statuses.</p>
    </div>
    <div class="col-auto d-flex gap-2">
        <button class="btn btn-premium d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#addVehicleModal">
            <i class="bi bi-plus-circle me-1"></i> Register Vehicle
        </button>
        <button class="btn btn-outline-dark d-flex align-items-center rounded-3" data-bs-toggle="modal" data-bs-target="#addDriverModal">
            <i class="bi bi-person-plus me-1"></i> Register Driver
        </button>
    </div>
</div>

<!-- Registry Tabs -->
<div class="card premium-card p-4">
    <ul class="nav nav-tabs border-bottom mb-4" id="registryTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold text-dark border-0 border-bottom border-primary" id="vehicles-tab" data-bs-toggle="tab" data-bs-target="#vehicles-pane" type="button" role="tab" aria-controls="vehicles-pane" aria-selected="true">
                <i class="bi bi-truck me-1"></i> Fleet Vehicles ({{ $vehicles->count() }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold text-muted border-0" id="drivers-tab" data-bs-toggle="tab" data-bs-target="#drivers-pane" type="button" role="tab" aria-controls="drivers-pane" aria-selected="false">
                <i class="bi bi-people me-1"></i> Drivers Directory ({{ $drivers->count() }})
            </button>
        </li>
    </ul>

    <div class="tab-content" id="registryTabsContent">
        <!-- Vehicles List Tab -->
        <div class="tab-pane fade show active" id="vehicles-pane" role="tabpanel" aria-labelledby="vehicles-tab" tabindex="0">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr class="text-muted" style="font-size: 13px;">
                            <th>VEHICLE / CLASS</th>
                            <th>PLATE NUMBER</th>
                            <th>YEAR</th>
                            <th>FUEL TANK</th>
                            <th>STATUS</th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vehicles as $vehicle)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-2">
                                            <i class="bi bi-truck text-primary fs-5"></i>
                                        </div>
                                        <div>
                                            <span class="fw-bold d-block">{{ $vehicle->make }} {{ $vehicle->model }}</span>
                                            <small class="badge bg-secondary" style="font-size: 10px;">{{ $vehicle->type }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="fw-bold">{{ $vehicle->license_plate }}</td>
                                <td>{{ $vehicle->year }}</td>
                                <td>{{ $vehicle->fuel_capacity }} Liters</td>
                                <td>
                                    <span class="badge rounded-pill {{ $vehicle->status === 'active' ? 'bg-success' : ($vehicle->status === 'maintenance' ? 'bg-warning text-dark' : 'bg-secondary') }}">
                                        {{ ucfirst($vehicle->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button class="btn btn-sm btn-outline-primary border-0 rounded-2" data-bs-toggle="modal" data-bs-target="#editVehicleModal{{ $vehicle->id }}">
                                            <i class="bi bi-pencil-fill"></i>
                                        </button>
                                        <form action="{{ route('vehicles.destroy', $vehicle) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this vehicle?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger border-0 rounded-2">
                                                <i class="bi bi-trash-fill"></i>
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
                                                <h5 class="modal-title fw-bold" id="editVehicleModalLabel{{ $vehicle->id }}">Edit Vehicle Details</h5>
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
                                                        <label class="form-label" style="font-weight: 500;">Vehicle Type</label>
                                                        <select name="type" class="form-select rounded-3" required>
                                                            <option value="Sedan" {{ $vehicle->type === 'Sedan' ? 'selected' : '' }}>Sedan</option>
                                                            <option value="SUV" {{ $vehicle->type === 'SUV' ? 'selected' : '' }}>SUV</option>
                                                            <option value="Van" {{ $vehicle->type === 'Van' ? 'selected' : '' }}>Van</option>
                                                            <option value="Hatchback" {{ $vehicle->type === 'Hatchback' ? 'selected' : '' }}>Hatchback</option>
                                                            <option value="Truck" {{ $vehicle->type === 'Truck' ? 'selected' : '' }}>Truck</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label" style="font-weight: 500;">Fuel Capacity (L)</label>
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

        <!-- Drivers Tab -->
        <div class="tab-pane fade" id="drivers-pane" role="tabpanel" aria-labelledby="drivers-tab" tabindex="0">
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
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title fw-bold" id="addVehicleModalLabel">Register New Vehicle</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" style="font-weight: 500;">Make (Brand)</label>
                            <input type="text" name="make" placeholder="e.g. Toyota" class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-weight: 500;">Model (Name)</label>
                            <input type="text" name="model" placeholder="e.g. Vios" class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-weight: 500;">License Plate</label>
                            <input type="text" name="license_plate" placeholder="e.g. NBD-1234" class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-weight: 500;">Year</label>
                            <input type="number" name="year" placeholder="e.g. 2023" class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-weight: 500;">Vehicle Type</label>
                            <select name="type" class="form-select rounded-3" required>
                                <option value="Sedan" selected>Sedan</option>
                                <option value="SUV">SUV</option>
                                <option value="Van">Van</option>
                                <option value="Hatchback">Hatchback</option>
                                <option value="Truck">Truck</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-weight: 500;">Fuel Capacity (L)</label>
                            <input type="number" name="fuel_capacity" placeholder="e.g. 42" step="0.1" class="form-control rounded-3" required>
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
                    <button type="submit" class="btn btn-premium rounded-3">Register Vehicle</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Driver Modal -->
<div class="modal fade" id="addDriverModal" tabindex="-1" aria-labelledby="addDriverModalLabel" aria-hidden="true">
    <div class="modal-dialog rounded-4 overflow-hidden">
        <div class="modal-content border-0">
            <form action="{{ route('fleet.drivers.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-dark text-white border-0">
                    <h5 class="modal-title fw-bold" id="addDriverModalLabel">Register New Driver</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label" style="font-weight: 500;">Driver Full Name</label>
                            <input type="text" name="name" placeholder="e.g. Juan Dela Cruz" class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-weight: 500;">Email Address</label>
                            <input type="email" name="email" placeholder="e.g. juan@gmail.com" class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-weight: 500;">Password</label>
                            <input type="password" name="password" placeholder="At least 6 chars" class="form-control rounded-3" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label" style="font-weight: 500;">Professional License Number</label>
                            <input type="text" name="license_number" placeholder="e.g. N01-99-999999" class="form-control rounded-3" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="button" class="btn btn-outline-secondary rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark rounded-3">Register Driver</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
