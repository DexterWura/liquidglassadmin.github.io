<?php
declare(strict_types=1);

function buyer_package_root(): string
{
    return realpath(__DIR__ . '/..') ?: dirname(__DIR__);
}

function buyer_package_rrmdir(string $dir): void
{
    if (!is_dir($dir)) {
        return;
    }

    $items = scandir($dir);
    if ($items === false) {
        return;
    }

    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }
        $path = $dir . DIRECTORY_SEPARATOR . $item;
        if (is_dir($path)) {
            buyer_package_rrmdir($path);
            @rmdir($path);
        } else {
            @unlink($path);
        }
    }
}

function buyer_package_copy_files(string $sourceDir, string $targetDir, callable $allow): int
{
    if (!is_dir($sourceDir)) {
        return 0;
    }

    $files = scandir($sourceDir);
    if ($files === false) {
        return 0;
    }

    $copied = 0;
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        $sourcePath = $sourceDir . DIRECTORY_SEPARATOR . $file;
        if (!is_file($sourcePath) || !$allow($file)) {
            continue;
        }
        $targetPath = $targetDir . DIRECTORY_SEPARATOR . $file;
        if (!@copy($sourcePath, $targetPath)) {
            throw new RuntimeException('Failed to copy file: ' . $file);
        }
        $copied++;
    }

    return $copied;
}

function buyer_package_create(array $options = []): array
{
    $projectRoot = buyer_package_root();
    $version = isset($options['version']) && is_string($options['version']) && $options['version'] !== ''
        ? $options['version']
        : '1.0.2';

    $outputDir = isset($options['output_dir']) && is_string($options['output_dir']) && $options['output_dir'] !== ''
        ? $options['output_dir']
        : 'release/dist';

    $distRoot = $projectRoot . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $outputDir);
    $packageName = 'liquid-glass-admin-' . $version;
    $stagingRoot = $distRoot . DIRECTORY_SEPARATOR . $packageName;
    $zipPath = $distRoot . DIRECTORY_SEPARATOR . $packageName . '.zip';

    if (!is_dir($distRoot) && !mkdir($distRoot, 0775, true) && !is_dir($distRoot)) {
        throw new RuntimeException('Unable to create output directory.');
    }

    buyer_package_rrmdir($stagingRoot);
    @rmdir($stagingRoot);
    @unlink($zipPath);

    $dirs = [
        $stagingRoot,
        $stagingRoot . DIRECTORY_SEPARATOR . 'assets',
        $stagingRoot . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'css',
        $stagingRoot . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'js',
        $stagingRoot . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images',
    ];

    foreach ($dirs as $dir) {
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new RuntimeException('Unable to create staging directory: ' . $dir);
        }
    }

    $htmlCount = buyer_package_copy_files(
        $projectRoot,
        $stagingRoot,
        static fn (string $file): bool => (bool)preg_match('/^[a-z0-9_.-]+\.html$/i', $file)
    );

    $cssCount = buyer_package_copy_files(
        $projectRoot . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'css',
        $stagingRoot . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'css',
        static fn (string $file): bool => (bool)preg_match('/^[a-z0-9_.-]+\.css$/i', $file)
    );

    $jsCount = buyer_package_copy_files(
        $projectRoot . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'js',
        $stagingRoot . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'js',
        static fn (string $file): bool => (bool)preg_match('/^[a-z0-9_.-]+\.js$/i', $file) && $file !== 'demo-guard.js'
    );

    $imageCount = buyer_package_copy_files(
        $projectRoot . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images',
        $stagingRoot . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images',
        static fn (string $file): bool => (bool)preg_match('/^[a-z0-9_.-]+\.(png|jpg|jpeg|gif|webp|svg)$/i', $file)
    );

    $buyerReadme = $projectRoot . DIRECTORY_SEPARATOR . 'README_BUYER.md';
    if (is_file($buyerReadme)) {
        @copy($buyerReadme, $stagingRoot . DIRECTORY_SEPARATOR . 'README.md');
    }

    $zip = new ZipArchive();
    if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        throw new RuntimeException('Unable to create buyer package zip.');
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($stagingRoot, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($iterator as $fileInfo) {
        /** @var SplFileInfo $fileInfo */
        $filePath = $fileInfo->getPathname();
        $relativePath = substr($filePath, strlen($stagingRoot) + 1);
        if ($relativePath === false) {
            continue;
        }
        $zip->addFile($filePath, str_replace('\\', '/', $relativePath));
    }
    $zip->close();

    return [
        'version' => $version,
        'zip_path' => $zipPath,
        'staging_path' => $stagingRoot,
        'counts' => [
            'html' => $htmlCount,
            'css' => $cssCount,
            'js' => $jsCount,
            'images' => $imageCount,
        ],
    ];
}

