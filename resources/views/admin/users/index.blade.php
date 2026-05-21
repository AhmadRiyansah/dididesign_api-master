<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengguna · Didi Design</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>@include('admin.partials.styles')</style>
</head>
<body>
@include('admin.partials.sidebar', ['active' => 'users'])

<main class="main-content">
    <div class="header">
        <div>
            <h1 class="page-title">Pengguna</h1>
            <p class="breadcrumb"><a href="{{ route('admin.dashboard') }}">Dashboard</a> / Pengguna</p>
        </div>
        <div class="filter-bar">
            <a href="{{ route('admin.users.index') }}" class="filter-btn {{ !request('role') ? 'active' : '' }}">Semua</a>
            <a href="{{ route('admin.users.index', ['role'=>'user']) }}" class="filter-btn {{ request('role')==='user' ? 'active' : '' }}">Customer</a>
            <a href="{{ route('admin.users.index', ['role'=>'kurir']) }}" class="filter-btn {{ request('role')==='kurir' ? 'active' : '' }}">Kurir</a>
            <a href="{{ route('admin.users.index', ['role'=>'admin']) }}" class="filter-btn {{ request('role')==='admin' ? 'active' : '' }}">Admin</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
    @endif

    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(183,28,28,.1);color:var(--primary);"><i class="fa-solid fa-users"></i></div>
            <div><div class="stat-value">{{ \App\Models\User::count() }}</div><div class="stat-label">Total Pengguna</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(16,185,129,.1);color:var(--success);"><i class="fa-solid fa-user"></i></div>
            <div><div class="stat-value">{{ \App\Models\User::where('role','user')->count() }}</div><div class="stat-label">Customer</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(245,185,66,.1);color:#D97706;"><i class="fa-solid fa-motorcycle"></i></div>
            <div><div class="stat-value">{{ \App\Models\User::where('role','kurir')->count() }}</div><div class="stat-label">Kurir</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(59,130,246,.1);color:#1E40AF;"><i class="fa-solid fa-shield"></i></div>
            <div><div class="stat-value">{{ \App\Models\User::where('role','admin')->count() }}</div><div class="stat-label">Admin</div></div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header"><div class="panel-title"><i class="fa-solid fa-list"></i> Daftar Pengguna</div></div>
        @if($users->count())
        <table class="data-table">
            <thead><tr><th>Pengguna</th><th>Email</th><th>Telepon</th><th>Role</th><th>Terdaftar</th><th>Aksi</th></tr></thead>
            <tbody>
            @foreach($users as $user)
            <tr>
                <td>
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div style="width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--primary-light));color:white;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:15px;">
                            {{ strtoupper(substr($user->profile->name ?? $user->email, 0, 1)) }}
                        </div>
                        <div style="font-weight:700;color:var(--text-primary);">{{ $user->profile->name ?? '-' }}</div>
                    </div>
                </td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->profile->phone ?? '-' }}</td>
                <td>
                    @php $rc = ['admin'=>'badge-danger','kurir'=>'badge-warning','user'=>'badge-success']; @endphp
                    @php $rl = ['admin'=>'Admin','kurir'=>'Kurir','user'=>'Customer']; @endphp
                    <span class="badge {{ $rc[$user->role->value ?? $user->role] ?? 'badge-muted' }}">{{ $rl[$user->role->value ?? $user->role] ?? $user->role }}</span>
                </td>
                <td style="font-size:13px;">{{ $user->created_at->format('d M Y') }}</td>
                <td>
                    @if(($user->role->value ?? $user->role) !== 'admin')
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Yakin hapus pengguna ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-delete"><i class="fa-solid fa-trash"></i></button>
                    </form>
                    @else
                    <span style="color:var(--text-muted);font-size:12px;">—</span>
                    @endif
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        @if($users->hasPages())<div style="padding:16px 20px;display:flex;justify-content:center;">{{ $users->withQueryString()->links() }}</div>@endif
        @else
        <div class="empty-state"><i class="fa-solid fa-users"></i><p>Belum ada pengguna.</p></div>
        @endif
    </div>
</main>
</body>
</html>
