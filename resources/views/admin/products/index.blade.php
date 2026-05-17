<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Produk · Didi Design</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --primary: #B71C1C; --primary-light: #D32F2F;
            --bg-main: #F4F6F8; --bg-panel: #FFFFFF;
            --border: rgba(0,0,0,0.08); --text-primary: #1F2937;
            --text-secondary: #4B5563; --text-muted: #9CA3AF;
            --success: #10B981; --danger: #EF4444; --warning: #F59E0B;
        }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Outfit',sans-serif; background:var(--bg-main); color:var(--text-primary); display:flex; min-height:100vh; }
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
        .user-name { font-size:14px; font-weight:700; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .user-role { font-size:12px; color:var(--text-muted); }
        .btn-logout { background:none; border:none; color:var(--text-muted); cursor:pointer; padding:8px; transition:color .2s; }
        .btn-logout:hover { color:var(--danger); }
        .main-content { flex:1; margin-left:280px; padding:40px; }
        .header { display:flex; justify-content:space-between; align-items:center; margin-bottom:32px; }
        .page-title { font-size:28px; font-weight:800; }
        .breadcrumb { font-size:14px; color:var(--text-muted); margin-top:4px; }
        .breadcrumb a { color:var(--primary); text-decoration:none; }
        .btn-primary { background:var(--primary); color:white; border:none; padding:12px 24px; border-radius:10px; font-family:inherit; font-weight:600; font-size:14px; cursor:pointer; display:inline-flex; align-items:center; gap:8px; transition:all .2s; box-shadow:0 4px 12px rgba(183,28,28,.25); text-decoration:none; }
        .btn-primary:hover { background:var(--primary-light); transform:translateY(-2px); }
        .alert { padding:14px 18px; border-radius:12px; margin-bottom:24px; display:flex; align-items:center; gap:10px; font-weight:500; font-size:14px; }
        .alert-success { background:rgba(16,185,129,.1); border:1px solid rgba(16,185,129,.2); color:#047857; }
        .panel { background:white; border:1px solid var(--border); border-radius:20px; overflow:hidden; box-shadow:0 4px 20px rgba(0,0,0,.02); }
        .panel-header { padding:20px 24px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; }
        .panel-title { font-size:18px; font-weight:700; }
        table { width:100%; border-collapse:collapse; }
        th { text-align:left; padding:14px 20px; font-size:12px; text-transform:uppercase; letter-spacing:.8px; color:var(--text-muted); font-weight:600; border-bottom:1px solid var(--border); }
        td { padding:16px 20px; border-bottom:1px solid rgba(0,0,0,.04); font-size:14px; color:var(--text-secondary); vertical-align:middle; }
        tbody tr:hover { background:#FAFAFA; }
        tbody tr:last-child td { border-bottom:none; }
        .product-img { width:52px; height:52px; border-radius:10px; object-fit:cover; border:1px solid var(--border); }
        .product-img-placeholder { width:52px; height:52px; border-radius:10px; background:var(--bg-main); display:flex; align-items:center; justify-content:center; color:var(--text-muted); border:1px solid var(--border); }
        .product-name { font-weight:700; color:var(--text-primary); font-size:14px; }
        .badge { padding:5px 10px; border-radius:20px; font-size:11px; font-weight:700; }
        .badge-popular { background:rgba(183,28,28,.1); color:var(--primary); }
        .badge-new { background:rgba(16,185,129,.1); color:#047857; }
        .badge-no { background:#F3F4F6; color:var(--text-muted); }
        .action-btns { display:flex; gap:8px; }
        .btn-edit { background:rgba(245,158,11,.1); color:#D97706; border:none; padding:8px 14px; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:5px; transition:all .2s; }
        .btn-edit:hover { background:rgba(245,158,11,.2); }
        .btn-delete { background:rgba(239,68,68,.1); color:var(--danger); border:none; padding:8px 14px; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:5px; transition:all .2s; }
        .btn-delete:hover { background:rgba(239,68,68,.2); }
        .empty-state { text-align:center; padding:60px 20px; color:var(--text-muted); }
        .empty-state i { font-size:48px; margin-bottom:16px; opacity:.3; }
        .empty-state p { font-size:16px; margin-bottom:20px; }
        .pagination { padding:20px 24px; border-top:1px solid var(--border); display:flex; gap:8px; justify-content:flex-end; }
        .page-link { padding:8px 14px; border-radius:8px; border:1px solid var(--border); color:var(--text-secondary); text-decoration:none; font-size:13px; font-weight:600; transition:all .2s; background:white; }
        .page-link:hover, .page-link.active { background:var(--primary); color:white; border-color:var(--primary); }
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
        <a href="{{ route('admin.products.index') }}" class="nav-item active"><i class="fa-solid fa-box-open nav-icon"></i> Produk</a>
        <div class="nav-label">Transaksi</div>
        <a href="#" class="nav-item"><i class="fa-solid fa-cart-shopping nav-icon"></i> Pesanan</a>
        <a href="#" class="nav-item"><i class="fa-solid fa-print nav-icon"></i> Cetak File</a>
        <div class="nav-label">Pengiriman</div>
        <a href="#" class="nav-item"><i class="fa-solid fa-motorcycle nav-icon"></i> Kurir</a>
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

<main class="main-content">
    <div class="header">
        <div>
            <h1 class="page-title">Manajemen Produk</h1>
            <p class="breadcrumb"><a href="{{ route('admin.dashboard') }}">Dashboard</a> / Produk</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="btn-primary">
            <i class="fa-solid fa-plus"></i> Tambah Produk
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    <div class="panel">
        <div class="panel-header">
            <span class="panel-title">Semua Produk <span style="color:var(--text-muted);font-size:14px;font-weight:500;">({{ $products->total() }} produk)</span></span>
        </div>

        @if($products->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Populer</th>
                    <th>Baru</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:14px;">
                            @if($product->image)
                                <img src="{{ Storage::url($product->image) }}" class="product-img" alt="{{ $product->name }}">
                            @else
                                <div class="product-img-placeholder"><i class="fa-solid fa-image"></i></div>
                            @endif
                            <div>
                                <div class="product-name">{{ $product->name }}</div>
                                <div style="font-size:12px;color:var(--text-muted);margin-top:2px;">ID #{{ $product->id }}</div>
                            </div>
                        </div>
                    </td>
                    <td>{{ $product->category->name ?? '-' }}</td>
                    <td style="font-weight:700;color:var(--primary);">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                    <td>
                        <span style="font-weight:600;color:{{ $product->stock < 5 ? 'var(--danger)' : 'var(--text-primary)' }}">
                            {{ $product->stock }}
                        </span>
                    </td>
                    <td><span class="badge {{ $product->is_popular ? 'badge-popular' : 'badge-no' }}">{{ $product->is_popular ? 'Ya' : 'Tidak' }}</span></td>
                    <td><span class="badge {{ $product->is_new_arrival ? 'badge-new' : 'badge-no' }}">{{ $product->is_new_arrival ? 'Ya' : 'Tidak' }}</span></td>
                    <td>
                        <div class="action-btns">
                            <a href="{{ route('admin.products.edit', $product) }}" class="btn-edit"><i class="fa-solid fa-pen"></i> Edit</a>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus produk ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-delete"><i class="fa-solid fa-trash"></i> Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if($products->hasPages())
        <div class="pagination">
            @foreach($products->links()->elements[0] as $page => $url)
                <a href="{{ $url }}" class="page-link {{ $products->currentPage() == $page ? 'active' : '' }}">{{ $page }}</a>
            @endforeach
        </div>
        @endif
        @else
        <div class="empty-state">
            <i class="fa-solid fa-box-open"></i>
            <p>Belum ada produk. Mulai tambahkan produk pertama Anda!</p>
            <a href="{{ route('admin.products.create') }}" class="btn-primary" style="display:inline-flex;">
                <i class="fa-solid fa-plus"></i> Tambah Produk Pertama
            </a>
        </div>
        @endif
    </div>
</main>
</body>
</html>
