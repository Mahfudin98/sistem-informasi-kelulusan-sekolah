<?php

use App\Services\LicenseService;

const TEMP_LICENSE_FILE = ROOT_PATH . '/license.key';

beforeEach(function () {
    if (file_exists(TEMP_LICENSE_FILE)) {
        @unlink(TEMP_LICENSE_FILE);
    }
});

afterEach(function () {
    if (file_exists(TEMP_LICENSE_FILE)) {
        @unlink(TEMP_LICENSE_FILE);
    }
});

test('checkStatus returns missing if file does not exist', function () {
    expect(LicenseService::checkStatus())->toBe('missing');
});

test('checkStatus returns invalid if key hash does not match domain', function () {
    $_SERVER['HTTP_HOST'] = 'my-app.test';
    file_put_contents(TEMP_LICENSE_FILE, 'invalid-key-hash');
    
    expect(LicenseService::checkStatus())->toBe('invalid');
});

test('checkStatus returns valid if key hash matches domain', function () {
    $_SERVER['HTTP_HOST'] = 'my-app.test';
    $domain = 'my-app.test';
    $salt = 'AppKelulusan_Secure_Salt_2024';
    $expectedKey = hash('sha256', $domain . $salt);
    
    file_put_contents(TEMP_LICENSE_FILE, $expectedKey);
    
    expect(LicenseService::checkStatus())->toBe('valid');
});
