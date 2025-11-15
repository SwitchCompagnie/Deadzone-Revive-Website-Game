@extends('layouts.app')

@section('title', '@yield("page-title", "Forum") - Deadzone Revive')

@push('styles')
<style>
    @keyframes glow-pulse {
        0%, 100% {
            box-shadow: 0 0 20px rgba(220, 38, 38, 0.3), 0 0 40px rgba(220, 38, 38, 0.1);
        }
        50% {
            box-shadow: 0 0 30px rgba(220, 38, 38, 0.5), 0 0 60px rgba(220, 38, 38, 0.2);
        }
    }

    @keyframes slide-in {
        from {
            transform: translateY(-100%);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .nav-futuristic {
        background: linear-gradient(135deg, rgba(0, 0, 0, 0.95) 0%, rgba(26, 0, 0, 0.95) 100%);
        border-bottom: 2px solid transparent;
        border-image: linear-gradient(90deg, rgba(220, 38, 38, 0), rgba(220, 38, 38, 0.5), rgba(220, 38, 38, 0)) 1;
        animation: slide-in 0.5s ease-out;
    }

    .nav-futuristic::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(220, 38, 38, 0.5), transparent);
    }

    .nav-link-futuristic {
        position: relative;
        overflow: hidden;
    }

    .nav-link-futuristic::before {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 2px;
        background: linear-gradient(90deg, #dc2626, #ef4444);
        transform: translateX(-100%);
        transition: transform 0.3s ease;
    }

    .nav-link-futuristic:hover::before {
        transform: translateX(0);
    }

    .nav-link-futuristic::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(220, 38, 38, 0.1), transparent);
        transform: translateX(-100%);
        transition: transform 0.5s ease;
    }

    .nav-link-futuristic:hover::after {
        transform: translateX(100%);
    }

    .logo-glow {
        filter: drop-shadow(0 0 10px rgba(220, 38, 38, 0.5));
        transition: filter 0.3s ease;
    }

    .logo-glow:hover {
        filter: drop-shadow(0 0 20px rgba(220, 38, 38, 0.8));
    }

    .btn-futuristic {
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, #dc2626, #991b1b);
        box-shadow: 0 0 20px rgba(220, 38, 38, 0.3);
        transition: all 0.3s ease;
    }

    .btn-futuristic::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s ease;
    }

    .btn-futuristic:hover::before {
        left: 100%;
    }

    .btn-futuristic:hover {
        box-shadow: 0 0 30px rgba(220, 38, 38, 0.6);
        transform: translateY(-2px);
    }

    .user-badge {
        background: linear-gradient(135deg, rgba(220, 38, 38, 0.2), rgba(153, 27, 27, 0.2));
        border: 1px solid rgba(220, 38, 38, 0.3);
        backdrop-filter: blur(10px);
    }
</style>
@endpush

@section('content')
<body class="text-white bg-black min-h-screen">
    <nav class="nav-futuristic fixed top-0 w-full z-50 backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <a href="{{ route('forum.index') }}" class="flex-shrink-0 group">
                    <img class="h-16 w-auto logo-glow transition-all duration-300 group-hover:scale-105"
                         src="https://deadzonegame.net/assets/img/logo.png"
                         alt="Deadzone Revive Logo">
                </a>

                <!-- Navigation Links -->
                <div class="flex items-center gap-2">
                    <a href="{{ route('forum.index') }}"
                       class="nav-link-futuristic text-gray-300 hover:text-white px-4 py-2 text-sm font-medium transition-all duration-300 rounded-lg hover:bg-red-900/20">
                        <i class="fas fa-comments mr-2"></i>Forum
                    </a>
                    <a href="https://status.deadzonegame.net/"
                       target="_blank"
                       class="nav-link-futuristic text-gray-300 hover:text-white px-4 py-2 text-sm font-medium transition-all duration-300 rounded-lg hover:bg-red-900/20">
                        <i class="fas fa-server mr-2"></i>Status
                    </a>

                    @auth
                        <!-- User Badge -->
                        <div class="user-badge hidden md:flex items-center gap-2 px-3 py-1.5 rounded-full ml-2">
                            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                            <span class="text-gray-300 text-sm font-medium">{{ auth()->user()->name }}</span>
                        </div>

                        @if(auth()->user()->email_verified_at)
                            <a href="{{ route('game.index') }}"
                               class="nav-link-futuristic text-gray-300 hover:text-white px-4 py-2 text-sm font-medium transition-all duration-300 rounded-lg hover:bg-red-900/20">
                                <i class="fas fa-gamepad mr-2"></i>Game
                            </a>
                        @endif

                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit"
                                    class="nav-link-futuristic text-gray-300 hover:text-red-400 px-4 py-2 text-sm font-medium transition-all duration-300 rounded-lg hover:bg-red-900/20">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}"
                           class="btn-futuristic text-white px-5 py-2.5 rounded-lg text-sm font-bold transition-all duration-300 ml-2">
                            <i class="fas fa-sign-in-alt mr-2"></i>Login
                        </a>
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

        @yield('forum-content')
    </main>

    <footer class="mt-auto border-t border-gray-800 bg-black">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <p class="text-center text-gray-400 text-sm">&copy; {{ date('Y') }} Deadzone Revive. All rights reserved.</p>
        </div>
    </footer>
</body>
@endsection
