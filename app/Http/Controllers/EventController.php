<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Info(
 *     title="Event Booking API",
 *     version="1.0.0",
 *     description="API documentation for the Event Booking application"
 * )
 */
class EventController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/events",
     *     summary="Get list of events",
     *     tags={"event"},
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function index(Request $request)
    {
        if (!$request->session()->has('user_id')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return Event::all();
    }

    /**
     * @OA\Get(
     *     path="/api/events/{id}",
     *     summary="Get event by ID",
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
    public function show($id)
    {
        return Event::findOrFail($id);
    }

    /**
     * @OA\Post(
     *     path="/api/events",
     *     summary="Create a new event (admin only)",
     *     tags={"event"},
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="description",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="venue_id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="start_date",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string", format="date-time")
     *     ),
     *     @OA\Parameter(
     *         name="end_date",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string", format="date-time")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=201, description="Created"),
     *     @OA\Response(response=400, description="Bad Request")
     * )
     */
    public function store(Request $request)
    {
        if (!$request->session()->has('user_id')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'venue_id' => 'required|exists:venues,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'status' => 'required|string',
        ]);
        if ($request->session()->get('is_admin')) {
            $event = Event::create($request->all());
            return response()->json($event, 201);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @OA\Put(
     *     path="/api/events/{id}",
     *     summary="Update an existing event (admin only)",
     *      tags={"event"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="description",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="venue_id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="start_date",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string", format="date-time")
     *     ),
     *     @OA\Parameter(
     *         name="end_date",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string", format="date-time")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=400, description="Bad Request"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function update(Request $request, $id)
    {
        if (!$request->session()->has('user_id')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        if ($request->session()->get('is_admin')) {
            $event = Event::findOrFail($id);

            $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'description' => 'sometimes|required|string',
                'venue_id' => 'sometimes|required|exists:venues,id',
                'start_date' => 'sometimes|required|date',
                'end_date' => 'sometimes|required|date',
                'status' => 'sometimes|required|string',
            ]);

            $event->update($request->all());

            return response()->json($event, 200);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @OA\Delete(
     *     path="/api/events/{id}",
     *     summary="Delete an event (admin only)",
     *      tags={"event"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=204, description="Successfully deleted."),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroy(Request $request, $id)
    {
        if (!$request->session()->has('user_id')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if ($request->session()->get('is_admin')) {
            Event::findOrFail($id)->delete();
            return response()->json("Successfully deleted.", 204);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
