<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Forum') - Deadzone Revive</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="text-white bg-black min-h-screen">
    <nav class="fixed top-0 w-full z-50 bg-black/95 backdrop-blur-sm border-b border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <a href="{{ route('forum.index') }}" class="flex-shrink-0">
                    <img class="h-16 w-auto" src="https://deadzonegame.net/assets/img/logo.png" alt="Deadzone Revive Logo">
                </a>
                <div class="flex items-center gap-2 sm:gap-4">
                    @auth
                        <span class="text-gray-300 text-sm hidden md:inline">{{ auth()->user()->name }}</span>
                        @if(auth()->user()->email_verified_at)
                            <a href="{{ route('game.index') }}" class="text-gray-300 hover:text-white px-3 py-2 text-sm font-medium transition-colors rounded-lg hover:bg-gray-800">Game</a>
                        @endif
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-300 hover:text-white px-3 py-2 text-sm font-medium transition-colors rounded-lg hover:bg-gray-800">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">Login</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-28 pb-12">
        @if (session('success'))
            <div class="mb-6 p-4 rounded-lg bg-green-900/50 border border-green-500 text-green-200">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-6 p-4 rounded-lg bg-red-900/50 border border-red-500 text-red-200">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="mt-auto border-t border-gray-800 bg-black">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <p class="text-center text-gray-400 text-sm">&copy; {{ date('Y') }} Deadzone Revive. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
