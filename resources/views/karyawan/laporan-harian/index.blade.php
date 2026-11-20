@extends('layouts.app')

@section('title', 'Laporan Keuangan')

@section('content')
@php
    $formatRupiah = fn (int $amount): string => ($amount < 0 ? '-Rp ' : 'Rp ')
        . number_format(abs($amount), 0, ',', '.');
@endphp

<div class="mx-auto max-w-7xl px-4 py-6 md:px-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold">Laporan Keuangan</h1>
            <p class="mt-1 text-sm text-[#70685F]">
                {{ $date ? 'Ringkasan tanggal ' . \Illuminate\Support\Carbon::parse($date)->format('d/m/Y') : 'Ringkasan semua tanggal' }}
            </p>
        </div>
        <form method="GET" action="{{ route('karyawan.laporan-harian') }}" class="flex flex-wrap items-end gap-2">
            <div>
                <label class="mb-1 block text-sm font-medium" for="tanggal">Filter tanggal</label>
                <input id="tanggal" name="tanggal" type="date" value="{{ $date ?? '' }}"
                       class="h-11 rounded-lg border border-[#E4DED6] px-3">
            </div>
            <button class="h-11 rounded-lg bg-[#6F4E37] px-4 font-semibold text-white">Tampilkan</button>
            @if($date)
                <a class="h-11 rounded-lg border border-[#E4DED6] px-4 py-2.5 text-sm" href="{{ route('karyawan.laporan-harian') }}">
                    Semua hari
                </a>
            @endif
        </form>
    </div>

    @if(session('success'))
        <p class="mt-4 rounded-lg border border-[#237A57] bg-[#E8F4ED] px-4 py-3 text-sm text-[#237A57]">{{ session('success') }}</p>
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
        <h2 class="text-lg font-semibold">Ringkasan pemasukan dan pengeluaran</h2>
        <div class="mt-3 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
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
                <article class="rounded-xl border border-[#E4DED6] bg-white p-5">
                    <p class="text-sm text-[#70685F]">{{ $label }}</p>
                    <p class="mt-2 text-xl font-bold {{ $color }}">{{ $formatRupiah((int) $amount) }}</p>
                </article>
            @endforeach
        </div>
        <p class="mt-3 text-sm text-[#70685F]">
            {{ number_format($summary['transactions_count']) }} transaksi pemasukan ·
            {{ number_format($summary['expenses_count']) }} catatan pengeluaran
        </p>
    </section>

    <section class="mt-6 rounded-xl border border-[#E4DED6] bg-white p-5">
        <h2 class="text-lg font-semibold">Catat pengeluaran</h2>
        <form method="POST" action="{{ route('karyawan.pengeluaran.store') }}" class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @csrf
            <input type="hidden" name="filter_tanggal" value="{{ $date ?? '' }}">
            <div>
                <label class="block text-sm font-medium" for="tanggal_pengeluaran">Tanggal</label>
                <input id="tanggal_pengeluaran" name="tanggal_pengeluaran" type="date"
                       value="{{ old('tanggal_pengeluaran', $date ?? today()->toDateString()) }}" required
                       class="mt-2 h-11 w-full rounded-lg border border-[#E4DED6] px-3">
                @error('tanggal_pengeluaran') <p class="mt-1 text-sm text-[#B42318]">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium" for="kategori">Kategori</label>
                <input id="kategori" name="kategori" type="text" maxlength="80" value="{{ old('kategori') }}" required
                       placeholder="Contoh: Bahan baku"
                       class="mt-2 h-11 w-full rounded-lg border border-[#E4DED6] px-3">
                @error('kategori') <p class="mt-1 text-sm text-[#B42318]">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium" for="jumlah">Nominal</label>
                <input id="jumlah" name="jumlah" type="number" min="1" step="1" value="{{ old('jumlah') }}" required
                       placeholder="Contoh: 25000"
                       class="mt-2 h-11 w-full rounded-lg border border-[#E4DED6] px-3">
                @error('jumlah') <p class="mt-1 text-sm text-[#B42318]">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium" for="metode_pembayaran">Dibayar dengan</label>
                <select id="metode_pembayaran" name="metode_pembayaran" required
                        class="mt-2 h-11 w-full rounded-lg border border-[#E4DED6] px-3">
                    <option value="tunai" @selected(old('metode_pembayaran') === 'tunai')>Tunai</option>
                    <option value="non_tunai" @selected(old('metode_pembayaran') === 'non_tunai')>Non-tunai</option>
                </select>
                @error('metode_pembayaran') <p class="mt-1 text-sm text-[#B42318]">{{ $message }}</p> @enderror
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium" for="keterangan">Keterangan</label>
                <input id="keterangan" name="keterangan" type="text" maxlength="255" value="{{ old('keterangan') }}" required
                       placeholder="Contoh: Membeli gula dan susu"
                       class="mt-2 h-11 w-full rounded-lg border border-[#E4DED6] px-3">
                @error('keterangan') <p class="mt-1 text-sm text-[#B42318]">{{ $message }}</p> @enderror
            </div>
            <div class="flex items-end">
                <button class="h-11 w-full rounded-lg bg-[#6F4E37] px-4 font-semibold text-white">Simpan pengeluaran</button>
            </div>
        </form>
    </section>

    <section class="mt-6 overflow-x-auto rounded-xl border border-[#E4DED6] bg-white">
        <div class="p-5">
            <h2 class="text-lg font-semibold">Riwayat pemasukan</h2>
        </div>
        <table class="w-full text-left text-sm">
            <thead class="bg-[#F1EEE8] text-[#70685F]">
                <tr>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Kode transaksi</th>
                    <th class="px-4 py-3">Metode</th>
                    <th class="px-4 py-3">Nominal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $transaction)
                    <tr class="border-t border-[#E4DED6]">
                        <td class="whitespace-nowrap px-4 py-3">{{ $transaction->tanggal_transaksi->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3">{{ $transaction->kode_transaksi }}</td>
                        <td class="px-4 py-3">{{ $transaction->metode_pembayaran === 'tunai' ? 'Tunai' : 'Non-tunai (' . strtoupper($transaction->metode_pembayaran) . ')' }}</td>
                        <td class="whitespace-nowrap px-4 py-3 font-medium">{{ $formatRupiah((int) $transaction->total) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-8 text-center text-[#70685F]">Belum ada pemasukan pada rentang ini.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">{{ $transactions->links() }}</div>
    </section>

    <section class="mt-6 overflow-x-auto rounded-xl border border-[#E4DED6] bg-white">
        <div class="p-5">
            <h2 class="text-lg font-semibold">Riwayat pengeluaran</h2>
        </div>
        <table class="w-full text-left text-sm">
            <thead class="bg-[#F1EEE8] text-[#70685F]">
                <tr>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Kategori / keterangan</th>
                    <th class="px-4 py-3">Metode</th>
                    <th class="px-4 py-3">Nominal</th>
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
                        <td class="whitespace-nowrap px-4 py-3 font-medium">{{ $formatRupiah((int) $expense->jumlah) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-8 text-center text-[#70685F]">Belum ada pengeluaran pada rentang ini.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">{{ $expenses->links() }}</div>
    </section>

    <section class="mt-6 rounded-xl border border-[#E4DED6] bg-white p-5">
        <h2 class="text-lg font-semibold">Setoran harian</h2>
        @if($date)
            <dl class="mt-4 grid gap-3 text-sm sm:grid-cols-2 lg:grid-cols-4">
                <div><dt class="text-[#70685F]">Setoran tunai diharapkan</dt><dd class="mt-1 font-semibold">{{ $formatRupiah($summary['deposit_cash']) }}</dd></div>
                <div><dt class="text-[#70685F]">Setoran non-tunai diharapkan</dt><dd class="mt-1 font-semibold">{{ $formatRupiah($summary['deposit_non_cash']) }}</dd></div>
                <div><dt class="text-[#70685F]">Selisih tunai</dt><dd class="mt-1 font-semibold">{{ $handover ? $formatRupiah($handover->selisih_tunai) : '-' }}</dd></div>
                <div><dt class="text-[#70685F]">Selisih non-tunai</dt><dd class="mt-1 font-semibold">{{ $handover ? $formatRupiah($handover->selisih_non_tunai) : '-' }}</dd></div>
                <div><dt class="text-[#70685F]">Status</dt><dd class="mt-1 font-semibold">{{ $handover->status ?? 'Belum dikirim' }}</dd></div>
            </dl>
            <form method="POST" action="{{ route('karyawan.serah-terima-kas.store') }}" class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @csrf
                <input type="hidden" name="tanggal_serah" value="{{ $date }}">
                <div>
                    <label class="block text-sm font-medium" for="uang_tunai_aktual">Uang tunai aktual</label>
                    <input id="uang_tunai_aktual" name="uang_tunai_aktual" type="number" min="0"
                           value="{{ old('uang_tunai_aktual', $handover->tunai_aktual ?? '') }}" required
                           class="mt-2 h-11 w-full rounded-lg border border-[#E4DED6] px-3">
                    @error('uang_tunai_aktual') <p class="mt-1 text-sm text-[#B42318]">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium" for="uang_non_tunai_aktual">Uang non-tunai aktual</label>
                    <input id="uang_non_tunai_aktual" name="uang_non_tunai_aktual" type="number" min="0"
                           value="{{ old('uang_non_tunai_aktual', $handover->non_tunai_aktual ?? '') }}" required
                           class="mt-2 h-11 w-full rounded-lg border border-[#E4DED6] px-3">
                    @error('uang_non_tunai_aktual') <p class="mt-1 text-sm text-[#B42318]">{{ $message }}</p> @enderror
                </div>
                <div class="flex items-end">
                    <button class="h-11 w-full rounded-lg bg-[#6F4E37] px-4 font-semibold text-white">Kirim setoran</button>
                </div>
            </form>
        @else
            <p class="mt-2 text-sm text-[#70685F]">
                Ringkasan di atas mencakup semua hari. Pilih satu tanggal untuk melihat dan mengirim setoran tunai serta non-tunai hari tersebut.
            </p>
        @endif
    </section>
</div>
@endsection
