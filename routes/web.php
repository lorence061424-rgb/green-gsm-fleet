<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FleetController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\FuelController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\CostAnalysisController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Dashboard (Accessible to all internal roles)
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Fleet Management (FVM) - Viewable by all, modification restricted to Fleet Manager & Admin
Route::get('/vehicles', [FleetController::class, 'index'])->name('vehicles.index');
Route::post('/vehicles', [FleetController::class, 'storeVehicle'])->middleware('role:fleet_manager')->name('vehicles.store');
Route::put('/vehicles/{vehicle}', [FleetController::class, 'updateVehicle'])->middleware('role:fleet_manager')->name('vehicles.update');
Route::delete('/vehicles/{vehicle}', [FleetController::class, 'deleteVehicle'])->middleware('role:admin')->name('vehicles.destroy');
Route::post('/fleet/assign-driver', [FleetController::class, 'assignDriver'])->middleware('role:fleet_manager,dispatcher')->name('fleet.assign-driver');
Route::post('/fleet/drivers', [FleetController::class, 'storeDriver'])->middleware('role:fleet_manager')->name('fleet.drivers.store');

// Vehicle Reservation & Dispatch System (VRDS) - Dispatcher & Operations Focus
Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
Route::post('/reservations', [ReservationController::class, 'store'])->middleware('role:dispatcher,operations')->name('reservations.store');
Route::post('/reservations/{reservation}/status', [ReservationController::class, 'updateStatus'])->middleware('role:dispatcher')->name('reservations.update-status');
Route::get('/reservations/check-availability', [ReservationController::class, 'checkAvailability'])->name('reservations.check-availability');

// Trip Scheduling & Dispatch / Telemetry Monitoring - Dispatcher & Operations Focus
Route::get('/trips', [TripController::class, 'index'])->name('trips.index');
Route::post('/trips/plan-preview', [TripController::class, 'planRoutePreview'])->name('trips.plan-preview');
Route::post('/trips', [TripController::class, 'store'])->middleware('role:dispatcher')->name('trips.store');
Route::post('/trips/{trip}/start', [TripController::class, 'startTrip'])->middleware('role:dispatcher,operations')->name('trips.start');
Route::post('/trips/{trip}/complete', [TripController::class, 'completeTrip'])->middleware('role:dispatcher,operations')->name('trips.complete');
Route::post('/trips/{trip}/simulate-gps', [TripController::class, 'simulateTelemetry'])->name('trips.simulate-gps');

// Fuel Management & AI Predictions - Viewable by all, Training restricted to Admin
Route::get('/fuel', [FuelController::class, 'index'])->name('fuel.index');
Route::post('/fuel', [FuelController::class, 'store'])->middleware('role:finance,fleet_manager')->name('fuel.store');
Route::post('/fuel/predict', [FuelController::class, 'predict'])->name('fuel.predict');
Route::post('/fuel/train', [FuelController::class, 'train'])->middleware('role:admin')->name('fuel.train');

// Transport Cost Analysis & Optimization (TCAO) - Finance & Operations Focus
Route::get('/cost-analysis', [CostAnalysisController::class, 'index'])->name('cost-analysis.index');

// Maintenance Management - Fleet Manager Focus
Route::get('/maintenance', [MaintenanceController::class, 'index'])->name('maintenance.index');
Route::post('/maintenance', [MaintenanceController::class, 'store'])->middleware('role:fleet_manager')->name('maintenance.store');
Route::post('/maintenance/{record}/status', [MaintenanceController::class, 'updateStatus'])->middleware('role:fleet_manager')->name('maintenance.update-status');

// Authentication & Dynamic Role Switching
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/switch-role', [AuthController::class, 'switchRole'])->name('switch-role');
