<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Item;
use App\Models\Taster;
use App\Models\Shop;
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
    $tickets = Ticket::where('buyer_id', $buyer_id)->get();

    // Check if the tickets collection is not empty
    if ($tickets->isNotEmpty()) {
        return response()->json([
            'message' => 'Tickets successfully retrieved',
            'success' => true,
            'tickets' => $tickets,
        ], 200);
    } else {
        return response()->json(['message' => 'Tickets not found', 'success' => false], 404);
    }
}


    /**
     * Display a specific shop's ticket.
     */
    public function indexShopTickets(String $shop_id)
    {
        $tickets = Ticket::where('shop_id', $shop_id)->get();

    // Check if the tickets collection is not empty
    if ($tickets->isNotEmpty()) {
        return response()->json([
            'message' => 'Tickets successfully retrieved',
            'success' => true,
            'tickets' => $tickets,
        ], 200);
    } else {
        return response()->json(['message' => 'Tickets not found', 'success' => false], 404);
    }
        
    }


    /**
     * Show the form for creating a new resource.
     */
    public function deleteTicket(String $ticket_id)
    {
        // Method to show form is not implemented
        try {
            $deleted = Ticket::where('ticket_id', $ticket_id)->delete();
            return response()->json([
                'message' => 'Ticket deleted',
                'success' => true,
            ], 200);
        }
        catch (Exception $e){
            return response()->json([
                'message' => 'Ticket not found',
                'success' => false,
            ], 404);
        }
        
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

        // Retrieve the shop, item, and user
        $shop = Shop::where('shop_id', $request->shop_id)->first();
        $item = Item::where('item_id', $request->item_id)->first();
        $user = Taster::where('user_id', $request->buyer_id)->first();

        // Initialize variables for ticket creation
        $shopName = $shop ? $shop->shop_name : "Shop not in database";
        $itemName = $item ? $item->item_name : "Item not in database";
        $itemImage = $item ? $item->item_image : "Item not in database";
        $buyerName = $user ? $user->user_name : "User not in database";

        try {
            DB::transaction(function () use ($request, $sTicket_Id, $buyerName, $shopName, $itemName, $itemImage) {
                Ticket::create([
                    'ticket_id' => $sTicket_Id,
                    'buyer_id' => $request->buyer_id,
                    'buyer_name' => $buyerName,
                    'shop_id' => $request->shop_id,
                    'shop_name' => $shopName,
                    'item_id' => $request->item_id,
                    'item_name' => $itemName,
                    'item_image' => $itemImage,
                    'quantity' => $request->quantity,
                    'price' => (float)$request->price,
                    'status' => 'Pending',
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
    public function updateStatus(Request $request, $ticket_id)
    {
        $target = Ticket::where('ticket_id', $ticket_id)->first();
        if ($target) {
            $target->update(['status' => $request->status]); // Correctly update status
            return response()->json(['message' => 'Ticket Updated', 'success' => true], 201);
        }
        return response()->json(['message' => 'Ticket not found', 'success' => false, ], 404);
    }

    // Personal Functions

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
