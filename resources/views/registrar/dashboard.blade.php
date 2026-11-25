@extends('layouts.portal')

@section('title', 'Registrar Overview')
@section('description', 'Registrar student enrollment and record overview.')

@section('content')
<main class="admin-dashboard analytics-dashboard">
    <div class="analytics-shell">
        <div class="analytics-content">
    <section class="admin-heading">
        <div>
            <p class="portal-kicker"><span></span> REGISTRAR OFFICE</p>
            <h1>Student overview</h1>
            <p>Enrollment, student records, and current course progress across CCCC.</p>
        </div>
        <a class="portal-primary-button" href="{{ route('students.index') }}"><span class="button-icon" aria-hidden="true">♙</span> Manage student records</a>
    </section>

    <section class="admin-metrics" aria-label="Registrar metrics">
        <article><span>Total students</span><strong>{{ $totalStudents }}</strong></article>
        <article><span>Grade entries</span><strong>{{ $totalGrades }}</strong></article>
        <article><span>Completed checklists</span><strong>{{ $progress['completed'] }}</strong></article>
        <article><span>Not started</span><strong>{{ $progress['not_started'] }}</strong></article>
    </section>

    <section class="admin-grid">
        <article class="admin-chart-card admin-enrollment-chart">
            <div class="admin-card-heading"><div><span>ENROLLMENT</span><h2>Students by entry year</h2></div></div>
            @include('partials.enrollment-chart', ['enrollment' => $enrollment])
        </article>

        <article class="admin-chart-card admin-status-chart">
            <div class="admin-card-heading"><div><span>CHECKLIST STATUS</span><h2>Student progress</h2></div></div>
            @include('partials.progress-radial-chart', ['progress' => $progress])
        </article>
    </section>

    <section class="admin-grid admin-grid-secondary">
        <article class="admin-table-card">
            <div class="admin-card-heading"><div><span>RECENT STUDENT ACTIVITY</span><h2>Latest records</h2></div></div>
            <div class="admin-table-wrap">
                <table>
                    <thead><tr><th>Student</th><th>Student ID</th><th>Entry year</th><th>Program Head</th></tr></thead>
                    <tbody>
                    @forelse ($recentTransactions as $student)
                        <tr>
                            <td><a href="{{ route('students.show', $student) }}">{{ $student->name }}</a></td>
                            <td>{{ $student->student_id }}</td>
                            <td>{{ $student->entry_year }}</td>
                            <td>{{ $student->programHead?->name ?? 'Unassigned' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4">No student records yet.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </article>

        <article class="admin-chart-card">
            <div class="admin-card-heading"><div><span>ASSIGNMENTS</span><h2>Students by Program Head</h2></div></div>
            <div class="line-chart" role="img" aria-label="Students assigned to each Program Head">
                <div class="line-chart-bars">
                    @foreach ($programHeadNames as $headId => $headName)
                        @php($count = (int) $programHeadCounts->get($headId, 0))
                        @php($height = $programHeadCounts->max() ? round(($count / $programHeadCounts->max()) * 100) : 0)
                        <div class="line-chart-point">
                            <strong>{{ $count }}</strong>
                            <span style="height: {{ max($height, 4) }}%"></span>
                            <small>{{ \Illuminate\Support\Str::limit($headName, 12) }}</small>
                        </div>
                    @endforeach
                </div>
            </div>
        </article>
    </section>

    <section class="admin-card-full registrar-grade-completion" aria-labelledby="registrar-grade-completion-title">
        <div class="admin-card-heading">
            <div>
                <span>GRADE COMPLETION LOG</span>
                <h2 id="registrar-grade-completion-title">Student grade progress by semester</h2>
                <p>Track course grades and fully graded semesters for every student.</p>
            </div>
            <strong class="registrar-grade-total">{{ $gradeProgressStudentCount }} {{ \Illuminate\Support\Str::plural('student', $gradeProgressStudentCount) }}</strong>
        </div>

        <form class="registrar-grade-search" role="search" aria-label="Search students with grades" method="GET" action="{{ route('registrar.dashboard') }}">
            <label for="registrar-grade-search">Search by student name or ID</label>
            <div class="registrar-grade-search-controls">
                <input
                    id="registrar-grade-search"
                    type="search"
                    name="search"
                    value="{{ $gradeSearch }}"
                    placeholder="Name or student ID"
                    maxlength="100"
                    autocomplete="off"
                    aria-controls="registrar-grade-progress-table"
                >
                <button class="portal-primary-button" type="submit">Search</button>
                @if ($gradeSearch !== '')
                    <a class="registrar-grade-clear" href="{{ route('registrar.dashboard') }}">Clear</a>
                @endif
            </div>
        </form>

        <p class="registrar-grade-summary" role="status">
            @if ($gradeSearch !== '')
                Showing {{ $gradeProgressStudents->count() }} of {{ $gradeProgressStudentCount }} students.
            @else
                Showing {{ $gradeProgressStudentCount }} {{ \Illuminate\Support\Str::plural('student', $gradeProgressStudentCount) }}.
            @endif
        </p>

        <div class="admin-table-wrap">
            <table id="registrar-grade-progress-table">
                <thead>
                    <tr><th>Student</th><th>Student ID</th><th>Entry year</th><th>Program Head</th><th>Semesters complete</th><th>Course grades</th><th>Grade status</th></tr>
                </thead>
                <tbody>
                    @forelse ($gradeProgressStudents as $student)
                        <tr data-grade-progress-student="{{ $student->student_id }}">
                            <td><a href="{{ route('students.show', $student) }}">{{ $student->name }}</a></td>
                            <td>{{ $student->student_id }}</td>
                            <td>{{ $student->entry_year }}</td>
                            <td>{{ $student->programHead?->name ?? 'Unassigned' }}</td>
                            <td>{{ $student->complete_semesters_count }} / {{ $semesterCount }} semesters</td>
                            <td>{{ $student->graded_course_count }} / {{ $curriculumCourseCount }} courses</td>
                            <td>
                                @if ($semesterCount > 0 && $student->complete_semesters_count === $semesterCount)
                                    <span class="registrar-grade-status registrar-grade-status-complete">All semesters complete</span>
                                @elseif ($student->graded_course_count > 0)
                                    <span class="registrar-grade-status registrar-grade-status-progress">In progress</span>
                                @else
                                    <span class="registrar-grade-status registrar-grade-status-empty">No grades entered</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                @if ($gradeSearch !== '')
                                    No students match "{{ $gradeSearch }}".
                                @elseif ($totalStudents > 0)
                                    No students with grades yet.
                                @else
                                    No student records yet.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section id="student-trend" class="admin-card-full">
        <div class="admin-card-heading"><div><span>YEARLY REGISTRAR VIEW</span><h2>Enrollment trend</h2></div></div>
        <div class="monthly-line-chart" role="img" aria-label="Monthly student record enrollment line chart">
            @foreach ($monthlyEnrollment as $month => $count)
                @php($peak = max($monthlyEnrollment->max(), 1))
                <div class="monthly-line-point">
                    <strong>{{ $count }}</strong>
                    <span style="height: {{ max(round(($count / $peak) * 100), 4) }}%"></span>
                    <small>{{ \Illuminate\Support\Carbon::createFromFormat('Y-m', $month)->format('M') }}</small>
                </div>
            @endforeach
        </div>
    </section>
</div>
</div>
</main>
@endsection
