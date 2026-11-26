@php $f = $cfg['font']; $sp = $cfg['spacing']; @endphp
@font-face {
    font-family: 'Tahoma';
    src: url('{{ $forPdf ? storage_path('fonts/tahoma.ttf') : asset('fonts/tahoma.ttf') }}') format('truetype');
    font-weight: normal;
    font-style: normal;
}
@font-face {
    font-family: 'Tahoma';
    src: url('{{ $forPdf ? storage_path('fonts/tahomabd.ttf') : asset('fonts/tahomabd.ttf') }}') format('truetype');
    font-weight: bold;
    font-style: normal;
}
@font-face {
    font-family: 'Arial Narrow';
    src: url('{{ $forPdf ? storage_path('fonts/arial-narrow.ttf') : asset('fonts/arial-narrow.ttf') }}') format('truetype');
    font-weight: normal;
    font-style: normal;
}
@font-face {
    font-family: 'Arial Narrow';
    src: url('{{ $forPdf ? storage_path('fonts/arial-narrow-bold.ttf') : asset('fonts/arial-narrow-bold.ttf') }}') format('truetype');
    font-weight: bold;
    font-style: normal;
}
@page { size: 8.5in 13in; margin: 0.25in 0.35in; }
* { box-sizing: border-box; }
html, body { margin: 0; padding: 0; }
body { font-family: {!! $cfg['font_family'] !!}; font-size: 10pt; line-height: {{ $sp['line_height'] }}; color: #000; }
.sheet { width: 7.8in; margin: 0 auto; background: #fff; }
.b { font-weight: bold; }
.page-break { page-break-before: always; padding-top: 18pt; }

.t-republic { font-size: {{ $f['republic'] }}pt; }
.t-college  { font-size: {{ $f['college'] }}pt; font-weight: bold; }
.t-program  { font-size: {{ $f['program'] }}pt; font-weight: bold; margin-top: 7pt; }
.t-ref      { font-size: {{ $f['ref'] }}pt; }
.t-year     { font-size: {{ $f['year'] }}pt; font-weight: bold; text-decoration: underline; background: #ffff00; padding: 0 3pt; }
.t-sem      { font-size: {{ $f['sem'] }}pt; font-weight: bold; }
.t-colhead th { font-size: {{ $f['colhead'] }}pt; font-weight: bold; text-align: left; vertical-align: top; padding: 0 2pt; }
.t-colhead .reg { font-weight: normal; }
.t-row td   { font-size: {{ $f['row'] }}pt; vertical-align: top; padding: {{ $sp['row_pad'] }}pt 2pt; }
.t-total td { font-size: {{ $f['total'] }}pt; font-weight: bold; padding: 1pt 2pt 0 2pt; }
.t-total .lbl { text-align: right; padding-right: 10pt; }
.evaluation-row { width: auto; margin: 0 0 0 auto; border-collapse: collapse; }
.evaluation-row td { font-size: {{ $f['eval'] }}pt; padding: 3pt 0 0; vertical-align: bottom; white-space: nowrap; }
.evaluation-row .eval-line { display: block; width: 95pt; height: 9pt; border-bottom-color: #000; }

table.hdr { width: 100%; margin-top: 12pt; border-collapse: collapse; table-layout: fixed; }
.hdr td { vertical-align: top; padding: 0; }
.hdr td.hdr-l { width: 25%; padding-top: 38pt; padding-right: 6pt; color: #1f3a7a; font-family: 'Tahoma', sans-serif; }
.hdr td.hdr-c { width: 50%; text-align: center; }
.hdr td.hdr-r { width: 25%; padding-left: 6pt; padding-top: 20pt; }
.f-name { font-size: 9pt; font-weight: normal; margin-bottom: 7pt; }
.f-line { font-size: 9pt; font-weight: normal; margin-bottom: 7pt; }
.f-note { font-size: 8pt; font-weight: normal; margin-bottom: 7pt; line-height: 1.3; }
.student-field-value { display: inline-block; border-bottom: 0.75pt solid #1f3a7a; line-height: 1.05; }
.student-name { font-weight: bold; }
.f-note span { white-space: nowrap; }
.line { display: inline-block; border-bottom: 0.75pt solid #1f3a7a; height: 9pt; vertical-align: baseline; }
.logos-t { border-collapse: collapse; margin: 0 auto 2pt auto; }
.logos-t td { padding: 0 2pt; vertical-align: top; }
.logo-img { height: 34pt; }
.copy-box { border: 1.2pt solid #000; text-align: center; color: #e60000; font-size: 13pt; font-weight: bold; padding: 5pt 0; margin: 4pt 0 22pt 0; }
.grade-box { border: 0.75pt solid #000; }
.gb-head { background: #ff6600; color: #fff; font-weight: bold; font-size: 9pt; text-align: center; padding: 1pt 0; }
table.gb { width: 100%; border-collapse: collapse; }
.gb td { font-size: 6.5pt; font-weight: bold; padding: 0.5pt 2pt; text-align: center; }
.gb-foot { font-size: 6pt; text-align: center; padding: 0 2pt; line-height: 1.25; }

.year { text-align: center; margin: 6pt 0 4pt 0; }
.sem-block { margin-bottom: {{ $sp['sem_gap'] }}pt; padding-left: 0.15in; padding-right: 0.05in; page-break-inside: avoid; }
table.ct { width: 100%; border-collapse: collapse; table-layout: fixed; }
.chk { display: inline-block; width: 14pt; border-bottom: 0.75pt solid #000; height: 8pt; margin-right: 2pt; vertical-align: baseline; }
.chk-grade { width: 21pt; font-size: 9pt; font-weight: bold; line-height: 1; text-align: center; }
.chk-failing { color: #dc2626; border-bottom-color: #dc2626; }
.t-row td:first-child { white-space: nowrap; }

.sum-title { font-family: 'Arial Narrow', sans-serif; font-size: 11pt; font-weight: bold; text-align: center; border-top: 1.2pt solid #000; margin-top: 16pt; padding-top: 1pt; }
.pdf-summary-title { margin-top: 72pt; }
table.sum { width: 100%; border-collapse: collapse; table-layout: fixed; }
.s1 { width: 27%; } .s2 { width: 65%; } .s3 { width: 8%; }
.sum td { border: 0.75pt solid #000; font-family: 'Arial Narrow', sans-serif; font-size: 8pt; padding: 1pt 3pt; vertical-align: middle; }
.sum .sh td { font-weight: bold; }
.sum .summary-primary-head td:first-child { font-size: 10pt; }
.sum .summary-primary-head .mid { font-size: 11pt; }
.sum .u { text-align: center; font-weight: bold; }
.sum .r { text-align: right; }
