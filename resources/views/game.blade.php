<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>The Last Stand: Dead Zone</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/favicon.ico') }}" />
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/favicon.ico') }}" />
    <link href="{{ asset('assets/css/screen.css') }}" rel="stylesheet" type="text/css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/swfobject/2.2/swfobject.min.js"
        integrity="sha512-INjccm+ffMBD7roophHluNrqwX0TLzZSEUPX2omxJP78ho8HbymItbcdh3HvgznbxeBhwcuqd6BnkBvdXeb1pg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script type="text/javascript" src="{{ asset('assets/js/game.js') }}"></script>
    <script id="publishingnetwork" type="text/javascript" async src="{{ asset('assets/js/PublishingNetwork.js') }}"></script>
    <script>
        window.RufflePlayer = window.RufflePlayer || {};
        window.RufflePlayer.config = {
            socketProxy: [
                {
                    host: "serverlet.deadzonegame.net",
                    port: 7777,
                    proxyUrl: "ws://serverlet.deadzonegame.net:8181"
                }
            ]
        };
        const originalEval = window.eval;
        window.eval = function (code) { return originalEval(code); };
        function setUserId(params) { }
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
                <p class="server-status">Server status: online</p>
                <button class="debug-log-btn">Open debug log</button>
            </div>
        </div>

        <div id="warning-container"></div>
        <div id="message-container"></div>

        <div id="content">
            <div id="game-wrapper">
                <div id="game-container">
                    <div id="noflash" class="error">
                        <h2>Flash Player Required</h2>
                        <p><strong>The Last Stand: Dead Zone</strong> requires the latest version of Adobe<sup>®</sup> Flash<sup>®</sup> Player.<br />
                        It's free, and only takes a small amount of time to download.</p>
                        <p>Required version: <strong><span id="noflash-reqVersion"></span></strong></p>
                        <p>Currently running version: <strong><span id="noflash-currentVersion"></span></strong></p>
                        <div id="download-flash">
                            <p><a href="flashplayer" title="Download Flash Player"><strong>Download Flash Player</strong></a></p>
                            <p><a href="?detectflash=false" title="I already have the latest Flash Player">I already have the latest Flash Player!</a></p>
                        </div>
                    </div>
                </div>
            </div>
            <div id="generic-error" class="error"></div>
        </div>

        <div id="footer">
            <a href="https://switchcompagnie.eu/terms" target="_blank">Terms</a> |
            <a href="https://switchcompagnie.eu/terms" target="_blank">Privacy</a> |
            © 2025 Con Artist Games. Fan-made revival.
        </div>

        <div id="user-id">Connecting...</div>
        <div id="con-artist-logo">
            <a href="conartist" title="Con Artist Games" target="_blank">
                <img src="{{ asset('assets/images/conartistlogo.gif') }}" alt="Con Artist Games">
            </a>
        </div>
    </div>
</body>
</html>