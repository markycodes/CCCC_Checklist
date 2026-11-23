<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentCourseCompletion;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalCourses = (int) config('bpa_curriculum.total_courses');
        $students = Student::query()
            ->with(['programHead'])
            ->withCount('completedCourses')
            ->orderBy('entry_year')
            ->orderBy('name')
            ->get();

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
            'totalStudents' => $students->count(),
            'totalGrades' => StudentCourseCompletion::query()->count(),
        ]);
    }
}
