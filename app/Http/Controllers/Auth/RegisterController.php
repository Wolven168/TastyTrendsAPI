<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Taster;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{
    use RegistersUsers;

    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'user_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:tasters'],
            'password' => ['required', 'string', 'min:8'], // Removed 'confirmed'
        ]);
    }

    protected function create(array $data)
    {
        return Taster::create([
            'user_name' => $data['user_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    public function register(Request $request)
    {
        Log::info('Registration request data:', $request->all());

        $validator = $this->validator($request->all());

        // if ($validator->fails()) {
        //     return response()->json(['errors' => $validator->errors()], 422);
        // }

        try {
            // Check for existing email
            if (Taster::where('email', $request->email)->exists()) {
                return response()->json(['message' => 'Email already exists'], 400);
            }

            $taster = $this->create($request->all());

            return response()->json([
                'message' => 'Registration successful',
                'success' => true,
                'taster' => $taster
            ], 201);
        } catch (\Exception $e) {
            Log::error('Registration error:', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Registration failed',
                'success' => false,
                'error' => 'An error occurred while creating the account: ' . $e->getMessage()
            ], 500);
        }
    }

}
