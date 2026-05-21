<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pembayaran · Didi Design</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        @include('admin.partials.styles')
    </style>
</head>
<body>
@include('admin.partials.sidebar')

<main class="main-content">
    <div class="header">
        <div>
            <h1 class="page-title">Manajemen Pembayaran</h1>
            <p class="breadcrumb"><a href="{{ route('admin.dashboard') }}">Dashboard</a> / Pembayaran</p>
        </div>
    </div>

    {{-- Stats --}}
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(16,185,129,.1);color:#10B981;">
                <i class="fa-solid fa-money-bill-wave"></i>
            </div>
            <div>
                <div class="stat-value" style="font-size:20px;">Rp {{ number_format($stats['total_paid'], 0, ',', '.') }}</div>
                <div class="stat-label">Total Pendapatan</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(245,185,66,.15);color:#D97706;">
                <i class="fa-solid fa-hourglass-half"></i>
            </div>
            <div>
                <div class="stat-value">{{ $stats['total_pending'] }}</div>
                <div class="stat-label">Menunggu Pembayaran</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(183,28,28,.1);color:var(--primary);">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <div>
                <div class="stat-value">{{ $stats['total_orders'] }}</div>
                <div class="stat-label">Total Pesanan</div>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div style="display:flex;gap:8px;margin-bottom:24px;">
        <a href="{{ route('admin.pembayaran.index') }}" class="filter-btn {{ $status === 'all' ? 'active' : '' }}">Semua</a>
        <a href="{{ route('admin.pembayaran.index', ['status' => 'paid']) }}" class="filter-btn {{ $status === 'paid' ? 'active' : '' }}">Lunas</a>
        <a href="{{ route('admin.pembayaran.index', ['status' => 'pending']) }}" class="filter-btn {{ $status === 'pending' ? 'active' : '' }}">Menunggu</a>
    </div>

    <div class="panel">
        <div class="panel-header">
            <div class="panel-title"><i class="fa-solid fa-money-bill-transfer"></i> Daftar Pembayaran</div>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Kode Pesanan</th>
                    <th>Pelanggan</th>
                    <th>Total</th>
                    <th>Metode</th>
                    <th>Status Pembayaran</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td style="font-weight:700;color:var(--text-primary);">#{{ $order->order_code }}</td>
                    <td>{{ $order->customer->profile->name ?? $order->customer->email ?? '-' }}</td>
                    <td style="font-weight:700;">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</td>
                    <td>{{ strtoupper($order->payment_method ?? 'N/A') }}</td>
                    <td>
                        @if($order->payment_status === 'paid')
                            <span class="badge badge-success"><i class="fa-solid fa-circle-check"></i> Lunas</span>
                        @else
                            <span class="badge badge-warning"><i class="fa-solid fa-hourglass-half"></i> Menunggu</span>
                        @endif
                    </td>
                    <td style="color:var(--text-muted);">{{ $order->created_at->format('d M Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <i class="fa-solid fa-money-bill-transfer"></i>
                            <p>Belum ada data pembayaran.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($orders->hasPages())
        <div style="padding:20px 28px;">{{ $orders->appends(['status' => $status])->links() }}</div>
        @endif
    </div>
</main>
</body>
</html>
