<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentCourseCompletion;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalCourses = (int) config('bpa_curriculum.total_courses');
        $students = Student::query()
            ->withCount('completedCourses')
            ->orderBy('entry_year')
            ->orderBy('name')
            ->get();

        $enrollment = $students
            ->groupBy('entry_year')
            ->map(fn ($group) => $group->count())
            ->sortKeys();

        $headCounts = Student::query()
            ->select('user_id', DB::raw('count(*) as total'))
            ->groupBy('user_id')
            ->pluck('total', 'user_id');

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

        $programHeads = User::query()
            ->where('role', User::ROLE_PROGRAM_HEAD)
            ->withCount('students')
            ->orderBy('name')
            ->get();

        $recentTransactions = Student::query()
            ->with('programHead')
            ->orderByDesc('updated_at')
            ->limit(8)
            ->get();

        $gradeUpdates = StudentCourseCompletion::query()
            ->with('student.programHead')
            ->orderByDesc('updated_at')
            ->limit(8)
            ->get();

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

        return view('admin.dashboard', [
            'students' => $students,
            'programHeads' => $programHeads,
            'enrollment' => $enrollment,
            'headCounts' => $headCounts,
            'progress' => $progress,
            'recentTransactions' => $recentTransactions,
            'gradeUpdates' => $gradeUpdates,
            'monthlyEnrollment' => $monthlyEnrollment,
            'totalStudents' => $students->count(),
            'totalProgramHeads' => $programHeads->count(),
            'totalCompletedCourses' => StudentCourseCompletion::query()->count(),
        ]);
    }

    public function storeProgramHead(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:12', 'confirmed'],
            'program' => ['required', 'string', 'max:20', 'in:BPA,BSCRIM,BSISM,BSENTREP,BSOA'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => Str::lower(trim($validated['email'])),
            'password' => $validated['password'],
            'role' => User::ROLE_PROGRAM_HEAD,
            'program' => Str::upper($validated['program']),
        ]);

        return redirect()
            ->route('admin.dashboard')
            ->with('status', 'Program Head account created.');
    }
}
