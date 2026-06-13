<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password — Toba Tourism Admin</title>
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

        .alert-error {
            background: rgba(239,68,68,0.12);
            color: #fca5a5;
            border: 1px solid rgba(239,68,68,0.25);
        }

        .form-group { margin-bottom: 18px; }

        .form-label {
            display: block;
            font-size: 0.82rem;
            font-weight: 500;
            color: rgba(255,255,255,0.8);
            margin-bottom: 8px;
        }

        .input-wrapper { position: relative; }

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
        .form-input.has-toggle { padding-right: 48px; }

        .error-text {
            font-size: 0.75rem;
            color: #fca5a5;
            margin-top: 6px;
        }

        .password-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: rgba(255,255,255,0.4);
            transition: color 0.3s;
            display: flex;
            align-items: center;
            padding: 4px;
        }

        .password-toggle:hover { color: rgba(255,255,255,0.8); }
        .password-toggle.active { color: #4da8ff; }

        /* Password strength indicator */
        .strength-bar {
            display: flex;
            gap: 4px;
            margin-top: 8px;
        }

        .strength-segment {
            flex: 1;
            height: 3px;
            border-radius: 3px;
            background: rgba(255,255,255,0.1);
            transition: background 0.3s;
        }

        .strength-label {
            font-size: 0.72rem;
            margin-top: 5px;
            color: rgba(255,255,255,0.4);
            min-height: 16px;
        }

        .req-list {
            margin-top: 10px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4px 12px;
        }

        .req-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.72rem;
            color: rgba(255,255,255,0.4);
            transition: color 0.3s;
        }

        .req-item svg { flex-shrink: 0; transition: stroke 0.3s; }

        .req-item.met {
            color: #86efac;
        }

        .req-item.met svg { stroke: #86efac; }

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
            margin-top: 8px;
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, #047857, #059669, #065f46);
            box-shadow: 0 8px 25px rgba(5,150,105,0.35);
            transform: translateY(-1px);
        }

        .btn-submit:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

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
            .req-list { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">

            <div class="icon-wrap">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#4da8ff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
            </div>

            <h2 class="card-title">Buat Password Baru</h2>
            <p class="card-subtitle">Masukkan password baru untuk akun admin Anda.</p>

            @if($errors->any())
                <div class="alert alert-error">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.reset-password.post') }}" id="resetForm">
                @csrf
                <input type="hidden" name="token" value="{{ $token ?? '' }}">

                {{-- Email --}}
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

                {{-- Password baru --}}
                <div class="form-group">
                    <label for="password" class="form-label">Password Baru</label>
                    <div class="input-wrapper">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-input has-toggle @error('password') input-error @enderror"
                            placeholder="••••••••"
                            required
                            oninput="checkStrength(this.value)"
                        >
                        <button type="button" class="password-toggle" onclick="togglePwd('password', this)">
                            <svg class="eye-show" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            <svg class="eye-hide" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                                <line x1="1" y1="1" x2="23" y2="23"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Strength bar --}}
                    <div class="strength-bar" id="strengthBar">
                        <div class="strength-segment" id="seg1"></div>
                        <div class="strength-segment" id="seg2"></div>
                        <div class="strength-segment" id="seg3"></div>
                        <div class="strength-segment" id="seg4"></div>
                    </div>
                    <div class="strength-label" id="strengthLabel"></div>

                    {{-- Requirements --}}
                    <div class="req-list">
                        <div class="req-item" id="req-len">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/></svg>
                            Min. 8 karakter
                        </div>
                        <div class="req-item" id="req-upper">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/></svg>
                            Huruf kapital
                        </div>
                        <div class="req-item" id="req-num">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/></svg>
                            Angka
                        </div>
                        <div class="req-item" id="req-sym">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/></svg>
                            Simbol (!@#$%^&*)
                        </div>
                    </div>

                    @error('password')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Konfirmasi password --}}
                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                    <div class="input-wrapper">
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="form-input has-toggle"
                            placeholder="••••••••"
                            required
                            oninput="checkMatch()"
                        >
                        <button type="button" class="password-toggle" onclick="togglePwd('password_confirmation', this)">
                            <svg class="eye-show" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            <svg class="eye-hide" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                                <line x1="1" y1="1" x2="23" y2="23"/>
                            </svg>
                        </button>
                    </div>
                    <p class="error-text" id="matchError" style="display:none">Password tidak cocok</p>
                </div>

                <button type="submit" class="btn-submit" id="submitBtn">Simpan Password Baru</button>
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

    <script>
        const CHECK_ICON = `<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>`;
        const CIRCLE_ICON = `<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/></svg>`;

        const COLORS = { weak: '#ef4444', fair: '#f97316', good: '#eab308', strong: '#22c55e' };

        function checkStrength(val) {
            const hasLen   = val.length >= 8;
            const hasUpper = /[A-Z]/.test(val);
            const hasNum   = /[0-9]/.test(val);
            const hasSym   = /[!@#$%^&*]/.test(val);

            setReq('req-len',   hasLen);
            setReq('req-upper', hasUpper);
            setReq('req-num',   hasNum);
            setReq('req-sym',   hasSym);

            const score = [hasLen, hasUpper, hasNum, hasSym].filter(Boolean).length;
            const segs  = ['seg1','seg2','seg3','seg4'];
            const labels = ['', 'Lemah', 'Cukup', 'Baik', 'Kuat'];
            const colors = ['', COLORS.weak, COLORS.fair, COLORS.good, COLORS.strong];

            segs.forEach((id, i) => {
                document.getElementById(id).style.background = i < score ? colors[score] : 'rgba(255,255,255,0.1)';
            });

            document.getElementById('strengthLabel').textContent = val.length > 0 ? labels[score] : '';
            document.getElementById('strengthLabel').style.color = val.length > 0 ? colors[score] : 'rgba(255,255,255,0.4)';

            checkMatch();
        }

        function setReq(id, met) {
            const el = document.getElementById(id);
            el.classList.toggle('met', met);
            el.querySelector('svg').outerHTML; // trigger repaint
            el.innerHTML = (met ? CHECK_ICON : CIRCLE_ICON) + el.innerHTML.replace(/<svg[\s\S]*?<\/svg>/, '');
        }

        function checkMatch() {
            const pwd  = document.getElementById('password').value;
            const conf = document.getElementById('password_confirmation').value;
            const err  = document.getElementById('matchError');
            const btn  = document.getElementById('submitBtn');
            const allMet = pwd.length >= 8 && /[A-Z]/.test(pwd) && /[0-9]/.test(pwd) && /[!@#$%^&*]/.test(pwd);

            if (conf.length > 0 && pwd !== conf) {
                err.style.display = 'block';
                btn.disabled = true;
            } else {
                err.style.display = 'none';
                btn.disabled = !(allMet && conf.length > 0 && pwd === conf);
            }
        }

        function togglePwd(fieldId, btn) {
            const input = document.getElementById(fieldId);
            const show  = btn.querySelector('.eye-show');
            const hide  = btn.querySelector('.eye-hide');
            if (input.type === 'password') {
                input.type = 'text';
                show.style.display = 'none';
                hide.style.display = 'block';
                btn.classList.add('active');
            } else {
                input.type = 'password';
                show.style.display = 'block';
                hide.style.display = 'none';
                btn.classList.remove('active');
            }
        }

        // Initial state: disable submit
        document.getElementById('submitBtn').disabled = true;
    </script>
</body>
</html>
