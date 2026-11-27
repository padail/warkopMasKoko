@extends('layouts.app')

@section('title', 'Laporan Keuangan')

@section('content')
@php
    $formatRupiah = fn (int $amount): string => ($amount < 0 ? '-Rp ' : 'Rp ')
        . number_format(abs($amount), 0, ',', '.');
@endphp

<div class="mx-auto max-w-7xl px-4 py-4 md:px-6 md:py-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold">Laporan Keuangan</h1>
            <p class="mt-1 text-sm text-[#70685F]">
                {{ $date ? 'Ringkasan tanggal ' . \Illuminate\Support\Carbon::parse($date)->format('d/m/Y') : 'Ringkasan semua tanggal' }}
            </p>
        </div>

        <form method="GET" action="{{ route('karyawan.laporan-harian') }}"
              class="grid w-full grid-cols-1 gap-2 sm:w-auto sm:grid-cols-[minmax(170px,1fr)_auto_auto] sm:items-end">
            <div>
                <label class="mb-1 block text-sm font-medium" for="tanggal">Pilih tanggal</label>
                <input id="tanggal" name="tanggal" type="date" value="{{ $date ?? '' }}"
                       class="min-h-11 w-full rounded-lg border border-[#E4DED6] px-3 text-base sm:text-sm">
            </div>
            <button type="submit" class="min-h-11 w-full rounded-lg bg-[#6F4E37] px-4 font-semibold text-white sm:w-auto">
                Tampilkan
            </button>
            @if($date)
                <a class="inline-flex min-h-11 w-full items-center justify-center rounded-lg border border-[#E4DED6] px-4 text-sm font-semibold sm:w-auto"
                   href="{{ route('karyawan.laporan-harian') }}">
                    Semua hari
                </a>
            @endif
        </form>
    </div>

    @if(session('success'))
        <p class="mt-4 rounded-lg border border-[#237A57] bg-[#E8F4ED] px-4 py-3 text-sm text-[#237A57]" role="status">
            {{ session('success') }}
        </p>
    @endif

    @if($errors->any())
        <div class="mt-4 rounded-lg border border-[#B42318] bg-[#FDECEC] px-4 py-3 text-sm text-[#B42318]" role="alert">
            <p class="font-semibold">Periksa kembali data yang dimasukkan.</p>
            <ul class="mt-1 list-inside list-disc">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="mt-6">
        <div>
            <h2 class="text-lg font-semibold">Ringkasan pemasukan dan pengeluaran</h2>
            <p class="mt-1 text-xs text-[#70685F]">Angka di bawah mengikuti tanggal yang dipilih.</p>
        </div>

        <div class="mt-3 grid grid-cols-2 gap-3 lg:grid-cols-3">
            @foreach([
                ['Pemasukan tunai', $summary['income_cash'], 'text-[#237A57]'],
                ['Pemasukan non-tunai', $summary['income_non_cash'], 'text-[#237A57]'],
                ['Total pemasukan', $summary['income_total'], 'text-[#237A57]'],
                ['Pengeluaran tunai', $summary['expense_cash'], 'text-[#B42318]'],
                ['Pengeluaran non-tunai', $summary['expense_non_cash'], 'text-[#B42318]'],
                ['Total pengeluaran', $summary['expense_total'], 'text-[#B42318]'],
                ['Saldo tunai', $summary['net_cash'], 'text-[#26221E]'],
                ['Saldo non-tunai', $summary['net_non_cash'], 'text-[#26221E]'],
                ['Saldo bersih', $summary['net_total'], 'text-[#3B2A20]'],
            ] as [$label, $amount, $color])
                <article class="min-w-0 rounded-xl border border-[#E4DED6] bg-white p-3 sm:p-5">
                    <p class="text-xs font-medium leading-5 text-[#70685F] sm:text-sm">{{ $label }}</p>
                    <p class="mt-2 break-words text-base font-bold leading-tight {{ $color }} sm:text-xl">
                        {{ $formatRupiah((int) $amount) }}
                    </p>
                </article>
            @endforeach
        </div>

        <p class="mt-3 text-xs leading-5 text-[#70685F] sm:text-sm">
            {{ number_format($summary['transactions_count']) }} transaksi pemasukan
            <span aria-hidden="true">·</span>
            {{ number_format($summary['expenses_count']) }} catatan pengeluaran
        </p>
    </section>

    <section class="mt-6 rounded-xl border border-[#E4DED6] bg-white p-4 sm:p-5">
        <div>
            <h2 class="text-lg font-semibold">Catat pengeluaran</h2>
            <p class="mt-1 text-sm text-[#70685F]">Catat uang yang digunakan untuk kebutuhan operasional warkop.</p>
        </div>

        <form method="POST" action="{{ route('karyawan.pengeluaran.store') }}"
              class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @csrf
            <input type="hidden" name="filter_tanggal" value="{{ $date ?? '' }}">

            <div>
                <label class="block text-sm font-medium" for="tanggal_pengeluaran">Tanggal</label>
                <input id="tanggal_pengeluaran" name="tanggal_pengeluaran" type="date"
                       value="{{ old('tanggal_pengeluaran', $date ?? today()->toDateString()) }}" required
                       class="mt-2 min-h-11 w-full rounded-lg border border-[#E4DED6] px-3 text-base sm:text-sm">
                @error('tanggal_pengeluaran') <p class="mt-1 text-xs text-[#B42318]">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium" for="kategori">Kategori</label>
                <input id="kategori" name="kategori" type="text" maxlength="80" value="{{ old('kategori') }}" required
                       placeholder="Contoh: Bahan baku"
                       class="mt-2 min-h-11 w-full rounded-lg border border-[#E4DED6] px-3 text-base sm:text-sm">
                @error('kategori') <p class="mt-1 text-xs text-[#B42318]">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium" for="jumlah">Nominal (Rp)</label>
                <input id="jumlah" name="jumlah" type="number" min="1" step="1" value="{{ old('jumlah') }}" required
                       placeholder="Contoh: 25000"
                       class="mt-2 min-h-11 w-full rounded-lg border border-[#E4DED6] px-3 text-base sm:text-sm">
                @error('jumlah') <p class="mt-1 text-xs text-[#B42318]">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium" for="metode_pembayaran">Dibayar dengan</label>
                <select id="metode_pembayaran" name="metode_pembayaran" required
                        class="mt-2 min-h-11 w-full rounded-lg border border-[#E4DED6] px-3 text-base sm:text-sm">
                    <option value="tunai" @selected(old('metode_pembayaran') === 'tunai')>Tunai</option>
                    <option value="non_tunai" @selected(old('metode_pembayaran') === 'non_tunai')>Non-tunai</option>
                </select>
                @error('metode_pembayaran') <p class="mt-1 text-xs text-[#B42318]">{{ $message }}</p> @enderror
            </div>

            <div class="sm:col-span-2">
                <label class="block text-sm font-medium" for="keterangan">Keterangan</label>
                <input id="keterangan" name="keterangan" type="text" maxlength="255" value="{{ old('keterangan') }}" required
                       placeholder="Contoh: Membeli gula dan susu"
                       class="mt-2 min-h-11 w-full rounded-lg border border-[#E4DED6] px-3 text-base sm:text-sm">
                @error('keterangan') <p class="mt-1 text-xs text-[#B42318]">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-end sm:col-span-2 lg:col-span-1">
                <button type="submit" class="min-h-12 w-full rounded-lg bg-[#6F4E37] px-4 font-semibold text-white">
                    Simpan pengeluaran
                </button>
            </div>
        </form>
    </section>

    <section class="mt-6 overflow-hidden rounded-xl border border-[#E4DED6] bg-white">
        <div class="p-4 sm:p-5">
            <h2 class="text-lg font-semibold">Riwayat pemasukan</h2>
            <p class="mt-1 text-xs text-[#70685F]">Daftar transaksi penjualan yang sudah selesai.</p>
        </div>

        <div class="hidden overflow-x-auto md:block">
            <table class="w-full min-w-[680px] text-left text-sm">
                <thead class="bg-[#F1EEE8] text-[#70685F]">
                    <tr>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Kode transaksi</th>
                        <th class="px-4 py-3">Metode</th>
                        <th class="px-4 py-3 text-right">Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                        <tr class="border-t border-[#E4DED6]">
                            <td class="whitespace-nowrap px-4 py-3">{{ $transaction->tanggal_transaksi->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3">{{ $transaction->kode_transaksi }}</td>
                            <td class="px-4 py-3">{{ $transaction->metode_pembayaran === 'tunai' ? 'Tunai' : 'Non-tunai (' . strtoupper($transaction->metode_pembayaran) . ')' }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-right font-semibold">{{ $formatRupiah((int) $transaction->total) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-8 text-center text-[#70685F]">Belum ada pemasukan pada rentang ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="divide-y divide-[#E4DED6] md:hidden">
            @forelse($transactions as $transaction)
                <article class="space-y-2 p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="break-all text-sm font-semibold text-[#26221E]">{{ $transaction->kode_transaksi }}</p>
                            <p class="mt-1 text-xs text-[#70685F]">{{ $transaction->tanggal_transaksi->format('d/m/Y · H:i') }}</p>
                        </div>
                        <p class="shrink-0 text-right text-sm font-bold text-[#237A57]">
                            {{ $formatRupiah((int) $transaction->total) }}
                        </p>
                    </div>
                    <span class="inline-flex rounded-full bg-[#E8F4ED] px-2.5 py-1 text-xs font-medium text-[#237A57]">
                        {{ $transaction->metode_pembayaran === 'tunai' ? 'Tunai' : 'Non-tunai · '.strtoupper($transaction->metode_pembayaran) }}
                    </span>
                </article>
            @empty
                <p class="px-4 py-8 text-center text-sm text-[#70685F]">Belum ada pemasukan pada rentang ini.</p>
            @endforelse
        </div>

        <div class="border-t border-[#E4DED6] p-3 sm:p-4">{{ $transactions->links() }}</div>
    </section>

    <section class="mt-6 overflow-hidden rounded-xl border border-[#E4DED6] bg-white">
        <div class="p-4 sm:p-5">
            <h2 class="text-lg font-semibold">Riwayat pengeluaran</h2>
            <p class="mt-1 text-xs text-[#70685F]">Catatan uang yang digunakan untuk kebutuhan warkop.</p>
        </div>

        <div class="hidden overflow-x-auto md:block">
            <table class="w-full min-w-[720px] text-left text-sm">
                <thead class="bg-[#F1EEE8] text-[#70685F]">
                    <tr>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Kategori / keterangan</th>
                        <th class="px-4 py-3">Metode</th>
                        <th class="px-4 py-3 text-right">Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $expense)
                        <tr class="border-t border-[#E4DED6]">
                            <td class="whitespace-nowrap px-4 py-3">{{ $expense->tanggal_pengeluaran->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">
                                <p class="font-medium">{{ $expense->kategori }}</p>
                                <p class="mt-1 text-[#70685F]">{{ $expense->keterangan }}</p>
                            </td>
                            <td class="px-4 py-3">{{ $expense->metode_pembayaran === 'tunai' ? 'Tunai' : 'Non-tunai' }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-right font-semibold">{{ $formatRupiah((int) $expense->jumlah) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-8 text-center text-[#70685F]">Belum ada pengeluaran pada rentang ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="divide-y divide-[#E4DED6] md:hidden">
            @forelse($expenses as $expense)
                <article class="space-y-2 p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-[#26221E]">{{ $expense->kategori }}</p>
                            <p class="mt-1 break-words text-xs text-[#70685F]">{{ $expense->keterangan }}</p>
                            <p class="mt-1 text-xs text-[#70685F]">{{ $expense->tanggal_pengeluaran->format('d/m/Y') }}</p>
                        </div>
                        <p class="shrink-0 text-right text-sm font-bold text-[#B42318]">
                            {{ $formatRupiah((int) $expense->jumlah) }}
                        </p>
                    </div>
                    <span class="inline-flex rounded-full bg-[#F1EEE8] px-2.5 py-1 text-xs font-medium text-[#70685F]">
                        {{ $expense->metode_pembayaran === 'tunai' ? 'Tunai' : 'Non-tunai' }}
                    </span>
                </article>
            @empty
                <p class="px-4 py-8 text-center text-sm text-[#70685F]">Belum ada pengeluaran pada rentang ini.</p>
            @endforelse
        </div>

        <div class="border-t border-[#E4DED6] p-3 sm:p-4">{{ $expenses->links() }}</div>
    </section>

    <section class="mt-6 rounded-xl border border-[#E4DED6] bg-white p-4 sm:p-5">
        <div>
            <h2 class="text-lg font-semibold">Setoran harian</h2>
            <p class="mt-1 text-sm text-[#70685F]">Cocokkan uang yang ada sebelum mengirim setoran ke owner.</p>
        </div>

        @if($date)
            <dl class="mt-4 grid grid-cols-2 gap-3 text-sm sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-lg bg-[#F7F5F1] p-3">
                    <dt class="text-xs leading-5 text-[#70685F]">Setoran tunai diharapkan</dt>
                    <dd class="mt-1 break-words font-semibold">{{ $formatRupiah($summary['deposit_cash']) }}</dd>
                </div>
                <div class="rounded-lg bg-[#F7F5F1] p-3">
                    <dt class="text-xs leading-5 text-[#70685F]">Setoran non-tunai diharapkan</dt>
                    <dd class="mt-1 break-words font-semibold">{{ $formatRupiah($summary['deposit_non_cash']) }}</dd>
                </div>
                <div class="rounded-lg bg-[#F7F5F1] p-3">
                    <dt class="text-xs leading-5 text-[#70685F]">Selisih tunai</dt>
                    <dd class="mt-1 break-words font-semibold">{{ $handover ? $formatRupiah($handover->selisih_tunai) : '-' }}</dd>
                </div>
                <div class="rounded-lg bg-[#F7F5F1] p-3">
                    <dt class="text-xs leading-5 text-[#70685F]">Selisih non-tunai</dt>
                    <dd class="mt-1 break-words font-semibold">{{ $handover ? $formatRupiah($handover->selisih_non_tunai) : '-' }}</dd>
                </div>
                <div class="col-span-2 sm:col-span-1">
                    <dt class="text-xs text-[#70685F]">Status setoran</dt>
                    <dd class="mt-1 font-semibold">{{ $handover->status ?? 'Belum dikirim' }}</dd>
                </div>
            </dl>

            <form method="POST" action="{{ route('karyawan.serah-terima-kas.store') }}"
                  class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @csrf
                <input type="hidden" name="tanggal_serah" value="{{ $date }}">

                <div>
                    <label class="block text-sm font-medium" for="uang_tunai_aktual">Uang tunai aktual</label>
                    <input id="uang_tunai_aktual" name="uang_tunai_aktual" type="number" min="0"
                           value="{{ old('uang_tunai_aktual', $handover->tunai_aktual ?? '') }}" required
                           class="mt-2 min-h-11 w-full rounded-lg border border-[#E4DED6] px-3 text-base sm:text-sm">
                    @error('uang_tunai_aktual') <p class="mt-1 text-xs text-[#B42318]">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium" for="uang_non_tunai_aktual">Uang non-tunai aktual</label>
                    <input id="uang_non_tunai_aktual" name="uang_non_tunai_aktual" type="number" min="0"
                           value="{{ old('uang_non_tunai_aktual', $handover->non_tunai_aktual ?? '') }}" required
                           class="mt-2 min-h-11 w-full rounded-lg border border-[#E4DED6] px-3 text-base sm:text-sm">
                    @error('uang_non_tunai_aktual') <p class="mt-1 text-xs text-[#B42318]">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-end sm:col-span-2 lg:col-span-1">
                    <button type="submit" class="min-h-12 w-full rounded-lg bg-[#6F4E37] px-4 font-semibold text-white">
                        Kirim setoran
                    </button>
                </div>
            </form>
        @else
            <p class="mt-3 rounded-lg bg-[#F7F5F1] p-3 text-sm leading-6 text-[#70685F]">
                Ringkasan di atas mencakup semua hari. Pilih satu tanggal untuk melihat dan mengirim setoran tunai serta non-tunai hari tersebut.
            </p>
        @endif
    </section>
</div>
@endsection
