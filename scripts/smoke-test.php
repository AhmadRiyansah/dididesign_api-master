<?php

/**
 * Smoke test API — jalankan: php scripts/smoke-test.php
 * Pastikan `php artisan serve` sudah berjalan di http://127.0.0.1:8000
 */

$base = getenv('API_BASE_URL') ?: 'http://127.0.0.1:8000';

function request(string $method, string $url, ?array $body = null, ?string $token = null): array
{
    $ch = curl_init($url);
    $headers = ['Accept: application/json', 'Content-Type: application/json'];
    if ($token) {
        $headers[] = 'Authorization: Bearer '.$token;
    }
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST  => $method,
        CURLOPT_HTTPHEADER     => $headers,
        CURLOPT_POSTFIELDS     => $body ? json_encode($body) : null,
    ]);
    $raw = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return ['code' => $code, 'body' => json_decode($raw, true) ?? $raw];
}

$passed = 0;
$failed = 0;

function check(string $label, bool $ok, mixed $detail = null): void
{
    global $passed, $failed;
    if ($ok) {
        $passed++;
        echo "[OK]   {$label}\n";
    } else {
        $failed++;
        echo "[FAIL] {$label}\n";
        if ($detail !== null) {
            echo '       '.(is_string($detail) ? $detail : json_encode($detail, JSON_UNESCAPED_UNICODE))."\n";
        }
    }
}

echo "API base: {$base}\n\n";

// 1. Health
$r = request('GET', "{$base}/up");
check('Health /up', $r['code'] === 200, $r);

// 2. Products list (public)
$r = request('GET', "{$base}/api/products");
check('GET /api/products', $r['code'] === 200, $r);

// 3. Admin login
$r = request('POST', "{$base}/api/auth/login", [
    'email'    => 'admin@dididesign.com',
    'password' => 'password',
]);
$adminToken = $r['body']['token'] ?? null;
check('POST /api/auth/login (admin)', $r['code'] === 200 && $adminToken, $r);

// 4. Admin profile nested
$hasProfile = isset($r['body']['user']['profile']['name']);
check('Login response includes profile', $hasProfile, $r['body']['user'] ?? null);

// 5. Authenticated /user
if ($adminToken) {
    $r = request('GET', "{$base}/api/user", null, $adminToken);
    check('GET /api/user (authenticated)', $r['code'] === 200 && isset($r['body']['profile']), $r);
}

// 6. Admin couriers list
if ($adminToken) {
    $r = request('GET', "{$base}/api/admin/couriers", null, $adminToken);
    check('GET /api/admin/couriers', $r['code'] === 200, $r);
}

// 7. Auth sync (new mobile user)
$uid = 'smoke-test-'.time();
$r = request('POST', "{$base}/api/auth/sync", [
    'firebase_uid' => $uid,
    'email'        => "smoke_{$uid}@test.local",
    'name'         => 'Smoke Test User',
]);
check('POST /api/auth/sync', $r['code'] === 200 && isset($r['body']['token']), $r);

echo "\n---\nPassed: {$passed}, Failed: {$failed}\n";
exit($failed > 0 ? 1 : 0);
