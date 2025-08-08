<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController 
{
    public function index(Request $request) {
        $credentials = $request->validate([
            "email"=> "required|email",
            "password"=> "required",
        ]);

        $user = User::where(['email' => $request->email, 'user_type' => 'agent'])->first();

        if(!$user) {
            return response()->json(['message' => 'Invalid Credentials'], 404);
        }

        if(!Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid Credentials'], 404);
        }

        $user->tokens()->delete();
        Auth::login($user);
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'data' => ['token' => $token]
        ]); 
    }

    public function logout(Request $request) {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
}
