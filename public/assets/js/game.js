var flashVersion = "11.3.300.271";
var messages = [];
var unloadMessage = "";
var mt = false;
var mtPST = "00:00";
const BASE_URL = window.API_BASE_URL || 'https://serverlet.deadzonegame.net';
const STATUS_API = BASE_URL + '/api/status';
const STATUS_URL = 'https://status.deadzonegame.net';
const MAINTENANCE_API = '/api/maintenance/status';

function checkMaintenanceMode() {
    return fetch(MAINTENANCE_API)
        .then(response => response.ok ? response.json() : Promise.reject())
        .then(data => {
            mt = data.maintenance || false;
            mtPST = data.eta || "00:00";
            return data;
        })
        .catch(error => {
            console.error("Failed to check maintenance status:", error);
            return { maintenance: false, eta: "00:00" };
        });
}

function updateServerStatus() {
    const statusElement = $(".server-status");
    statusElement.html(`<a href="${STATUS_URL}" target="_blank">Server Status: N/A</a>`);
    fetch(STATUS_API)
        .then(response => response.ok ? response.json() : Promise.reject())
        .then(data => {
            if (data.status && data.status.toLowerCase() === "online") {
                statusElement.html(`<a href="${STATUS_URL}" target="_blank">Server Status: Online</a>`);
            } else {
                statusElement.html(`<a href="${STATUS_URL}" target="_blank">Server Status: Offline</a>`);
            }
        })
        .catch(() => {
            statusElement.html(`<a href="${STATUS_URL}" target="_blank">Server Status: Offline</a>`);
        });
}

$(document).ready(function () {
    updateServerStatus();
    setInterval(updateServerStatus, 60000);

    // Get token from backend (set in game.blade.php) or URL param (fallback)
    window.token = window.gameToken || new URLSearchParams(window.location.search).get("token");

    if (!window.token) {
        console.error("No token found. Redirecting to login...");
        window.location.href = "/login";
        return;
    }

    // Check maintenance mode before starting the game
    checkMaintenanceMode().then(maintenanceData => {
        if (mt) {
            showMaintenanceScreen();
        } else if (window.token) {
            startGame(window.token);
            setInterval(refreshSession, 50 * 60 * 1000);
            showGameScreen();
        }
    });
});

function refreshSession() {
    fetch(`/keepalive?token=${window.token}`)
        .then(response => {
            if (response.status === 401) $(".server-status").text("Session expired, login again.").css("color", "red");
        })
        .catch(err => console.error("Keepalive request failed:", err));
}

function showGameScreen() {
    var a = swfobject.getFlashPlayerVersion();
    $("#noflash-reqVersion").html(flashVersion);
    $("#noflash-currentVersion").html(a.major + "." + a.minor + "." + a.release);
    if (screen.availWidth <= 1250) $("#nav").css("left", "220px");
}

function startGame(token) {
    $("#loading").css("display", "block");
    const flashVars = {
        path: "/game/",
        service: "pio",
        affiliate: getParameterByName("a"),
        useSSL: 0,
        gameId: "laststand-deadzone",
        connectionId: "public",
        clientAPI: "javascript",
        playerInsightSegments: [],
        playCodes: [],
        userToken: token,
        clientInfo: { platform: navigator.platform, userAgent: navigator.userAgent }
    };
    const params = { allowScriptAccess: "always", allowFullScreen: "true", allowFullScreenInteractive: "true", allowNetworking: "all", menu: "false", scale: "noScale", salign: "tl", wmode: "direct", bgColor: "#000000" };
    const attributes = { id: "game", name: "game" };
    $("#game-wrapper").height("0px");
    embedSWF("/game/preloader.swf", flashVars, params, attributes);
}

function embedSWF(swfURL, flashVars, params, attributes) {
    swfobject.embedSWF(BASE_URL + swfURL, "game-container", "100%", "100%", flashVersion, "swf/expressinstall.swf", flashVars, params, attributes, e => {
        if (!e.success) showNoFlash();
        else setMouseWheelState(false);
    });
}

function showNoFlash() {
    $("#loading").remove();
    $("#noflash").css("display", "block");
    $("#game-wrapper").height("100%");
    $("#user-id").html("");
}

function showMaintenanceScreen() {
    var maintenanceMessage = "The Last Stand: Dead Zone is down for scheduled maintenance. ETA " + mtPST + " local time.";
    addMessage("maintenance", maintenanceMessage);
    showError("Scheduled Maintenance", "The Last Stand: Dead Zone is down for scheduled maintenance.<br/><strong>ETA " + mtPST + " local time</strong>");
}

