<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\User;
use App\Models\MaintenanceRecord;
use Illuminate\Http\Request;

class FleetController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::with('trips')->get();
        $drivers = Driver::with('user')->get();
        $maintenanceRecords = MaintenanceRecord::with('vehicle')->latest()->get();

        return view('vehicles.index', compact('vehicles', 'drivers', 'maintenanceRecords'));
    }

    public function storeVehicle(Request $request)
    {
        $validated = $request->validate([
            'license_plate' => 'required|string|unique:vehicles',
            'model' => 'required|string',
            'make' => 'required|string',
            'year' => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'type' => 'required|in:Sedan,SUV,Van,Hatchback,Truck',
            'fuel_capacity' => 'required|numeric|min:1',
            'status' => 'required|in:active,maintenance,offline',
        ]);

        Vehicle::create($validated);

        return redirect()->back()->with('success', 'Vehicle registered successfully.');
    }

    public function updateVehicle(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'license_plate' => 'required|string|unique:vehicles,license_plate,' . $vehicle->id,
            'model' => 'required|string',
            'make' => 'required|string',
            'year' => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'type' => 'required|in:Sedan,SUV,Van,Hatchback,Truck',
            'fuel_capacity' => 'required|numeric|min:1',
            'status' => 'required|in:active,maintenance,offline',
        ]);

        $vehicle->update($validated);

        return redirect()->back()->with('success', 'Vehicle updated successfully.');
    }

    public function deleteVehicle(Vehicle $vehicle)
    {
        $vehicle->delete();
        return redirect()->back()->with('success', 'Vehicle deleted successfully.');
    }

    public function assignDriver(Request $request)
    {
        $validated = $request->validate([
            'driver_id' => 'required|exists:drivers,id',
            'vehicle_id' => 'required|exists:vehicles,id',
        ]);

        // Clear active assignments for this vehicle or driver if necessary
        // (In a simple capstone, we just show active status and log it)
        $vehicle = Vehicle::find($validated['vehicle_id']);
        $driver = Driver::find($validated['driver_id']);

        // Set status of driver to available or occupied
        $driver->update(['status' => 'available']); 

        // In this implementation, assignments are made dynamically per trip,
        // but we can log the driver-vehicle pairing or assign them directly in trips.
        return redirect()->back()->with('success', "Driver {$driver->user->name} assigned to Vehicle {$vehicle->license_plate}.");
    }

    // Driver CRUD & registration
    public function storeDriver(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'license_number' => 'required|string|unique:drivers',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => 'driver',
        ]);

        Driver::create([
            'user_id' => $user->id,
            'license_number' => $validated['license_number'],
            'status' => 'available',
        ]);

        return redirect()->back()->with('success', 'Driver registered successfully.');
    }
}
