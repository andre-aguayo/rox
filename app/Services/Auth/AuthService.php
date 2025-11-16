<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Contracts\Auth\AuthServiceInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService implements AuthServiceInterface
{
    public function register(string $name, string $email, string $password): User
    {
        return User::query()->create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);
    }

    public function login(string $email, string $password): string
    {
        /** @var User|null $user */
        $user = User::query()->where('email', $email)->first();

        if ($user === null || Hash::check($password, $user->password) === false) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('api-token');

        return $token->plainTextToken;
    }

    public function logout(User $user, ?string $tokenId = null): void
    {
        if ($tokenId !== null) {
            $user->tokens()->where('id', $tokenId)->delete();

            return;
        }

        $user->currentAccessToken()?->delete();
    }
}
