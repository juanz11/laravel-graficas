<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SimpleAuthController extends Controller
{
    public function showLoginForm(): View
    {
        return view('login');
    }

    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $expectedUser = (string) env('SIMPLE_AUTH_USER', 'admin');
        $expectedPass = (string) env('SIMPLE_AUTH_PASS', 'admin');

        if ($validated['username'] !== $expectedUser || $validated['password'] !== $expectedPass) {
            return back()->withErrors([
                'username' => 'Usuario o clave incorrectos.',
            ])->onlyInput('username');
        }

        $request->session()->put('simple_auth.logged_in', true);

        $intended = $request->session()->pull('url.intended');

        return redirect()->to($intended ?: '/');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('simple_auth.logged_in');

        return redirect()->route('simple.login');
    }
}
