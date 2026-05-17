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
            display: grid;
            grid-template-columns: 1fr 480px;
        }

        /* Left Panel */
        .left-panel {
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px 80px;
            position: relative;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 60px;
        }

        .brand-icon {
            width: 48px; height: 48px;
            background: var(--primary);
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px;
            color: white;
            box-shadow: 0 8px 24px rgba(183,28,28,0.3);
        }

        .brand-name {
            font-size: 26px;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: var(--text-primary);
        }
        .brand-name span { color: var(--primary); }

        .hero-headline {
            font-size: clamp(38px, 4vw, 58px);
            font-weight: 800;
            line-height: 1.1;
            letter-spacing: -1.5px;
            margin-bottom: 20px;
            color: var(--text-primary);
        }

        .hero-headline .gradient-text {
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-sub {
            font-size: 17px;
            color: var(--text-secondary);
            line-height: 1.6;
            max-width: 420px;
            margin-bottom: 48px;
        }

        .feature-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 14px;
            font-size: 15px;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .feature-dot {
            width: 36px; height: 36px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 15px;
            flex-shrink: 0;
            background: rgba(183,28,28,0.08);
            color: var(--primary);
        }

        /* Decorative card */
        .deco-cards {
            position: absolute;
            right: 0px; bottom: 80px;
            display: flex; flex-direction: column; gap: 12px;
        }

        .deco-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 14px 18px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            display: flex; align-items: center; gap: 12px;
            font-size: 13px;
            animation: slideInLeft 0.6s ease both;
            white-space: nowrap;
        }
        .deco-card:nth-child(2) { animation-delay: 0.15s; margin-left: 20px; }
        .deco-card:nth-child(3) { animation-delay: 0.3s; }

        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(30px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        .deco-icon {
            width: 32px; height: 32px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px;
        }

        .stat-label { color: var(--text-muted); font-size: 11px; margin-top: 1px; font-weight: 500; }
        .stat-val { font-weight: 700; font-size: 14px; color: var(--text-primary); }

        /* Right Panel */
        .right-panel {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 50px;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            background: var(--bg-card);
            padding: 40px;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.04), 0 1px 3px rgba(0,0,0,0.02);
            border: 1px solid var(--border);
        }

        .login-header {
            margin-bottom: 32px;
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
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 16px;
            transition: color 0.2s;
            pointer-events: none;
        }

        .form-input {
            width: 100%;
            background: var(--bg-main);
            border: 1px solid var(--border);
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

        .form-input:focus ~ .input-icon,
        .input-wrapper:focus-within .input-icon {
            color: var(--primary);
        }

        .input-icon { z-index: 1; }

        .toggle-password {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            font-size: 16px;
            transition: color 0.2s;
            padding: 4px;
        }
        .toggle-password:hover { color: var(--text-secondary); }

        .input-error .form-input {
            border-color: var(--error);
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
        }

        .field-error {
            font-size: 12px;
            color: var(--error);
            margin-top: 6px;
            display: flex; align-items: center; gap: 5px;
            font-weight: 500;
        }

        /* Remember & Forgot */
        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
        }

        .remember-check {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            color: var(--text-secondary);
            user-select: none;
        }

        .remember-check input[type="checkbox"] {
            width: 18px; height: 18px;
            accent-color: var(--primary);
            cursor: pointer;
        }

        .forgot-link {
            font-size: 14px;
            font-weight: 600;
            color: var(--primary);
            text-decoration: none;
            transition: color 0.2s;
        }
        .forgot-link:hover { color: var(--primary-dark); }

        /* Submit Button */
        .btn-login {
            width: 100%;
            padding: 16px;
            background: var(--primary);
            border: none;
            border-radius: 12px;
            font-family: 'Outfit', sans-serif;
            font-size: 16px;
            font-weight: 700;
            color: white;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: transform 0.15s, box-shadow 0.15s, background 0.15s;
            box-shadow: 0 8px 20px rgba(183, 28, 28, 0.25);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            background: var(--primary-light);
            box-shadow: 0 12px 24px rgba(183, 28, 28, 0.35);
        }
        .btn-login:active { transform: translateY(0); }

        .btn-login.loading {
            pointer-events: none;
            opacity: 0.8;
        }

        .btn-spinner {
            display: none;
            width: 20px; height: 20px;
            border: 2.5px solid rgba(255,255,255,0.4);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
            margin: 0 auto;
        }
        .btn-login.loading .btn-text { display: none; }
        .btn-login.loading .btn-spinner { display: block; }

        @keyframes spin { to { transform: rotate(360deg); } }

        /* Footer text */
        .login-footer {
            text-align: center;
            margin-top: 32px;
            font-size: 13px;
            font-weight: 500;
            color: var(--text-muted);
        }

        /* Responsive */
        @media (max-width: 900px) {
            .page-wrapper {
                grid-template-columns: 1fr;
            }
            .left-panel { display: none; }
            .login-card {
                box-shadow: none;
                border: none;
                background: transparent;
                padding: 20px;
            }
            .right-panel {
                padding: 20px;
            }
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

<div class="page-wrapper">

    <!-- Left Panel (Branding) -->
    <div class="left-panel">
        <div class="brand-logo">
            <div class="brand-icon"><i class="fa-solid fa-pen-nib"></i></div>
            <div class="brand-name">Didi<span>Design</span></div>
        </div>

        <h1 class="hero-headline">
            Panel Kontrol<br>
            <span class="gradient-text">Admin Terpadu</span>
        </h1>

        <p class="hero-sub">
            Kelola seluruh operasional bisnis Anda — produk, pesanan, kurir, dan laporan — dari satu dasbor yang cerdas.
        </p>

        <div class="feature-list">
            <div class="feature-item">
                <div class="feature-dot"><i class="fa-solid fa-box-archive"></i></div>
                <span>Manajemen produk & kategori secara real-time</span>
            </div>
            <div class="feature-item">
                <div class="feature-dot"><i class="fa-solid fa-receipt"></i></div>
                <span>Pantau status pesanan & pembayaran</span>
            </div>
            <div class="feature-item">
                <div class="feature-dot"><i class="fa-solid fa-truck-fast"></i></div>
                <span>Monitor kurir & pengiriman langsung</span>
            </div>
            <div class="feature-item">
                <div class="feature-dot"><i class="fa-solid fa-chart-line"></i></div>
                <span>Laporan penjualan & analitik mendalam</span>
            </div>
        </div>

        <!-- Floating Deco Cards -->
        <div class="deco-cards">
            <div class="deco-card">
                <div class="deco-icon" style="background:rgba(16, 185, 129, 0.1); color:#10B981;">
                    <i class="fa-solid fa-arrow-trend-up"></i>
                </div>
                <div>
                    <div class="stat-val">+24.5%</div>
                    <div class="stat-label">Revenue Bulan Ini</div>
                </div>
            </div>
            <div class="deco-card">
                <div class="deco-icon" style="background:rgba(183, 28, 28, 0.1); color:var(--primary);">
                    <i class="fa-solid fa-bags-shopping"></i>
                </div>
                <div>
                    <div class="stat-val">1,248</div>
                    <div class="stat-label">Pesanan Aktif</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Panel (Login Form) -->
    <div class="right-panel">
        <div class="login-card">

            <div class="login-header">
                <h2 class="login-title">Selamat Datang 👋</h2>
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
