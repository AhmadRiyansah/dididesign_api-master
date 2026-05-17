{{-- Shared admin styles --}}
:root {
    --primary:#B71C1C; --primary-light:#D32F2F;
    --bg-main:#F4F6F8; --bg-panel:#FFFFFF;
    --border:rgba(0,0,0,0.08); --text-primary:#1F2937;
    --text-secondary:#4B5563; --text-muted:#9CA3AF;
    --danger:#EF4444; --success:#10B981; --warning:#F5B942;
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
.header { display:flex; justify-content:space-between; align-items:center; margin-bottom:32px; }
.page-title { font-size:28px; font-weight:800; }
.breadcrumb { font-size:14px; color:var(--text-muted); margin-top:4px; }
.breadcrumb a { color:var(--primary); text-decoration:none; }
.alert-success { background:rgba(16,185,129,.08); border:1px solid rgba(16,185,129,.2); color:#065F46; padding:14px 20px; border-radius:12px; margin-bottom:24px; font-weight:600; font-size:14px; display:flex; align-items:center; gap:10px; }
.panel { background:white; border:1px solid var(--border); border-radius:20px; box-shadow:0 4px 20px rgba(0,0,0,.02); overflow:hidden; }
.panel-header { padding:24px 28px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; }
.panel-title { font-size:18px; font-weight:700; display:flex; align-items:center; gap:10px; }
.panel-title i { color:var(--primary); }
.data-table { width:100%; border-collapse:collapse; }
.data-table th { text-align:left; padding:14px 20px; color:var(--text-muted); font-size:11px; text-transform:uppercase; letter-spacing:1px; border-bottom:1px solid var(--border); font-weight:700; background:var(--bg-main); }
.data-table td { padding:16px 20px; border-bottom:1px solid var(--border); font-size:14px; color:var(--text-secondary); vertical-align:middle; }
.data-table tbody tr { transition:background .15s; }
.data-table tbody tr:hover { background:rgba(183,28,28,.02); }
.badge { padding:6px 14px; border-radius:20px; font-size:12px; font-weight:700; display:inline-flex; align-items:center; gap:6px; }
.badge-success { background:rgba(16,185,129,.1); color:#065F46; }
.badge-warning { background:rgba(245,185,66,.15); color:#92400E; }
.badge-danger { background:rgba(239,68,68,.1); color:#991B1B; }
.badge-info { background:rgba(59,130,246,.1); color:#1E40AF; }
.badge-muted { background:rgba(156,163,175,.15); color:#4B5563; }
.stats-row { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:20px; margin-bottom:32px; }
.stat-card { background:white; border:1px solid var(--border); border-radius:16px; padding:20px 24px; display:flex; align-items:center; gap:16px; box-shadow:0 4px 16px rgba(0,0,0,.02); }
.stat-icon { width:48px; height:48px; border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:20px; }
.stat-value { font-size:28px; font-weight:800; }
.stat-label { font-size:13px; color:var(--text-muted); font-weight:600; }
.btn-sm { border:none; padding:6px 14px; border-radius:8px; font-family:inherit; font-size:12px; font-weight:700; cursor:pointer; transition:all .2s; }
.btn-primary-sm { background:var(--primary); color:white; }
.btn-primary-sm:hover { background:var(--primary-light); }
.empty-state { text-align:center; padding:60px 20px; color:var(--text-muted); }
.empty-state i { font-size:48px; margin-bottom:16px; opacity:.4; display:block; }
.empty-state p { font-size:15px; font-weight:600; }
.filter-bar { display:flex; gap:8px; }
.filter-btn { padding:8px 16px; border-radius:20px; border:1px solid var(--border); background:white; font-family:inherit; font-size:13px; font-weight:600; color:var(--text-secondary); cursor:pointer; text-decoration:none; transition:all .2s; }
.filter-btn:hover, .filter-btn.active { background:var(--primary); color:white; border-color:var(--primary); }
.btn-delete { background:none; border:1px solid var(--border); color:var(--text-muted); padding:6px 10px; border-radius:8px; cursor:pointer; font-size:13px; transition:all .2s; }
.btn-delete:hover { color:var(--danger); border-color:var(--danger); }
