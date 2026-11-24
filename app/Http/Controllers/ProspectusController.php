<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ProspectusController extends Controller
{
    /** Browser preview (printable). */
    public function show(Request $request)
    {
        return view('prospectus_templates.preview', $this->data(false) + [
            'autoprint' => $request->boolean('print'),
        ]);
    }

    /** Download as PDF. */
    public function download()
    {
        return $this->pdf()->download(config('prospectus.filename'));
    }

    /** Open the PDF in the browser tab. */
    public function stream()
    {
        return $this->pdf()->stream(config('prospectus.filename'));
    }

    private function pdf()
    {
        return Pdf::loadView('prospectus_templates.pdf', $this->data(true))
            ->setPaper(config('prospectus.paper'), 'portrait');
    }

    private function data(bool $forPdf): array
    {
        $cfg = config('prospectus');

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

        return ['cfg' => $cfg, 'logos' => $logos, 'grandTotal' => $grandTotal, 'forPdf' => $forPdf];
    }
}
