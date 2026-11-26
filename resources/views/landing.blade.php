@extends('layouts.portal')

@section('title', 'Student Curriculum')
@section('description', 'The student curriculum and course checklist for Catbalogan City Community College.')

@section('content')
    <div class="landing-loader" role="status" aria-label="Loading Catbalogan City Community College page">
        <div class="landing-loader-progress landing-loader-progress-wordmark" aria-hidden="true">
            <span class="landing-loader-wordmark-base">CATBALOGAN CITY COMMUNITY COLLEGE</span>
            <span class="landing-loader-wordmark-fill">CATBALOGAN CITY COMMUNITY COLLEGE</span>
        </div>
        <img class="landing-loader-logo" src="{{ asset('assets/img/cccc.png') }}" alt="CCCC seal">
    </div>
    <main>
        <section class="portal-hero landing-hero" aria-labelledby="landing-title">
            <div class="portal-hero-copy landing-hero-copy">
                <p class="portal-kicker"><span></span> CCCC STUDENT CHECKLIST</p>
                <h1 id="landing-title">Every course.<br><em>Every step forward.</em></h1>
                <p class="portal-hero-intro">A clear academic roadmap for Catbalogan City Community College students,
                    from enrollment through graduation.</p>
                <div class="portal-hero-actions">
                    @auth
                        <a class="portal-primary-button" href="{{ route('students.index') }}">
                            Open student records
                            <span class="button-icon" aria-hidden="true">→</span>
                        </a>
                        <span class="portal-action-note">Signed in as {{ auth()->user()->name }}</span>
                    @else
                        <a class="portal-primary-button" href="{{ route('login') }}">
                            Program Head sign in
                            <span class="button-icon" aria-hidden="true">→</span>
                        </a>
                        <span class="portal-action-note">Access for authorized Program Heads</span>
                    @endauth
                </div>
                <div class="landing-hero-proof" aria-label="Academic planning information">
                    <span><strong>4</strong> academic years</span>
                    <span><strong>8</strong> semesters</span>
                    <span><strong>146</strong> total units</span>
                </div>
            </div>

            <aside class="landing-program-deck" aria-label="Available academic programs">
                @php
                    $programDeck = [
                        [
                            'code' => 'BSCRIM',
                            'name' => 'Bachelor of Science in Criminology',
                            'description' => 'Prepares students for criminal justice, law enforcement, public safety, and community-based security work through science, investigation, and ethics.',
                        ],
                        [
                            'code' => 'BSISM',
                            'name' => 'Bachelor of Science in Information Systems Management',
                            'description' => 'Combines information technology, business systems, data management, and organizational leadership for digital and enterprise operations.',
                        ],
                        [
                            'code' => 'BPA',
                            'name' => 'Bachelor of Public Administration',
                            'description' => 'Develops public service leaders skilled in governance, policy, administration, fiscal management, and community development.',
                        ],
                        [
                            'code' => 'BSENTREP',
                            'name' => 'Bachelor of Science in Entrepreneurship',
                            'description' => 'Builds entrepreneurial skills in business planning, innovation, marketing, finance, and sustainable enterprise creation.',
                        ],
                        [
                            'code' => 'BSOA',
                            'name' => 'Bachelor of Science in Office Administration',
                            'description' => 'Trains students in office operations, records management, communications, human resources, and administrative support.',
                        ],
                    ];
                @endphp
                <div class="landing-program-deck-stack" role="list" aria-label="Curriculum overview cards">
                    @foreach ($programDeck as $index => $program)
                        @php
                            $deckPosition = $index - 2;
                        @endphp
                        <button
                            type="button"
                            class="curriculum-card landing-program-card {{ $index === 2 ? 'is-front' : '' }}"
                            role="listitem"
                            data-deck-position="{{ $deckPosition }}"
                            aria-label="Show {{ $program['name'] }} overview"
                            aria-pressed="{{ $index === 2 ? 'true' : 'false' }}"
                        >
                            <span class="landing-program-card-heading">
                                <span>
                                    <span class="curriculum-card-label">{{ $program['name'] }}</span>
                                    <strong>Program overview</strong>
                                </span>
                                <span class="landing-program-code">{{ $program['code'] }}</span>
                            </span>
                            <span class="landing-program-description">{{ $program['description'] }}</span>
                            <span class="curriculum-card-note">
                                <span aria-hidden="true">i</span>
                                Explore the {{ $program['code'] }} curriculum
                            </span>
                        </button>
                    @endforeach
                </div>
            </aside>
        </section>

        <section class="scroll-reveal-shell is-scroll-reveal-pending" aria-label="How the CCCC student checklist system works">
            <div class="scroll-reveal-sticky">
                <div class="scroll-reveal-heading">
                    <p class="portal-kicker"><span></span> THE STUDENT CHECKLIST PROCESS</p>
                    <h2>From student records to academic progress.</h2>
                </div>
                <div class="scroll-reveal-frame" role="img" aria-label="Program Heads, Administrators, and Registrars use the CCCC checklist system to connect student records to program prospectuses, grades, course progress, and academic summaries">
                    <div class="scroll-reveal-process">
                        <div class="process-visual" aria-hidden="true">
                            <svg class="process-connectors" viewBox="0 0 1000 300" preserveAspectRatio="none">
                                <path class="process-path process-path-primary" d="M130 64 C265 64 278 138 400 138" />
                                <path class="process-path process-path-secondary" d="M180 148 C280 148 310 148 400 148" />
                                <path class="process-path process-path-secondary" d="M118 228 C250 228 300 164 400 158" />
                                <path class="process-path process-path-primary" d="M646 143 C714 143 744 108 812 108" />
                                <path class="process-path process-path-secondary" d="M646 158 C724 158 745 199 812 199" />
                                <path class="process-data-path" d="M130 64 C265 64 278 138 400 138" />
                                <path class="process-data-path" d="M180 148 C280 148 310 148 400 148" />
                                <path class="process-data-path" d="M118 228 C250 228 300 164 400 158" />
                                <path class="process-data-path" d="M646 143 C714 143 744 108 812 108" />
                                <path class="process-data-path" d="M646 158 C724 158 745 199 812 199" />
                                <circle cx="286" cy="87" r="4" />
                                <circle cx="348" cy="146" r="4" />
                                <circle cx="286" cy="193" r="4" />
                                <circle cx="704" cy="138" r="4" />
                                <circle cx="746" cy="177" r="4" />
                            </svg>
                            <div class="process-roles">
                                <div class="process-role">
                                    <span class="process-avatar">PH</span>
                                    <span class="process-presence"></span>
                                    <span class="process-role-label">Program Head</span>
                                </div>
                                <div class="process-role">
                                    <span class="process-avatar">AD</span>
                                    <span class="process-presence"></span>
                                    <span class="process-role-label">Administrator</span>
                                </div>
                                <div class="process-role">
                                    <span class="process-avatar">RG</span>
                                    <span class="process-presence"></span>
                                    <span class="process-role-label">Registrar</span>
                                </div>
                            </div>
                            <div class="process-system-card">
                                <div class="process-system-brand">
                                    <img src="{{ asset('assets/img/CCCClogo.webp') }}" alt="" width="34" height="34">
                                    <span>
                                        <strong>CCCC</strong>
                                        <small>CHECKLIST SYSTEM</small>
                                    </span>
                                    <span class="process-system-arrow" aria-hidden="true">›</span>
                                </div>
                                <div class="process-system-flow">
                                    <span>Enroll</span><i>›</i><span>Match program</span><i>›</i><span>Track progress</span>
                                </div>
                            </div>
                            <div class="process-report-stack">
                                <span></span>
                                <span></span>
                                <div class="process-report-card">
                                    <div class="process-report-top">
                                        <span>ACADEMIC RECORD</span>
                                        <strong>PDF</strong>
                                    </div>
                                    <h3>Progress summary</h3>
                                    <div class="process-report-subtitle">PROGRAM PROSPECTUS</div>
                                    <div class="process-report-chart">
                                        <span></span><span></span><span></span><span></span><span></span><span></span>
                                        <div><i></i><i></i></div>
                                    </div>
                                    <div class="process-report-foot">
                                        <span>Courses &amp; grades</span>
                                        <b>Updated</b>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="process-captions">
                            <article>
                                <span class="process-caption-step">01</span>
                                <div>
                                    <h3>Program-based access</h3>
                                    <p>Program Heads manage their assigned program. Administrators and Registrars can oversee records across all programs.</p>
                                </div>
                            </article>
                            <article>
                                <span class="process-caption-step">02</span>
                                <div>
                                    <h3>From prospectus to progress</h3>
                                    <p>A student's program selects the matching prospectus. Course completions and grades update the checklist and progress view.</p>
                                </div>
                            </article>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="curriculum" class="course-scroll-shell">
            <div class="course-scroll-inner">
                <div class="course-scroll-header">
                    <div>
                        <p class="portal-kicker"><span></span> COURSE OVERVIEW</p>
                        <h2>Student records from enrollment to completion.</h2>
                    </div>
                    <p>Manage academic records, monitor enrollment, and keep each student checklist ready for Program Head
                        evaluation.</p>
                </div>
                <div class="course-scroll-card">
                    <div class="course-scroll-card-inner">
                        <div class="tablet-status-bar" aria-hidden="true">
                            <span class="tablet-status-time">9:41</span>
                            <span class="tablet-status-indicators">
                                <svg class="tablet-status-icon tablet-status-wifi" viewBox="0 0 18 14" fill="none">
                                    <path d="M1.5 4.1a11.7 11.7 0 0 1 15 0" />
                                    <path d="M4.2 7.1a7.5 7.5 0 0 1 9.6 0" />
                                    <path d="M6.9 10.1a3.3 3.3 0 0 1 4.2 0" />
                                    <circle cx="9" cy="12.2" r="1" />
                                </svg>
                                <svg class="tablet-status-icon tablet-status-battery" viewBox="0 0 22 14">
                                    <rect x="1" y="2.5" width="17" height="9" rx="2" />
                                    <rect class="tablet-status-battery-level" x="3" y="4.5" width="12" height="5" rx="1" />
                                    <path d="M20 5v4" />
                                </svg>
                            </span>
                        </div>
                        <div class="program-stages">
                            <article class="program-stage">
                                <span class="stage-number">01</span>
                                <div>
                                    <h3>Enrollment</h3>
                                    <p>Register new students and maintain entry-year information.</p>
                                </div>
                                <span class="stage-units">Student records</span>
                            </article>
                            <article class="program-stage">
                                <span class="stage-number">02</span>
                                <div>
                                    <h3>Assignment</h3>
                                    <p>Place each student under the appropriate Program Head.</p>
                                </div>
                                <span class="stage-units">Program access</span>
                            </article>
                            <article class="program-stage">
                                <span class="stage-number">03</span>
                                <div>
                                    <h3>Grade review</h3>
                                    <p>Monitor course grades entered by authorized faculty staff.</p>
                                </div>
                                <span class="stage-units">Progress status</span>
                            </article>
                            <article class="program-stage">
                                <span class="stage-number">04</span>
                                <div>
                                    <h3>Reporting</h3>
                                    <p>Review enrollment trends and academic completion summaries.</p>
                                </div>
                                <span class="stage-units">School overview</span>
                            </article>
                        </div>
                        <p class="portal-disclaimer">Student records and grade changes remain subject to role permissions and official
                            college records.</p>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection