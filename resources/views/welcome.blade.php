<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Deadzone Revive</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    @if (env('TURNSTILE_ENABLED', false))
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @endif
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px) translateX(0px); }
            33% { transform: translateY(-20px) translateX(10px); }
            66% { transform: translateY(-10px) translateX(-10px); }
        }

        @keyframes pulse {
            0%, 100% { opacity: 0.3; }
            50% { opacity: 0.6; }
        }

        @keyframes drift {
            0% { transform: translate(0, 0) rotate(0deg); }
            100% { transform: translate(100vw, 100vh) rotate(360deg); }
        }

        .particle {
            position: absolute;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.8) 0%, rgba(156, 163, 175, 0.4) 40%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
            animation: drift linear infinite;
            filter: blur(1px);
        }

        .bg-grid {
            background-image:
                linear-gradient(rgba(75, 85, 99, 0.1) 1px, transparent 1px),
                linear-gradient(90deg, rgba(75, 85, 99, 0.1) 1px, transparent 1px);
            background-size: 40px 40px;
            animation: pulse 6s ease-in-out infinite;
        }
    </style>
</head>
<body class="text-white bg-black flex items-center justify-center min-h-screen pt-24 overflow-hidden relative">
    <div class="fixed inset-0 bg-gradient-to-br from-gray-950 via-black to-gray-950"></div>
    <div class="fixed inset-0 bg-grid"></div>
    <div id="particles-container" class="fixed inset-0"></div>
    <div class="fixed inset-0 bg-gradient-to-t from-black/80 via-transparent to-black/80"></div>
    <nav class="fixed top-0 w-full z-50 bg-black/95 backdrop-blur-sm border-b border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <a href="{{ route('login') }}" class="flex-shrink-0">
                    <img class="h-16 w-auto" src="https://deadzonegame.net/assets/img/logo.png" alt="Deadzone Revive Logo">
                </a>
                <div class="flex items-center gap-4">
                    <a href="{{ route('forum.index') }}" class="text-gray-300 hover:text-white px-4 py-2 text-sm font-medium transition-colors rounded-lg hover:bg-gray-800">Forum</a>
                    <a href="https://status.deadzonegame.net/" target="_blank" class="text-gray-300 hover:text-white px-4 py-2 text-sm font-medium transition-colors rounded-lg hover:bg-gray-800">Status</a>
                </div>
            </div>
        </div>
    </nav>
    <div class="form-container p-8 rounded-xl w-full max-w-lg mx-4 border border-gray-800/50 bg-black/70 backdrop-blur-xl relative z-10 shadow-2xl">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold tracking-tight text-white">Login</h1>
            <div class="w-16 h-1 bg-red-600 mx-auto mt-2 rounded-full"></div>
        </div>
        @if (session('error'))
            <div class="mb-4 p-3 rounded-lg bg-red-900/50 border border-red-500 text-red-200 text-sm">
                {{ session('error') }}
            </div>
        @endif
        @if (session('status'))
            <div class="mb-4 p-3 rounded-lg bg-green-900/50 border border-green-500 text-green-200 text-sm">
                {{ session('status') }}
            </div>
        @endif
        <form id="pio-login" action="{{ route('login.post') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label for="username" class="block text-sm font-medium mb-1 text-gray-300">Username</label>
                <div class="relative">
                    <input type="text" id="username" name="username" value="{{ old('username') }}" required
                        class="input-field w-full px-4 py-3 rounded-lg bg-black border border-gray-700 text-white placeholder-gray-500 focus:outline-none focus:border-red-500">
                    <div class="username-info text-xs mt-1 text-gray-400"></div>
                    @error('username')
                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div>
                <label for="password" class="block text-sm font-medium mb-1 text-gray-300">Password</label>
                <div class="relative">
                    <input type="password" id="password" name="password" required
                        class="input-field w-full px-4 py-3 rounded-lg bg-black border border-gray-700 text-white placeholder-gray-500 focus:outline-none focus:border-red-500">
                    <div class="password-info text-xs mt-1 text-gray-400"></div>
                    @error('password')
                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            @if (env('TURNSTILE_ENABLED', false))
                <div class="flex justify-center">
                    <div class="cf-turnstile" data-sitekey="{{ env('TURNSTILE_SITEKEY') }}"></div>
                </div>
                @error('captcha')
                    <div class="text-red-500 text-xs text-center">{{ $message }}</div>
                @enderror
            @endif
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 rounded bg-black border border-gray-700 text-red-600 focus:ring-red-500 appearance-none checked:bg-red-600 checked:border-red-600 relative cursor-pointer before:content-[''] before:absolute before:inset-0 before:rounded before:bg-red-600 before:scale-0 checked:before:scale-100 before:transition-transform before:duration-200 before:flex before:items-center before:justify-center before:text-white before:text-xs before:font-bold before:content-['✓']">
                    <label for="remember-me" class="ml-2 block text-sm text-gray-300">Remember me</label>
                </div>
                <div class="text-sm">
                    <a href="{{ route('password.request') }}" class="font-medium text-red-500 hover:text-red-400">Forgot password?</a>
                </div>
            </div>
            <div>
                <button type="submit" id="login-button" class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-500 hover:to-red-600 py-3 px-4 rounded-lg font-medium text-sm transition-all">
                    <i class="fa-solid fa-right-to-bracket"></i> Register / Login
                </button>
                <div class="login-info text-xs mt-1 text-gray-400"></div>
                @error('login')
                    <div class="text-red-500 text-xs mt-2 text-center">{{ $message }}</div>
                @enderror
            </div>
        </form>
        <div class="mt-6">
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-700"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-black text-gray-400">Or continue with</span>
                </div>
            </div>
        </div>
        <div class="flex justify-between mt-6 gap-3">
            @if(isset($maintenanceMode) && $maintenanceMode)
                <button disabled class="social-btn flex-1 flex items-center justify-center gap-2 bg-[#5865F2] opacity-50 cursor-not-allowed text-white font-medium py-3 px-4 rounded-lg">
                    <i class="fab fa-discord"></i> Discord
                </button>
                <button disabled class="social-btn flex-1 flex items-center justify-center gap-2 bg-transparent opacity-50 cursor-not-allowed text-white font-medium py-3 px-4 rounded-lg border border-gray-600">
                    <i class="fab fa-x-twitter"></i>
                </button>
                <button disabled class="social-btn flex-1 flex items-center justify-center gap-2 bg-white opacity-50 cursor-not-allowed text-black font-medium py-3 px-4 rounded-lg border border-gray-600">
                    <i class="fab fa-github"></i> GitHub
                </button>
            @else
                <a href="{{ route('auth.social', 'discord') }}" class="social-btn flex-1 flex items-center justify-center gap-2 bg-[#5865F2] hover:bg-[#4752C4] text-white font-medium py-3 px-4 rounded-lg transition-all">
                    <i class="fab fa-discord"></i> Discord
                </a>
                <a href="{{ route('auth.social', 'twitter') }}" class="social-btn flex-1 flex items-center justify-center gap-2 bg-transparent hover:bg-gray-900 text-white font-medium py-3 px-4 rounded-lg border border-gray-600 transition-all">
                    <i class="fab fa-x-twitter"></i>
                </a>
                <a href="{{ route('auth.social', 'github') }}" class="social-btn flex-1 flex items-center justify-center gap-2 bg-white hover:bg-gray-200 text-black font-medium py-3 px-4 rounded-lg border border-gray-600 transition-all">
                    <i class="fab fa-github"></i> GitHub
                </a>
            @endif
        </div>
        <div class="mt-4">
            <button disabled class="w-full flex items-center justify-center gap-2 bg-green-600 text-white font-medium py-3 px-4 rounded-lg cursor-not-allowed opacity-50 transition-all">
                <img src="{{ asset('assets/images/greenspirits.svg') }}" alt="Green Spirit Icon" class="h-6 w-6">
                Spirit Account [SOON]
            </button>
        </div>
    </div>
    <script src="{{ asset('assets/js/login.js') }}"></script>
    <script>
        const particlesContainer = document.getElementById('particles-container');
        const particleCount = 60;

        for (let i = 0; i < particleCount; i++) {
            const particle = document.createElement('div');
            particle.className = 'particle';

            const size = Math.random() * 8 + 3;
            particle.style.width = `${size}px`;
            particle.style.height = `${size}px`;

            particle.style.left = `${Math.random() * 100}%`;
            particle.style.top = `${Math.random() * 100}%`;

            const duration = Math.random() * 50 + 40;
            particle.style.animationDuration = `${duration}s`;
            particle.style.animationDelay = `${Math.random() * 8}s`;

            particle.style.opacity = Math.random() * 0.5 + 0.2;

            particlesContainer.appendChild(particle);
        }
    </script>
</body>
</html>