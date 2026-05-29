<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="theme" x-init="init">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - AI YouTube SEO Workspace</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-zinc-50 text-zinc-900 antialiased dark:bg-zinc-950 dark:text-zinc-100">
    <main class="flex min-h-screen items-center justify-center px-4 py-10">
        <section class="w-full max-w-md rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-950">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-600 text-white shadow-sm shadow-blue-600/30">
                    <i data-lucide="wand-sparkles" class="h-5 w-5"></i>
                </div>
                <div>
                    <h1 class="text-lg font-semibold tracking-tight text-zinc-950 dark:text-white">AI YouTube SEO Workspace</h1>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Login untuk masuk dashboard.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('login.store') }}" class="mt-8 space-y-5">
                @csrf

                <div>
                    <label for="email" class="text-sm font-medium text-zinc-800 dark:text-zinc-200">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus class="mt-2 block w-full rounded-xl border-zinc-200 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white">
                    <x-field-error name="email" />
                </div>

                <div>
                    <label for="password" class="text-sm font-medium text-zinc-800 dark:text-zinc-200">Password</label>
                    <input id="password" name="password" type="password" required class="mt-2 block w-full rounded-xl border-zinc-200 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white">
                    <x-field-error name="password" />
                </div>

                <label class="flex items-center gap-3 text-sm text-zinc-600 dark:text-zinc-400">
                    <input type="checkbox" name="remember" value="1" class="rounded border-zinc-300 text-blue-600 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-900">
                    Remember me
                </label>

                <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-sm shadow-blue-600/30 transition hover:bg-blue-500">
                    <i data-lucide="sparkles" class="h-4 w-4"></i>
                    Login
                </button>
            </form>
        </section>
    </main>
</body>
</html>
