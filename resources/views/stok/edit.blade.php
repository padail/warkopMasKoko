@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-4xl px-4 py-6 md:px-5 lg:px-6">
    <div class="mb-6 flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-[#26221E]">
                {{ auth()->user()->isAdmin() ? 'Kelola Barang & Stok' : 'Perbarui Jumlah Stok' }}
            </h1>
            <p class="mt-1 text-sm text-[#70685F]">{{ $item->nama }} &middot; {{ $item->kategori }}</p>
        </div>
        @include('stok.partials.stock-status', ['item' => $item])
    </div>

    {{-- Ringkasan Kondisi Stok Saat Ini (Read-Only) --}}
    <div class="mb-6 rounded-xl border border-[#E4DED6] bg-white p-6">
        <h2 class="mb-4 text-lg font-semibold text-[#26221E]">Informasi Barang Saat Ini</h2>
        <dl class="grid grid-cols-2 gap-4 text-sm sm:grid-cols-4">
            <div>
                <dt class="text-[#70685F]">Stok Saat Ini</dt>
                <dd class="mt-1 text-lg font-bold text-[#26221E]">
                    {{ rtrim(rtrim(number_format((float) $item->stok, 3, ',', '.'), '0'), ',') }} {{ $item->satuan }}
                </dd>
            </div>
            <div>
                <dt class="text-[#70685F]">Stok Minimum</dt>
                <dd class="mt-1 font-semibold text-[#26221E]">
                    {{ rtrim(rtrim(number_format((float) $item->stok_minimum, 3, ',', '.'), '0'), ',') }} {{ $item->satuan }}
                </dd>
            </div>
            <div>
                <dt class="text-[#70685F]">Harga Jual</dt>
                <dd class="mt-1 font-semibold text-[#26221E]">
                    {{ $item->is_dijual ? 'Rp '.number_format($item->harga_jual, 0, ',', '.') : 'Tidak dijual' }}
                </dd>
            </div>
            <div>
                <dt class="text-[#70685F]">Terakhir Diubah</dt>
                <dd class="mt-1 text-xs font-medium text-[#26221E]">
                    {{ $item->stok_diperbarui_pada?->format('d/m/Y H:i') ?? $item->updated_at?->format('d/m/Y H:i') }}
                    <span class="block text-[#70685F]">{{ $item->stokDiperbaruiOleh?->name ?? '-' }}</span>
                </dd>
            </div>
        </dl>
    </div>

    {{-- Form Pembaruan Stok Cepat (Untuk Admin dan Karyawan) --}}
    <div class="mb-6 rounded-xl border border-[#E4DED6] bg-white p-6">
        <h2 class="mb-1 text-lg font-semibold text-[#26221E]">Pembaruan Jumlah Stok</h2>
        <p class="mb-4 text-sm text-[#70685F]">
            Perubahan stok akan mencatat nama pengguna yang sedang login dan alasan pembaruan.
        </p>

        @include('stok.partials.stock-form', ['item' => $item, 'alasanOptions' => $alasanOptions])
    </div>

    {{-- Form Edit Identitas & Harga (Khusus Admin) --}}
    @if (auth()->user()->isAdmin())
        <div class="rounded-xl border border-[#E4DED6] bg-white p-6">
            <h2 class="mb-1 text-lg font-semibold text-[#26221E]">Edit Identitas & Harga Barang (Admin)</h2>
            <p class="mb-4 text-sm text-[#70685F]">
                Perubahan harga jual hanya berlaku untuk transaksi berikutnya dan tidak mengubah riwayat transaksi lama.
            </p>

            <form method="POST" action="{{ route('stok.update', $item) }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="nama" class="mb-1.5 block text-sm font-medium text-[#26221E]">Nama Barang</label>
                        <input id="nama" name="nama" type="text" required value="{{ old('nama', $item->nama) }}"
                               class="min-h-10 w-full rounded-lg border border-[#E4DED6] bg-white px-3 text-sm text-[#26221E] focus:border-[#6F4E37] focus:outline-none">
                    </div>

                    <div>
                        <label for="kategori" class="mb-1.5 block text-sm font-medium text-[#26221E]">Kategori</label>
                        <select id="kategori" name="kategori" required
                                class="min-h-10 w-full rounded-lg border border-[#E4DED6] bg-white px-3 text-sm text-[#26221E] focus:border-[#6F4E37] focus:outline-none">
                            @foreach ($categories as $category)
                                <option value="{{ $category }}" @selected(old('kategori', $item->kategori) === $category)>
                                    {{ $category }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                    <div>
                        <label for="harga_jual" class="mb-1.5 block text-sm font-medium text-[#26221E]">Harga Jual (Rp)</label>
                        <input id="harga_jual" name="harga_jual" type="number" min="0" value="{{ old('harga_jual', $item->harga_jual) }}"
                               class="min-h-10 w-full rounded-lg border border-[#E4DED6] bg-white px-3 text-sm text-[#26221E] focus:border-[#6F4E37] focus:outline-none">
                    </div>
                    <div>
                        <label for="stok" class="mb-1.5 block text-sm font-medium text-[#26221E]">Stok</label>
                        <input id="stok" name="stok" type="number" step="any" min="0" required value="{{ old('stok', (float) $item->stok) }}"
                               class="min-h-10 w-full rounded-lg border border-[#E4DED6] bg-white px-3 text-sm text-[#26221E] focus:border-[#6F4E37] focus:outline-none">
                    </div>
                    <div>
                        <label for="satuan" class="mb-1.5 block text-sm font-medium text-[#26221E]">Satuan</label>
                        <input id="satuan" name="satuan" type="text" required value="{{ old('satuan', $item->satuan) }}"
                               class="min-h-10 w-full rounded-lg border border-[#E4DED6] bg-white px-3 text-sm text-[#26221E] focus:border-[#6F4E37] focus:outline-none">
                    </div>
                    <div>
                        <label for="stok_minimum" class="mb-1.5 block text-sm font-medium text-[#26221E]">Stok Minimum</label>
                        <input id="stok_minimum" name="stok_minimum" type="number" step="any" min="0" required value="{{ old('stok_minimum', (float) $item->stok_minimum) }}"
                               class="min-h-10 w-full rounded-lg border border-[#E4DED6] bg-white px-3 text-sm text-[#26221E] focus:border-[#6F4E37] focus:outline-none">
                    </div>
                </div>

                <div class="space-y-2 pt-1">
                    <label class="flex items-center gap-2 text-sm text-[#26221E]">
                        <input type="checkbox" name="is_dijual" value="1" @checked(old('is_dijual', $item->is_dijual))
                               class="rounded border-[#E4DED6] text-[#6F4E37]">
                        Tampilkan di halaman kasir (barang dijual langsung)
                    </label>
                    <label class="flex items-center gap-2 text-sm text-[#26221E]">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $item->is_active))
                               class="rounded border-[#E4DED6] text-[#6F4E37]">
                        Status barang aktif
                    </label>
                </div>

                <div class="flex justify-end pt-2">
                    <button
                        type="submit"
                        class="inline-flex h-10 items-center rounded-lg bg-[#6F4E37] px-4 text-sm font-semibold text-white transition hover:bg-[#805B42]"
                    >
                        Simpan Perubahan Barang
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>
@endsection