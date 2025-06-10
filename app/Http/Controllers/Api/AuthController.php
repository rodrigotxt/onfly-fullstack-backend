<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Registra um novo usuário
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
                'message' => 'Usuário registrado com sucesso',
                'user' => $user,
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            Log::error('Registration error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Erro ao registrar usuário',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Autentica um usuário e retorna um token JWT
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $credentials = $request->only(['email', 'password']);

            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'message' => 'Credenciais inválidas'
                ], Response::HTTP_UNAUTHORIZED);
            }
    
            return $this->respondWithToken($token);
    
        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Erro ao realizar login',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Retorna os dados do usuário autenticado
     *
     * @return JsonResponse
     */
    public function me(): JsonResponse
    {
        try {
            return response()->json([
                'user' => Auth::user()
            ]);

        } catch (\Exception $e) {
            Log::error('Get user error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Erro ao obter informações do usuário',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Renova o token JWT
     *
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
    {
        try {
            return $this->respondWithToken(Auth::refresh());

        } catch (\Exception $e) {
            Log::error('Refresh token error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Erro ao renovar token',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Invalida o token JWT (logout)
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        try {
            Auth::logout();
            return response()->json([
                'message' => 'Logout realizado com sucesso'
            ]);

        } catch (\Exception $e) {
            Log::error('Logout error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Erro ao realizar logout',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Formata a resposta com o token JWT
     *
     * @param string $token
     * @return JsonResponse
     */
    protected function respondWithToken(string $token): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60,
            'user' => Auth::user()
        ]);
    }
}