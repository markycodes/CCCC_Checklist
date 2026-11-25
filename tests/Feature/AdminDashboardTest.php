<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
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
            ->assertSee('internal-portal', false)
            ->assertSee('Registrar overview')
            ->assertSee('Enrollment trend')
            ->assertSee('enrollment-line-chart-svg', false)
            ->assertSee('No enrollment data yet')
            ->assertSee('progress-radial-svg', false)
            ->assertSee('aria-label="Student progress: 0 completed, 0 in progress, 0 not started"', false)
            ->assertSee('Create Program Head')
            ->assertSee('program-head-modal', false);
    }

    public function test_enrollment_chart_shows_student_counts_for_each_entry_year(): void
    {
        $administrator = User::factory()->administrator()->create();
        $programHead = User::factory()->programHead()->create();

        $programHead->students()->createMany([
            [
                'student_id' => 'CCCC-CHART-001',
                'name' => 'First Year Student',
                'entry_year' => 2022,
            ],
            [
                'student_id' => 'CCCC-CHART-002',
                'name' => 'Second Year Student',
                'entry_year' => 2024,
            ],
            [
                'student_id' => 'CCCC-CHART-003',
                'name' => 'Third Year Student',
                'entry_year' => 2024,
            ],
        ]);

        $this->actingAs($administrator)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('2022: 1 student.')
            ->assertSee('2023: 0 students.')
            ->assertSee('2024: 2 students.')
            ->assertSee('aria-label="Student progress: 0 completed, 0 in progress, 3 not started"', false);
    }

    public function test_single_entry_year_still_renders_an_enrollment_line(): void
    {
        $administrator = User::factory()->administrator()->create();
        $programHead = User::factory()->programHead()->create();

        $programHead->students()->create([
            'student_id' => 'CCCC-CHART-SINGLE',
            'name' => 'Single Entry Year Student',
            'entry_year' => 2024,
        ]);

        $this->actingAs($administrator)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('class="enrollment-trend-line" d="M 90 260 L 535 212 L 980 260"', false)
            ->assertSee('2023: 0 students.')
            ->assertSee('2024: 1 student.')
            ->assertSee('2025: 0 students.');
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
                'program' => 'BPA',
            ])
            ->assertRedirect(route('admin.dashboard'))
            ->assertSessionHas('status', 'Program Head account created.');

        $this->assertDatabaseHas('users', [
            'email' => 'head@cccc.edu.ph',
            'role' => User::ROLE_PROGRAM_HEAD,
            'program' => 'BPA',
        ]);
    }

    #[DataProvider('programProvider')]
    public function test_administrator_can_provision_each_supported_program(string $program): void
    {
        $administrator = User::factory()->administrator()->create();
        $email = strtolower($program).'@cccc.edu.ph';

        $this->actingAs($administrator)
            ->post(route('admin.program-heads.store'), [
                'name' => "{$program} Program Head",
                'email' => $email,
                'password' => 'StrongPassword123!',
                'password_confirmation' => 'StrongPassword123!',
                'program' => $program,
            ])
            ->assertRedirect(route('admin.dashboard'));

        $this->assertDatabaseHas('users', [
            'email' => $email,
            'role' => User::ROLE_PROGRAM_HEAD,
            'program' => $program,
        ]);
    }

    public static function programProvider(): array
    {
        return [
            'BPA' => ['BPA'],
            'BSCRIM' => ['BSCRIM'],
            'BSISM' => ['BSISM'],
            'BSENTREP' => ['BSENTREP'],
            'BSOA' => ['BSOA'],
        ];
    }

    public function test_administrator_cannot_provision_an_unknown_program(): void
    {
        $administrator = User::factory()->administrator()->create();

        $this->actingAs($administrator)
            ->post(route('admin.program-heads.store'), [
                'name' => 'Unknown Program Head',
                'email' => 'unknown@cccc.edu.ph',
                'password' => 'StrongPassword123!',
                'password_confirmation' => 'StrongPassword123!',
                'program' => 'UNKNOWN',
            ])
            ->assertInvalid(['program']);

        $this->assertDatabaseMissing('users', ['email' => 'unknown@cccc.edu.ph']);
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
            ->assertSee('Enrollment trend')
            ->assertSee('enrollment-line-chart-svg', false);

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
