@extends('layouts.app')

@section('title', 'Forgot Password - Deadzone Revive')

@push('head-scripts')
    @if (env('TURNSTILE_ENABLED', false))
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @endif
@endpush

@section('content')
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
            <h1 class="text-3xl font-bold tracking-tight text-white">Reset Password</h1>
            <div class="w-16 h-1 bg-red-600 mx-auto mt-2 rounded-full"></div>
        </div>
        <p class="text-center text-gray-300 mb-6">
            Enter your email address and we will send you a link to reset your password.
        </p>
        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf
            <div>
                <label for="email" class="block text-sm font-medium mb-1 text-gray-300">Email Address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                    class="w-full px-4 py-3 rounded-lg bg-black border border-gray-700 text-white placeholder-gray-500 focus:outline-none focus:border-red-500">
                @error('email')
                    <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>
            @if (env('TURNSTILE_ENABLED', false))
                <div class="flex justify-center">
                    <div class="cf-turnstile" data-sitekey="{{ env('TURNSTILE_SITEKEY') }}"></div>
                </div>
                @error('captcha')
                    <div class="text-red-500 text-xs text-center">{{ $message }}</div>
                @enderror
            @endif
            <div>
                <button type="submit" class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-500 hover:to-red-600 py-3 px-4 rounded-lg font-medium text-sm transition-all">
                    Send Password Reset Link
                </button>
            </div>
            @if (session('status'))
                <div class="text-green-500 text-xs mt-2 text-center">{{ session('status') }}</div>
            @endif
        </form>
        <div class="mt-6 text-center">
            <a href="{{ route('welcome') }}" class="font-medium text-red-500 hover:text-red-400">Back to Login</a>
        </div>
    </div>
</body>
@endsection
