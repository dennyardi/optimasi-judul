@if(session('toast'))
    <div x-data="{ show: true }"
         x-init="setTimeout(() => show = false, 4200)"
         x-show="show"
         x-transition
         class="fixed right-4 top-4 z-50 w-[calc(100%-2rem)] max-w-sm rounded-xl border p-4 shadow-xl {{ session('toast.type') === 'error' ? 'border-red-200 bg-red-50 text-red-800 dark:border-red-900/50 dark:bg-red-950 dark:text-red-200' : 'border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-900/50 dark:bg-emerald-950 dark:text-emerald-200' }}">
        <div class="flex items-start gap-3">
            <i data-lucide="{{ session('toast.type') === 'error' ? 'x' : 'sparkles' }}" class="mt-0.5 h-5 w-5"></i>
            <p class="text-sm font-medium">{{ session('toast.message') }}</p>
        </div>
    </div>
@endif
