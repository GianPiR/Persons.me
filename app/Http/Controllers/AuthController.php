<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();

            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
            ]);

            return response()->json([
                'status' => 'success',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'created_at' => $user->created_at
                ],
                'message' => 'Usuário criado com sucesso'
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao criar usuário: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Login user
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $credentials = $request->validated();

            if (Auth::attempt($credentials)) {
                $user = Auth::user();

                return response()->json([
                    'status' => 'success',
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'email_verified_at' => $user->email_verified_at
                    ],
                    'message' => 'Login realizado com sucesso'
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Email ou senha inválidos'
            ], 401);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro no login: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Logout user
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            Auth::logout();

            return response()->json([
                'status' => 'success',
                'message' => 'Logout realizado com sucesso'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro no logout: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current authenticated user
     */
    public function me(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Usuário não autenticado'
                ], 401);
            }

            return response()->json([
                'status' => 'success',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'email_verified_at' => $user->email_verified_at,
                    'created_at' => $user->created_at
                ],
                'message' => 'Dados do usuário recuperados com sucesso'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao obter dados do usuário: ' . $e->getMessage()
            ], 500);
        }
    }
}
