<?php

namespace App\Controllers;

class MirrorController
{
    public function index()
    {
        $htmlPath = __DIR__ . '/../../mirror/cellphones/index.html';
        if (!file_exists($htmlPath)) {
            http_response_code(404);
            echo 'Mirrored UI not found. Run the scraper first.';
            return;
        }
        // Output raw mirrored HTML (already has relative asset paths)
        header('Content-Type: text/html; charset=UTF-8');
        readfile($htmlPath);
    }
}


