<?php

namespace App\Filament\Pages;

use BackedEnum;
use UnitEnum;
use App\Services\AI\BusinessInsightService;
use App\Services\AI\GroqService;
use App\Services\AI\PromptBuilder;
use App\Services\OwnerDashboard\OwnerDashboardService;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Throwable;

class MasKokoAI extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';

    protected static ?string $navigationLabel = 'Mas Koko AI';

    protected static string|UnitEnum|null $navigationGroup = 'Analisis Bisnis';

    protected static ?string $title = 'Mas Koko AI';

    protected static ?string $slug = 'mas-koko-ai';

    protected static ?int $navigationSort = 10;

    protected string $view = 'filament.pages.mas-koko-a-i';

    public string $startDate = '';

    public string $endDate = '';

    public string $question = '';

    /**
     * Ringkasan bisnis untuk ditampilkan. Data ini selalu dapat dimuat ulang
     * dari database dan tidak digunakan sebagai sumber riwayat percakapan.
     *
     * @var array<string, mixed>
     */
    public array $insights = [];

    /**
     * Salinan tampilan percakapan. Saat mengirim pesan, riwayat otoritatif
     * selalu dibaca ulang dari session server.
     *
     * @var array<int, array{role: string, content: string}>
     */
    public array $messages = [];

    public ?string $insightsError = null;

    public ?string $chatError = null;

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->isAdmin();
    }

    public function mount(): void
    {
        $this->endDate = now()->toDateString();
        $this->startDate = now()->subDays(6)->toDateString();
        $this->messages = $this->conversationFromSession();

        $this->refreshInsights();
    }

    public function applyPeriod(): void
    {
        $filters = $this->validatedPeriodFilters();

        $this->refreshInsights($filters);
    }

    public function sendMessage(
        BusinessInsightService $businessInsights,
        PromptBuilder $promptBuilder,
        GroqService $groq
    ): void {
        $this->chatError = null;

        $validated = $this->validate([
            'question' => ['required', 'string', 'min:3', 'max:1000'],
        ]);

        $question = trim($validated['question']);

        if ($question === '') {
            throw ValidationException::withMessages([
                'question' => 'Pertanyaan tidak boleh kosong.',
            ]);
        }

        $filters = $this->validatedPeriodFilters();

        $rateLimitKey = 'mas-koko-ai:chat:'.auth()->id();

        if (RateLimiter::tooManyAttempts($rateLimitKey, 10)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            $this->chatError = "Batas pertanyaan tercapai. Coba lagi dalam {$seconds} detik.";

            return;
        }

        RateLimiter::hit($rateLimitKey, 60);

        try {
            $context = $businessInsights->buildContext($filters);
            $history = $this->conversationFromSession();

            $history[] = [
                'role' => 'user',
                'content' => $question,
            ];

            $apiMessages = $promptBuilder->buildMessages(
                $context,
                $history
            );

            $answer = $groq->chatCompletion(
                $apiMessages,
                1200
            );

            $history[] = [
                'role' => 'assistant',
                'content' => $answer,
            ];

            // Simpan maksimal 8 pesan (4 pasang pertanyaan/jawaban).
            $history = array_slice($history, -8);

            session()->put($this->conversationSessionKey(), $history);

            $this->messages = $history;
            $this->insights = $context;
            $this->question = '';
        } catch (Throwable $exception) {
            report($exception);

            $this->chatError = 'Mas Koko AI belum dapat memproses pertanyaan. Periksa koneksi layanan dan coba kembali.';
        }
    }

    public function clearConversation(): void
    {
        session()->forget($this->conversationSessionKey());

        $this->messages = [];
        $this->chatError = null;
        $this->question = '';
    }

    /**
     * @return array<string, string>
     */
    private function validatedPeriodFilters(): array
    {
        $validated = $this->validate([
            'startDate' => ['required', 'date_format:Y-m-d'],
            'endDate' => ['required', 'date_format:Y-m-d', 'after_or_equal:startDate'],
        ]);

        $start = Carbon::createFromFormat('Y-m-d', $validated['startDate']);
        $end = Carbon::createFromFormat('Y-m-d', $validated['endDate']);

        if ($start->diffInDays($end) > 30) {
            throw ValidationException::withMessages([
                'endDate' => 'Rentang analisis maksimal 31 hari.',
            ]);
        }

        return [
            'periode' => OwnerDashboardService::PRESET_RENTANG,
            'start' => $start->toDateString(),
            'end' => $end->toDateString(),
        ];
    }

    /**
     * @param  array<string, string>|null  $filters
     */
    private function refreshInsights(?array $filters = null): void
    {
        $this->insightsError = null;

        try {
            $filters ??= [
                'periode' => OwnerDashboardService::PRESET_RENTANG,
                'start' => $this->startDate,
                'end' => $this->endDate,
            ];

            $this->insights = app(BusinessInsightService::class)
                ->buildContext($filters);
        } catch (Throwable $exception) {
            report($exception);

            $this->insights = [];
            $this->insightsError = 'Ringkasan bisnis belum dapat dimuat. Periksa database dan konfigurasi aplikasi.';
        }
    }

    /**
     * Riwayat dari session server adalah sumber yang dipercaya untuk prompt.
     *
     * @return array<int, array{role: string, content: string}>
     */
    private function conversationFromSession(): array
    {
        $history = session()->get($this->conversationSessionKey(), []);

        if (! is_array($history)) {
            return [];
        }

        $safeHistory = [];

        foreach (array_slice($history, -8) as $message) {
            if (
                ! is_array($message)
                || ! in_array($message['role'] ?? null, ['user', 'assistant'], true)
                || ! is_string($message['content'] ?? null)
                || trim($message['content']) === ''
            ) {
                continue;
            }

            $safeHistory[] = [
                'role' => $message['role'],
                'content' => $message['content'],
            ];
        }

        return $safeHistory;
    }

    private function conversationSessionKey(): string
    {
        return 'mas_koko_ai.conversation.'.auth()->id();
    }
}
