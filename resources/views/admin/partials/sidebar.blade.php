{{-- Sidebar partial — @include('admin.partials.sidebar', ['active' => 'dashboard']) --}}
<aside class="sidebar">
    <div class="brand">
        <div class="brand-icon"><i class="fa-solid fa-pen-nib"></i></div>
        <div class="brand-name">Didi<span>Design</span></div>
    </div>
    <nav class="nav-menu">
        <div class="nav-label">Main Menu</div>
        <a href="{{ route('admin.dashboard') }}" class="nav-item {{ ($active ?? '') === 'dashboard' ? 'active' : '' }}"><i class="fa-solid fa-house nav-icon"></i> Dashboard</a>
        <a href="{{ route('admin.products.index') }}" class="nav-item {{ ($active ?? '') === 'products' ? 'active' : '' }}"><i class="fa-solid fa-box-open nav-icon"></i> Produk</a>

        <div class="nav-label">Transaksi</div>
        <a href="{{ route('admin.orders.index') }}" class="nav-item {{ ($active ?? '') === 'orders' ? 'active' : '' }}"><i class="fa-solid fa-cart-shopping nav-icon"></i> Pesanan</a>
        <a href="{{ route('admin.print-orders.index') }}" class="nav-item {{ ($active ?? '') === 'print-orders' ? 'active' : '' }}"><i class="fa-solid fa-print nav-icon"></i> Cetak File</a>

        <div class="nav-label">Pengiriman</div>
        <a href="{{ route('admin.couriers.index') }}" class="nav-item {{ ($active ?? '') === 'couriers' ? 'active' : '' }}"><i class="fa-solid fa-motorcycle nav-icon"></i> Kurir</a>

        <div class="nav-label">Pengaturan</div>
        <a href="{{ route('admin.users.index') }}" class="nav-item {{ ($active ?? '') === 'users' ? 'active' : '' }}"><i class="fa-solid fa-users nav-icon"></i> Pengguna</a>
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
