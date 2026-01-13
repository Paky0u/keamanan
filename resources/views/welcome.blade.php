<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>LMS - Learning Management System</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50">
            <div class="relative min-h-screen flex flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white">
                <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
                    <header class="grid grid-cols-2 items-center gap-2 py-10 lg:grid-cols-3">
                        <div class="flex lg:justify-center lg:col-start-2">
                            <h1 class="text-3xl font-bold text-blue-600">LMS</h1>
                        </div>
                        @if (Route::has('login'))
                            <nav class="-mx-3 flex flex-1 justify-end">
                                @auth
                                    <a href="{{ url('/dashboard') }}" class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white">
                                        Dashboard
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white">
                                        Log in
                                    </a>
                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white">
                                            Register
                                        </a>
                                    @endif
                                @endauth
                            </nav>
                        @endif
                    </header>

                    <main class="mt-6">
                        <div class="text-center">
                            <h2 class="text-4xl font-bold text-gray-900 mb-4">Simple Learning Management System</h2>
                            <p class="text-xl text-gray-600 mb-8">Create classes, share materials, manage assignments - all in one place</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-12">
                                <div class="bg-white p-6 rounded-lg shadow-md">
                                    <div class="text-3xl mb-4">🏫</div>
                                    <h3 class="text-lg font-semibold mb-2">Create & Join Classes</h3>
                                    <p class="text-gray-600">Easily create classes or join existing ones with a simple class code</p>
                                </div>
                                
                                <div class="bg-white p-6 rounded-lg shadow-md">
                                    <div class="text-3xl mb-4">📚</div>
                                    <h3 class="text-lg font-semibold mb-2">Share Materials</h3>
                                    <p class="text-gray-600">Upload and share PDFs, images, and videos with your class</p>
                                </div>
                                
                                <div class="bg-white p-6 rounded-lg shadow-md">
                                    <div class="text-3xl mb-4">📝</div>
                                    <h3 class="text-lg font-semibold mb-2">Manage Assignments</h3>
                                    <p class="text-gray-600">Create assignments, collect submissions, and provide grades</p>
                                </div>
                            </div>

                            <div class="mt-12">
                                @auth
                                    <a href="{{ url('/dashboard') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg text-lg">
                                        Go to Dashboard
                                    </a>
                                @else
                                    <div class="space-x-4">
                                        <a href="{{ route('register') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg text-lg">
                                            Get Started
                                        </a>
                                        <a href="{{ route('login') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-lg text-lg">
                                            Sign In
                                        </a>
                                    </div>
                                @endauth
                            </div>
                        </div>
                    </main>

                    <footer class="py-16 text-center text-sm text-black dark:text-white/70">
                        Simple LMS - Built with Laravel
                    </footer>
                </div>
            </div>
        </div>
    </body>
</html>