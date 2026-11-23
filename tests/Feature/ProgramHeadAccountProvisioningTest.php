<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProgramHeadAccountProvisioningTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrator_can_create_a_program_head_account_from_the_console(): void
    {
        $this->artisan('program-head:create')
            ->expectsQuestion('Program Head full name', 'Patricia Reyes')
            ->expectsQuestion('College email address', 'PReyes@CCCC.edu.ph')
            ->expectsQuestion('Temporary password (at least 12 characters)', 'CampusAccess2026!')
            ->expectsQuestion('Confirm temporary password', 'CampusAccess2026!')
            ->expectsOutputToContain('Program Head account created for preyes@cccc.edu.ph.')
            ->assertExitCode(0);

        $programHead = User::query()->where('email', 'preyes@cccc.edu.ph')->firstOrFail();

        $this->assertSame(User::ROLE_PROGRAM_HEAD, $programHead->role);
        $this->assertTrue(Hash::check('CampusAccess2026!', $programHead->password));
    }

    public function test_console_rejects_a_program_head_password_that_is_too_short(): void
    {
        $this->artisan('program-head:create')
            ->expectsQuestion('Program Head full name', 'Patricia Reyes')
            ->expectsQuestion('College email address', 'preyes@cccc.edu.ph')
            ->expectsQuestion('Temporary password (at least 12 characters)', 'short')
            ->expectsOutputToContain('The password must contain at least 12 characters.')
            ->assertExitCode(1);

        $this->assertDatabaseCount('users', 0);
    }
}
