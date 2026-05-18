<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan · Didi Design</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        @include('admin.partials.styles')
        .chart-container { position:relative; height:300px; padding:24px 28px; }
        .top-list { padding:0 28px 24px; }
        .top-item { display:flex; align-items:center; justify-content:space-between; padding:14px 0; border-bottom:1px solid var(--border); }
        .top-item:last-child { border-bottom:none; }
        .top-rank { width:30px; height:30px; border-radius:50%; background:var(--bg-main); font-weight:800; font-size:13px; display:flex; align-items:center; justify-content:center; color:var(--text-muted); }
        .top-rank.gold { background:rgba(245,185,66,.2); color:#D97706; }
    </style>
</head>
<body>
@include('admin.partials.sidebar')

<main class="main-content">
    <div class="header">
        <div>
            <h1 class="page-title">Laporan</h1>
            <p class="breadcrumb"><a href="{{ route('admin.dashboard') }}">Dashboard</a> / Laporan</p>
        </div>
    </div>

    {{-- Stats --}}
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(183,28,28,.1);color:var(--primary);"><i class="fa-solid fa-wallet"></i></div>
            <div>
                <div class="stat-value" style="font-size:18px;">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</div>
                <div class="stat-label">Total Pendapatan</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(59,130,246,.1);color:#3B82F6;"><i class="fa-solid fa-receipt"></i></div>
            <div>
                <div class="stat-value">{{ $stats['total_orders'] }}</div>
                <div class="stat-label">Total Pesanan</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(16,185,129,.1);color:#10B981;"><i class="fa-solid fa-check-circle"></i></div>
            <div>
                <div class="stat-value">{{ $stats['completed_orders'] }}</div>
                <div class="stat-label">Pesanan Selesai</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(245,185,66,.15);color:#D97706;"><i class="fa-solid fa-box-open"></i></div>
            <div>
                <div class="stat-value">{{ $stats['total_products'] }}</div>
                <div class="stat-label">Total Produk</div>
            </div>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:2fr 1fr;gap:24px;">
        {{-- Chart Pendapatan Bulanan --}}
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title"><i class="fa-solid fa-chart-line"></i> Pendapatan Bulanan ({{ now()->year }})</div>
            </div>
            <div class="chart-container">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        {{-- Top Produk --}}
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title"><i class="fa-solid fa-trophy"></i> Produk Terlaris</div>
            </div>
            <div class="top-list">
                @forelse($topProducts as $i => $product)
                <div class="top-item">
                    <div style="display:flex;align-items:center;gap:14px;">
                        <div class="top-rank {{ $i === 0 ? 'gold' : '' }}">{{ $i + 1 }}</div>
                        <div>
                            <div style="font-weight:700;font-size:14px;color:var(--text-primary);">{{ Str::limit($product->name, 25) }}</div>
                            <div style="font-size:12px;color:var(--text-muted);">{{ $product->order_items_count }} terjual</div>
                        </div>
                    </div>
                    <span class="badge badge-info">{{ $product->order_items_count }}x</span>
                </div>
                @empty
                <div class="empty-state"><i class="fa-solid fa-trophy"></i><p>Belum ada data.</p></div>
                @endforelse
            </div>
        </div>
    </div>
</main>

<script>
const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
const revenueData = Array(12).fill(0);
@foreach($revenueByMonth as $row)
revenueData[{{ $row->month - 1 }}] = {{ $row->total }};
@endforeach

new Chart(document.getElementById('revenueChart'), {
    type: 'bar',
    data: {
        labels: months,
        datasets: [{
            label: 'Pendapatan (Rp)',
            data: revenueData,
            backgroundColor: 'rgba(183,28,28,0.15)',
            borderColor: '#B71C1C',
            borderWidth: 2,
            borderRadius: 8,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: {
                ticks: { callback: v => 'Rp ' + (v/1000).toFixed(0) + 'k' },
                grid: { color: 'rgba(0,0,0,0.04)' }
            },
            x: { grid: { display: false } }
        }
    }
});
</script>
</body>
</html>
