<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\Ticket;

class TicketController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/tickets",
     *     summary="Create a new ticket",
     *     tags={"tickets"},
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
     *     @OA\Parameter(
     *         name="price",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="number", format="float")
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
            'event_id' => 'required|exists:events,id',
            'seat_id' => 'required|exists:seats,id',
            'price' => 'required|numeric',
            'status' => 'required|string'
        ]);

        $ticket = Ticket::create(array_merge($request->all(), ['code' => uniqid()]));
        return response()->json($ticket, 201);
    }
    /**
     * @OA\Post(
     *     path="/api/tickets/{id}/transfer",
     *     summary="Transfer a ticket (only available tickets)",
     *     tags={"tickets"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Ticket transferred successfully"),
     *     @OA\Response(response=400, description="Only unused tickets can be transferred"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function transfer(Request $request, $id)
    {
        if (!$request->session()->has('user_id')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $ticket = Ticket::findOrFail($id);
        if ($ticket->status !== 'available') {
            return response()->json(['error' => 'Only unused tickets can be transferred'], 400);
        }

        $ticket->update(['user_id' => $request->user_id]);
        return response()->json(['message' => 'Ticket transferred successfully']);
    }
    /**
     * @OA\Get(
     *     path="/api/tickets/{id}/download",
     *     summary="Download a ticket",
     *     tags={"tickets"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="download_url object"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function download($id)
    {
        if (!$request->session()->has('user_id')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $ticket = Ticket::with('event', 'seat')->findOrFail($id);
        $pdf = PDF::loadView('tickets.pdf', compact('ticket'));

        // Save the PDF to a temporary location
        $filePath = storage_path('app/public/' . $ticket->uuid . '.pdf');
        $pdf->save($filePath);

        // Generate a URL to the PDF
        $downloadUrl = url('storage/' . $ticket->uuid . '.pdf');

        return response()->json(['download_url' => $downloadUrl], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/tickets/{id}",
     *     summary="Get ticket by ID",
     *     tags={"tickets"},
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
    public function show(Request $request, $id)
    {
        if (!$request->session()->has('user_id')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $ticket = Ticket::findOrFail($id);
        return response()->json($ticket, 201);
    }

    /**
     * @OA\Put(
     *     path="/api/tickets/{id}",
     *     summary="Update an existing ticket",
     *     tags={"tickets"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="event_id",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="seat_id",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="price",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="number", format="float")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=false,
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
        $ticket = Ticket::findOrFail($id);

        $request->validate([
            'event_id' => 'sometimes|required|exists:events,id',
            'seat_id' => 'sometimes|required|exists:seats,id',
            'price' => 'sometimes|required|numeric',
            'status' => 'sometimes|required|string'
        ]);

        $ticket->update($request->all());
        return response()->json($ticket, 201);
    }

    /**
     * @OA\Delete(
     *     path="/api/tickets/{id}",
     *     summary="Delete a ticket",
     *     tags={"tickets"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=201, description="Ticket deleted successfully."),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroy(Request $request, $id)
    {
        if (!$request->session()->has('user_id')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $ticket = Ticket::findOrFail($id);
        $ticket->delete();
        return response()->json(['message' => 'Ticket deleted successfully.'], 201);
    }
}
