<?php
declare(strict_types=1);

require_once __DIR__ . '/server/bootstrap.php';
demo_start_session();
demo_rate_limit_check();

if (!demo_session_valid()) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Demo session expired.';
    exit;
}

$asset = isset($_GET['f']) ? (string)$_GET['f'] : '';
$exp = isset($_GET['exp']) ? (string)$_GET['exp'] : '';
$sig = isset($_GET['sig']) ? (string)$_GET['sig'] : '';

if ($asset === '' || !ctype_digit($exp)) {
    http_response_code(400);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Invalid asset request.';
    exit;
}

$expires = (int)$exp;
if ($expires < time()) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Expired asset link.';
    exit;
}

$expectedSig = demo_sign($asset, $expires);
if (!hash_equals($expectedSig, $sig)) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Asset signature mismatch.';
    exit;
}

$isWhitelisted = preg_match('/^assets\/(css|js|images)\/[a-z0-9_.-]+\.(css|js|png|jpg|jpeg|gif|webp|svg)$/i', $asset) === 1;
if (!$isWhitelisted) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Asset not available.';
    exit;
}

$assetPath = realpath(__DIR__ . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $asset));
$rootPath = realpath(__DIR__);
if ($assetPath === false || $rootPath === false || strpos($assetPath, $rootPath) !== 0 || !is_file($assetPath)) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Asset missing.';
    exit;
}

$mime = 'application/octet-stream';
$ext = strtolower(pathinfo($assetPath, PATHINFO_EXTENSION));
if ($ext === 'css') {
    $mime = 'text/css; charset=UTF-8';
} elseif ($ext === 'js') {
    $mime = 'application/javascript; charset=UTF-8';
} elseif ($ext === 'png') {
    $mime = 'image/png';
} elseif ($ext === 'jpg' || $ext === 'jpeg') {
    $mime = 'image/jpeg';
} elseif ($ext === 'gif') {
    $mime = 'image/gif';
} elseif ($ext === 'webp') {
    $mime = 'image/webp';
} elseif ($ext === 'svg') {
    $mime = 'image/svg+xml';
}

header('Content-Type: ' . $mime);
header('Cache-Control: private, max-age=120');
header('X-Content-Type-Options: nosniff');
readfile($assetPath);

