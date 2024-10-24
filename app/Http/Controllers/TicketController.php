<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TicketController extends Controller
{
    /**
     * Display a listing of all tickets.
     */
    public function indexAllTickets()
    {
        return Ticket::all();
    }

    /**
     * Display tickets for a specific buyer.
     */
    public function indexUserTickets($buyer_id) 
    {
        return Ticket::where('buyer_id', $buyer_id)->get(); // Added ->get() to retrieve results
    }

    /**
     * Display a specific shop's ticket.
     */
    public function indexShopTickets($id)
    {
        $ticket = Ticket::find($id);
        if ($ticket) {
            return response()->json($ticket);
        }
        return response()->json(['message' => 'Ticket not found'], 404);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Method to show form is not implemented
    }

    /**
     * Store a newly created resource in storage.
     */

     public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'buyer_id' => 'required',
            'shop_id' => 'required',
            'item_id' => 'required',
            'quantity' => 'required|integer',
            'price' => 'required|numeric',
            'location' => 'sometimes|string'
        ]);

        Log::info($request->all()); // Log the request data
        $sTicket_Id = $request->buyer_id . '_Ticket_' . $this->RSG(32);

        // Ensure unique ticket ID
        while ($this->check($sTicket_Id)) {
            $sTicket_Id = $request->buyer_id . '_Ticket_' . $this->RSG(32);
        }

        try {
            DB::transaction(function () use ($request, $sTicket_Id) {
                Ticket::create([
                    'ticket_id' => $sTicket_Id,
                    'buyer_id' => $request->buyer_id,
                    'shop_id' => $request->shop_id,
                    'item_id' => $request->item_id,
                    'quantity' => $request->quantity,
                    'price' => (float)$request->price,
                    'status' => 'To Be Accepted',
                    'location' => $request->location,
                ]);
            });
            
            // Successful response
            return response()->json(['message' => 'Ticket Sent', 'success' => true], 201);
            
        } catch (Exception $e) {
            // Error response
            $errorResponse = response()->json([
                'message' => 'Error creating ticket',
                'success' => false,
                'errorMessage' => $e->getMessage() // Include the error message
            ], 500); // Return a 500 Internal Server Error response

            // Log the error response data
            Log::error('Error creating ticket: ' . $e->getMessage());
            Log::info($errorResponse);
            return $errorResponse;
        }
    }

    /**
     * Update the status of a ticket.
     */
    public function updateStatus($id, $status)
    {
        $target = Ticket::where('ticket_id', $id)->first();
        if ($target) {
            $target->update(['status' => $status]); // Correctly update status
            return $target;
        }
        return response()->json(['message' => 'Ticket not found'], 404);
    }

    private function RNG($iMin, $iMax) // Random number generator
    {
        return rand($iMin, $iMax);
    }

    private function RSG($iMaxLength) // Random string generator
    {
        $aSam = array_merge(range('a', 'z'), range('A', 'Z'), range('0', '9'));
        $sText = "";
        if ($iMaxLength < 5) {
            $iMaxLength = 5;
        }
        $iLength = $this->RNG(4, $iMaxLength);

        for ($iTemp = 0; $iTemp < $iLength; $iTemp++) {
            $iRNG = $this->RNG(0, count($aSam) - 1);
            $sText .= $aSam[$iRNG];
        }
        return $sText;
    }

    private function check($sTicket_Id)
    {
        return Ticket::where('ticket_id', $sTicket_Id)->exists(); // Check if ticket exists
    }
}
