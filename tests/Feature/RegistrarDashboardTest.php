<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class RegistrarDashboardTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_registrar_dashboard_lists_students_with_course_and_semester_grade_progress(): void
    {
        $registrar = User::factory()->registrar()->create();
        $programHead = User::factory()->programHead()->create();
        $courseCodes = $this->curriculumCourseCodes();
        $lastCourseCode = $courseCodes[array_key_last($courseCodes)];

        $completeStudent = $programHead->students()->create([
            'student_id' => 'CCCC-GRADE-COMPLETE',
            'name' => 'Fully Graded Student',
            'entry_year' => 2022,
        ]);
        $missingGradeStudent = $programHead->students()->create([
            'student_id' => 'CCCC-GRADE-MISSING',
            'name' => 'Missing Grade Student',
            'entry_year' => 2022,
        ]);
        $nullGradeStudent = $programHead->students()->create([
            'student_id' => 'CCCC-GRADE-NULL',
            'name' => 'Null Grade Student',
            'entry_year' => 2022,
        ]);
        $noGradeStudent = $programHead->students()->create([
            'student_id' => 'CCCC-GRADE-NONE',
            'name' => 'No Grade Student',
            'entry_year' => 2022,
        ]);

        $completeStudent->completedCourses()->createMany($this->gradeRows($courseCodes));
        $missingGradeStudent->completedCourses()->createMany($this->gradeRows(array_slice($courseCodes, 0, -1)));
        $nullGradeStudent->completedCourses()->createMany(array_map(
            fn (string $courseCode): array => [
                'course_code' => $courseCode,
                'grade' => $courseCode === $lastCourseCode ? null : 2.00,
            ],
            $courseCodes,
        ));

        $this->actingAs($registrar)
            ->get(route('registrar.dashboard'))
            ->assertOk()
            ->assertSee('Student grade progress by semester')
            ->assertSee('data-grade-progress-student="CCCC-GRADE-COMPLETE"', false)
            ->assertSee('data-grade-progress-student="CCCC-GRADE-MISSING"', false)
            ->assertSee('data-grade-progress-student="CCCC-GRADE-NULL"', false)
            ->assertDontSee('data-grade-progress-student="CCCC-GRADE-NONE"', false)
            ->assertSee('3 students')
            ->assertSee('8 / 8 semesters')
            ->assertSee('7 / 8 semesters')
            ->assertSee('49 / 49 courses')
            ->assertSee('48 / 49 courses')
            ->assertDontSee('No grades entered');
    }

    #[DataProvider('gradeSearchTerms')]
    public function test_registrar_can_search_complete_grade_students_by_name_or_id(string $search): void
    {
        $registrar = User::factory()->registrar()->create();
        $programHead = User::factory()->programHead()->create();
        $student = $programHead->students()->create([
            'student_id' => 'CCCC-GRADE-SEARCH',
            'name' => 'Searchable Complete Student',
            'entry_year' => 2022,
        ]);
        $otherStudent = $programHead->students()->create([
            'student_id' => 'CCCC-GRADE-OTHER',
            'name' => 'Different Complete Student',
            'entry_year' => 2022,
        ]);
        $student->completedCourses()->createMany($this->gradeRows($this->curriculumCourseCodes()));
        $otherStudent->completedCourses()->createMany($this->gradeRows($this->curriculumCourseCodes()));

        $this->actingAs($registrar)
            ->get(route('registrar.dashboard', ['search' => $search]))
            ->assertOk()
            ->assertSee('value="'.$search.'"', false)
            ->assertSee('Showing 1 of 2 students.')
            ->assertSee('data-grade-progress-student="CCCC-GRADE-SEARCH"', false)
            ->assertDontSee('data-grade-progress-student="CCCC-GRADE-OTHER"', false);
    }

    public static function gradeSearchTerms(): array
    {
        return [
            'student name' => ['searchable complete'],
            'student ID' => ['CCCC-GRADE-SEARCH'],
        ];
    }

    public function test_registrar_search_shows_an_empty_state_when_no_complete_student_matches(): void
    {
        $registrar = User::factory()->registrar()->create();
        $search = 'No matching student';

        $this->actingAs($registrar)
            ->get(route('registrar.dashboard', ['search' => $search]))
            ->assertOk()
            ->assertSee('Showing 0 of 0 students.')
            ->assertSee('No students match', false)
            ->assertSee($search);
    }

    public function test_registrar_dashboard_shows_an_empty_state_when_no_students_exist(): void
    {
        $registrar = User::factory()->registrar()->create();

        $this->actingAs($registrar)
            ->get(route('registrar.dashboard'))
            ->assertOk()
            ->assertSee('No student records yet.');
    }

    public function test_registrar_dashboard_hides_students_without_grades(): void
    {
        $registrar = User::factory()->registrar()->create();
        $programHead = User::factory()->programHead()->create();
        $student = $programHead->students()->create([
            'student_id' => 'CCCC-GRADE-NONE',
            'name' => 'No Grade Student',
            'entry_year' => 2022,
        ]);

        $this->actingAs($registrar)
            ->get(route('registrar.dashboard'))
            ->assertOk()
            ->assertSee('Showing 0 students.')
            ->assertSee('No students with grades yet.')
            ->assertDontSee('data-grade-progress-student="'.$student->student_id.'"', false);
    }

    public function test_registrar_search_rejects_queries_longer_than_one_hundred_characters(): void
    {
        $registrar = User::factory()->registrar()->create();

        $this->actingAs($registrar)
            ->get(route('registrar.dashboard', ['search' => str_repeat('a', 101)]))
            ->assertInvalid([
                'search' => 'The search field must not be greater than 100 characters.',
            ]);
    }

    public function test_registrar_search_escapes_user_supplied_html(): void
    {
        $registrar = User::factory()->registrar()->create();
        $search = '<script>alert(1)</script>';

        $this->actingAs($registrar)
            ->get(route('registrar.dashboard', ['search' => $search]))
            ->assertOk()
            ->assertSee('value="&lt;script&gt;alert(1)&lt;/script&gt;"', false)
            ->assertDontSee($search, false);
    }

    public function test_non_registrar_cannot_open_registrar_grade_completion_log(): void
    {
        $programHead = User::factory()->programHead()->create();

        $this->actingAs($programHead)
            ->get(route('registrar.dashboard'))
            ->assertForbidden();
    }

    /**
     * @return array<int, string>
     */
    private function curriculumCourseCodes(): array
    {
        return collect(config('bpa_curriculum.years'))
            ->flatMap(fn (array $year): array => $year['semesters'])
            ->flatMap(fn (array $semester): array => $semester['courses'])
            ->pluck('code')
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @param  array<int, string>  $courseCodes
     * @return array<int, array{course_code: string, grade: float}>
     */
    private function gradeRows(array $courseCodes): array
    {
        return array_map(
            fn (string $courseCode): array => [
                'course_code' => $courseCode,
                'grade' => 2.00,
            ],
            $courseCodes,
        );
    }
}
