<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deadzone Revive</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://kit.fontawesome.com/7b481d966b.js" crossorigin="anonymous"></script>
    <script type="text/javascript" src="{{ asset('assets/js/login.js') }}"></script>
</head>
<body class="text-white bg-black flex items-center justify-center min-h-screen pt-24">
    <nav class="fixed top-0 w-full z-50 bg-black bg-opacity-90 border-b border-gray-800"> 
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <img class="h-24" src="https://deadzonegame.net/assets/img/logo.png" alt="Deadzone Revive Logo">
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="form-container p-8 rounded-xl w-full max-w-lg mx-4 border border-gray-800 bg-black/80 backdrop-blur-sm">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold tracking-tight text-white">Login</h1>
            <div class="w-16 h-1 bg-red-600 mx-auto mt-2 rounded-full"></div>
        </div>
        <form id="pio-login" class="space-y-5">
            <div>
                <label for="username" class="block text-sm font-medium mb-1 text-gray-300">Username</label>
                <div class="relative">
                    <input type="text" id="username" name="username" required
                        class="input-field w-full px-4 py-3 rounded-lg bg-black border border-gray-700 text-white placeholder-gray-500 focus:outline-none focus:border-red-500">
                    <div class="username-info text-xs mt-1 text-gray-400"></div>
                </div>
            </div>
            <div>
                <label for="password" class="block text-sm font-medium mb-1 text-gray-300">Password</label>
                <div class="relative">
                    <input type="password" id="password" name="password" required
                        class="input-field w-full px-4 py-3 rounded-lg bg-black border border-gray-700 text-white placeholder-gray-500 focus:outline-none focus:border-red-500">
                    <div class="password-info text-xs mt-1 text-gray-400"></div>
                </div>
            </div>
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 rounded bg-black border-gray-700 text-red-600 focus:ring-red-500">
                    <label for="remember-me" class="ml-2 block text-sm text-gray-300">Remember me</label>
                </div>
                <div class="text-sm">
                    <a href="#" class="font-medium text-red-500 hover:text-red-400">Forgot password?</a>
                </div>
            </div>
            <div>
                <button type="submit" id="login-button" class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-500 hover:to-red-600 py-3 px-4 rounded-lg font-medium text-sm transition-all">
                    <i data-feather="log-in"></i> Register / Login
                </button>
                <div class="login-info text-xs mt-1 text-gray-400"></div>
            </div>
        </form>
        <div class="flex justify-between mt-6 gap-3">
            <a href="{{ route('auth.social', 'discord') }}" class="social-btn flex-1 flex items-center justify-center gap-2 bg-[#5865F2] hover:bg-[#4752C4] text-white font-medium py-3 px-4 rounded-lg transition-all">
                <i class="fab fa-discord"></i> Discord
            </a>
            <a href="{{ route('auth.social', 'twitter') }}" class="social-btn flex-1 flex items-center justify-center gap-2 bg-transparent hover:bg-gray-900 text-white font-medium py-3 px-4 rounded-lg border border-gray-600 transition-all">
                <i class="fab fa-x-twitter"></i>
            </a>
            <a href="{{ route('auth.social', 'github') }}" class="social-btn flex-1 flex items-center justify-center gap-2 bg-white hover:bg-gray-200 text-black font-medium py-3 px-4 rounded-lg border border-gray-600 transition-all">
                <i class="fab fa-github"></i> GitHub
            </a>
        </div>
        <div class="mt-4">
            <button disabled
                class="w-full flex items-center justify-center gap-2 bg-green-600 text-white font-medium py-3 px-4 rounded-lg cursor-not-allowed opacity-50 transition-all">
                <img src="{{ asset('assets/images/greenspirits.svg') }}" alt="Green Spirit Icon" class="h-6 w-6">
                Green Spirit [SOON]
            </button>
        </div>
    </div>
</body>
</html>