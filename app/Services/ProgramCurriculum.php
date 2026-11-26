<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;

class ProgramCurriculum
{
    /** @var array<string, array<string, mixed>> */
    private array $curricula = [];

    /**
     * @return array{
     *     program: string,
     *     name: string,
     *     years: list<array{
     *         name: string,
     *         focus?: string,
     *         semesters: list<array{
     *             name: string,
     *             courses: list<array{code: string, title: string, units: int}>
     *         }>
     *     }>,
     *     course_codes: list<string>,
     *     total_courses: int,
     *     total_units: int
     * }
     */
    public function forProgram(?string $program): array
    {
        $program = Str::upper(trim((string) $program));
        $templates = config('prospectus.templates', []);

        if (! array_key_exists($program, $templates)) {
            $program = User::PROGRAM_BPA;
        }

        if (isset($this->curricula[$program])) {
            return $this->curricula[$program];
        }

        $template = $templates[$program];
        $config = require $template['config'];
        $years = [];
        $courseCodes = [];
        $totalUnits = 0;

        foreach ($config['curriculum'] as $yearIndex => $year) {
            $semesters = [];

            foreach ($year['semesters'] as $semester) {
                $courses = [];

                foreach ($semester['courses'] as $course) {
                    $courseCode = (string) $course[0];
                    $units = (int) $course[4];
                    $courseCodes[] = $courseCode;
                    $totalUnits += $units;
                    $courses[] = [
                        'code' => $courseCode,
                        'title' => (string) $course[1],
                        'units' => $units,
                    ];
                }

                $semesters[] = [
                    'name' => ucfirst(Str::lower($semester['label'])),
                    'courses' => $courses,
                ];
            }

            $normalizedYear = [
                'name' => ucfirst(Str::lower($year['label'])),
                'semesters' => $semesters,
            ];
            $focus = $program === User::PROGRAM_BPA
                ? config("bpa_curriculum.years.{$yearIndex}.focus")
                : null;

            if (is_string($focus) && $focus !== '') {
                $normalizedYear['focus'] = $focus;
            }

            $years[] = $normalizedYear;
        }

        return $this->curricula[$program] = [
            'program' => $program,
            'name' => (string) $config['header']['program'],
            'years' => $years,
            'course_codes' => $courseCodes,
            'total_courses' => count($courseCodes),
            'total_units' => $totalUnits,
        ];
    }

    public function containsCourse(?string $program, string $courseCode): bool
    {
        return in_array($courseCode, $this->forProgram($program)['course_codes'], true);
    }
}
