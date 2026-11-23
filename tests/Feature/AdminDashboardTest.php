<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrator_can_open_school_dashboard(): void
    {
        $administrator = User::factory()->administrator()->create();

        $this->actingAs($administrator)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Registrar overview')
            ->assertSee('Enrollment trend')
            ->assertSee('Create Program Head')
            ->assertSee('program-head-modal', false);
    }

    public function test_program_head_cannot_open_admin_dashboard(): void
    {
        $programHead = User::factory()->programHead()->create();

        $this->actingAs($programHead)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_administrator_can_provision_a_program_head(): void
    {
        $administrator = User::factory()->administrator()->create();

        $this->actingAs($administrator)
            ->post(route('admin.program-heads.store'), [
                'name' => 'New Program Head',
                'email' => 'head@cccc.edu.ph',
                'password' => 'StrongPassword123!',
                'password_confirmation' => 'StrongPassword123!',
            ])
            ->assertRedirect(route('admin.dashboard'))
            ->assertSessionHas('status', 'Program Head account created.');

        $this->assertDatabaseHas('users', [
            'email' => 'head@cccc.edu.ph',
            'role' => User::ROLE_PROGRAM_HEAD,
        ]);
    }

    public function test_administrator_can_view_all_students(): void
    {
        $administrator = User::factory()->administrator()->create();
        $head = User::factory()->programHead()->create();
        $student = $head->students()->create([
            'student_id' => 'CCCC-ADMIN-001',
            'name' => 'Visible To Administrator',
            'entry_year' => 2024,
        ]);

        $this->actingAs($administrator)
            ->get(route('students.show', $student))
            ->assertOk()
            ->assertSee('Visible To Administrator');
    }

    public function test_registrar_can_manage_students_but_cannot_create_program_heads(): void
    {
        $registrar = User::factory()->registrar()->create();
        $programHead = User::factory()->programHead()->create();
        $student = $programHead->students()->create([
            'student_id' => 'CCCC-REG-001',
            'name' => 'Registrar Managed Student',
            'entry_year' => 2024,
        ]);

        $this->actingAs($registrar)
            ->get(route('students.index'))
            ->assertOk()
            ->assertSee('Registrar Managed Student');

        $this->get(route('registrar.dashboard'))
            ->assertOk()
            ->assertSee('Student overview')
            ->assertSee('Enrollment trend');

        $this->get(route('students.show', $student))
            ->assertOk()
            ->assertSee('Registrar Managed Student');

        $this->get(route('admin.dashboard'))
            ->assertForbidden();

        $this->post(route('admin.program-heads.store'), [
            'name' => 'Blocked Head',
            'email' => 'blocked@cccc.edu.ph',
            'password' => 'StrongPassword123!',
            'password_confirmation' => 'StrongPassword123!',
        ])
            ->assertForbidden();

        $this->assertDatabaseMissing('users', ['email' => 'blocked@cccc.edu.ph']);
    }
}
