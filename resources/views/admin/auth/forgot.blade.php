@php
    // Same banner source as login page
    $bannerUrl = ($banner && $banner->image_url)
        ? $banner->image_url
        : config('app.url') . '/assets/images/login-pic.jpg';

    // Same logo source as login page
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

    <meta name="robots" content="noindex, nofollow" />

    <link
        href="{{ config('app.url') }}/assets/images/favicon.png"
        rel="shortcut icon"
    />

    <!-- Font Awesome -->
    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css"
        rel="stylesheet"
    />

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    />

    <!-- Existing CSS -->
    <link
        rel="stylesheet"
        href="{{ config('app.url') }}/assets/css/style.css"
    />

    <link
        rel="stylesheet"
        href="{{ config('app.url') }}/assets/css/dashboard-style.css"
    />

    <!-- Poppins -->
    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    />

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    />

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet"
    />


    <style>

        /* =========================================
           RESET
        ========================================= */

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
        }


        /* =========================================
           MAIN PAGE
           SAME BACKGROUND AS LOGIN
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

            /*
             * SAME IMAGE AS LOGIN
             */
            background-image: url("{{ $bannerUrl }}");

            background-size: cover;

            background-position: center center;

            background-repeat: no-repeat;
        }


        /* =========================================
           LIGHT OVERLAY
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

            z-index: 1;

            pointer-events: none;
        }


        /* =========================================
           CARD WRAPPER
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

            padding: 38px 42px 40px;

            border-radius: 18px;

            /*
             * Transparent glass
             * Background image visible through card
             */
            background: rgba(255, 255, 255, 0.08);

            border: 1px solid
                rgba(255, 255, 255, 0.28);

            border-top-color:
                rgba(255, 255, 255, 0.42);

            -webkit-backdrop-filter:
                blur(10px) saturate(115%);

            backdrop-filter:
                blur(10px) saturate(115%);

            box-shadow:
                0 25px 60px
                rgba(0, 0, 0, 0.28),

                0 8px 22px
                rgba(0, 0, 0, 0.12),

                inset 0 1px 0
                rgba(255, 255, 255, 0.20),

                inset 0 -1px 0
                rgba(255, 255, 255, 0.05);

            overflow: hidden;
        }


        /* =========================================
           CARD TOP SHINE
        ========================================= */

        .rc-card::before {
            content: "";

            position: absolute;

            top: 0;
            left: 0;
            right: 0;

            height: 1px;

            background:
                rgba(255, 255, 255, 0.35);

            pointer-events: none;
        }


        /* =========================================
           LOGO
        ========================================= */

        .rc-logo {
            width: 100%;

            display: flex;

            align-items: center;

            justify-content: center;

            margin: 0 auto 26px;
        }


        .rc-logo img {
            width: 180px;

            max-width: 70%;

            height: auto;

            display: block;

            object-fit: contain;

            border-radius: 6px;

            filter:
                drop-shadow(
                    0 7px 16px
                    rgba(0, 0, 0, 0.40)
                );
        }


        /* =========================================
           HEADER
        ========================================= */

        .rc-head {
            text-align: center;

            margin-bottom: 28px;
        }


        .rc-head h1 {
            margin: 0 0 10px;

            color: #ffffff;

            font-size: 25px;

            line-height: 1.35;

            font-weight: 600;

            letter-spacing: -0.2px;

            text-shadow:
                0 2px 8px
                rgba(0, 0, 0, 0.28);
        }


        .rc-head p {
            margin: 0;

            color:
                rgba(255, 255, 255, 0.88);

            font-size: 14px;

            line-height: 1.65;

            font-weight: 400;

            text-shadow:
                0 1px 5px
                rgba(0, 0, 0, 0.25);
        }


        /* =========================================
           FORM
        ========================================= */

        .rc-form {
            width: 100%;
        }


        .rc-field {
            width: 100%;

            margin-bottom: 20px;
        }


        /* =========================================
           LABEL
        ========================================= */

        .rc-label {
            display: block;

            margin-bottom: 8px;

            color: #ffffff;

            font-size: 13px;

            font-weight: 500;

            text-shadow:
                0 1px 5px
                rgba(0, 0, 0, 0.30);
        }


        /* =========================================
           INPUT
        ========================================= */

        .rc-input {
            width: 100%;

            height: 52px;

            padding: 0 16px;

            border: 1px solid
                rgba(255, 255, 255, 0.30);

            border-radius: 9px;

            outline: none;

            background:
                rgba(255, 255, 255, 0.12);

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
            color:
                rgba(255, 255, 255, 0.70);
        }


        .rc-input:hover {
            background:
                rgba(255, 255, 255, 0.15);

            border-color:
                rgba(255, 255, 255, 0.40);
        }


        .rc-input:focus {
            background:
                rgba(255, 255, 255, 0.16);

            border-color:
                rgba(255, 255, 255, 0.70);

            box-shadow:
                0 0 0 3px
                rgba(255, 255, 255, 0.08);
        }


        /* =========================================
           BUTTON ROW
        ========================================= */

        .rc-button-row {
            display: flex;

            align-items: center;

            gap: 12px;

            flex-wrap: wrap;
        }


        /* =========================================
           PRIMARY BUTTON
        ========================================= */

        .rc-btn {
            min-height: 52px;

            padding: 0 28px;

            border: none;

            border-radius: 9px;

            outline: none;

            cursor: pointer;

            background:
                rgba(255, 255, 255, 0.92);

            color: #17282b;

            font-family: "Poppins", sans-serif;

            font-size: 14px;

            font-weight: 600;

            text-decoration: none;

            display: inline-flex;

            align-items: center;

            justify-content: center;

            box-shadow:
                0 8px 20px
                rgba(0, 0, 0, 0.20);

            transition:
                transform 0.2s ease,
                background 0.2s ease,
                box-shadow 0.2s ease;
        }


        .rc-btn:hover {
            background: #ffffff;

            color: #17282b;

            transform: translateY(-1px);

            box-shadow:
                0 10px 24px
                rgba(0, 0, 0, 0.26);
        }


        .rc-btn:active {
            transform: translateY(0);
        }


        /* =========================================
           SECONDARY BUTTON
        ========================================= */

        .rc-btn-secondary {
            min-height: 52px;

            padding: 0 28px;

            border-radius: 9px;

            border: 1px solid
                rgba(255, 255, 255, 0.30);

            background:
                rgba(255, 255, 255, 0.10);

            color: #ffffff;

            font-family: "Poppins", sans-serif;

            font-size: 14px;

            font-weight: 500;

            text-decoration: none;

            display: inline-flex;

            align-items: center;

            justify-content: center;

            transition:
                background 0.2s ease,
                border-color 0.2s ease;
        }


        .rc-btn-secondary:hover {
            color: #ffffff;

            background:
                rgba(255, 255, 255, 0.17);

            border-color:
                rgba(255, 255, 255, 0.45);
        }


        /* =========================================
           DYNAMIC RESET FORM
        ========================================= */

        #auth-form-container {
            width: 100%;
        }


        #auth-form-container form {
            width: 100%;
        }


        /* =========================================
           ERROR
        ========================================= */

        .error {
            display: block;

            margin-top: 6px;

            color: #ffe0e0 !important;

            font-size: 11px;
        }


        /* =========================================
           TABLET
        ========================================= */

        @media (min-width: 768px)
        and (max-width: 1199px) {

            .rc-page {
                justify-content: flex-end;

                padding:
                    35px 7vw 35px 24px;
            }

            .rc-wrap {
                max-width: 580px;
            }

            .rc-card {
                padding:
                    35px 34px 35px;
            }

            .rc-logo img {
                width: 170px;
            }

            .rc-head h1 {
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

                background-position:
                    center center;
            }

            .rc-wrap {
                max-width: 100%;
            }

            .rc-card {
                padding:
                    30px 20px 28px;

                border-radius: 15px;

                background:
                    rgba(255, 255, 255, 0.08);

                -webkit-backdrop-filter:
                    blur(9px) saturate(115%);

                backdrop-filter:
                    blur(9px) saturate(115%);
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

            .rc-head h1 {
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

            .rc-btn,
            .rc-btn-secondary {
                width: 100%;

                min-height: 50px;

                padding: 0 20px;

                font-size: 13px;
            }

            .rc-button-row {
                flex-direction: column;

                width: 100%;
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
                padding:
                    26px 16px 24px;

                border-radius: 14px;
            }

            .rc-logo {
                margin-bottom: 18px;
            }

            .rc-logo img {
                width: 135px;
            }

            .rc-head h1 {
                font-size: 19px;
            }

            .rc-head p {
                font-size: 12px;
            }

            .rc-input {
                height: 48px;
            }

            .rc-btn,
            .rc-btn-secondary {
                min-height: 48px;
            }
        }

    </style>

</head>


<body>

<main class="rc-page">

    <div class="rc-wrap">

        <div class="rc-card">


            <!-- =================================
                 SAME LOGO AS LOGIN
            ================================== -->

            <div class="rc-logo">

                <img
                    src="{{ $logoUrl }}"
                    alt="Paraben Corporations Logo"
                />

            </div>


            <!-- =================================
                 HEADER
            ================================== -->

            <div class="rc-head">

                <h1>
                    Welcome to Paraben Corporation
                </h1>

                <p>
                    Please enter the email address associated
                    with your account to receive a reset link.
                </p>

            </div>


            <!-- =================================
                 AUTH FORM CONTAINER
            ================================== -->

            <div id="auth-form-container">


                <!-- =================================
                     STEP 1: EMAIL
                ================================== -->

                <form
                    id="email-form"
                    class="rc-form"
                >

                    <div class="rc-field">

                        <label
                            class="rc-label"
                            for="email-field"
                        >
                            Email Address
                        </label>

                        <input
                            id="email-field"
                            type="email"
                            name="email"
                            class="rc-input"
                            placeholder="Enter Your Email"
                            autocomplete="email"
                            required
                        />

                    </div>


                    <div class="rc-button-row">

                        <button
                            type="submit"
                            class="rc-btn"
                        >
                            Submit
                        </button>


                        <a
                            href="{{ route('admin.login') }}"
                            class="rc-btn-secondary"
                        >
                            Back to Login
                        </a>

                    </div>

                </form>

            </div>

        </div>

    </div>

</main>


<!-- =========================================
     JQUERY
========================================= -->

<script
    src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js">
</script>


<script>

$(document).ready(function () {


    /* =========================================
       STEP 1: SEND OTP
    ========================================= */

    $('#email-form').on('submit', function (e) {

        e.preventDefault();

        const form = $(this);

        const email =
            form.find('input[name="email"]').val();


        if (!email) {

            alert(
                'Please enter your email address.'
            );

            return;
        }


        $.ajax({

            url:
                "{{ route('admin.send.otp') }}",

            method: "POST",

            data:
                form.serialize(),

            headers: {

                'X-CSRF-TOKEN':
                    '{{ csrf_token() }}'

            },


            success: function (response) {

                if (response.success) {

                    $('#auth-form-container').html(`

                        <form
                            id="otp-form"
                            class="rc-form"
                        >

                            <div class="rc-field">

                                <label
                                    class="rc-label"
                                    for="otp-field"
                                >
                                    OTP
                                </label>

                                <input
                                    id="otp-field"
                                    type="text"
                                    name="otp"
                                    class="rc-input"
                                    placeholder="Enter OTP"
                                    maxlength="10"
                                    autocomplete="one-time-code"
                                    required
                                />

                                <input
                                    type="hidden"
                                    name="email"
                                    value="${email}"
                                />

                            </div>


                            <div class="rc-button-row">

                                <button
                                    type="submit"
                                    class="rc-btn"
                                >
                                    Submit
                                </button>

                            </div>

                        </form>

                    `);

                } else {

                    alert(
                        response.message ||
                        'Something went wrong'
                    );

                }

            },


            error: function (xhr) {

                alert(
                    'Error: ' +
                    xhr.responseText
                );

            }

        });

    });



    /* =========================================
       STEP 2: VERIFY OTP
    ========================================= */

    $(document).on(
        'submit',
        '#otp-form',
        function (e) {

            e.preventDefault();

            const form = $(this);


            $.ajax({

                url:
                    "{{ route('admin.verify.otp') }}",

                method: "POST",

                data:
                    form.serialize(),

                headers: {

                    'X-CSRF-TOKEN':
                        '{{ csrf_token() }}'

                },


                success:
                    function (response) {

                        if (
                            response.success &&
                            response.reset_password_html
                        ) {

                            $('#auth-form-container')
                                .html(
                                    response.reset_password_html
                                );


                            /*
                             * Style the dynamically
                             * generated reset form
                             */

                            const resetForm =
                                $('#reset-password-form');


                            resetForm.addClass(
                                'rc-form'
                            );


                            resetForm
                                .find('input')
                                .addClass(
                                    'rc-input'
                                );


                            resetForm
                                .find('.form-group')
                                .addClass(
                                    'rc-field'
                                );


                            resetForm
                                .find('label')
                                .addClass(
                                    'rc-label'
                                );


                            resetForm
                                .find('button')
                                .addClass(
                                    'rc-btn'
                                );

                        } else {

                            alert(
                                response.message ||
                                'Invalid OTP'
                            );

                        }

                    },


                error:
                    function (xhr) {

                        alert(
                            'Error: ' +
                            xhr.responseText
                        );

                    }

            });

        }
    );



    /* =========================================
       STEP 3: RESET PASSWORD
    ========================================= */

    $(document).on(
        'submit',
        '#reset-password-form',
        function (e) {

            e.preventDefault();

            const form = $(this);


            $.ajax({

                url:
                    "{{ route('admin.reset.password') }}",

                method: "POST",

                data:
                    form.serialize(),

                headers: {

                    'X-CSRF-TOKEN':
                        '{{ csrf_token() }}'

                },


                success:
                    function (response) {

                        if (response.success) {

                            alert(
                                'Password reset successful! ' +
                                'Redirecting to login...'
                            );


                            window.location.href =
                                "{{ route('admin.login') }}";

                        } else {

                            alert(
                                response.message ||
                                'Password reset failed'
                            );

                        }

                    },


                error:
                    function (xhr) {

                        alert(
                            'Error: ' +
                            xhr.responseText
                        );

                    }

            });

        }
    );

});

</script>

</body>

</html>