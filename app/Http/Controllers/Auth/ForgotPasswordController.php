<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    /**
     * Send a password reset link to the given email.
     */
    

public function sendResetLinkEmail(Request $request)
{
    $request->validate(['email' => 'required|email']);

    // Log the request for debugging
    Log::info('Forgot password request for email: ' . $request->email);

    $response = Password::sendResetLink($request->only('email'));

    if ($response == Password::RESET_LINK_SENT) {
        return response()->json(['message' => __('A password reset link has been sent to your email.')], 200);
    } else {
        Log::error('Failed to send reset link: ' . $response);
        return response()->json(['message' => __('Unable to send reset link. Please check your email address.')], 400);
    }
}

}
