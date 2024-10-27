<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Taster;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class TasterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Taster::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        return Taster::create($request->all());
    }

    public function register(Request $request) 
    {
        Log::info($request->all()); // Log the request data
        $request->validate([
            'user_name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $sHashed = Hash::make($request->password);
        $sUser_id = $request->user_name . '_' . $this->RSG(32);

        $emailCheck = Taster::where('email', $request->email)->first();
        if (!$emailCheck) {
            Taster::create([
                "user_name" => $request->user_name,
                "user_id" => $sUser_id,
                "email" => $request->email,
                "password" => $sHashed,
            ]);
            return response()->json([
                'message' => 'Registration successful',
                'success' => true,
            ], 200);
        } else {
            return response()->json(['message' => 'Email has already been taken'], 400);
        }
    }



    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $emailCheck = Taster::where('email', $request->email)->first();

        if ($emailCheck && Hash::check($request->password, $emailCheck->password)) {
            return response()->json(
                [
                    'message' => 'Login successful',
                    'success' => true,
                    'user_id' => $emailCheck->user_id,
                    'user_name' => $emailCheck->user_name,
                    'user_image' => $emailCheck->user_image,
                    'shop_id' => $emailCheck->shop_id,
                ], 
                200);
        }

        return response()->json([
            'message' => 'Invalid credentials',
            'success' => false
        ], 401);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $user_id)
    {
        return Taster::where('user_id', $user_id);
    }

    public function getUserName(String $user_id)
    {
        $user = Taster::where('user_id', $user_id);
        if($user) {
            return response()->json([
                'message' => 'User found',
                'success' => true,
                'user_name' => $user->user_name,
            ], 401);
        }
        else {
            return response()->json(['errorMessage' => 'User not found', 'success' => false,], 401);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $user_id)
    {
        // Validate incoming request
        $request->validate([
            'user_name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|max:255|unique:tasters,email,' . $user_id,
            'password' => 'sometimes|nullable|string|min:8',
            'shop_id' => 'sometimes|required|string|max:255',
            'user_image' => 'sometimes|required|string|max:255',
            'phone_num' => 'sometimes|required|string|max:255',
            'student_num' => 'sometimes|required|string|max:255',
        ]);

        // Find the target Taster
        $target = Taster::where('user_id', $user_id)->first();
        if (!$target) {
            return response()->json(['message' => 'User not found', 'success' => false], 404);
        }

        // Update password if provided
        if ($request->filled('password')) {
            $target->password = Hash::make($request->password);
        }

        // Only update fields that are present in the request
        $target->fill($request->only([
            'user_name', 
            'user_email', 
            'store_id',
            'user_image',
            'phone_num',
            'student_num',
        ])); // Exclude password if already hashed
        $target->save(); // Save the updated model

        return response()->json([
            'message' => 'User updated',
            'success' => true,
        ], 200);
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(String $user_id)
    {
        try {
            $deleted = Taster::where('user_id', $user_id)->delete();
            return response()->json([
                'message' => 'User deleted',
                'success' => true,
            ], 200);
        }
        catch (Exception $e){
            return response()->json([
                'message' => 'User not found',
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
