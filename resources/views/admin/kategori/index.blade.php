<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Kategori · Didi Design</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        @include('admin.partials.styles')
        .form-control { width:100%; background:var(--bg-main); border:1px solid var(--border); border-radius:10px; padding:11px 16px; font-family:inherit; font-size:14px; font-weight:500; color:var(--text-primary); outline:none; transition:border-color .2s,box-shadow .2s; }
        .form-control:focus { border-color:var(--primary); box-shadow:0 0 0 4px rgba(183,28,28,.08); background:white; }
        .inline-form { display:flex; gap:12px; align-items:center; }
        .modal-backdrop { display:none; position:fixed; inset:0; background:rgba(0,0,0,.4); z-index:200; align-items:center; justify-content:center; }
        .modal-backdrop.open { display:flex; }
        .modal { background:white; border-radius:20px; padding:32px; width:420px; box-shadow:0 20px 60px rgba(0,0,0,.15); }
        .modal-title { font-size:18px; font-weight:800; margin-bottom:24px; }
        .btn-group { display:flex; gap:12px; margin-top:20px; }
        .btn-outline { background:white; border:1px solid var(--border); color:var(--text-secondary); padding:11px 20px; border-radius:10px; font-family:inherit; font-weight:600; font-size:14px; cursor:pointer; transition:all .2s; }
        .btn-outline:hover { background:var(--bg-main); }
        .btn-save { background:var(--primary); color:white; border:none; padding:11px 24px; border-radius:10px; font-family:inherit; font-weight:700; font-size:14px; cursor:pointer; transition:all .2s; }
        .btn-save:hover { background:var(--primary-light); }
        .alert-error { background:rgba(239,68,68,.08); border:1px solid rgba(239,68,68,.2); color:#991B1B; padding:14px 20px; border-radius:12px; margin-bottom:24px; font-weight:600; font-size:14px; }
    </style>
</head>
<body>
@include('admin.partials.sidebar')

<main class="main-content">
    <div class="header">
        <div>
            <h1 class="page-title">Manajemen Kategori</h1>
            <p class="breadcrumb"><a href="{{ route('admin.dashboard') }}">Dashboard</a> / Kategori</p>
        </div>
        <button class="btn-save" onclick="document.getElementById('modalTambah').classList.add('open')">
            <i class="fa-solid fa-plus"></i> Tambah Kategori
        </button>
    </div>

    @if(session('success'))
    <div class="alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert-error"><i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}</div>
    @endif

    <div class="panel">
        <div class="panel-header">
            <div class="panel-title"><i class="fa-solid fa-tags"></i> Daftar Kategori ({{ $categories->count() }})</div>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Kategori</th>
                    <th>Jumlah Produk</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $i => $cat)
                <tr>
                    <td style="color:var(--text-muted);font-weight:700;">{{ $i + 1 }}</td>
                    <td style="font-weight:700;color:var(--text-primary);">{{ $cat->name }}</td>
                    <td>
                        <span class="badge badge-info">{{ $cat->products_count }} Produk</span>
                    </td>
                    <td>
                        <div style="display:flex;gap:8px;">
                            <button class="btn-sm btn-primary-sm" onclick="openEdit({{ $cat->id }}, '{{ addslashes($cat->name) }}')">
                                <i class="fa-solid fa-pen"></i> Edit
                            </button>
                            @if($cat->products_count == 0)
                            <form action="{{ route('admin.kategori.destroy', $cat) }}" method="POST" onsubmit="return confirm('Yakin hapus kategori ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-delete"><i class="fa-solid fa-trash"></i></button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4">
                        <div class="empty-state">
                            <i class="fa-solid fa-tags"></i>
                            <p>Belum ada kategori.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</main>

{{-- Modal Tambah --}}
<div class="modal-backdrop" id="modalTambah">
    <div class="modal">
        <div class="modal-title"><i class="fa-solid fa-tags" style="color:var(--primary);margin-right:10px;"></i>Tambah Kategori</div>
        <form action="{{ route('admin.kategori.store') }}" method="POST">
            @csrf
            <label style="font-size:13px;font-weight:600;color:var(--text-secondary);display:block;margin-bottom:8px;">NAMA KATEGORI</label>
            <input type="text" name="name" class="form-control" placeholder="Contoh: ATK" required>
            <div class="btn-group">
                <button type="submit" class="btn-save" style="flex:1;justify-content:center;">Simpan</button>
                <button type="button" class="btn-outline" onclick="document.getElementById('modalTambah').classList.remove('open')">Batal</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit --}}
<div class="modal-backdrop" id="modalEdit">
    <div class="modal">
        <div class="modal-title"><i class="fa-solid fa-pen" style="color:var(--primary);margin-right:10px;"></i>Edit Kategori</div>
        <form id="formEdit" method="POST">
            @csrf @method('PUT')
            <label style="font-size:13px;font-weight:600;color:var(--text-secondary);display:block;margin-bottom:8px;">NAMA KATEGORI</label>
            <input type="text" name="name" id="editName" class="form-control" required>
            <div class="btn-group">
                <button type="submit" class="btn-save" style="flex:1;justify-content:center;">Simpan Perubahan</button>
                <button type="button" class="btn-outline" onclick="document.getElementById('modalEdit').classList.remove('open')">Batal</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEdit(id, name) {
    document.getElementById('editName').value = name;
    document.getElementById('formEdit').action = `/admin/kategori/${id}`;
    document.getElementById('modalEdit').classList.add('open');
}
document.querySelectorAll('.modal-backdrop').forEach(m => {
    m.addEventListener('click', e => { if (e.target === m) m.classList.remove('open'); });
});
</script>
</body>
</html>
