@props(['titles' => [], 'scores' => []])

@php
    $scoreMap = collect($scores ?? [])->keyBy('title');
    $scoreTone = function ($score) {
        return match (true) {
            $score >= 85 => 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-200',
            $score >= 70 => 'border-blue-200 bg-blue-50 text-blue-700 dark:border-blue-500/30 dark:bg-blue-500/10 dark:text-blue-200',
            $score >= 55 => 'border-amber-200 bg-amber-50 text-amber-700 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-200',
            default => 'border-red-200 bg-red-50 text-red-700 dark:border-red-500/30 dark:bg-red-500/10 dark:text-red-200',
        };
    };
    $metrics = [
        'ctr_potential' => 'CTR',
        'keyword_clarity' => 'Keyword',
        'emotional_hook' => 'Hook',
        'length_score' => 'Length',
        'capslock_balance' => 'Caps',
        'tone_safety' => 'Tone',
    ];
@endphp

<div class="space-y-3">
    @foreach($titles as $index => $title)
        @php
            $score = $scoreMap->get($title) ?? ($scores[$index] ?? null);
            $overall = (int) ($score['overall_score'] ?? 0);
        @endphp

        <article class="rounded-xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <p class="text-sm font-semibold leading-6 text-zinc-900 dark:text-zinc-100">{{ $title }}</p>
                @if($score)
                    <span class="inline-flex shrink-0 items-center justify-center rounded-full border px-3 py-1 text-xs font-bold {{ $scoreTone($overall) }}">
                        {{ $overall }}/100
                    </span>
                @endif
            </div>

            @if($score)
                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    @foreach($metrics as $key => $label)
                        @php $metricScore = (int) ($score[$key] ?? 0); @endphp
                        <div>
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">{{ $label }}</span>
                                <span class="text-xs font-semibold text-zinc-700 dark:text-zinc-200">{{ $metricScore }}</span>
                            </div>
                            <div class="mt-1 h-1.5 overflow-hidden rounded-full bg-zinc-200 dark:bg-zinc-800">
                                <div class="h-full rounded-full bg-blue-600" style="width: {{ max(0, min(100, $metricScore)) }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <p class="mt-4 rounded-xl border border-zinc-200 bg-white p-3 text-xs leading-5 text-zinc-600 dark:border-zinc-800 dark:bg-zinc-950 dark:text-zinc-300">
                    {{ $score['recommendation'] ?? '' }}
                </p>
            @endif
        </article>
    @endforeach
</div>
