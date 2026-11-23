@extends('layouts.portal')

@section('title', 'Student Curriculum')
@section('description', 'The student curriculum and course checklist for Catbalogan City Community College.')

@section('content')
    <main>
        <section class="portal-hero landing-hero">
            <div class="portal-hero-copy landing-hero-copy">
                <p class="portal-kicker"><span></span> STUDENT COURSE CHECKLIST</p>
                <h1>Curriculum and<br>course checklist</h1>
                <p class="portal-hero-intro">An academic course guide for Catbalogan City Community College. Review a
                    curriculum by semester and keep track of completed subjects.</p>
                <div class="portal-hero-actions">
                    @auth
                        <a class="portal-primary-button" href="{{ route('students.index') }}"><span class="button-icon" aria-hidden="true">♙</span> Open student records</a>
                        <span class="portal-action-note">Signed in as {{ auth()->user()->name }}</span>
                    @else
                        <a class="portal-primary-button" href="{{ route('login') }}"><span class="button-icon" aria-hidden="true">↗</span> Program Head sign in</a>
                        <span class="portal-action-note">Access for authorized Program Heads</span>
                    @endauth
                </div>
            </div>

            <aside class="curriculum-card landing-hero-overview" aria-label="Curriculum overview">
                <div class="curriculum-card-heading">
                    <div>
                        <span class="curriculum-card-label">PROGRAM OVERVIEW</span>
                        <h2>At a glance</h2>
                    </div>
                  <img class="college-logo" src="{{ asset('assets/img/cccc.png') }}" width="45" height="47" alt="">
                </div>
                <div class="curriculum-facts">
                    <div><strong>4</strong><span>academic years</span></div>
                    <div><strong>8</strong><span>semesters</span></div>
                    <div><strong>146</strong><span>total units</span></div>
                </div>
                <div class="year-list">
                    <div class="year-list-heading"><span>CURRICULUM BY YEAR</span><span>UNITS</span></div>
                    <div class="year-list-row"><span><i>01</i> First year</span><strong>46</strong></div>
                    <div class="year-list-row"><span><i>02</i> Second year</span><strong>40</strong></div>
                    <div class="year-list-row"><span><i>03</i> Third year</span><strong>36</strong></div>
                    <div class="year-list-row"><span><i>04</i> Fourth year</span><strong>24</strong></div>
                </div>
                <p class="curriculum-card-note"><span aria-hidden="true">i</span> Use your official evaluation for
                    enrollment and advising decisions.</p>
            </aside>
        </section>

        <section id="curriculum" class="portal-details">
            <div class="portal-details-heading">
                <div>
                    <p class="portal-kicker"><span></span> REGISTRAR WORKFLOW</p>
                    <h2>Student records from enrollment to completion.</h2>
                </div>
                <p>Manage academic records, monitor enrollment, and keep each student checklist ready for Program Head
                    evaluation.</p>
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
        </section>
    </main>
@endsection