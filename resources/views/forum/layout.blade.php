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
    <nav class="w-full z-50 bg-black bg-opacity-90 border-b border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-8">
                    <div class="flex-shrink-0">
                        <img class="h-24" src="https://deadzonegame.net/assets/img/logo.png" alt="Deadzone Revive Logo">
                    </div>
                    <a href="{{ route('forum.index') }}" class="text-gray-300 hover:text-white px-3 py-2 text-sm font-medium">Forum</a>
                    @auth
                        @if(auth()->user()->email_verified_at)
                            <a href="{{ route('game.index') }}" class="text-gray-300 hover:text-white px-3 py-2 text-sm font-medium">Game</a>
                        @endif
                    @endauth
                </div>
                <div class="flex items-center space-x-4">
                    <form action="{{ route('forum.search') }}" method="GET" class="relative">
                        <input type="text" name="q" placeholder="Search..." 
                            class="px-4 py-2 rounded-lg bg-gray-900 border border-gray-700 text-white placeholder-gray-500 focus:outline-none focus:border-red-500 text-sm">
                    </form>
                    @auth
                        <span class="text-gray-300 text-sm">{{ auth()->user()->name }}</span>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="text-gray-300 hover:text-white px-3 py-2 text-sm font-medium">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-300 hover:text-white px-3 py-2 text-sm font-medium">Login</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if (session('success'))
            <div class="mb-4 p-3 rounded-lg bg-green-900/50 border border-green-500 text-green-200 text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 p-3 rounded-lg bg-red-900/50 border border-red-500 text-red-200 text-sm">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="mt-16 border-t border-gray-800 bg-black">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="text-center text-gray-400 text-sm">
                <p>&copy; {{ date('Y') }} Deadzone Revive. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
