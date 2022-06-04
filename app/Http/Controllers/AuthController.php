<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required',
            'password' => 'required',
        ]);
        $user = User::where([
            'login' => $request->login
        ])->first();

        $response = ['error' => null];

        if (!$user) $response['error'] = 'User not exists';
        if ($user && !Hash::check($request->password, $user->password))
            $response['error'] = 'Incorrect password';

        if (!$response['error']) {
            $user->tokens()->delete();
            $token = $user->createToken("{$user->name}_token");
            $response['token'] = $token->plainTextToken;
        }
        return $response;
    }
}
