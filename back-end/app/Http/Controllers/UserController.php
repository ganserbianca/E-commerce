<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService
    ) {}

    // GET /api/perfil
    public function profile(Request $request): UserResource
    {
        $user = $this->userService->show($request->user()->id);

        return new UserResource($user);
    }

    // PUT /api/perfil
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $user = $this->userService->update(
            $request->user()->id,
            $request->validated()
        );

        return response()->json([
            'message' => 'Perfil atualizado com sucesso.',
            'user'    => (new UserResource($user))->resolve(),
        ]);
    }

    // PATCH /api/perfil/senha
    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        $this->userService->updatePassword(
            $request->user()->id,
            $request->validated('current_password'),
            $request->validated('password')
        );

        return response()->json([
            'message' => 'Senha alterada com sucesso.',
        ]);
    }
}