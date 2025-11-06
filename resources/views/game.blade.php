<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>The Last Stand: Dead Zone</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/favicon.ico') }}" />
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/favicon.ico') }}" />
    <link href="{{ asset('assets/css/screen.css') }}" rel="stylesheet" type="text/css" />
    <script src="https://unpkg.com/@ruffle-rs/ruffle"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script type="text/javascript" src="{{ asset('assets/js/game.js') }}"></script>
    <script id="publishingnetwork" type="text/javascript" async src="{{ asset('assets/js/PublishingNetwork.js') }}"></script>
    <script type="text/javascript">
        // Set token from backend
        window.gameToken = "{{ $token ?? '' }}";
        // WebSocket configuration
        window.wsHost = "{{ env('WEBSOCKET_HOST', 'localhost') }}";
        window.wsPort = "{{ env('WEBSOCKET_PORT', '8080') }}";
        window.wsProtocol = "{{ env('WEBSOCKET_PROTOCOL', 'ws') }}";
    </script>
</head>
<body>
    <div id="wrapper">
        <a name="top"></a>
        <div id="header">
            <a id="logo" href="#" onclick="refresh()"><img src="{{ asset('assets/images/logo.png') }}" alt="TLS" /></a>
            <div id="nav">
                <ul id="nav-ul" class="play">
                    <li id="get-more"><a href="#top" onclick="openGetMoreDialogue()">Get More</a></li>
                    <li id="code"><a href="#top" onclick="openRedeemCodeDialogue()">Redeem Code</a></li>
                    <li id="fan-page"><a href="https://deadzonegame.net" target="_blank" onclick="updateNavClass('fan-page')">Fan Page</a></li>
                    <li id="help"><a href="https://discord.gg/7EyxwYEush" onclick="updateNavClass('help')" target="_blank">Help</a></li>
                    <li id="feedback"><a href="https://discord.gg/7EyxwYEush" onclick="updateNavClass('feedback')" target="_blank">Forum</a></li>
                </ul>
            </div>
            <div id="fb-likes">
                <iframe
                    src="https://www.facebook.com/plugins/like.php?href=http%3A%2F%2Fwww.facebook.com%2FLastStandDeadZone&send=false&layout=button_count&width=200&show_faces=true&action=like&colorscheme=dark&font&height=21"
                    scrolling="no" frameborder="0" style="max-width:85px;" allowtransparency="true" data-ruffle-polyfilled=""></iframe>
            </div>
            <div class="debug-container">
                <p class="server-status"><a href="https://status.deadzonegame.net" target="_blank">Server Status: N/A</a></p>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="logout-btn bg-gradient-to-r from-red-600 to-red-700 hover:from-red-500 hover:to-red-600 py-2 px-4 rounded-lg font-medium text-sm text-white transition-all">Logout</button>
                </form>
            </div>
        </div>

        <div id="warning-container"></div>
        <div id="message-container"></div>

        @if (session('status'))
            <div class="flash-message flash-success">
                {{ session('status') }}
            </div>
        @endif
        @if (session('error'))
            <div class="flash-message flash-error">
                {{ session('error') }}
            </div>
        @endif
        @if (session('message'))
            <div class="flash-message flash-info">
                {{ session('message') }}
            </div>
        @endif

        <div id="content">
            <div id="game-wrapper">
                <div id="game-container">
                    <div id="noflash" class="error" style="display:none;">
                        <h2>Ruffle Player Required</h2>
                        <p><strong>The Last Stand: Dead Zone</strong> requires Ruffle Flash Emulator to play.<br />
                        If you see this message, please check your browser console for errors.</p>
                        <p>Ruffle is loading automatically. If the game doesn't start, please refresh the page.</p>
                    </div>
                </div>
            </div>
            <div id="generic-error" class="error"></div>
        </div>

        <div id="footer">
            <a href="https://switchcompagnie.eu/terms" target="_blank">Terms</a> |
            <a href="https://switchcompagnie.eu/terms" target="_blank">Privacy</a> |
            © 2025 Ruby Realms Studio. Fan-made revival.
        </div>

        <div id="user-id">
            User ID : {{ Auth::check() ? Auth::user()->id : 'Connecting...' }}
        </div>
        <div id="ruby-realms-logo">
            <a href="https://switchcompagnie.eu" title="Ruby Realms Studio" target="_blank">
                <img src="{{ asset('assets/images/rubyrealmslogo.gif') }}" alt="Ruby Realms Studio">
            </a>
        </div>
    </div>
</body>
</html>