@extends('layouts.portal')

@section('title', 'Reset password')
@section('description', 'Request a password reset link for an authorized CCCC staff account.')

@section('content')
    <main class="login-page login-backdrop-page">
        <section class="login-intro">
            <p class="portal-kicker"><span></span> ACCOUNT RECOVERY</p>
            <h1>Reset your<br>password</h1>
            <p class="login-intro-copy">Enter the college email address linked to your authorized staff account. We will send a secure reset link if the account is eligible.</p>
            <div class="login-detail">
                <span class="login-detail-mark" aria-hidden="true">SAFE</span>
                <div><strong>Secure account recovery</strong></div>
            </div>
        </section>

        <section class="login-card" aria-labelledby="reset-request-title">
            <div class="login-card-heading">
                <span class="login-card-label">ACCOUNT RECOVERY</span>
                <h2 id="reset-request-title">Request a reset link</h2>
                <p>Reset links expire after 60 minutes.</p>
            </div>

            @if (session('status'))
                <p class="form-status" role="status">{{ session('status') }}</p>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="login-form">
                @csrf
                <label for="email">College email</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    autocomplete="email"
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
                <button class="portal-primary-button login-submit" type="submit">Send reset link</button>
            </form>

            <p class="login-help"><a href="{{ route('login') }}">Back to sign in</a></p>
        </section>
    </main>
@endsection
