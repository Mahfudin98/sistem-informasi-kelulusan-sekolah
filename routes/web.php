<?php

declare(strict_types=1);

/**
 * Web Routes
 *
 * All HTTP routes are registered here via the $router instance
 * injected by Application::run().
 */

use App\Core\Router;

/** @var Router $router */

$router->post('/api/license/sync', function() {
    \App\Services\LicenseService::sync();
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'License synced']);
});

$router->get('/license-error', function() {
    echo "<!DOCTYPE html>
          <html lang='id'>
          <head>
              <meta charset='UTF-8'>
              <meta name='viewport' content='width=device-width, initial-scale=1.0'>
              <title>Lisensi Tidak Valid</title>
          </head>
          <body style='background:#f1f5f9;font-family:sans-serif;display:flex;align-items:center;justify-content:center;height:100vh;margin:0;'>
            <div style='background:white;padding:40px;border-radius:24px;box-shadow:0 20px 25px -5px rgb(0 0 0 / 0.1);text-align:center;max-width:400px;'>
                <h1 style='font-size:48px;margin:0;'>🔒</h1>
                <h2 style='color:#1e293b;margin:20px 0 10px;'>Lisensi Tidak Valid</h2>
                <p style='color:#64748b;font-size:14px;line-height:1.6;'>Lisensi untuk domain ini tidak ditemukan atau telah dibekukan. Silakan hubungi pengembang.</p>
                <div style='margin-top:25px;padding:15px;background:#f8fafc;border-radius:12px;font-family:monospace;font-size:12px;color:#94a3b8;'>
                    Domain Target: " . ($_SERVER['HTTP_HOST'] ?? 'unknown') . "
                </div>
                <a href='/setup' style='display:inline-block;margin-top:20px;color:#3b82f6;text-decoration:none;font-size:14px;font-weight:600;'>Masukkan Kunci Baru &rarr;</a>
            </div>
          </body>
          </html>";
});

$router->get('/setup', function() {
    if (\App\Services\LicenseService::checkStatus() === 'valid') {
        header('Location: /');
        exit;
    }

    $domain = $_SERVER['HTTP_HOST'] ?? 'unknown';
    
    echo "<!DOCTYPE html>
          <html lang='id'>
          <head>
              <meta charset='UTF-8'>
              <meta name='viewport' content='width=device-width, initial-scale=1.0'>
              <title>Setup Aplikasi</title>
          </head>
          <body style='background:#f1f5f9;font-family:sans-serif;display:flex;align-items:center;justify-content:center;height:100vh;margin:0;'>
            <div style='background:white;padding:40px;border-radius:24px;box-shadow:0 20px 25px -5px rgb(0 0 0 / 0.1);max-width:450px;width:100%;'>
                <div style='text-align:center;'>
                    <h1 style='font-size:40px;margin:0;'>🚀</h1>
                    <h2 style='color:#1e293b;margin:15px 0 5px;'>Aktivasi Aplikasi</h2>
                    <p style='color:#64748b;font-size:14px;line-height:1.5;margin-bottom:25px;'>Masukkan email yang Anda gunakan saat pembelian untuk mengaktifkan aplikasi di domain ini.</p>
                </div>
                
                <form method='POST' action='/setup'>
                    <div style='margin-bottom:15px;'>
                        <label style='display:block;font-size:13px;font-weight:600;color:#475569;margin-bottom:5px;'>Domain Anda (Otomatis)</label>
                        <input type='text' disabled value='{$domain}' style='width:100%;padding:12px;border:1px solid #cbd5e1;border-radius:8px;background:#f8fafc;color:#64748b;box-sizing:border-box;'>
                    </div>
                    <div style='margin-bottom:25px;'>
                        <label style='display:block;font-size:13px;font-weight:600;color:#475569;margin-bottom:5px;'>Email Pembelian</label>
                        <input type='email' name='email' required placeholder='nama@email.com' style='width:100%;padding:12px;border:1px solid #cbd5e1;border-radius:8px;font-size:14px;box-sizing:border-box;'>
                    </div>
                    <button type='submit' style='width:100%;background:#3b82f6;color:white;border:none;padding:14px;border-radius:8px;font-weight:600;cursor:pointer;font-size:15px;transition:0.2s;'>Aktifkan Lisensi</button>
                </form>
            </div>
          </body>
          </html>";
});

$router->post('/setup', function() {
    $request = new \App\Core\Request();
    $email = $request->input('email');
    
    if (empty($email)) {
        header('Location: /setup?error=empty');
        exit;
    }

    $result = \App\Services\LicenseService::activate($email);

    if ($result['success']) {
        header('Location: /');
        exit;
    } else {
        $msg = addslashes($result['message']);
        echo "<script>alert('{$msg}'); window.location.href='/setup';</script>";
        exit;
    }
});

// ============================================================
// PUBLIC ROUTES
// ============================================================

