<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');

    }

    public function store(LoginRequest $request): RedirectResponse
{
    $request->authenticate();
    
    // 💥 FIX: Laravel needs to know if it should set the long-term cookie
    $request->session()->regenerate();

    // The $request->authenticate() usually calls Auth::attempt() internally.
    // If you are using standard Breeze, it uses the boolean $request->boolean('remember').

    return redirect()->intended(route('dashboard', absolute: false));
}

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'captcha' => ['required', 'captcha'],
        ];
    }

}
