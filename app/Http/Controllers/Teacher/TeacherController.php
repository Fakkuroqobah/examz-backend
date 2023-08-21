<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\Teacher;

class TeacherController extends Controller
{
    private function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('teacher')->factory()->getTTL() * 60
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        try {
            if (!$token = auth('teacher')->attempt($credentials)) {
                return response()->json(['error' => 'Username atau password salah'], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Tidak dapat membuat token'], 500);
        }

        return $this->respondWithToken($token);
    }

    public function logout()
    {
        auth('teacher')->logout();
        
        return response()->json(['message' => 'success'], 200);
    }

    public function getAuthenticatedUser()
    {
        return response()->json(auth('teacher')->user());
    }

    public function refresh()
    {
        return $this->respondWithToken(auth('teacher')->refresh());
    }
}