<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    // Cadastro de cliente
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->authService->register($request->validated());

        return (new UserResource($user))
            ->response()
            ->setStatusCode(201);
    }

    // Login por sessão/cookie
    public function login(LoginRequest $request): JsonResponse
    {
        $user = $this->authService->login(
            $request->validated('email'),
            $request->validated('password'),
            (string) $request->ip()
        );

        return response()->json([
            'message' => 'Login realizado com sucesso!',
            'user'    => (new UserResource($user))->resolve(),
        ]);
    }

    // Logout: destrói a sessão
    public function logout(): JsonResponse
    {
        $this->authService->logout();

        return response()->json([
            'message' => 'Logout realizado com sucesso!',
        ]);
    }
}