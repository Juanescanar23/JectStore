<?php

namespace App\Http\Controllers\Landlord;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('landlord.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::guard('landlord')->attempt($credentials, true)) {
            $request->session()->regenerate();
            $user = Auth::guard('landlord')->user();
            $defaultRedirect = ($user && $user->role === 'superadmin') ? '/admin' : '/portal/billing';

            return redirect()->intended($defaultRedirect);
        }

        return back()->withErrors([
            'email' => 'Credenciales inválidas.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('landlord')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
