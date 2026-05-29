<x-layouts.app page-title="Dashboard" page-subtitle="Overview workspace dan status integrasi AI.">
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <x-stat-card label="Total History" :value="$totalGenerations" icon="history">
            <x-slot:meta>Semua paket SEO yang tersimpan.</x-slot:meta>
        </x-stat-card>
        <x-stat-card label="Latest Generation" :value="$latest?->created_at?->diffForHumans() ?? 'Empty'" icon="clock-3" tone="sky">
            <x-slot:meta>{{ $latest?->video_title ?? 'Belum ada hasil generate.' }}</x-slot:meta>
        </x-stat-card>
        <x-stat-card label="OpenAI API" :value="$apiReady ? 'Ready' : 'Missing'" icon="bolt" tone="emerald">
            <x-slot:meta>{{ $apiReady ? 'API key tersimpan.' : 'Tambahkan API key di Settings.' }}</x-slot:meta>
        </x-stat-card>
        <x-stat-card label="Active Model" :value="$settings->ai_model" icon="sparkles" tone="indigo">
            <x-slot:meta>Dipakai untuk generate berikutnya.</x-slot:meta>
        </x-stat-card>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-[1.2fr_.8fr]">
        <section class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-950">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wide text-blue-600 dark:text-blue-300">Shortcut</p>
                    <h2 class="mt-2 text-2xl font-semibold tracking-tight text-zinc-950 dark:text-white">Generate SEO YouTube baru</h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-zinc-600 dark:text-zinc-400">Masukkan judul dan deskripsi singkat, lalu biarkan OpenAI membuat title, description, tags, hashtags, dan pin comment yang siap dipakai.</p>
                </div>
                <a href="{{ route('seo.create') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-sm shadow-blue-600/30 transition hover:bg-blue-500">
                    <i data-lucide="sparkles" class="h-4 w-4"></i>
                    Generate SEO
                </a>
            </div>
        </section>

        <section class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-950">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Latest Output</h2>
            @if($latest)
                <p class="mt-3 text-lg font-semibold text-zinc-950 dark:text-white">{{ $latest->video_title }}</p>
                <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">{{ $latest->ai_model }} • {{ $latest->created_at->format('M d, Y H:i') }}</p>
                <a href="{{ route('history.show', $latest) }}" class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-blue-600 hover:text-blue-500 dark:text-blue-300">
                    View detail
                    <span aria-hidden="true">→</span>
                </a>
            @else
                <div class="mt-6 rounded-xl border border-dashed border-zinc-300 p-6 text-center dark:border-zinc-700">
                    <i data-lucide="file-text" class="mx-auto h-8 w-8 text-zinc-400"></i>
                    <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400">Belum ada generation history.</p>
                </div>
            @endif
        </section>
    </div>
</x-layouts.app>
