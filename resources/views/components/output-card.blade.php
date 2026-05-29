@props(['title', 'copy' => null])

<section class="fade-enter rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-950">
    <div class="mb-4 flex items-center justify-between gap-3">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">{{ $title }}</h2>
        @if($copy)
            <x-copy-button :value="$copy" />
        @endif
    </div>
    {{ $slot }}
</section>
