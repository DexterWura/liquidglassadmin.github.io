<?php
declare(strict_types=1);

const DEMO_SESSION_TTL = 1800;
const DEMO_URL_TTL = 600;
const DEMO_RATE_WINDOW = 60;
const DEMO_RATE_MAX = 240;

function demo_start_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    session_name('lg_demo');
    session_start([
        'cookie_httponly' => true,
        'cookie_secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
        'cookie_samesite' => 'Lax',
        'use_strict_mode' => true,
        'gc_maxlifetime' => DEMO_SESSION_TTL,
    ]);

    if (!isset($_SESSION['demo_started_at'])) {
        $_SESSION['demo_started_at'] = time();
    }

    if (!isset($_SESSION['demo_nonce'])) {
        $_SESSION['demo_nonce'] = bin2hex(random_bytes(16));
    }
}

function demo_secret_key(): string
{
    $envKey = getenv('LIQUIDGLASS_DEMO_SECRET');
    if (is_string($envKey) && $envKey !== '') {
        return $envKey;
    }

    return 'replace-this-demo-secret-in-production';
}

function demo_allowed_pages(): array
{
    return [
        'dashboard' => 'dashboard.html',
        'analytics' => 'analytics.html',
        'users' => 'users.html',
        'products' => 'products.html',
        'orders' => 'orders.html',
        'pos' => 'pos.html',
        'messages' => 'messages.html',
        'notifications' => 'notifications.html',
        'calendar' => 'calendar.html',
        'reports' => 'reports.html',
        'invoices' => 'invoices.html',
        'expenses' => 'expenses.html',
        'tasks' => 'tasks.html',
        'blog' => 'blog.html',
        'help' => 'help.html',
        'tables' => 'tables.html',
        'forms' => 'forms.html',
        'buttons' => 'buttons.html',
        'progress' => 'progress.html',
        'profile' => 'profile.html',
        'modals' => 'modals.html',
        'settings' => 'settings.html',
        'login' => 'login.html',
        'signup' => 'signup.html',
    ];
}

function demo_page_for_file(string $file): ?string
{
    $allowed = demo_allowed_pages();
    $page = array_search($file, $allowed, true);
    return $page === false ? null : $page;
}

function demo_sign(string $page, int $expires): string
{
    $nonce = (string)($_SESSION['demo_nonce'] ?? '');
    $payload = $page . '|' . $expires . '|' . $nonce;
    return hash_hmac('sha256', $payload, demo_secret_key());
}

function demo_url(string $page, int $ttl = DEMO_URL_TTL): string
{
    $expires = time() + $ttl;
    $sig = demo_sign($page, $expires);
    return 'demo.php?page=' . rawurlencode($page) . '&exp=' . $expires . '&sig=' . rawurlencode($sig);
}

function demo_validate_request(string $page, string $exp, string $sig): bool
{
    if (!isset(demo_allowed_pages()[$page])) {
        return false;
    }

    if (!ctype_digit($exp)) {
        return false;
    }

    $expires = (int)$exp;
    if ($expires < time()) {
        return false;
    }

    $expected = demo_sign($page, $expires);
    return hash_equals($expected, $sig);
}

function demo_session_valid(): bool
{
    $startedAt = (int)($_SESSION['demo_started_at'] ?? 0);
    if ($startedAt <= 0) {
        return false;
    }
    return (time() - $startedAt) <= DEMO_SESSION_TTL;
}

function demo_client_id(): string
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    return hash('sha256', $ip . '|' . $agent);
}

function demo_rate_limit_check(): void
{
    $now = time();
    $windowStart = $now - DEMO_RATE_WINDOW;
    $bucket = $_SESSION['demo_rate_bucket'] ?? [];

    if (!is_array($bucket)) {
        $bucket = [];
    }

    $bucket = array_filter($bucket, static function ($ts) use ($windowStart) {
        return is_int($ts) && $ts >= $windowStart;
    });
    $bucket[] = $now;
    $_SESSION['demo_rate_bucket'] = $bucket;

    if (count($bucket) > DEMO_RATE_MAX) {
        http_response_code(429);
        header('Content-Type: text/plain; charset=UTF-8');
        echo 'Too many demo requests. Please try again shortly.';
        exit;
    }
}

function demo_watermark_label(): string
{
    $stamp = gmdate('Y-m-d H:i');
    $fingerprint = substr(demo_client_id(), 0, 10);
    return 'Demo Session ' . $fingerprint . ' | ' . $stamp . ' UTC';
}

