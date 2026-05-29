@props(['href', 'icon', 'active' => false])

<a href="{{ $href }}"
   class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition {{ $active ? 'bg-blue-600 text-white shadow-sm shadow-blue-600/20' : 'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-950 dark:text-zinc-400 dark:hover:bg-zinc-900 dark:hover:text-zinc-50' }}">
    <i data-lucide="{{ $icon }}" class="h-4 w-4"></i>
    <span>{{ $slot }}</span>
</a>
