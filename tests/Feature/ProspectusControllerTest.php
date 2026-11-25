<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProspectusControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_registrar_can_preview_prospectus_from_student_checklist(): void
    {
        $programHead = User::factory()->programHead()->create();
        $registrar = User::factory()->registrar()->create();
        $student = $programHead->students()->create([
            'student_id' => 'CCCC-PROSPECTUS-001',
            'name' => 'Prospectus Student',
            'entry_year' => 2024,
            'expected_graduation_year' => 2028,
            'previous_school' => 'Samar State University',
            'last_year_attended' => '2023-2024',
        ]);
        $student->completedCourses()->create([
            'course_code' => 'GED 1003',
            'grade' => 2.25,
        ]);
        $student->completedCourses()->create([
            'course_code' => 'GED 1006',
            'grade' => 0.00,
        ]);
        $student->completedCourses()->create([
            'course_code' => 'PATHFit 2103',
            'grade' => 3.25,
        ]);

        $this->actingAs($registrar)
            ->get(route('students.show', $student))
            ->assertOk()
            ->assertSee('Preview Prospectus')
            ->assertSee('id="prospectus-modal"', false)
            ->assertDontSee('prospectus-modal-download', false)
            ->assertDontSee('print-prospectus-modal', false)
            ->assertDontSee('id="prospectus-modal-title"', false)
            ->assertSee('id="close-prospectus-modal"', false)
            ->assertSee(route('students.prospectus.stream', $student), false);

        $this->get(route('prospectus.show'))
            ->assertOk()
            ->assertSee('GRADING SYSTEM')
            ->assertSee('FIRST YEAR')
            ->assertSee('class="evaluation-row"', false);

        $preview = $this->get(route('students.prospectus.show', [$student, 'modal' => 1]))
            ->assertOk()
            ->assertSee('class="embedded-preview"', false)
            ->assertDontSee('Modal Preview')
            ->assertSee('GRADING SYSTEM')
            ->assertSee('class="evaluation-row"', false)
            ->assertSee('Prospectus Student')
            ->assertSee('CCCC-PROSPECTUS-001')
            ->assertSee('2024 - 2028')
            ->assertSee('Samar State University')
            ->assertSee('Last year attended: 2023-2024')
            ->assertSee('<span class="chk chk-grade">2.25</span>', false)
            ->assertSee('<span class="chk chk-grade chk-failing">3.25</span>PATHFit 2103', false)
            ->assertSee('<span class="chk"></span>GED 1006', false);

        $this->assertSame(3, substr_count($preview->getContent(), 'class="student-field-value'));
        $preview->assertSee('class="student-field-value student-name"', false);
        $preview->assertSee('class="line" style="width:100%"', false);
    }

    public function test_only_registrars_can_access_the_prospectus(): void
    {
        $administrator = User::factory()->administrator()->create();
        $registrar = User::factory()->registrar()->create();
        $programHead = User::factory()->programHead()->create();
        $student = $programHead->students()->create([
            'student_id' => 'CCCC-PROSPECTUS-STAFF',
            'name' => 'Staff Prospectus Student',
            'entry_year' => 2024,
        ]);

        $this->actingAs($administrator)
            ->get(route('students.show', $student))
            ->assertOk()
            ->assertDontSee('Preview Prospectus');

        $this->actingAs($administrator)
            ->get(route('students.prospectus.show', [$student, 'modal' => 1]))
            ->assertForbidden();

        $this->actingAs($programHead)
            ->get(route('students.show', $student))
            ->assertOk()
            ->assertDontSee('Preview Prospectus');

        $this->actingAs($programHead)
            ->get(route('students.prospectus.show', [$student, 'modal' => 1]))
            ->assertForbidden();

        $this->actingAs($registrar)
            ->get(route('students.prospectus.show', [$student, 'modal' => 1]))
            ->assertOk()
            ->assertSee('GRADING SYSTEM')
            ->assertSee('Staff Prospectus Student');
    }

    public function test_registrar_can_download_prospectus_as_a_pdf(): void
    {
        $programHead = User::factory()->programHead()->create();
        $registrar = User::factory()->registrar()->create();
        $student = $programHead->students()->create([
            'student_id' => 'CCCC-PROSPECTUS-PDF',
            'name' => 'PDF Prospectus Student',
            'entry_year' => 2024,
        ]);

        $this->actingAs($registrar)
            ->get(route('students.prospectus.download', $student))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->actingAs($registrar)
            ->get(route('students.prospectus.stream', $student))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_guests_and_student_accounts_cannot_preview_prospectus(): void
    {
        $this->get(route('prospectus.show'))
            ->assertRedirect(route('login'));

        $student = User::factory()->create();
        $programHead = User::factory()->programHead()->create();
        $record = $programHead->students()->create([
            'student_id' => 'CCCC-PROSPECTUS-STUDENT',
            'name' => 'Protected Prospectus Student',
            'entry_year' => 2024,
        ]);

        $this->actingAs($student)
            ->get(route('students.prospectus.show', [$record, 'modal' => 1]))
            ->assertForbidden();
    }

    public function test_program_head_cannot_preview_another_heads_prospectus(): void
    {
        $programHead = User::factory()->programHead()->create();
        $anotherProgramHead = User::factory()->programHead()->create();
        $student = $anotherProgramHead->students()->create([
            'student_id' => 'CCCC-PROSPECTUS-PRIVATE',
            'name' => 'Private Prospectus Student',
            'entry_year' => 2024,
        ]);

        $this->actingAs($programHead)
            ->get(route('students.prospectus.show', [$student, 'modal' => 1]))
            ->assertForbidden();
    }
}
