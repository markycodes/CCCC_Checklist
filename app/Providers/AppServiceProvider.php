<?php

namespace App\Providers;

use App\Models\Student;
use App\Policies\StudentPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->isProduction()) {
            URL::forceScheme('https');
        }

        Gate::policy(Student::class, StudentPolicy::class);

        RateLimiter::for('program-head-login', function (Request $request): Limit {
            $input = $request->input('email');
            $email = is_string($input) ? Str::lower(trim($input)) : '';

            return Limit::perMinute(5)
                ->by(Str::transliterate($email.'|'.$request->ip()));
        });
    }
}
