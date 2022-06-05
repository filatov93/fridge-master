<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/login",
     *     tags={"authentication"},
     *     summary="Log in",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="login",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string"
     *                 ),
     *                 example={"login": "test_user_1", "password": "password1"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *             ),
     *             @OA\Property(
     *                 property="token",
     *                 type="string",
     *             ),
     *             @OA\Examples(
     *                  example="result",
     *                  value={"error": null, "token": "1|AiKTkfvTwAE2vnbq55PVgPTreXVkfeugyyilXThO"},
     *                  summary="An result object."),
     *         )
     *     )
     * )
     */
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
