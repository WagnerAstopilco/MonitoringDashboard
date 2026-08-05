<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (!Auth::attempt($credentials)) {

            return response()->json([
                'message' => 'Credenciales inválidas.'
            ], 401);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $token = $user
            ->createToken('auth_token')
            ->plainTextToken;

        return response()->json([

            'access_token' => $token,

            'token_type' => 'Bearer',

            'user' => $user

        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}
