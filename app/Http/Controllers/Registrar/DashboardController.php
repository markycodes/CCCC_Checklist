<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentCourseCompletion;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $totalCourses = (int) config('bpa_curriculum.total_courses');
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
        ]);
        $gradeSearch = trim($validated['search'] ?? '');
        $semesterCourseCodes = collect(config('bpa_curriculum.years'))
            ->flatMap(fn (array $year): array => $year['semesters'])
            ->map(fn (array $semester): array => collect($semester['courses'])->pluck('code')->all())
            ->values();
        $curriculumCourseCodes = $semesterCourseCodes
            ->flatten()
            ->unique()
            ->values()
            ->all();
        $curriculumCourseCount = count($curriculumCourseCodes);

        $students = Student::query()
            ->with([
                'programHead',
                'completedCourses' => fn (HasMany $relation) => $relation
                    ->getQuery()
                    ->select('id', 'student_id', 'course_code')
                    ->whereNotNull('grade')
                    ->whereIn('course_code', $curriculumCourseCodes),
            ])
            ->withCount('completedCourses')
            ->orderBy('entry_year')
            ->orderBy('name')
            ->get();

        $students = $students->map(function (Student $student) use ($semesterCourseCodes): Student {
            $gradedCourseCodes = $student->completedCourses
                ->pluck('course_code')
                ->unique()
                ->values()
                ->all();
            $completeSemestersCount = $semesterCourseCodes
                ->filter(fn (array $courseCodes): bool => $courseCodes !== []
                    && array_diff($courseCodes, $gradedCourseCodes) === [])
                ->count();

            $student->setAttribute('graded_course_count', count($gradedCourseCodes));
            $student->setAttribute('complete_semesters_count', $completeSemestersCount);

            return $student;
        });
        $gradeProgressStudents = $students
            ->filter(fn (Student $student): bool => $student->graded_course_count > 0)
            ->values();
        $gradeProgressStudentCount = $gradeProgressStudents->count();

        if ($gradeSearch !== '') {
            $normalizedSearch = Str::lower($gradeSearch);
            $gradeProgressStudents = $gradeProgressStudents
                ->filter(fn (Student $student): bool => str_contains(
                    Str::lower($student->name.' '.$student->student_id),
                    $normalizedSearch,
                ))
                ->values();
        }

        $enrollment = $students
            ->groupBy('entry_year')
            ->map(fn ($group) => $group->count())
            ->sortKeys();

        $progress = [
            'completed' => $students->filter(
                fn (Student $student) => $totalCourses > 0
                    && $student->completed_courses_count >= $totalCourses,
            )->count(),
            'in_progress' => $students->filter(
                fn (Student $student) => $student->completed_courses_count > 0
                    && $student->completed_courses_count < $totalCourses,
            )->count(),
            'not_started' => $students->filter(
                fn (Student $student) => $student->completed_courses_count === 0,
            )->count(),
        ];

        $programHeadCounts = Student::query()
            ->select('user_id', DB::raw('count(*) as total'))
            ->groupBy('user_id')
            ->pluck('total', 'user_id');

        $programHeadNames = User::query()
            ->where('role', User::ROLE_PROGRAM_HEAD)
            ->pluck('name', 'id');

        $monthlyEnrollment = collect(range(0, 11))->mapWithKeys(function (int $month) {
            $date = now()->startOfYear()->addMonths($month);

            return [$date->format('Y-m') => 0];
        });

        $monthlyCounts = Student::query()
            ->get(['created_at'])
            ->groupBy(fn (Student $student) => $student->created_at->format('Y-m'))
            ->map->count();

        $monthlyEnrollment = $monthlyEnrollment->map(
            fn (int $count, string $month) => (int) $monthlyCounts->get($month, 0),
        );

        return view('registrar.dashboard', [
            'students' => $students,
            'enrollment' => $enrollment,
            'progress' => $progress,
            'monthlyEnrollment' => $monthlyEnrollment,
            'programHeadCounts' => $programHeadCounts,
            'programHeadNames' => $programHeadNames,
            'recentTransactions' => $students->sortByDesc('updated_at')->take(8),
            'gradeProgressStudents' => $gradeProgressStudents,
            'gradeProgressStudentCount' => $gradeProgressStudentCount,
            'gradeSearch' => $gradeSearch,
            'curriculumCourseCount' => $curriculumCourseCount,
            'semesterCount' => $semesterCourseCodes->count(),
            'totalStudents' => $students->count(),
            'totalGrades' => StudentCourseCompletion::query()->count(),
        ]);
    }
}
