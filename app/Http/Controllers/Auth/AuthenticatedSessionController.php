<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Login form megjelenítése
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Bejelentkezés a TÁVOLI REST API-n keresztül
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Lokális validáció (email, password mezők ellenőrzése)
        $request->validated();

        // Hívjuk a REST API /users/login végpontját
        $response = Http::api()->post('users/login', [
            'email'    => $request->email,
            'password' => $request->password,
        ]);

        if ($response->successful()) {
            $token = $response['token'] ?? null;
            $user  = $response['user']  ?? [];

            // Session regenerálása biztonsági okokból
            $request->session()->regenerate();

            // Token + user adatok eltárolása session-be
            session([
                'api_token'  => $token,
                'user_name'  => $user['name']  ?? null,
                'user_email' => $user['email'] ?? null,
            ]);

            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'Hibás bejelentkezési adatok.',
        ]);
    }

    /**
     * Kijelentkezés
     */
    public function destroy(Request $request): RedirectResponse
    {
        // API token + user adatok törlése a session-ből
        $request->session()->forget([
            'api_token',
            'user_name',
            'user_email',
        ]);

        // Standard session invalidálás
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
