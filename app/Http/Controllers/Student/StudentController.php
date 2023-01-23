<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\Student;

class StudentController extends Controller
{
    private function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('student')->factory()->getTTL() * 60
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        try {
            if (!$token = auth('student')->attempt($credentials)) {
                return response()->json(['error' => 'Username atau password salah'], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'tidak dapat membuat token'], 500);
        }

        return $this->respondWithToken($token);
    }

    public function logout()
    {
        auth('student')->logout();
        
        return response()->json(['message' => 'success'], 500);
    }

    public function getAuthenticatedUser()
    {
        return response()->json(auth('student')->user());
    }

    public function refresh()
    {
        return $this->respondWithToken(auth('student')->refresh());
    }
}