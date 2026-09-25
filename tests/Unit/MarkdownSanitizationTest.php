<?php

declare(strict_types=1);

use App\Services\MarkdownSanitizer;

beforeEach(function () {
    $this->sanitizer = new MarkdownSanitizer();
});

test('mengonversi markdown normal dengan benar', function () {
    $markdown = "**Tebal** dan *Miring*";
    $html = $this->sanitizer->sanitizeAndConvert($markdown);
    
    expect($html)->toContain('<strong>Tebal</strong>');
    expect($html)->toContain('<em>Miring</em>');
});

test('menghapus tag script dan iframe yang berbahaya', function () {
    $markdown = "Halo <script>alert('xss');</script> <iframe>evil</iframe> Dunia";
    $html = $this->sanitizer->sanitizeAndConvert($markdown);
    
    expect($html)->not->toContain('<script>');
    expect($html)->not->toContain('<iframe>');
    // Commonmark dengan 'html_input' => 'strip' biasanya membuang isinya atau escape
    expect($html)->toContain('Halo');
});

test('menghapus inline event handlers berbahaya', function () {
    $markdown = "[Link Jahat](javascript:alert('xss'))";
    $html = $this->sanitizer->sanitizeAndConvert($markdown);
    
    // allow_unsafe_links => false pada konfigurasi CommonMark membuang href javascript:
    expect($html)->not->toContain("javascript:alert('xss')");
});
