<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\Taster; // Ensure this is imported if you're using it
use Illuminate\Http\Request;
use Exception;

class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $shops = Shop::all();
        return response()->json([
            'shops' => $shops,
            'success' => true,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "shop_owner_id" => 'required',
            "shop_name" => 'required|string',
            "shop_image" => 'sometimes|string',
        ]);
        
        $sShop_id = $request->shop_name . '_' . $this->RSG(32);
        
        // Create shop
        Shop::create([
            'shop_id' => $sShop_id,
            "shop_owner_id" => $request->shop_owner_id,
            "shop_name" => $request->shop_name,
            "shop_image" => $request->shop_image,
        ]);

        // Update user shop_id
        $user = Taster::where("user_id", $request->shop_owner_id)->first();
        if ($user) {
            $user->update(["shop_id" => $sShop_id]);
            $user->update(['user_type' => 'Owner']);
        }

        return response()->json([
            'message' => 'Shop created',
            'success' => true,
        ], 201); // 201 for resource created
    }

    /**
     * Show shop ticket data by shop_id.
     */
    public function ShopTicketData(String $shop_id)
    {
        $shop = Shop::where('shop_id', $shop_id)->first();

        if ($shop) {
            return response()->json([
                'message' => 'Shop found',
                'success' => true,
                'shop_name' => $shop->shop_name,
            ], 200);
        }

        return response()->json([
            'message' => 'Shop not found',
            'success' => false,
        ], 404);
    }

    public function getShopDetails(String $shop_id)
    {
        $shop = Shop::where('shop_id', $shop_id)->first();
        $user = Taster::where('shop_id', $shop_id)->where('user_type', 'Owner');
        
        if ($shop) {
            return response()->json([
                'message' => 'Shop found',
                'success' => true,
                'shop_name' => $shop->shop_name,
                'shop_email' => $user->email,
                'shop_image' => $shop->shop_image,
            ], 200);
        }

        return response()->json([
            'message' => 'Shop not found',
            'success' => false,
        ], 404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(String $shop_id)
    {
        try {
            $deleted = Shop::where('shop_id', $shop_id)->delete();
            if ($deleted) {
                return response()->json([
                    'message' => 'Shop deleted',
                    'success' => true,
                ], 200); // Success
            }
            return response()->json([
                'message' => 'Shop not found',
                'success' => false,
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error: ' . $e->getMessage(),
                'success' => false,
            ], 500); // Internal server error
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
