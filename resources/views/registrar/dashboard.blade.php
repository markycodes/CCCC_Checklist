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
        <article class="admin-chart-card">
            <div class="admin-card-heading"><div><span>ENROLLMENT</span><h2>Students by entry year</h2></div></div>
            <div class="line-chart" role="img" aria-label="Enrollment line graph">
                <div class="line-chart-bars">
                    @foreach ($enrollment as $year => $count)
                        @php($height = $enrollment->max() ? round(($count / $enrollment->max()) * 100) : 0)
                        <div class="line-chart-point">
                            <strong>{{ $count }}</strong>
                            <span style="height: {{ max($height, 4) }}%"></span>
                            <small>{{ $year }}</small>
                        </div>
                    @endforeach
                </div>
            </div>
        </article>

        <article class="admin-chart-card">
            <div class="admin-card-heading"><div><span>CHECKLIST STATUS</span><h2>Student progress</h2></div></div>
            <div class="admin-pie-layout">
                @php($totalProgress = max(array_sum($progress), 1))
                @php($completedPercent = round(($progress['completed'] / $totalProgress) * 100))
                @php($inProgressPercent = round(($progress['in_progress'] / $totalProgress) * 100))
                <div class="progress-pie" style="--completed: {{ $completedPercent }}%; --in-progress: {{ $inProgressPercent }}%" role="img" aria-label="{{ $completedPercent }} percent completed, {{ $inProgressPercent }} percent in progress">
                    <span>{{ $totalProgress }}<small>STUDENTS</small></span>
                </div>
                <div class="chart-legend">
                    <span><i class="legend-completed"></i><b>Completed</b><strong>{{ $progress['completed'] }}</strong></span>
                    <span><i class="legend-progress"></i><b>In progress</b><strong>{{ $progress['in_progress'] }}</strong></span>
                    <span><i class="legend-empty"></i><b>Not started</b><strong>{{ $progress['not_started'] }}</strong></span>
                </div>
            </div>
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
