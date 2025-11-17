<?php

namespace App\Services\Auth;

use App\Contracts\Auth\AuthServiceInterface;
use App\Mail\PasswordResetMail;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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

    public function requestPasswordReset(string $email): void
    {
        /** @var User|null $user */
        $user = User::query()->where('email', $email)->first();

        if ($user === null) {
            return;
        }

        $plainToken = bin2hex(random_bytes(32));
        $hashedToken = Hash::make($plainToken);
        $expiresAt = CarbonImmutable::now()->addHour();

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => $hashedToken,
                'expires_at' => $expiresAt,
            ]
        );

        $appUrl = config('app.url', 'http://localhost');
        $resetUrl = $appUrl.'/reset-password?token='.$plainToken.'&email='.urlencode($email);

        Mail::to($email)->send(new PasswordResetMail($resetUrl));
    }

    public function resetPassword(string $token, string $newPassword): void
    {
        $record = DB::table('password_reset_tokens')
            ->where('expires_at', '>', now())
            ->get()
            ->first(function ($row) use ($token): bool {
                return Hash::check($token, (string) $row->token);
            });

        if ($record === null) {
            throw ValidationException::withMessages([
                'token' => ['The provided reset token is invalid or expired.'],
            ]);
        }

        /** @var User|null $user */
        $user = User::query()->where('email', $record->email)->first();

        if ($user === null) {
            throw ValidationException::withMessages([
                'email' => ['User not found for this reset token.'],
            ]);
        }

        $user->password = Hash::make($newPassword);
        $user->save();

        DB::table('password_reset_tokens')
            ->where('email', $record->email)
            ->delete();
    }
    }
