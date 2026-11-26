<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentCourseCompletion;
use App\Models\User;
use App\Services\ProgramCurriculum;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $query = $request->user()->canViewAllPrograms()
            ? Student::query()
            : $request->user()->students();

        $students = $query
            ->with(['programHead'])
            ->withCount('completedCourses')
            ->orderBy('name')
            ->get();

        $schoolYearOptions = $query
            ->clone()
            ->select('entry_year', 'expected_graduation_year')
            ->get()
            ->flatMap(function (Student $student): array {
                $entryYear = (int) $student->entry_year;
                $graduationYear = $student->expected_graduation_year
                    ? (int) $student->expected_graduation_year
                    : $entryYear + 4;

                if ($graduationYear < $entryYear) {
                    $graduationYear = $entryYear;
                }

                $years = [];
                for ($year = $entryYear; $year < $graduationYear; $year++) {
                    $years[] = "{$year}-".($year + 1);
                }

                return $years;
            })
            ->unique()
            ->sortDesc()
            ->values();

        return view('students.index', [
            'students' => $students,
            'schoolYearOptions' => $schoolYearOptions,
            'totalCourses' => config('bpa_curriculum.total_courses'),
            'programHeads' => $request->user()->canViewAllPrograms()
                ? User::query()->where('role', User::ROLE_PROGRAM_HEAD)->orderBy('name')->get()
                : collect(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $owner = $request->user();
        if ($owner->canViewAllPrograms()) {
            $validatedOwner = $request->validate([
                'program_head_id' => ['required', 'integer', 'exists:users,id'],
            ]);
            $owner = User::query()
                ->where('role', User::ROLE_PROGRAM_HEAD)
                ->whereKey($validatedOwner['program_head_id'])
                ->firstOrFail();
        }

        $student = $owner->students()->create(
            $this->validatedStudent($request, null, $owner),
        );

        return redirect()
            ->route('students.show', $student)
            ->with('status', 'Student record added.');
    }

    public function show(Student $student, ProgramCurriculum $programCurriculum): View
    {
        $this->authorize('view', $student);
        $student->load(['programHead', 'completedCourses']);
        $studentQuery = auth()->user()->canViewAllPrograms()
            ? Student::query()
            : auth()->user()->students();
        $studentList = $studentQuery
            ->with(['programHead'])
            ->withCount('completedCourses')
            ->orderBy('name')
            ->get();
        $curriculum = $programCurriculum->forProgram($student->programHead?->program);
        $studentCourseTotals = $studentList->mapWithKeys(
            fn (Student $switchStudent): array => [
                $switchStudent->id => $programCurriculum
                    ->forProgram($switchStudent->programHead?->program)['total_courses'],
            ],
        );

        return view('student-checklist', [
            'student' => $student,
            'studentList' => $studentList,
            'programCode' => $curriculum['program'],
            'programName' => $curriculum['name'],
            'curriculum' => $curriculum['years'],
            'studentCourseTotals' => $studentCourseTotals,
            'completedCourseGrades' => $student->completedCourses->mapWithKeys(
                fn (StudentCourseCompletion $completion): array => [
                    $completion->course_code => $completion->grade === null
                        ? null
                        : (float) $completion->grade,
                ],
            )->all(),
            'totalCourses' => $curriculum['total_courses'],
            'totalUnits' => $curriculum['total_units'],
        ]);
    }

    public function update(Request $request, Student $student): RedirectResponse
    {
        $this->authorize('update', $student);

        $student->update($this->validatedStudent($request, $student));

        return redirect()
            ->route('students.show', $student)
            ->with('status', 'Student record updated.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        $this->authorize('delete', $student);
        $student->delete();

        return redirect()
            ->route('students.index')
            ->with('status', 'Student record and checklist deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedStudent(Request $request, ?Student $student = null, ?User $owner = null): array
    {
        $ownerId = $student?->user_id ?? $owner?->id ?? $request->user()->id;
        $studentIdRule = Rule::unique('students', 'student_id')
            ->where(fn (Builder $query) => $query->where('user_id', $ownerId));

        if ($student) {
            $studentIdRule->ignore($student);
        }

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'student_id' => ['required', 'string', 'max:50', $studentIdRule],
            'entry_year' => ['required', 'integer', 'between:1950,2100'],
            'expected_graduation_year' => ['nullable', 'integer', 'between:1950,2105', 'gte:entry_year'],
            'previous_school' => ['nullable', 'string', 'max:255'],
            'last_year_attended' => ['nullable', 'string', 'max:50'],
        ]);
    }
}
