<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Admin — ' . profil_sekolah('nama_sekolah', env('APP_NAME'))) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">

    <?php 
    $warnaDasar = profil_sekolah('warna_dasar', '#6366f1');
    $warnaTeks  = get_contrast_color($warnaDasar);
    ?>
    <style>
        :root {
            --color-primary: <?= $warnaDasar ?>;
            --color-primary-dk: <?= $warnaDasar ?>e6;
            --color-primary-text: <?= $warnaTeks ?>;
        }
    </style>
</head>
<body class="flex overflow-hidden bg-bg text-text min-h-screen font-sans">

<!-- ── Sidebar ──────────────────────────────────────────────────────────── -->
<aside class="w-[260px] bg-surface border-r border-border flex flex-col h-screen shrink-0 z-50 fixed lg:static top-0 left-0 sidebar" id="sidebar">
    <div class="h-16 px-6 flex items-center justify-between border-b border-border">
        <div class="flex items-center gap-3">
            <?php if ($logo = profil_sekolah('logo')): ?>
                <img src="/<?= ltrim($logo, '/') ?>" alt="Logo" class="h-8 w-auto rounded">
            <?php else: ?>
                <span class="text-2xl">🎓</span>
            <?php endif; ?>
            <span class="font-extrabold text-sm uppercase tracking-tight"><?= e(profil_sekolah('nama_sekolah', env('APP_NAME'))) ?></span>
        </div>
        <button class="lg:hidden text-2xl text-text-muted hover:text-text cursor-pointer" id="sidebarClose" aria-label="Close sidebar">✕</button>
    </div>

    <nav class="flex-1 p-4 flex flex-col gap-1 overflow-y-auto">
        <a href="/admin/dashboard"
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/dashboard') ? 'bg-primary/10 text-primary font-bold border-l-4 border-primary' : 'text-text-muted hover:bg-white/5' ?>">
            <span class="text-xl">📊</span> Dashboard
        </a>
        <a href="/admin/siswa"
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/siswa') ? 'bg-primary/10 text-primary font-bold border-l-4 border-primary' : 'text-text-muted hover:bg-white/5' ?>">
            <span class="text-xl">👥</span> Data Siswa
        </a>
        <a href="/admin/profil"
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/admin/profil') ? 'bg-primary/10 text-primary font-bold border-l-4 border-primary' : 'text-text-muted hover:bg-white/5' ?>">
            <span class="text-xl">🏫</span> Profil Sekolah
        </a>
        <a href="/admin/users"
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/admin/users') ? 'bg-primary/10 text-primary font-bold border-l-4 border-primary' : 'text-text-muted hover:bg-white/5' ?>">
            <span class="text-xl">🔐</span> Manajemen Admin
        </a>
        <a href="/admin/my/profile"
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/admin/my/profile') ? 'bg-primary/10 text-primary font-bold border-l-4 border-primary' : 'text-text-muted hover:bg-white/5' ?>">
            <span class="text-xl">👤</span> Profil Saya
        </a>
        <?php if ((session('user')['role'] ?? '') === 'superadmin'): ?>
        <a href="/admin/audit-logs"
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/admin/audit-logs') ? 'bg-primary/10 text-primary font-bold border-l-4 border-primary' : 'text-text-muted hover:bg-white/5' ?>">
            <span class="text-xl">📜</span> Log Audit
        </a>
        <a href="/admin/backup"
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/admin/backup') ? 'bg-primary/10 text-primary font-bold border-l-4 border-primary' : 'text-text-muted hover:bg-white/5' ?>">
            <span class="text-xl">💾</span> Backup Database
        </a>
        <?php endif; ?>
    </nav>

    <div class="p-6 border-t border-border bg-white/2">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-primary/20 rounded-full flex items-center justify-center text-xl">👤</div>
            <div>
                <div class="font-bold text-sm"><?= e(session('user')['name'] ?? 'Admin') ?></div>
                <div class="text-[0.7rem] text-text-muted uppercase tracking-wider"><?= e(session('user')['role'] ?? '') ?></div>
            </div>
        </div>
        <form method="POST" action="/logout">
            <?= csrf_field() ?>
            <button type="submit" class="w-full bg-red-500/10 text-red-400 border border-red-500/20 py-2 rounded-lg text-xs font-bold hover:bg-red-500/20 transition-all cursor-pointer">Logout</button>
        </form>
    </div>
</aside>

<!-- ── Main Wrapper ──────────────────────────────────────────────────────── -->
<div class="flex-1 flex flex-col h-screen min-w-0">

    <!-- Topbar -->
    <header class="h-16 bg-surface border-b border-border flex items-center px-6 gap-4 z-40 shrink-0">
        <button class="lg:hidden text-2xl text-text-muted hover:text-text cursor-pointer" id="sidebarToggle" aria-label="Toggle sidebar">☰</button>
        <h1 class="text-lg font-bold tracking-tight"><?= e($title ?? '') ?></h1>
    </header>

    <!-- Flash messages -->
    <div class="px-6 pt-4">
        <?php if ($msg = flash('success')): ?>
            <div class="bg-green-500/10 border border-green-500/30 text-green-300 p-4 rounded-xl text-sm"><?= $msg ?></div>
        <?php endif; ?>
        <?php if ($msg = flash('error')): ?>
            <div class="bg-red-500/10 border border-red-500/30 text-red-300 p-4 rounded-xl text-sm"><?= $msg ?></div>
        <?php endif; ?>
    </div>

    <!-- Page content -->
    <main class="flex-1 p-6 overflow-y-auto">
        <?= $content ?>
    </main>
</div>

<!-- Sidebar Overlay (Mobile) -->
<div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-[45] hidden opacity-0 transition-opacity duration-300"></div>

<script src="<?= asset('js/app.js') ?>" defer></script>
<script src="<?= asset('js/admin.js') ?>" defer></script>

<script>
    // Failsafe Sidebar Toggle
    document.addEventListener('DOMContentLoaded', function() {
        const btn = document.getElementById('sidebarToggle');
        const closeBtn = document.getElementById('sidebarClose');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        function toggle(show) {
            if (!sidebar) return;
            if (show) {
                sidebar.classList.add('open');
                overlay?.classList.remove('hidden');
                setTimeout(() => overlay?.classList.add('opacity-100'), 10);
            } else {
                sidebar.classList.remove('open');
                overlay?.classList.remove('opacity-100');
                setTimeout(() => overlay?.classList.add('hidden'), 300);
            }
        }

        btn?.addEventListener('click', () => toggle(true));
        closeBtn?.addEventListener('click', () => toggle(false));
        overlay?.addEventListener('click', () => toggle(false));
    });
</script>
</body>
</html>
