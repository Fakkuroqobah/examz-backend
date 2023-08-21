<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\Admin;

class AdminController extends Controller
{
    private function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('admin')->factory()->getTTL() * 60
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        try {
            if (!$token = auth('admin')->attempt($credentials)) {
                return response()->json(['error' => 'Username atau password salah'], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Tidak dapat membuat token'], 500);
        }

        return $this->respondWithToken($token);
    }

    public function logout()
    {
        auth('admin')->logout();
        
        return response()->json(['message' => 'success'], 200);
    }

    public function getAuthenticatedUser()
    {
        return response()->json(auth('admin')->user());
    }

    public function refresh()
    {
        return $this->respondWithToken(auth('admin')->refresh());
    }

    public function teacherAll()
    {
        $data = Teacher::all();

        return response()->json([
            'data' => $data,
            'message' => 'Success'
        ], 200);
    }

    public function studentByClass($class)
    {
        $data = Student::where('class', $class)->get();

        return response()->json([
            'data' => $data,
            'message' => 'Success'
        ], 200);
    }
}