$router->get('/', 'HomeController@index', name: 'home');
$router->post('/cek', 'HomeController@cek', ['CsrfMiddleware'], 'home.cek');
$router->get('/cetak/:nisn', 'HomeController@cetak', name: 'home.cetak');
$router->get('/download/:nisn', 'HomeController@download', name: 'home.download');
$router->get('/verifikasi/:nisn', 'HomeController@verifikasi', name: 'home.verifikasi');

// ============================================================
// AUTH ROUTES  (guests only)
// ============================================================

$router->group('/login', function (Router $r) {
    $r->get('',  'AuthController@loginForm', ['GuestMiddleware'], 'login');
    $r->post('', 'AuthController@login',     ['GuestMiddleware', 'CsrfMiddleware']);
});

// Password Reset
$router->get('/forgot-password',        'Auth\ForgotPasswordController@showLinkRequestForm', ['GuestMiddleware'], 'password.request');
$router->post('/forgot-password',       'Auth\ForgotPasswordController@sendResetLinkEmail', ['GuestMiddleware', 'CsrfMiddleware']);
$router->get('/reset-password/:token',  'Auth\ResetPasswordController@showResetForm', ['GuestMiddleware'], 'password.reset');
$router->post('/reset-password',        'Auth\ResetPasswordController@reset', ['GuestMiddleware', 'CsrfMiddleware'], 'password.update');

$router->post('/logout', 'AuthController@logout', ['AuthMiddleware', 'CsrfMiddleware'], 'logout');

// ============================================================
// ADMIN ROUTES  (requires auth)
// ============================================================

$router->group('/admin', function (Router $r) {

    // Dashboard
    $r->get('/dashboard', 'Admin\DashboardController@index', name: 'admin.dashboard');

    // Profil Sekolah
    $r->get('/profil', 'Admin\ProfilSekolahController@edit', name: 'admin.profil.edit');
    $r->post('/profil', 'Admin\ProfilSekolahController@update', ['CsrfMiddleware'], 'admin.profil.update');

    // Siswa resource
    $r->get('/siswa',                  'Admin\SiswaController@index',   name: 'admin.siswa.index');
    $r->get('/siswa/create',           'Admin\SiswaController@create',  name: 'admin.siswa.create');
    $r->get('/siswa/import',           'Admin\SiswaController@import',  name: 'admin.siswa.import');
    $r->post('/siswa/import',          'Admin\SiswaController@processImport', ['CsrfMiddleware'], 'admin.siswa.process_import');
    $r->get('/siswa/template',         'Admin\SiswaController@downloadTemplate', name: 'admin.siswa.template');
    $r->get('/siswa/export',           'Admin\SiswaController@export',  name: 'admin.siswa.export');
    
    $r->post('/siswa',                 'Admin\SiswaController@store',   ['CsrfMiddleware'], 'admin.siswa.store');
    $r->get('/siswa/:id',              'Admin\SiswaController@show',    name: 'admin.siswa.show');
    $r->get('/siswa/:id/edit',         'Admin\SiswaController@edit',    name: 'admin.siswa.edit');
    $r->post('/siswa/:id/update',      'Admin\SiswaController@update',  ['CsrfMiddleware'], 'admin.siswa.update');
    $r->post('/siswa/:id/delete',      'Admin\SiswaController@destroy', ['CsrfMiddleware'], 'admin.siswa.destroy');
    $r->post('/siswa/bulk-update',     'Admin\SiswaController@bulkUpdate', ['CsrfMiddleware'], 'admin.siswa.bulk_update');
    $r->post('/siswa/bulk-delete',     'Admin\SiswaController@bulkDelete', ['CsrfMiddleware'], 'admin.siswa.bulk_delete');

    // Admin Management
    $r->get('/users',                  'Admin\UserController@index',    name: 'admin.user.index');
    $r->get('/users/create',           'Admin\UserController@create',   name: 'admin.user.create');
    $r->post('/users',                 'Admin\UserController@store',    ['CsrfMiddleware'], 'admin.user.store');
    $r->get('/users/:id/edit',         'Admin\UserController@edit',     name: 'admin.user.edit');
    $r->post('/users/:id/update',      'Admin\UserController@update',   ['CsrfMiddleware'], 'admin.user.update');
    $r->post('/users/:id/delete',      'Admin\UserController@destroy',  ['CsrfMiddleware'], 'admin.user.destroy');

    // Profile
    $r->get('/my/profile',                'Admin\ProfileController@edit',  name: 'admin.profile.edit');
    $r->post('/my/profile',               'Admin\ProfileController@update', ['CsrfMiddleware'], 'admin.profile.update');

    // Audit Logs
    $r->get('/audit-logs',                'Admin\AuditLogController@index', name: 'admin.audit_logs.index');

    // Backup
    $r->get('/backup',                    'Admin\BackupController@index',    name: 'admin.backup.index');
    $r->post('/backup/download',          'Admin\BackupController@download', ['CsrfMiddleware'], 'admin.backup.download');

}, ['AuthMiddleware']);
