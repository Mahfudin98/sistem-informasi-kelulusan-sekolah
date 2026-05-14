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

}, ['AuthMiddleware']);
