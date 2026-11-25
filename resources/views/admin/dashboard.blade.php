@extends('layouts.portal')

@section('title', 'School Administration')
@section('description', 'Interactive registrar and student enrollment overview.')

@section('content')
<main class="admin-dashboard analytics-dashboard">
    <div class="analytics-shell">
        <div class="analytics-content">
    <section class="admin-heading">
        <div>
            <p class="portal-kicker"><span></span> SCHOOL ADMINISTRATION</p>
            <h1>Registrar overview</h1>
            <p>Enrollment, student progress, and Program Head activity across CCCC.</p>
        </div>
        <div class="admin-heading-actions">
            <span class="admin-live-status"><i aria-hidden="true"></i> Live overview</span>
            <button class="portal-primary-button" type="button" data-open-program-head-modal>
                <span class="button-icon" aria-hidden="true">＋</span>
                Create Program Head
            </button>
        </div>
    </section>

    @if (session('status'))
        <p class="form-status" role="status">{{ session('status') }}</p>
    @endif

    <section class="admin-metrics" aria-label="School metrics">
        <article>
            <div class="admin-metric-top"><span class="admin-metric-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </span><small>All records</small></div>
            <strong>{{ $totalStudents }}</strong>
            <span>Total students</span>
        </article>
        <article>
            <div class="admin-metric-top"><span class="admin-metric-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21v-2a8 8 0 0 1 16 0v2"/></svg>
            </span><small>Authorized</small></div>
            <strong>{{ $totalProgramHeads }}</strong>
            <span>Program Heads</span>
        </article>
        <article>
            <div class="admin-metric-top"><span class="admin-metric-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24"><path d="M4 19V9M10 19V5M16 19v-7M22 19H2"/></svg>
            </span><small>Curriculum</small></div>
            <strong>{{ $totalCompletedCourses }}</strong>
            <span>Course grades entered</span>
        </article>
        <article>
            <div class="admin-metric-top"><span class="admin-metric-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="m8 12 2.5 2.5L16 9"/></svg>
            </span><small>On track</small></div>
            <strong>{{ $progress['completed'] }}</strong>
            <span>Completed checklists</span>
        </article>
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
            <div class="admin-card-heading"><div><span>REGISTRAR ACTIVITY</span><h2>Recent student records</h2></div></div>
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

        <aside class="admin-create-card" aria-label="Program Head account summary">
            <span class="form-card-kicker">STAFF OVERVIEW</span>
            <h2>Program Heads</h2>
            <p>{{ $totalProgramHeads }} authorized staff {{ $totalProgramHeads === 1 ? 'account' : 'accounts' }}.</p>
            <div class="program-head-summary">
                @foreach ($programHeads as $programHead)
                    <div>
                        <span>{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($programHead->name, 0, 1)) }}</span>
                        <p><strong>{{ $programHead->name }}</strong><small>{{ $programHead->email }}</small></p>
                        <b title="Prospectus template">{{ $programHead->program }}</b>
                    </div>
                @endforeach
            </div>
        </aside>
    </section>

    <section class="admin-card-full">
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

<div id="program-head-modal" class="account-modal" role="dialog" aria-modal="true" aria-labelledby="program-head-modal-title" @if($errors->any()) data-open-on-load="true" @endif>
    <button class="account-modal-backdrop" type="button" data-close-program-head-modal aria-label="Close Program Head dialog"></button>
    <section class="account-modal-panel">
        <header class="account-modal-header">
            <div>
                <span class="form-card-kicker">ADMIN ACCOUNT</span>
                <h2 id="program-head-modal-title">Create Program Head</h2>
            </div>
            <button class="account-modal-close" type="button" data-close-program-head-modal aria-label="Close dialog">×</button>
        </header>
        @if ($errors->any())
            <p class="student-form-error-summary" role="alert">Please review the highlighted fields and try again.</p>
        @endif
        <form method="POST" action="{{ route('admin.program-heads.store') }}" class="student-create-form program-head-modal-form">
            @csrf
            <label for="head-name">Full name</label>
            <input id="head-name" name="name" value="{{ old('name') }}" required maxlength="255" autocomplete="name" @if($errors->has('name')) aria-invalid="true" aria-describedby="head-name-error" @endif>
            @error('name')<p id="head-name-error" class="field-error">{{ $message }}</p>@enderror
            <label for="head-email">Email address</label>
            <input id="head-email" name="email" type="email" value="{{ old('email') }}" required maxlength="255" autocomplete="email" @if($errors->has('email')) aria-invalid="true" aria-describedby="head-email-error" @endif>
            @error('email')<p id="head-email-error" class="field-error">{{ $message }}</p>@enderror
            <label for="head-program">Prospectus program</label>
            <select id="head-program" name="program" required>
                <option value="BPA" @selected(old('program', 'BPA') === 'BPA')>BPA — Bachelor of Public Administration</option>
                <option value="BSCRIM" @selected(old('program') === 'BSCRIM')>BSCRIM — Bachelor of Science in Criminology</option>
                <option value="BSISM" @selected(old('program') === 'BSISM')>BSISM — Bachelor of Science in Information Systems Management</option>
                <option value="BSENTREP" @selected(old('program') === 'BSENTREP')>BSENTREP — Bachelor of Science in Entrepreneurship</option>
                <option value="BSOA" @selected(old('program') === 'BSOA')>BSOA — Bachelor of Science in Office Administration</option>
            </select>
            @error('program')<p id="head-program-error" class="field-error">{{ $message }}</p>@enderror
            <label for="head-password">Temporary password</label>
            <input id="head-password" name="password" type="password" required minlength="12" autocomplete="new-password" @if($errors->has('password')) aria-invalid="true" aria-describedby="head-password-error" @endif>
            @error('password')<p id="head-password-error" class="field-error">{{ $message }}</p>@enderror
            <label for="head-password-confirmation">Confirm password</label>
            <input id="head-password-confirmation" name="password_confirmation" type="password" required minlength="12" autocomplete="new-password">
            <div class="account-modal-actions">
                <button class="account-modal-cancel" type="button" data-close-program-head-modal>Cancel</button>
                <button class="portal-primary-button create-student-button" type="submit"><span class="button-icon" aria-hidden="true">＋</span> Create account</button>
            </div>
        </form>
    </section>
</div>

<script>
const programHeadModal = document.querySelector('#program-head-modal');
const programHeadOpenButton = document.querySelector('[data-open-program-head-modal]');
let programHeadReturnFocus = null;

function openProgramHeadModal() {
    if (!programHeadModal) return;
    programHeadReturnFocus = document.activeElement;
    programHeadModal.classList.add('is-open');
    document.body.classList.add('modal-open');
    window.setTimeout(() => programHeadModal.querySelector('#head-name')?.focus(), 30);
}

function closeProgramHeadModal() {
    if (!programHeadModal) return;
    programHeadModal.classList.remove('is-open');
    document.body.classList.remove('modal-open');
    programHeadReturnFocus?.focus();
}

programHeadOpenButton?.addEventListener('click', openProgramHeadModal);
document.querySelectorAll('[data-close-program-head-modal]').forEach((button) => button.addEventListener('click', closeProgramHeadModal));
programHeadModal?.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') closeProgramHeadModal();
});
if (programHeadModal?.dataset.openOnLoad === 'true') openProgramHeadModal();
</script>
@endsection
