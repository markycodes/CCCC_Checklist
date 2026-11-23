<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            'password' => ['required', 'string'],
        ]);
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
