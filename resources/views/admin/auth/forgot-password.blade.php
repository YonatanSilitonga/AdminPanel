<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password — Toba Tourism Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0a0a0a;
            overflow: hidden;
        }

        .wrapper {
            position: relative;
            width: 100vw;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .wrapper::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url('{{ asset("images/background.jpeg") }}') center/cover no-repeat;
            filter: brightness(0.85);
            z-index: 0;
        }

        .wrapper::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(0,0,0,0.15) 0%, rgba(0,0,0,0.05) 50%, rgba(0,0,0,0.1) 100%);
            z-index: 1;
        }

        .card {
            position: relative;
            z-index: 2;
            width: 90%;
            max-width: 420px;
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 20px;
            padding: 44px 40px;
            box-shadow: 0 8px 40px rgba(0,0,0,0.2);
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .icon-wrap {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            background: rgba(77,168,255,0.15);
            border: 1px solid rgba(77,168,255,0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 24px;
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 8px;
        }

        .card-subtitle {
            font-size: 0.84rem;
            color: rgba(255,255,255,0.5);
            margin-bottom: 28px;
            line-height: 1.6;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 0.82rem;
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            line-height: 1.5;
        }

        .alert svg { flex-shrink: 0; margin-top: 1px; }

        .alert-success {
            background: rgba(34,197,94,0.12);
            color: #86efac;
            border: 1px solid rgba(34,197,94,0.25);
        }

        .alert-info {
            background: rgba(77,168,255,0.12);
            color: #93c5fd;
            border: 1px solid rgba(77,168,255,0.25);
        }

        .alert-error {
            background: rgba(239,68,68,0.12);
            color: #fca5a5;
            border: 1px solid rgba(239,68,68,0.25);
        }

        .dev-token {
            margin-top: 10px;
            padding: 10px 14px;
            background: rgba(0,0,0,0.3);
            border-radius: 8px;
            font-size: 0.78rem;
            font-family: monospace;
            word-break: break-all;
            color: #fde68a;
            border: 1px solid rgba(253,230,138,0.2);
        }

        .dev-token strong {
            display: block;
            color: #fbbf24;
            margin-bottom: 4px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .form-group { margin-bottom: 20px; }

        .form-label {
            display: block;
            font-size: 0.82rem;
            font-weight: 500;
            color: rgba(255,255,255,0.8);
            margin-bottom: 8px;
        }

        .form-input {
            width: 100%;
            padding: 13px 16px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 12px;
            color: #fff;
            font-family: 'Poppins', sans-serif;
            font-size: 0.9rem;
            outline: none;
            transition: all 0.3s ease;
        }

        .form-input::placeholder { color: rgba(255,255,255,0.3); }

        .form-input:focus {
            border-color: rgba(77,168,255,0.6);
            background: rgba(255,255,255,0.12);
            box-shadow: 0 0 0 3px rgba(77,168,255,0.15);
        }

        .form-input.input-error { border-color: rgba(239,68,68,0.5); }

        .error-text {
            font-size: 0.75rem;
            color: #fca5a5;
            margin-top: 6px;
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #065f46, #047857, #059669);
            color: #fff;
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
            font-weight: 600;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 4px;
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, #047857, #059669, #065f46);
            box-shadow: 0 8px 25px rgba(5,150,105,0.35);
            transform: translateY(-1px);
        }

        .btn-submit:active { transform: translateY(0); }

        .back-link {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            margin-top: 24px;
            font-size: 0.82rem;
            color: rgba(255,255,255,0.45);
            text-decoration: none;
            transition: color 0.3s;
        }

        .back-link:hover { color: #93c5fd; }

        .page-footer {
            position: fixed;
            bottom: 24px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 2;
            font-size: 0.75rem;
            color: rgba(255,255,255,0.3);
        }

        @media (max-width: 480px) {
            .card { padding: 32px 24px; }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">

            <div class="icon-wrap">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#4da8ff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
            </div>

            <h2 class="card-title">Lupa Password?</h2>
            <p class="card-subtitle">Masukkan email akun admin Anda. Kami akan mengirimkan link untuk reset password.</p>

            {{-- Status success --}}
            @if(session('status'))
                <div class="alert alert-success">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            {{-- Dev mode: tampilkan reset URL langsung karena MAIL_MAILER=log --}}
            @if(session('dev_reset_url'))
                <div class="alert alert-info">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <div>
                        <span>Mode Development: email tidak terkirim (MAIL_MAILER=log). Gunakan link reset di bawah ini langsung.</span>
                        <div class="dev-token">
                            <strong>🔗 Link Reset Password</strong>
                            <a href="{{ session('dev_reset_url') }}" style="color:#93c5fd; word-break:break-all;">{{ session('dev_reset_url') }}</a>
                        </div>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-error">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.forgot-password.post') }}">
                @csrf
                <div class="form-group">
                    <label for="email" class="form-label">Alamat Email</label>
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

                <button type="submit" class="btn-submit">Kirim Link Reset</button>
            </form>

            <a href="{{ route('admin.login') }}" class="back-link">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
                </svg>
                Kembali ke halaman login
            </a>
        </div>

        <div class="page-footer">
            &copy; {{ date('Y') }} Aplikasi Wisata Toba. Hak Cipta Dilindungi.
        </div>
    </div>
</body>
</html>
