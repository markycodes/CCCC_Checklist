<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProgramHeadLoginControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_shows_the_curriculum_overview(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Curriculum and');
    }

    public function test_program_head_login_page_is_available(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Program Head sign in')
            ->assertSee('College email');
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
