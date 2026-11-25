<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config()->set('cloudflare-turnstile.enabled', false);
    }

    public function test_password_reset_request_form_uses_the_public_portal_layout(): void
    {
        $this->get(route('password.request'))
            ->assertOk()
            ->assertDontSee('internal-portal', false)
            ->assertSee('Request a reset link')
            ->assertSee('name="email"', false);
    }

    public function test_authorized_staff_can_request_a_password_reset_link(): void
    {
        Notification::fake();
        $programHead = User::factory()->programHead()->create();

        $this->post(route('password.email'), [
            'email' => strtoupper($programHead->email),
        ])
            ->assertRedirect(route('password.request'))
            ->assertSessionHas(
                'status',
                'If an authorized staff account exists for this email address, a password reset link will be sent.',
            );

        Notification::assertSentTo($programHead, ResetPasswordNotification::class);
    }

    public function test_password_reset_email_identifies_cccc_and_includes_html_and_text_versions(): void
    {
        $programHead = User::factory()->programHead()->create();
        $notification = new ResetPasswordNotification('test-reset-token');
        $message = $notification->toMail($programHead);
        $resetUrl = route('password.reset', [
            'token' => 'test-reset-token',
            'email' => $programHead->email,
        ]);

        $this->assertSame(
            [config('mail.from.address'), 'Catbalogan City Community College (CCCC)'],
            $message->from,
        );
        $this->assertSame('Reset your CCCC Student Checklist password', $message->subject);
        $this->assertSame([
            'html' => 'mail.password-reset',
            'text' => 'mail.password-reset-text',
        ], $message->view);

        $html = (string) $message->render();
        $plainText = view($message->view['text'], $message->viewData)->render();

        $this->assertStringContainsString('Catbalogan City Community College (CCCC)', $html);
        $this->assertStringContainsString($resetUrl, $html);
        $this->assertStringContainsString('Catbalogan City Community College (CCCC)', $plainText);
        $this->assertStringContainsString($resetUrl, $plainText);
    }

    public function test_password_reset_request_does_not_disclose_unknown_or_student_accounts(): void
    {
        Notification::fake();
        $student = User::factory()->create();

        $this->post(route('password.email'), [
            'email' => $student->email,
        ])
            ->assertRedirect(route('password.request'))
            ->assertSessionHas(
                'status',
                'If an authorized staff account exists for this email address, a password reset link will be sent.',
            );

        $this->post(route('password.email'), [
            'email' => 'unknown@cccc.edu.ph',
        ])
            ->assertRedirect(route('password.request'))
            ->assertSessionHas(
                'status',
                'If an authorized staff account exists for this email address, a password reset link will be sent.',
            );

        Notification::assertNothingSent();
    }

    public function test_password_reset_updates_the_password_and_allows_sign_in(): void
    {
        Notification::fake();
        $programHead = User::factory()->programHead()->create();
        $programHead->setRememberToken('existing-remember-token');
        $programHead->save();

        $this->post(route('password.email'), [
            'email' => $programHead->email,
        ]);

        $token = '';
        Notification::assertSentTo(
            $programHead,
            ResetPasswordNotification::class,
            function (ResetPassword $notification, array $channels) use (&$token): bool {
                $token = $notification->token;

                return true;
            },
        );
        $this->assertNotSame('', $token);

        $newPassword = 'CCCC Secure Passphrase 2026!';

        $this->get(route('password.reset', [
            'token' => $token,
            'email' => $programHead->email,
        ]))
            ->assertOk()
            ->assertSee('Set a new password')
            ->assertSee('name="password_confirmation"', false)
            ->assertDontSee('internal-portal', false);

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $programHead->email,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ])
            ->assertRedirect(route('login'))
            ->assertSessionHas('status', 'Your password has been reset. You can now sign in.');

        $programHead->refresh();
        $this->assertNotSame('existing-remember-token', $programHead->remember_token);

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Your password has been reset. You can now sign in.');

        $this->post(route('login.store'), [
            'email' => $programHead->email,
            'password' => $newPassword,
        ])->assertRedirect(route('students.index'));

        $this->assertAuthenticatedAs($programHead);
    }

    public function test_password_reset_rejects_an_invalid_token_without_changing_the_password(): void
    {
        $programHead = User::factory()->programHead()->create();
        $originalPassword = $programHead->password;

        $this->get(route('password.reset', [
            'token' => 'invalid-token',
            'email' => $programHead->email,
        ]));

        $this->post(route('password.update'), [
            'token' => 'invalid-token',
            'email' => $programHead->email,
            'password' => 'CCCC Secure Passphrase 2026!',
            'password_confirmation' => 'CCCC Secure Passphrase 2026!',
        ])
            ->assertInvalid([
                'email' => 'This password reset link is invalid or expired. Request a new link.',
            ]);

        $this->assertSame($originalPassword, $programHead->fresh()->password);
    }

    public function test_password_reset_requires_a_twelve_character_password(): void
    {
        $this->post(route('password.update'), [
            'token' => 'unused-token',
            'email' => 'head@cccc.edu.ph',
            'password' => 'short',
            'password_confirmation' => 'short',
        ])->assertInvalid([
            'password' => 'at least 12 characters',
        ]);
    }

    public function test_password_reset_requires_matching_password_confirmation(): void
    {
        $this->post(route('password.update'), [
            'token' => 'unused-token',
            'email' => 'head@cccc.edu.ph',
            'password' => 'CCCC Secure Passphrase 2026!',
            'password_confirmation' => 'A different passphrase 2026!',
        ])->assertInvalid([
            'password' => 'confirmation does not match',
        ]);
    }

    public function test_password_reset_rejects_password_control_characters(): void
    {
        $password = "CCCC Secure\x00Passphrase 2026!";

        $this->post(route('password.update'), [
            'token' => 'unused-token',
            'email' => 'head@cccc.edu.ph',
            'password' => $password,
            'password_confirmation' => $password,
        ])->assertInvalid([
            'password' => 'unsupported control characters',
        ]);
    }
}
