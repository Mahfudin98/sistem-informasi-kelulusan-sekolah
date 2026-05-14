<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Login — ' . env('APP_NAME')) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body class="bg-bg text-text min-h-screen flex items-center justify-center p-6 bg-[radial-gradient(ellipse_60%_50%_at_50%_-20%,rgba(99,102,241,0.25),transparent)] font-sans">
    <?= $content ?>
    <script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
