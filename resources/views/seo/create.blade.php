<x-layouts.app page-title="Generate SEO" page-subtitle="Core workspace untuk membuat paket SEO YouTube.">
    <div class="grid gap-6 xl:grid-cols-[.85fr_1.15fr]">
        <section class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-950">
            <form method="POST" action="{{ route('seo.store') }}" x-data="submitState" x-on:submit.prevent="submit" class="space-y-5">
                @csrf
                <div>
                    <label for="video_title" class="text-sm font-medium text-zinc-800 dark:text-zinc-200">Video Title</label>
                    <textarea id="video_title" name="video_title" rows="3" required class="mt-2 block w-full rounded-xl border-zinc-200 bg-white text-sm shadow-sm transition focus:border-blue-500 focus:ring-blue-500 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white">{{ old('video_title') }}</textarea>
                    <x-field-error name="video_title" />
                </div>

                <div>
                    <label for="short_description" class="text-sm font-medium text-zinc-800 dark:text-zinc-200">Short Description</label>
                    <textarea id="short_description" name="short_description" rows="8" required class="mt-2 block w-full rounded-xl border-zinc-200 bg-white text-sm leading-6 shadow-sm transition focus:border-blue-500 focus:ring-blue-500 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white">{{ old('short_description') }}</textarea>
                    <x-field-error name="short_description" />
                </div>

                <button type="submit" :disabled="loading" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-sm shadow-blue-600/30 transition hover:bg-blue-500 disabled:cursor-not-allowed disabled:opacity-70">
                    <i data-lucide="loader-2" x-show="loading" x-cloak class="h-4 w-4 animate-spin"></i>
                    <i data-lucide="sparkles" x-show="!loading" class="h-4 w-4"></i>
                    <span x-text="loading ? 'Generating...' : 'Generate SEO'">Generate SEO</span>
                </button>

                <div x-show="loading" x-cloak x-transition class="overflow-hidden rounded-xl border border-blue-200 bg-blue-50/80 p-4 dark:border-blue-500/30 dark:bg-blue-500/10">
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-600 text-white shadow-sm shadow-blue-600/30">
                            <i data-lucide="loader-2" class="h-5 w-5 animate-spin"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-zinc-950 dark:text-white" x-text="activeStage.title">Validating input</p>
                                    <p class="mt-1 text-sm leading-5 text-zinc-600 dark:text-zinc-300" x-text="activeStage.detail">Memastikan input siap diproses.</p>
                                </div>
                                <span class="rounded-full border border-blue-200 bg-white px-2.5 py-1 text-xs font-semibold text-blue-700 dark:border-blue-500/30 dark:bg-zinc-950 dark:text-blue-200" x-text="progress + '%'">8%</span>
                            </div>

                            <div class="mt-4 h-2 overflow-hidden rounded-full bg-blue-100 dark:bg-blue-950">
                                <div class="h-full rounded-full bg-blue-600 transition-all duration-700 ease-out" :style="`width: ${progress}%`"></div>
                            </div>

                            <div class="mt-4 space-y-2">
                                <template x-for="(stage, index) in stages" :key="stage.title">
                                    <div class="flex items-center gap-3 rounded-xl px-2 py-1.5 transition"
                                         :class="index === currentStage ? 'bg-white/80 dark:bg-zinc-950/70' : ''">
                                        <div class="flex h-7 w-7 items-center justify-center rounded-lg"
                                             :class="index < currentStage ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300' : index === currentStage ? 'bg-blue-600 text-white' : 'bg-zinc-100 text-zinc-400 dark:bg-zinc-900 dark:text-zinc-500'">
                                            <i :data-lucide="index < currentStage ? 'check-circle-2' : stage.icon" class="h-4 w-4"></i>
                                        </div>
                                        <span class="text-sm"
                                              :class="index <= currentStage ? 'font-medium text-zinc-800 dark:text-zinc-100' : 'text-zinc-500 dark:text-zinc-500'"
                                              x-text="stage.title"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                @unless($settings->hasApiKey())
                    <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800 dark:border-amber-900/60 dark:bg-amber-950 dark:text-amber-200">
                        OpenAI API key belum disimpan. Buka Settings sebelum generate.
                    </div>
                @endunless
            </form>
        </section>

        <div class="space-y-5">
            @if($generation)
                <x-output-card title="SEO Titles" :copy="implode(PHP_EOL, $generation->generated_titles)">
                    <x-title-score-list :titles="$generation->generated_titles" :scores="$generation->generated_title_scores ?? []" />
                </x-output-card>

                <x-output-card title="SEO Description" :copy="$generation->generated_description">
                    <textarea readonly rows="8" class="block w-full resize-none rounded-xl border-zinc-200 bg-zinc-50 text-sm leading-6 text-zinc-800 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-100">{{ $generation->generated_description }}</textarea>
                </x-output-card>

                <x-output-card title="Top 10 Meta Tags" :copy="implode(', ', $generation->generated_tags)">
                    <p class="text-sm leading-6 text-zinc-700 dark:text-zinc-300">{{ implode(', ', $generation->generated_tags) }}</p>
                </x-output-card>

                <x-output-card title="Hashtags" :copy="implode(' ', $generation->generated_hashtags)">
                    <div class="flex flex-wrap gap-2">
                        @foreach($generation->generated_hashtags as $hashtag)
                            <span class="rounded-full border border-blue-200 bg-blue-50 px-3 py-1 text-sm font-medium text-blue-700 dark:border-blue-500/30 dark:bg-blue-500/10 dark:text-blue-200">{{ $hashtag }}</span>
                        @endforeach
                    </div>
                </x-output-card>

                <x-output-card title="Pin Comment" :copy="$generation->generated_pin_comment">
                    <textarea readonly rows="4" class="block w-full resize-none rounded-xl border-zinc-200 bg-zinc-50 text-sm leading-6 text-zinc-800 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-100">{{ $generation->generated_pin_comment }}</textarea>
                </x-output-card>
            @else
                <section class="rounded-xl border border-dashed border-zinc-300 bg-white p-10 text-center dark:border-zinc-700 dark:bg-zinc-950">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-300">
                        <i data-lucide="wand-sparkles" class="h-6 w-6"></i>
                    </div>
                    <h2 class="mt-4 text-lg font-semibold text-zinc-950 dark:text-white">AI output akan muncul di sini</h2>
                    <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Hasil akan dipisah menjadi title, description, meta tags, hashtags, dan pin comment lengkap dengan tombol copy.</p>
                </section>
            @endif
        </div>
    </div>
</x-layouts.app>
