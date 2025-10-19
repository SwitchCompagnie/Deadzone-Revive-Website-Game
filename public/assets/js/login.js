const BASE_URL = 'https://serverlet.deadzonegame.net';
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
        
        const formData = new FormData(this);
        
        try {
            const response = await fetch($(this).attr('action'), {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (response.ok && data.success) {
                window.token = data.token;
                window.location.href = data.redirect;
            } else {
                btn.html(originalContent).prop("disabled", false);
                if (data.errors) {
                    const errorMessages = Object.values(data.errors).flat();
                    $(".login-info").text(errorMessages.join(', ')).css("color", "red");
                } else {
                    $(".login-info").text("Login failed. Please try again.").css("color", "red");
                }
            }
        } catch (error) {
            console.error('Login error:', error);
            btn.html(originalContent).prop("disabled", false);
            $(".login-info").text("Unexpected error during login").css("color", "red");
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