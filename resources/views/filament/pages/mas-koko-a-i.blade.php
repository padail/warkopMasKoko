<x-filament-panels::page>
    @php
        $sales = $insights['sales'] ?? [];
        $comparison = $insights['sales_comparison'] ?? [];
        $stock = $insights['stock'] ?? [];
        $expenses = $insights['expenses'] ?? [];
        $topProducts = $insights['top_products'] ?? [];
        $stockAlerts = $stock['alerts'] ?? [];
        $formatRupiah = fn ($amount) => 'Rp '.number_format((int) ($amount ?? 0), 0, ',', '.');
    @endphp

    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold tracking-tight text-gray-950 dark:text-white">
                    Business Intelligence Assistant
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Analisis kondisi Warkop Mas Koko berdasarkan data operasional.
                </p>
            </div>

            <form wire:submit="applyPeriod" class="flex flex-col gap-2 sm:flex-row sm:items-end">
                <div>
                    <label for="startDate" class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">
                        Tanggal mulai
                    </label>
                    <input
                        id="startDate"
                        type="date"
                        wire:model="startDate"
                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white sm:w-40"
                    >
                    @error('startDate')
                        <p class="mt-1 text-xs text-danger-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="endDate" class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">
                        Tanggal akhir
                    </label>
                    <input
                        id="endDate"
                        type="date"
                        wire:model="endDate"
                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white sm:w-40"
                    >
                    @error('endDate')
                        <p class="mt-1 text-xs text-danger-600">{{ $message }}</p>
                    @enderror
                </div>

                <x-filament::button type="submit" icon="heroicon-o-calendar" wire:loading.attr="disabled" wire:target="applyPeriod">
                    <span wire:loading.remove wire:target="applyPeriod">Terapkan</span>
                    <span wire:loading wire:target="applyPeriod">Memuat...</span>
                </x-filament::button>
            </form>
        </div>

        @if ($insightsError)
            <div class="rounded-xl border border-danger-200 bg-danger-50 p-4 text-sm text-danger-700 dark:border-danger-900 dark:bg-danger-950/30 dark:text-danger-300">
                {{ $insightsError }}
            </div>
        @endif

        @if (! empty($insights))
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Omzet periode</p>
                    <p class="mt-2 text-2xl font-semibold text-gray-950 dark:text-white">
                        {{ $formatRupiah($sales['revenue_total'] ?? 0) }}
                    </p>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        {{ number_format((int) ($sales['transactions_count'] ?? 0), 0, ',', '.') }} transaksi selesai
                    </p>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Rata-rata transaksi</p>
                    <p class="mt-2 text-2xl font-semibold text-gray-950 dark:text-white">
                        {{ $formatRupiah($sales['average_per_transaction'] ?? 0) }}
                    </p>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Nilai rata-rata per transaksi</p>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Perubahan omzet</p>
                    @if (is_numeric($comparison['revenue_change_percent'] ?? null))
                        <p class="mt-2 text-2xl font-semibold text-gray-950 dark:text-white">
                            {{ ($comparison['revenue_change_percent'] > 0 ? '+' : '') . number_format($comparison['revenue_change_percent'], 2, ',', '.') }}%
                        </p>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Dibanding periode sebelumnya
                        </p>
                    @else
                        <p class="mt-2 text-lg font-semibold text-gray-950 dark:text-white">Belum ada baseline</p>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Data pembanding belum tersedia</p>
                    @endif
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Pengeluaran tercatat</p>
                    <p class="mt-2 text-2xl font-semibold text-gray-950 dark:text-white">
                        {{ $formatRupiah($expenses['expenses_total'] ?? 0) }}
                    </p>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        {{ number_format((int) ($expenses['expenses_count'] ?? 0), 0, ',', '.') }} catatan pengeluaran
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
                <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="mb-4 flex items-center justify-between">
                        <div>
                            <h3 class="font-semibold text-gray-950 dark:text-white">Produk terlaris</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Berdasarkan jumlah unit terjual</p>
                        </div>
                        <x-filament::icon icon="heroicon-o-trophy" class="h-5 w-5 text-warning-500" />
                    </div>

                    @forelse ($topProducts as $index => $product)
                        <div class="flex items-center justify-between gap-3 border-t border-gray-100 py-3 first:border-0 dark:border-gray-800">
                            <div class="flex min-w-0 items-center gap-3">
                                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-gray-100 text-xs font-semibold text-gray-700 dark:bg-gray-800 dark:text-gray-200">
                                    {{ $index + 1 }}
                                </span>
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $product['product_name'] }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ number_format((float) $product['quantity_sold'], 3, ',', '.') }} unit
                                    </p>
                                </div>
                            </div>
                            <p class="shrink-0 text-sm font-medium text-gray-700 dark:text-gray-200">
                                {{ $formatRupiah($product['revenue_total']) }}
                            </p>
                        </div>
                    @empty
                        <p class="py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                            Belum ada produk terjual pada periode ini.
                        </p>
                    @endforelse
                </section>

                <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="mb-4 flex items-center justify-between">
                        <div>
                            <h3 class="font-semibold text-gray-950 dark:text-white">Peringatan persediaan</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Kondisi stok saat ini</p>
                        </div>
                        <x-filament::icon icon="heroicon-o-exclamation-triangle" class="h-5 w-5 text-warning-500" />
                    </div>

                    <div class="mb-3 grid grid-cols-3 gap-2">
                        <div class="rounded-lg bg-gray-50 p-2 dark:bg-gray-800">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Aktif</p>
                            <p class="text-lg font-semibold text-gray-950 dark:text-white">{{ $stock['summary']['active_count'] ?? 0 }}</p>
                        </div>
                        <div class="rounded-lg bg-warning-50 p-2 dark:bg-warning-950/20">
                            <p class="text-xs text-warning-700 dark:text-warning-300">Menipis</p>
                            <p class="text-lg font-semibold text-warning-800 dark:text-warning-200">{{ $stock['summary']['low_count'] ?? 0 }}</p>
                        </div>
                        <div class="rounded-lg bg-danger-50 p-2 dark:bg-danger-950/20">
                            <p class="text-xs text-danger-700 dark:text-danger-300">Habis</p>
                            <p class="text-lg font-semibold text-danger-800 dark:text-danger-200">{{ $stock['summary']['out_count'] ?? 0 }}</p>
                        </div>
                    </div>

                    @forelse ($stockAlerts as $item)
                        <div class="flex items-center justify-between gap-3 border-t border-gray-100 py-3 first:border-0 dark:border-gray-800">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-gray-900 dark:text-white">{{ $item['name'] }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $item['category'] }}</p>
                            </div>
                            <div class="shrink-0 text-right">
                                <p class="text-sm font-semibold text-warning-700 dark:text-warning-300">
                                    {{ number_format((float) $item['current_stock'], 3, ',', '.') }} {{ $item['unit'] }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Minimum {{ number_format((float) $item['minimum_stock'], 3, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <p class="py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                            Tidak ada barang aktif yang berada di bawah batas stok minimum.
                        </p>
                    @endforelse
                </section>
            </div>
        @endif

        <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="flex flex-col gap-3 border-b border-gray-200 p-5 dark:border-gray-800 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary-50 text-primary-700 dark:bg-primary-950/40 dark:text-primary-300">
                        <x-filament::icon icon="heroicon-o-sparkles" class="h-6 w-6" />
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-950 dark:text-white">Tanyakan kepada Mas Koko AI</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Jawaban menggunakan konteks data bisnis pada periode terpilih.
                        </p>
                    </div>
                </div>

                <x-filament::button
                    color="gray"
                    outlined
                    size="sm"
                    icon="heroicon-o-trash"
                    wire:click="clearConversation"
                    wire:confirm="Hapus riwayat percakapan sementara ini?"
                >
                    Hapus percakapan
                </x-filament::button>
            </div>

            <div class="max-h-[520px] space-y-4 overflow-y-auto p-5" aria-live="polite">
                @forelse ($messages as $message)
                    <div class="flex {{ $message['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[90%] rounded-2xl px-4 py-3 text-sm sm:max-w-[80%] {{ $message['role'] === 'user' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100' }}">
                            <p class="mb-1 text-xs font-semibold opacity-70">
                                {{ $message['role'] === 'user' ? 'Owner' : 'Mas Koko AI' }}
                            </p>
                            <div class="break-words leading-6 space-y-2 [&_p]:mb-2 [&_ul]:my-2 [&_ul]:list-disc [&_ul]:pl-5 [&_ol]:my-2 [&_ol]:list-decimal [&_ol]:pl-5 [&_li]:my-1 [&_strong]:font-semibold [&_h1]:mb-2 [&_h1]:text-base [&_h1]:font-semibold [&_h2]:mb-2 [&_h2]:text-base [&_h2]:font-semibold [&_h3]:mb-2 [&_h3]:text-sm [&_h3]:font-semibold">
                                {!! app(\App\Services\AI\AIResponseFormatter::class)->toHtml($message['content']) !!}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="mx-auto max-w-xl py-8 text-center">
                        <x-filament::icon icon="heroicon-o-chat-bubble-left-right" class="mx-auto h-10 w-10 text-gray-400" />
                        <h4 class="mt-3 font-medium text-gray-900 dark:text-white">Mulai analisis bisnis</h4>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Ajukan pertanyaan tentang penjualan, produk terlaris, stok, atau pengeluaran.
                        </p>
                        <div class="mt-4 flex flex-wrap justify-center gap-2">
                            <span class="rounded-full bg-gray-100 px-3 py-1.5 text-xs text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                                Apa produk terlaris?
                            </span>
                            <span class="rounded-full bg-gray-100 px-3 py-1.5 text-xs text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                                Bagaimana kondisi stok?
                            </span>
                            <span class="rounded-full bg-gray-100 px-3 py-1.5 text-xs text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                                Bandingkan omzet periode ini.
                            </span>
                        </div>
                    </div>
                @endforelse

                <div wire:loading.flex wire:target="sendMessage" class="justify-start">
                    <div class="rounded-2xl bg-gray-100 px-4 py-3 text-sm text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                        Mas Koko AI sedang menganalisis data...
                    </div>
                </div>
            </div>

            @if ($chatError)
                <div class="mx-5 mb-3 rounded-lg border border-danger-200 bg-danger-50 p-3 text-sm text-danger-700 dark:border-danger-900 dark:bg-danger-950/30 dark:text-danger-300">
                    {{ $chatError }}
                </div>
            @endif

            <form wire:submit="sendMessage" class="border-t border-gray-200 p-4 dark:border-gray-800">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
                    <div class="min-w-0 flex-1">
                        <label for="question" class="sr-only">Pertanyaan untuk Mas Koko AI</label>
                        <textarea
                            id="question"
                            wire:model="question"
                            rows="2"
                            maxlength="1000"
                            placeholder="Contoh: Apa yang perlu saya perhatikan dari penjualan dan stok minggu ini?"
                            class="w-full resize-y rounded-xl border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-950 dark:text-white"
                        ></textarea>
                        @error('question')
                            <p class="mt-1 text-xs text-danger-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-400">Maksimal 1.000 karakter · Maksimal 10 pertanyaan per menit</p>
                    </div>

                    <x-filament::button
                        type="submit"
                        icon="heroicon-o-paper-airplane"
                        wire:loading.attr="disabled"
                        wire:target="sendMessage"
                    >
                        <span wire:loading.remove wire:target="sendMessage">Kirim</span>
                        <span wire:loading wire:target="sendMessage">Memproses...</span>
                    </x-filament::button>
                </div>
            </form>
        </section>

        <p class="text-xs leading-5 text-gray-500 dark:text-gray-400">
            Catatan: Mas Koko AI memberikan analisis berdasarkan data yang tersedia. Omzet bukan laba bersih,
            dan rekomendasi AI tidak menggantikan verifikasi operasional oleh owner.
        </p>
    </div>
</x-filament-panels::page>
