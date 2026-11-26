<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProspectusControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_registrar_can_preview_prospectus_from_student_checklist(): void
    {
        $programHead = User::factory()->programHead()->create();
        $registrar = User::factory()->registrar()->create();
        $student = $programHead->students()->create([
            'student_id' => 'CCCC-PROSPECTUS-001',
            'name' => 'Prospectus Student',
            'entry_year' => 2024,
            'expected_graduation_year' => 2028,
            'previous_school' => 'Samar State University',
            'last_year_attended' => '2023-2024',
        ]);
        $student->completedCourses()->create([
            'course_code' => 'GED 1003',
            'grade' => 2.25,
        ]);
        $student->completedCourses()->create([
            'course_code' => 'GED 1006',
            'grade' => 0.00,
        ]);
        $student->completedCourses()->create([
            'course_code' => 'PATHFit 2103',
            'grade' => 3.25,
        ]);

        $this->actingAs($registrar)
            ->get(route('students.show', $student))
            ->assertOk()
            ->assertSee('Preview Prospectus')
            ->assertDontSee('prospectus-template-picker')
            ->assertSee('id="prospectus-modal"', false)
            ->assertDontSee('prospectus-modal-download', false)
            ->assertDontSee('print-prospectus-modal', false)
            ->assertDontSee('id="prospectus-modal-title"', false)
            ->assertSee('id="close-prospectus-modal"', false)
            ->assertSee(route('students.prospectus.stream', $student), false);

        $this->get(route('prospectus.show'))
            ->assertOk()
            ->assertSee('GRADING SYSTEM')
            ->assertSee('FIRST YEAR')
            ->assertSee('class="evaluation-row"', false);

        $preview = $this->get(route('students.prospectus.show', [$student, 'modal' => 1]))
            ->assertOk()
            ->assertSee('class="embedded-preview"', false)
            ->assertDontSee('Modal Preview')
            ->assertSee('GRADING SYSTEM')
            ->assertSee('class="evaluation-row"', false)
            ->assertSee('Prospectus Student')
            ->assertSee('CCCC-PROSPECTUS-001')
            ->assertSee('2024 - 2028')
            ->assertSee('Samar State University')
            ->assertSee('Last year attended: 2023-2024')
            ->assertSee('<span class="chk chk-grade">2.25</span>', false)
            ->assertSee('<span class="chk chk-grade chk-failing">3.25</span>PATHFit 2103', false)
            ->assertSee('<span class="chk"></span>GED 1006', false);

        $this->assertSame(3, substr_count($preview->getContent(), 'class="student-field-value'));
        $preview->assertSee('class="student-field-value student-name"', false);
        $preview->assertSee('class="line" style="width:100%"', false);
    }

    public function test_bscrim_student_renders_the_bscrim_prospectus_template(): void
    {
        $programHead = User::factory()->programHead()->create(['program' => 'BSCRIM']);
        $registrar = User::factory()->registrar()->create();
        $student = $programHead->students()->create([
            'student_id' => 'CCCC-BSCRIM-001',
            'name' => 'BSCRIM Prospectus Student',
            'entry_year' => 2024,
            'expected_graduation_year' => 2028,
        ]);

        $this->actingAs($registrar)
            ->get(route('students.prospectus.show', [$student, 'modal' => 1]))
            ->assertOk()
            ->assertSee('Bachelor of Science in Criminology (BS Criminology)')
            ->assertSee('FIRST YEAR')
            ->assertSee('FOURTH YEAR')
            ->assertSee('PE DefTac 2204')
            ->assertSee('CBA 3201')
            ->assertSee('(3)')
            ->assertSee('540 Hours')
            ->assertSee(
                '<tr class="t-row"><td><span class="chk"></span>CRM 2203</td><td>Human Behavior and Victimology</td><td>3</td><td>0</td><td>3</td><td>CRM 2102</td></tr>',
                false,
            )
            ->assertSee(
                '<tr class="t-total"><td colspan="2" class="lbl">Total Units</td><td>6</td><td>0</td><td>6</td><td></td></tr>',
                false,
            )
            ->assertSee('Grand Total');
    }

    public function test_bscrim_prospectus_keeps_second_year_second_semester_on_page_one(): void
    {
        $programHead = User::factory()->programHead()->create(['program' => 'BSCRIM']);
        $registrar = User::factory()->registrar()->create();
        $student = $programHead->students()->create([
            'student_id' => 'CCCC-BSCRIM-PAGES',
            'name' => 'BSCRIM Page Layout Student',
            'entry_year' => 2024,
        ]);

        $pdf = $this->actingAs($registrar)
            ->get(route('students.prospectus.stream', $student))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf')
            ->getContent();

        preg_match_all('/\/Type\s*\/Page\b/', $pdf, $pages);

        $this->assertSame(2, count($pages[0]));
    }

    public function test_bsentrep_template_renders_reference_rows_and_track_totals(): void
    {
        $programHead = User::factory()->programHead()->create(['program' => 'BSENTREP']);
        $registrar = User::factory()->registrar()->create();
        $student = $programHead->students()->create([
            'student_id' => 'CCCC-BSENTREP-001',
            'name' => 'BSENTREP Prospectus Student',
            'entry_year' => 2024,
        ]);

        $this->actingAs($registrar)
            ->get(route('students.prospectus.show', [$student, 'modal' => 1]))
            ->assertOk()
            ->assertSee('Bachelor of Science in Entrepreneurship (BSENTREP)')
            ->assertSee('Special Track in Tourism and Hospitality Business')
            ->assertSee('DEAN’S COPY')
            ->assertSee('ABM 1001')
            ->assertSee(
                '<td>Business Mathematics<br><span class="track-label">(For Non-ABM track)</span></td>',
                false,
            )
            ->assertSee(
                '<tr class="t-row"><td><span class="chk"></span>ABM 1003</td><td>Fundamentals of Accounting 2<br><span class="track-label">(For Non-ABM track)</span></td><td>3</td><td>0</td><td>3</td><td>ABM 1002</td></tr>',
                false,
            )
            ->assertSee('PATHFit 1101,1202,1203')
            ->assertSee(
                '<tr class="t-total"><td colspan="2" class="lbl">Total Units <span class="track-label">(For NON-ABM track)</span></td><td>26</td><td>0</td><td>20</td><td></td></tr>',
                false,
            )
            ->assertSee('GRAND TOTAL (For NON-ABM Track)')
            ->assertSee('<u>138 Units</u>', false)
            ->assertSee('150 units')
            ->assertSee('GRAND TOTAL (For NON-ABM Track)');
    }

    public function test_bsentrep_prospectus_renders_as_two_pages(): void
    {
        $programHead = User::factory()->programHead()->create(['program' => 'BSENTREP']);
        $registrar = User::factory()->registrar()->create();
        $student = $programHead->students()->create([
            'student_id' => 'CCCC-BSENTREP-PAGES',
            'name' => 'BSENTREP Page Layout Student',
            'entry_year' => 2024,
        ]);

        $pdf = $this->actingAs($registrar)
            ->get(route('students.prospectus.stream', $student))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf')
            ->getContent();

        preg_match_all('/\/Type\s*\/Page\b/', $pdf, $pages);

        $this->assertSame(2, count($pages[0]));
    }

    public function test_bsoa_template_renders_reference_rows_and_underlined_grand_total(): void
    {
        $programHead = User::factory()->programHead()->create(['program' => 'BSOA']);
        $registrar = User::factory()->registrar()->create();
        $student = $programHead->students()->create([
            'student_id' => 'CCCC-BSOA-001',
            'name' => 'BSOA Prospectus Student',
            'entry_year' => 2024,
        ]);

        $this->actingAs($registrar)
            ->get(route('students.prospectus.show', [$student, 'modal' => 1]))
            ->assertOk()
            ->assertSee('Bachelor of Science in Office Administration (BSOA)')
            ->assertSee('STUDENT’S COPY')
            ->assertSee(
                '<tr class="t-colhead"><th style="width:18%">Course Code</th><th style="width:40%">Course Title</th><th style="width:8%">Lecture<br><span class="reg">(Hours)</span></th><th style="width:8%">Lab<br><span class="reg">(Hours)</span></th><th style="width:8%">Credit<br><span class="reg">(Units)</span></th><th style="width:18%">Pre-requisite<br><span class="reg">Co-requisite</span></th></tr>',
                false,
            )
            ->assertSee(
                '<tr class="t-row"><td><span class="chk"></span>ABM 1003</td><td>Fundamentals of Accounting 2<br><span class="track-label">(For Non-ABM track)</span></td><td>3</td><td>0</td><td>3</td><td>ABM 1002</td></tr>',
                false,
            )
            ->assertSee(
                '<tr class="t-total"><td colspan="2" class="lbl">Total Units <span class="track-label">(For NON-ABM track)</span></td><td>26</td><td>0</td><td>20</td><td></td></tr>',
                false,
            )
            ->assertSee('<u>128 Units</u>', false)
            ->assertSee('140 units');
    }

    public function test_bsoa_prospectus_renders_as_two_pages(): void
    {
        $programHead = User::factory()->programHead()->create(['program' => 'BSOA']);
        $registrar = User::factory()->registrar()->create();
        $student = $programHead->students()->create([
            'student_id' => 'CCCC-BSOA-PAGES',
            'name' => 'BSOA Page Layout Student',
            'entry_year' => 2024,
        ]);

        $pdf = $this->actingAs($registrar)
            ->get(route('students.prospectus.stream', $student))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf')
            ->getContent();

        preg_match_all('/\/Type\s*\/Page\b/', $pdf, $pages);

        $this->assertSame(2, count($pages[0]));
    }

    public function test_bsism_template_renders_reference_rows_and_underlined_grand_total(): void
    {
        $programHead = User::factory()->programHead()->create(['program' => 'BSISM']);
        $registrar = User::factory()->registrar()->create();
        $student = $programHead->students()->create([
            'student_id' => 'CCCC-BSISM-001',
            'name' => 'BSISM Prospectus Student',
            'entry_year' => 2024,
        ]);

        $this->actingAs($registrar)
            ->get(route('students.prospectus.show', [$student, 'modal' => 1]))
            ->assertOk()
            ->assertSee('Bachelor of Science in Industrial Security Management (BSISM)')
            ->assertSee('DEAN’S COPY')
            ->assertSee(
                '<tr class="t-row"><td><span class="chk"></span>PE DEFTACT 2103</td><td>First Aid and Water Safety w/ Disaster Risk Reduction Management (DRRM)</td><td>2</td><td>0</td><td>2</td><td>PE DefTac 1201</td></tr>',
                false,
            )
            ->assertSee(
                '<tr class="t-row"><td><span class="chk"></span>ISP 4202</td><td>Practicum 1 (Private Institutions)</td><td>6</td><td>540 Hours</td><td>6</td><td>Junior Standing</td></tr>',
                false,
            )
            ->assertSee(
                '<tr class="t-total"><td colspan="2" class="lbl">Total Units</td><td>6</td><td>540</td><td>6</td><td></td></tr>',
                false,
            )
            ->assertSee('<u>178 units</u>', false)
            ->assertSee('MAJOR COURSES')
            ->assertSee('CLJ4206');
    }

    public function test_bsism_prospectus_renders_as_two_pages(): void
    {
        $programHead = User::factory()->programHead()->create(['program' => 'BSISM']);
        $registrar = User::factory()->registrar()->create();
        $student = $programHead->students()->create([
            'student_id' => 'CCCC-BSISM-PAGES',
            'name' => 'BSISM Page Layout Student',
            'entry_year' => 2024,
        ]);

        $pdf = $this->actingAs($registrar)
            ->get(route('students.prospectus.stream', $student))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf')
            ->assertHeader('content-disposition', 'inline; filename=BSISM-Prospectus-Deans-Copy.pdf')
            ->getContent();

        preg_match_all('/\/Type\s*\/Page\b/', $pdf, $pages);

        $this->assertSame(2, count($pages[0]));
    }

    public function test_registrar_and_administrator_see_the_program_heads_assigned_prospectus(): void
    {
        $programHead = User::factory()->programHead()->create(['program' => 'BSCRIM']);
        $student = $programHead->students()->create([
            'student_id' => 'CCCC-SHARED-TEMPLATE',
            'name' => 'Shared Template Student',
            'entry_year' => 2024,
        ]);

        foreach ([
            User::factory()->registrar()->create(),
            User::factory()->administrator()->create(),
        ] as $staff) {
            $this->actingAs($staff)
                ->get(route('students.show', $student))
                ->assertOk()
                ->assertSee('Preview Prospectus')
                ->assertDontSee('prospectus-template-picker');

            $this->get(route('students.prospectus.show', [
                $student,
                'modal' => 1,
                'template' => 'BSISM',
            ]))
                ->assertOk()
                ->assertSee('Bachelor of Science in Criminology (BS Criminology)')
                ->assertSee('CRM 1101')
                ->assertDontSee('Bachelor of Science in Industrial Security Management (BSISM)');
        }
    }

    public function test_administrators_and_registrars_can_preview_prospectus_but_program_heads_cannot(): void
    {
        $administrator = User::factory()->administrator()->create();
        $registrar = User::factory()->registrar()->create();
        $programHead = User::factory()->programHead()->create();
        $student = $programHead->students()->create([
            'student_id' => 'CCCC-PROSPECTUS-STAFF',
            'name' => 'Staff Prospectus Student',
            'entry_year' => 2024,
        ]);

        $this->actingAs($administrator)
            ->get(route('students.show', $student))
            ->assertOk()
            ->assertSee('Preview Prospectus');

        $this->actingAs($administrator)
            ->get(route('students.prospectus.show', [$student, 'modal' => 1]))
            ->assertOk()
            ->assertSee('GRADING SYSTEM')
            ->assertSee('Staff Prospectus Student');

        $this->actingAs($programHead)
            ->get(route('students.show', $student))
            ->assertOk()
            ->assertDontSee('Preview Prospectus');

        $this->actingAs($programHead)
            ->get(route('students.prospectus.show', [$student, 'modal' => 1]))
            ->assertForbidden();

        $this->actingAs($registrar)
            ->get(route('students.prospectus.show', [$student, 'modal' => 1]))
            ->assertOk()
            ->assertSee('GRADING SYSTEM')
            ->assertSee('Staff Prospectus Student');
    }

    public function test_registrar_can_download_prospectus_as_a_pdf(): void
    {
        $programHead = User::factory()->programHead()->create();
        $registrar = User::factory()->registrar()->create();
        $student = $programHead->students()->create([
            'student_id' => 'CCCC-PROSPECTUS-PDF',
            'name' => 'PDF Prospectus Student',
            'entry_year' => 2024,
        ]);

        $this->actingAs($registrar)
            ->get(route('students.prospectus.download', $student))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf')
            ->assertDownload('BPA-Prospectus-Students-Copy.pdf');

        $this->actingAs($registrar)
            ->get(route('students.prospectus.stream', $student))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_guests_and_student_accounts_cannot_preview_prospectus(): void
    {
        $this->get(route('prospectus.show'))
            ->assertRedirect(route('login'));

        $student = User::factory()->create();
        $programHead = User::factory()->programHead()->create();
        $record = $programHead->students()->create([
            'student_id' => 'CCCC-PROSPECTUS-STUDENT',
            'name' => 'Protected Prospectus Student',
            'entry_year' => 2024,
        ]);

        $this->actingAs($student)
            ->get(route('students.prospectus.show', [$record, 'modal' => 1]))
            ->assertForbidden();
    }

    public function test_program_head_cannot_preview_another_heads_prospectus(): void
    {
        $programHead = User::factory()->programHead()->create();
        $anotherProgramHead = User::factory()->programHead()->create();
        $student = $anotherProgramHead->students()->create([
            'student_id' => 'CCCC-PROSPECTUS-PRIVATE',
            'name' => 'Private Prospectus Student',
            'entry_year' => 2024,
        ]);

        $this->actingAs($programHead)
            ->get(route('students.prospectus.show', [$student, 'modal' => 1]))
            ->assertForbidden();
    }
}
