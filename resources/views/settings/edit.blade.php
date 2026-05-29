<x-layouts.app page-title="Settings" page-subtitle="OpenAI dan channel context untuk prompt generation.">
    <form method="POST" action="{{ route('settings.update') }}" class="grid gap-6 xl:grid-cols-2">
        @csrf
        @method('PUT')

        <section class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-950">
            <h2 class="text-lg font-semibold tracking-tight text-zinc-950 dark:text-white">OpenAI Settings</h2>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">API key disimpan terenkripsi oleh Laravel cast.</p>

            <div class="mt-6 space-y-5">
                <div x-data="{ show: false }">
                    <label for="openai_api_key" class="text-sm font-medium text-zinc-800 dark:text-zinc-200">OpenAI API Key</label>
                    <div class="mt-2 flex rounded-xl border border-zinc-200 bg-white shadow-sm focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500 dark:border-zinc-800 dark:bg-zinc-900">
                        <input id="openai_api_key" name="openai_api_key" :type="show ? 'text' : 'password'" placeholder="{{ $settings->hasApiKey() ? 'API key tersimpan. Isi hanya jika ingin mengganti.' : 'sk-...' }}" class="block min-w-0 flex-1 border-0 bg-transparent text-sm focus:ring-0 dark:text-white">
                        <button type="button" x-on:click="show = !show" class="px-3 text-zinc-500 hover:text-zinc-900 dark:hover:text-white">
                            <i data-lucide="eye" x-show="!show" class="h-4 w-4"></i>
                            <i data-lucide="eye-off" x-show="show" x-cloak class="h-4 w-4"></i>
                        </button>
                    </div>
                    <x-field-error name="openai_api_key" />
                </div>

                <div>
                    <label for="ai_model" class="text-sm font-medium text-zinc-800 dark:text-zinc-200">AI Model Selection</label>
                    <select id="ai_model" name="ai_model" class="mt-2 block w-full rounded-xl border-zinc-200 bg-white text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white">
                        @foreach($models as $model)
                            <option value="{{ $model }}" @selected(old('ai_model', $settings->ai_model) === $model)>{{ $model }}</option>
                        @endforeach
                    </select>
                    <x-field-error name="ai_model" />
                </div>

                <div x-data="{ show: false }" class="rounded-xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-800 dark:bg-zinc-900/60">
                    <label for="youtube_api_key" class="text-sm font-medium text-zinc-800 dark:text-zinc-200">YouTube Data API Key</label>
                    <div class="mt-2 flex rounded-xl border border-zinc-200 bg-white shadow-sm focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500 dark:border-zinc-800 dark:bg-zinc-950">
                        <input id="youtube_api_key" name="youtube_api_key" :type="show ? 'text' : 'password'" placeholder="{{ $settings->hasYouTubeApiKey() ? 'YouTube API key tersimpan. Isi hanya jika ingin mengganti.' : 'AIza...' }}" class="block min-w-0 flex-1 border-0 bg-transparent text-sm focus:ring-0 dark:text-white">
                        <button type="button" x-on:click="show = !show" class="px-3 text-zinc-500 hover:text-zinc-900 dark:hover:text-white">
                            <i data-lucide="eye" x-show="!show" class="h-4 w-4"></i>
                            <i data-lucide="eye-off" x-show="show" x-cloak class="h-4 w-4"></i>
                        </button>
                    </div>
                    <p class="mt-2 text-xs leading-5 text-zinc-500 dark:text-zinc-400">Opsional. Dipakai untuk mengambil video kompetitor dan statistik real saat Keyword Research.</p>
                    <x-field-error name="youtube_api_key" />
                </div>

                <div class="rounded-xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-800 dark:bg-zinc-900/60">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <label for="auto_capslock_titles" class="text-sm font-medium text-zinc-800 dark:text-zinc-200">Auto Capslock Titles</label>
                            <p class="mt-1 text-xs leading-5 text-zinc-500 dark:text-zinc-400">Aktifkan penekanan CAPSLOCK pada 1-3 kata hook di judul, bukan seluruh judul.</p>
                        </div>
                        <label class="relative inline-flex cursor-pointer items-center">
                            <input id="auto_capslock_titles" name="auto_capslock_titles" type="checkbox" value="1" class="peer sr-only" @checked(old('auto_capslock_titles', $settings->auto_capslock_titles ?? true))>
                            <span class="h-6 w-11 rounded-full bg-zinc-300 transition after:absolute after:left-0.5 after:top-0.5 after:h-5 after:w-5 after:rounded-full after:bg-white after:shadow-sm after:transition peer-checked:bg-blue-600 peer-checked:after:translate-x-5 dark:bg-zinc-700"></span>
                        </label>
                    </div>
                    <x-field-error name="auto_capslock_titles" />
                </div>

                <div>
                    <label for="description_style" class="text-sm font-medium text-zinc-800 dark:text-zinc-200">SEO Description Style</label>
                    <select id="description_style" name="description_style" class="mt-2 block w-full rounded-xl border-zinc-200 bg-white text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white">
                        <option value="long" @selected(old('description_style', $settings->description_style ?? 'long') === 'long')>Long description - detail dan lengkap</option>
                        <option value="short" @selected(old('description_style', $settings->description_style ?? 'long') === 'short')>Simple short description - ringkas</option>
                    </select>
                    <p class="mt-2 text-xs leading-5 text-zinc-500 dark:text-zinc-400">Long cocok untuk video utama. Short cocok untuk upload cepat atau deskripsi minimalis.</p>
                    <x-field-error name="description_style" />
                </div>
            </div>
        </section>

        <section class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-950">
            <h2 class="text-lg font-semibold tracking-tight text-zinc-950 dark:text-white">Channel Settings</h2>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Dipakai sebagai context prompt AI saat generate.</p>

            <div class="mt-6 grid gap-5">
                <div>
                    <label for="channel_name" class="text-sm font-medium text-zinc-800 dark:text-zinc-200">Channel Name</label>
                    <input id="channel_name" name="channel_name" value="{{ old('channel_name', $settings->channel_name) }}" class="mt-2 block w-full rounded-xl border-zinc-200 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white">
                    <x-field-error name="channel_name" />
                </div>

                <div>
                    <label for="niche" class="text-sm font-medium text-zinc-800 dark:text-zinc-200">Channel Niche</label>
                    <input id="niche" name="niche" value="{{ old('niche', $settings->niche) }}" class="mt-2 block w-full rounded-xl border-zinc-200 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white">
                    <x-field-error name="niche" />
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="tone" class="text-sm font-medium text-zinc-800 dark:text-zinc-200">Channel Tone</label>
                        <input id="tone" name="tone" value="{{ old('tone', $settings->tone) }}" class="mt-2 block w-full rounded-xl border-zinc-200 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white">
                        <x-field-error name="tone" />
                    </div>
                    <div>
                        <label for="language" class="text-sm font-medium text-zinc-800 dark:text-zinc-200">Language</label>
                        <input id="language" name="language" value="{{ old('language', $settings->language) }}" class="mt-2 block w-full rounded-xl border-zinc-200 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white">
                        <x-field-error name="language" />
                    </div>
                </div>

                <div>
                    <label for="default_cta" class="text-sm font-medium text-zinc-800 dark:text-zinc-200">Default CTA</label>
                    <textarea id="default_cta" name="default_cta" rows="4" class="mt-2 block w-full rounded-xl border-zinc-200 text-sm leading-6 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white">{{ old('default_cta', $settings->default_cta) }}</textarea>
                    <x-field-error name="default_cta" />
                </div>
            </div>
        </section>

        <div class="xl:col-span-2">
            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-sm shadow-blue-600/30 transition hover:bg-blue-500">
                <i data-lucide="settings" class="h-4 w-4"></i>
                Save Settings
            </button>
        </div>
    </form>
</x-layouts.app>
