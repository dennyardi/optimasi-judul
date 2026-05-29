@props(['label', 'value', 'icon', 'tone' => 'blue'])

@php
    $toneClasses = [
        'blue' => 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-300',
        'sky' => 'bg-sky-50 text-sky-600 dark:bg-sky-500/10 dark:text-sky-300',
        'emerald' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-300',
        'indigo' => 'bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-300',
    ][$tone] ?? 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-300';
@endphp

<div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-zinc-800 dark:bg-zinc-950">
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $label }}</p>
            <p class="mt-3 text-3xl font-semibold tracking-tight text-zinc-950 dark:text-white">{{ $value }}</p>
        </div>
        <div class="rounded-xl p-3 {{ $toneClasses }}">
            <i data-lucide="{{ $icon }}" class="h-5 w-5"></i>
        </div>
    </div>
    @isset($meta)
        <div class="mt-4 text-sm text-zinc-500 dark:text-zinc-400">{{ $meta }}</div>
    @endisset
</div>
