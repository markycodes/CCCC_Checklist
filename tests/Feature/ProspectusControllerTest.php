<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProspectusControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_program_head_can_preview_prospectus_from_student_checklist(): void
    {
        $programHead = User::factory()->programHead()->create();
        $student = $programHead->students()->create([
            'student_id' => 'CCCC-PROSPECTUS-001',
            'name' => 'Prospectus Student',
            'entry_year' => 2024,
        ]);

        $this->actingAs($programHead)
            ->get(route('students.show', $student))
            ->assertOk()
            ->assertSee('Preview Prospectus')
            ->assertSee('id="prospectus-modal"', false)
            ->assertSee('Download PDF')
            ->assertSee(route('prospectus.download'), false)
            ->assertSee('id="print-prospectus-modal"', false)
            ->assertSee('Print')
            ->assertSee(route('prospectus.show', ['modal' => 1]), false);

        $this->get(route('prospectus.show'))
            ->assertOk()
            ->assertSee('GRADING SYSTEM')
            ->assertSee('FIRST YEAR');

        $this->get(route('prospectus.show', ['modal' => 1]))
            ->assertOk()
            ->assertSee('class="embedded-preview"', false)
            ->assertDontSee('Modal Preview')
            ->assertSee('GRADING SYSTEM');
    }

    public function test_registrars_and_administrators_can_preview_prospectus(): void
    {
        $administrator = User::factory()->administrator()->create();
        $registrar = User::factory()->registrar()->create();

        $this->actingAs($administrator)
            ->get(route('prospectus.show'))
            ->assertOk()
            ->assertSee('GRADING SYSTEM');

        $this->actingAs($registrar)
            ->get(route('prospectus.show'))
            ->assertOk()
            ->assertSee('GRADING SYSTEM');
    }

    public function test_program_head_can_download_prospectus_as_a_pdf(): void
    {
        $programHead = User::factory()->programHead()->create();

        $this->actingAs($programHead)
            ->get(route('prospectus.download'))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_guests_and_student_accounts_cannot_preview_prospectus(): void
    {
        $this->get(route('prospectus.show'))
            ->assertRedirect(route('login'));

        $student = User::factory()->create();

        $this->actingAs($student)
            ->get(route('prospectus.show'))
            ->assertForbidden();
    }
}
