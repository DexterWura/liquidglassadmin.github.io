<?php
declare(strict_types=1);

require_once __DIR__ . '/server/bootstrap.php';
demo_start_session();
demo_rate_limit_check();

$page = isset($_GET['page']) ? (string)$_GET['page'] : '';
$exp = isset($_GET['exp']) ? (string)$_GET['exp'] : '';
$sig = isset($_GET['sig']) ? (string)$_GET['sig'] : '';

if (!demo_session_valid() || !demo_validate_request($page, $exp, $sig)) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Demo link is invalid or has expired. Please start again from the homepage.';
    exit;
}

$allowed = demo_allowed_pages();
$fileName = $allowed[$page] ?? '';
$filePath = __DIR__ . DIRECTORY_SEPARATOR . $fileName;
if ($fileName === '' || !is_file($filePath)) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Requested demo page was not found.';
    exit;
}

header('Content-Type: text/html; charset=UTF-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header("Content-Security-Policy: default-src 'self' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net https://ui-avatars.com data:; img-src 'self' https://ui-avatars.com data: https:; style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com; script-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net; font-src 'self' https://cdnjs.cloudflare.com data:; frame-ancestors 'self'; base-uri 'self';");

$content = (string)file_get_contents($filePath);

$content = preg_replace_callback(
    '/(href|src)=("|\')([^"\']+)\\2/i',
    static function (array $matches): string {
        $attr = $matches[1];
        $quote = $matches[2];
        $url = $matches[3];

        if (preg_match('/^(https?:|mailto:|tel:|#|javascript:)/i', $url) === 1) {
            return $matches[0];
        }

        if ($url === 'index.html') {
            return $attr . '=' . $quote . 'index.php' . $quote;
        }

        if (preg_match('/^([a-z0-9_-]+)\.html$/i', $url, $pageMatch) === 1) {
            $target = strtolower($pageMatch[1]);
            if (isset(demo_allowed_pages()[$target])) {
                return $attr . '=' . $quote . demo_url($target) . $quote;
            }
            return $matches[0];
        }

        if (preg_match('/^assets\/(css|js)\/[a-z0-9_.-]+\.(css|js)$/i', $url) === 1) {
            $assetExp = time() + DEMO_URL_TTL;
            $assetUrl = 'asset.php?f=' . rawurlencode($url) . '&exp=' . $assetExp . '&sig=' . rawurlencode(demo_sign($url, $assetExp));
            return $attr . '=' . $quote . $assetUrl . $quote;
        }

        if (preg_match('/^assets\/images\/[a-z0-9_.-]+\.(png|jpg|jpeg|gif|webp|svg)$/i', $url) === 1) {
            $assetExp = time() + DEMO_URL_TTL;
            $assetUrl = 'asset.php?f=' . rawurlencode($url) . '&exp=' . $assetExp . '&sig=' . rawurlencode(demo_sign($url, $assetExp));
            return $attr . '=' . $quote . $assetUrl . $quote;
        }

        return $matches[0];
    },
    $content
);

$watermark = htmlspecialchars(demo_watermark_label(), ENT_QUOTES, 'UTF-8');
$guardExp = time() + DEMO_URL_TTL;
$watermarkHtml = '<div id="demo-watermark" style="position:fixed;right:12px;bottom:12px;z-index:99999;font-size:11px;background:rgba(15,15,20,0.72);color:#fff;padding:6px 10px;border-radius:10px;backdrop-filter:blur(8px);pointer-events:none;user-select:none;">'
    . $watermark
    . '</div><script src="asset.php?f=assets/js/demo-guard.js&amp;exp='
    . $guardExp
    . '&amp;sig='
    . rawurlencode(demo_sign('assets/js/demo-guard.js', $guardExp))
    . '"></script>';

$content = str_ireplace('</body>', $watermarkHtml . '</body>', $content);
echo $content;

