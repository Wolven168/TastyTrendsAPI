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
                    'id' => $emailCheck->user_id,
                ], 
                200);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Taster::where('user_id', $id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $target = Taster::find($id);
        if ($request->password != null) {
            $new_psw = Hash::make($request->password);
            $request->password = $new_psw;
        }
        $target->update($request->all());
        return $target;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Taster::find($id)->delete();
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
