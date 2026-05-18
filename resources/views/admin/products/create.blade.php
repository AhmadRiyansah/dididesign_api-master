<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($product) ? 'Edit' : 'Tambah' }} Produk · Didi Design</title>
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
        .brand-icon { width:40px; height:40px; background:var(--primary); border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:18px; color:white; }
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
        .form-control.is-invalid { border-color:var(--danger); box-shadow:0 0 0 4px rgba(239,68,68,.08); }
        select.form-control { cursor:pointer; }
        textarea.form-control { resize:vertical; min-height:120px; }
        .field-error { font-size:12px; color:var(--danger); margin-top:6px; display:flex; align-items:center; gap:5px; font-weight:500; }
        .toggle-group { display:flex; flex-direction:column; gap:12px; }
        .toggle-item { display:flex; align-items:center; justify-content:space-between; padding:14px 16px; background:var(--bg-main); border-radius:10px; border:1px solid var(--border); cursor:pointer; }
        .toggle-label { font-size:14px; font-weight:600; color:var(--text-primary); }
        .toggle-sub { font-size:12px; color:var(--text-muted); margin-top:2px; }
        .toggle-switch { position:relative; width:48px; height:26px; }
        .toggle-switch input { opacity:0; width:0; height:0; }
        .toggle-slider { position:absolute; inset:0; background:#D1D5DB; border-radius:50px; transition:.3s; cursor:pointer; }
        .toggle-slider::before { content:''; position:absolute; height:20px; width:20px; left:3px; bottom:3px; background:white; border-radius:50%; transition:.3s; }
        input:checked + .toggle-slider { background:var(--primary); }
        input:checked + .toggle-slider::before { transform:translateX(22px); }
        .image-upload-area { border:2px dashed var(--border); border-radius:12px; padding:30px; text-align:center; cursor:pointer; transition:all .2s; background:var(--bg-main); position:relative; }
        .image-upload-area:hover { border-color:var(--primary); background:rgba(183,28,28,.02); }
        .image-upload-area input[type="file"] { position:absolute; inset:0; opacity:0; cursor:pointer; }
        .image-preview { width:100%; max-height:200px; object-fit:contain; border-radius:8px; margin-top:12px; display:none; }
        .btn-group { display:flex; gap:12px; margin-top:8px; }
        .btn-primary { background:var(--primary); color:white; border:none; padding:14px 28px; border-radius:10px; font-family:inherit; font-weight:700; font-size:15px; cursor:pointer; display:inline-flex; align-items:center; gap:8px; transition:all .2s; box-shadow:0 4px 12px rgba(183,28,28,.25); }
        .btn-primary:hover { background:var(--primary-light); transform:translateY(-1px); }
        .btn-secondary { background:white; color:var(--text-secondary); border:1px solid var(--border); padding:14px 24px; border-radius:10px; font-family:inherit; font-weight:600; font-size:15px; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:8px; transition:all .2s; }
        .btn-secondary:hover { background:var(--bg-main); }
        /* ── Variant Section ── */
        .variant-card { background:var(--bg-main); border:1px solid var(--border); border-radius:12px; padding:16px; margin-bottom:12px; position:relative; }
        .variant-card-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; }
        .variant-badge { font-size:12px; font-weight:700; color:var(--primary); background:rgba(183,28,28,.08); padding:4px 10px; border-radius:20px; }
        .btn-remove-variant { background:none; border:none; color:var(--text-muted); cursor:pointer; font-size:16px; padding:4px 8px; border-radius:6px; transition:all .2s; }
        .btn-remove-variant:hover { color:var(--danger); background:rgba(239,68,68,.08); }
        .variant-grid { display:grid; grid-template-columns:1fr 1fr 1fr; gap:12px; }
        .variant-img-area { border:2px dashed var(--border); border-radius:10px; padding:14px; text-align:center; cursor:pointer; position:relative; transition:border-color .2s; margin-top:12px; }
        .variant-img-area:hover { border-color:var(--primary); }
        .variant-img-area input[type=file] { position:absolute; inset:0; opacity:0; cursor:pointer; width:100%; }
        .variant-img-preview { width:100%; height:90px; object-fit:contain; border-radius:6px; margin-top:8px; display:none; }
        .btn-add-variant { width:100%; padding:12px; border:2px dashed var(--primary); border-radius:10px; background:rgba(183,28,28,.04); color:var(--primary); font-family:inherit; font-size:14px; font-weight:700; cursor:pointer; transition:all .2s; margin-top:4px; }
        .btn-add-variant:hover { background:rgba(183,28,28,.1); }
    </style>
</head>
<body>
@include('admin.partials.sidebar')

