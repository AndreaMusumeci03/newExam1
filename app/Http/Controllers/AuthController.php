<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email:rfc,dns|max:255|unique:users',
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)->letters()->mixedCase()->numbers()->symbols(),
            ],
        ], [
            'name.required' => 'Il nome è obbligatorio.',
            'email.required' => "L'email è obbligatoria.",
            'email.email' => "L'email deve essere valida.",
            'email.unique' => 'Questa email è già registrata.',
            'password.required' => 'La password è obbligatoria.',
            'password.confirmed' => 'Le password non coincidono.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        return redirect()->route('news.index')->with('success', 'Registrazione completata con successo!');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => "L'email è obbligatoria.",
            'email.email' => "L'email deve essere valida.",
            'password.required' => 'La password è obbligatoria.',
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('news.index'))->with('success', 'Login effettuato con successo!');
        }

        return back()->withErrors([
            'email' => 'Le credenziali inserite non sono corrette.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Logout effettuato con successo!'
            ]);
        }

        return redirect()->route('home')->with('success', 'Logout effettuato con successo!');
    }
}