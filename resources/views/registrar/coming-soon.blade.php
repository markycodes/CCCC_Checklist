@extends('layouts.portal')

@section('title', $feature)
@section('description', $feature.' workspace for the CCCC registrar.')

@section('content')
<main class="admin-dashboard analytics-dashboard">
    <div class="analytics-shell">
        <div class="analytics-content">
            <section class="admin-heading">
                <div>
                    <p class="portal-kicker"><span></span> REGISTRAR WORKSPACE</p>
                    <h1>{{ $feature }}</h1>
                    <p>This workspace is being prepared for the registrar team.</p>
                </div>
                <a class="portal-primary-button" href="{{ route('registrar.dashboard') }}">Back to registrar overview</a>
            </section>

            <section class="registrar-coming-soon" aria-labelledby="registrar-coming-soon-title">
                <span class="registrar-coming-soon-status">IN DEVELOPMENT</span>
                <h2 id="registrar-coming-soon-title">Coming soon</h2>
                <p>{{ $description }}</p>
            </section>
        </div>
    </div>
</main>
@endsection
