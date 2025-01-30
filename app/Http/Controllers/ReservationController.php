<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Seat;
use Carbon\Carbon;

class ReservationController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/reservations",
     *     summary="Create a new reservation",
     *     tags={"reservations"},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="event_id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="seat_id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=201, description="Reservation created successfully"),
     *     @OA\Response(response=400, description="Seat is not available")
     * )
     */
    public function store(Request $request)
    {
        if (!$request->session()->has('user_id')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $seat = Seat::find($request->seat_id);
        if (!$seat || $seat->is_reserved) {
            return response()->json(['error' => 'Seat is not available'], 400);
        }

        $reservation = Reservation::create([
            'user_id' => $request->user_id,
            'event_id' => $request->event_id,
            'seat_id' => $request->seat_id,
            'status' => 'pending',
            'expires_at' => Carbon::now()->addMinutes(15)
        ]);

        $seat->is_reserved = true;
        $seat->save();

        return response()->json($reservation, 201);
    }

    /**
     * @OA\Post(
     *     path="/api/reservations/{id}/confirm",
     *     summary="Confirm a reservation",
     *      tags={"reservations"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Reservation confirmed successfully"),
     *     @OA\Response(response=400, description="Reservation has expired")
     * )
     */
    public function confirm($id)
    {
        if (!$request->session()->has('user_id')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $reservation = Reservation::findOrFail($id);
        if ($reservation->expires_at < Carbon::now()) {
            return response()->json(['error' => 'Reservation has expired'], 400);
        }
        $reservation->update(['status' => 'confirmed']);
        return response()->json(['message' => 'Reservation confirmed successfully']);
    }

    /**
     * @OA\Delete(
     *     path="/api/reservations/{id}",
     *     summary="Cancel a reservation",
     *      tags={"reservations"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Reservation cancelled successfully")
     * )
     */
    public function destroy($id)
    {
        if (!$request->session()->has('user_id')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $reservation = Reservation::findOrFail($id);
        $reservation->delete();

        // Release seat
        $seat = Seat::find($reservation->seat_id);
        $seat->is_reserved = false;
        $seat->save();

        return response()->json(['message' => 'Reservation cancelled successfully']);
    }
}
