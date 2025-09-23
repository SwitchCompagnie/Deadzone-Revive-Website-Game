var flashVersion = "11.3.300.271";
var messages = [];
var unloadMessage = "";
var mt = false;
var mtPST = "00:00";

$(document).ready(function () {
    const urlParams = new URLSearchParams(window.location.search);
    window.token = urlParams.get("token");

    if (!window.token) {
        console.warn("No token found. The game cannot start automatically.");
    } else {
        startGame(window.token);
        setInterval(refreshSession, 50 * 60 * 1000);
    }

    if (mt) {
        showMaintenanceScreen();
    } else {
        showGameScreen();
    }
});

function refreshSession() {
    let ss = $(".server-status");
    fetch(`/keepalive?token=${window.token}`)
        .then((response) => {
            if (response.status === 401) {
                ss.text("Session expired, login again.").css("color", "red");
            }
        })
        .catch((err) => console.error("Keepalive request failed:", err));
}

function showGameScreen() {
    var a = swfobject.getFlashPlayerVersion();
    $("#noflash-reqVersion").html(flashVersion);
    $("#noflash-currentVersion").html(a.major + "." + a.minor + "." + a.release);
    if (screen.availWidth <= 1250) {
        $("#nav").css("left", "220px");
    }
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
        clientInfo: {
            platform: navigator.platform,
            userAgent: navigator.userAgent,
        },
    };

    const params = {
        allowScriptAccess: "always",
        allowFullScreen: "true",
        allowFullScreenInteractive: "true",
        allowNetworking: "all",
        menu: "false",
        scale: "noScale",
        salign: "tl",
        wmode: "direct",
        bgColor: "#000000",
    };

    const attributes = { id: "game", name: "game" };

    $("#game-wrapper").height("0px");
    embedSWF("https://serverlet.deadzonegame.net/game/preloader.swf", flashVars, params, attributes);
}

function embedSWF(swfURL, flashVars, params, attributes) {
    swfobject.embedSWF(
        swfURL,
        "game-container",
        "100%",
        "100%",
        flashVersion,
        "swf/expressinstall.swf",
        flashVars,
        params,
        attributes,
        (e) => {
            if (!e.success) {
                showNoFlash();
            } else {
                setMouseWheelState(false);
            }
        }
    );
}

function showNoFlash() {
    $("#loading").remove();
    $("#noflash").css("display", "block");
    $("#game-wrapper").height("100%");
    $("#user-id").html("");
}

function showMaintenanceScreen() {
    var maintenanceMessage =
        "The Last Stand: Dead Zone is down for scheduled maintenance. ETA " +
        mtPST +
        " local time.";
    addMessage("maintenance", maintenanceMessage);
    showError(
        "Scheduled Maintenance",
        "The Last Stand: Dead Zone is down for scheduled maintenance.<br/>We apologize for any inconvenience.<br/><br/><strong>ETA " +
            mtPST +
            " local time</strong>"
    );
}

function showError(b, a) {
    $("#loading").remove();
    $("#generic-error").css("display", "block");
    $("#generic-error").html("<p><h2>" + b + "</h2></p><p>" + a + "</p>");
    $("#user-id").html("");
}

function killGame() {
    $("#game").remove();
    $("#game-container").remove();
    $("#loading").remove();
    $("#content").prepend(
        "<div id='messagebox'><div class='header'>Are you there?</div><div class='msg'>You've left your compound unattended for some time. Are you still playing?</div><div class='btn' onclick='refresh()'>BACK TO THE DEAD ZONE</div></div>"
    );
}

function onPreloaderReady() {
    $("#loading").remove();
    $("#game-wrapper").height("100%");
}

function onFlashHide(c) {
    if (c.state == "opened") {
        var b = document.getElementById("game").getScreenshot();
        if (b != null) {
            $("#content").append(
                "<img id='screenshot' style='position:absolute; top:120px; width:960px; height:804px;' src='data:image/jpeg;base64," +
                    b +
                    "'/>"
            );
        }
    } else {
        var a = $("#screenshot");
        if (a != null) a.remove();
    }
}

function refresh() {
    location.reload();
}

function addMessage(h, f, g, b) {
    var e = $('<div class="header-message-bar"></div>');
    e.data("id", h);
    if (g) {
        var c = $('<div class="close"></div>').click(function () {
            e.stop(true, true).animate({ height: "toggle" }, 250);
        });
        e.append(c);
    }
    if (b) {
        var a = $('<div class="loader"></div>');
        e.append(a);
    }
    f = parseUTCStrings(f);
    var d = $('<div class="header-message">' + f + "</div>");
    e.append(d);
    $("#warning-container").append(e);
    e.height("0px").animate({ height: "30px" }, 250);
    messages.push(e);
}

function removeMessage(c) {
    for (var a = messages.length - 1; a >= 0; a--) {
        var b = messages[a];
        if (b.data("id") == c) {
            b.stop(true).animate({ height: "toggle" }, 250);
            messages.splice(a, 1);
        }
    }
}

function parseUTCStrings(a) {
    reg = /\[\%UTC (\d{4})\-(\d{2})\-(\d{2}) (\d{2})\-(\d{2})\]/gi;
    while ((seg = reg.exec(a))) {
        a = a.replace(
            seg[0],
            convertUTCtoLocal(
                Number(seg[1]),
                Number(seg[2]),
                Number(seg[3]),
                Number(seg[4]),
                Number(seg[5])
            )
        );
    }
    reg = /\[\%UTC (\d{2})\-(\d{2})\]/gi;
    while ((seg = reg.exec(a))) {
        a = a.replace(seg[0], convertUTCtoLocal(0, 0, 0, Number(seg[1]), Number(seg[2])));
    }
    return a;
}

function convertUTCtoLocal(c, f, b, a, e) {
    var h = new Date();
    if (c > 0) h.setUTCFullYear(c, f - 1, b);
    h.setUTCHours(a);
    h.setUTCMinutes(e);
    var g = "";
    if (c > 0) {
        months = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
        g = months[h.getMonth()] + " " + h.getDate() + ", " + h.getFullYear() + " ";
    }
    g += (h.getHours() <= 12 ? h.getHours() : h.getHours() - 12) + ":" + (h.getMinutes() < 10 ? "0" + h.getMinutes() : h.getMinutes()) + (h.getHours() < 12 ? "am" : "pm");
    return g;
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

function updateNavClass(a) {
    $("#nav-ul")[0].className = a;
}

function setMouseWheelState(a) {
    if (a) {
        document.onmousewheel = null;
        if (document.addEventListener) document.removeEventListener("DOMMouseScroll", preventWheel, false);
    } else {
        document.onmousewheel = function () { preventWheel(); };
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
    if (c == null) return "";
    return decodeURIComponent(c[1].replace(/\+/g, " "));
}