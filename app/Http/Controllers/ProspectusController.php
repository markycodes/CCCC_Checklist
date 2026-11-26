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
        $data = $this->data(false, $student);

        return view($data['template']['view'].'.preview', $data + [
            'autoprint' => $request->boolean('print'),
        ]);
    }

    /** Download as PDF. */
    public function download(Request $request, ?Student $student = null)
    {
        $data = $this->data(true, $student);

        return $this->pdf($student, $data)->download($data['cfg']['filename']);
    }

    /** Open the PDF in the browser tab. */
    public function stream(Request $request, ?Student $student = null)
    {
        $data = $this->data(true, $student);

        return $this->pdf($student, $data)->stream($data['cfg']['filename']);
    }

    private function pdf(?Student $student = null, ?array $data = null)
    {
        $data ??= $this->data(true, $student);

        return Pdf::loadView($data['template']['view'].'.pdf', $data)
            ->setPaper($data['cfg']['paper'], 'portrait');
    }

    private function data(bool $forPdf, ?Student $student = null): array
    {
        $program = User::PROGRAM_BPA;
        if ($student?->programHead) {
            $program = Str::upper(trim((string) $student->programHead->program));
        } elseif (auth()->user()?->isProgramHead()) {
            $program = Str::upper(trim((string) auth()->user()->program));
        }

        $templates = config('prospectus.templates', []);
        if (! array_key_exists($program, $templates)) {
            $program = User::PROGRAM_BPA;
        }

        $template = $templates[$program];
        $cfg = require $template['config'];

        if ($student) {
            abort_unless(auth()->user()?->canViewAllPrograms(), 403);
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
            'template' => $template,
        ];
    }
}
