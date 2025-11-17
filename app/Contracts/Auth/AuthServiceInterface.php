<?php

namespace App\Contracts\Auth;

use App\Models\User;

interface AuthServiceInterface
{
    public function register(string $name, string $email, string $password): User;

    public function login(string $email, string $password): string;

    public function logout(User $user, ?string $tokenId = null): void;

    public function requestPasswordReset(string $email): void;

    public function resetPassword(string $token, string $newPassword): void;
}
