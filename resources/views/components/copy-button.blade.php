@props(['value'])

<button type="button"
        x-data="copyable"
        x-on:click="copy(@js($value))"
        class="inline-flex h-9 items-center gap-2 rounded-xl border border-zinc-200 px-3 text-sm font-medium text-zinc-700 transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700 dark:border-zinc-800 dark:text-zinc-300 dark:hover:border-blue-500/40 dark:hover:bg-blue-500/10 dark:hover:text-blue-200">
    <i data-lucide="clipboard" class="h-4 w-4"></i>
    <span x-text="copied ? 'Copied' : 'Copy'">Copy</span>
</button>
