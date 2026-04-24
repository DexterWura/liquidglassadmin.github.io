<?php
declare(strict_types=1);

require_once __DIR__ . '/release/export-buyer-package.php';

header('Content-Type: application/json; charset=UTF-8');

if (strtoupper($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'ok' => false,
        'error' => 'Method not allowed. Use POST.',
    ]);
    exit;
}

$rawBody = file_get_contents('php://input');
if (!is_string($rawBody)) {
    $rawBody = '';
}

$payload = [];
if ($rawBody !== '') {
    $decoded = json_decode($rawBody, true);
    if (is_array($decoded)) {
        $payload = $decoded;
    }
}

$signatureHeader = $_SERVER['HTTP_X_FLIPIT_SIGNATURE'] ?? '';
$secret = getenv('FLIPIT_WEBHOOK_SECRET');
$token = getenv('FLIPIT_WEBHOOK_TOKEN');
$queryToken = isset($_GET['token']) ? (string)$_GET['token'] : '';

$authorized = false;

// Option A: token auth fallback (use when marketplace cannot sign payloads).
if (is_string($token) && $token !== '' && hash_equals($token, $queryToken)) {
    $authorized = true;
}

// Option B: HMAC signature auth: header "X-Flipit-Signature: sha256=<hash>".
if (!$authorized && is_string($secret) && $secret !== '' && is_string($signatureHeader) && $signatureHeader !== '') {
    $expected = 'sha256=' . hash_hmac('sha256', $rawBody, $secret);
    if (hash_equals($expected, trim($signatureHeader))) {
        $authorized = true;
    }
}

if (!$authorized) {
    http_response_code(401);
    echo json_encode([
        'ok' => false,
        'error' => 'Unauthorized webhook request.',
    ]);
    exit;
}

$version = '1.0.2';
if (isset($payload['version']) && is_string($payload['version']) && preg_match('/^[0-9a-z_.-]+$/i', $payload['version']) === 1) {
    $version = $payload['version'];
}

try {
    $result = buyer_package_create([
        'version' => $version,
        'output_dir' => 'release/dist',
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'error' => 'Package generation failed.',
        'message' => $e->getMessage(),
    ]);
    exit;
}

$orderId = isset($payload['order_id']) ? (string)$payload['order_id'] : null;
$buyerEmail = isset($payload['buyer_email']) ? (string)$payload['buyer_email'] : null;

http_response_code(200);
echo json_encode([
    'ok' => true,
    'message' => 'Buyer package generated.',
    'order_id' => $orderId,
    'buyer_email' => $buyerEmail,
    'version' => $result['version'],
    'zip_path' => $result['zip_path'],
    'counts' => $result['counts'],
]);

