<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $shops = Shop::all();
        return $shops;
        // return response()->json([
        //     'shops' => $shops,
        //     'success' => true,
        // ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "shop_owner_id" => 'required',
            "shop_name" => 'required|string',
            "shop_image" => 'sometimes',
        ]);
        $sShop_id = $request->shop_name . '_' . $this->RSG(32); // Uses a random string generator for creating a unique id
        $ticket = Shop::create([
            'shop_id' => $sShop_id,
            "shop_owner_id" => $request->shop_owner_id,
            "shop_name" => $request->shop_name,
            "shop_image" =>  $request->shop_image,
        ]);
        return response()->json([
            'message' => 'shop created',
            'success' => true,
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Shop $shop)
    {
        //
    }

    public function ShopTicketData(String $shop_id)
    {
        $shop = Shop::where('shop_id', $shop_id);
        return response()->json([
            'message' => 'Shop found',
            'success' => true,
            'shop_name' => $shop->shop_name,
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Shop $shop)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Shop $shop)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(String $shop_id)
    {
        try {
            $deleted = Shop::where('shop_id', $shop_id)->delete();
            return response()->json([
                'message' => 'Shop deleted',
                'success' => true,
            ], 404);
        }
        catch (Exception $e){
            return response()->json([
                'message' => 'Shop not deleted',
                'success' => false,
            ], 404);
        }
    }

    private function RNG($iMin, $iMax)
    {
        return rand($iMin, $iMax);
    }

    private function RSG($iMaxLength)
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
}
