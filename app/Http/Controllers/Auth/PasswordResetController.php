<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Closure;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    private const RESET_LINK_MESSAGE = 'If an authorized staff account exists for this email address, a password reset link will be sent.';

    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $email = Str::lower(trim($validated['email']));
        $user = User::query()
            ->where('email', $email)
            ->whereIn('role', [
                User::ROLE_PROGRAM_HEAD,
                User::ROLE_ADMINISTRATOR,
                User::ROLE_REGISTRAR,
            ])
            ->first();

        if ($user !== null) {
            Password::sendResetLink(['email' => $user->email]);
        }

        return redirect()
            ->route('password.request')
            ->with('status', self::RESET_LINK_MESSAGE);
    }

    public function edit(Request $request, string $token): View
    {
        $email = $request->query('email');

        return view('auth.reset-password', [
            'email' => is_string($email) ? $email : '',
            'token' => $token,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => [
                'required',
                'string',
                'min:12',
                'max:4096',
                'confirmed',
                function (string $attribute, mixed $value, Closure $fail): void {
                    if (is_string($value) && preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', $value)) {
                        $fail('The password contains unsupported control characters.');
                    }
                },
            ],
            'password_confirmation' => ['required', 'string', 'max:4096'],
        ]);

        $status = Password::reset(
            [
                ...$validated,
                'email' => Str::lower(trim($validated['email'])),
            ],
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => $password,
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            },
        );

        if ($status === Password::PasswordReset) {
            return redirect()
                ->route('login')
                ->with('status', 'Your password has been reset. You can now sign in.');
        }

        return back()
            ->withInput(['email' => $validated['email']])
            ->withErrors([
                'email' => 'This password reset link is invalid or expired. Request a new link.',
            ]);
    }
}
