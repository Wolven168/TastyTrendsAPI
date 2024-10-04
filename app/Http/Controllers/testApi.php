<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Test;
use validator;

class testApi extends Controller
{
     /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return Test::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([ #Validates if the information fulfills the requirements
            'username' => 'required',
            'email' => 'required',
            'password' => 'required',
        ]);

        // Miscellaneous functions
        $sHashed = Hash::make($request->password); # Creates a hashed version of the password
        $sUser_id = $request->username . '_' . $this->RSG(32);

        // Creating the Test to add to the database using the values provided
        return Test::create([
            "username" => $request->username,
            "user_id" => $sUser_id, // Uses a random string generator for creating a
            "email" => $request->email,
            "password" => $sHashed,
        ]);
    }

    public function register(Request $request) 
    {
        $request->validate([ #Validates if the information fulfills the requirements
            'username' => 'required',
            'email' => 'required',
            'password' => 'required',
        ]);

        // Miscellaneous functions
        $sHashed = Hash::make($request->password); # Creates a hashed version of the password
        $sUser_id = $request->username . '_' . $this->RSG(32);

        $test = Test::where('email', $request->email)->first(); // Checks if provided email is already in database
        
        if(!$test) {
            // Creating the Test to add to the database using the values provided
            return Test::create([
                "username" => $request->username,
                "user_id" => $sUser_id, // Uses a random string generator for creating a
                "email" => $request->email,
                "password" => $sHashed,
            ]);
        }
        else {
            return "Email has already been taken";
        }
        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Test::find($id);
    }

    public function login(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $test = Test::where('email', $request->email)->first();

        // Check if the user exists and verify the password
        if ($test && Hash::check($request->password, $test->password)) {
            // Password is correct, log the user in (or generate a token)
            return response()->json(['message' => 'Login successful'], 200);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $target = Test::find($id);
        $target->update($request->all());
        return $target;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(string $id)
    {
        Test::find($id)->delete();
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