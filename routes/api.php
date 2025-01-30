<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\SeatController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\TicketController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::middleware(['throttle:3,1'])->group(function () {
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login'])->name('login');
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);


    Route::get('/events', [EventController::class, 'index']);
    Route::get('/events/{id}', [EventController::class, 'show']);
    Route::post('/events', [EventController::class, 'store']);
    Route::put('/events/{id}', [EventController::class, 'update']);
    Route::delete('/events/{id}', [EventController::class, 'destroy']);

    Route::get('/events/{id}/seats', [SeatController::class, 'getEventSeats']);
    Route::get('/venues/{id}/seats', [SeatController::class, 'getVenueSeats']);
    Route::post('/seats/block', [SeatController::class, 'blockSeat']);
    Route::delete('/seats/release', [SeatController::class, 'releaseSeat']);

    Route::post('/reservations', [ReservationController::class, 'store']);
    Route::get('/reservations', [ReservationController::class, 'index']);
    Route::get('/reservations/{id}', [ReservationController::class, 'show']);
    Route::post('/reservations/{id}/confirm', [ReservationController::class, 'confirm']);
    Route::delete('/reservations/{id}', [ReservationController::class, 'destroy']);

    Route::get('/tickets', [TicketController::class, 'index']);
    Route::get('/tickets/{id}', [TicketController::class, 'show']);
    Route::get('/tickets/{id}/download', [TicketController::class, 'download']);
    Route::post('/tickets/{id}/transfer', [TicketController::class, 'transfer']);
    Route::post('/tickets', [TicketController::class, 'store']);
    Route::put('/tickets/{id}', [TicketController::class, 'update']);
    Route::delete('/tickets/{id}', [TicketController::class, 'destroy']);
});

Route::fallback(function(){
    return response()->json(['error' => 'Not Found.'], 404);
})->name('api.fallback.404');

Route::fallback(function(){
    return response()->json(['error' => 'Internal server error.'], 500);
})->name('api.fallback.500');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
