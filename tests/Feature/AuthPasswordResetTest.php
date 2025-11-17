<?php


namespace Tests\Feature;

use App\Mail\PasswordResetMail;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AuthPasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_sends_password_reset_email_when_user_exists(): void
    {
        Mail::fake();

        /** @var User $user */
        $user = User::factory()->create([
            'email' => 'user@example.com',
        ]);

        $response = $this->postJson('/api/v1/auth/password/forgot', [
            'email' => $user->email,
        ]);

        $response
            ->assertOk()
            ->assertJson([
                'message' => 'If the email exists, a reset link has been sent.',
            ]);

        Mail::assertSent(PasswordResetMail::class, function (PasswordResetMail $mail) use ($user): bool {
            return $mail->hasTo($user->email);
        });

        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => $user->email,
        ]);
    }

    public function test_it_does_not_fail_when_email_does_not_exist(): void
    {
        Mail::fake();

        $response = $this->postJson('/api/v1/auth/password/forgot', [
            'email' => 'non-existent@example.com',
        ]);

        $response
            ->assertOk()
            ->assertJson([
                'message' => 'If the email exists, a reset link has been sent.',
            ]);

        Mail::assertNothingSent();

        $this->assertDatabaseCount('password_reset_tokens', 0);
    }

    public function test_it_resets_password_with_valid_token(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('old-password'),
        ]);

        $plainToken = 'test-reset-token-123';
        $hashedToken = Hash::make($plainToken);

        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => $hashedToken,
            'expires_at' => CarbonImmutable::now()->addHour(),
            'created_at' => now(),
        ]);

        $response = $this->postJson('/api/v1/auth/password/reset', [
            'token' => $plainToken,
            'password' => 'new-password-123',
        ]);

        $response
            ->assertOk()
            ->assertJson([
                'message' => 'Password has been reset successfully.',
            ]);

        $user->refresh();

        $this->assertTrue(
            Hash::check('new-password-123', $user->password),
            'User password should be updated to the new value.'
        );

        $this->assertDatabaseMissing('password_reset_tokens', [
            'email' => $user->email,
        ]);
    }

    public function test_it_returns_validation_error_for_invalid_or_expired_token(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('old-password'),
        ]);

        $plainToken = 'expired-token';
        $hashedToken = Hash::make($plainToken);

        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => $hashedToken,
            'expires_at' => CarbonImmutable::now()->subMinute(),
            'created_at' => now(),
        ]);

        $response = $this->postJson('/api/v1/auth/password/reset', [
            'token' => $plainToken,
            'password' => 'new-password-123',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['token']);

        $user->refresh();

        $this->assertTrue(
            Hash::check('old-password', $user->password),
            'User password should remain unchanged when token is invalid or expired.'
        );
    }
}
