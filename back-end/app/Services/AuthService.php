<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use App\Exceptions\InvalidCredentialsException;
use App\Exceptions\TooManyLoginAttemptsException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthService
{
    
    public function __construct(
        private readonly UserRepository $userRepository
    ) {}


    public function register(array $dados): User
    {
        $dados['password'] = Hash::make($dados['password']);

        return $this->userRepository->create($dados);
    }

    public function login(string $email, string $password, string $ip): User
    {
        $chave = Str::lower($email) . '|' . $ip;

        if (RateLimiter::tooManyAttempts($chave, 5)) {
            throw new TooManyLoginAttemptsException();
        }

        $user = $this->userRepository->findByEmail($email);

        if (!$user || !Hash::check($password, $user->password)) {
            RateLimiter::hit($chave, 60);

            throw new InvalidCredentialsException();
        }

        RateLimiter::clear($chave);

        Auth::guard('web')->login($user);

        request()->session()->regenerate();

        return $user;
    }

    public function logout(): void
    {
        Auth::guard('web')->logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }
}
