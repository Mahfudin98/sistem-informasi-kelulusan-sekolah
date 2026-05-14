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

    <?php if ($warnaDasar = profil_sekolah('warna_dasar')): ?>
    <style>
        :root {
            --clr-primary: <?= $warnaDasar ?>;
            --clr-primary-hover: <?= $warnaDasar ?>e6;
        }
    </style>
    <?php endif; ?>
</head>
<body class="admin-body">

<!-- ── Sidebar ──────────────────────────────────────────────────────────── -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <?php if ($logo = profil_sekolah('logo')): ?>
            <img src="<?= url($logo) ?>" alt="Logo" style="height: 32px; width: auto; border-radius: 4px;">
        <?php else: ?>
            <span class="brand-icon">🎓</span>
        <?php endif; ?>
        <span class="sidebar-title"><?= e(profil_sekolah('nama_sekolah', env('APP_NAME'))) ?></span>
    </div>

    <nav class="sidebar-nav">
        <a href="/admin/dashboard"
           class="sidebar-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/dashboard') ? 'active' : '' ?>">
            <span class="sidebar-icon">📊</span> Dashboard
        </a>
        <a href="/admin/siswa"
           class="sidebar-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/siswa') ? 'active' : '' ?>">
            <span class="sidebar-icon">👥</span> Data Siswa
        </a>
        <a href="/admin/profil"
           class="sidebar-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/profil') ? 'active' : '' ?>">
            <span class="sidebar-icon">🏫</span> Profil Sekolah
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="user-info">
            <span class="user-avatar">👤</span>
            <div>
                <div class="user-name"><?= e(session('user')['name'] ?? 'Admin') ?></div>
                <div class="user-role"><?= e(session('user')['role'] ?? '') ?></div>
            </div>
        </div>
        <form method="POST" action="/logout" class="mt-2">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-logout btn-sm w-full">Logout</button>
        </form>
    </div>
</aside>

<!-- ── Main Wrapper ──────────────────────────────────────────────────────── -->
<div class="admin-wrapper">

    <!-- Topbar -->
    <header class="admin-topbar">
        <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">☰</button>
        <h1 class="page-title"><?= e($title ?? '') ?></h1>
    </header>

    <!-- Flash messages -->
    <?php if ($msg = flash('success')): ?>
        <div class="alert alert-success"><?= $msg ?></div>
    <?php endif; ?>
    <?php if ($msg = flash('error')): ?>
        <div class="alert alert-danger"><?= $msg ?></div>
    <?php endif; ?>

    <!-- Page content -->
    <main class="admin-content">
        <?= $content ?>
    </main>
</div>

<script src="<?= asset('js/app.js') ?>"></script>
<script src="<?= asset('js/admin.js') ?>"></script>
</body>
</html>
