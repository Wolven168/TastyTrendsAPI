<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use App\Mail\MailScent;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    /**
     * Reset the user's password.
     */
    public function reset(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Attempt to reset the password
        $response = Password::reset($request->only('email', 'password', 'password_confirmation', 'token'), function ($user, $password) {
            // Hash the new password
            $user->password = Hash::make($password);
            $user->save();

            // Send confirmation email
            $data = [
                'name' => $user->user_name, // Changed to 'user_name' based on your model
                'message' => 'Your password has been successfully reset!',
            ];

            Mail::to($user->email)->send(new MailScent($data));
        });

        // Return the appropriate response based on the result
        return $response == Password::PASSWORD_RESET
            ? response()->json(['message' => __('Your password has been reset!')], 200)
            : response()->json(['message' => __('Unable to reset password. Please try again.')], 400);
    }
}
