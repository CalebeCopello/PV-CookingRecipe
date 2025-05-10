<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required',
            'email' => 'required',
            'password' => 'required|min:6'
        ]);
        $checkUser = User::whereRaw('LOWER(name) LIKE ?', [strtolower($request->name)])->first();
        $checkEmail = User::whereRaw('LOWER(email) LIKE ?', [strtolower($request->email)])->first();
        if ($checkUser) {
            return response()->json(['status' => 'error', 'message' => 'The Name is already taken. Please choose another one'], 409);
        }
        if ($checkEmail) {
            return response()->json(['status' => 'error', 'message' => 'The Email is already taken'], 409);
        }
        $user = User::create($fields);
        $token = $user->createToken($user->name);

        return response()->json(['info' => $user, 'token' => $token], 201);
    }

        public function login(Request $request)
    {
        $request->validate(
            [
                'email' => 'required|exists:users,email',
                'password' => 'required',
            ]
        );
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid Email or Password.',
            ], 401);
        }

        $token = $user->createToken($user->name);

        return [
            'info' => $user,
            'token' => $token,
        ];
    }

        public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return [
            'message' => 'You have logged out.',
        ];
    }
}
