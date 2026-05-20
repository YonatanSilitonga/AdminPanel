<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toba Tourism - Admin Panel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0a0a0a;
            overflow: hidden;
        }

        /* Full-screen background */
        .login-wrapper {
            position: relative;
            width: 100vw;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-wrapper::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url('{{ asset("images/background.jpeg") }}') center/cover no-repeat;
            filter: brightness(0.85);
            z-index: 0;
        }

        .login-wrapper::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(0,0,0,0.15) 0%, rgba(0,0,0,0.05) 50%, rgba(0,0,0,0.1) 100%);
            z-index: 1;
        }

        /* Main container */
        .login-container {
            position: relative;
            z-index: 2;
            display: flex;
            width: 90%;
            max-width: 1100px;
            min-height: 600px;
            align-items: center;
            animation: fadeInUp 0.8s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Left side — branding */
        .login-left {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px 50px;
            position: relative;
        }

        .login-left::before {
            display: none;
        }

        .login-left > * {
            position: relative;
            z-index: 1;
        }

        .brand-logo {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 32px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            animation: fadeIn 1s ease-out 0.3s both;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .tagline {
            font-size: 3rem;
            font-weight: 800;
            color: #ffffff;
            line-height: 1.15;
            margin-bottom: 20px;
            text-shadow: 0 2px 20px rgba(0, 0, 0, 0.3);
            animation: fadeIn 1s ease-out 0.5s both;
        }

        .tagline-sub {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.95);
            font-weight: 400;
            line-height: 1.7;
            max-width: 380px;
            text-shadow: 0 1px 8px rgba(0, 0, 0, 0.5);
            animation: fadeIn 1s ease-out 0.7s both;
        }

        .tagline-divider {
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, #4da8ff, #80c4ff);
            border-radius: 3px;
            margin: 24px 0;
            animation: fadeIn 1s ease-out 0.6s both;
        }

        /* Right side — form */
        .login-right {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 50px 40px;
            position: relative;
        }

        .login-right::before {
            display: none;
        }

        .form-card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 400px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 20px;
            padding: 40px 36px;
            box-shadow: 0 8px 40px rgba(0, 0, 0, 0.2);
            animation: fadeIn 1s ease-out 0.4s both;
        }

        .form-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 8px;
        }

        .form-subtitle {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.55);
            margin-bottom: 32px;
        }

        /* Alerts */
        .alert {
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 0.82rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .alert-success {
            background: rgba(34, 197, 94, 0.15);
            color: #86efac;
            border: 1px solid rgba(34, 197, 94, 0.25);
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.15);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.25);
        }

        /* Form fields */
        .form-group {
            margin-bottom: 22px;
        }

        .form-label {
            display: block;
            font-size: 0.82rem;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 8px;
        }

        .form-input {
            width: 100%;
            padding: 14px 16px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            color: #ffffff;
            font-family: 'Poppins', sans-serif;
            font-size: 0.9rem;
            outline: none;
            transition: all 0.3s ease;
        }

        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }

        .form-input:focus {
            border-color: rgba(77, 168, 255, 0.6);
            background: rgba(255, 255, 255, 0.12);
            box-shadow: 0 0 0 3px rgba(77, 168, 255, 0.15);
        }

        .form-input.input-error {
            border-color: rgba(239, 68, 68, 0.5);
        }

        .input-wrapper {
            position: relative;
        }

        .error-text {
            font-size: 0.75rem;
            color: #fca5a5;
            margin-top: 6px;
        }

        /* Password toggle */
        .password-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: rgba(255, 255, 255, 0.4);
            transition: color 0.3s;
            display: flex;
            align-items: center;
            padding: 4px;
        }

        .password-toggle:hover {
            color: rgba(255, 255, 255, 0.8);
        }

        .password-toggle.active {
            color: #4da8ff;
        }

        /* Submit button */
        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #065f46, #047857, #059669);
            color: #ffffff;
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
            font-weight: 600;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 8px;
            letter-spacing: 0.3px;
            position: relative;
            overflow: hidden;
        }

        .btn-submit::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.1), transparent);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, #047857, #059669, #065f46);
            box-shadow: 0 8px 25px rgba(5, 150, 105, 0.35);
            transform: translateY(-1px);
        }

        .btn-submit:hover::before {
            opacity: 1;
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        /* Footer link */
        .form-footer {
            text-align: center;
            margin-top: 24px;
            font-size: 0.82rem;
            color: rgba(255, 255, 255, 0.45);
        }

        .form-footer a {
            color: #93c5fd;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        .form-footer a:hover {
            color: #bfdbfe;
        }

        /* Page footer */
        .page-footer {
            position: absolute;
            bottom: 24px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 2;
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.3);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                width: 95%;
                min-height: auto;
                max-width: 480px;
            }

            .login-left {
                padding: 40px 30px 30px;
                text-align: center;
                align-items: center;
            }

            .tagline {
                font-size: 2rem;
            }

            .tagline-sub {
                font-size: 0.9rem;
            }

            .tagline-divider {
                margin: 16px auto;
            }

            .brand-logo {
                width: 60px;
                height: 60px;
                margin-bottom: 20px;
            }

            .login-right {
                padding: 30px 24px 40px;
            }

            .form-card {
                padding: 30px 24px;
            }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-container">
            <!-- Left Side: Login Form -->
            <div class="login-right">
                <div class="form-card">
                    <h2 class="form-title">Masuk</h2>
                    <p class="form-subtitle">Masuk ke dashboard admin Anda</p>

                    @if(session('success'))
                        <div class="alert alert-success">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-error">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.login.post') }}">
                        @csrf
                        <!-- Email -->
                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                class="form-input @error('email') input-error @enderror"
                                placeholder="admin@tobatourism.id"
                                required
                                autofocus
                            >
                            @error('email')
                                <p class="error-text">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="form-group">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-wrapper">
                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    class="form-input @error('password') input-error @enderror"
                                    placeholder="••••••••"
                                    required
                                    style="padding-right: 48px;"
                                >
                                <button type="button" class="password-toggle" id="toggleBtn" onclick="togglePasswordVisibility()">
                                    <svg id="eyeIcon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                    <svg id="eyeOffIcon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                                        <line x1="1" y1="1" x2="23" y2="23"/>
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <p class="error-text">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit -->
                        <button type="submit" class="btn-submit">Masuk</button>
                    </form>

                    <div class="form-footer">
                        Lupa password? <a href="#">Hubungi Superadmin</a>
                    </div>
                </div>
            </div>

            <!-- Right Side: Logo + Tagline -->
            <div class="login-left">
                <img src="{{ asset('images/logo.jpeg') }}" alt="Toba Tourism Logo" class="brand-logo">
                <h1 class="tagline">Jelajahi<br>Keindahan Toba</h1>
                <div class="tagline-divider"></div>
                <p class="tagline-sub">
                    Kelola destinasi wisata Danau Toba dengan mudah melalui panel admin yang terintegrasi.
                </p>
            </div>
        </div>

        <div class="page-footer">
            &copy; {{ date('Y') }} Aplikasi Wisata Toba. Hak Cipta Dilindungi.
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            const eyeOffIcon = document.getElementById('eyeOffIcon');
            const toggleBtn = document.getElementById('toggleBtn');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.style.display = 'none';
                eyeOffIcon.style.display = 'block';
                toggleBtn.classList.add('active');
            } else {
                passwordInput.type = 'password';
                eyeIcon.style.display = 'block';
                eyeOffIcon.style.display = 'none';
                toggleBtn.classList.remove('active');
            }
        }
    </script>
</body>
</html>