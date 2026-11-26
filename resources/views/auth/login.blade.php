@extends('layouts.portal')

@section('title', 'Program Head sign in')
@section('description', 'Sign in to the Catbalogan City Community College student course checklist.')

@section('content')
    <div class="landing-loader" role="status" aria-label="Loading Catbalogan City Community College sign-in page">
        <div class="landing-loader-progress landing-loader-progress-wordmark" aria-hidden="true">
            <span class="landing-loader-wordmark-base">CATBALOGAN CITY COMMUNITY COLLEGE</span>
            <span class="landing-loader-wordmark-fill">CATBALOGAN CITY COMMUNITY COLLEGE</span>
        </div>
        <img class="landing-loader-logo" src="{{ asset('assets/img/cccc.png') }}" alt="CCCC seal">
    </div>
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

            @if (session('status'))
                <p class="form-status" role="status">{{ session('status') }}</p>
            @endif

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
                    maxlength="255"
                    autocapitalize="none"
                    spellcheck="false"
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
                    <a class="login-forgot-password" href="{{ route('password.request') }}">Forgot password?</a>
                </div>
                <input
                    id="password"
                    name="password"
                    type="password"
                    autocomplete="current-password"
                    maxlength="4096"
                    autocapitalize="none"
                    spellcheck="false"
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
                @if (config('cloudflare-turnstile.enabled'))
                    <div
                        class="cf-turnstile login-turnstile"
                        data-sitekey="{{ config('cloudflare-turnstile.site_key') }}"
                        data-theme="light"
                        data-size="flexible"
                    ></div>
                    @error('cf-turnstile-response')
                        <p class="login-error" role="alert">{{ $message }}</p>
                    @enderror
                @endif
                <button class="portal-primary-button login-submit" type="submit"><span class="button-icon" aria-hidden="true">↗</span> Sign in</button>
            </form>

            <p class="login-help">Need access? Contact your system administrator to request a Program Head account.</p>
        </section>
    </main>
    @if (config('cloudflare-turnstile.enabled'))
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @endif
@endsection
