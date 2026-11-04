@php
    // DB banner ya fallback
    $bannerUrl = ($banner && $banner->image_url)
        ? $banner->image_url
        : config('app.url') . '/assets/images/login-pic.jpg';

    // DB logo ya fallback
    $logoUrl = ($logo && $logo->image_url)
        ? $logo->image_url
        : config('app.url') . '/assets/images/logo1.png';
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <title>Paraben Corporations Admin Panel</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">

    <!-- Font Awesome -->
    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css"
        rel="stylesheet"
    >

    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <style>
        * {
            box-sizing: border-box;
        }

        html,
        body {
            width: 100%;
            min-height: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: "Poppins", sans-serif;
            background: #06191c;
            color: #ffffff;
        }

        /* =========================================
           MAIN PAGE
        ========================================= */

        .rc-page {
            position: relative;
            width: 100%;
            min-height: 100vh;

            display: flex;
            align-items: center;
            justify-content: flex-end;

            padding: 40px 9vw 40px 24px;

            overflow: hidden;

            background-color: #06191c;

            background-image: url("{{ $bannerUrl }}");
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
        }

        /* =========================================
           OPTIONAL DARK OVERLAY
           Very light so background image remains
           clearly visible
        ========================================= */

        .rc-page::before {
            content: "";
            position: absolute;
            inset: 0;

            background:
                linear-gradient(
                    90deg,
                    rgba(0, 0, 0, 0.08) 0%,
                    rgba(0, 0, 0, 0.03) 45%,
                    rgba(0, 0, 0, 0.10) 100%
                );

            pointer-events: none;
            z-index: 1;
        }

        /* =========================================
           WRAPPER
        ========================================= */

        .rc-wrap {
            position: relative;
            z-index: 2;

            width: 100%;
            max-width: 600px;
        }

        /* =========================================
           GLASS CARD
        ========================================= */

        .rc-card {
            position: relative;

            width: 100%;

            padding: 36px 42px 38px;

            border-radius: 18px;

            /*
             * LIGHT TRANSPARENT GLASS
             * Background image will remain visible
             */
            background: rgba(255, 255, 255, 0.08);

            border: 1px solid rgba(255, 255, 255, 0.28);

            border-top-color: rgba(255, 255, 255, 0.42);

            /*
             * Less blur = background image more visible
             */
            -webkit-backdrop-filter: blur(10px) saturate(115%);
            backdrop-filter: blur(10px) saturate(115%);

            box-shadow:
                0 25px 60px rgba(0, 0, 0, 0.28),
                0 8px 22px rgba(0, 0, 0, 0.12),
                inset 0 1px 0 rgba(255, 255, 255, 0.20),
                inset 0 -1px 0 rgba(255, 255, 255, 0.05);

            overflow: hidden;
        }

        /*
         * Very subtle glass shine
         */
        .rc-card::before {
            content: "";

            position: absolute;
            top: 0;
            left: 0;
            right: 0;

            height: 1px;

            background: rgba(255, 255, 255, 0.35);

            pointer-events: none;
        }

        /* =========================================
           LOGO
        ========================================= */

        .rc-logo {
            display: flex;
            align-items: center;
            justify-content: center;

            margin: 0 auto 26px;

            width: 100%;
        }

        .rc-logo img {
            width: 210px;
            max-width: 70%;

            height: auto;

            display: block;

            object-fit: contain;

            border-radius: 6px;

            filter:
                drop-shadow(
                    0 7px 16px rgba(0, 0, 0, 0.40)
                );
        }

        /* =========================================
           HEADER
        ========================================= */

        .rc-head {
            text-align: center;

            margin-bottom: 28px;
        }

        .rc-head h3 {
            margin: 0 0 10px;

            color: #ffffff;

            font-size: 25px;

            line-height: 1.35;

            font-weight: 600;

            letter-spacing: -0.2px;

            text-shadow:
                0 2px 8px rgba(0, 0, 0, 0.28);
        }

        .rc-head p {
            margin: 0;

            color: rgba(255, 255, 255, 0.88);

            font-size: 14px;

            line-height: 1.6;

            font-weight: 400;

            text-shadow:
                0 1px 5px rgba(0, 0, 0, 0.25);
        }

        /* =========================================
           ALERT
        ========================================= */

        .rc-alert {
            width: 100%;

            margin-bottom: 20px;

            padding: 11px 14px;

            border-radius: 9px;

            background: rgba(255, 255, 255, 0.12);

            border: 1px solid rgba(255, 255, 255, 0.20);

            color: #ffffff;

            font-size: 13px;

            line-height: 1.5;

            text-align: center;

            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
        }

        /* =========================================
           FIELD
        ========================================= */

        .rc-field {
            width: 100%;

            margin-bottom: 20px;
        }

        .rc-label {
            display: block;

            margin-bottom: 8px;

            color: #ffffff;

            font-size: 13px;

            font-weight: 500;

            letter-spacing: 0.1px;

            text-shadow:
                0 1px 5px rgba(0, 0, 0, 0.30);
        }

        /* =========================================
           INPUT
        ========================================= */

        .rc-input {
            width: 100%;

            height: 52px;

            padding: 0 16px;

            border: 1px solid rgba(255, 255, 255, 0.30);

            border-radius: 9px;

            outline: none;

            background: rgba(255, 255, 255, 0.12);

            color: #ffffff;

            font-family: "Poppins", sans-serif;

            font-size: 14px;

            font-weight: 400;

            transition:
                border-color 0.2s ease,
                background 0.2s ease,
                box-shadow 0.2s ease;
        }

        .rc-input::placeholder {
            color: rgba(255, 255, 255, 0.70);
        }

        .rc-input:hover {
            background: rgba(255, 255, 255, 0.15);

            border-color: rgba(255, 255, 255, 0.40);
        }

        .rc-input:focus {
            background: rgba(255, 255, 255, 0.16);

            border-color: rgba(255, 255, 255, 0.70);

            box-shadow:
                0 0 0 3px rgba(255, 255, 255, 0.08);
        }

        /* =========================================
           PASSWORD
        ========================================= */

        .rc-pass {
            position: relative;

            width: 100%;
        }

        .rc-pass .rc-input {
            padding-right: 48px;
        }

        .rc-eye {
            position: absolute;

            top: 50%;
            right: 16px;

            transform: translateY(-50%);

            color: rgba(255, 255, 255, 0.75);

            font-size: 15px;

            cursor: pointer;

            transition:
                color 0.2s ease,
                transform 0.2s ease;
        }

        .rc-eye:hover {
            color: #ffffff;

            transform: translateY(-50%) scale(1.05);
        }

        /* =========================================
           FORGOT PASSWORD
        ========================================= */

        .rc-forgot-row {
            display: flex;

            justify-content: flex-end;

            margin-top: -5px;

            margin-bottom: 22px;
        }

        .rc-forgot {
            color: #ffffff;

            font-size: 13px;

            font-weight: 500;

            text-decoration: none;

            transition:
                opacity 0.2s ease,
                text-shadow 0.2s ease;
        }

        .rc-forgot:hover {
            color: #ffffff;

            opacity: 0.85;

            text-shadow:
                0 1px 6px rgba(0, 0, 0, 0.35);
        }

        /* =========================================
           BUTTON
        ========================================= */

        .rc-btn {
            width: 100%;

            height: 52px;

            border: none;

            border-radius: 9px;

            outline: none;

            cursor: pointer;

            background: rgba(255, 255, 255, 0.92);

            color: #17282b;

            font-family: "Poppins", sans-serif;

            font-size: 14px;

            font-weight: 600;

            letter-spacing: 0.1px;

            box-shadow:
                0 8px 20px rgba(0, 0, 0, 0.20);

            transition:
                transform 0.2s ease,
                background 0.2s ease,
                box-shadow 0.2s ease;
        }

        .rc-btn:hover {
            background: #ffffff;

            transform: translateY(-1px);

            box-shadow:
                0 10px 24px rgba(0, 0, 0, 0.26);
        }

        .rc-btn:active {
            transform: translateY(0);
        }

        /* =========================================
           VALIDATION
        ========================================= */

        label.error {
            display: block;

            margin-top: 6px;

            color: #ffe1e1 !important;

            font-size: 11px !important;

            font-weight: 400 !important;
        }

        .rc-input.error {
            border-color: rgba(255, 120, 120, 0.80);
        }

        /* =========================================
           LARGE DESKTOP
        ========================================= */

        @media (min-width: 1200px) {

            .rc-page {
                padding: 40px 9vw 40px 24px;
            }

            .rc-wrap {
                max-width: 600px;
            }

            .rc-card {
                padding: 38px 42px 38px;
            }

            .rc-logo img {
                width: 210px;
            }
        }

        /* =========================================
           TABLET
        ========================================= */

        @media (min-width: 768px) and (max-width: 1199px) {

            .rc-page {
                justify-content: flex-end;

                padding: 35px 7vw 35px 24px;
            }

            .rc-wrap {
                max-width: 580px;
            }

            .rc-card {
                padding: 35px 34px 34px;
            }

            .rc-logo img {
                width: 190px;
            }

            .rc-head h3 {
                font-size: 23px;
            }
        }

        /* =========================================
           MOBILE
        ========================================= */

        @media (max-width: 767px) {

            .rc-page {
                justify-content: center;

                padding: 25px 16px;

                background-position: center center;
            }

            .rc-wrap {
                max-width: 100%;
            }

            .rc-card {
                padding: 30px 20px 28px;

                border-radius: 15px;

                /*
                 * Transparent glass on mobile
                 */
                background: rgba(255, 255, 255, 0.08);

                -webkit-backdrop-filter: blur(9px) saturate(115%);
                backdrop-filter: blur(9px) saturate(115%);
            }

            .rc-logo {
                margin-bottom: 22px;
            }

            .rc-logo img {
                width: 155px;

                max-width: 70%;
            }

            .rc-head {
                margin-bottom: 24px;
            }

            .rc-head h3 {
                font-size: 21px;

                line-height: 1.4;
            }

            .rc-head p {
                font-size: 13px;
            }

            .rc-input {
                height: 50px;

                font-size: 13px;
            }

            .rc-btn {
                height: 50px;
            }
        }

        /* =========================================
           SMALL MOBILE
        ========================================= */

        @media (max-width: 380px) {

            .rc-page {
                padding: 20px 12px;
            }

            .rc-card {
                padding: 26px 16px 24px;

                border-radius: 14px;
            }

            .rc-logo {
                margin-bottom: 18px;
            }

            .rc-logo img {
                width: 135px;
            }

            .rc-head h3 {
                font-size: 19px;
            }

            .rc-head p {
                font-size: 12px;
            }

            .rc-input {
                height: 48px;
            }

            .rc-btn {
                height: 48px;
            }
        }

        /* =========================================
           REDUCED MOTION
        ========================================= */

        @media (prefers-reduced-motion: reduce) {

            .rc-input,
            .rc-eye,
            .rc-forgot,
            .rc-btn {
                transition: none;
            }
        }
    </style>
