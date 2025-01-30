<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Venue;
use Illuminate\Http\Request;
use App\Models\Seat;

class SeatController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/seats/block",
     *     summary="Block a seat",
     *     tags={"seats"},
     *     @OA\Parameter(
     *         name="seat_id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string", example="blocked")
     *     ),
     *     @OA\Response(response=200, description="Seat blocked successfully"),
     *     @OA\Response(response=400, description="Bad Request"),
     *     @OA\Response(response=404, description="Seat not found")
     * )
     */
    public function blockSeat(Request $request)
    {
        if (!$request->session()->has('user_id')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $request->validate([
            'seat_id' => 'required|integer|exists:seats,id',
            'status' => 'required|string|in:blocked'
        ]);

        $seat = Seat::findOrFail($request->seat_id);
        $seat->status = $request->status;
        $seat->save();

        return response()->json(['message' => 'Seat blocked successfully'], 200);
    }
    /**
     * @OA\Delete(
     *     path="/api/seats/release",
     *     summary="Release a blocked seat",
     *     tags={"seats"},
     *     @OA\Parameter(
     *         name="seat_id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Seat released successfully"),
     *     @OA\Response(response=400, description="Bad Request"),
     *     @OA\Response(response=404, description="Seat not found")
     * )
     */
    public function releaseSeat(Request $request)
    {
        if (!$request->session()->has('user_id')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $request->validate([
            'seat_id' => 'required|integer|exists:seats,id'
        ]);

        $seat = Seat::findOrFail($request->seat_id);
        $seat->status = 'available';
        $seat->save();

        return response()->json(['message' => 'Seat released successfully'], 200);
    }
    /**
     * @OA\Get(
     *     path="/api/venues/{id}/seats",
     *     summary="Get seats for a specific venue",
     *     tags={"venues"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function getVenueSeats($id)
    {
        $venue = Venue::findOrFail($id);
        return response()->json($venue->seats, 200);
    }

    /**
     * @OA\Get(
     *     path="/api/events/{id}/seats",
     *     summary="Get seats for a specific event",
     *     tags={"event"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function getEventSeats(Request $request, $id)
    {
        if (!$request->session()->has('user_id')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $event = Event::findOrFail($id);
        $seats = Seat::where('event_id', $id)->get();
        return response()->json($seats, 200);
    }
}
