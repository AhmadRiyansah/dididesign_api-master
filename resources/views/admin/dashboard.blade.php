<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin · Didi Design</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --primary: #B71C1C;
            --primary-light: #D32F2F;
            --accent: #FF5252;
            --gold: #F5B942;
            --bg-main: #F4F6F8;
            --bg-panel: #FFFFFF;
            --bg-card: #FFFFFF;
            --bg-card-hover: #FAFAFA;
            --border: rgba(0, 0, 0, 0.08);
            --text-primary: #1F2937;
            --text-secondary: #4B5563;
            --text-muted: #9CA3AF;
            --success: #10B981;
            --warning: #F5B942;
            --danger: #EF4444;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-main);
            color: var(--text-primary);
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ── Sidebar ── */
        .sidebar {
            width: 280px;
            background: var(--bg-panel);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; bottom: 0; left: 0;
            z-index: 100;
        }

        .brand {
            padding: 30px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid var(--border);
        }

        .brand-icon {
            width: 40px; height: 40px;
            background: var(--primary);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; color: white;
            box-shadow: 0 4px 12px rgba(183,28,28,0.25);
        }

        .brand-name { font-size: 20px; font-weight: 800; }
        .brand-name span { color: var(--primary); }

        .nav-menu {
            padding: 20px;
            flex: 1;
            overflow-y: auto;
        }

        .nav-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            margin: 20px 0 10px 10px;
            font-weight: 600;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 16px;
            color: var(--text-secondary);
            text-decoration: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            transition: all 0.2s;
            margin-bottom: 6px;
        }

        .nav-item:hover {
            background: var(--bg-main);
            color: var(--primary);
        }

        .nav-item.active {
            background: rgba(183,28,28,0.08);
            color: var(--primary);
            border: 1px solid rgba(183,28,28,0.15);
        }

        .nav-icon { font-size: 18px; width: 24px; text-align: center; }

        .sidebar-footer {
            padding: 20px;
            border-top: 1px solid var(--border);
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px;
            background: var(--bg-main);
            border-radius: 12px;
            border: 1px solid var(--border);
        }

        .user-avatar {
            width: 40px; height: 40px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700;
        }

        .user-info { flex: 1; overflow: hidden; }
        .user-name { font-size: 14px; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .user-role { font-size: 12px; color: var(--text-muted); font-weight: 500;}

        .btn-logout {
            background: none; border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 8px;
            transition: color 0.2s;
        }
        .btn-logout:hover { color: var(--danger); }

        /* ── Main Content ── */
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 40px;
            background: 
                radial-gradient(circle at top right, rgba(183,28,28,0.03) 0%, transparent 40%),
                var(--bg-main);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        .page-title { font-size: 28px; font-weight: 800; color: var(--text-primary); }
        .page-subtitle { color: var(--text-secondary); font-size: 15px; margin-top: 4px; font-weight: 500;}

        .header-actions { display: flex; gap: 16px; align-items: center; }

        .btn-primary {
            background: var(--primary);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 10px;
            font-family: inherit;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            display: flex; align-items: center; gap: 8px;
            transition: all 0.2s;
            box-shadow: 0 4px 12px rgba(183,28,28,0.25);
        }
        .btn-primary:hover { background: var(--primary-light); transform: translateY(-2px); box-shadow: 0 6px 16px rgba(183,28,28,0.35); }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 24px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 24px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.02);
        }

        .stat-icon {
            width: 48px; height: 48px;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px;
            margin-bottom: 16px;
        }

        .stat-value { font-size: 32px; font-weight: 800; margin-bottom: 4px; color: var(--text-primary); }
        .stat-label { color: var(--text-secondary); font-size: 14px; font-weight: 600; }
        
        .stat-glow {
            position: absolute;
            top: -30px; right: -30px;
            width: 120px; height: 120px;
            border-radius: 50%;
            filter: blur(40px);
            opacity: 0.15;
        }

        /* Two Columns */
        .grid-2 {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
        }

        .panel {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.02);
        }

        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .panel-title { font-size: 18px; font-weight: 700; color: var(--text-primary); }
        .panel-action { color: var(--primary); text-decoration: none; font-size: 14px; font-weight: 600; }
        .panel-action:hover { text-decoration: underline; color: var(--primary-light); }

        /* Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th {
            text-align: left;
            padding: 12px 16px;
            color: var(--text-muted);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 1px solid var(--border);
            font-weight: 600;
        }

        .data-table td {
            padding: 16px;
            border-bottom: 1px solid var(--border);
            font-size: 14px;
            color: var(--text-secondary);
        }

        .data-table tbody tr:hover { background: var(--bg-card-hover); }

        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
        }
        .badge-pending { background: rgba(245,185,66,0.15); color: #D97706; }
        .badge-paid { background: rgba(16,185,129,0.15); color: var(--success); }

        /* Lists */
        .list-item {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 12px 0;
            border-bottom: 1px solid var(--border);
        }
        .list-item:last-child { border-bottom: none; }

        .item-img {
            width: 48px; height: 48px;
            border-radius: 10px;
            background: var(--bg-main);
            object-fit: cover;
            border: 1px solid var(--border);
        }

        .item-info { flex: 1; }
        .item-title { font-weight: 700; font-size: 14px; margin-bottom: 4px; color: var(--text-primary); }
        .item-sub { color: var(--text-muted); font-size: 13px; font-weight: 500; }

        .item-action {
            color: var(--text-muted);
            background: none; border: none; cursor: pointer;
            width: 32px; height: 32px; border-radius: 8px;
            transition: all 0.2s;
        }
        .item-action:hover { background: var(--bg-main); color: var(--primary); }

    </style>
