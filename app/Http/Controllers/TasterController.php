<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Taster;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class TasterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json(Taster::all(), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        // Use the private validation method
        $this->validateTaster($request);

        $taster = Taster::create([
            'user_name' => $request->user_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'Taster registered successfully.',
            'taster' => $taster,
        ], 201);
    }

    public function addFavorite(Request $request, string $user_id): JsonResponse
    {
        $user = Taster::where('user_id', $user_id)->first();

        if ($user) {
            // Assuming $request->favorites contains the updated favorites
            $user->favorites = $request->favorites;
            $user->save();
            return response()->json([
                'message' => 'Favorites updated',
                'success' => true,
            ], 200);
        }

        return response()->json(['errorMessage' => 'User not found', 'success' => false], 404);
    }

    public function register(Request $request)
    {
        Log::info('Registration request data:', $request->all());

        // Validate request
        $request->validate([
            'user_name' => 'required|string|max:255',
            'email' => 'required|email|unique:tasters,email', // Ensure email is unique
            'password' => 'required|string|min:6',
        ]);

        // Generate a unique user_id
        $sUser_id = $request->user_name . '_' . $this->RSG(32);
        $hashedPassword = Hash::make($request->password);

        try {
            // Create new Taster
            $taster = Taster::create([
                "user_name" => $request->user_name,
                "user_id" => $sUser_id,
                "email" => $request->email,
                "password" => $hashedPassword,
            ]);

            // Generate the JWT token
            $token = Auth::guard('api')->login($taster);

            Log::info('New Taster created:', $taster->toArray());

            return response()->json([
                'message' => 'Registration successful',
                'success' => true,
                'token' => $token,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating Taster:', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Registration failed',
                'error' => 'An error occurred while creating the account.'
            ], 500);
        }
    }


    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Attempt to log the user in
        $user = Taster::where('email', $request->email)->first();
        if ($user && Hash::check($request->password, $user->password)) {
            // Generate the JWT token
            $token = Auth::guard('api')->login($user);

            // Construct userDetails as an associative array
            $userDetails = [
                'user_id' => $user->user_id,
                'user_name' => $user->user_name,
                'user_email' => $user->email,
                'user_image' => $user->user_image,
                'shop_id' => $user->shop_id, // Assuming you meant store_id instead of shop_id
                'user_type' => $user->user_type,
                // 'favorites' => $user->favorites,
            ];

            return response()->json([
                'message' => 'Login successful',
                'success' => true,
                'userDetails' => $userDetails,
                'token' => $token,
            ], 200);
        }

        return response()->json([
            'message' => 'Invalid credentials',
            'success' => false
        ], 401);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $user_id): JsonResponse
    {
        $user = $this->findTasterById($user_id);
        if ($user) {
            return response()->json($user, 200);
        }

        return response()->json(['message' => 'User not found'], 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $user_id): JsonResponse
    {
        // Validate incoming request
        $request->validate([
            'user_name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:tasters,email,' . $user_id,
            'password' => 'sometimes|required|string|min:8',
            'user_image' => 'sometimes|nullable',
            'student_num' => 'sometimes|nullable',
            'phone_num' => 'sometimes|nullable',
            'favorites' => 'sometimes|nullable|array',
        ]);

        // Find the target Taster
        $target = $this->findTasterById($user_id);
        if (!$target) {
            return response()->json(['message' => 'User not found', 'success' => false], 404);
        }

        // Update fields that are present in the request
        if ($request->filled('password')) {
            $target->password = Hash::make($request->password);
        }

        $target->fill($request->only([
            'user_name', 
            'email', 
            'pasword',
            'user_image', 
            'student_num', 
            'phone_num', 
            'favorites']));
        $target->save();

        return response()->json([
            'message' => 'User updated',
            'success' => true,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $user_id): JsonResponse
    {
        $user = $this->findTasterById($user_id);
        
        if (!$user) {
            return response()->json([
                'message' => 'User not found',
                'success' => false,
            ], 404);
        }

        $user->delete();

        return response()->json([
            'message' => 'User deleted',
            'success' => true,
        ], 200);
    }

    // Private method to validate Taster data
    private function validateTaster(Request $request)
    {
        $request->validate([
            'user_name' => 'required|string|max:255',
            'email' => 'required|email|unique:tasters,email',
            'password' => 'required|min:6',
        ]);
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'user_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:tasters'],
            'password' => ['required', 'string', 'min:8'], // Removed 'confirmed'
        ]);
    }

    private function findTasterById(string $user_id): ?Taster
    {
        return Taster::where('user_id', $user_id)->first();
    }

    private function RNG(int $iMin, int $iMax): int
    {
        return rand($iMin, $iMax);
    }

    private function RSG(int $iMaxLength): string
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
