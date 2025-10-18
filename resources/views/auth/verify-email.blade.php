<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="text-white bg-black flex items-center justify-center min-h-screen">
    <div class="p-8 rounded-xl w-full max-w-lg mx-4 border border-gray-800 bg-black/80 backdrop-blur-sm">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold tracking-tight text-white">Verify Your Email Address</h1>
            <div class="w-16 h-1 bg-red-600 mx-auto mt-2 rounded-full"></div>
        </div>
        <p class="text-center text-gray-300 mb-6">
            A verification link has been sent to your email address. Please check your inbox and click the link to verify your email.
        </p>
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="w-full bg-gradient-to-r from-red-600 to-red-700 hover:from-red-500 hover:to-red-600 py-3 px-4 rounded-lg font-medium text-sm transition-all">
                Resend Verification Email
            </button>
        </form>
        @if (session('message'))
            <p class="text-green-500 text-center mt-4">{{ session('message') }}</p>
        @endif
    </div>
</body>
</html>