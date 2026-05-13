<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Portal - เข้าสู่ระบบ</title>
    <meta name="description" content="Employee Portal - เข้าสู่ระบบ">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --bg: #0a0e1a;
            --surface: rgba(255,255,255,0.04);
            --border: rgba(255,255,255,0.08);
            --text: #e2e8f0;
            --text-muted: #64748b;
            --accent: #10b981;
            --accent-glow: rgba(16,185,129,0.3);
            --error: #ef4444;
            --success: #22c55e;
            --radius: 12px;
        }
        body {
            font-family: 'Inter', -apple-system, sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* Background effects */
        .bg-gradient {
            position: fixed; inset: 0;
            background:
                radial-gradient(ellipse 80% 60% at 20% 80%, rgba(16,185,129,0.12), transparent),
                radial-gradient(ellipse 60% 50% at 80% 20%, rgba(6,182,212,0.1), transparent),
                radial-gradient(ellipse 50% 40% at 50% 50%, rgba(16,185,129,0.05), transparent);
            z-index: 0;
        }
        .orb { position: fixed; border-radius: 50%; filter: blur(80px); opacity: 0.4; animation: float 20s ease-in-out infinite; z-index: 0; }
        .orb-1 { width: 400px; height: 400px; background: rgba(16,185,129,0.15); top: -100px; right: -100px; }
        .orb-2 { width: 350px; height: 350px; background: rgba(6,182,212,0.12); bottom: -80px; left: -80px; animation-delay: -7s; }
        .orb-3 { width: 200px; height: 200px; background: rgba(16,185,129,0.1); top: 60%; left: 50%; animation-delay: -14s; }
        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -30px) scale(1.05); }
            66% { transform: translate(-20px, 20px) scale(0.95); }
        }
        .grid-overlay {
            position: fixed; inset: 0;
            background-image: linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px),
                              linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px);
            background-size: 60px 60px;
            z-index: 0;
        }

        /* Login Container */
        .login-container { position: relative; z-index: 1; width: 100%; max-width: 420px; padding: 20px; }
        .login-card {
            background: rgba(15,23,42,0.8);
            backdrop-filter: blur(40px);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 40px 36px;
            box-shadow: 0 0 80px rgba(16,185,129,0.06), 0 25px 50px rgba(0,0,0,0.3);
        }

        /* Brand */
        .brand { text-align: center; margin-bottom: 32px; }
        .brand-icon {
            width: 64px; height: 64px;
            background: linear-gradient(135deg, #10b981, #06b6d4);
            border-radius: 16px;
            display: inline-flex; align-items: center; justify-content: center;
            margin-bottom: 16px;
            box-shadow: 0 8px 32px rgba(16,185,129,0.3);
        }
        .brand-icon svg { width: 32px; height: 32px; color: #fff; }
        .brand h1 { font-size: 22px; font-weight: 700; color: #f1f5f9; letter-spacing: -0.02em; }
        .brand p { font-size: 13px; color: var(--text-muted); margin-top: 6px; }

        /* Alert */
        .alert {
            display: flex; align-items: center; gap: 10px;
            padding: 12px 16px; border-radius: 10px; margin-bottom: 20px;
            font-size: 13px; font-weight: 500;
            animation: slideDown 0.3s ease;
        }
        .alert svg { width: 18px; height: 18px; flex-shrink: 0; }
        .alert-error { background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.25); color: #fca5a5; }
        .alert-success { background: rgba(34,197,94,0.12); border: 1px solid rgba(34,197,94,0.25); color: #86efac; }
        @keyframes slideDown { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }

        /* Form */
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; font-size: 13px; font-weight: 500; color: #94a3b8; margin-bottom: 8px; }
        .input-wrapper { position: relative; }
        .form-input {
            width: 100%; padding: 13px 16px 13px 44px;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: var(--radius);
            color: #f1f5f9; font-size: 14px; font-family: inherit;
            transition: all 0.2s;
            outline: none;
        }
        .form-input::placeholder { color: #475569; }
        .form-input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-glow); background: rgba(255,255,255,0.06); }
        .input-icon {
            position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
            color: #475569; pointer-events: none; transition: color 0.2s;
        }
        .input-icon svg { width: 18px; height: 18px; }
        .form-input:focus ~ .input-icon { color: var(--accent); }
        .password-toggle {
            position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
            background: none; border: none; color: #475569; cursor: pointer; padding: 4px;
            transition: color 0.2s;
        }
        .password-toggle:hover { color: #94a3b8; }
        .password-toggle svg { width: 18px; height: 18px; }

        /* Submit */
        .btn-submit {
            width: 100%; padding: 14px;
            background: linear-gradient(135deg, #10b981, #059669);
            border: none; border-radius: var(--radius);
            color: #fff; font-size: 15px; font-weight: 600; font-family: inherit;
            cursor: pointer; transition: all 0.25s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            position: relative;
            box-shadow: 0 4px 20px rgba(16,185,129,0.3);
        }
        .btn-submit:hover { transform: translateY(-1px); box-shadow: 0 6px 30px rgba(16,185,129,0.4); }
        .btn-submit:active { transform: translateY(0); }
        .btn-submit:disabled { opacity: 0.7; cursor: not-allowed; transform: none; }
        .spinner {
            width: 18px; height: 18px; border: 2px solid rgba(255,255,255,0.3);
            border-top-color: #fff; border-radius: 50%;
            display: none;
        }
        .btn-submit.loading .spinner { display: block; animation: spin 0.6s linear infinite; }
        .btn-submit.loading .btn-text { opacity: 0.7; }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* Footer */
        .login-footer { text-align: center; margin-top: 28px; }
        .login-footer p { font-size: 12px; color: var(--text-muted); }
        .login-footer a { color: var(--accent); text-decoration: none; font-weight: 500; }
        .login-footer a:hover { text-decoration: underline; }

        /* Admin link */
        .admin-link {
            display: block; text-align: center; margin-top: 16px;
            font-size: 13px; color: var(--text-muted);
            text-decoration: none; transition: color 0.2s;
        }
        .admin-link:hover { color: var(--accent); }

        @media (max-width: 480px) {
            .login-card { padding: 32px 24px; }
            .brand-icon { width: 56px; height: 56px; border-radius: 14px; }
            .brand h1 { font-size: 20px; }
        }
    </style>
</head>
<body>
    <div class="bg-gradient"></div>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
    <div class="grid-overlay"></div>

    <div class="login-container">
        <div class="login-card">
            <div class="brand">
                <div class="brand-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                    </svg>
                </div>
                <h1>Employee Portal</h1>
                <p>ระบบสำหรับพนักงาน</p>
            </div>

            @if(session('error'))
                <div class="alert alert-error" id="alert-error">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                    {{ session('error') }}
                </div>
            @endif
            @if(session('success'))
                <div class="alert alert-success" id="alert-success">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('employee.login.submit') }}" id="loginForm">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="username">ชื่อผู้ใช้</label>
                    <div class="input-wrapper">
                        <input type="text" class="form-input" id="username" name="username"
                               placeholder="กรอกชื่อผู้ใช้" value="{{ old('username') }}"
                               autocomplete="username" required>
                        <span class="input-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                        </span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">รหัสผ่าน</label>
                    <div class="input-wrapper">
                        <input type="password" class="form-input" id="password" name="password"
                               placeholder="กรอกรหัสผ่าน" autocomplete="current-password" required>
                        <span class="input-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                        </span>
                        <button type="button" class="password-toggle" id="togglePassword" aria-label="Toggle password">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" id="eyeIcon">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-submit" id="submitBtn">
                    <span class="spinner"></span>
                    <span class="btn-text">เข้าสู่ระบบ</span>
                </button>
            </form>

            <a href="{{ route('login') }}" class="admin-link">
                ← เข้าสู่ระบบผู้ดูแล (Admin)
            </a>

            <div class="login-footer">
                <p>&copy; {{ date('Y') }} Employee Management System</p>
            </div>
        </div>
    </div>

    <script>
        const toggleBtn = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        toggleBtn.addEventListener('click', () => {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            eyeIcon.innerHTML = isPassword
                ? '<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />'
                : '<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />';
        });

        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            btn.classList.add('loading');
            btn.disabled = true;
        });

        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(el => {
                el.style.transition = 'opacity 0.3s, transform 0.3s';
                el.style.opacity = '0';
                el.style.transform = 'translateY(-8px)';
                setTimeout(() => el.remove(), 300);
            });
        }, 5000);
    </script>
</body>
</html>