</head>
<body>

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="brand">
            <div class="brand-icon"><i class="fa-solid fa-pen-nib"></i></div>
            <div class="brand-name">Didi<span>Design</span></div>
        </div>

        <nav class="nav-menu">
            <div class="nav-label">Main Menu</div>
            <a href="{{ route('admin.dashboard') }}" class="nav-item active"><i class="fa-solid fa-house nav-icon"></i> Dashboard</a>
            <a href="{{ route('admin.products.index') }}" class="nav-item"><i class="fa-solid fa-box-open nav-icon"></i> Produk</a>
            
            <div class="nav-label">Transaksi</div>
            <a href="#" class="nav-item"><i class="fa-solid fa-cart-shopping nav-icon"></i> Pesanan</a>
            <a href="#" class="nav-item"><i class="fa-solid fa-print nav-icon"></i> Cetak File</a>
            <a href="#" class="nav-item"><i class="fa-solid fa-money-bill-transfer nav-icon"></i> Pembayaran</a>

            <div class="nav-label">Pengiriman</div>
            <a href="#" class="nav-item"><i class="fa-solid fa-motorcycle nav-icon"></i> Kurir</a>
            <a href="#" class="nav-item"><i class="fa-solid fa-route nav-icon"></i> Tracking</a>

            <div class="nav-label">Pengaturan</div>
            <a href="#" class="nav-item"><i class="fa-solid fa-users nav-icon"></i> Pengguna</a>
            <a href="#" class="nav-item"><i class="fa-solid fa-gear nav-icon"></i> Sistem</a>
        </nav>

        <div class="sidebar-footer">
            <div class="user-profile">
                <div class="user-avatar">{{ substr(Auth::user()->email, 0, 1) }}</div>
                <div class="user-info">
                    <div class="user-name">Administrator</div>
                    <div class="user-role">{{ Auth::user()->email }}</div>
                </div>
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-logout" title="Logout"><i class="fa-solid fa-arrow-right-from-bracket"></i></button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="header">
            <div>
                <h1 class="page-title">DASHBOARD ADMIN DIDI DESIGN</h1>
                <p class="page-subtitle">Pantau performa Didi Design hari ini</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.products.create') }}" class="btn-primary" style="text-decoration:none;"><i class="fa-solid fa-plus"></i> Produk Baru</a>
            </div>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-glow" style="background: var(--primary);"></div>
                <div class="stat-icon" style="background: rgba(183,28,28,0.1); color: var(--primary);">
                    <i class="fa-solid fa-wallet"></i>
                </div>
                <div class="stat-value">Rp {{ number_format($stats['revenue'], 0, ',', '.') }}</div>
                <div class="stat-label">Total Pendapatan</div>
            </div>
            <div class="stat-card">
                <div class="stat-glow" style="background: var(--accent);"></div>
                <div class="stat-icon" style="background: rgba(255,82,82,0.1); color: var(--accent);">
                    <i class="fa-solid fa-cart-shopping"></i>
                </div>
                <div class="stat-value">{{ $stats['pending_orders'] }}</div>
                <div class="stat-label">Pesanan Menunggu</div>
            </div>
            <div class="stat-card">
                <div class="stat-glow" style="background: var(--success);"></div>
                <div class="stat-icon" style="background: rgba(16,185,129,0.1); color: var(--success);">
                    <i class="fa-solid fa-box-open"></i>
                </div>
                <div class="stat-value">{{ $stats['total_products'] }}</div>
                <div class="stat-label">Total Produk</div>
            </div>
            <div class="stat-card">
                <div class="stat-glow" style="background: var(--warning);"></div>
                <div class="stat-icon" style="background: rgba(245,185,66,0.1); color: #D97706;">
                    <i class="fa-solid fa-motorcycle"></i>
                </div>
                <div class="stat-value">{{ $stats['total_couriers'] }}</div>
                <div class="stat-label">Kurir Aktif</div>
            </div>
        </div>

        <!-- Bottom Grid -->
        <div class="grid-2">
            <!-- Recent Orders -->
            <div class="panel">
                <div class="panel-header">
                    <h2 class="panel-title">Pesanan Terbaru</h2>
                    <a href="#" class="panel-action">Lihat Semua</a>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID Pesanan</th>
                            <th>Pelanggan</th>
                            <th>Total</th>
                            <th>Status Pembayaran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOrders as $order)
                        <tr>
                            <td style="font-weight: 700; color: var(--text-primary);">#{{ $order->order_code }}</td>
                            <td style="font-weight: 500;">{{ $order->customer->profile->name ?? 'User' }}</td>
                            <td style="font-weight: 700; color: var(--text-primary);">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</td>
                            <td>
                                @if($order->payment_status === 'paid')
                                    <span class="badge badge-paid">Lunas</span>
                                @else
                                    <span class="badge badge-pending">Menunggu</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 30px;">Belum ada pesanan terbaru.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Top Products -->
            <div class="panel">
                <div class="panel-header">
                    <h2 class="panel-title">Produk Populer</h2>
                </div>
                <div class="list">
                    @forelse($topProducts as $product)
                    <div class="list-item">
                        @if($product->image)
                            <img src="{{ Storage::url($product->image) }}" class="item-img" alt="Product">
                        @else
                            <div class="item-img" style="display:flex;align-items:center;justify-content:center;font-size:20px;color:var(--text-muted)"><i class="fa-solid fa-image"></i></div>
                        @endif
                        <div class="item-info">
                            <div class="item-title">{{ $product->name }}</div>
                            <div class="item-sub">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                        </div>
                        <button class="item-action"><i class="fa-solid fa-chevron-right"></i></button>
                    </div>
                    @empty
                    <div style="text-align: center; color: var(--text-muted); padding: 30px;">Belum ada produk.</div>
                    @endforelse
                </div>
            </div>
        </div>

    </main>

</body>
</html>
