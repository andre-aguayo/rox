<?php

declare(strict_types=1);

namespace App\Contracts\Auth;

use App\Models\User;

interface AuthServiceInterface
{
    public function register(string $name, string $email, string $password): User;

    public function login(string $email, string $password): string;

    public function logout(User $user, ?string $tokenId = null): void;
}
