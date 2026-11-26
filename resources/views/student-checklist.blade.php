<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#a94720">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description"
        content="Program Head course checklist for {{ $student->name }} at Catbalogan City Community College.">
    <title>{{ $student->name }} · Student Course Checklist</title>
    <link rel="stylesheet" href="{{ asset('css/student-checklist.css') }}">
    <link rel="stylesheet" href="{{ asset('css/portal.css') }}">
    <link rel="icon" href="{{ asset('assets/img/cccc.png') }}" type="image/x-icon">
    <script>
        window.bpaCurriculum = @js($curriculum);
        window.completedCourseGrades = @js($completedCourseGrades);
        window.canEnterGrades = @js(auth()->user()->canEnterGrades());
        window.bpaTotalUnits = {{ $totalUnits }};
    </script>
    <script src="{{ asset('js/student-checklist.js') }}" defer></script>
</head>

<body data-student-id="{{ $student->id }}" data-course-base="{{ route('students.show', $student) }}/courses">
    <header class="topbar">
        <a class="brand" href="{{ route('students.index') }}" aria-label="Student records">
            <img class="college-logo" src="{{ asset('assets/img/CCCClogo.webp') }}" width="45" height="47" alt="">
            <span class="brand-copy">
                <strong>CCCC</strong>
                <span>CATBALOGAN CITY COMMUNITY COLLEGE</span>
            </span>
        </a>
        <button class="checklist-menu-toggle" type="button" aria-label="Open navigation menu" aria-expanded="false" aria-controls="checklist-mobile-menu">
            <span></span><span></span><span></span>
        </button>
        <div id="checklist-mobile-menu" class="topbar-right">
            <a class="topbar-link is-active" href="{{ route('students.index') }}" aria-current="page"><span aria-hidden="true">←</span> All students</a>
            <span class="account-name">{{ auth()->user()->isRegistrar() ? 'Registrar' : (auth()->user()->isAdministrator() ? 'Administrator' : 'Program Head') }} · {{ auth()->user()->name }}</span>
            <form class="logout-form" method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="logout-button" type="submit"><span aria-hidden="true">↪</span> Sign out</button>
            </form>
        </div>
    </header>

    <main id="home" class="page-shell">
        @if (session('status'))
            <p class="student-status" role="status">{{ session('status') }}</p>
        @elseif ($errors->any())
            <p class="student-status" role="alert">Could not save the student details. Review the highlighted fields in Edit
                record.</p>
        @endif
        <section class="student-record-bar">
            <div class="student-record-summary">
                <span class="student-record-label">STUDENT RECORD</span>
                <strong>{{ $student->student_id }}</strong>
                <span>Entered {{ $student->entry_year }}@if ($student->expected_graduation_year) <span
                    class="footer-dot">·</span> Expected graduation
                {{ $student->expected_graduation_year }}@endif</span>
                @if ($student->previous_school)
                    <span>Previous school: {{ $student->previous_school }}@if ($student->last_year_attended) · Last attended
                    {{ $student->last_year_attended }}@endif</span>
                @endif
            </div>
            <details class="student-edit" @if ($errors->any()) open @endif>
                <summary>Edit record</summary>
                <form method="POST" action="{{ route('students.update', $student) }}" class="student-edit-form">
                    @csrf
                    @method('PUT')
                    <label for="edit-student-name">Full name<input id="edit-student-name" name="name"
                            value="{{ old('name', $student->name) }}" required maxlength="255"
                            aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}" @if ($errors->has('name'))
                            aria-describedby="edit-student-name-error" @endif></label>
                    @error('name')
                    <p id="edit-student-name-error" class="login-error">{{ $message }}</p>@enderror
                    <label for="edit-student-id">Student ID<input id="edit-student-id" name="student_id"
                            value="{{ old('student_id', $student->student_id) }}" required maxlength="50"
                            aria-invalid="{{ $errors->has('student_id') ? 'true' : 'false' }}" @if ($errors->has('student_id')) aria-describedby="edit-student-id-error" @endif></label>
                    @error('student_id')
                    <p id="edit-student-id-error" class="login-error">{{ $message }}</p>@enderror
                    <label for="edit-entry-year">Year entered<input id="edit-entry-year" name="entry_year" type="number"
                            value="{{ old('entry_year', $student->entry_year) }}" min="1950" max="2100" required
                            aria-invalid="{{ $errors->has('entry_year') ? 'true' : 'false' }}" @if ($errors->has('entry_year')) aria-describedby="edit-entry-year-error" @endif></label>
                    @error('entry_year')
                    <p id="edit-entry-year-error" class="login-error">{{ $message }}</p>@enderror
                    <label for="edit-graduation-year">Expected graduation <span
                            class="optional-label">Optional</span><input id="edit-graduation-year"
                            name="expected_graduation_year" type="number"
                            value="{{ old('expected_graduation_year', $student->expected_graduation_year) }}" min="1950"
                            max="2105" aria-invalid="{{ $errors->has('expected_graduation_year') ? 'true' : 'false' }}"
                            @if ($errors->has('expected_graduation_year')) aria-describedby="edit-graduation-year-error"
                            @endif></label>
                    @error('expected_graduation_year')
                    <p id="edit-graduation-year-error" class="login-error">{{ $message }}</p>@enderror
                    <label for="edit-previous-school">Previous school <span class="optional-label">Optional</span><input
                            id="edit-previous-school" name="previous_school"
                            value="{{ old('previous_school', $student->previous_school) }}" maxlength="255"
                            aria-invalid="{{ $errors->has('previous_school') ? 'true' : 'false' }}" @if ($errors->has('previous_school')) aria-describedby="edit-previous-school-error"
                            @endif></label>
                    @error('previous_school')
                    <p id="edit-previous-school-error" class="login-error">{{ $message }}</p>@enderror
                    <label for="edit-last-year">Last year attended <span class="optional-label">Optional</span><input
                            id="edit-last-year" name="last_year_attended"
                            value="{{ old('last_year_attended', $student->last_year_attended) }}" maxlength="50"
                            aria-invalid="{{ $errors->has('last_year_attended') ? 'true' : 'false' }}" @if ($errors->has('last_year_attended')) aria-describedby="edit-last-year-error" @endif></label>
                    @error('last_year_attended')
                    <p id="edit-last-year-error" class="login-error">{{ $message }}</p>@enderror
                    <button class="portal-primary-button" type="submit"><span class="button-icon" aria-hidden="true">✓</span> Save student details</button>
                </form>
            </details>
        </section>

        <section class="welcome-row" aria-labelledby="welcome-title">
            <div class="welcome-copy">
                <div class="eyebrow"><span class="eyebrow-line"></span> STUDENT COURSE CHECKLIST <span
                        class="eyebrow-year">CURRICULUM</span></div>
                <h1 id="welcome-title">Course checklist<br><span>{{ $student->name }}.</span></h1>
                <p class="welcome-intro">Review course progress against the assigned CCCC curriculum. Use the official
                    student evaluation for advising and enrollment decisions.</p>
                <a class="primary-link" href="#curriculum">Review courses <span aria-hidden="true">↓</span></a>
            </div>
            <aside class="student-curriculum-summary">
                <span>ACADEMIC PROGRAM</span>
                <strong>Assigned curriculum</strong>
                <span>Catbalogan City Community College · curriculum</span>
                <div><span>Courses</span><strong>{{ $totalCourses }}</strong></div>
                <div><span>Semesters</span><strong>8</strong></div>
                <div><span>Total units</span><strong>{{ $totalUnits }}</strong></div>
            </aside>
        </section>

        <section class="dashboard" aria-label="Degree progress">
            <div class="dashboard-heading">
                <div>
                    <div class="eyebrow"><span class="eyebrow-line"></span> COURSE RECORD</div>
                    <h2>Progress for {{ $student->student_id }}.</h2>
                </div>
                <span class="save-note" aria-live="polite"><span id="save-icon" class="save-check"
                        aria-hidden="true">✓</span> <span id="save-label">Saved to this student record</span></span>
            </div>
            <div class="stats-grid">
                <article class="stat-card stat-progress">
                    <div class="stat-copy">
                        <span class="stat-label">OVERALL PROGRESS</span>
                        <strong id="overall-percent" class="stat-value">0<span> %</span></strong>
                        <span id="overall-detail" class="stat-detail">0 of {{ $totalCourses }} courses complete</span>
                    </div>
                    <div id="progress-ring" class="progress-ring" role="img"
                        aria-label="0 percent of courses completed">
                        <div class="ring-inner"><span id="ring-percent">0%</span><small>COMPLETE</small></div>
                    </div>
                </article>
                <article class="stat-card">
                    <span class="stat-icon icon-gold" aria-hidden="true">✓</span>
                    <span class="stat-label">COURSES COMPLETED</span>
                    <strong id="completed-count" class="stat-value">0</strong>
                    <span id="course-count-detail" class="stat-detail">across 8 semesters</span>
                </article>
                <article class="stat-card">
                    <span class="stat-icon icon-orange" aria-hidden="true">U</span>
                    <span class="stat-label">UNITS EARNED</span>
                    <strong id="earned-units" class="stat-value">0<span class="unit-suffix"> /
                            {{ $totalUnits }}</span></strong>
                    <span class="stat-detail">Curriculum total: {{ $totalUnits }} units.</span>
                </article>
                <article class="stat-card stat-next">
                    <span class="stat-label">UP NEXT</span>
                    <strong id="next-course-title" class="next-course">Start with your first course</strong>
                    <span id="next-course-detail" class="stat-detail">Your journey begins here.</span>
                    <a href="#curriculum" class="text-link">View checklist <span aria-hidden="true">→</span></a>
                </article>
            </div>
        </section>

        <section id="curriculum" class="curriculum-section" aria-labelledby="curriculum-title">
            <div class="curriculum-heading">
                <div>
                    <div class="eyebrow"><span class="eyebrow-line"></span> OFFICIAL CURRICULUM</div>
                    <h2 id="curriculum-title">Course checklist</h2>
                    <p class="section-subtitle">Review by year and semester. Changes are saved to this student record.
                    </p>
                </div>
                <div class="curriculum-actions">
                    @if (auth()->user()->isRegistrar())
                        <label class="prospectus-template-picker" for="prospectus-template">
                            <span>Template <small>Testing</small></span>
                            <select id="prospectus-template" aria-label="Select prospectus template for testing">
                                @foreach (config('prospectus.templates', []) as $program => $template)
                                    <option value="{{ $program }}" @selected($student->programHead?->program === $program)>
                                        {{ $program }}
                                    </option>
                                @endforeach
                            </select>
                        </label>
                        <button class="primary-link" type="button" id="open-prospectus-modal" aria-haspopup="dialog"
                            aria-controls="prospectus-modal">
                            Preview Prospectus <span aria-hidden="true">↗</span>
                        </button>
                    @endif
                    <label class="search-box">
                        <span class="search-icon" aria-hidden="true">
                            <svg viewBox="0 0 20 20" fill="none">
                                <circle cx="8.8" cy="8.8" r="5.6" stroke="currentColor" stroke-width="1.6" />
                                <path d="m13 13 4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
                            </svg>
                        </span>
                        <input id="course-search" type="search" placeholder="Find a course..." autocomplete="off"
                            aria-label="Find a course in this semester">
                        <kbd>/</kbd>
                    </label>
                </div>
            </div>

            @if (auth()->user()->isRegistrar())
                <dialog class="prospectus-modal" id="prospectus-modal" aria-label="Prospectus preview">
                    <header class="prospectus-modal-header">
                        <button class="prospectus-modal-close" type="button" id="close-prospectus-modal"
                            aria-label="Close prospectus preview">×</button>
                    </header>
                    <iframe class="prospectus-modal-frame" title="Prospectus PDF preview" src="about:blank"
                        data-url="{{ route('students.prospectus.stream', $student) }}"></iframe>
                </dialog>
            @endif

            <div class="curriculum-layout">
                <aside class="year-rail" aria-label="Choose an academic year">
                    <span class="rail-label">YOUR YEARS</span>
                    <div id="year-tabs" class="year-tabs" role="tablist" aria-label="Academic year"></div>
                    <div class="rail-note">
                        <span class="rail-note-icon" aria-hidden="true">i</span>
                        <p>Use 0.00 for no grade. Grades 1.00–2.99 are passed; 3.00–5.00 are not passed and shown in red.</p>
                    </div>
                </aside>

                <div class="semester-panel">
                    <div class="semester-header">
                        <div class="semester-heading">
                            <span id="semester-kicker" class="semester-kicker">YEAR ONE · FOUNDATIONS</span>
                            <h3 id="semester-title">First semester</h3>
                            <span id="semester-subtitle" class="semester-subtitle">8 courses · 23 units</span>
                        </div>
                        <div class="semester-controls">
                            <div id="semester-tabs" class="semester-tabs" role="tablist" aria-label="Choose a semester">
                                <button class="semester-tab is-active" type="button" role="tab" aria-selected="true"
                                    data-semester="0">First semester</button>
                                <button class="semester-tab" type="button" role="tab" aria-selected="false"
                                    data-semester="1">Second semester</button>
                            </div>
                            <div class="term-progress" aria-live="polite">
                                <div class="term-progress-copy"><span>SEMESTER PROGRESS</span><strong
                                        id="semester-progress-text">0 / 8</strong></div>
                                <div class="term-progress-track"><span id="semester-progress-bar"></span></div>
                            </div>
                        </div>
                    </div>
                    <div class="course-list-head" aria-hidden="true">
                        <span>SUBJECT / GRADE</span><span>UNITS</span><span>STATUS</span>
                    </div>
                    <div id="course-list" class="course-list"></div>
                    <div id="empty-state" class="empty-state" role="status" hidden>
                        <span aria-hidden="true">⌕</span>
                        <strong>No courses found</strong>
                        <p>Try a different course title or code.</p>
                    </div>
                    <div class="semester-footer">
                        <span><span class="footer-star" aria-hidden="true">•</span> Course progress is saved
                            automatically.</span>
                    </div>
                </div>
                <aside class="student-switcher" aria-label="Switch student record">
                    <div class="student-switcher-heading">
                        <span>STUDENT RECORDS</span>
                        <strong>{{ $studentList->count() }}</strong>
                    </div>
                    <label class="student-switcher-search">
                        <span class="visually-hidden">Find a student record</span>
                        <input id="student-switcher-search" type="search" placeholder="Find student..." autocomplete="off">
                    </label>
                    <nav class="student-switcher-list" aria-label="Other student records">
                        @foreach ($studentList as $switchStudent)
                            @php($switchProgress = $totalCourses ? (int) round(($switchStudent->completed_courses_count / $totalCourses) * 100) : 0)
                            <a class="student-switcher-item {{ $switchStudent->is($student) ? 'is-active' : '' }}"
                                href="{{ route('students.show', $switchStudent) }}"
                                data-student-name="{{ $switchStudent->name }}"
                                data-student-id="{{ $switchStudent->student_id }}"
                                @if ($switchStudent->is($student)) aria-current="page" @endif>
                                <span class="student-switcher-avatar" aria-hidden="true">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($switchStudent->name, 0, 1)) }}</span>
                                <span class="student-switcher-copy">
                                    <strong>{{ $switchStudent->name }}</strong>
                                    <span>{{ $switchStudent->student_id }}</span>
                                    <span class="student-switcher-progress"><i style="width: {{ $switchProgress }}%"></i></span>
                                </span>
                                @if ($switchStudent->is($student))
                                    <span class="student-switcher-current" aria-hidden="true">•</span>
                                @else
                                    <span class="student-switcher-arrow" aria-hidden="true">→</span>
                                @endif
                            </a>
                        @endforeach
                    </nav>
                    <p id="student-switcher-empty" class="student-switcher-empty" role="status" hidden>No matching student records.</p>
                    <a class="student-switcher-all" href="{{ route('students.index') }}">View all student records <span aria-hidden="true">→</span></a>
                </aside>
            </div>
        </section>

        <footer class="page-footer">
            <a class="footer-brand" href="{{ route('students.index') }}"><span class="footer-mark"
                    aria-hidden="true">CCCC</span> Student records</a>
            <span>Academic course checklist <span class="footer-dot">·</span> {{ $totalUnits }} total units</span>
            <a href="#home" class="back-to-top">Back to top ↑</a>
        </footer>
        <p id="announcement" class="visually-hidden" aria-live="polite"></p>
    </main>
    <script>
        document.querySelectorAll('.checklist-menu-toggle').forEach((button) => {
            const menu = document.querySelector('#checklist-mobile-menu');
            if (!menu) return;

            button.addEventListener('click', () => {
                const open = button.getAttribute('aria-expanded') === 'true';
                button.setAttribute('aria-expanded', String(!open));
                button.setAttribute('aria-label', open ? 'Open navigation menu' : 'Close navigation menu');
                menu.classList.toggle('is-open', !open);
            });

            menu.querySelectorAll('a, button').forEach((item) => {
                item.addEventListener('click', () => {
                    button.setAttribute('aria-expanded', 'false');
                    button.setAttribute('aria-label', 'Open navigation menu');
                    menu.classList.remove('is-open');
                });
            });
        });

        document.addEventListener('keydown', (event) => {
            if (event.key !== 'Escape') return;
            const button = document.querySelector('.checklist-menu-toggle');
            const menu = document.querySelector('#checklist-mobile-menu');
            if (!button || !menu) return;
            button.setAttribute('aria-expanded', 'false');
            button.setAttribute('aria-label', 'Open navigation menu');
            menu.classList.remove('is-open');
            button.focus();
        });

        @if (auth()->user()->isRegistrar())
            const prospectusModal = document.querySelector('#prospectus-modal');
            const prospectusFrame = document.querySelector('.prospectus-modal-frame');
            const prospectusTemplate = document.querySelector('#prospectus-template');
            document.querySelector('#open-prospectus-modal').addEventListener('click', () => {
                const previewUrl = new URL(prospectusFrame.dataset.url, window.location.href);
                previewUrl.searchParams.set('template', prospectusTemplate.value);
                prospectusFrame.src = previewUrl.toString();
                prospectusModal.showModal();
            });
            document.querySelector('#close-prospectus-modal').addEventListener('click', () => {
                prospectusModal.close();
            });
            prospectusModal.addEventListener('click', (event) => {
                if (event.target === prospectusModal) {
                    prospectusModal.close();
                }
            });
        @endif
    </script>
</body>

</html>