<?php

use App\Http\Controllers\IntegrationApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Standard JSON integration APIs for external teams and systems.
|
*/

// Operations System (Team 9)
Route::post('/operations/trip-request', [IntegrationApiController::class, 'receiveTripRequest']);
Route::get('/operations/vehicle-availability', [IntegrationApiController::class, 'getVehicleAvailability']);

// Booking System (Team 10)
Route::post('/booking/assign-trip', [IntegrationApiController::class, 'receiveBookingData']);

// Inventory System (Team 6)
Route::post('/inventory/fuel-stock', [IntegrationApiController::class, 'receiveFuelStock']);
Route::get('/inventory/fuel-usage', [IntegrationApiController::class, 'sendFuelUsageData']);

// Finance System (Team 5)
Route::get('/finance/expenses', [IntegrationApiController::class, 'sendExpensesData']);

// HR System (Teams 1-4)
Route::post('/hr/sync-driver', [IntegrationApiController::class, 'receiveDriverInfo']);
Route::get('/hr/driver-performance', [IntegrationApiController::class, 'sendDriverPerformanceData']);
