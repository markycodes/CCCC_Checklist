@extends('layouts.portal')

@section('title', 'Choose a new password')
@section('description', 'Set a new password for your authorized CCCC staff account.')

@section('content')
    <main class="login-page login-backdrop-page">
        <section class="login-intro">
            <p class="portal-kicker"><span></span> ACCOUNT RECOVERY</p>
            <h1>Choose a new<br>password</h1>
            <p class="login-intro-copy">Create a new password to securely return to your CCCC staff account.</p>
            <div class="login-detail">
                <span class="login-detail-mark" aria-hidden="true">SAFE</span>
                <div><strong>Use at least 12 characters</strong></div>
            </div>
        </section>

        <section class="login-card" aria-labelledby="new-password-title">
            <div class="login-card-heading">
                <span class="login-card-label">ACCOUNT RECOVERY</span>
                <h2 id="new-password-title">Set a new password</h2>
                <p>Choose a password you have not used for this account.</p>
            </div>

            <form method="POST" action="{{ route('password.update') }}" class="login-form">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <label for="email">College email</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email', $email) }}"
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

                <label for="password">New password</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    autocomplete="new-password"
                    minlength="12"
                    maxlength="4096"
                    required
                    aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                    @if ($errors->has('password')) aria-describedby="password-error" @endif
                >
                @error('password')
                    <p id="password-error" class="login-error">{{ $message }}</p>
                @enderror

                <label for="password_confirmation">Confirm new password</label>
                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    autocomplete="new-password"
                    minlength="12"
                    maxlength="4096"
                    required
                >
                <button class="portal-primary-button login-submit" type="submit">Reset password</button>
            </form>

            <p class="login-help"><a href="{{ route('login') }}">Back to sign in</a></p>
        </section>
    </main>
@endsection
