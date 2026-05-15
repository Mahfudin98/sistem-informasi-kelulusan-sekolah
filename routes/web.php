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

// ============================================================
// PUBLIC ROUTES
// ============================================================

$router->get('/', 'HomeController@index', name: 'home');
$router->post('/cek', 'HomeController@cek', ['CsrfMiddleware'], 'home.cek');
$router->get('/cetak/:nisn', 'HomeController@cetak', name: 'home.cetak');

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
    
    $r->post('/siswa',                 'Admin\SiswaController@store',   ['CsrfMiddleware'], 'admin.siswa.store');
    $r->get('/siswa/:id',              'Admin\SiswaController@show',    name: 'admin.siswa.show');
    $r->get('/siswa/:id/edit',         'Admin\SiswaController@edit',    name: 'admin.siswa.edit');
    $r->post('/siswa/:id/update',      'Admin\SiswaController@update',  ['CsrfMiddleware'], 'admin.siswa.update');
    $r->post('/siswa/:id/delete',      'Admin\SiswaController@destroy', ['CsrfMiddleware'], 'admin.siswa.destroy');
    $r->post('/siswa/bulk-update',     'Admin\SiswaController@bulkUpdate', ['CsrfMiddleware'], 'admin.siswa.bulk_update');

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

}, ['AuthMiddleware']);
