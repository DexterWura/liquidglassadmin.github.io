<?php
declare(strict_types=1);

require_once __DIR__ . '/server/bootstrap.php';
demo_start_session();
demo_rate_limit_check();

$indexPath = __DIR__ . '/index.html';
if (!is_file($indexPath)) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Landing page is missing.';
    exit;
}

$content = (string)file_get_contents($indexPath);
$dashboardUrl = demo_url('dashboard');

$content = preg_replace(
    '/href=("|\')dashboard\.html\\1/i',
    'href="$1' . $dashboardUrl . '$1',
    $content
);

header('Content-Type: text/html; charset=UTF-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');

echo $content;

