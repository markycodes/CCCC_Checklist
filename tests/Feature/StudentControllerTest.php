<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class StudentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_program_head_can_create_a_student_record(): void
    {
        $programHead = User::factory()->programHead()->create();

        $response = $this->actingAs($programHead)->post(route('students.store'), [
            'name' => 'Alex Santos',
            'student_id' => 'CCCC-2024-001',
            'entry_year' => 2024,
            'expected_graduation_year' => 2028,
            'previous_school' => 'Samar State University',
            'last_year_attended' => '2023–2024',
        ]);

        $student = Student::query()->where('student_id', 'CCCC-2024-001')->firstOrFail();

        $response->assertRedirect(route('students.show', $student));
        $this->assertModelExists($student);
        $this->assertSame($programHead->id, $student->user_id);
    }

    public function test_program_head_can_only_view_their_own_student_records(): void
    {
        $programHead = User::factory()->programHead()->create();
        $anotherProgramHead = User::factory()->programHead()->create();
        $ownedStudent = $programHead->students()->create([
            'student_id' => 'CCCC-OWN-001',
            'name' => 'Owned Student',
            'entry_year' => 2024,
        ]);
        $otherStudent = $anotherProgramHead->students()->create([
            'student_id' => 'CCCC-OTHER-001',
            'name' => 'Other Student',
            'entry_year' => 2024,
        ]);

        $this->actingAs($programHead)
            ->get(route('students.index'))
            ->assertOk()
            ->assertSee('Owned Student')
            ->assertDontSee('Other Student');

        $this->get(route('students.show', $otherStudent))
            ->assertNotFound();

        $this->get(route('students.show', $ownedStudent))
            ->assertOk()
            ->assertSee('Owned Student')
            ->assertSee('STUDENT RECORDS')
            ->assertSee('View all student records');
    }

    public function test_administrator_and_registrar_can_view_students_across_programs(): void
    {
        $bpaHead = User::factory()->programHead()->create(['program' => 'BPA']);
        $bscrimHead = User::factory()->programHead()->create(['program' => 'BSCRIM']);

        $bpaStudent = $bpaHead->students()->create([
            'student_id' => 'CCCC-BPA-001',
            'name' => 'BPA Student',
            'entry_year' => 2024,
        ]);
        $bscrimStudent = $bscrimHead->students()->create([
            'student_id' => 'CCCC-BSCRIM-001',
            'name' => 'BSCRIM Student',
            'entry_year' => 2024,
        ]);

        foreach ([User::ROLE_ADMINISTRATOR, User::ROLE_REGISTRAR] as $role) {
            $staff = User::factory()->create(['role' => $role]);

            $this->actingAs($staff)
                ->get(route('students.index'))
                ->assertOk()
                ->assertSee('BPA Student')
                ->assertSee('BSCRIM Student');

            $this->get(route('students.show', $bpaStudent))
                ->assertOk()
                ->assertSee('BPA Student');

            $this->get(route('students.show', $bscrimStudent))
                ->assertOk()
                ->assertSee('BSCRIM Student');
        }
    }

    #[DataProvider('programCurriculumOrder')]
    public function test_student_checklist_uses_assigned_program_prospectus_order(
        string $program,
        string $firstSemesterCourse,
        string $secondSemesterCourse,
        string $lastCourse,
    ): void {
        $programHead = User::factory()->programHead()->create(['program' => $program]);
        $student = $programHead->students()->create([
            'student_id' => "CCCC-{$program}-CURRICULUM",
            'name' => "{$program} Curriculum Student",
            'entry_year' => 2024,
        ]);

        $this->actingAs($programHead)
            ->get(route('students.show', $student))
            ->assertOk()
            ->assertSee("Enrolled program: {$program}")
            ->assertViewHas('programCode', $program)
            ->assertViewHas('curriculum', function (array $curriculum) use (
                $firstSemesterCourse,
                $secondSemesterCourse,
                $lastCourse,
            ): bool {
                return count($curriculum) === 4
                    && $curriculum[0]['name'] === 'First year'
                    && $curriculum[0]['semesters'][0]['name'] === 'First semester'
                    && $curriculum[0]['semesters'][0]['courses'][0]['code'] === $firstSemesterCourse
                    && $curriculum[0]['semesters'][1]['courses'][0]['code'] === $secondSemesterCourse
                    && $curriculum[3]['semesters'][1]['courses'][
                        array_key_last($curriculum[3]['semesters'][1]['courses'])
                    ]['code'] === $lastCourse;
            });
    }

    public static function programCurriculumOrder(): array
    {
        return [
            'BPA' => ['BPA', 'GED 1003', 'GED 1001', 'PPA 4201'],
            'BSCRIM' => ['BSCRIM', 'GED 1001', 'GED 1003', 'CBA 4203'],
            'BSISM' => ['BSISM', 'GED 1003', 'GED 1001', 'ISP 4202'],
            'BSENTREP' => ['BSENTREP', 'GED 1003', 'GED 1001', 'ENT 4215'],
            'BSOA' => ['BSOA', 'GED 1003', 'GED 1001', 'PEC 4206'],
        ];
    }

    public function test_duplicate_student_id_is_rejected_for_the_same_program_head(): void
    {
        $programHead = User::factory()->programHead()->create();
        $programHead->students()->create([
            'student_id' => 'CCCC-DUP-001',
            'name' => 'Existing Student',
            'entry_year' => 2024,
        ]);

        $this->actingAs($programHead)
            ->from(route('students.index'))
            ->post(route('students.store'), [
                'name' => 'Another Student',
                'student_id' => 'CCCC-DUP-001',
                'entry_year' => 2024,
            ])
            ->assertInvalid('student_id');

        $this->assertDatabaseCount('students', 1);
    }

    public function test_program_head_can_update_student_details(): void
    {
        $programHead = User::factory()->programHead()->create();
        $student = $programHead->students()->create([
            'student_id' => 'CCCC-EDIT-001',
            'name' => 'Old Name',
            'entry_year' => 2024,
        ]);

        $this->actingAs($programHead)
            ->put(route('students.update', $student), [
                'name' => 'Updated Name',
                'student_id' => 'CCCC-EDIT-001',
                'entry_year' => 2024,
                'expected_graduation_year' => 2028,
                'previous_school' => '',
                'last_year_attended' => '',
            ])
            ->assertRedirect(route('students.show', $student));

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'name' => 'Updated Name',
            'expected_graduation_year' => 2028,
        ]);
    }

    public function test_deleting_a_student_also_removes_their_course_progress(): void
    {
        $programHead = User::factory()->programHead()->create();
        $student = $programHead->students()->create([
            'student_id' => 'CCCC-DELETE-001',
            'name' => 'Student To Remove',
            'entry_year' => 2024,
        ]);
        $completion = $student->completedCourses()->create([
            'course_code' => 'GED 1003',
        ]);

        $this->actingAs($programHead)
            ->delete(route('students.destroy', $student))
            ->assertRedirect(route('students.index'));

        $this->assertDatabaseMissing('students', ['id' => $student->id]);
        $this->assertDatabaseMissing('student_course_completions', ['id' => $completion->id]);
    }
}
