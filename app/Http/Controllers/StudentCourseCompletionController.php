<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Services\ProgramCurriculum;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentCourseCompletionController extends Controller
{
    public function __construct(private readonly ProgramCurriculum $programCurriculum) {}

    public function store(Request $request, Student $student, string $course): JsonResponse
    {
        $this->authorize('view', $student);
        abort_unless(auth()->user()?->canEnterGrades(), 403);
        abort_unless($this->programCurriculum->containsCourse($student->programHead?->program, $course), 404);

        $validated = $request->validate([
            'grade' => ['required', 'numeric', 'between:0,5'],
        ]);
        $grade = round((float) $validated['grade'], 2);

        if ($grade === 0.0) {
            $student->completedCourses()
                ->where('course_code', $course)
                ->delete();

            return response()->json([
                'completed' => false,
                'grade' => '0.00',
            ]);
        }

        $student->completedCourses()->updateOrCreate(
            ['course_code' => $course],
            ['grade' => $grade],
        );

        return response()->json([
            'completed' => $grade >= 1 && $grade < 3,
            'grade' => number_format($grade, 2, '.', ''),
        ]);
    }

    public function destroy(Student $student, string $course): JsonResponse
    {
        $this->authorize('view', $student);
        abort_unless(auth()->user()?->canEnterGrades(), 403);
        abort_unless($this->programCurriculum->containsCourse($student->programHead?->program, $course), 404);

        $student->completedCourses()
            ->where('course_code', $course)
            ->delete();

        return response()->json(['completed' => false]);
    }
}
