<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Kurir · Didi Design</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --primary:#B71C1C; --primary-light:#D32F2F;
            --bg-main:#F4F6F8; --bg-panel:#FFFFFF;
            --border:rgba(0,0,0,0.08); --text-primary:#1F2937;
            --text-secondary:#4B5563; --text-muted:#9CA3AF;
            --danger:#EF4444; --success:#10B981; --warning:#F5B942;
        }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Outfit',sans-serif; background:var(--bg-main); color:var(--text-primary); display:flex; min-height:100vh; }

        /* ── Sidebar ── */
        .sidebar { width:280px; background:var(--bg-panel); border-right:1px solid var(--border); display:flex; flex-direction:column; position:fixed; top:0; bottom:0; left:0; z-index:100; }
        .brand { padding:30px; display:flex; align-items:center; gap:12px; border-bottom:1px solid var(--border); }
        .brand-icon { width:40px; height:40px; background:var(--primary); border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:18px; color:white; box-shadow:0 4px 12px rgba(183,28,28,.25); }
        .brand-name { font-size:20px; font-weight:800; }
        .brand-name span { color:var(--primary); }
        .nav-menu { padding:20px; flex:1; overflow-y:auto; }
        .nav-label { font-size:11px; text-transform:uppercase; letter-spacing:1px; color:var(--text-muted); margin:20px 0 10px 10px; font-weight:600; }
        .nav-item { display:flex; align-items:center; gap:14px; padding:14px 16px; color:var(--text-secondary); text-decoration:none; border-radius:12px; font-size:15px; font-weight:600; transition:all .2s; margin-bottom:6px; }
        .nav-item:hover { background:var(--bg-main); color:var(--primary); }
        .nav-item.active { background:rgba(183,28,28,.08); color:var(--primary); border:1px solid rgba(183,28,28,.15); }
        .nav-icon { font-size:18px; width:24px; text-align:center; }
        .sidebar-footer { padding:20px; border-top:1px solid var(--border); }
        .user-profile { display:flex; align-items:center; gap:12px; padding:10px; background:var(--bg-main); border-radius:12px; border:1px solid var(--border); }
        .user-avatar { width:40px; height:40px; border-radius:50%; background:var(--primary); color:white; display:flex; align-items:center; justify-content:center; font-weight:700; }
        .user-info { flex:1; overflow:hidden; }
        .user-name { font-size:14px; font-weight:700; }
        .user-role { font-size:12px; color:var(--text-muted); }
        .btn-logout { background:none; border:none; color:var(--text-muted); cursor:pointer; padding:8px; transition:color .2s; }
        .btn-logout:hover { color:var(--danger); }

        /* ── Main ── */
        .main-content { flex:1; margin-left:280px; padding:40px; }
        .header { display:flex; justify-content:space-between; align-items:center; margin-bottom:32px; }
        .page-title { font-size:28px; font-weight:800; }
        .breadcrumb { font-size:14px; color:var(--text-muted); margin-top:4px; }
        .breadcrumb a { color:var(--primary); text-decoration:none; }

        .btn-primary { background:var(--primary); color:white; border:none; padding:12px 24px; border-radius:10px; font-family:inherit; font-weight:700; font-size:14px; cursor:pointer; display:inline-flex; align-items:center; gap:8px; transition:all .2s; box-shadow:0 4px 12px rgba(183,28,28,.25); text-decoration:none; }
        .btn-primary:hover { background:var(--primary-light); transform:translateY(-1px); }

        /* ── Alert ── */
        .alert-success { background:rgba(16,185,129,.08); border:1px solid rgba(16,185,129,.2); color:#065F46; padding:14px 20px; border-radius:12px; margin-bottom:24px; font-weight:600; font-size:14px; display:flex; align-items:center; gap:10px; }

        /* ── Stats ── */
        .stats-row { display:grid; grid-template-columns:repeat(3,1fr); gap:20px; margin-bottom:32px; }
        .stat-card { background:white; border:1px solid var(--border); border-radius:16px; padding:20px 24px; display:flex; align-items:center; gap:16px; box-shadow:0 4px 16px rgba(0,0,0,.02); }
        .stat-icon { width:48px; height:48px; border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:20px; }
        .stat-value { font-size:28px; font-weight:800; }
        .stat-label { font-size:13px; color:var(--text-muted); font-weight:600; }

        /* ── Table ── */
        .panel { background:white; border:1px solid var(--border); border-radius:20px; padding:0; box-shadow:0 4px 20px rgba(0,0,0,.02); overflow:hidden; }
        .panel-header { padding:24px 28px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; }
        .panel-title { font-size:18px; font-weight:700; display:flex; align-items:center; gap:10px; }
        .panel-title i { color:var(--primary); }

        .data-table { width:100%; border-collapse:collapse; }
        .data-table th { text-align:left; padding:14px 20px; color:var(--text-muted); font-size:11px; text-transform:uppercase; letter-spacing:1px; border-bottom:1px solid var(--border); font-weight:700; background:var(--bg-main); }
        .data-table td { padding:16px 20px; border-bottom:1px solid var(--border); font-size:14px; color:var(--text-secondary); vertical-align:middle; }
        .data-table tbody tr { transition:background .15s; }
        .data-table tbody tr:hover { background:rgba(183,28,28,.02); }

        .courier-info { display:flex; align-items:center; gap:12px; }
        .courier-avatar { width:40px; height:40px; border-radius:50%; background:linear-gradient(135deg,var(--primary),var(--primary-light)); color:white; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:15px; }
        .courier-name { font-weight:700; color:var(--text-primary); font-size:14px; }
        .courier-email { font-size:12px; color:var(--text-muted); }

        .badge { padding:6px 14px; border-radius:20px; font-size:12px; font-weight:700; display:inline-flex; align-items:center; gap:6px; }
        .badge-active { background:rgba(16,185,129,.1); color:#065F46; }
        .badge-active::before { content:''; width:7px; height:7px; border-radius:50%; background:var(--success); display:inline-block; }
        .badge-inactive { background:rgba(239,68,68,.08); color:#991B1B; }
        .badge-inactive::before { content:''; width:7px; height:7px; border-radius:50%; background:var(--danger); display:inline-block; }

        .vehicle-info { display:flex; align-items:center; gap:6px; font-size:13px; font-weight:600; }
        .vehicle-info i { color:var(--primary); font-size:14px; }

        .action-group { display:flex; gap:8px; align-items:center; }
        .btn-toggle { border:none; padding:8px 16px; border-radius:8px; font-family:inherit; font-size:12px; font-weight:700; cursor:pointer; transition:all .2s; display:inline-flex; align-items:center; gap:6px; }
        .btn-activate { background:rgba(16,185,129,.1); color:#065F46; }
        .btn-activate:hover { background:rgba(16,185,129,.2); }
        .btn-deactivate { background:rgba(245,185,66,.1); color:#92400E; }
        .btn-deactivate:hover { background:rgba(245,185,66,.25); }
        .btn-delete { background:none; border:1px solid var(--border); color:var(--text-muted); padding:8px 10px; border-radius:8px; cursor:pointer; font-size:13px; transition:all .2s; }
        .btn-delete:hover { color:var(--danger); border-color:var(--danger); background:rgba(239,68,68,.05); }

        .empty-state { text-align:center; padding:60px 20px; color:var(--text-muted); }
        .empty-state i { font-size:48px; margin-bottom:16px; opacity:.4; }
        .empty-state p { font-size:15px; font-weight:600; }

        .pagination-bar { padding:16px 20px; border-top:1px solid var(--border); display:flex; justify-content:center; }
    </style>
</head>
<body>

<aside class="sidebar">
    <div class="brand">
        <div class="brand-icon"><i class="fa-solid fa-pen-nib"></i></div>
        <div class="brand-name">Didi<span>Design</span></div>
    </div>
    <nav class="nav-menu">
        <div class="nav-label">Main Menu</div>
        <a href="{{ route('admin.dashboard') }}" class="nav-item"><i class="fa-solid fa-house nav-icon"></i> Dashboard</a>
        <a href="{{ route('admin.products.index') }}" class="nav-item"><i class="fa-solid fa-box-open nav-icon"></i> Produk</a>
        <div class="nav-label">Pengiriman</div>
        <a href="{{ route('admin.couriers.index') }}" class="nav-item active"><i class="fa-solid fa-motorcycle nav-icon"></i> Kurir</a>
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
                <button type="submit" class="btn-logout"><i class="fa-solid fa-arrow-right-from-bracket"></i></button>
            </form>
        </div>
    </div>
</aside>

<main class="main-content">
    <div class="header">
        <div>
            <h1 class="page-title">Manajemen Kurir</h1>
            <p class="breadcrumb">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a> / Kurir
            </p>
        </div>
        <a href="{{ route('admin.couriers.create') }}" class="btn-primary">
            <i class="fa-solid fa-plus"></i> Tambah Kurir
        </a>
    </div>

    @if(session('success'))
        <div class="alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
    @endif

    <!-- Stats -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(183,28,28,.1);color:var(--primary);"><i class="fa-solid fa-motorcycle"></i></div>
            <div>
                <div class="stat-value">{{ $couriers->total() }}</div>
                <div class="stat-label">Total Kurir</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(16,185,129,.1);color:var(--success);"><i class="fa-solid fa-signal"></i></div>
            <div>
                <div class="stat-value">{{ $couriers->filter(fn($c) => $c->is_available)->count() }}</div>
                <div class="stat-label">Kurir Aktif</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(239,68,68,.1);color:var(--danger);"><i class="fa-solid fa-moon"></i></div>
            <div>
                <div class="stat-value">{{ $couriers->filter(fn($c) => !$c->is_available)->count() }}</div>
                <div class="stat-label">Kurir Nonaktif</div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="panel">
        <div class="panel-header">
            <div class="panel-title"><i class="fa-solid fa-list"></i> Daftar Kurir</div>
        </div>

        @if($couriers->count())
        <table class="data-table">
            <thead>
                <tr>
                    <th>Kurir</th>
                    <th>Telepon</th>
                    <th>Kendaraan</th>
                    <th>Plat Nomor</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($couriers as $courier)
                <tr>
                    <td>
                        <div class="courier-info">
                            <div class="courier-avatar">{{ strtoupper(substr($courier->user->profile->name ?? $courier->user->email, 0, 1)) }}</div>
                            <div>
                                <div class="courier-name">{{ $courier->user->profile->name ?? '-' }}</div>
                                <div class="courier-email">{{ $courier->user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="font-weight:600;">{{ $courier->user->profile->phone ?? '-' }}</td>
                    <td>
                        <div class="vehicle-info">
                            <i class="fa-solid fa-motorcycle"></i>
                            {{ $courier->vehicle_type ?? '-' }}
                        </div>
                    </td>
                    <td style="font-weight:700; letter-spacing:.5px;">{{ $courier->plate_number ?? '-' }}</td>
                    <td>
                        @if($courier->is_available)
                            <span class="badge badge-active">Aktif</span>
                        @else
                            <span class="badge badge-inactive">Nonaktif</span>
                        @endif
                    </td>
                    <td>
                        <div class="action-group">
                            <form action="{{ route('admin.couriers.toggle', $courier) }}" method="POST">
                                @csrf @method('PATCH')
                                @if($courier->is_available)
                                    <button type="submit" class="btn-toggle btn-deactivate"><i class="fa-solid fa-pause"></i> Nonaktifkan</button>
                                @else
                                    <button type="submit" class="btn-toggle btn-activate"><i class="fa-solid fa-play"></i> Aktifkan</button>
                                @endif
                            </form>
                            <form action="{{ route('admin.couriers.destroy', $courier) }}" method="POST" onsubmit="return confirm('Yakin hapus kurir ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-delete"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if($couriers->hasPages())
        <div class="pagination-bar">
            {{ $couriers->links() }}
        </div>
        @endif

        @else
        <div class="empty-state">
            <i class="fa-solid fa-motorcycle"></i>
            <p>Belum ada kurir terdaftar.</p>
        </div>
        @endif
    </div>
</main>

</body>
</html>
