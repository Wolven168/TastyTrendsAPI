<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{

    public function indexAllItems()
    {
        return Item::all();
    }

    public function indexShopItems($shop_id)
    {
        $items = Item::where('shop_id', $shop_id)->get();
        return response()->json([
            "items" => $items,
            'message' => 'Items found',
            'success' => true,
        ], 200);
    }

    public function createItem(Request $request)
    {
        $request->validate([
            "shop_id" => 'required',
            "item_name" => 'sometimes|string',
            "item_price" => 'required',
            "item_image" => 'sometimes',
        ]);
        $sItem_id = $request->item_name . '_' . $this->RSG(32); // Uses a random string generator for creating a unique id
        Item::create([
            "shop_id" => $request->shop_id,
            'item_id' => $sItem_id,
            "item_name" => $request->item_name,
            "item_price" => $request->item_price,
            "item_image" =>  $request->item_image,
            "available" => false,
        ]);
        return response()->json([
            'message' => 'Item created',
            'success' => true,
        ], 200);
    }

    public function updateItem(Request $request, string $item_id)
    {
        // Validate incoming request
        $request->validate([
            'item_name' => 'sometimes|string',
            'item_price' => 'sometimes|numeric',
            'item_image' => 'sometimes|nullable|string',
        ]);

        // Find the target Item
        $target = Item::where('item_id', $item_id);
        if ($target != null) {
            return response()->json(['message' => 'Item not found', 'success' => false], 404);
        }

        // Only update fields that are present in the request
        $target->fill($request->only(['item_name', 'item_price', 'item_image']));
        $target->save(); // Save the updated model

        return response()->json([
            'message' => 'Item updated',
            'success' => true,
        ], 200);
    }

    public function updateItemAvailability(String $item_id, Request $request) {
        // Validate incoming request
        $request->validate([
            'available' => 'sometimes|boolean',
        ]);
        $target = Item::where('item_id', $item_id)->first();
        if ($target) {
            $target->fill(['available' => $request->available]); // Correctly update status
            $target->save();
            return response()->json(['message' => 'Ticket Updated', 'success' => true], 201);
        }
        return response()->json(['message' => 'Ticket not found', 'success' => false, ], 404);
    }

    public function showItem(String $item_id) // Retrieves data
    {
        try {
            $item = Item::where('item_id', $item_id)->get();
            return response()->json([
                'message' => 'Item found',
                'success' => true,
                'item' => $item,
            ], 200);
        }
        catch (Exception $e){
            return response()->json([
                'message' => 'Item not found',
                'success' => false,
            ], 404);
        }
    }

    public function showItemTicket(String $item_id) {
        try {
            $item = Item::where('item_id', $item_id)->get();
            return response()->json([
                'message' => 'Item found',
                'success' => true,
                'item_name' => $item->item_name,
                'item_image' => $item->item_image,
            ], 200);
        }
        catch (Exception $e){
            return response()->json([
                'message' => 'Item not found',
                'success' => false,
            ], 404);
        }
    }

    public function deleteItem(string $item_id)
    {
        try {
            $deleted = Item::where('item_id', $item_id)->delete();
            return response()->json([
                'message' => 'Item deleted',
                'success' => true,
            ], 200);
        }
        catch (Exception $e){
            return response()->json([
                'message' => 'Item not found',
                'success' => false,
            ], 404);
        }
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
}
