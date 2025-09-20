const BASE_URL = 'http://78.47.120.130:8080';
let debounceTimeout;
let usernameTimer;
let TOKEN_REFRESH_INTERVAL = 50 * 60 * 1000;
let isUsernameValid = false;
let isPasswordValid = false;

$(document).ready(function () {
    $("#username").on("input", function () {
        const value = $(this).val();
        clearTimeout(debounceTimeout);
        $(".username-info").text("");
        debounceTimeout = setTimeout(() => {
            if (validateUsername(value)) {
                clearTimeout(usernameTimer);
                usernameTimer = setTimeout(() => doesUserExist(value), 500);
            }
            const currentPassword = $("#password").val();
            if (currentPassword.length > 0) validatePassword(currentPassword);
            else {
                $(".password-info").text("");
                isPasswordValid = false;
            }
        }, 500);
    });

    $("#password").on("input", function () {
        const value = $(this).val();
        clearTimeout(debounceTimeout);
        $(".password-info").text("");
        debounceTimeout = setTimeout(() => validatePassword(value), 500);
    });

    $("#pio-login").submit(async function (event) {
        event.preventDefault();
        if (!isUsernameValid || !isPasswordValid) return;
        const btn = $("#login-button");
        const originalContent = btn.html();
        btn.html('<i class="fa-solid fa-circle-notch fa-spin"></i>').prop("disabled", true);
        const username = $("#username").val();
        const password = $("#password").val();
        const success = await login(username, password);
        if (!success) {
            btn.html(originalContent).prop("disabled", false);
        } else {
            window.location.href = `/game?token=${window.token}`;
        }
    });
});

function validateUsername(username) {
    const usernameRegex = /^[a-zA-Z0-9]+$/;
    const badwords = ["dick"];
    const infoDiv = $(".username-info");
    if (username.length < 6 || !usernameRegex.test(username)) {
        infoDiv.text("Username must be at least 6 characters. Only letters and digits allowed.").css("color", "red");
        isUsernameValid = false;
        return false;
    }
    if (badwords.some(bad => username.toLowerCase().includes(bad))) {
        infoDiv.text("Possible badword detected. Please choose another name.").css("color", "orange");
        isUsernameValid = false;
        return false;
    }
    infoDiv.text("Checking server...").css("color", "orange");
    return true;
}

function doesUserExist(username) {
    fetch(`${BASE_URL}/api/userexist?username=${encodeURIComponent(username)}`)
        .then(async response => {
            if (!response.ok) {
                const error = await response.json();
                throw new Error(error.reason || "User check failed");
            }
            return response.text();
        })
        .then(result => {
            const infoDiv = $(".username-info");
            if (result === "yes") {
                infoDiv.html('<span style="color:#7a8bac">Username taken.<br>Enter the password to log in.</span>');
            } else {
                infoDiv.text("Username is available, you will be registered.").css("color", "green");
            }
            isUsernameValid = true;
        })
        .catch(error => {
            console.error("Error:", error);
            $(".username-info").text("Error checking username").css("color", "red");
            isUsernameValid = false;
        });
}

function validatePassword(password) {
    const infoDiv = $(".password-info");
    if (password.length >= 6) {
        infoDiv.text("Password is fine.").css("color", "green");
        isPasswordValid = true;
    } else {
        infoDiv.text("Password must be at least 6 characters.").css("color", "red");
        isPasswordValid = false;
    }
}

async function login(username, password) {
    const loginDiv = $(".login-info");
    loginDiv.text("").css("color", "orange");
    try {
        const response = await fetch(`${BASE_URL}/api/login`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ username, password })
        });
        if (!response.ok) {
            const error = await response.json();
            loginDiv.text(error.reason === "wrong password" ? "Incorrect password. Please try again." : `Login failed: ${error.reason || "Unknown error"}`).css("color", "red");
            return false;
        }
        const data = await response.json();
        if (!data?.token) return false;
        window.token = data.token;
        return true;
    } catch (err) {
        console.error("Login error:", err);
        loginDiv.text("Unexpected error during login").css("color", "red");
        return false;
    }
}