@extends('layouts.portal')

@section('title', 'Student records')
@section('description', 'Manage student records and course checklists.')

@section('content')
    <main class="records-page">
        <section class="records-heading">
            <div>
                <p class="portal-kicker"><span></span> STUDENT COURSE CHECKLIST</p>
                <h1>Student records</h1>
                <p>Manage student details and review course progress for assigned programs.</p>
            </div>
            <div class="records-count"><strong>{{ $students->count() }}</strong><span>{{ $students->count() === 1 ? 'student' : 'students' }} on this list</span></div>
        </section>

        @if (session('status'))
            <p class="form-status" role="status">{{ session('status') }}</p>
        @endif

        <div class="records-layout">
            <section class="records-list" aria-labelledby="student-list-title">
                <div class="records-list-heading">
                    <div><h2 id="student-list-title">Your students</h2><p>Search by name or student ID. Only you can see your records.</p></div>
                    @if ($students->isNotEmpty())
                        <div class="student-record-filters">
                            <label class="student-search">
                                <span>Find a student</span>
                                <input id="student-search" type="search" placeholder="Name or student ID" autocomplete="off" aria-controls="student-card-list">
                            </label>
                            <label class="school-year-filter">
                                <span>School year</span>
                                <select id="school-year-filter" aria-controls="student-card-list">
                                    <option value="">All school years</option>
                                    @foreach ($schoolYearOptions as $schoolYear)
                                        <option value="{{ $schoolYear }}">{{ $schoolYear }}</option>
                                    @endforeach
                                </select>
                            </label>
                        </div>
                    @endif
                </div>
                @if ($students->isEmpty())
                    <div class="records-empty">
                        <span class="records-empty-mark" aria-hidden="true">+</span>
                        <h3>No student records yet</h3>
                        <p>Use the form to add a student and start a separate course checklist.</p>
                    </div>
                @else
                    <div id="student-card-list" class="student-card-list">
                        @foreach ($students as $student)
                            @php($progress = $totalCourses ? (int) round(($student->completed_courses_count / $totalCourses) * 100) : 0)
                            @php($studentEndYear = $student->expected_graduation_year ?: $student->entry_year + 4)
                            <a class="student-card"
                                data-student-search="{{ $student->name }} {{ $student->student_id }}"
                                data-entry-year="{{ $student->entry_year }}"
                                data-end-year="{{ $studentEndYear }}"
                                href="{{ route('students.show', $student) }}">
                                <span class="student-avatar" aria-hidden="true">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($student->name, 0, 1)) }}</span>
                                <span class="student-card-main">
                                    <strong>{{ $student->name }}</strong>
                                    <span>{{ $student->student_id }} <i>·</i> Entry {{ $student->entry_year }}</span>
                                    <span class="student-card-progress"><span><i style="width: {{ $progress }}%"></i></span><small>{{ $student->completed_courses_count }} / {{ $totalCourses }} courses</small></span>
                                </span>
                                <span class="student-card-arrow" aria-hidden="true">→</span>
                            </a>
                        @endforeach
                    </div>
                    <p id="student-search-results" class="records-search-results" role="status" hidden></p>
                @endif
            </section>

            <aside class="student-form-card">
                <span class="form-card-kicker">NEW RECORD</span>
                <h2>Add a student</h2>
                <p>Enter the student details as shown on their evaluation form.</p>
                @if ($errors->any())
                    <p class="student-form-error-summary" role="alert">Please review the highlighted fields and try again.</p>
                @endif
                <form method="POST" action="{{ route('students.store') }}" class="student-create-form">
                    @csrf
                    @if (auth()->user()->isRegistrar() || auth()->user()->isAdministrator())
                        <label for="program-head-id">Program Head</label>
                        <select id="program-head-id" name="program_head_id" required>
                            <option value="">Select Program Head</option>
                            @foreach ($programHeads as $programHead)
                                <option value="{{ $programHead->id }}" @selected(old('program_head_id') == $programHead->id)>
                                    {{ $programHead->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('program_head_id')<p class="field-error">{{ $message }}</p>@enderror
                    @endif
                    <label for="student-name">Full name</label>
                    <input id="student-name" name="name" value="{{ old('name') }}" autocomplete="name" maxlength="255" required aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}" @if ($errors->has('name')) aria-describedby="student-name-error" @endif>
                    @error('name')<p id="student-name-error" class="field-error">{{ $message }}</p>@enderror

                    <label for="student-id">Student ID number</label>
                    <input id="student-id" name="student_id" value="{{ old('student_id') }}" maxlength="50" required aria-invalid="{{ $errors->has('student_id') ? 'true' : 'false' }}" @if ($errors->has('student_id')) aria-describedby="student-id-error" @endif>
                    @error('student_id')<p id="student-id-error" class="field-error">{{ $message }}</p>@enderror

                    <div class="form-field-row">
                        <div>
                            <label for="entry-year">Year entered</label>
                            <input id="entry-year" name="entry_year" type="number" value="{{ old('entry_year') }}" min="1950" max="2100" placeholder="2024" required aria-invalid="{{ $errors->has('entry_year') ? 'true' : 'false' }}" @if ($errors->has('entry_year')) aria-describedby="entry-year-error" @endif>
                            @error('entry_year')<p id="entry-year-error" class="field-error">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="graduation-year">Expected graduation <span class="optional-label">Optional</span></label>
                            <input id="graduation-year" name="expected_graduation_year" type="number" value="{{ old('expected_graduation_year') }}" min="1950" max="2105" placeholder="2028" aria-invalid="{{ $errors->has('expected_graduation_year') ? 'true' : 'false' }}" @if ($errors->has('expected_graduation_year')) aria-describedby="graduation-year-error" @endif>
                            @error('expected_graduation_year')<p id="graduation-year-error" class="field-error">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <label for="previous-school">Previous school <span class="optional-label">Optional</span></label>
                    <input id="previous-school" name="previous_school" value="{{ old('previous_school') }}" maxlength="255" aria-invalid="{{ $errors->has('previous_school') ? 'true' : 'false' }}" @if ($errors->has('previous_school')) aria-describedby="previous-school-error" @endif>
                    @error('previous_school')<p id="previous-school-error" class="field-error">{{ $message }}</p>@enderror

                    <label for="last-year">Last year attended <span class="optional-label">Optional</span></label>
                    <input id="last-year" name="last_year_attended" value="{{ old('last_year_attended') }}" maxlength="50" placeholder="e.g. 2022–2023" aria-invalid="{{ $errors->has('last_year_attended') ? 'true' : 'false' }}" @if ($errors->has('last_year_attended')) aria-describedby="last-year-error" @endif>
                    @error('last_year_attended')<p id="last-year-error" class="field-error">{{ $message }}</p>@enderror

                    <button class="portal-primary-button create-student-button" type="submit"><span class="button-icon" aria-hidden="true">＋</span> Add student</button>
                </form>
            </aside>
        </div>
    </main>
    <script src="{{ asset('js/student-list.js') }}" defer></script>
@endsection
