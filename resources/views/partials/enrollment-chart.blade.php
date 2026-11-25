@php
    $yearCounts = [];

    foreach ($enrollment as $year => $count) {
        $yearCounts[(int) $year] = (int) $count;
    }

    if (count($yearCounts) === 1) {
        $year = (int) array_key_first($yearCounts);
        $yearCounts = [
            $year - 1 => 0,
            $year => $yearCounts[$year],
            $year + 1 => 0,
        ];
    } elseif (count($yearCounts) > 1) {
        $firstYear = min(array_keys($yearCounts));
        $lastYear = max(array_keys($yearCounts));
        $completeYearCounts = [];

        for ($year = $firstYear; $year <= $lastYear; $year++) {
            $completeYearCounts[$year] = $yearCounts[$year] ?? 0;
        }

        $yearCounts = $completeYearCounts;
    }

    $yearCount = count($yearCounts);
    $maximumEnrollment = max($yearCounts ?: [0]);
    $axisMaximum = max((int) ceil($maximumEnrollment / 4) * 4, 4);
    $chartPoints = [];

    foreach ($yearCounts as $year => $count) {
        $index = count($chartPoints);
        $chartPoints[] = [
            'year' => (string) $year,
            'count' => $count,
            'x' => $yearCount > 1 ? 90 + (int) round($index * 890 / ($yearCount - 1)) : 535,
            'y' => 260 - (int) round(($count / $axisMaximum) * 190),
        ];
    }

    $chartLinePath = collect($chartPoints)
        ->map(fn (array $point, int $index): string => ($index === 0 ? 'M ' : 'L ').$point['x'].' '.$point['y'])
        ->implode(' ');

    $chartAreaPath = '';

    if ($yearCount > 1) {
        $firstPoint = $chartPoints[0];
        $lastPoint = $chartPoints[$yearCount - 1];
        $chartAreaPath = $chartLinePath.' L '.$lastPoint['x'].' 260 L '.$firstPoint['x'].' 260 Z';
    }
@endphp

<div class="line-chart enrollment-line-chart">
    <svg
        class="enrollment-line-chart-svg"
        viewBox="0 0 1000 300"
        preserveAspectRatio="none"
        role="img"
        aria-labelledby="enrollment-chart-title enrollment-chart-description"
    >
        <title id="enrollment-chart-title">Students by entry year</title>
        <desc id="enrollment-chart-description">
            @forelse ($chartPoints as $point)
                {{ $point['year'] }}: {{ $point['count'] }} {{ \Illuminate\Support\Str::plural('student', $point['count']) }}.
            @empty
                No enrollment data yet.
            @endforelse
        </desc>
        <defs>
            <linearGradient id="enrollment-area-fill" x1="0" x2="0" y1="0" y2="1">
                <stop offset="0%" stop-color="#00d6c1" stop-opacity=".24" />
                <stop offset="100%" stop-color="#00d6c1" stop-opacity=".015" />
            </linearGradient>
        </defs>

        @for ($gridIndex = 0; $gridIndex <= 4; $gridIndex++)
            @php
                $gridY = 55 + ($gridIndex * 51);
                $axisValue = (int) round($axisMaximum * (4 - $gridIndex) / 4);
            @endphp
            <line class="enrollment-grid-line" x1="82" y1="{{ $gridY }}" x2="990" y2="{{ $gridY }}" />
            <text class="enrollment-axis-label" x="68" y="{{ $gridY + 5 }}" text-anchor="end">{{ $axisValue }}</text>
        @endfor

        @if ($yearCount > 1)
            <path class="enrollment-area" d="{{ $chartAreaPath }}" />
        @endif

        @if ($yearCount > 0)
            <path class="enrollment-trend-line" d="{{ $chartLinePath }}" pathLength="1" />

            @foreach ($chartPoints as $point)
                <g class="enrollment-data-point">
                    <text class="enrollment-value-label" x="{{ $point['x'] }}" y="{{ $point['y'] - 14 }}" text-anchor="middle">
                        {{ $point['count'] }}
                    </text>
                    <circle class="enrollment-point-marker" cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" r="8" />
                    <text class="enrollment-year-label" x="{{ $point['x'] }}" y="290" text-anchor="middle">
                        {{ $point['year'] }}
                    </text>
                </g>
            @endforeach
        @else
            <text class="enrollment-empty-label" x="540" y="165" text-anchor="middle">No enrollment data yet</text>
        @endif
    </svg>
</div>
