<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak File · Didi Design</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>@include('admin.partials.styles')</style>
</head>
<body>
@include('admin.partials.sidebar', ['active' => 'print-orders'])

<main class="main-content">
    <div class="header">
        <div>
            <h1 class="page-title">Cetak File</h1>
            <p class="breadcrumb"><a href="{{ route('admin.dashboard') }}">Dashboard</a> / Cetak File</p>
        </div>
        <div class="filter-bar">
            <a href="{{ route('admin.print-orders.index') }}" class="filter-btn {{ !request('status') ? 'active' : '' }}">Semua</a>
            <a href="{{ route('admin.print-orders.index', ['status'=>'process']) }}" class="filter-btn {{ request('status')==='process' ? 'active' : '' }}">Diproses</a>
            <a href="{{ route('admin.print-orders.index', ['status'=>'printing']) }}" class="filter-btn {{ request('status')==='printing' ? 'active' : '' }}">Dicetak</a>
            <a href="{{ route('admin.print-orders.index', ['status'=>'done']) }}" class="filter-btn {{ request('status')==='done' ? 'active' : '' }}">Selesai</a>
            <a href="{{ route('admin.print-orders.index', ['status'=>'cancel']) }}" class="filter-btn {{ request('status')==='cancel' ? 'active' : '' }}">Batal</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
    @endif

    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(183,28,28,.1);color:var(--primary);"><i class="fa-solid fa-print"></i></div>
            <div><div class="stat-value">{{ \App\Models\PrintOrder::count() }}</div><div class="stat-label">Total Order Cetak</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(245,185,66,.1);color:#D97706;"><i class="fa-solid fa-clock"></i></div>
            <div><div class="stat-value">{{ \App\Models\PrintOrder::where('order_status','process')->count() }}</div><div class="stat-label">Diproses</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(59,130,246,.1);color:#1E40AF;"><i class="fa-solid fa-gears"></i></div>
            <div><div class="stat-value">{{ \App\Models\PrintOrder::where('order_status','printing')->count() }}</div><div class="stat-label">Sedang Cetak</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(16,185,129,.1);color:var(--success);"><i class="fa-solid fa-check-circle"></i></div>
            <div><div class="stat-value">{{ \App\Models\PrintOrder::where('order_status','done')->count() }}</div><div class="stat-label">Selesai</div></div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header"><div class="panel-title"><i class="fa-solid fa-list"></i> Daftar Order Cetak</div></div>
        @if($printOrders->count())
        <table class="data-table">
            <thead><tr><th>Kode</th><th>Pelanggan</th><th>Jenis</th><th>Ukuran</th><th>Qty</th><th>Total</th><th>Status</th><th>Tanggal</th><th>Aksi</th></tr></thead>
            <tbody>
            @foreach($printOrders as $po)
            <tr>
                <td style="font-weight:700;color:var(--text-primary);">#{{ $po->order_code }}</td>
                <td>{{ $po->user?->profile?->name ?? 'User' }}</td>
                <td>{{ $po->service_label }}</td>
                <td>{{ $po->paper_size ?? ($po->width_meter.'×'.$po->height_meter.'m') }}</td>
                <td style="font-weight:700;">{{ $po->quantity }}</td>
                <td style="font-weight:700;">Rp {{ number_format($po->total_price, 0, ',', '.') }}</td>
                <td>
                    @php $sc = ['process'=>'badge-warning','printing'=>'badge-info','done'=>'badge-success','cancel'=>'badge-danger']; @endphp
                    <span class="badge {{ $sc[$po->order_status] ?? 'badge-muted' }}">{{ $po->status_label }}</span>
                </td>
                <td style="font-size:13px;">{{ $po->created_at->format('d M Y') }}</td>
                <td>
                    <form action="{{ route('admin.print-orders.status', $po) }}" method="POST" style="display:inline;">
                        @csrf @method('PATCH')
                        <select name="order_status" onchange="this.form.submit()" class="btn-sm" style="border:1px solid var(--border);padding:6px 8px;border-radius:8px;font-family:inherit;cursor:pointer;">
                            <option value="process" {{ $po->order_status==='process' ? 'selected' : '' }}>Diproses</option>
                            <option value="printing" {{ $po->order_status==='printing' ? 'selected' : '' }}>Dicetak</option>
                            <option value="done" {{ $po->order_status==='done' ? 'selected' : '' }}>Selesai</option>
                            <option value="cancel" {{ $po->order_status==='cancel' ? 'selected' : '' }}>Batal</option>
                        </select>
                    </form>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        @if($printOrders->hasPages())<div style="padding:16px 20px;display:flex;justify-content:center;">{{ $printOrders->withQueryString()->links() }}</div>@endif
        @else
        <div class="empty-state"><i class="fa-solid fa-print"></i><p>Belum ada order cetak.</p></div>
        @endif
    </div>
</main>
</body>
</html>
