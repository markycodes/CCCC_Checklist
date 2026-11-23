@extends('layouts.portal')

@section('title', 'Program Head sign in')
@section('description', 'Sign in to the Catbalogan City Community College student course checklist.')

@section('content')
    <main class="login-page login-backdrop-page">
        <section class="login-intro ">
            <p class="portal-kicker"><span></span> STUDENT COURSE CHECKLIST</p>
            <h1>Program Head<br>sign in</h1>
            <p class="login-intro-copy">Sign in to review course checklists and keep student academic progress up to date.</p>
            <div class="login-detail">
                <span class="login-detail-mark" aria-hidden="true">2026</span>
                <div><strong>Official curriculum</strong></div>
            </div>
        </section>

        <section class="login-card" aria-labelledby="login-title">
            <div class="login-card-heading">
                <span class="login-card-label">PROGRAM HEAD PORTAL</span>
                <h2 id="login-title">Welcome back</h2>
                <p>Use the staff account provided by your school administrator.</p>
            </div>

            <form method="POST" action="{{ route('login.store') }}" class="login-form">
                @csrf
                <label for="email">College email</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    autocomplete="username"
                    inputmode="email"
                    required
                    autofocus
                    aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                    @if ($errors->has('email')) aria-describedby="email-error" @endif
                >
                @error('email')
                    <p id="email-error" class="login-error">{{ $message }}</p>
                @enderror

                <div class="password-label-row">
                    <label for="password">Password</label>
                    <span>Provided by your administrator</span>
                </div>
                <input
                    id="password"
                    name="password"
                    type="password"
                    autocomplete="current-password"
                    required
                    aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                    @if ($errors->has('password')) aria-describedby="password-error" @endif
                >
                @error('password')
                    <p id="password-error" class="login-error">{{ $message }}</p>
                @enderror

                <label class="remember-label">
                    <input type="checkbox" name="remember" value="1">
                    <span>Remember this device</span>
                </label>
                <button class="portal-primary-button login-submit" type="submit"><span class="button-icon" aria-hidden="true">↗</span> Sign in</button>
            </form>

            <p class="login-help">Need access? Contact your system administrator to request a Program Head account.</p>
        </section>
    </main>
@endsection
