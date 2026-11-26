<?php

namespace App\Services\AI;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use RuntimeException;

class GroqService
{
    /**
     * Memeriksa apakah API key dapat mengakses model yang dikonfigurasi.
     *
     * @return array{id: string, owned_by?: string}
     */
    public function checkConnection(): array
    {
        $this->ensureConfigured();

        $configuredModel = (string) config('services.groq.model');
        $response = $this->request()->get('/models');

        $response->throw();

        $models = $response->json('data');

        if (! is_array($models)) {
            throw new RuntimeException('Groq mengembalikan daftar model dengan format yang tidak valid.');
        }

        foreach ($models as $model) {
            if (($model['id'] ?? null) === $configuredModel) {
                return [
                    'id' => $model['id'],
                    'owned_by' => $model['owned_by'] ?? null,
                ];
            }
        }

        throw new RuntimeException('Model yang dikonfigurasi tidak tersedia pada akun Groq ini.');
    }

    /**
     * Mengirim percakapan ke endpoint Chat Completions Groq.
     *
     * @param  array<int, array{role: string, content: string}>  $messages
     */
    public function chatCompletion(array $messages, ?int $maxCompletionTokens = 1200): string
    {
        $this->ensureConfigured();

        if ($messages === []) {
            throw new InvalidArgumentException('Pesan percakapan tidak boleh kosong.');
        }

        foreach ($messages as $message) {
            if (
                ! is_array($message)
                || ! in_array($message['role'] ?? null, ['system', 'user', 'assistant'], true)
                || ! is_string($message['content'] ?? null)
                || trim($message['content']) === ''
            ) {
                throw new InvalidArgumentException('Format pesan percakapan tidak valid.');
            }
        }

        $payload = [
            'model' => config('services.groq.model'),
            'messages' => $messages,
        ];

        if ($maxCompletionTokens !== null) {
            if ($maxCompletionTokens < 1) {
                throw new InvalidArgumentException('Batas token harus lebih besar dari nol.');
            }

            $payload['max_completion_tokens'] = $maxCompletionTokens;
        }

        $response = $this->request()
            ->post('/chat/completions', $payload);

        $response->throw();

        $content = $response->json('choices.0.message.content');

        if (! is_string($content) || trim($content) === '') {
            throw new RuntimeException('Groq tidak mengembalikan konten jawaban yang valid.');
        }

        return trim($content);
    }

    protected function request(): PendingRequest
    {
        return Http::baseUrl(rtrim((string) config('services.groq.base_url'), '/'))
            ->withToken((string) config('services.groq.api_key'))
            ->acceptJson()
            ->asJson()
            ->connectTimeout(10)
            ->timeout((int) config('services.groq.timeout', 30));
    }

    protected function ensureConfigured(): void
    {
        if (blank(config('services.groq.api_key'))) {
            throw new RuntimeException('GROQ_API_KEY belum dikonfigurasi.');
        }

        if (blank(config('services.groq.base_url'))) {
            throw new RuntimeException('GROQ_BASE_URL belum dikonfigurasi.');
        }

        if (blank(config('services.groq.model'))) {
            throw new RuntimeException('GROQ_MODEL belum dikonfigurasi.');
        }
    }
}
