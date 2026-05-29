<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="theme" x-init="init">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'AI YouTube SEO Workspace' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-zinc-50 text-zinc-900 antialiased dark:bg-zinc-950 dark:text-zinc-100">
    <x-toast />

    <div x-data="{ sidebar: false }" class="min-h-screen lg:grid lg:grid-cols-[280px_1fr]">
        <aside class="fixed inset-y-0 left-0 z-40 w-72 -translate-x-full border-r border-zinc-200 bg-white/95 px-4 py-5 shadow-xl backdrop-blur transition lg:static lg:w-auto lg:translate-x-0 lg:shadow-none dark:border-zinc-800 dark:bg-zinc-950/95"
               :class="{ 'translate-x-0': sidebar }">
            <div class="flex items-center justify-between">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-600 text-white shadow-sm shadow-blue-600/30">
                        <i data-lucide="wand-sparkles" class="h-5 w-5"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-zinc-950 dark:text-white">AI YouTube</p>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">SEO Workspace</p>
                    </div>
                </a>
                <button type="button" class="rounded-xl p-2 text-zinc-500 hover:bg-zinc-100 lg:hidden dark:hover:bg-zinc-900" x-on:click="sidebar = false">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>

            <nav class="mt-8 space-y-1">
                <x-nav-link :href="route('dashboard')" icon="bar-chart-3" :active="request()->routeIs('dashboard')">Dashboard</x-nav-link>
                <x-nav-link :href="route('seo.create')" icon="sparkles" :active="request()->routeIs('seo.*')">Generate SEO</x-nav-link>
                <x-nav-link :href="route('competitors.index')" icon="bar-chart-3" :active="request()->routeIs('competitors.*')">Competitor Analysis</x-nav-link>
                <x-nav-link :href="route('history.index')" icon="history" :active="request()->routeIs('history.*')">History</x-nav-link>
                <x-nav-link :href="route('settings.edit')" icon="settings" :active="request()->routeIs('settings.*')">Settings</x-nav-link>
            </nav>

            <div class="mt-8 rounded-xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-800 dark:bg-zinc-900/60">
                <p class="text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Workspace Mode</p>
                <p class="mt-2 text-sm text-zinc-700 dark:text-zinc-300">Single-user dashboard with local settings and private generation history.</p>
            </div>
        </aside>

        <div class="min-w-0">
            <header class="sticky top-0 z-30 border-b border-zinc-200 bg-zinc-50/85 backdrop-blur dark:border-zinc-800 dark:bg-zinc-950/85">
                <div class="flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center gap-3">
                        <button type="button" class="rounded-xl border border-zinc-200 bg-white p-2 text-zinc-600 lg:hidden dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-300" x-on:click="sidebar = true">
                            <i data-lucide="panel-left" class="h-5 w-5"></i>
                        </button>
                        <div>
                            <h1 class="text-lg font-semibold tracking-tight text-zinc-950 dark:text-white">{{ $pageTitle ?? 'Dashboard' }}</h1>
                            <p class="hidden text-sm text-zinc-500 sm:block dark:text-zinc-400">{{ $pageSubtitle ?? 'Generate and manage YouTube SEO assets with OpenAI.' }}</p>
                        </div>
                    </div>
                    <button type="button" x-on:click="toggle" class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-zinc-200 bg-white text-zinc-600 transition hover:border-blue-300 hover:text-blue-600 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:border-blue-500/40">
                        <i data-lucide="moon" class="hidden h-5 w-5 dark:block"></i>
                        <i data-lucide="sun" class="h-5 w-5 dark:hidden"></i>
                    </button>
                </div>
            </header>

            <main class="px-4 py-6 sm:px-6 lg:px-8">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
