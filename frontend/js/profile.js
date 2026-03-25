$(document).ready(function () {

    const API_BASE = "http://127.0.0.1:8000";

    const sessionKey = localStorage.getItem("session_key");
    const userName = localStorage.getItem("user_name");
    const userEmail = localStorage.getItem("user_email");

    if (!sessionKey) {
        window.location.href = "/pages/signUp.html";
        return;
    }

    $("#profileName").text(userName || "");
    $("#profileEmail").text(userEmail || "");

    const firstLetter = userName ? userName.charAt(0).toUpperCase() : "U";
    $("#avatarLetter").text(firstLetter);

    // =====================================
    // INPUT RESTRICTIONS (BIND ONCE)
    // =====================================

    // Age: only digits, 0–99
    $("#age").on("input", function () {
        let value = $(this).val().replace(/\D/g, "");

        if (value === "") {
            $(this).val("");
            return;
        }

        let num = parseInt(value, 10);
        if (num < 0) num = 0;
        if (num > 99) num = 99;

        $(this).val(num);
    });

    // Contact: only digits, first digit 6/7/8/9, max 10 digits
    $("#contact").on("input", function () {
        let value = $(this).val().replace(/\D/g, "");

        // First digit must be 6,7,8,9
        if (value.length === 1 && !/^[6789]$/.test(value)) {
            value = "";
        }

        // Max 10 digits
        if (value.length > 10) {
            value = value.slice(0, 10);
        }

        $(this).val(value);
    });

    // =====================================
    // LOAD PROFILE
    // =====================================
    $.ajax({
        url: `${API_BASE}/api/profile`,
        method: "GET",
        data: { session_key: sessionKey },
        success: function (res) {
            if (res.status === "success" && res.data) {
                $("#age").val(res.data.age ?? "");
                $("#dob").val(res.data.dob ?? "");
                $("#contact").val(res.data.contact ?? "");
                $("#address").val(res.data.address ?? "");
                $("#gender").val(res.data.gender ?? "");
                $("#designation").val(res.data.designation ?? "");
                $("#company").val(res.data.company ?? "");
            }
        },
        error: function () {
            alert("Failed to load profile data");
        }
    });

    // =====================================
    // SAVE PROFILE
    // =====================================
    $("#saveProfileBtn").click(function () {

        const age = $("#age").val().trim();
        const dob = $("#dob").val().trim();
        const contact = $("#contact").val().trim();
        const address = $("#address").val().trim();
        const gender = $("#gender").val().trim();
        const designation = $("#designation").val().trim();
        const company = $("#company").val().trim();

        // Required check
        if (!age || !dob || !contact || !address || !gender) {
            alert("All fields including gender are required");
            return;
        }

        // Age validation
        const ageNum = parseInt(age, 10);
        if (isNaN(ageNum) || ageNum < 0 || ageNum > 99) {
            alert("Age must be between 0 and 99");
            return;
        }

        // DOB validation
        const dobDate = new Date(dob);
        if (isNaN(dobDate.getTime())) {
            alert("Invalid date of birth");
            return;
        }

        // Contact validation
        if (!/^[6789][0-9]{9}$/.test(contact)) {
            alert("Contact number must be 10 digits and start with 6, 7, 8, or 9");
            return;
        }

        // AJAX SAVE
        $.ajax({
            url: `${API_BASE}/api/profile`,
            method: "POST",
            data: {
                session_key: sessionKey,
                age,
                dob,
                contact,
                address,
                gender,
                designation,
                company
            },
            success: function (res) {
                if (res.status === "success") {
                    alert("Profile saved successfully");
                } else {
                    alert(res.message || "Failed to save profile");
                }
            },
            error: function () {
                alert("Server error while saving profile");
            }
        });

    });

    // =====================================
    // LOGOUT
    // =====================================
    $("#logoutBtn").click(function () {
        $.post(
            `${API_BASE}/api/logout`,
            { session_key: sessionKey },
            function () {
                localStorage.clear();
                window.location.href = "/pages/signUp.html";
            }
        );
    });

});
