<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentCourseCompletion;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProspectusController extends Controller
{
    /** Browser preview (printable). */
    public function show(Request $request, ?Student $student = null)
    {
        return view('prospectus_templates.preview', $this->data(false, $student) + [
            'autoprint' => $request->boolean('print'),
        ]);
    }

    /** Download as PDF. */
    public function download(?Student $student = null)
    {
        return $this->pdf($student)->download(config('prospectus.filename'));
    }

    /** Open the PDF in the browser tab. */
    public function stream(?Student $student = null)
    {
        return $this->pdf($student)->stream(config('prospectus.filename'));
    }

    private function pdf(?Student $student = null)
    {
        return Pdf::loadView('prospectus_templates.pdf', $this->data(true, $student))
            ->setPaper(config('prospectus.paper'), 'portrait');
    }

    private function data(bool $forPdf, ?Student $student = null): array
    {
        $cfg = config('prospectus');

        $program = User::PROGRAM_BPA;
        if ($student?->programHead) {
            $program = Str::upper(trim((string) $student->programHead->program));
        } elseif (auth()->user()?->isProgramHead()) {
            $program = Str::upper(trim((string) auth()->user()->program));
        }

        if (! in_array($program, config('prospectus.templates', [User::PROGRAM_BPA]), true)) {
            $program = User::PROGRAM_BPA;
        }

        if ($student) {
            $this->authorize('view', $student);
            $student->loadMissing('completedCourses');

            $grades = $student->completedCourses->mapWithKeys(
                fn (StudentCourseCompletion $completion): array => [
                    $completion->course_code => $completion->grade && (float) $completion->grade > 0
                        ? number_format((float) $completion->grade, 2, '.', '')
                        : '',
                ],
            );

            foreach ($cfg['curriculum'] as &$year) {
                foreach ($year['semesters'] as &$semester) {
                    foreach ($semester['courses'] as &$course) {
                        $course[] = $grades->get($course[0], '');
                    }
                    unset($course);
                }
                unset($semester);
            }
            unset($year);
        }

        // DomPDF needs file paths; the browser needs URLs.
        // Logo height is 34pt; width follows each image's own proportions.
        $logos = array_map(function ($f) use ($forPdf) {
            $path = public_path('assets/img/'.$f);
            [$w, $h] = getimagesize($path);

            return [
                'src' => $forPdf ? $path : asset('assets/img/'.$f),
                'w' => round(34 * $w / $h, 1),
            ];
        }, $cfg['logos']);

        $grandTotal = 0;
        foreach ($cfg['curriculum'] as $year) {
            foreach ($year['semesters'] as $sem) {
                $grandTotal += array_sum(array_column($sem['courses'], 4));
            }
        }

        return [
            'cfg' => $cfg,
            'logos' => $logos,
            'grandTotal' => $grandTotal,
            'forPdf' => $forPdf,
            'student' => $student,
        ];
    }
}
