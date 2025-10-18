const BASE_URL = 'https://serverlet.deadzonegame.net';
let debounceTimeout;
let usernameTimer;
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
                usernameTimer = setTimeout(() => checkUsernameAvailability(value), 500);
            }
            
            const currentPassword = $("#password").val();
            if (currentPassword.length > 0) {
                validatePassword(currentPassword);
            } else {
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
        
        if (!isUsernameValid || !isPasswordValid) {
            $(".login-info").text("Please fix the errors before submitting.").css("color", "red");
            return;
        }

        const btn = $("#login-button");
        const originalContent = btn.html();
        btn.html('<i class="fa-solid fa-circle-notch fa-spin"></i>').prop("disabled", true);

        try {
            const formData = new FormData(this);
            const response = await fetch($(this).attr('action'), {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (response.redirected) {
                window.location.href = response.url;
                return;
            }

            const data = await response.json();
            
            if (data.errors) {
                displayErrors(data.errors);
                btn.html(originalContent).prop("disabled", false);
            } else if (data.redirect) {
                window.location.href = data.redirect;
            }
        } catch (error) {
            console.error("Login error:", error);
            $(".login-info").text("An unexpected error occurred. Please try again.").css("color", "red");
            btn.html(originalContent).prop("disabled", false);
        }
    });
});

function validateUsername(username) {
    const usernameRegex = /^[a-zA-Z0-9]+$/;
    const badwords = ["dick", "fuck", "shit", "bitch"];
    const infoDiv = $(".username-info");

    if (username.length < 6 || !usernameRegex.test(username)) {
        infoDiv.text("Username must be at least 6 characters. Only letters and digits allowed.").css("color", "red");
        isUsernameValid = false;
        return false;
    }

    if (badwords.some(bad => username.toLowerCase().includes(bad))) {
        infoDiv.text("Inappropriate username. Please choose another name.").css("color", "orange");
        isUsernameValid = false;
        return false;
    }

    infoDiv.text("Checking availability...").css("color", "orange");
    return true;
}

function checkUsernameAvailability(username) {
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
                infoDiv.html('<span style="color:#7a8bac">Username exists. Enter password to log in.</span>');
            } else {
                infoDiv.text("Username available. You will be registered.").css("color", "green");
            }
            isUsernameValid = true;
        })
        .catch(error => {
            console.error("Error:", error);
            $(".username-info").text("Error checking username availability.").css("color", "red");
            isUsernameValid = false;
        });
}

function validatePassword(password) {
    const infoDiv = $(".password-info");
    
    if (password.length >= 6) {
        infoDiv.text("Password is valid.").css("color", "green");
        isPasswordValid = true;
    } else {
        infoDiv.text("Password must be at least 6 characters.").css("color", "red");
        isPasswordValid = false;
    }
}

function displayErrors(errors) {
    if (errors.username) {
        $(".username-info").text(errors.username[0]).css("color", "red");
    }
    if (errors.password) {
        $(".password-info").text(errors.password[0]).css("color", "red");
    }
    if (errors.captcha) {
        $(".login-info").text(errors.captcha[0]).css("color", "red");
    }
    if (errors.login) {
        $(".login-info").text(errors.login[0]).css("color", "red");
    }
}