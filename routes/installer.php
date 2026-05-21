<?php

/**
 * INSTALLER UI
 * Handles the initial setup when .env is missing.
 */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appName = $_POST['app_name'] ?? 'App Kelulusan';
    $appUrl = $_POST['app_url'] ?? ('http://' . $_SERVER['HTTP_HOST']);
    $dbHost = $_POST['db_host'] ?? '127.0.0.1';
    $dbPort = $_POST['db_port'] ?? '3306';
    $dbName = $_POST['db_name'] ?? 'app_kelulusan';
    $dbUser = $_POST['db_user'] ?? 'root';
    $dbPass = $_POST['db_pass'] ?? '';
    $emailPembelian = trim($_POST['email_pembelian'] ?? '');

    $errors = [];
    $licenseKey = '';

    // 1. Activate License via Email
    $domain = $_SERVER['HTTP_HOST'];
    $ch = curl_init('http://localhost:4000/api/activate'); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['email' => $emailPembelian, 'domain' => $domain]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode >= 500 || $response === false) {
        $errors[] = "Tidak dapat menghubungi server lisensi.";
    } else {
        $data = json_decode($response, true);
        if (empty($data['success']) || $data['success'] !== true) {
            $errors[] = "Aktivasi Gagal: " . ($data['message'] ?? 'Email tidak valid.');
        } else {
            $licenseKey = $data['key'];
        }
    }

    // 2. Test DB Connection
    if (empty($errors)) {
        try {
            $pdo = new PDO("mysql:host=$dbHost;port=$dbPort", $dbUser, $dbPass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            $errors[] = "Koneksi Database Gagal: " . $e->getMessage();
        }
    }

    if (empty($errors)) {
        // Create .env
        $envContent = <<<ENV
APP_NAME="$appName"
APP_ENV=production
APP_DEBUG=false
APP_URL="$appUrl"
APP_TIMEZONE="Asia/Jakarta"

DB_HOST="$dbHost"
DB_PORT="$dbPort"
DB_NAME="$dbName"
DB_USER="$dbUser"
DB_PASS="$dbPass"
ENV;
        file_put_contents(ROOT_PATH . '/.env', $envContent);

        // Run migrations
        ob_start();
        require_once ROOT_PATH . '/database/setup_db.php';
        $migrations = glob(ROOT_PATH . '/database/migrate_*.php');
        foreach ($migrations as $migration) {
            require_once $migration;
        }
        ob_end_clean();

        // Save license key
        file_put_contents(ROOT_PATH . '/license.key', $licenseKey);

        header('Location: /');
        exit;
    }
}

$domain = $_SERVER['HTTP_HOST'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalasi App Kelulusan</title>
    <style>
        body { background: #f1f5f9; font-family: sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; padding: 20px; box-sizing: border-box; }
        .container { background: white; padding: 40px; border-radius: 24px; box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1); max-width: 600px; width: 100%; }
        h2 { color: #1e293b; margin: 10px 0 5px; text-align: center; font-size: 28px; }
        p.subtitle { color: #64748b; font-size: 14px; text-align: center; margin-bottom: 30px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 5px; }
        input, textarea { width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-family: sans-serif; font-size: 14px; box-sizing: border-box; }
        input:focus, textarea:focus { border-color: #3b82f6; outline: none; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }
        .row { display: flex; gap: 15px; }
        .row > div { flex: 1; }
        button { width: 100%; background: #3b82f6; color: white; border: none; padding: 15px; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 16px; margin-top: 15px; transition: 0.2s; }
        button:hover { background: #2563eb; }
        .error { background: #fef2f2; border: 1px solid #f87171; color: #b91c1c; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; }
        .error ul { margin: 5px 0 0; padding-left: 20px; }
        .section-title { font-size: 16px; font-weight: bold; color: #0f172a; margin: 25px 0 15px; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>✨ Setup Instalasi</h2>
        <p class="subtitle">Lengkapi formulir di bawah ini untuk menginstal aplikasi.</p>

        <?php if (!empty($errors)): ?>
            <div class="error">
                <strong>Gagal:</strong>
                <ul>
                    <?php foreach ($errors as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="section-title">Konfigurasi Aplikasi</div>
            <div class="row">
                <div class="form-group">
                    <label>Nama Aplikasi</label>
                    <input type="text" name="app_name" value="<?= htmlspecialchars($_POST['app_name'] ?? 'App Kelulusan') ?>" required>
                </div>
                <div class="form-group">
                    <label>URL Aplikasi</label>
                    <input type="url" name="app_url" value="<?= htmlspecialchars($_POST['app_url'] ?? 'http://' . $domain) ?>" required>
                </div>
            </div>

            <div class="section-title">Konfigurasi Database</div>
            <div class="row">
                <div class="form-group">
                    <label>Host Database</label>
                    <input type="text" name="db_host" value="<?= htmlspecialchars($_POST['db_host'] ?? '127.0.0.1') ?>" required>
                </div>
                <div class="form-group">
                    <label>Port Database</label>
                    <input type="text" name="db_port" value="<?= htmlspecialchars($_POST['db_port'] ?? '3306') ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label>Nama Database</label>
                <input type="text" name="db_name" value="<?= htmlspecialchars($_POST['db_name'] ?? 'app_kelulusan') ?>" required>
            </div>
            <div class="row">
                <div class="form-group">
                    <label>Username Database</label>
                    <input type="text" name="db_user" value="<?= htmlspecialchars($_POST['db_user'] ?? 'root') ?>" required>
                </div>
                <div class="form-group">
                    <label>Password Database</label>
                    <input type="password" name="db_pass" value="<?= htmlspecialchars($_POST['db_pass'] ?? '') ?>">
                </div>
            </div>

            <div class="section-title">Validasi Lisensi</div>
            <div class="form-group">
                <label>Domain Saat Ini</label>
                <input type="text" value="<?= $domain ?>" disabled style="background: #f8fafc;">
            </div>
            <div class="form-group">
                <label>Email Pembelian</label>
                <input type="email" name="email_pembelian" placeholder="Masukkan email yang digunakan saat membeli..." required value="<?= htmlspecialchars($_POST['email_pembelian'] ?? '') ?>">
            </div>

            <button type="submit">Install Aplikasi Sekarang</button>
        </form>
    </div>
</body>
</html>
