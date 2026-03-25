$(document).ready(function () {

    const API_BASE = window.APP_CONFIG?.API_BASE || "http://127.0.0.1:8000";

    const sessionKey = localStorage.getItem("session_key");
    if (sessionKey) {
        window.location.href = "/pages/profile.html";
        return;
    }

    // SIGN UP
    $("#signupBtn").on("click", function () {
        const name = $("#signupName").val().trim();
        const email = $("#signupEmail").val().trim().toLowerCase();
        const password = $("#signupPassword").val();

        if (!name || !email || !password) {
            alert("All fields are required");
            return;
        }

        $.ajax({
            url: `${API_BASE}/api/signup`,
            type: "POST",
            contentType: "application/json",
            data: JSON.stringify({ name, email, password }),
            success: function () {
                alert("Signup successful. Please login.");

                $("#signupName").val("");
                $("#signupEmail").val("");
                $("#signupPassword").val("");

                const signinTab = new bootstrap.Tab(
                    document.querySelector('[data-bs-target="#signin"]')
                );
                signinTab.show();
            },
            error: function (xhr) {
                const res = xhr.responseJSON;
                const fallback = xhr.responseText || `Signup failed (HTTP ${xhr.status || 0})`;
                alert(res?.message || fallback);
            }
        });
    });

    // SIGN IN
    $("#signinBtn").on("click", function () {
        const email = $("#signinEmail").val().trim().toLowerCase();
        const password = $("#signinPassword").val();

        if (!email || !password) {
            alert("Email and password are required");
            return;
        }

        $.ajax({
            url: `${API_BASE}/api/login`,
            type: "POST",
            contentType: "application/json",
            data: JSON.stringify({ email, password }),
            success: function (response) {

                // STORE REDIS SESSION KEY
                localStorage.setItem("session_key", response.session_key);

                localStorage.setItem("user_name", response.user.name);
                localStorage.setItem("user_email", response.user.email);

                window.location.href = "/pages/profile.html";
            },
            error: function (xhr) {
                const res = xhr.responseJSON;
                const fallback = xhr.responseText || `Login failed (HTTP ${xhr.status || 0})`;
                alert(res?.message || fallback);
            }
        });
    });

});
