<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProgramHeadLoginController extends Controller
{
    public function create(): View|RedirectResponse
    {
        if (Auth::user()?->isAdministrator()) {
            return redirect()->route('admin.dashboard');
        }
        if (Auth::user()?->isProgramHead()) {
            return redirect()->route('students.index');
        }
        if (Auth::user()?->isRegistrar()) {
            return redirect()->route('registrar.dashboard');
        }

        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => [
                'required',
                'string',
                'max:4096',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (is_string($value) && preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', $value)) {
                        $fail('The password contains unsupported control characters.');
                    }
                },
            ],
        ]);

        if (config('cloudflare-turnstile.enabled')) {
            $request->validate([
                'cf-turnstile-response' => ['required', 'string'],
            ], [
                'cf-turnstile-response.required' => 'Please complete the Cloudflare verification.',
            ]);

            try {
                $verification = Http::asForm()
                    ->connectTimeout(5)
                    ->timeout(10)
                    ->post(
                        'https://challenges.cloudflare.com/turnstile/v0/siteverify',
                        [
                            'secret' => config('cloudflare-turnstile.secret_key'),
                            'response' => $request->input('cf-turnstile-response'),
                            'remoteip' => $request->ip(),
                        ],
                    );
            } catch (ConnectionException) {
                throw ValidationException::withMessages([
                    'cf-turnstile-response' => 'Cloudflare verification is temporarily unavailable. Please try again.',
                ]);
            }

            if (! $verification->successful() || ! ($verification->json('success') ?? false)) {
                throw ValidationException::withMessages([
                    'cf-turnstile-response' => 'Cloudflare verification failed. Please try again.',
                ]);
            }
        }

        $credentials['email'] = Str::lower($credentials['email']);

        if (! Auth::attempt([
            ...$credentials,
        ], $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Those details do not match an active account.',
            ]);
        }

        $user = Auth::user();
        if (! $user?->isProgramHead() && ! $user?->isAdministrator() && ! $user?->isRegistrar()) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'Those details do not match an authorized staff account.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(
            $user->isAdministrator()
                ? route('admin.dashboard')
                : ($user->isRegistrar()
                    ? route('registrar.dashboard')
                    : route('students.index'))
        );
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
