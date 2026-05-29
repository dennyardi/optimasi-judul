<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - AI YouTube SEO Workspace</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-zinc-50 text-zinc-900 antialiased">
    <main class="grid min-h-screen lg:grid-cols-[1fr_520px]">
        <section class="hidden border-r border-zinc-200 bg-white px-10 py-10 lg:flex lg:flex-col lg:justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-600 text-white shadow-sm shadow-blue-600/30">
                    <i data-lucide="wand-sparkles" class="h-5 w-5"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-zinc-950">AI YouTube</p>
                    <p class="text-xs text-zinc-500">SEO Workspace</p>
                </div>
            </div>

            <div class="max-w-xl">
                <p class="text-sm font-semibold uppercase tracking-wide text-blue-600">Single-user dashboard</p>
                <h1 class="mt-4 text-4xl font-semibold tracking-tight text-zinc-950">Optimasi judul, deskripsi, dan analisis kompetitor YouTube.</h1>
                <p class="mt-5 text-base leading-7 text-zinc-600">Masuk untuk mengelola settings OpenAI, generate SEO, melihat history, dan membaca pola upload kompetitor dari YouTube Data API.</p>

                <div class="mt-8 grid grid-cols-3 gap-3">
                    <div class="rounded-xl border border-zinc-200 bg-zinc-50 p-4">
                        <p class="text-2xl font-semibold text-zinc-950">SEO</p>
                        <p class="mt-1 text-xs text-zinc-500">Titles, tags, hashtags</p>
                    </div>
                    <div class="rounded-xl border border-zinc-200 bg-zinc-50 p-4">
                        <p class="text-2xl font-semibold text-zinc-950">AI</p>
                        <p class="mt-1 text-xs text-zinc-500">OpenAI powered</p>
                    </div>
                    <div class="rounded-xl border border-zinc-200 bg-zinc-50 p-4">
                        <p class="text-2xl font-semibold text-zinc-950">YT</p>
                        <p class="mt-1 text-xs text-zinc-500">Competitor data</p>
                    </div>
                </div>
            </div>

            <p class="text-xs text-zinc-400">Private workspace for your YouTube optimization workflow.</p>
        </section>

        <section class="flex items-center justify-center px-4 py-10 sm:px-6">
            <div class="w-full max-w-md">
                <div class="mb-8 flex items-center gap-3 lg:hidden">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-600 text-white shadow-sm shadow-blue-600/30">
                        <i data-lucide="wand-sparkles" class="h-5 w-5"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-zinc-950">AI YouTube</p>
                        <p class="text-xs text-zinc-500">SEO Workspace</p>
                    </div>
                </div>

                <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-wide text-blue-600">Welcome back</p>
                        <h2 class="mt-2 text-2xl font-semibold tracking-tight text-zinc-950">Login dashboard</h2>
                        <p class="mt-2 text-sm leading-6 text-zinc-500">Gunakan username admin untuk masuk ke workspace.</p>
                    </div>

                    <form method="POST" action="{{ route('login.store') }}" class="mt-8 space-y-5">
                        @csrf

                        <div>
                            <label for="username" class="text-sm font-medium text-zinc-800">Username</label>
                            <div class="mt-2 flex rounded-xl border border-zinc-200 bg-white shadow-sm focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500">
                                <div class="flex items-center px-3 text-zinc-400">
                                    <i data-lucide="user" class="h-4 w-4"></i>
                                </div>
                                <input id="username" name="username" type="text" value="{{ old('username') }}" required autofocus autocomplete="username" class="block min-w-0 flex-1 border-0 bg-transparent text-sm focus:ring-0">
                            </div>
                            <x-field-error name="username" />
                        </div>

                        <div>
                            <label for="password" class="text-sm font-medium text-zinc-800">Password</label>
                            <div class="mt-2 flex rounded-xl border border-zinc-200 bg-white shadow-sm focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500">
                                <div class="flex items-center px-3 text-zinc-400">
                                    <i data-lucide="lock" class="h-4 w-4"></i>
                                </div>
                                <input id="password" name="password" type="password" required autocomplete="current-password" class="block min-w-0 flex-1 border-0 bg-transparent text-sm focus:ring-0">
                            </div>
                            <x-field-error name="password" />
                        </div>

                        <label class="flex items-center justify-between gap-3 rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm text-zinc-600">
                            <span>Remember this device</span>
                            <input type="checkbox" name="remember" value="1" class="rounded border-zinc-300 text-blue-600 focus:ring-blue-500">
                        </label>

                        <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-sm shadow-blue-600/30 transition hover:bg-blue-500">
                            <i data-lucide="sparkles" class="h-4 w-4"></i>
                            Login
                        </button>
                    </form>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
