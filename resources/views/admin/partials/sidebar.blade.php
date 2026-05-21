{{-- Sidebar partial — @include('admin.partials.sidebar', ['active' => 'dashboard']) --}}
<aside class="sidebar">
    <div class="brand">
        <div class="brand-icon"><i class="fa-solid fa-pen-nib"></i></div>
        <div class="brand-name">Didi<span>Design</span></div>
    </div>
    <nav class="nav-menu">
        <div class="nav-label">Main Menu</div>
        <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="fa-solid fa-house nav-icon"></i> Dashboard</a>
        <a href="{{ route('admin.kategori.index') }}" class="nav-item {{ request()->routeIs('admin.kategori.*') ? 'active' : '' }}"><i class="fa-solid fa-tags nav-icon"></i> Kategori</a>
        <a href="{{ route('admin.produk.index') }}" class="nav-item {{ request()->routeIs('admin.produk.*') ? 'active' : '' }}"><i class="fa-solid fa-box-open nav-icon"></i> Produk</a>

        <div class="nav-label">Transaksi</div>
        <a href="{{ route('admin.pesanan.index') }}" class="nav-item {{ request()->routeIs('admin.pesanan.*') ? 'active' : '' }}"><i class="fa-solid fa-cart-shopping nav-icon"></i> Pesanan</a>
        <a href="{{ route('admin.pembayaran.index') }}" class="nav-item {{ request()->routeIs('admin.pembayaran.*') ? 'active' : '' }}"><i class="fa-solid fa-money-bill-transfer nav-icon"></i> Pembayaran</a>
        <a href="{{ route('admin.print-orders.index') }}" class="nav-item {{ request()->routeIs('admin.print-orders.*') ? 'active' : '' }}"><i class="fa-solid fa-print nav-icon"></i> Cetak File</a>

        <div class="nav-label">Pengiriman</div>
        <a href="{{ route('admin.kurir.index') }}" class="nav-item {{ request()->routeIs('admin.kurir.*') ? 'active' : '' }}"><i class="fa-solid fa-motorcycle nav-icon"></i> Kurir</a>

        <div class="nav-label">Laporan</div>
        <a href="{{ route('admin.laporan.index') }}" class="nav-item {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}"><i class="fa-solid fa-chart-line nav-icon"></i> Laporan</a>

        <div class="nav-label">Pengaturan</div>
        <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"><i class="fa-solid fa-users nav-icon"></i> Pengguna</a>
    </nav>
    <div class="sidebar-footer">
        <div class="user-profile">
            <div class="user-avatar">{{ strtoupper(substr(Auth::user()->email, 0, 1)) }}</div>
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
