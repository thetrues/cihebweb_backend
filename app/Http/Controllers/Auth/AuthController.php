<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        // Authentication logic goes here
        if (Auth::attempt($validated)) {
            if ($request->hasSession()) {
                $request->session()->regenerate();
            }
            $user = Auth::user();
             $token = $request->user()->createToken('auth_token')->plainTextToken;
            return response()->json(['message' => 'Login successful', 'user' => $user, 'token' => $token], 200);
        }else {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return response()->json(['message' => 'Logout successful'], 200);
    }


    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // User creation logic goes here
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        return response()->json(['message' => 'Registration successful', 'user' => $user], 201);
    }
}
