<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Service to handle application licensing
 */
final class LicenseService
{
    private const LICENSE_FILE = ROOT_PATH . '/license.key';
    private const SECRET_SALT = 'AppKelulusan_Secure_Salt_2024';
    private const LICENSE_SERVER_URL = 'http://localhost:4000/api/verify';
    private const CACHE_FILE = ROOT_PATH . '/storage/license_cache.json';
    
    // How often to check with server (e.g. 7 days)
    private const CHECK_INTERVAL = 604800; 

    /**
     * Check the license status locally and occasionally against the server.
     * Returns: 'missing', 'invalid', or 'valid'
     */
    public static function checkStatus(): string
    {
        if (!file_exists(self::LICENSE_FILE)) {
            return 'missing';
        }

        $key = trim(file_get_contents(self::LICENSE_FILE));
        if (empty($key)) {
            return 'missing';
        }

        $domain = $_SERVER['HTTP_HOST'] ?? '';
        $expectedKey = hash('sha256', $domain . self::SECRET_SALT);

        if (!hash_equals($expectedKey, $key)) {
            return 'invalid';
        }

        return 'valid';
    }

    /**
     * Forcefully sync with the license server (Triggered by Webhook).
     */
    public static function sync(): bool
    {
        if (!file_exists(self::LICENSE_FILE)) return false;
        
        $key = trim(file_get_contents(self::LICENSE_FILE));
        $domain = $_SERVER['HTTP_HOST'] ?? '';
        
        return self::verifyRemote($key, $domain);
    }

    /**
     * Activate license via email.
     * Hits /api/activate on the license server.
     */
    public static function activate(string $email): array
    {
        $domain = $_SERVER['HTTP_HOST'] ?? '';
        
        $ch = curl_init('http://localhost:4000/api/activate');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Add this for compatibility
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'email' => $email,
            'domain' => $domain
        ]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false) {
            return ['success' => false, 'message' => 'Tidak dapat terhubung ke server lisensi.'];
        }

        $data = json_decode($response, true);
        
        if ($httpCode === 200 && isset($data['success']) && $data['success'] === true) {
            self::saveKey($data['key']);
            return ['success' => true];
        }

        return [
            'success' => false, 
            'message' => $data['message'] ?? 'Gagal mengaktifkan lisensi.'
        ];
    }

    /**
     * Set the license key programmatically from the setup UI.
     */
    public static function saveKey(string $key): void
    {
        file_put_contents(self::LICENSE_FILE, trim($key));
        // Force sync immediately after saving new key
        self::sync();
    }

    /**
     * Determine if we need to ping the server based on the last success.
     */
    private static function shouldCheckServer(): bool
    {
        if (!file_exists(self::CACHE_FILE)) {
            return true;
        }

        $cache = json_decode(file_get_contents(self::CACHE_FILE), true);
        $lastSuccess = $cache['last_success'] ?? 0;

        return (time() - $lastSuccess) > self::CHECK_INTERVAL;
    }

    /**
     * Verify the license key against the remote server.
     */
    private static function verifyRemote(string $key, string $domain): bool
    {
        $ch = curl_init(self::LICENSE_SERVER_URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10); 
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['key' => $key, 'domain' => $domain]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // If server is unreachable (down/timeout), we trust the local cache for now
        if ($response === false || $httpCode >= 500) {
            return file_exists(self::CACHE_FILE); // Stay valid if we have any previous success
        }

        $data = json_decode($response, true);
        
        if (isset($data['valid']) && $data['valid'] === true) {
            self::updateCache();
            return true;
        }

        // If explicitly invalid (suspended/expired), kill the cache and delete the license key file
        if (file_exists(self::CACHE_FILE)) {
            @unlink(self::CACHE_FILE); 
        }
        if (file_exists(self::LICENSE_FILE)) {
            @unlink(self::LICENSE_FILE);
        }
        return false;
    }

    /**
     * Update the local cache file.
     */
    private static function updateCache(): void
    {
        $dir = dirname(self::CACHE_FILE);
        if (!is_dir($dir)) mkdir($dir, 0777, true);
        file_put_contents(self::CACHE_FILE, json_encode(['last_success' => time()]));
    }
}
