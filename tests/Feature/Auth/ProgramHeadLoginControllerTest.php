<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ProgramHeadLoginControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config()->set('cloudflare-turnstile.enabled', false);
    }

    public function test_landing_page_shows_the_curriculum_overview(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('portal-topbar-landing', false)
            ->assertSee('Every course.')
            ->assertSee('BACHELOR OF PUBLIC ADMINISTRATION');
    }

    public function test_program_head_login_page_is_available(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Program Head sign in')
            ->assertSee('College email');
    }

    public function test_login_rejects_invalid_email_text(): void
    {
        config()->set('cloudflare-turnstile.enabled', false);

        $this->post(route('login.store'), [
            'email' => "head@example.test' OR 1=1 --",
            'password' => 'password',
        ])
            ->assertInvalid(['email']);
    }

    public function test_login_rejects_password_control_characters(): void
    {
        config()->set('cloudflare-turnstile.enabled', false);

        $this->post(route('login.store'), [
            'email' => 'head@cccc.edu.ph',
            'password' => "password\x00",
        ])
            ->assertInvalid(['password']);
    }

    public function test_cloudflare_turnstile_is_required_when_enabled(): void
    {
        config()->set('cloudflare-turnstile.enabled', true);
        config()->set('cloudflare-turnstile.secret_key', 'test-secret');

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('cf-turnstile', false);
    }

    public function test_cloudflare_turnstile_rejects_a_failed_challenge(): void
    {
        config()->set('cloudflare-turnstile.enabled', true);
        config()->set('cloudflare-turnstile.secret_key', 'test-secret');
        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response([
                'success' => false,
            ], 200),
        ]);

        $this->post(route('login.store'), [
            'email' => 'head@cccc.edu.ph',
            'password' => 'password',
            'cf-turnstile-response' => 'invalid-token',
        ])
            ->assertInvalid([
                'cf-turnstile-response' => 'Cloudflare verification failed. Please try again.',
            ]);
    }

    public function test_valid_cloudflare_turnstile_allows_login(): void
    {
        config()->set('cloudflare-turnstile.enabled', true);
        config()->set('cloudflare-turnstile.secret_key', 'test-secret');
        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response([
                'success' => true,
            ], 200),
        ]);
        $programHead = User::factory()->programHead()->create();

        $this->post(route('login.store'), [
            'email' => $programHead->email,
            'password' => 'password',
            'cf-turnstile-response' => 'valid-token',
        ])
            ->assertRedirect(route('students.index'));

        $this->assertAuthenticatedAs($programHead);
    }

    public function test_program_head_can_sign_in_and_reaches_student_records(): void
    {
        $programHead = User::factory()->programHead()->create();

        $this->post(route('login.store'), [
            'email' => $programHead->email,
            'password' => 'password',
        ])
            ->assertRedirect(route('students.index'));

        $this->assertAuthenticatedAs($programHead);
    }

    public function test_student_account_cannot_sign_in_as_a_program_head(): void
    {
        $student = User::factory()->create();

        $this->post(route('login.store'), [
            'email' => $student->email,
            'password' => 'password',
        ])
            ->assertInvalid(['email' => 'Those details do not match an authorized staff account.']);

        $this->assertGuest();
    }

    public function test_guest_is_redirected_from_student_records(): void
    {
        $this->get(route('students.index'))
            ->assertRedirect(route('login'));
    }

    public function test_student_account_cannot_view_program_head_records(): void
    {
        $student = User::factory()->create();

        $this->actingAs($student)
            ->get(route('students.index'))
            ->assertForbidden();
    }

    public function test_program_head_can_sign_out(): void
    {
        $programHead = User::factory()->programHead()->create();

        $this->actingAs($programHead)
            ->post(route('logout'))
            ->assertRedirect(route('home'));

        $this->assertGuest();
    }
}
