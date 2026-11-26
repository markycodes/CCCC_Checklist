<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentCourseCompletionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_registrar_can_enter_grades_across_programs(): void
    {
        $registrar = User::factory()->registrar()->create();
        $programHead = User::factory()->programHead()->create();
        $student = $programHead->students()->create([
            'student_id' => 'CCCC-REG-GRADE',
            'name' => 'No Grade Student',
            'entry_year' => 2024,
        ]);

        $this->actingAs($registrar)
            ->putJson(route('students.courses.complete', [$student, 'GED 1003']), [
                'grade' => 2.00,
            ])
            ->assertOk()
            ->assertExactJson([
                'completed' => true,
                'grade' => '2.00',
            ]);

        $this->assertDatabaseHas('student_course_completions', [
            'student_id' => $student->id,
            'course_code' => 'GED 1003',
            'grade' => 2.00,
        ]);
    }

    public function test_program_head_can_save_course_completion_for_a_student(): void
    {
        $programHead = User::factory()->programHead()->create();
        $student = $programHead->students()->create([
            'student_id' => 'CCCC-CHECK-001',
            'name' => 'Checklist Student',
            'entry_year' => 2024,
        ]);

        $this->actingAs($programHead)
            ->putJson(route('students.courses.complete', [$student, 'GED 1003']), [
                'grade' => 2.25,
            ])
            ->assertOk()
            ->assertExactJson([
                'completed' => true,
                'grade' => '2.25',
            ]);

        $this->assertDatabaseHas('student_course_completions', [
            'student_id' => $student->id,
            'course_code' => 'GED 1003',
            'grade' => 2.25,
        ]);
    }

    public function test_program_head_can_save_a_course_from_their_assigned_program_prospectus(): void
    {
        $programHead = User::factory()->programHead()->create(['program' => 'BSCRIM']);
        $student = $programHead->students()->create([
            'student_id' => 'CCCC-BSCRIM-GRADE',
            'name' => 'Criminology Grade Student',
            'entry_year' => 2024,
        ]);

        $this->actingAs($programHead)
            ->putJson(route('students.courses.complete', [$student, 'CRM 1101']), [
                'grade' => 2.00,
            ])
            ->assertOk()
            ->assertExactJson([
                'completed' => true,
                'grade' => '2.00',
            ]);

        $this->assertDatabaseHas('student_course_completions', [
            'student_id' => $student->id,
            'course_code' => 'CRM 1101',
            'grade' => 2.00,
        ]);
    }

    public function test_program_head_cannot_save_progress_for_another_heads_student(): void
    {
        $programHead = User::factory()->programHead()->create();
        $anotherProgramHead = User::factory()->programHead()->create();
        $student = $anotherProgramHead->students()->create([
            'student_id' => 'CCCC-PRIVATE-001',
            'name' => 'Private Student',
            'entry_year' => 2024,
        ]);

        $this->actingAs($programHead)
            ->put(route('students.courses.complete', [$student, 'GED 1003']))
            ->assertNotFound();

        $this->assertDatabaseCount('student_course_completions', 0);
    }

    public function test_course_outside_the_approved_curriculum_is_rejected(): void
    {
        $programHead = User::factory()->programHead()->create(['program' => 'BSCRIM']);
        $student = $programHead->students()->create([
            'student_id' => 'CCCC-INVALID-001',
            'name' => 'Checklist Student',
            'entry_year' => 2024,
        ]);

        $this->actingAs($programHead)
            ->put(route('students.courses.complete', [$student, 'PAC 1101']))
            ->assertNotFound();

        $this->assertDatabaseCount('student_course_completions', 0);
    }

    public function test_failing_grade_is_saved_but_does_not_complete_the_course(): void
    {
        $programHead = User::factory()->programHead()->create();
        $student = $programHead->students()->create([
            'student_id' => 'CCCC-GRADE-FAIL',
            'name' => 'Grade Student',
            'entry_year' => 2024,
        ]);

        $this->actingAs($programHead)
            ->putJson(route('students.courses.complete', [$student, 'GED 1003']), [
                'grade' => 3.50,
            ])
            ->assertOk()
            ->assertExactJson([
                'completed' => false,
                'grade' => '3.50',
            ]);

        $this->assertDatabaseHas('student_course_completions', [
            'student_id' => $student->id,
            'course_code' => 'GED 1003',
            'grade' => 3.50,
        ]);
    }

    public function test_grade_boundary_marks_only_three_as_failing(): void
    {
        $programHead = User::factory()->programHead()->create();
        $student = $programHead->students()->create([
            'student_id' => 'CCCC-GRADE-BOUNDARY',
            'name' => 'Boundary Grade Student',
            'entry_year' => 2024,
        ]);

        $this->actingAs($programHead)
            ->putJson(route('students.courses.complete', [$student, 'GED 1003']), [
                'grade' => 2.99,
            ])
            ->assertOk()
            ->assertExactJson([
                'completed' => true,
                'grade' => '2.99',
            ]);

        $this->actingAs($programHead)
            ->putJson(route('students.courses.complete', [$student, 'GED 1003']), [
                'grade' => 3.00,
            ])
            ->assertOk()
            ->assertExactJson([
                'completed' => false,
                'grade' => '3.00',
            ]);
    }

    public function test_grade_must_be_within_the_philippine_scale(): void
    {
        $programHead = User::factory()->programHead()->create();
        $student = $programHead->students()->create([
            'student_id' => 'CCCC-GRADE-SCALE',
            'name' => 'Grade Student',
            'entry_year' => 2024,
        ]);

        $this->actingAs($programHead)
            ->putJson(route('students.courses.complete', [$student, 'GED 1003']), [
                'grade' => 5.50,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('grade');

        $this->assertDatabaseCount('student_course_completions', 0);
    }

    public function test_program_head_can_remove_saved_course_completion(): void
    {
        $programHead = User::factory()->programHead()->create(['program' => 'BSCRIM']);
        $student = $programHead->students()->create([
            'student_id' => 'CCCC-UNDO-001',
            'name' => 'Checklist Student',
            'entry_year' => 2024,
        ]);
        $student->completedCourses()->create(['course_code' => 'CRM 1101']);

        $this->actingAs($programHead)
            ->delete(route('students.courses.incomplete', [$student, 'CRM 1101']))
            ->assertOk()
            ->assertExactJson(['completed' => false]);

        $this->assertDatabaseCount('student_course_completions', 0);
    }
}
