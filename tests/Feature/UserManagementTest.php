<?php

use App\Services\UserService;
use App\Core\Session;

beforeEach(function () {
    $_SESSION = [];
    $this->service = new UserService();
});

test('admin cannot see superadmins', function () {
    // Mock data for repository would be better, 
    // but here we test the service filtering logic
    
    // We need to mock the repo if we want to be pure, 
    // but for now let's just test that the filtering logic in service works
    $result = $this->service->getAll('admin');
    
    foreach ($result as $user) {
        expect($user['role'])->not->toBe('superadmin');
    }
});

test('superadmin can see everyone', function () {
    $result = $this->service->getAll('superadmin');
    
    // Check if there is at least one superadmin (the logged in user likely is one)
    $hasSuper = false;
    foreach ($result as $user) {
        if ($user['role'] === 'superadmin') {
            $hasSuper = true;
            break;
        }
    }
    expect($hasSuper)->toBeTrue();
});

test('it validates user data for creation', function () {
    $result = $this->service->create([
        'username' => 'a', // too short
        'email' => 'not-an-email',
        'password' => '123' // too short
    ]);
    
    expect($result['success'])->toBeFalse();
    expect($result['errors'])->toHaveKeys(['username', 'email', 'password']);
});
