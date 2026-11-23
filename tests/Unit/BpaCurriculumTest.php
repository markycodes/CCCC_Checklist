<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class BpaCurriculumTest extends TestCase
{
    public function test_bpa_curriculum_matches_the_49_course_146_unit_total(): void
    {
        $curriculum = require __DIR__.'/../../config/bpa_curriculum.php';
        $courseCount = 0;
        $unitCount = 0;

        foreach ($curriculum['years'] as $year) {
            foreach ($year['semesters'] as $semester) {
                foreach ($semester['courses'] as $course) {
                    $courseCount++;
                    $unitCount += $course['units'];
                }
            }
        }

        $this->assertSame(49, $courseCount);
        $this->assertSame(146, $unitCount);
    }
}
