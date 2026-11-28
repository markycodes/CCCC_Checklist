<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class RegistrarComingSoonTest extends TestCase
{
    use RefreshDatabase;

    #[DataProvider('registrarFeatures')]
    public function test_registrar_can_open_coming_soon_feature_pages(
        string $routeName,
        string $feature,
        string $navigationLabel,
    ): void {
        $registrar = User::factory()->registrar()->create();

        $this->actingAs($registrar)
            ->get(route($routeName))
            ->assertOk()
            ->assertViewIs('registrar.coming-soon')
            ->assertViewHas('feature', $feature)
            ->assertSee($feature)
            ->assertSee('Coming soon')
            ->assertSee(route($routeName), false)
            ->assertSee('aria-current="page"', false);
    }

    public function test_registrar_feature_navigation_links_to_each_coming_soon_page(): void
    {
        $registrar = User::factory()->registrar()->create();

        $response = $this->actingAs($registrar)
            ->get(route('registrar.enrollment'))
            ->assertOk();

        foreach (self::registrarFeatures() as [$routeName, , $navigationLabel]) {
            $response
                ->assertSee('href="'.route($routeName).'"', false)
                ->assertSee($navigationLabel);
        }
    }

    public function test_guest_is_redirected_to_login_from_a_registrar_feature_page(): void
    {
        $this->get(route('registrar.enrollment'))
            ->assertRedirectToRoute('login');
    }

    #[DataProvider('nonRegistrarUsers')]
    public function test_non_registrar_roles_cannot_open_registrar_feature_pages(string $role): void
    {
        $user = match ($role) {
            'administrator' => User::factory()->administrator()->create(),
            'program-head' => User::factory()->programHead()->create(),
            'student' => User::factory()->create(),
        };

        $this->actingAs($user)
            ->get(route('registrar.enrollment'))
            ->assertForbidden();
    }

    public static function registrarFeatures(): array
    {
        return [
            'enrollment' => ['registrar.enrollment', 'Enrollment management', 'Enrollment'],
            'grades' => ['registrar.grades', 'Grade management', 'Grade management'],
            'transcripts' => ['registrar.transcript-requests', 'Transcript requests', 'Transcript requests'],
            'academic calendar' => ['registrar.academic-calendar', 'Academic calendar', 'Academic calendar'],
            'reports' => ['registrar.reports', 'Registrar reports', 'Reports'],
            'settings' => ['registrar.settings', 'Registrar settings', 'Settings'],
        ];
    }

    public static function nonRegistrarUsers(): array
    {
        return [
            'administrator' => ['administrator'],
            'program head' => ['program-head'],
            'student' => ['student'],
        ];
    }
}
