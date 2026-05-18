<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pesanan · Didi Design</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --primary: #B71C1C; --primary-light: #D32F2F; --accent: #FF5252;
            --bg-main: #F4F6F8; --bg-panel: #FFFFFF; --bg-card: #FFFFFF;
            --bg-card-hover: #FAFAFA; --border: rgba(0,0,0,0.08);
            --text-primary: #1F2937; --text-secondary: #4B5563; --text-muted: #9CA3AF;
            --success: #10B981; --warning: #F5B942; --danger: #EF4444;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Outfit', sans-serif; background: var(--bg-main); color: var(--text-primary); display: flex; min-height: 100vh; }

        .sidebar { width: 280px; background: var(--bg-panel); border-right: 1px solid var(--border); display: flex; flex-direction: column; position: fixed; top: 0; bottom: 0; left: 0; z-index: 100; }
        .brand { padding: 30px; display: flex; align-items: center; gap: 12px; border-bottom: 1px solid var(--border); }
        .brand-icon { width: 40px; height: 40px; background: var(--primary); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px; color: white; box-shadow: 0 4px 12px rgba(183,28,28,0.25); }
        .brand-name { font-size: 20px; font-weight: 800; }
        .brand-name span { color: var(--primary); }
        .nav-menu { padding: 20px; flex: 1; overflow-y: auto; }
        .nav-label { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: var(--text-muted); margin: 20px 0 10px 10px; font-weight: 600; }
        .nav-item { display: flex; align-items: center; gap: 14px; padding: 14px 16px; color: var(--text-secondary); text-decoration: none; border-radius: 12px; font-size: 15px; font-weight: 600; transition: all 0.2s; margin-bottom: 6px; }
        .nav-item:hover { background: var(--bg-main); color: var(--primary); }
        .nav-item.active { background: rgba(183,28,28,0.08); color: var(--primary); border: 1px solid rgba(183,28,28,0.15); }
        .nav-icon { font-size: 18px; width: 24px; text-align: center; }
        .nav-badge { margin-left: auto; background: var(--danger); color: white; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 10px; }
        .sidebar-footer { padding: 20px; border-top: 1px solid var(--border); }
        .user-profile { display: flex; align-items: center; gap: 12px; padding: 10px; background: var(--bg-main); border-radius: 12px; border: 1px solid var(--border); }
        .user-avatar { width: 40px; height: 40px; border-radius: 50%; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; }
        .user-info { flex: 1; overflow: hidden; }
        .user-name { font-size: 14px; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .user-role { font-size: 12px; color: var(--text-muted); font-weight: 500; }
        .btn-logout { background: none; border: none; color: var(--text-muted); cursor: pointer; padding: 8px; transition: color 0.2s; }
        .btn-logout:hover { color: var(--danger); }

        .main-content { flex: 1; margin-left: 280px; padding: 40px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .page-title { font-size: 28px; font-weight: 800; }
        .page-subtitle { color: var(--text-secondary); font-size: 15px; margin-top: 4px; font-weight: 500; }

        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: 16px; padding: 20px; display: flex; align-items: center; gap: 16px; }
        .stat-icon-sm { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px; }
        .stat-value { font-size: 24px; font-weight: 800; }
        .stat-label { color: var(--text-muted); font-size: 13px; font-weight: 500; }

        .filter-tabs { display: flex; gap: 8px; margin-bottom: 24px; flex-wrap: wrap; }
        .filter-tab { padding: 10px 20px; border-radius: 10px; font-family: inherit; font-size: 14px; font-weight: 600; border: 1px solid var(--border); background: var(--bg-card); color: var(--text-secondary); cursor: pointer; transition: all 0.2s; text-decoration: none; }
        .filter-tab:hover { border-color: var(--primary); color: var(--primary); }
        .filter-tab.active { background: var(--primary); color: white; border-color: var(--primary); box-shadow: 0 4px 12px rgba(183,28,28,0.25); }
        .filter-badge { background: rgba(255,255,255,0.3); padding: 2px 8px; border-radius: 8px; font-size: 12px; margin-left: 6px; }

        .panel { background: var(--bg-card); border: 1px solid var(--border); border-radius: 20px; overflow: hidden; }
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th { text-align: left; padding: 14px 20px; color: var(--text-muted); font-size: 12px; text-transform: uppercase; letter-spacing: 1px; border-bottom: 1px solid var(--border); font-weight: 600; background: var(--bg-main); }
        .data-table td { padding: 16px 20px; border-bottom: 1px solid var(--border); font-size: 14px; color: var(--text-secondary); vertical-align: middle; }
        .data-table tbody tr:hover { background: var(--bg-card-hover); }

        .badge { padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; display: inline-block; }
        .badge-process { background: rgba(245,185,66,0.15); color: #D97706; }
        .badge-shipping { background: rgba(59,130,246,0.15); color: #2563EB; }
        .badge-done { background: rgba(16,185,129,0.15); color: var(--success); }
        .badge-cancel { background: rgba(156,163,175,0.15); color: var(--text-muted); }
        .badge-unassigned { background: rgba(239,68,68,0.15); color: var(--danger); }
        .badge-assigned { background: rgba(16,185,129,0.15); color: var(--success); }

        .assign-form { display: flex; gap: 6px; align-items: center; }
        .assign-select { padding: 8px 12px; border: 1px solid var(--border); border-radius: 8px; font-family: inherit; font-size: 13px; background: white; min-width: 160px; }
        .assign-select:focus { outline: none; border-color: var(--primary); }
        .btn-assign { padding: 8px 16px; background: var(--primary); color: white; border: none; border-radius: 8px; font-family: inherit; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s; white-space: nowrap; }
        .btn-assign:hover { background: var(--primary-light); }

        .alert { padding: 14px 20px; border-radius: 12px; margin-bottom: 20px; font-weight: 500; font-size: 14px; display: flex; align-items: center; gap: 10px; }
        .alert-success { background: rgba(16,185,129,0.1); color: #059669; border: 1px solid rgba(16,185,129,0.2); }

        .empty-state { text-align: center; padding: 60px 20px; color: var(--text-muted); }
        .empty-state i { font-size: 48px; margin-bottom: 16px; display: block; }

        .pagination-wrap { display: flex; justify-content: center; padding: 20px; gap: 6px; }
        .pagination-wrap a, .pagination-wrap span { padding: 8px 14px; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none; border: 1px solid var(--border); color: var(--text-secondary); }
        .pagination-wrap span.current { background: var(--primary); color: white; border-color: var(--primary); }
        .pagination-wrap a:hover { background: var(--bg-main); }
    </style>
</head>
<body>
    @include('admin.partials.sidebar')

    <main class="main-content">
        <div class="header">
            <div>
                <h1 class="page-title">Manajemen Pesanan</h1>
                <p class="page-subtitle">Kelola pesanan dan assign kurir</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success"><i class="fa-solid fa-check-circle"></i> {{ session('success') }}</div>
        @endif

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon-sm" style="background:rgba(183,28,28,0.1);color:var(--primary);"><i class="fa-solid fa-box"></i></div>
                <div><div class="stat-value">{{ $totalOrders }}</div><div class="stat-label">Total Pesanan</div></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon-sm" style="background:rgba(239,68,68,0.1);color:var(--danger);"><i class="fa-solid fa-exclamation-triangle"></i></div>
                <div><div class="stat-value">{{ $unassignedCount }}</div><div class="stat-label">Belum Ada Kurir</div></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon-sm" style="background:rgba(59,130,246,0.1);color:#2563EB;"><i class="fa-solid fa-truck"></i></div>
                <div><div class="stat-value">{{ $shippingCount }}</div><div class="stat-label">Sedang Dikirim</div></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon-sm" style="background:rgba(16,185,129,0.1);color:var(--success);"><i class="fa-solid fa-check-circle"></i></div>
                <div><div class="stat-value">{{ $doneCount }}</div><div class="stat-label">Selesai</div></div>
            </div>
        </div>

        <div class="filter-tabs">
            <a href="{{ route('admin.pesanan.index') }}" class="filter-tab {{ $status === 'all' ? 'active' : '' }}">Semua</a>
            <a href="{{ route('admin.pesanan.index', ['status' => 'unassigned']) }}" class="filter-tab {{ $status === 'unassigned' ? 'active' : '' }}">
                🔴 Belum Ada Kurir @if($unassignedCount > 0)<span class="filter-badge">{{ $unassignedCount }}</span>@endif
            </a>
            <a href="{{ route('admin.pesanan.index', ['status' => 'process']) }}" class="filter-tab {{ $status === 'process' ? 'active' : '' }}">Diproses</a>
            <a href="{{ route('admin.pesanan.index', ['status' => 'shipping']) }}" class="filter-tab {{ $status === 'shipping' ? 'active' : '' }}">Dikirim</a>
            <a href="{{ route('admin.pesanan.index', ['status' => 'done']) }}" class="filter-tab {{ $status === 'done' ? 'active' : '' }}">Selesai</a>
            <a href="{{ route('admin.pesanan.index', ['status' => 'cancel']) }}" class="filter-tab {{ $status === 'cancel' ? 'active' : '' }}">Dibatalkan</a>
        </div>

        <div class="panel">
            <table class="data-table">
                <thead><tr><th>Kode Pesanan</th><th>Pelanggan</th><th>Total</th><th>Status</th><th>Kurir</th><th>Aksi</th></tr></thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td style="font-weight:700;color:var(--text-primary);">#{{ $order->order_code }}</td>
                        <td>
                            <div style="font-weight:600;">{{ $order->customer?->profile?->name ?? 'User' }}</div>
                            <div style="font-size:12px;color:var(--text-muted);">{{ Str::limit($order->address, 40) }}</div>
                        </td>
                        <td style="font-weight:700;">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</td>
                        <td>
                            @switch($order->order_status)
                                @case('process')<span class="badge badge-process">Diproses</span>@break
                                @case('shipping')<span class="badge badge-shipping">Dikirim</span>@break
                                @case('done')<span class="badge badge-done">Selesai</span>@break
                                @case('cancel')<span class="badge badge-cancel">Dibatalkan</span>@break
                            @endswitch
                        </td>
                        <td>
                            @if($order->courier_id)
                                <span class="badge badge-assigned"><i class="fa-solid fa-user-check" style="margin-right:4px;"></i>{{ $order->courier?->profile?->name ?? 'Kurir' }}</span>
                            @else
                                <span class="badge badge-unassigned"><i class="fa-solid fa-user-xmark" style="margin-right:4px;"></i>Belum ada</span>
                            @endif
                        </td>
                        <td>
                            @if(!$order->courier_id && $order->order_status === 'process')
                                <form action="{{ route('admin.pesanan.assign', $order) }}" method="POST" class="assign-form">
                                    @csrf @method('PATCH')
                                    <select name="courier_id" class="assign-select" required>
                                        <option value="">Pilih Kurir</option>
                                        @foreach($availableCouriers as $courier)
                                            <option value="{{ $courier->user_id }}">{{ $courier->user?->profile?->name ?? 'Kurir' }} ({{ $courier->vehicle_type ?? '-' }})</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn-assign"><i class="fa-solid fa-paper-plane"></i> Assign</button>
                                </form>
                            @else
                                <span style="color:var(--text-muted);font-size:13px;">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6"><div class="empty-state"><i class="fa-solid fa-inbox"></i><div>Tidak ada pesanan.</div></div></td></tr>
                    @endforelse
                </tbody>
            </table>
            @if($orders->hasPages())
            <div class="pagination-wrap">
                @if($orders->onFirstPage())<span style="opacity:0.5;">← Prev</span>@else<a href="{{ $orders->previousPageUrl() }}">← Prev</a>@endif
                @foreach($orders->getUrlRange(1, $orders->lastPage()) as $page => $url)
                    @if($page == $orders->currentPage())<span class="current">{{ $page }}</span>@else<a href="{{ $url }}">{{ $page }}</a>@endif
                @endforeach
                @if($orders->hasMorePages())<a href="{{ $orders->nextPageUrl() }}">Next →</a>@else<span style="opacity:0.5;">Next →</span>@endif
            </div>
            @endif
        </div>
    </main>
</body>
</html>
