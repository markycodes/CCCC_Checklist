@php $h = $cfg['header']; @endphp
<div class="sheet">
<table class="hdr"><tr>
<td class="hdr-l">
 <div class="f-name"><strong>Name:</strong> <span class="line" style="width:88pt"></span></div>
 <div class="f-line">Student ID No.: <span class="line" style="width:60pt"></span></div>
 <div class="f-line">Year Entry: 20<span class="line" style="width:18pt"></span> - 20<span class="line" style="width:18pt"></span></div>
 <div class="f-note"><span>if transferee, please write the previous</span><br><span>school and last year attended</span></div>
 <div class="f-line"><span class="line" style="width:100%"></span></div>
</td>
<td class="hdr-c">
 <table class="logos-t" align="center"><tr>@foreach ($logos as $logo)<td><img class="logo-img" src="{{ $logo['src'] }}" style="width:{{ $logo['w'] }}pt; height:34pt;" alt=""></td>@endforeach</tr></table>
 @foreach ($h['republic'] as $line)<div class="t-republic">{{ $line }}</div>@endforeach
 <div class="t-republic b">{{ $h['city'] }}</div>
 <div class="t-college">{{ $h['college'] }}</div>
 <div class="t-program">{{ $h['program'] }}</div>
 @foreach ($h['refs'] as $ref)<div class="t-ref">{{ $ref }}</div>@endforeach
</td>
<td class="hdr-r">
 <div class="copy-box">{{ $cfg['copy_label'] }}</div>
 <div class="grade-box"><div class="gb-head">GRADING SYSTEM</div>
 <table class="gb">
 @foreach ($cfg['grading'] as $row)
  <tr>@foreach ($row as $pair)<td>{{ $pair[0] }}</td><td>{{ $pair[1] }}</td><td>{{ $pair[2] }}</td>@endforeach</tr>
 @endforeach
 </table>
 <div class="gb-foot">{{ $cfg['grading_foot'] }}</div>
 <div class="gb-foot b">{{ $cfg['grading_res'] }}</div></div>
</td></tr></table>

@foreach ($cfg['curriculum'] as $year)
<div class="year {{ !empty($year['page_break_before']) ? 'page-break' : '' }}"><span class="t-year">{{ $year['label'] }}</span></div>
@foreach ($year['semesters'] as $sem)
@php
  $lec = array_sum(array_column($sem['courses'], 2));
  $lab = array_sum(array_column($sem['courses'], 3));
  $cr  = array_sum(array_column($sem['courses'], 4));
@endphp
<div class="sem-block"><div class="t-sem">{{ $sem['label'] }}</div>
<table class="ct">
<thead><tr class="t-colhead"><th style="width:{{ $cfg['cols'][0] }}">Course Code</th><th style="width:{{ $cfg['cols'][1] }}">Course Title</th><th style="width:{{ $cfg['cols'][2] }}">Lecture<br><span class="reg">(Hours)</span></th><th style="width:{{ $cfg['cols'][3] }}">Lab<br><span class="reg">(Hours)</span></th><th style="width:{{ $cfg['cols'][4] }}">Credit<br><span class="reg">(Units)</span></th><th style="width:{{ $cfg['cols'][5] }}">Pre-requisite<br><span class="reg">Co-requisite</span></th></tr></thead><tbody>
@foreach ($sem['courses'] as $c)
<tr class="t-row"><td><span class="chk"></span>{{ $c[0] }}</td><td>{{ $c[1] }}</td><td>{{ $c[2] }}</td><td>{{ $c[3] }}</td><td>{{ $c[4] }}</td><td>{{ $c[5] }}</td></tr>
@endforeach
<tr class="t-total"><td colspan="2" class="lbl">Total Units</td><td>{{ $lec }}</td><td>{{ $lab }}</td><td>{{ $cr }}</td><td></td></tr>
@if (!empty($sem['show_grand_total']))
<tr class="t-total"><td colspan="2" class="lbl">Grand Total</td><td colspan="2">{{ $grandTotal }} units</td><td colspan="2"></td></tr>
@endif
<tr class="t-eval"><td colspan="4"></td><td colspan="2">Evaluated by: <span class="line eval-line"></span></td></tr>
</tbody></table></div>
@endforeach
@endforeach

<div class="sum-title {{ $forPdf ? 'pdf-summary-title' : '' }}">SUMMARY OF SUBJECTS/UNITS</div>
<table class="sum">
@foreach ($cfg['summary'] as $r)
 @if ($r[0] === 'head')
  <tr class="sh {{ $loop->first ? 'summary-primary-head' : '' }}"><td style="width:27%">{{ $r[1] }}</td><td style="width:65%" class="{{ $r[2] !== '' ? 'mid' : '' }}">{{ $r[2] }}</td><td style="width:8%" class="u">{{ $r[3] }}</td></tr>
 @elseif ($r[0] === 'row')
  <tr><td>{{ $r[1] }}</td><td>{{ $r[2] }}</td><td class="u">{{ $r[3] }}</td></tr>
 @elseif ($r[0] === 'blank')
  <tr><td>&nbsp;</td><td></td><td></td></tr>
 @else
  <tr class="sh"><td></td><td class="r">{{ $r[2] }}</td><td class="u">{{ $r[3] }}</td></tr>
 @endif
@endforeach
</table>
</div>
