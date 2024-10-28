<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ImageController extends Controller
{
    public function uploadToImgur(Request $request)
    {
        // Validate the request
        $request->validate([
            'image' => 'required|image|max:5000', // Max 5MB
        ]);

        // Convert the image to base64 format
        $image = base64_encode(file_get_contents($request->file('image')->path()));

        // Send the request to Imgur
        $response = Http::withHeaders([
            'Authorization' => 'Client-ID 8b791601ce81511',
        ])->post('https://api.imgur.com/3/image', [
            'image' => $image,
            'type' => 'base64',
        ]);

        // Check if the upload was successful
        if ($response->successful()) {
            $link = $response->json()['data']['link'];
            return response()->json([
                'message' => 'Image uploaded successfully!',
                'link' => $link,
            ], 201);
        }

        return response()->json([
            'message' => 'Image upload failed.',
            'error' => $response->json(),
        ], 500);
    }
}
