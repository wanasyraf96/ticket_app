<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    private $apiToken;
    public function __construct()
    {
        $this->apiToken = uniqid(base64_encode(Str::random()));
    }
    //Sign In
    public function signin(Request $request)
    {
        $request->validate([
            'email' => 'required|exists:users,email|email',
            'password' => 'required|string'
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Credentials mismatch'
            ], 401);
        }

        $user = Auth::user();
        $token = $user->createToken($this->apiToken)->plainTextToken;
        return response()->json([
            "access_token" => $token,
        ]);
    }

    // Sign Out
    public function signout()
    {
        Auth::user()->tokens->each(function ($token) {
            $token->delete();
        });

        return response()->noContent();
    }
}
