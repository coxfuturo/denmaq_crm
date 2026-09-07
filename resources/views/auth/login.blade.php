<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - Denmaq CRM</title>

    <link rel="apple-touch-icon"
        sizes="180x180"
        href="{{ asset('admin/assets/images/favicon_io/apple-touch-icon.png') }}">

    <link rel="icon"
        type="image/png"
        sizes="32x32"
        href="{{ asset('admin/assets/images/favicon_io/favicon-32x32.png') }}">

    <link rel="icon"
        type="image/png"
        sizes="16x16"
        href="{{ asset('admin/assets/images/favicon_io/favicon-16x16.png') }}">


    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            width: 100%;
            min-height: 100%;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f6f7fb;
            color: #222;
        }


        /* =====================================
           MAIN WRAPPER
        ===================================== */

        .login-wrapper {
            width: 100%;
            min-height: 100vh;
            display: flex;
        }


        /* =====================================
           LEFT BRAND SECTION
        ===================================== */

        .brand-section {
            width: 50%;
            min-height: 100vh;

            position: relative;
            overflow: hidden;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 40px;

            background:
                radial-gradient(
                    circle at 20% 20%,
                    rgba(163, 68, 255, 0.45),
                    transparent 30%
                ),
                radial-gradient(
                    circle at 80% 80%,
                    rgba(255, 102, 0, 0.20),
                    transparent 30%
                ),
                linear-gradient(
                    135deg,
                    #21006b 0%,
                    #4b13a9 45%,
                    #6d19c9 100%
                );
        }


        .brand-section::before {
            content: "";

            position: absolute;

            width: 500px;
            height: 500px;

            border-radius: 50%;

            border: 1px solid rgba(255, 255, 255, 0.10);

            top: -220px;
            left: -200px;
        }


        .brand-section::after {
            content: "";

            position: absolute;

            width: 650px;
            height: 650px;

            border-radius: 50%;

            border: 1px solid rgba(255, 255, 255, 0.08);

            bottom: -370px;
            right: -280px;
        }


        .brand-content {
            width: 100%;
            max-width: 600px;

            position: relative;
            z-index: 2;

            text-align: center;

            padding: 30px;
        }


        .brand-logo {
            width: 260px;
            max-width: 85%;

            height: auto;

            display: block;

            margin: 0 auto 35px;

            object-fit: contain;

            filter:
                drop-shadow(
                    0 15px 30px rgba(0, 0, 0, 0.25)
                );
        }


        .brand-line {
            width: 65px;
            height: 4px;

            border-radius: 10px;

            background: #ff6500;

            margin: 20px auto 25px;
        }


        .brand-title {
            color: #ffffff;

            font-size: 34px;
            line-height: 1.2;

            font-weight: 700;

            margin-bottom: 15px;

            letter-spacing: 0.3px;
        }


        .brand-description {
            color: rgba(255, 255, 255, 0.80);

            font-size: 16px;

            line-height: 1.7;

            max-width: 480px;

            margin: 0 auto;
        }


        /* =====================================
           RIGHT LOGIN SECTION
        ===================================== */

        .login-section {
            width: 50%;
            min-height: 100vh;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 50px;

            background: #ffffff;
        }


        .login-box {
            width: 100%;
            max-width: 460px;

            margin: auto;
        }


        /* =====================================
           WELCOME TEXT
        ===================================== */

        .welcome-text {
            margin-bottom: 32px;
        }


        .welcome-text h1 {
            font-size: 32px;

            color: #202124;

            font-weight: 700;

            line-height: 1.25;

            margin-bottom: 10px;
        }


        .welcome-text p {
            color: #777;

            font-size: 15px;

            line-height: 1.6;

            margin: 0;
        }


        /* =====================================
           FORM
        ===================================== */

        .form-group {
            margin-bottom: 21px;
        }


        .form-label {
            display: block;

            font-size: 14px;

            font-weight: 600;

            color: #303030;

            margin-bottom: 8px;
        }


        .input-wrapper {
            position: relative;

            width: 100%;
        }


        .input-icon {
            position: absolute;

            left: 16px;
            top: 50%;

            transform: translateY(-50%);

            color: #999;

            font-size: 16px;

            line-height: 1;

            z-index: 2;
        }


        .form-control {
            width: 100%;

            height: 52px;

            border: 1px solid #dedee5;

            border-radius: 9px;

            background: #fafafd;

            padding: 0 16px 0 45px;

            font-size: 15px;

            color: #222;

            outline: none;

            transition: all 0.25s ease;
        }


        .form-control:focus {
            border-color: #5a16b8;

            background: #ffffff;

            box-shadow:
                0 0 0 4px rgba(90, 22, 184, 0.08);
        }


        .form-control::placeholder {
            color: #aaa;
        }


        /* =====================================
           PASSWORD
        ===================================== */

        .password-wrapper {
            position: relative;
        }


        .password-wrapper .form-control {
            padding-right: 50px;
        }


        .password-toggle {
            position: absolute;

            right: 14px;
            top: 50%;

            transform: translateY(-50%);

            width: 30px;
            height: 30px;

            border: none;

            background: transparent;

            cursor: pointer;

            color: #888;

            font-size: 16px;

            display: flex;
            align-items: center;
            justify-content: center;
        }


        .password-toggle:hover {
            color: #5513b2;
        }


        /* =====================================
           REMEMBER + FORGOT
        ===================================== */

        .form-options {
            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 15px;

            margin: 5px 0 25px;
        }


        .remember {
            display: flex;

            align-items: center;

            gap: 8px;

            font-size: 14px;

            color: #666;

            cursor: pointer;

            white-space: nowrap;
        }


        .remember input {
            width: 16px;
            height: 16px;

            accent-color: #5715b5;

            cursor: pointer;
        }


        .forgot-link {
            color: #5513b2;

            font-size: 14px;

            font-weight: 600;

            text-decoration: none;

            white-space: nowrap;
        }


        .forgot-link:hover {
            text-decoration: underline;
        }


        /* =====================================
           LOGIN BUTTON
        ===================================== */

        .login-btn {
            width: 100%;

            height: 53px;

            border: none;

            border-radius: 9px;

            background:
                linear-gradient(
                    135deg,
                    #4c0e9e,
                    #7119d2
                );

            color: #ffffff;

            font-size: 15px;

            font-weight: 700;

            letter-spacing: 0.2px;

            cursor: pointer;

            box-shadow:
                0 8px 20px rgba(87, 21, 181, 0.22);

            transition: all 0.25s ease;
        }


        .login-btn:hover {
            transform: translateY(-2px);

            box-shadow:
                0 12px 25px rgba(87, 21, 181, 0.30);
        }


        .login-btn:active {
            transform: translateY(0);
        }


        /* =====================================
           VALIDATION
        ===================================== */

        .invalid-feedback {
            color: #dc3545;

            font-size: 12px;

            margin-top: 6px;
        }


        .form-control.is-invalid {
            border-color: #dc3545;
        }


        /* =====================================
           COPYRIGHT
        ===================================== */

        .copyright {
            text-align: center;

            margin-top: 30px;

            color: #aaa;

            font-size: 12px;
        }


        /* =====================================
           TABLET
        ===================================== */

        @media (max-width: 1100px) {

            .brand-section {
                width: 45%;
                padding: 25px;
            }

            .login-section {
                width: 55%;
                padding: 40px;
            }

            .brand-logo {
                width: 300px;
            }

            .brand-title {
                font-size: 29px;
            }

            .brand-description {
                font-size: 15px;
            }

            .welcome-text h1 {
                font-size: 29px;
            }
        }


        /* =====================================
           TABLET
        ===================================== */

        @media (max-width: 850px) {

            .brand-section {
                width: 42%;
            }

            .login-section {
                width: 58%;
                padding: 30px;
            }

            .brand-content {
                padding: 15px;
            }

            .brand-logo {
                width: 240px;
            }

            .brand-title {
                font-size: 25px;
            }

            .brand-description {
                font-size: 14px;
            }

            .welcome-text h1 {
                font-size: 27px;
            }
        }


        /* =====================================
           MOBILE
        ===================================== */

        @media (max-width: 700px) {

            .login-wrapper {
                display: block;

                min-height: 100vh;
            }

            .brand-section {
                display: none;
            }

            .login-section {
                width: 100%;

                min-height: 100vh;

                padding: 35px 22px;

                display: flex;

                align-items: center;

                justify-content: center;
            }

            .login-box {
                width: 100%;

                max-width: 430px;
            }

            .welcome-text h1 {
                font-size: 26px;
            }

            .welcome-text p {
                font-size: 14px;
            }

            .form-group {
                margin-bottom: 19px;
            }
        }


        /* =====================================
           SMALL MOBILE
        ===================================== */

        @media (max-width: 480px) {

            .login-section {
                padding: 28px 18px;
            }

            .welcome-text h1 {
                font-size: 23px;
            }

            .welcome-text p {
                font-size: 13px;
            }

            .form-control {
                height: 50px;

                font-size: 14px;
            }

            .login-btn {
                height: 51px;
            }

            .form-options {
                flex-wrap: wrap;

                row-gap: 12px;
            }

            .forgot-link {
                margin-left: auto;
            }
        }


        /* =====================================
           VERY SMALL MOBILE
        ===================================== */

        @media (max-width: 360px) {

            .login-section {
                padding: 22px 15px;
            }

            .welcome-text h1 {
                font-size: 21px;
            }

            .form-options {
                align-items: flex-start;
            }

            .remember,
            .forgot-link {
                font-size: 13px;
            }
        }

    </style>

