<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Deadzone Revive')</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/js/all.min.js"
        integrity="sha512-6BTOlkauINO65nLhXhthZMtepgJSghyimIalb+crKRPhvhmsCdnIuGcVbR5/aQY2A+260iC1OPy1oCdB6pSSwQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
        crossorigin="anonymous"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Polyfills for older browsers -->
    <script nomodule src="https://cdn.jsdelivr.net/npm/promise-polyfill@8/dist/polyfill.min.js"></script>
    <script nomodule src="https://cdn.jsdelivr.net/npm/whatwg-fetch@3/dist/fetch.umd.js"></script>

    @stack('head-scripts')

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
            background-image: radial-gradient(circle, rgb(220 38 38 / 77%) 1px, transparent 1px);
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

    @stack('styles')
</head>
<body class="text-white">
    @yield('content')

    <script type="text/javascript">
        window.API_BASE_URL = "{{ config('app.api_base_url', env('API_BASE_URL')) }}";
    </script>

    @stack('scripts')
</body>
</html>
