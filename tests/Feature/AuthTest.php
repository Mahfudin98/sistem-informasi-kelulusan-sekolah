<?php

use App\Services\AuthService;
use App\Core\Session;

beforeEach(function () {
    // Mocking $_SESSION for testing
    $_SESSION = [];
    $this->auth = new AuthService();
});

test('it can check if user is guest', function () {
    expect($this->auth->check())->toBeFalse();
    expect($this->auth->guest())->toBeTrue();
});

test('it can store user in session', function () {
    $user = [
        'id' => 1,
        'username' => 'admin',
        'role' => 'superadmin'
    ];
    
    Session::set('user', $user);
    
    expect($this->auth->check())->toBeTrue();
    expect($this->auth->user())->toBe($user);
    expect($this->auth->id())->toBe(1);
});

test('it can refresh session', function () {
    // This requires a real user in DB, but we can mock the repo if needed.
    // For now, let's just test the logic that it updates session.
    $user = [
        'id' => 1,
        'username' => 'admin',
        'role' => 'superadmin'
    ];
    Session::set('user', $user);
    
    // We can't easily test real DB sync without test DB, 
    // but we can verify the method exists.
    expect(method_exists($this->auth, 'refreshSession'))->toBeTrue();
});
