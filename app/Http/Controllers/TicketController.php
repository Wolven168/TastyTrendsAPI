<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function indexAllTickets()
    {
        return Ticket::all()
    }

    public function indexUserTickets($buyer_id) 
    {
        return Ticket::where('buyer_id', $buyer_id);
    }

    public function indexShopTickets($id)
    {
        $target = Ticket::find($id);
        return $target;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(
            'buyer_id' => 'required',
            'shop_id' => 'required',
            'price' => 'required'
        );
        
        $sTicket_Id = $request->buyer_id . '_Ticket_' . $this->RSG(32); // Uses a random string generator for creating a unique id
        $check = $this->check();
        while($check){
            $sTicket_Id = $request->buyer_id . '_Ticket_' . $this->RSG(32); // Uses a random string generator for creating a unique id
            $check = $this->check();
        }

        return Ticket::create([
            'ticket_id' => $sTicket_Id,
            'buyer_id' => $request->buyer_id,
            'shop_id' => $request->shop_id,
            'order' => $request->order,
            'status' => 'Created',
        ]);
    }
    
    public function updateStatus($id, $status){
        $target = Ticket::where('ticket_id', $id)->first();
        $target->update('status' => $status);
        return $target;
    }

    private function RNG($iMin, $iMax) // Random number generator
    {
        return rand($iMin, $iMax); // Return the random number
    }

    private function RSG($iMaxLength) // Random string generator
    {
        $aSam = array_merge(range('a', 'z'), range('A', 'Z'), range('0', '9')); // Simplified array creation
        $sText = "";
        if ($iMaxLength < 5) {
            $iMaxLength = 5; // Ensure minimum length
        }
        $iLength = $this->RNG(4, $iMaxLength); // Random length between 4 and max length

        for ($iTemp = 0; $iTemp < $iLength; $iTemp++) {
            $iRNG = $this->RNG(0, count($aSam) - 1); // Get a valid index for the array
            $sText .= $aSam[$iRNG];
        }
        return $sText;
    }

    private function check(){
        $sTicket_Id = $request->buyer_id . '_Ticket_' . $this->RSG(32); // Uses a random string generator for creating a unique id
        $check = Ticket::where('ticket_id', $sTicket_Id)->first();
        if($check){
            return true;
        }
        else{
            return false;
        }
    }
}
