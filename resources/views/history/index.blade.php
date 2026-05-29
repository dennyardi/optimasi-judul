<x-layouts.app page-title="History" page-subtitle="Semua paket SEO yang pernah digenerate.">
    <section class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-950">
        @if($generations->count())
            <div class="hidden overflow-x-auto md:block">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800">
                    <thead class="bg-zinc-50 dark:bg-zinc-900/60">
                        <tr>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">Video Title</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">Created At</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">AI Model</th>
                            <th class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wide text-zinc-500">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @foreach($generations as $generation)
                            <tr class="transition hover:bg-zinc-50 dark:hover:bg-zinc-900/50">
                                <td class="max-w-xl px-5 py-4 text-sm font-medium text-zinc-900 dark:text-white">{{ $generation->video_title }}</td>
                                <td class="px-5 py-4 text-sm text-zinc-500 dark:text-zinc-400">{{ $generation->created_at->format('M d, Y H:i') }}</td>
                                <td class="px-5 py-4 text-sm text-zinc-500 dark:text-zinc-400">{{ $generation->ai_model }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('history.show', $generation) }}" class="inline-flex h-9 items-center gap-2 rounded-xl border border-zinc-200 px-3 text-sm font-medium text-zinc-700 hover:bg-zinc-50 dark:border-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-900">
                                            <i data-lucide="eye" class="h-4 w-4"></i>
                                            View
                                        </a>
                                        <form method="POST" action="{{ route('history.destroy', $generation) }}" onsubmit="return confirm('Delete this history item?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="inline-flex h-9 items-center gap-2 rounded-xl border border-red-200 px-3 text-sm font-medium text-red-600 hover:bg-red-50 dark:border-red-900/60 dark:text-red-300 dark:hover:bg-red-950">
                                                <i data-lucide="trash-2" class="h-4 w-4"></i>
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="divide-y divide-zinc-200 md:hidden dark:divide-zinc-800">
                @foreach($generations as $generation)
                    <article class="p-5">
                        <h2 class="text-sm font-semibold text-zinc-950 dark:text-white">{{ $generation->video_title }}</h2>
                        <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">{{ $generation->created_at->format('M d, Y H:i') }} • {{ $generation->ai_model }}</p>
                        <div class="mt-4 flex gap-2">
                            <a href="{{ route('history.show', $generation) }}" class="inline-flex h-9 items-center gap-2 rounded-xl border border-zinc-200 px-3 text-sm font-medium text-zinc-700 dark:border-zinc-800 dark:text-zinc-300">
                                <i data-lucide="eye" class="h-4 w-4"></i>
                                View
                            </a>
                            <form method="POST" action="{{ route('history.destroy', $generation) }}" onsubmit="return confirm('Delete this history item?')">
                                @csrf
                                @method('DELETE')
                                <button class="inline-flex h-9 items-center gap-2 rounded-xl border border-red-200 px-3 text-sm font-medium text-red-600 dark:border-red-900/60 dark:text-red-300">
                                    <i data-lucide="trash-2" class="h-4 w-4"></i>
                                    Delete
                                </button>
                            </form>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="border-t border-zinc-200 px-5 py-4 dark:border-zinc-800">
                {{ $generations->links() }}
            </div>
        @else
            <div class="p-10 text-center">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-zinc-100 text-zinc-500 dark:bg-zinc-900 dark:text-zinc-400">
                    <i data-lucide="history" class="h-6 w-6"></i>
                </div>
                <h2 class="mt-4 text-lg font-semibold text-zinc-950 dark:text-white">History masih kosong</h2>
                <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Generate SEO pertama untuk mulai menyimpan riwayat.</p>
                <a href="{{ route('seo.create') }}" class="mt-5 inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white">
                    <i data-lucide="sparkles" class="h-4 w-4"></i>
                    Generate SEO
                </a>
            </div>
        @endif
    </section>
</x-layouts.app>
