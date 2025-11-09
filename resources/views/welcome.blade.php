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
    <script nomodule src="https://cdn.jsdelivr.net/npm/promise-polyfill@8/dist/polyfill.min.js"></script>
    <script nomodule src="https://cdn.jsdelivr.net/npm/whatwg-fetch@3/dist/fetch.umd.js"></script>
    <style>
        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        @keyframes pulse {
            0%, 100% { opacity: 0.3; }
            50% { opacity: 0.5; }
        }

        .bg-animated {
            background: linear-gradient(45deg, #000000, #1a0000, #000000, #0a0000);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
        }

        .bg-dots {
            background-image: radial-gradient(circle, rgba(220, 38, 38, 0.35) 1px, transparent 1px);
            background-size: 30px 30px;
            animation: pulse 8s ease-in-out infinite;
        }

        @supports not (animation: gradientShift 15s ease infinite) {
            .bg-animated {
                background: #000000;
            }
        }

        @media all and (-ms-high-contrast: none), (-ms-high-contrast: active) {
            .bg-animated {
                background: #000000;
            }
        }

        .flex {
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
        }

        .items-center {
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
        }

        .justify-center {
            -webkit-box-pack: center;
            -ms-flex-pack: center;
            justify-content: center;
        }

        .justify-between {
            -webkit-box-pack: justify;
            -ms-flex-pack: justify;
            justify-content: space-between;
        }

        input, button, .form-container {
            -webkit-border-radius: 0.5rem;
            -moz-border-radius: 0.5rem;
            border-radius: 0.5rem;
        }

        input, button, a {
            -webkit-transition: all 0.3s ease;
            -moz-transition: all 0.3s ease;
            -o-transition: all 0.3s ease;
            transition: all 0.3s ease;
        }
    </style>
</head>
<body class="text-white flex items-center justify-center min-h-screen pt-24 overflow-hidden relative">
    <div class="fixed inset-0 bg-animated"></div>
    <div class="fixed inset-0 bg-dots"></div>
    <div class="fixed inset-0 bg-gradient-to-t from-black/60 via-transparent to-black/80"></div>
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
    <div class="form-container p-8 rounded-xl w-full max-w-lg mx-4 border border-gray-800 bg-black relative z-10 shadow-2xl">
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
                <label for="email" class="block text-sm font-medium mb-1 text-gray-300">Email <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                        class="input-field w-full px-4 py-3 rounded-lg bg-black border border-gray-700 text-white placeholder-gray-500 focus:outline-none focus:border-red-500"
                        placeholder="your-email@example.com">
                    <div class="email-info text-xs mt-1 text-gray-400"></div>
                    @error('email')
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
                    <input id="remember-me" name="remember-me" type="checkbox"
                        class="h-4 w-4 appearance-none rounded border border-gray-600 bg-black 
                            checked:bg-red-600 checked:border-red-600 
                            focus:ring-2 focus:ring-red-500 focus:outline-none 
                            cursor-pointer relative transition-all duration-200
                            before:content-['✓'] before:absolute before:inset-0 
                            before:flex before:items-center before:justify-center 
                            before:text-white before:text-xs before:font-bold 
                            before:opacity-0 checked:before:opacity-100 checked:before:scale-100 
                            before:scale-0 before:transition-all before:duration-200">
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
    <script type="text/javascript">
        window.API_BASE_URL = "{{ config('app.api_base_url', env('API_BASE_URL')) }}";
    </script>
    <script src="{{ asset('assets/js/login.js') }}"></script>
</body>
</html>