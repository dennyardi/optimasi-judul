<x-layouts.app page-title="Competitor Analysis" page-subtitle="Analisis channel YouTube kompetitor berdasarkan upload dan judul video.">
    <div class="grid gap-6 xl:grid-cols-[.85fr_1.15fr]">
        <section class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-950">
            <form method="POST" action="{{ route('competitors.store') }}" x-data="competitorAnalysisState" x-on:submit.prevent="submit" class="space-y-5">
                @csrf

                <div>
                    <label for="channel_input" class="text-sm font-medium text-zinc-800 dark:text-zinc-200">YouTube Channel</label>
                    <div class="mt-2 flex rounded-xl border border-zinc-200 bg-white shadow-sm focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500 dark:border-zinc-800 dark:bg-zinc-900">
                        <div class="flex items-center px-3 text-zinc-400">
                            <i data-lucide="search" class="h-4 w-4"></i>
                        </div>
                        <input id="channel_input" name="channel_input" value="{{ old('channel_input', $channelInput) }}" placeholder="URL channel, @handle, atau nama channel" required class="block min-w-0 flex-1 border-0 bg-transparent text-sm focus:ring-0 dark:text-white">
                    </div>
                    <x-field-error name="channel_input" />
                </div>

                <div>
                    <label for="range" class="text-sm font-medium text-zinc-800 dark:text-zinc-200">Range Waktu</label>
                    <select id="range" name="range" class="mt-2 block w-full rounded-xl border-zinc-200 bg-white text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white">
                        <option value="1_month" @selected(old('range', $range) === '1_month')>1 bulan terakhir</option>
                        <option value="3_months" @selected(old('range', $range) === '3_months')>3 bulan terakhir</option>
                        <option value="6_months" @selected(old('range', $range) === '6_months')>6 bulan terakhir</option>
                        <option value="1_year" @selected(old('range', $range) === '1_year')>1 tahun terakhir</option>
                    </select>
                    <x-field-error name="range" />
                </div>

                <button type="submit" :disabled="loading" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-sm shadow-blue-600/30 transition hover:bg-blue-500 disabled:cursor-not-allowed disabled:opacity-70">
                    <i data-lucide="loader-2" x-show="loading" x-cloak class="h-4 w-4 animate-spin"></i>
                    <i data-lucide="bar-chart-3" x-show="!loading" class="h-4 w-4"></i>
                    <span x-text="loading ? 'Analyzing...' : 'Analyze Competitor'">Analyze Competitor</span>
                </button>

                <div x-show="loading" x-cloak x-transition class="rounded-xl border border-blue-200 bg-blue-50/80 p-4 dark:border-blue-500/30 dark:bg-blue-500/10">
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-600 text-white">
                            <i data-lucide="loader-2" class="h-5 w-5 animate-spin"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-zinc-950 dark:text-white" x-text="activeStage.title">Reading channel</p>
                                    <p class="mt-1 text-sm leading-5 text-zinc-600 dark:text-zinc-300" x-text="activeStage.detail">Menganalisis channel.</p>
                                </div>
                                <span class="rounded-full border border-blue-200 bg-white px-2.5 py-1 text-xs font-semibold text-blue-700 dark:border-blue-500/30 dark:bg-zinc-950 dark:text-blue-200" x-text="progress + '%'">10%</span>
                            </div>
                            <div class="mt-4 h-2 overflow-hidden rounded-full bg-blue-100 dark:bg-blue-950">
                                <div class="h-full rounded-full bg-blue-600 transition-all duration-700 ease-out" :style="`width: ${progress}%`"></div>
                            </div>
                            <div class="mt-4 space-y-2">
                                <template x-for="(stage, index) in stages" :key="stage.title">
                                    <div class="flex items-center gap-3 rounded-xl px-2 py-1.5" :class="index === currentStage ? 'bg-white/80 dark:bg-zinc-950/70' : ''">
                                        <div class="flex h-7 w-7 items-center justify-center rounded-lg" :class="index < currentStage ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300' : index === currentStage ? 'bg-blue-600 text-white' : 'bg-zinc-100 text-zinc-400 dark:bg-zinc-900 dark:text-zinc-500'">
                                            <i :data-lucide="index < currentStage ? 'check-circle-2' : stage.icon" class="h-4 w-4"></i>
                                        </div>
                                        <span class="text-sm" :class="index <= currentStage ? 'font-medium text-zinc-800 dark:text-zinc-100' : 'text-zinc-500 dark:text-zinc-500'" x-text="stage.title"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                @unless($settings->hasYouTubeApiKey())
                    <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm leading-6 text-amber-800 dark:border-amber-900/60 dark:bg-amber-950 dark:text-amber-200">
                        YouTube Data API key belum disimpan. Isi di Settings agar analisis kompetitor bisa mengambil data channel.
                    </div>
                @endunless
            </form>

            <div class="mt-6 rounded-xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-800 dark:bg-zinc-900/60">
                <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Saran Data Tambahan</p>
                <p class="mt-2 text-sm leading-6 text-zinc-600 dark:text-zinc-300">Selain jumlah upload dan keyword judul, metrik paling berguna adalah average views, engagement rate, hari upload, frekuensi per minggu, top videos, dan pola judul video dengan views tertinggi.</p>
            </div>
        </section>

        <div class="space-y-5">
            @if($analysis)
                <section class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-950">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                        <div class="flex items-start gap-4">
                            @if($analysis['channel']['thumbnail'])
                                <img src="{{ $analysis['channel']['thumbnail'] }}" alt="" class="h-14 w-14 rounded-xl object-cover">
                            @endif
                            <div>
                                <p class="text-sm font-semibold uppercase tracking-wide text-blue-600 dark:text-blue-300">Channel Snapshot</p>
                                <h2 class="mt-1 text-2xl font-semibold tracking-tight text-zinc-950 dark:text-white">{{ $analysis['channel']['title'] }}</h2>
                                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ $analysis['range_label'] }} • sejak {{ $analysis['published_after'] }}</p>
                            </div>
                        </div>
                        <a href="{{ $analysis['channel']['url'] }}" target="_blank" rel="noreferrer" class="inline-flex items-center justify-center rounded-xl border border-zinc-200 px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-50 dark:border-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-900">Open Channel</a>
                    </div>
                </section>

                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <x-stat-card label="Uploaded Videos" :value="$analysis['summary']['uploaded_videos']" icon="file-text">
                        <x-slot:meta>{{ $analysis['summary']['uploads_per_week'] }} video/minggu</x-slot:meta>
                    </x-stat-card>
                    <x-stat-card label="Average Views" :value="number_format($analysis['summary']['average_views'])" icon="bar-chart-3" tone="sky">
                        <x-slot:meta>{{ number_format($analysis['summary']['total_views']) }} total views</x-slot:meta>
                    </x-stat-card>
                    <x-stat-card label="Top Views" :value="number_format($analysis['summary']['highest_views'])" icon="sparkles" tone="emerald">
                        <x-slot:meta>Video performa tertinggi</x-slot:meta>
                    </x-stat-card>
                    <x-stat-card label="Avg Engagement" :value="$analysis['summary']['average_engagement'].'%'" icon="bolt" tone="indigo">
                        <x-slot:meta>Like + comment / views</x-slot:meta>
                    </x-stat-card>
                </div>

                <section class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-950">
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Key Insights</h2>
                    <div class="mt-4 grid gap-3">
                        @foreach($analysis['insights'] as $insight)
                            <div class="rounded-xl border border-zinc-200 bg-zinc-50 p-4 text-sm leading-6 text-zinc-700 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-300">{{ $insight }}</div>
                        @endforeach
                    </div>
                </section>

                <div class="grid gap-5 xl:grid-cols-2">
                    <section class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-950">
                        <h2 class="text-sm font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Jam Upload Paling Sering</h2>
                        <div class="mt-4 space-y-3">
                            @forelse($analysis['upload_hours'] as $hour)
                                <div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="font-medium text-zinc-800 dark:text-zinc-100">{{ $hour['label'] }} WIB</span>
                                        <span class="text-zinc-500">{{ $hour['count'] }} video</span>
                                    </div>
                                    <div class="mt-2 h-2 rounded-full bg-zinc-100 dark:bg-zinc-900">
                                        <div class="h-2 rounded-full bg-blue-600" style="width: {{ min(100, $hour['count'] * 12) }}%"></div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-zinc-500">Belum ada data upload pada range ini.</p>
                            @endforelse
                        </div>
                    </section>

                    <section class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-950">
                        <h2 class="text-sm font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Keyword Judul Teratas</h2>
                        <div class="mt-4 flex flex-wrap gap-2">
                            @forelse($analysis['title_keywords'] as $keyword)
                                <span class="rounded-full border border-blue-200 bg-blue-50 px-3 py-1 text-sm font-medium text-blue-700 dark:border-blue-500/30 dark:bg-blue-500/10 dark:text-blue-200">{{ $keyword['keyword'] }} • {{ $keyword['count'] }}</span>
                            @empty
                                <p class="text-sm text-zinc-500">Belum ada keyword yang cukup sering muncul.</p>
                            @endforelse
                        </div>
                    </section>
                </div>

                <section class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-950">
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Top Performing Videos</h2>
                    <div class="mt-4 space-y-3">
                        @foreach($analysis['top_videos'] as $video)
                            <a href="{{ $video['url'] }}" target="_blank" rel="noreferrer" class="block rounded-xl border border-zinc-200 bg-zinc-50 p-4 transition hover:border-blue-300 hover:bg-blue-50 dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-blue-500/40 dark:hover:bg-blue-500/10">
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                    <div>
                                        <p class="text-sm font-semibold leading-6 text-zinc-900 dark:text-white">{{ $video['title'] }}</p>
                                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $video['published_at'] }}</p>
                                    </div>
                                    <div class="flex shrink-0 gap-2 text-xs font-medium text-zinc-500 dark:text-zinc-400">
                                        <span>{{ number_format($video['views']) }} views</span>
                                        <span>{{ $video['engagement_rate'] }}% ER</span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </section>
            @else
                <section class="rounded-xl border border-dashed border-zinc-300 bg-white p-10 text-center dark:border-zinc-700 dark:bg-zinc-950">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-300">
                        <i data-lucide="bar-chart-3" class="h-6 w-6"></i>
                    </div>
                    <h2 class="mt-4 text-lg font-semibold text-zinc-950 dark:text-white">Data kompetitor akan muncul di sini</h2>
                    <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Masukkan URL channel, handle, atau nama channel lalu pilih range waktu untuk melihat pola upload dan keyword judul.</p>
                </section>
            @endif
        </div>
    </div>
</x-layouts.app>
