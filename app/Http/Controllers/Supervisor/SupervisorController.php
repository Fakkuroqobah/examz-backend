<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\Supervisor;

class SupervisorController extends Controller
{
    private function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('supervisor')->factory()->getTTL() * 60
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        try {
            if (!$token = auth('supervisor')->attempt($credentials)) {
                return response()->json(['error' => 'invalid credentials'], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could not create token'], 500);
        }

        return $this->respondWithToken($token);
    }

    public function logout()
    {
        auth('supervisor')->logout();
        
        return response()->json(['message' => 'success'], 500);
    }

    public function getAuthenticatedUser()
    {
        return response()->json(auth('supervisor')->user());
    }

    public function refresh()
    {
        return $this->respondWithToken(auth('supervisor')->refresh());
    }
}