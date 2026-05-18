<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login Admin Panel - Didi Design Management System">
    <title>Login Admin · Didi Design</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *, *::before, *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #B71C1C;
            --primary-light: #D32F2F;
            --primary-dark: #880E4F;
            --accent: #FF5252;
            --gold: #F5B942;
            --bg-main: #F4F6F8;
            --bg-card: #FFFFFF;
            --bg-card-hover: #FAFAFA;
            --border: rgba(0, 0, 0, 0.08);
            --text-primary: #1F2937;
            --text-secondary: #4B5563;
            --text-muted: #9CA3AF;
            --error: #EF4444;
            --success: #10B981;
        }

        html, body {
            height: 100%;
            font-family: 'Outfit', sans-serif;
            background: var(--bg-main);
            color: var(--text-primary);
            overflow: hidden;
        }

        /* ── Animated Background ── */
        .bg-scene {
            position: fixed;
            inset: 0;
            z-index: 0;
            overflow: hidden;
            background: 
                radial-gradient(circle at 10% 20%, rgba(183, 28, 28, 0.05) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(211, 47, 47, 0.04) 0%, transparent 40%),
                var(--bg-main);
        }

        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            animation: float 8s ease-in-out infinite;
        }
        .orb-1 {
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(183,28,28,0.06) 0%, transparent 70%);
            top: -200px; left: -150px;
            animation-delay: 0s;
        }
        .orb-2 {
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(211,47,47,0.05) 0%, transparent 70%);
            bottom: -150px; right: -100px;
            animation-delay: -3s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) scale(1); }
            50% { transform: translateY(-20px) scale(1.02); }
        }

        /* Grid overlay */
        .grid-overlay {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(183,28,28,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(183,28,28,0.03) 1px, transparent 1px);
            background-size: 60px 60px;
            mask-image: radial-gradient(ellipse at center, black 10%, transparent 80%);
            -webkit-mask-image: radial-gradient(ellipse at center, black 10%, transparent 80%);
        }

        /* ── Layout ── */
        .page-wrapper {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        /* Top brand badge */
        .brand-top {
            position: fixed;
            top: 32px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 10;
            background: white;
            padding: 10px 20px;
            border-radius: 50px;
            border: 1px solid var(--border);
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }

        .brand-icon {
            width: 34px; height: 34px;
            background: var(--primary);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 16px;
            color: white;
            box-shadow: 0 4px 12px rgba(183,28,28,0.3);
        }

        .brand-name {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: var(--text-primary);
        }
        .brand-name span { color: var(--primary); }

        /* Floating deco cards */
        .deco-float {
            position: fixed;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .deco-float.left  { left: 40px;  bottom: 80px; }
        .deco-float.right { right: 40px; top: 120px; }

        .deco-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 14px 18px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.04);
            display: flex; align-items: center; gap: 12px;
            font-size: 13px;
            white-space: nowrap;
            animation: fadeSlideUp 0.6s ease both;
        }
        .deco-card:nth-child(2) { animation-delay: 0.15s; }
        .deco-card:nth-child(3) { animation-delay: 0.3s; }

        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .deco-icon {
            width: 34px; height: 34px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 15px;
        }

        .stat-label { color: var(--text-muted); font-size: 11px; margin-top: 1px; font-weight: 500; }
        .stat-val { font-weight: 700; font-size: 14px; color: var(--text-primary); }

        /* Center Card */
        .login-card {
            width: 100%;
            max-width: 440px;
            background: var(--bg-card);
            padding: 44px 48px;
            border-radius: 28px;
            box-shadow: 0 24px 60px rgba(0,0,0,0.07), 0 1px 3px rgba(0,0,0,0.03);
            border: 1px solid var(--border);
            animation: cardIn 0.5s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        @keyframes cardIn {
            from { opacity: 0; transform: translateY(24px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* Card Header */
        .login-header { margin-bottom: 32px; text-align: center; }

        .login-avatar {
            width: 64px; height: 64px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border-radius: 20px;
            display: flex; align-items: center; justify-content: center;
            font-size: 28px; color: white;
            margin: 0 auto 20px;
            box-shadow: 0 8px 24px rgba(183,28,28,0.3);
        }

        .login-title {
            font-size: 26px;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 6px;
            color: var(--text-primary);
        }

        .login-subtitle {
            font-size: 15px;
            color: var(--text-secondary);
        }

        /* Alert */
        .alert {
            padding: 14px 16px;
            border-radius: 12px;
            font-size: 14px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
            animation: shake 0.4s ease;
        }
        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #B91C1C;
        }
        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.2);
            color: #047857;
        }

        @keyframes shake {
            0%,100% { transform: translateX(0); }
            20% { transform: translateX(-6px); }
            40% { transform: translateX(6px); }
            60% { transform: translateX(-4px); }
            80% { transform: translateX(4px); }
        }

        /* Form */
        .form-group { margin-bottom: 20px; }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .input-wrapper { position: relative; }

        .input-icon {
            position: absolute;
            left: 16px; top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 16px;
            transition: color 0.2s;
            pointer-events: none;
            z-index: 1;
        }

        .form-input {
            width: 100%;
            background: var(--bg-main);
            border: 1.5px solid var(--border);
            border-radius: 12px;
            padding: 14px 16px 14px 46px;
            font-family: 'Outfit', sans-serif;
            font-size: 15px;
            font-weight: 500;
            color: var(--text-primary);
            outline: none;
            transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
            -webkit-appearance: none;
        }

        .form-input::placeholder { color: var(--text-muted); font-weight: 400; }

        .form-input:focus {
            border-color: var(--primary);
            background: var(--bg-card);
            box-shadow: 0 0 0 4px rgba(183, 28, 28, 0.08);
        }

        .input-wrapper:focus-within .input-icon { color: var(--primary); }

        .toggle-password {
            position: absolute;
            right: 16px; top: 50%;
            transform: translateY(-50%);
            background: none; border: none;
            color: var(--text-muted);
            cursor: pointer; font-size: 16px;
            transition: color 0.2s; padding: 4px;
        }
        .toggle-password:hover { color: var(--text-secondary); }

        .input-error .form-input {
            border-color: var(--error);
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
        }

        .field-error {
            font-size: 12px; color: var(--error);
            margin-top: 6px;
            display: flex; align-items: center; gap: 5px;
            font-weight: 500;
        }

        /* Remember & Forgot */
        .form-options {
            display: flex; align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
        }

        .remember-check {
            display: flex; align-items: center; gap: 8px;
            cursor: pointer; font-size: 14px; font-weight: 500;
            color: var(--text-secondary); user-select: none;
        }

        .remember-check input[type="checkbox"] {
            width: 18px; height: 18px;
            accent-color: var(--primary); cursor: pointer;
        }

        .forgot-link {
            font-size: 14px; font-weight: 600;
            color: var(--primary); text-decoration: none;
            transition: color 0.2s;
        }
        .forgot-link:hover { color: var(--primary-dark); }

        /* Submit Button */
        .btn-login {
            width: 100%; padding: 16px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            border: none; border-radius: 12px;
            font-family: 'Outfit', sans-serif;
            font-size: 16px; font-weight: 700; color: white;
            cursor: pointer; position: relative; overflow: hidden;
            transition: transform 0.15s, box-shadow 0.15s;
            box-shadow: 0 8px 20px rgba(183, 28, 28, 0.3);
        }
        .btn-login:hover { transform: translateY(-2px); box-shadow: 0 12px 28px rgba(183, 28, 28, 0.4); }
        .btn-login:active { transform: translateY(0); }
        .btn-login.loading { pointer-events: none; opacity: 0.8; }

        .btn-spinner {
            display: none; width: 20px; height: 20px;
            border: 2.5px solid rgba(255,255,255,0.4);
            border-top-color: white; border-radius: 50%;
            animation: spin 0.7s linear infinite; margin: 0 auto;
        }
        .btn-login.loading .btn-text { display: none; }
        .btn-login.loading .btn-spinner { display: block; }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* Footer */
        .login-footer {
            text-align: center; margin-top: 28px;
            font-size: 13px; font-weight: 500; color: var(--text-muted);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .deco-float { display: none; }
            .brand-top { top: 16px; }
            .login-card { padding: 32px 24px; }
            .page-wrapper { padding: 100px 16px 40px; align-items: flex-start; }
        }
    </style>
</head>
<body>

<!-- Background Scene -->
<div class="bg-scene">
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="grid-overlay"></div>
</div>

<!-- Brand Top Badge -->
<div class="brand-top">
    <div class="brand-icon"><i class="fa-solid fa-pen-nib"></i></div>
    <div class="brand-name">Didi<span>Design</span></div>
</div>

<!-- Floating Deco Cards — Left -->
<div class="deco-float left">
    <div class="deco-card">
        <div class="deco-icon" style="background:rgba(16,185,129,.1);color:#10B981;">
            <i class="fa-solid fa-arrow-trend-up"></i>
        </div>
        <div>
            <div class="stat-val">+24.5%</div>
            <div class="stat-label">Revenue Bulan Ini</div>
        </div>
    </div>
    <div class="deco-card">
        <div class="deco-icon" style="background:rgba(59,130,246,.1);color:#3B82F6;">
            <i class="fa-solid fa-box-open"></i>
        </div>
        <div>
            <div class="stat-val">142</div>
            <div class="stat-label">Total Produk</div>
        </div>
    </div>
</div>

<!-- Floating Deco Cards — Right -->
<div class="deco-float right">
    <div class="deco-card">
        <div class="deco-icon" style="background:rgba(183,28,28,.1);color:var(--primary);">
            <i class="fa-solid fa-bags-shopping"></i>
        </div>
        <div>
            <div class="stat-val">1,248</div>
            <div class="stat-label">Pesanan Aktif</div>
        </div>
    </div>
    <div class="deco-card">
        <div class="deco-icon" style="background:rgba(245,185,66,.15);color:#D97706;">
            <i class="fa-solid fa-motorcycle"></i>
        </div>
        <div>
            <div class="stat-val">8</div>
            <div class="stat-label">Kurir Online</div>
        </div>
    </div>
</div>

<div class="page-wrapper">
    <div class="login-card">
        <div class="login-header">
            <div class="login-avatar"><i class="fa-solid fa-pen-nib"></i></div>
            <h1 class="login-title">Selamat Datang 👋</h1>
            <p class="login-subtitle">Masuk ke panel admin untuk melanjutkan</p>
        </div>

        @if(session('error'))
            <div class="alert alert-error">
                <i class="fa-solid fa-circle-exclamation"></i>
                {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check"></i>
                {{ session('success') }}
            </div>
        @endif

        <form id="loginForm" method="POST" action="{{ route('admin.login.post') }}" novalidate>
            @csrf

            <!-- Email -->
            <div class="form-group {{ $errors->has('email') ? 'input-error' : '' }}">
                <label class="form-label" for="email">Email</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-envelope input-icon"></i>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-input"
                        placeholder="admin@dididesign.com"
                        value="{{ old('email') }}"
                        autocomplete="email"
                        required
                    >
                </div>
                @error('email')
                    <div class="field-error">
                        <i class="fa-solid fa-triangle-exclamation"></i> {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Password -->
            <div class="form-group {{ $errors->has('password') ? 'input-error' : '' }}">
                <label class="form-label" for="password">Password</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-lock input-icon"></i>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-input"
                        placeholder="••••••••••"
                        autocomplete="current-password"
                        required
                    >
                    <button type="button" class="toggle-password" id="togglePwd" aria-label="Toggle password">
                        <i class="fa-solid fa-eye" id="eyeIcon"></i>
                    </button>
                </div>
                @error('password')
                    <div class="field-error">
                        <i class="fa-solid fa-triangle-exclamation"></i> {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Options -->
            <div class="form-options">
                <label class="remember-check">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    Ingat saya
                </label>
                <a href="#" class="forgot-link">Lupa password?</a>
            </div>

            <!-- Submit -->
            <button type="submit" class="btn-login" id="loginBtn">
                <span class="btn-text">Masuk ke Dashboard</span>
                <div class="btn-spinner"></div>
            </button>
        </form>

        <div class="login-footer">
            <i class="fa-solid fa-shield-halved" style="color: var(--primary); margin-right:6px;"></i>
            Akses khusus administrator
        </div>
    </div>
</div>

<script>
    // ── Toggle Password ──
    const toggleBtn = document.getElementById('togglePwd');
    const pwdInput  = document.getElementById('password');
    const eyeIcon   = document.getElementById('eyeIcon');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', () => {
            const isHidden = pwdInput.type === 'password';
            pwdInput.type = isHidden ? 'text' : 'password';
            eyeIcon.className = isHidden ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye';
        });
    }

    // ── Loading State on Submit ──
    const form = document.getElementById('loginForm');
    const btn  = document.getElementById('loginBtn');
    if (form) {
        form.addEventListener('submit', (e) => {
            const email = document.getElementById('email').value.trim();
            const pwd   = document.getElementById('password').value;
            if (!email || !pwd) return;
            btn.classList.add('loading');
        });
    }
</script>

</body>
</html>