function showError(b, a) {
    $("#loading").remove();
    $("#generic-error").css("display", "block");
    $("#generic-error").html("<p><h2>" + b + "</h2></p><p>" + a + "</p>");
    $("#user-id").html("");
}

function killGame() {
    $("#game, #game-container, #loading").remove();
    $("#content").prepend("<div id='messagebox'><div class='header'>Are you there?</div><div class='msg'>You've left your compound unattended for some time. Are you still playing?</div><div class='btn' onclick='refresh()'>BACK TO THE DEAD ZONE</div></div>");
}

function onPreloaderReady() {
    $("#loading").remove();
    $("#game-wrapper").height("100%");
}

function onFlashHide(c) {
    if (c.state == "opened") {
        var b = document.getElementById("game").getScreenshot();
        if (b != null) $("#content").append("<img id='screenshot' style='position:absolute; top:120px; width:960px; height:804px;' src='data:image/jpeg;base64," + b + "'/>");
    } else $("#screenshot").remove();
}

function refresh() {
    location.reload();
}

function addMessage(h, f, g, b) {
    var e = $('<div class="header-message-bar"></div>');
    e.data("id", h);
    if (g) e.append($('<div class="close"></div>').click(() => e.stop(true, true).animate({ height: "toggle" }, 250)));
    if (b) e.append($('<div class="loader"></div>'));
    var d = $('<div class="header-message">' + f + "</div>");
    e.append(d);
    $("#warning-container").append(e);
    e.height("0px").animate({ height: "30px" }, 250);
    messages.push(e);
}

function removeMessage(c) {
    for (var a = messages.length - 1; a >= 0; a--) {
        if (messages[a].data("id") == c) {
            messages[a].stop(true).animate({ height: "toggle" }, 250);
            messages.splice(a, 1);
        }
    }
}

function updateNavClass(a) {
    $("#nav-ul")[0].className = a;
}

var requestCodeRedeemInterval;
var waitingForCodeRedeem = false;

function openRedeemCodeDialogue() {
  updateNavClass("code");
  if (mt || waitingForCodeRedeem) {
    return;
  }
  var a = function () {
    try {
      document.getElementById("game").openRedeemCode();
      removeMessage("openingCodeRedeem");
      updateNavClass(null);
      return true;
    } catch (b) {}
    return false;
  };
  if (!a()) {
    addMessage(
      "openingCodeRedeem",
      "Please wait while the game loads...",
      false,
      true
    );
    waitingForCodeRedeem = true;
    requestCodeRedeemInterval = setInterval(function () {
      if (a()) {
        waitingForCodeRedeem = false;
        clearInterval(requestCodeRedeemInterval);
      }
    }, 1000);
  }
}

var requestGetMoreInterval;
var waitingForGetMore = false;

function openGetMoreDialogue() {
  updateNavClass("get-more");
  if (mt || waitingForGetMore) {
    return;
  }
  var a = function () {
    try {
      if (document.getElementById("game").openGetMore()) {
        removeMessage("openingFuel");
        updateNavClass(null);
        return true;
      }
    } catch (b) {}
    return false;
  };
  if (!a()) {
    addMessage(
      "openingFuel",
      "Opening Fuel Store, please wait while the game loads...",
      false,
      true
    );
    waitingForGetMore = true;
    requestGetMoreInterval = setInterval(function () {
      if (a()) {
        waitingForGetMore = false;
        clearInterval(requestGetMoreInterval);
      }
    }, 1000);
  }
}

function setMouseWheelState(a) {
    if (a) {
        document.onmousewheel = null;
        if (document.addEventListener) document.removeEventListener("DOMMouseScroll", preventWheel, false);
    } else {
        document.onmousewheel = preventWheel;
        if (document.addEventListener) document.addEventListener("DOMMouseScroll", preventWheel, false);
    }
}

function preventWheel(a) {
    if (!a) a = window.event;
    if (a.preventDefault) a.preventDefault();
    else a.returnValue = false;
}

function getParameterByName(b) {
    b = b.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var a = "[\\?&]" + b + "=([^&#]*)";
    var d = new RegExp(a);
    var c = d.exec(window.location.search);
    return c == null ? "" : decodeURIComponent(c[1].replace(/\+/g, " "));
}