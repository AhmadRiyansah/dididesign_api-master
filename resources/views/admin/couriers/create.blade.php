<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kurir · Didi Design</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --primary:#B71C1C; --primary-light:#D32F2F;
            --bg-main:#F4F6F8; --bg-panel:#FFFFFF;
            --border:rgba(0,0,0,0.08); --text-primary:#1F2937;
            --text-secondary:#4B5563; --text-muted:#9CA3AF;
            --danger:#EF4444; --success:#10B981;
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
        .user-name { font-size:14px; font-weight:700; }
        .user-role { font-size:12px; color:var(--text-muted); }
        .btn-logout { background:none; border:none; color:var(--text-muted); cursor:pointer; padding:8px; transition:color .2s; }
        .btn-logout:hover { color:var(--danger); }

        .main-content { flex:1; margin-left:280px; padding:40px; }
        .header { margin-bottom:32px; }
        .page-title { font-size:28px; font-weight:800; }
        .breadcrumb { font-size:14px; color:var(--text-muted); margin-top:4px; }
        .breadcrumb a { color:var(--primary); text-decoration:none; }

        .form-grid { display:grid; grid-template-columns:2fr 1fr; gap:24px; align-items:start; }
        .panel { background:white; border:1px solid var(--border); border-radius:20px; padding:28px; box-shadow:0 4px 20px rgba(0,0,0,.02); }
        .panel-title { font-size:16px; font-weight:700; margin-bottom:24px; padding-bottom:16px; border-bottom:1px solid var(--border); display:flex; align-items:center; gap:10px; }
        .panel-title i { color:var(--primary); }
        .form-group { margin-bottom:20px; }
        .form-label { display:block; font-size:13px; font-weight:600; color:var(--text-secondary); margin-bottom:8px; text-transform:uppercase; letter-spacing:.5px; }
        .form-label .required { color:var(--primary); margin-left:3px; }
        .form-control { width:100%; background:var(--bg-main); border:1px solid var(--border); border-radius:10px; padding:13px 16px; font-family:inherit; font-size:15px; font-weight:500; color:var(--text-primary); outline:none; transition:border-color .2s, box-shadow .2s; }
        .form-control:focus { border-color:var(--primary); box-shadow:0 0 0 4px rgba(183,28,28,.08); background:white; }
        .form-control.is-invalid { border-color:var(--danger); }
        .field-error { font-size:12px; color:var(--danger); margin-top:6px; display:flex; align-items:center; gap:5px; font-weight:500; }
        .btn-primary { background:var(--primary); color:white; border:none; padding:14px 28px; border-radius:10px; font-family:inherit; font-weight:700; font-size:15px; cursor:pointer; display:inline-flex; align-items:center; gap:8px; transition:all .2s; box-shadow:0 4px 12px rgba(183,28,28,.25); width:100%; justify-content:center; }
        .btn-primary:hover { background:var(--primary-light); transform:translateY(-1px); }
        .btn-secondary { background:white; color:var(--text-secondary); border:1px solid var(--border); padding:14px 24px; border-radius:10px; font-family:inherit; font-weight:600; font-size:15px; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:8px; transition:all .2s; width:100%; justify-content:center; margin-top:10px; }
        .btn-secondary:hover { background:var(--bg-main); }
    </style>
</head>
<body>

@include('admin.partials.sidebar')

<main class="main-content">
    <div class="header">
        <h1 class="page-title">Tambah Kurir Baru</h1>
        <p class="breadcrumb">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a> /
            <a href="{{ route('admin.kurir.index') }}">Kurir</a> / Tambah
        </p>
    </div>

    <form action="{{ route('admin.kurir.store') }}" method="POST">
        @csrf
        <div class="form-grid">
            <div>
                <div class="panel" style="margin-bottom:24px;">
                    <div class="panel-title"><i class="fa-solid fa-user"></i> Informasi Akun</div>

                    <div class="form-group">
                        <label class="form-label">Nama Lengkap <span class="required">*</span></label>
                        <input type="text" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                            placeholder="Nama kurir" value="{{ old('name') }}" required>
                        @error('name') <div class="field-error"><i class="fa-solid fa-triangle-exclamation"></i> {{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email <span class="required">*</span></label>
                        <input type="email" name="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                            placeholder="kurir@email.com" value="{{ old('email') }}" required>
                        @error('email') <div class="field-error"><i class="fa-solid fa-triangle-exclamation"></i> {{ $message }}</div> @enderror
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                        <div class="form-group">
                            <label class="form-label">No. Telepon</label>
                            <input type="text" name="phone" class="form-control" placeholder="08xxxxxxxxxx" value="{{ old('phone') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Password <span class="required">*</span></label>
                            <input type="password" name="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                                placeholder="Min. 8 karakter" required>
                            @error('password') <div class="field-error"><i class="fa-solid fa-triangle-exclamation"></i> {{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="panel">
                    <div class="panel-title"><i class="fa-solid fa-motorcycle"></i> Informasi Kendaraan</div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                        <div class="form-group">
                            <label class="form-label">Jenis Kendaraan</label>
                            <input type="text" name="vehicle_type" class="form-control" placeholder="Motor / Mobil" value="{{ old('vehicle_type') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Plat Nomor</label>
                            <input type="text" name="plate_number" class="form-control" placeholder="BL 1234 XX" value="{{ old('plate_number') }}">
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <div class="panel">
                    <div class="panel-title"><i class="fa-solid fa-floppy-disk"></i> Simpan</div>
                    <button type="submit" class="btn-primary"><i class="fa-solid fa-plus"></i> Tambah Kurir</button>
                    <a href="{{ route('admin.kurir.index') }}" class="btn-secondary"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
                </div>
            </div>
        </div>
    </form>
</main>

</body>
</html>
