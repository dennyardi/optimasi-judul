<x-layouts.app page-title="History Detail" :page-subtitle="$generation->video_title">
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $generation->ai_model }} • {{ $generation->created_at->format('M d, Y H:i') }}</p>
        </div>
        <a href="{{ route('history.index') }}" class="inline-flex items-center justify-center rounded-xl border border-zinc-200 px-4 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-50 dark:border-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-900">Back to History</a>
    </div>

    <div class="grid gap-5 xl:grid-cols-2">
        <x-output-card title="SEO Titles" :copy="implode(PHP_EOL, $generation->generated_titles)">
            <x-title-score-list :titles="$generation->generated_titles" :scores="$generation->generated_title_scores ?? []" />
        </x-output-card>

        <x-output-card title="Top 10 Meta Tags" :copy="implode(', ', $generation->generated_tags)">
            <p class="text-sm leading-6 text-zinc-700 dark:text-zinc-300">{{ implode(', ', $generation->generated_tags) }}</p>
        </x-output-card>

        <x-output-card title="SEO Description" :copy="$generation->generated_description">
            <textarea readonly rows="10" class="block w-full resize-none rounded-xl border-zinc-200 bg-zinc-50 text-sm leading-6 dark:border-zinc-800 dark:bg-zinc-900">{{ $generation->generated_description }}</textarea>
        </x-output-card>

        <div class="space-y-5">
            <x-output-card title="Hashtags" :copy="implode(' ', $generation->generated_hashtags)">
                <div class="flex flex-wrap gap-2">
                    @foreach($generation->generated_hashtags as $hashtag)
                        <span class="rounded-full border border-blue-200 bg-blue-50 px-3 py-1 text-sm font-medium text-blue-700 dark:border-blue-500/30 dark:bg-blue-500/10 dark:text-blue-200">{{ $hashtag }}</span>
                    @endforeach
                </div>
            </x-output-card>

            <x-output-card title="Pin Comment" :copy="$generation->generated_pin_comment">
                <textarea readonly rows="5" class="block w-full resize-none rounded-xl border-zinc-200 bg-zinc-50 text-sm leading-6 dark:border-zinc-800 dark:bg-zinc-900">{{ $generation->generated_pin_comment }}</textarea>
            </x-output-card>
        </div>
    </div>
</x-layouts.app>
