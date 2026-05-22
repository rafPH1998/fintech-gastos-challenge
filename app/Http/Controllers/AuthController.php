<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function registerUser(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        $usuario = User::create([
            'name' => $data['nome'],
            'email' => $data['email'],
            'password' => $data['senha'],
        ]);

        $token = $usuario->createToken('tokenAcesso')->plainTextToken;

        return response()->json([
            'mensagem' => 'Usuário registrado com sucesso.',
            'usuario' => $this->formatarUsuario($usuario),
            'token' => $token,
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $data = $request->validated();

        $usuario = User::where('email', $data['email'])->first();

        if (! $usuario || ! Hash::check($data['senha'], $usuario->password)) {
            throw ValidationException::withMessages([
                'email' => ['E-mail ou senha incorretos.'],
            ]);
        }

        $token = $usuario->createToken('tokenAcesso')->plainTextToken;

        return response()->json([
            'mensagem' => 'Login realizado com sucesso.',
            'usuario' => $this->formatarUsuario($usuario),
            'token' => $token,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'mensagem' => 'Logout realizado com sucesso.',
        ]);
    }

    public function getUserLogado(Request $request): JsonResponse
    {
        return response()->json([
            'usuario' => $this->formatarUsuario($request->user()),
        ]);
    }

    private function formatarUsuario(User $usuario): array
    {
        return [
            'id' => $usuario->id,
            'nome' => $usuario->name,
            'email' => $usuario->email,
        ];
    }
}
