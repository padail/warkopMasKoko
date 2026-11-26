<?php

use App\Services\AI\AIResponseFormatter;

test('it renders escaped markdown as readable formatted html', function () {
    $html = app(AIResponseFormatter::class)->toHtml(
        '\\*\\*Kondisi stok saat ini\\*\\*'."\n\n".
        '- 6 barang dalam kondisi aman.'."\n".
        '- Tidak ada barang yang menipis.'
    )->toHtml();

    expect($html)
        ->toContain('<strong>Kondisi stok saat ini</strong>')
        ->toContain('<ul>')
        ->toContain('<li>6 barang dalam kondisi aman.</li>')
        ->not->toContain('\\*\\*');
});

test('it strips raw html from ai output', function () {
    $html = app(AIResponseFormatter::class)->toHtml(
        '<script>alert("xss")</script>'."\n\n".'Jawaban aman.'
    )->toHtml();

    expect($html)
        ->not->toContain('<script>')
        ->toContain('Jawaban aman.');
});