<main class="main-content">
    <div class="header">
        <h1 class="page-title">{{ isset($product) ? 'Edit Produk' : 'Tambah Produk Baru' }}</h1>
        <p class="breadcrumb">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a> /
            <a href="{{ route('admin.produk.index') }}">Produk</a> /
            {{ isset($product) ? 'Edit' : 'Tambah' }}
        </p>
    </div>

    <form
        action="{{ isset($product) ? route('admin.produk.update', $product) : route('admin.produk.store') }}"
        method="POST"
        enctype="multipart/form-data"
    >
        @csrf
        @if(isset($product)) @method('PUT') @endif

        <div class="form-grid">
            <!-- LEFT: Info Utama -->
            <div>
                <div class="panel" style="margin-bottom:24px;">
                    <div class="panel-title"><i class="fa-solid fa-circle-info"></i> Informasi Produk</div>

                    <div class="form-group">
                        <label class="form-label">Nama Produk <span class="required">*</span></label>
                        <input type="text" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                            placeholder="Contoh: Pulpen Pilot G2" value="{{ old('name', $product->name ?? '') }}" required>
                        @error('name') <div class="field-error"><i class="fa-solid fa-triangle-exclamation"></i> {{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Kategori <span class="required">*</span></label>
                        <select name="category_id" class="form-control {{ $errors->has('category_id') ? 'is-invalid' : '' }}" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}"
                                    {{ old('category_id', $product->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id') <div class="field-error"><i class="fa-solid fa-triangle-exclamation"></i> {{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-control" placeholder="Deskripsi singkat produk...">{{ old('description', $product->description ?? '') }}</textarea>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                        <div class="form-group">
                            <label class="form-label">Harga (Rp) <span class="required">*</span></label>
                            <input type="number" name="price" class="form-control {{ $errors->has('price') ? 'is-invalid' : '' }}"
                                placeholder="25000" value="{{ old('price', $product->price ?? '') }}" min="0" step="100" required>
                            @error('price') <div class="field-error"><i class="fa-solid fa-triangle-exclamation"></i> {{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Stok <span class="required">*</span></label>
                            <input type="number" name="stock" class="form-control {{ $errors->has('stock') ? 'is-invalid' : '' }}"
                                placeholder="100" value="{{ old('stock', $product->stock ?? '') }}" min="0" required>
                            @error('stock') <div class="field-error"><i class="fa-solid fa-triangle-exclamation"></i> {{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <!-- Image Upload -->
                <div class="panel">
                    <div class="panel-title"><i class="fa-solid fa-image"></i> Gambar Produk</div>
                    <div class="image-upload-area" id="uploadArea">
                        <input type="file" name="image" accept="image/*" id="imageInput">
                        <i class="fa-solid fa-cloud-arrow-up" style="font-size:36px;color:var(--text-muted);margin-bottom:12px;"></i>
                        <p style="font-weight:600;color:var(--text-secondary);margin-bottom:4px;">Klik atau seret gambar ke sini</p>
                        <p style="font-size:13px;color:var(--text-muted);">JPG, PNG, GIF, WEBP · Maks. 3 MB</p>
                        @if(isset($product) && $product->image)
                            <img src="{{ Storage::url($product->image) }}" class="image-preview" id="imagePreview" style="display:block;">
                        @else
                            <img class="image-preview" id="imagePreview">
                        @endif
                    </div>
                    @error('image') <div class="field-error" style="margin-top:8px;"><i class="fa-solid fa-triangle-exclamation"></i> {{ $message }}</div> @enderror
                </div>

                <!-- Variant Section -->
                <div class="panel" style="margin-top:24px;">
                    <div class="panel-title"><i class="fa-solid fa-layer-group"></i> Varian Produk <span style="font-size:12px;color:var(--text-muted);font-weight:500;">(opsional)</span></div>

                    <div id="variantContainer">
                        @if(isset($product) && $product->variants->count())
                            @foreach($product->variants as $vi => $vr)
                            <div class="variant-card" id="variant-{{ $vi }}">
                                <div class="variant-card-header">
                                    <span class="variant-badge">Varian #{{ $vi + 1 }}</span>
                                    <button type="button" class="btn-remove-variant" onclick="removeVariant({{ $vi }})"><i class="fa-solid fa-trash"></i></button>
                                </div>
                                <div class="variant-grid">
                                    <div class="form-group" style="margin-bottom:0">
                                        <label class="form-label">Nama Varian</label>
                                        <input type="text" name="variants[{{ $vi }}][nama_varian]" class="form-control" placeholder="Hitam" value="{{ $vr->nama_varian }}" required>
                                    </div>
                                    <div class="form-group" style="margin-bottom:0">
                                        <label class="form-label">Harga (Rp)</label>
                                        <input type="number" name="variants[{{ $vi }}][harga]" class="form-control" placeholder="5000" value="{{ $vr->harga }}" min="0" step="100" required>
                                    </div>
                                    <div class="form-group" style="margin-bottom:0">
                                        <label class="form-label">Stok</label>
                                        <input type="number" name="variants[{{ $vi }}][stok]" class="form-control" placeholder="10" value="{{ $vr->stok }}" min="0" required>
                                    </div>
                                </div>
                                <div class="variant-img-area">
                                    <input type="file" name="variants[{{ $vi }}][image]" accept="image/*" onchange="previewVariantImg(this, {{ $vi }})">
                                    <i class="fa-solid fa-image" style="color:var(--text-muted);font-size:22px;"></i>
                                    <p style="font-size:12px;color:var(--text-muted);margin-top:6px;">Foto varian (opsional)</p>
                                    @if($vr->image)
                                        <img src="{{ Storage::url($vr->image) }}" class="variant-img-preview" id="vimg-{{ $vi }}" style="display:block;">
                                    @else
                                        <img class="variant-img-preview" id="vimg-{{ $vi }}">
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        @endif
                    </div>

                    <button type="button" class="btn-add-variant" onclick="addVariant()">
                        <i class="fa-solid fa-plus"></i> Tambah Varian
                    </button>
                </div>
            </div>

            <!-- RIGHT: Opsi -->
            <div>
                <div class="panel">
                    <div class="panel-title"><i class="fa-solid fa-sliders"></i> Label Produk</div>
                    <div class="toggle-group">
                        <label class="toggle-item">
                            <div>
                                <div class="toggle-label">🔥 Produk Populer</div>
                                <div class="toggle-sub">Tampil di bagian "Popular Products"</div>
                            </div>
                            <div class="toggle-switch">
                                <input type="hidden" name="is_popular" value="0">
                                <input type="checkbox" name="is_popular" value="1"
                                    {{ old('is_popular', $product->is_popular ?? false) ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </div>
                        </label>
                        <label class="toggle-item">
                            <div>
                                <div class="toggle-label">✨ Produk Baru</div>
                                <div class="toggle-sub">Tampil di bagian "New Arrivals"</div>
                            </div>
                            <div class="toggle-switch">
                                <input type="hidden" name="is_new_arrival" value="0">
                                <input type="checkbox" name="is_new_arrival" value="1"
                                    {{ old('is_new_arrival', $product->is_new_arrival ?? false) ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </div>
                        </label>
                    </div>

                    <div class="btn-group" style="margin-top:28px;">
                        <button type="submit" class="btn-primary" style="flex:1;justify-content:center;">
                            <i class="fa-solid fa-floppy-disk"></i>
                            {{ isset($product) ? 'Simpan Perubahan' : 'Simpan Produk' }}
                        </button>
                    </div>
                    <a href="{{ route('admin.produk.index') }}" class="btn-secondary" style="width:100%;justify-content:center;margin-top:10px;">
                        <i class="fa-solid fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </form>
</main>

<script>
    // Preview gambar utama produk
    document.getElementById('imageInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = ev => {
            const preview = document.getElementById('imagePreview');
            preview.src = ev.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    });

    // Handle checkbox toggle
    document.querySelectorAll('.toggle-switch input[type="checkbox"]').forEach(cb => {
        cb.addEventListener('change', function() { this.previousElementSibling.disabled = this.checked; });
        cb.previousElementSibling.disabled = cb.checked;
    });

    // ── Variant Management ──────────────────────────────
    let variantIndex = {{ isset($product) ? $product->variants->count() : 0 }};

    function addVariant() {
        const i = variantIndex++;
        const html = `
        <div class="variant-card" id="variant-${i}">
            <div class="variant-card-header">
                <span class="variant-badge">Varian #${i + 1}</span>
                <button type="button" class="btn-remove-variant" onclick="removeVariant(${i})"><i class="fa-solid fa-trash"></i></button>
            </div>
            <div class="variant-grid">
                <div class="form-group" style="margin-bottom:0">
                    <label class="form-label">Nama Varian</label>
                    <input type="text" name="variants[${i}][nama_varian]" class="form-control" placeholder="Contoh: Hitam" required>
                </div>
                <div class="form-group" style="margin-bottom:0">
                    <label class="form-label">Harga (Rp)</label>
                    <input type="number" name="variants[${i}][harga]" class="form-control" placeholder="5000" min="0" step="100" required>
                </div>
                <div class="form-group" style="margin-bottom:0">
                    <label class="form-label">Stok</label>
                    <input type="number" name="variants[${i}][stok]" class="form-control" placeholder="10" min="0" required>
                </div>
            </div>
            <div class="variant-img-area">
                <input type="file" name="variants[${i}][image]" accept="image/*" onchange="previewVariantImg(this, ${i})">
                <i class="fa-solid fa-image" style="color:var(--text-muted);font-size:22px;"></i>
                <p style="font-size:12px;color:var(--text-muted);margin-top:6px;">Foto varian (opsional)</p>
                <img class="variant-img-preview" id="vimg-${i}">
            </div>
        </div>`;
        document.getElementById('variantContainer').insertAdjacentHTML('beforeend', html);
    }

    function removeVariant(i) {
        const el = document.getElementById(`variant-${i}`);
        if (el) el.remove();
    }

    function previewVariantImg(input, i) {
        const file = input.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = ev => {
            const img = document.getElementById(`vimg-${i}`);
            img.src = ev.target.result;
            img.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
</script>
</body>
</html>
