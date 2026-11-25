<?php

namespace Tests\Feature\Http\Middleware;

use Tests\TestCase;

class EnsureHttpsTest extends TestCase
{
    public function test_production_http_request_redirects_to_the_same_https_url(): void
    {
        $this->app->detectEnvironment(static fn (): string => 'production');

        $this->get('/login?next=students')
            ->assertStatus(308)
            ->assertRedirect('https://localhost:8000/login?next=students');
    }

    public function test_production_https_request_is_served_without_redirect(): void
    {
        $this->app->detectEnvironment(static fn (): string => 'production');

        $this->get('https://localhost/login')
            ->assertOk();
    }

    public function test_production_request_from_a_trusted_https_proxy_is_not_redirected(): void
    {
        $this->app->detectEnvironment(static fn (): string => 'production');
        config()->set('trustedproxy.proxies', '127.0.0.1');

        $this->withServerVariables([
            'REMOTE_ADDR' => '127.0.0.1',
            'HTTP_X_FORWARDED_PROTO' => 'https',
        ])
            ->get('/login')
            ->assertOk();
    }

    public function test_local_http_request_is_not_redirected(): void
    {
        $this->app->detectEnvironment(static fn (): string => 'local');

        $this->get('/login')
            ->assertOk();
    }
}
