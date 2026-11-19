<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

/** Fitur lupa / reset kata sandi (REQ-F-05). */
class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(): User
    {
        return User::factory()->create([
            'role'        => 'user',
            'is_active'   => true,
            'quota_bytes' => 5 * 1024 * 1024 * 1024,
            'password'    => Hash::make('password-lama-123'),
        ]);
    }

    public function test_guest_can_view_forgot_password_page(): void
    {
        $this->withoutVite();

        $this->get('/lupa-kata-sandi')->assertOk();
    }

    public function test_reset_link_request_creates_token_and_sends_email(): void
    {
        Notification::fake();
        $user = $this->makeUser();

        $this->post('/lupa-kata-sandi', ['email' => $user->email])
            ->assertSessionHas('status');

        $this->assertDatabaseHas('password_reset_tokens', ['email' => $user->email]);
        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    public function test_unknown_email_gets_generic_response_without_token(): void
    {
        Notification::fake();

        $this->post('/lupa-kata-sandi', ['email' => 'tidakada@example.com'])
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('password_reset_tokens', ['email' => 'tidakada@example.com']);
        Notification::assertNothingSent();
    }

    public function test_user_can_reset_password_with_valid_token(): void
    {
        Notification::fake();
        $user = $this->makeUser();

        $this->post('/lupa-kata-sandi', ['email' => $user->email]);

        $token = null;
        Notification::assertSentTo(
            $user,
            ResetPasswordNotification::class,
            function (ResetPasswordNotification $notification) use (&$token) {
                $token = $notification->token;

                return true;
            }
        );
        $this->assertNotNull($token);

        $this->withoutVite();
        $this->get('/atur-ulang-kata-sandi/' . $token . '?email=' . urlencode($user->email))
            ->assertOk();

        $this->post('/atur-ulang-kata-sandi', [
            'token'                 => $token,
            'email'                 => $user->email,
            'password'              => 'password-baru-456',
            'password_confirmation' => 'password-baru-456',
        ])->assertRedirect('/login');

        $this->assertTrue(Hash::check('password-baru-456', $user->fresh()->password));
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $user->email]);
    }

    public function test_invalid_token_is_rejected(): void
    {
        $user = $this->makeUser();

        $this->post('/atur-ulang-kata-sandi', [
            'token'                 => 'token-ngawur',
            'email'                 => $user->email,
            'password'              => 'password-baru-456',
            'password_confirmation' => 'password-baru-456',
        ])->assertSessionHasErrors('email');

        $this->assertTrue(Hash::check('password-lama-123', $user->fresh()->password));
    }
}
