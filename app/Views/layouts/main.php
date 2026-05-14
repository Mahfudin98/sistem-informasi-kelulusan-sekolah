<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem Pengecekan Kelulusan Online">
    <title><?= e($title ?? profil_sekolah('nama_sekolah', env('APP_NAME'))) ?></title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Main CSS -->
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    
    <?php if ($warnaDasar = profil_sekolah('warna_dasar')): ?>
    <style>
        :root {
            --clr-primary: <?= $warnaDasar ?>;
            --clr-primary-hover: <?= $warnaDasar ?>e6; /* slight transparency */
        }
    </style>
    <?php endif; ?>
</head>
<body>

<!-- ── Navigation ───────────────────────────────────────────────────────── -->
<nav class="navbar">
    <div class="container">
        <a href="/" class="navbar-brand">
            <?php if ($logo = profil_sekolah('logo')): ?>
                <img src="<?= url($logo) ?>" alt="Logo" style="height: 32px; width: auto; border-radius: 4px;">
            <?php else: ?>
                <span class="brand-icon">🎓</span>
            <?php endif; ?>
            <span><?= e(profil_sekolah('nama_sekolah', env('APP_NAME'))) ?></span>
        </a>
    </div>
</nav>

<!-- ── Main Content ──────────────────────────────────────────────────────── -->
<main class="main-content">
    <?= $content ?>
</main>

<!-- ── Footer ───────────────────────────────────────────────────────────── -->
<footer class="footer">
    <div class="container">
        <p>&copy; <?= date('Y') ?> <?= e(profil_sekolah('nama_sekolah', env('APP_NAME'))) ?>. All rights reserved.</p>
    </div>
</footer>

<script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
