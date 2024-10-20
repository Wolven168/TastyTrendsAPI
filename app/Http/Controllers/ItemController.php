<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ItemController extends Controller
{

    public function indexAllItems()
    {
        return Item::all();
    }

    public function indexShopItems($id)
    {
        return Item::where('shop_id', $id);
    }

    public function createItem(Request $request)
    {
        $request->validate([
            "shop_id" => 'required',
            "item_id" => 'required',
            "item_name" => 'required',
            "item_price" => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        $sItem_id = $request->item_name . '_' . $this->RSG(32); // Uses a random string generator for creating a unique id
        return Item::create([
            "shop_id" => $request->shop_id,
            "item_id" => $sItem_id, 
            "item_name" => $request->item_name,
            "item_price" => $request->item_price,
            "item_image" =>  $request->item_image,
            "item_desc" => $request->item_desc,
        ]);
    }

    public function updateItem(Request $request, string $id)
    {
        $target = Item::find($id);
        $target->update($request->all());
        return $target;
    }
    

    public function showItem(String $id) // Retrieves data
    {
        return Item::find($id);
    }

    public function deleteItem(string $id)
    {
        Item::find($id)->delete();
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
