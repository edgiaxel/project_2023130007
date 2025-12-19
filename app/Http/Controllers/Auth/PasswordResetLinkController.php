<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email', 'exists:users,email']]);

        $otp = rand(100000, 999999);
        $token = \Illuminate\Support\Str::random(60); // Keep token for Laravel's internal reset logic

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            ['token' => $token, 'otp' => $otp, 'created_at' => now()]
        );

        Mail::raw("Your Starium Reset Code is: {$otp}", function ($message) use ($request) {
            $message->to($request->email)->subject('Warp Key Reset Code');
        });

        // 💥 CHANGE THIS: Instead of back(), go to the reset page
        // We pass the email in the URL so the next page knows who is resetting
        return redirect()->route('password.reset', ['token' => $token, 'email' => $request->email])
            ->with('status', 'OTP sent! Please enter the code and your new password.');
    }
}
