<?php

declare(strict_types=1);

namespace App\Services;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;

class MarkdownSanitizer
{
    private MarkdownConverter $converter;

    public function __construct()
    {
        // Konfigurasi agar tag HTML diizinkan tetapi di-sanitize.
        // League CommonMark memiliki built-in HTML input allow/escape config
        $config = [
            'html_input' => 'strip', // Akan menghapus raw HTML berbahaya yang diketik user
            'allow_unsafe_links' => false,
        ];

        $environment = new Environment($config);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new GithubFlavoredMarkdownExtension());

        $this->converter = new MarkdownConverter($environment);
    }

    /**
     * Konversi Markdown ke HTML yang sudah dibersihkan (Sanitized).
     */
    public function sanitizeAndConvert(string $markdown): string
    {
        // 1. Konversi ke HTML via League CommonMark (raw html di-strip)
        $html = $this->converter->convert($markdown)->getContent();

        // 2. Extra Sanitizer layer untuk tag/atribut yang mungkin lolos
        // Menggunakan regex dasar untuk membuang event handler on* dan tag script/iframe/object
        
        // Hapus tag berbahaya jika entah bagaimana masuk
        $html = preg_replace('/<(script|iframe|object|embed|applet)[^>]*>.*?<\/\1>/is', '', $html);
        
        // Hapus inline event handlers (onclick, onerror, dll)
        $html = preg_replace('/(<[^>]+?)(on[a-z]+\s*=\s*(["\']).*?\3)([^>]*?>)/is', '$1$4', $html);
        $html = preg_replace('/(<[^>]+?)(javascript:)([^>]*?>)/is', '$1$3', $html);

        return trim($html);
    }
}
