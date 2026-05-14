<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Services\AuthService;
use App\Validation\Validator;

/**
 * AuthController
 *
 * Handles admin login and logout.
 */
final class AuthController extends BaseController
{
    public function __construct(
        private readonly AuthService $authService = new AuthService(),
    ) {}

    /**
     * GET /login
     */
    public function loginForm(Request $request): void
    {
        $this->view('auth.login', [
            'title' => 'Login Admin — ' . env('APP_NAME'),
        ], 'layouts/auth');
    }

    /**
     * POST /login
     */
    public function login(Request $request): void
    {
        $validator = Validator::make($request->all(), [
            'identifier' => 'required',
            'password'   => 'required',
        ]);

        if ($validator->fails()) {
            $this->withErrors($validator->errors())
                 ->withInput($request->all())
                 ->back();
        }

        $result = $this->authService->attempt(
            $request->input('identifier', ''),
            $request->input('password', ''),
        );

        if (!$result['success']) {
            $this->withError($result['message'])
                 ->withInput($request->all())
                 ->back();
        }

        $this->withSuccess('Selamat datang kembali!')
             ->redirect('/admin/dashboard');
    }

    /**
     * POST /logout
     */
    public function logout(Request $request): void
    {
        $this->authService->logout();
        $this->withSuccess('Anda telah logout.')
             ->redirect('/login');
    }
}
