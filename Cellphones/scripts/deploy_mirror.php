<?php
// Simple deploy script to copy mirror assets into public/clone_cellphones
$root = __DIR__ . '/..';
$mirror = $root . '/mirror/cellphones';
$dest = $root . '/public/clone_cellphones';

function rrmdir(string $dir): void {
    if (!is_dir($dir)) return;
    $items = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
    foreach ($items as $item) {
        if ($item->isDir()) rmdir($item->getPathname());
        else unlink($item->getPathname());
    }
    rmdir($dir);
}

function rcopy(string $src, string $dst): void {
    $dir = opendir($src);
    @mkdir($dst, 0777, true);
    while(false !== ($file = readdir($dir))) {
        if (($file !== '.') && ($file !== '..')) {
            $srcPath = $src . '/' . $file;
            $dstPath = $dst . '/' . $file;
            if (is_dir($srcPath)) rcopy($srcPath, $dstPath);
            else copy($srcPath, $dstPath);
        }
    }
    closedir($dir);
}

if (!is_dir($mirror)) {
    echo "Mirror source not found: $mirror\n";
    exit(1);
}

// Remove old
if (is_dir($dest)) {
    echo "Removing existing destination: $dest\n";
    rrmdir($dest);
}

echo "Copying mirror to public folder...\n";
rcopy($mirror, $dest);

// Fix index.html paths: convert relative "next/_next/..." to "clone_cellphones/next/_next/..." so assets load under /clone_cellphones
$index = $dest . '/index.html';
if (file_exists($index)) {
    $html = file_get_contents($index);
    $html = str_replace('href="next/_next', 'href="/clone_cellphones/next/_next', $html);
    $html = str_replace('src="next/_next', 'src="/clone_cellphones/next/_next', $html);
    $html = str_replace('href="/next/_next', 'href="/clone_cellphones/next/_next', $html);
    $html = str_replace('src="/next/_next', 'src="/clone_cellphones/next/_next', $html);
    file_put_contents($index, $html);
    echo "Fixed index.html asset paths.\n";
}

echo "Deploy finished. You can now visit /clone_cellphones/index.html or use ?page=mirror if enabled.\n";