</head>

<body>

    <main class="rc-page">

        <div class="rc-wrap">

            <div class="rc-card">

                <!-- Router Capture Logo -->
                <div class="rc-logo">
                    <img
                        src="{{ $logoUrl }}"
                        alt="Paraben Corporations Router Capture Logo"
                    >
                </div>

                <!-- Heading -->
                <div class="rc-head">

                    <h3>
                        Welcome to Paraben’s Router Capture Download Console
                    </h3>

                    <p>
                        You must have an account to login and download data.
                    </p>

                </div>

                <!-- Session Message -->
                @if(session('message'))

                    <div class="rc-alert">
                        {{ session('message') }}
                    </div>

                @endif

                <!-- Login Form -->
                <form
                    id="login-form"
                    method="post"
                    action="{{ route('admin.login.post') }}"
                    novalidate="novalidate"
                >

                    @csrf

                    <!-- Email -->
                    <div class="rc-field">

                        <label
                            class="rc-label"
                            for="email-field"
                        >
                            Email Address
                        </label>

                        <input
                            id="email-field"
                            class="rc-input"
                            type="email"
                            name="email"
                            maxlength="40"
                            placeholder="Enter Your Email"
                            autocomplete="email"
                        >

                    </div>

                    <!-- Password -->
                    <div class="rc-field">

                        <label
                            class="rc-label"
                            for="password-field"
                        >
                            Password
                        </label>

                        <div class="rc-pass">

                            <input
                                id="password-field"
                                class="rc-input"
                                type="password"
                                name="password"
                                maxlength="30"
                                placeholder="Enter your Password"
                                autocomplete="current-password"
                            >

                            <i
                                class="fas fa-eye-slash rc-eye"
                                id="eye-icon"
                                role="button"
                                tabindex="0"
                                aria-label="Show password"
                                title="Show password"
                            ></i>

                        </div>

                    </div>

                    <!-- Forgot Password -->
                    <div class="rc-forgot-row">

                        <a
                            href="{{ route('admin.forgot') }}"
                            class="rc-forgot"
                        >
                            Forgot Password?
                        </a>

                    </div>

                    <!-- Sign In -->
                    <button
                        type="submit"
                        class="rc-btn"
                    >
                        Sign in
                    </button>

                </form>

            </div>

        </div>

    </main>


    <!-- jQuery -->
    <script
        src="https://code.jquery.com/jquery-3.7.1.min.js"
    ></script>

    <!-- jQuery Validate -->
    <script
        src="https://cdn.jsdelivr.net/jquery.validation/1.19.5/jquery.validate.min.js"
    ></script>


    <script>
        $(document).ready(function () {

            /* =========================================
               PASSWORD SHOW / HIDE
            ========================================= */

            const passwordField = $("#password-field");
            const eyeIcon = $("#eye-icon");

            function togglePassword() {

                const isPassword =
                    passwordField.attr("type") === "password";

                if (isPassword) {

                    passwordField.attr("type", "text");

                    eyeIcon
                        .removeClass("fa-eye-slash")
                        .addClass("fa-eye");

                    eyeIcon.attr(
                        "aria-label",
                        "Hide password"
                    );

                    eyeIcon.attr(
                        "title",
                        "Hide password"
                    );

                } else {

                    passwordField.attr("type", "password");

                    eyeIcon
                        .removeClass("fa-eye")
                        .addClass("fa-eye-slash");

                    eyeIcon.attr(
                        "aria-label",
                        "Show password"
                    );

                    eyeIcon.attr(
                        "title",
                        "Show password"
                    );
                }
            }

            eyeIcon.on("click", function () {
                togglePassword();
            });

            eyeIcon.on("keydown", function (event) {

                if (
                    event.key === "Enter" ||
                    event.key === " "
                ) {
                    event.preventDefault();

                    togglePassword();
                }

            });


            /* =========================================
               LOGIN VALIDATION
            ========================================= */

            $("#login-form").validate({

                rules: {

                    email: {
                        required: true,
                        email: true,
                        maxlength: 40
                    },

                    password: {
                        required: true,
                        maxlength: 30
                    }

                },

                messages: {

                    email: {
                        required: "Please enter your email address.",
                        email: "Please enter a valid email address.",
                        maxlength: "Email address cannot exceed 40 characters."
                    },

                    password: {
                        required: "Please enter your password.",
                        maxlength: "Password cannot exceed 30 characters."
                    }

                },

                errorElement: "label",

                errorClass: "error",

                errorPlacement: function (
                    error,
                    element
                ) {

                    if (
                        element.attr("name") === "password"
                    ) {

                        error.insertAfter(
                            element.closest(".rc-pass")
                        );

                    } else {

                        error.insertAfter(element);

                    }

                },

                highlight: function (element) {

                    $(element).addClass("error");

                },

                unhighlight: function (element) {

                    $(element).removeClass("error");

                },

                submitHandler: function (form) {

                    form.submit();

                }

            });

        });
    </script>

</body>
</html>