<?php

namespace App\Http\Controllers;

use App\Actions\Auth\RegisterUserAction;
use App\Actions\Auth\LoginUserAction;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    function register(RegisterRequest $request, RegisterUserAction $action) {
        $result = $action->execute($request->validated());

        return response()->json([
            'ok' => true,
            'user' => $result['user'],
            'token' => $result['token'],
        ], 201);
    }

    public function login(LoginRequest $request, LoginUserAction $action) {
        $result = $action->execute($request->validated());

        if (! $result) {
            return response()->json([
                'ok' => false,
                'msg' => 'Credenciais inválidas',
            ], 401);
        }

        return response()->json([
            'ok' => true,
            'user' => $result['user'],
            'token' => $result['token'],
        ]);
    }

    function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'ok' => true,
            'msg' => 'Logout realizado com sucesso',
        ]);
    }

    function me(Request $request) {
        return response()->json([
            'ok' => true,
            'user' => $request->user(),
        ]);
    }
}