<?php

use App\Services\AI\GroqService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config([
        'services.groq.api_key' => 'test-groq-key',
        'services.groq.base_url' => 'https://api.groq.com/openai/v1',
        'services.groq.model' => 'openai/gpt-oss-120b',
        'services.groq.timeout' => 30,
    ]);
});

test('it checks the configured model using the groq models endpoint', function () {
    Http::fake([
        'api.groq.com/openai/v1/models' => Http::response([
            'object' => 'list',
            'data' => [
                [
                    'id' => 'openai/gpt-oss-120b',
                    'object' => 'model',
                    'owned_by' => 'OpenAI',
                ],
            ],
        ]),
    ]);

    $model = app(GroqService::class)->checkConnection();

    expect($model['id'])->toBe('openai/gpt-oss-120b')
        ->and($model['owned_by'])->toBe('OpenAI');

    Http::assertSent(fn (Request $request) =>
        str_ends_with($request->url(), '/models')
        && $request->hasHeader('Authorization', 'Bearer test-groq-key')
    );
});

test('it sends chat completion requests to groq and returns assistant content', function () {
    Http::fake([
        'api.groq.com/openai/v1/chat/completions' => Http::response([
            'choices' => [
                [
                    'message' => [
                        'role' => 'assistant',
                        'content' => 'Analisis berhasil.',
                    ],
                ],
            ],
        ]),
    ]);

    $answer = app(GroqService::class)->chatCompletion([
        ['role' => 'system', 'content' => 'Kamu asisten bisnis.'],
        ['role' => 'user', 'content' => 'Analisis penjualan.'],
    ]);

    expect($answer)->toBe('Analisis berhasil.');

    Http::assertSent(fn (Request $request) =>
        $request->url() === 'https://api.groq.com/openai/v1/chat/completions'
        && $request['model'] === 'openai/gpt-oss-120b'
        && count($request['messages']) === 2
        && $request->hasHeader('Authorization', 'Bearer test-groq-key')
    );
});

test('it refuses to call groq when the api key is missing', function () {
    config(['services.groq.api_key' => null]);

    expect(fn () => app(GroqService::class)->checkConnection())
        ->toThrow(RuntimeException::class, 'GROQ_API_KEY belum dikonfigurasi.');
});

test('it rejects an empty conversation', function () {
    expect(fn () => app(GroqService::class)->chatCompletion([]))
        ->toThrow(InvalidArgumentException::class, 'Pesan percakapan tidak boleh kosong.');
});

test('it rejects malformed conversation messages', function () {
    expect(fn () => app(GroqService::class)->chatCompletion([
        ['role' => 'developer', 'content' => 'Instruksi tidak didukung.'],
    ]))->toThrow(InvalidArgumentException::class, 'Format pesan percakapan tidak valid.');
});

test('it propagates groq http errors without pretending the request succeeded', function () {
    Http::fake([
        'api.groq.com/openai/v1/models' => Http::response([
            'error' => ['message' => 'Invalid API Key'],
        ], 401),
    ]);

    expect(fn () => app(GroqService::class)->checkConnection())
        ->toThrow(\Illuminate\Http\Client\RequestException::class);
});
