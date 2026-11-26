<?php

namespace App\Services\AI;

use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class AIResponseFormatter
{
    /**
     * Render Markdown dari model sebagai HTML aman untuk ditampilkan di Blade.
     *
     * Input HTML mentah di-strip dan tautan tidak aman ditolak oleh parser.
     */
    public function toHtml(string $content): HtmlString
    {
        $markdown = $this->normalizeEscapedMarkdown($content);

        return new HtmlString(Str::markdown($markdown, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]));
    }

    private function normalizeEscapedMarkdown(string $content): string
    {
        // Beberapa respons model mengirim \*\* atau \### sehingga Markdown
        // tampil sebagai karakter mentah. Hilangkan escape sebelum tanda format.
        $normalized = preg_replace(
            '/\\\\([\\\\`*_{}\[\]()#+\-.!>|])/u',
            '$1',
            $content
        );

        $normalized ??= $content;

        // Batasi jarak kosong agar jawaban tetap ringkas dan mudah dibaca.
        $normalized = preg_replace("/\n{3,}/u", "\n\n", $normalized) ?? $normalized;

        return trim($normalized);
    }
}
