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
<body class="bg-bg text-text font-sans min-h-screen">

<!-- ── Navigation ───────────────────────────────────────────────────────── -->
<nav class="sticky top-0 z-50 backdrop-blur-xl bg-bg/80 border-b border-border py-4">
    <div class="container mx-auto px-6 flex items-center justify-between">
        <a href="/" class="flex items-center gap-3 text-xl font-bold text-text hover:text-primary transition-colors">
            <?php if ($logo = profil_sekolah('logo')): ?>
                <img src="<?= url($logo) ?>" alt="Logo" class="h-8 w-auto rounded">
            <?php else: ?>
                <span class="text-2xl">🎓</span>
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
<footer class="border-t border-border py-12 mt-16 text-center text-text-muted text-sm">
    <div class="container mx-auto px-6">
        <p>&copy; <?= date('Y') ?> <?= e(profil_sekolah('nama_sekolah', env('APP_NAME'))) ?>. All rights reserved.</p>
    </div>
</footer>

<script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
