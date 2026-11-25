@php
    $totalProgress = array_sum($progress);
    $progressDenominator = max($totalProgress, 1);
    $rings = [
        [
            'key' => 'completed',
            'label' => 'Completed',
            'count' => (int) $progress['completed'],
            'percent' => (int) round(($progress['completed'] / $progressDenominator) * 100),
            'radius' => 56,
            'circumference' => 351.86,
        ],
        [
            'key' => 'progress',
            'label' => 'In progress',
            'count' => (int) $progress['in_progress'],
            'percent' => (int) round(($progress['in_progress'] / $progressDenominator) * 100),
            'radius' => 42,
            'circumference' => 263.89,
        ],
        [
            'key' => 'empty',
            'label' => 'Not started',
            'count' => (int) $progress['not_started'],
            'percent' => (int) round(($progress['not_started'] / $progressDenominator) * 100),
            'radius' => 28,
            'circumference' => 175.93,
        ],
    ];
@endphp

<div class="admin-pie-layout">
    <div
        class="progress-pie"
        role="img"
        aria-label="Student progress: {{ $rings[0]['count'] }} completed, {{ $rings[1]['count'] }} in progress, {{ $rings[2]['count'] }} not started"
    >
        <svg class="progress-radial-svg" viewBox="0 0 160 160" aria-hidden="true">
            <g transform="rotate(-90 80 80)">
                @foreach ($rings as $ring)
                    <circle class="progress-radial-track" cx="80" cy="80" r="{{ $ring['radius'] }}" />
                    <circle
                        class="progress-radial-fill radial-{{ $ring['key'] }}"
                        cx="80"
                        cy="80"
                        r="{{ $ring['radius'] }}"
                        stroke-dasharray="{{ $ring['circumference'] }}"
                        style="--ring-circumference: {{ $ring['circumference'] }}; --ring-offset: {{ round($ring['circumference'] * (1 - $ring['percent'] / 100), 2) }};"
                    />
                @endforeach
            </g>
        </svg>
        <span>{{ $totalProgress }}<small>STUDENTS</small></span>
    </div>

    <div class="chart-legend">
        @foreach ($rings as $ring)
            <span>
                <i class="legend-{{ $ring['key'] }}"></i>
                <b>{{ $ring['label'] }}</b>
                <strong>{{ $ring['count'] }}</strong>
            </span>
        @endforeach
    </div>
</div>
