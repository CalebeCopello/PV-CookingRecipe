<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;


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
}