</head>


<body>

    <div class="login-wrapper">


        <!-- =================================
             LEFT BRANDING
        ================================== -->

        <section class="brand-section">

            <div class="brand-content">

                <img
                    src="{{ asset('admin/assets/images/denmaq.jpeg') }}"
                    alt="Denmaq"
                    class="brand-logo"
                >

                <div class="brand-line"></div>

                <h2 class="brand-title">
                    Welcome to Denmaq CRM
                </h2>

                <p class="brand-description">
                    Manage your business, customers, sales and daily
                    operations from one powerful CRM platform.
                </p>

            </div>

        </section>


        <!-- =================================
             RIGHT LOGIN
        ================================== -->

        <section class="login-section">

            <div class="login-box">


                <!-- HEADING -->

                <div class="welcome-text">

                    <h1>
                        Sign in to your account
                    </h1>

                    <p>
                        Enter your credentials to access your
                        Denmaq CRM dashboard.
                    </p>

                </div>


                <!-- LOGIN FORM -->

                <form
                    id="loginForm"
                    method="POST"
                    action="{{ route('loginDashboard') }}"
                >

                    @csrf


                    {{-- EMAIL --}}

                    <div class="form-group">

                        <label
                            for="email"
                            class="form-label"
                        >
                            Email Address
                        </label>

                        <div class="input-wrapper">

                            <span class="input-icon">
                                ✉
                            </span>

                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="form-control @error('email') is-invalid @enderror"
                                placeholder="name@example.com"
                                value="{{ old('email') }}"
                                autocomplete="email"
                                required
                            >

                        </div>

                        @error('email')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    {{-- PASSWORD --}}

                    <div class="form-group">

                        <label
                            for="password"
                            class="form-label"
                        >
                            Password
                        </label>

                        <div class="input-wrapper password-wrapper">

                            <span class="input-icon">
                                🔒
                            </span>

                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="Enter your password"
                                autocomplete="current-password"
                                required
                            >

                            <button
                                type="button"
                                class="password-toggle"
                                id="togglePassword"
                                aria-label="Show password"
                            >
                                👁
                            </button>

                        </div>

                        @error('password')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    {{-- REMEMBER ME --}}

                    <div class="form-options">

                        <label class="remember">

                            <input
                                type="checkbox"
                                id="remember"
                                name="remember"
                                value="1"
                                {{ old('remember') ? 'checked' : '' }}
                            >

                            <span>
                                Remember me
                            </span>

                        </label>

                    </div>


                    {{-- GENERAL ERROR --}}

                    @if (session('error'))

                        <div
                            style="
                                color:#dc3545;
                                font-size:14px;
                                margin-bottom:20px;
                            "
                        >
                            {{ session('error') }}
                        </div>

                    @endif


                    {{-- LOGIN BUTTON --}}

                    <button
                        type="submit"
                        class="login-btn"
                        id="loginButton"
                    >
                        Sign In
                    </button>

                </form>


                <!-- COPYRIGHT -->

                <div class="copyright">

                    © {{ date('Y') }} Denmaq.
                    All rights reserved.

                </div>


            </div>

        </section>

    </div>


    <!-- =================================
         SIMPLE JAVASCRIPT
    ================================== -->

    <script>

        // Password Show / Hide

        const togglePassword =
            document.getElementById("togglePassword");

        const password =
            document.getElementById("password");


        togglePassword.addEventListener("click", function () {

            if (password.type === "password") {

                password.type = "text";

                this.textContent = "🙈";

            } else {

                password.type = "password";

                this.textContent = "👁";

            }

        });


        // Simple Login Validation

        const loginForm =
            document.getElementById("loginForm");

        const email =
            document.getElementById("email");


        loginForm.addEventListener("submit", function (event) {

            let valid = true;


            // Email

            if (
                !email.value.trim() ||
                !email.checkValidity()
            ) {

                email.classList.add("is-invalid");

                valid = false;

            } else {

                email.classList.remove("is-invalid");

            }


            // Password

            if (
                !password.value ||
                password.value.length < 6
            ) {

                password.classList.add("is-invalid");

                valid = false;

            } else {

                password.classList.remove("is-invalid");

            }


            // Only stop submission if validation fails

            if (!valid) {

                event.preventDefault();

                return;

            }

            // IMPORTANT:
            // No preventDefault here.
            // Laravel form will submit normally.

        });


        // Remove email error

        email.addEventListener("input", function () {

            if (
                this.value.trim() &&
                this.checkValidity()
            ) {

                this.classList.remove("is-invalid");

            }

        });


        // Remove password error

        password.addEventListener("input", function () {

            if (this.value.length >= 6) {

                this.classList.remove("is-invalid");

            }

        });

    </script>


</body>

</html>
