@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-[1440px] px-4 py-6 md:px-5 lg:px-6">
    {{-- Header Halaman --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-[#26221E]">Manajemen Stok</h1>
            <p class="mt-1 text-sm text-[#70685F]">
                Pantau ketersediaan barang jualan dan bahan baku Warkop Mas Koko secara real-time.
            </p>
        </div>

        <button
            type="button"
            onclick="document.getElementById('modal-tambah-barang').classList.remove('hidden')"
            class="inline-flex h-10 items-center justify-center rounded-lg bg-[#6F4E37] px-4 text-sm font-semibold text-white transition hover:bg-[#805B42]"
        >
            + Tambah Barang
        </button>
    </div>

    {{-- Notifikasi Sukses / Error --}}
    @if (session('success'))
        <div class="mb-6 rounded-xl border border-[#237A57]/20 bg-[#E8F4ED] px-4 py-3 text-sm font-medium text-[#237A57]">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 rounded-xl border border-[#B42318]/20 bg-[#FDECEC] px-4 py-3 text-sm text-[#B42318]">
            <p class="font-semibold">Periksa kembali input Anda:</p>
            <ul class="mt-1 list-inside list-disc text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- KPI Ringkasan Stok --}}
    <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="rounded-xl border border-[#E4DED6] bg-white p-5">
            <p class="text-sm font-medium text-[#70685F]">Total Barang Aktif</p>
            <p class="mt-2 text-2xl font-bold text-[#26221E]">{{ $summary['total'] }}</p>
            <p class="mt-1 text-xs text-[#70685F]">Menu jualan & bahan baku</p>
        </div>
        <div class="rounded-xl border border-[#E4DED6] bg-white p-5">
            <p class="text-sm font-medium text-[#70685F]">Stok Normal</p>
            <p class="mt-2 text-2xl font-bold text-[#237A57]">{{ $summary['normal'] }}</p>
            <p class="mt-1 text-xs text-[#70685F]">Di atas batas minimum</p>
        </div>
        <a href="{{ route('stok.index', ['status_stok' => 'menipis']) }}"
           class="block rounded-xl border border-[#E4DED6] bg-white p-5 transition hover:border-[#A86412]">
            <p class="text-sm font-medium text-[#70685F]">Stok Menipis</p>
            <p class="mt-2 text-2xl font-bold text-[#A86412]">{{ $summary['menipis'] }}</p>
            <p class="mt-1 text-xs text-[#A86412]">Stok &le; batas minimum</p>
        </a>
        <a href="{{ route('stok.index', ['status_stok' => 'habis']) }}"
           class="block rounded-xl border border-[#E4DED6] bg-white p-5 transition hover:border-[#B42318]">
            <p class="text-sm font-medium text-[#70685F]">Stok Habis</p>
            <p class="mt-2 text-2xl font-bold text-[#B42318]">{{ $summary['habis'] }}</p>
            <p class="mt-1 text-xs text-[#B42318]">Perlu restok segera</p>
        </a>
    </div>

    {{-- Filter & Pencarian --}}
    <div class="mb-6 rounded-xl border border-[#E4DED6] bg-white p-5">
        <form method="GET" action="{{ route('stok.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-12">
            <div class="md:col-span-5">
                <label for="search" class="mb-1.5 block text-sm font-medium text-[#26221E]">Cari Barang</label>
                <input
                    id="search"
                    name="search"
                    type="text"
                    value="{{ $filters['search'] ?? '' }}"
                    placeholder="Cari nama barang..."
                    class="min-h-10 w-full rounded-lg border border-[#E4DED6] bg-white px-3 text-sm text-[#26221E] placeholder-[#A8A29B] focus:border-[#6F4E37] focus:outline-none focus:ring-2 focus:ring-[#6F4E37]/20"
                >
            </div>

            <div class="md:col-span-3">
                <label for="kategori" class="mb-1.5 block text-sm font-medium text-[#26221E]">Kategori</label>
                <select
                    id="kategori"
                    name="kategori"
                    class="min-h-10 w-full rounded-lg border border-[#E4DED6] bg-white px-3 text-sm text-[#26221E] focus:border-[#6F4E37] focus:outline-none focus:ring-2 focus:ring-[#6F4E37]/20"
                >
                    <option value="semua">Semua Kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category }}" @selected(($filters['kategori'] ?? '') === $category)>
                            {{ $category }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <label for="status_stok" class="mb-1.5 block text-sm font-medium text-[#26221E]">Status Stok</label>
                <select
                    id="status_stok"
                    name="status_stok"
                    class="min-h-10 w-full rounded-lg border border-[#E4DED6] bg-white px-3 text-sm text-[#26221E] focus:border-[#6F4E37] focus:outline-none focus:ring-2 focus:ring-[#6F4E37]/20"
                >
                    <option value="semua">Semua Status</option>
                    <option value="normal" @selected(($filters['status_stok'] ?? '') === 'normal')>Normal</option>
                    <option value="menipis" @selected(($filters['status_stok'] ?? '') === 'menipis')>Menipis</option>
                    <option value="habis" @selected(($filters['status_stok'] ?? '') === 'habis')>Habis</option>
                </select>
            </div>

            <div class="flex items-end gap-2 md:col-span-2">
                <button
                    type="submit"
                    class="inline-flex h-10 flex-1 items-center justify-center rounded-lg bg-[#6F4E37] px-4 text-sm font-semibold text-white transition hover:bg-[#805B42]"
                >
                    Filter
                </button>
                @if (request()->hasAny(['search', 'kategori', 'status_stok']))
                    <a
                        href="{{ route('stok.index') }}"
                        class="inline-flex h-10 items-center justify-center rounded-lg border border-[#E4DED6] bg-white px-3 text-sm font-medium text-[#70685F] hover:bg-[#F1EEE8]"
                    >
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Tabel Daftar Stok --}}
    <div class="overflow-hidden rounded-xl border border-[#E4DED6] bg-white">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left">
                <thead>
                    <tr class="border-b border-[#E4DED6] bg-[#F1EEE8] text-xs font-semibold uppercase tracking-wide text-[#70685F]">
                        <th class="px-4 py-3.5">Nama Barang</th>
                        <th class="px-4 py-3.5">Kategori</th>
                        <th class="px-4 py-3.5 text-right">Harga Jual</th>
                        <th class="px-4 py-3.5 text-right">Stok Saat Ini</th>
                        <th class="px-4 py-3.5 text-right">Minimum</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-4 py-3.5">Pembaruan Terakhir</th>
                        <th class="px-4 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E4DED6] text-sm text-[#26221E]">
                    @forelse ($items as $item)
                        <tr class="min-h-12 transition hover:bg-[#F7F5F1]/70 {{ ! $item->is_active ? 'opacity-65' : '' }}">
                            <td class="px-4 py-3.5">
                                <p class="font-semibold text-[#26221E]">{{ $item->nama }}</p>
                                <p class="text-xs text-[#70685F]">
                                    {{ $item->is_dijual ? 'Dijual di kasir' : 'Bahan baku (pantau stok)' }}
                                </p>
                            </td>
                            <td class="px-4 py-3.5 text-[#70685F]">{{ $item->kategori }}</td>
                            <td class="px-4 py-3.5 text-right font-medium">
                                {{ $item->is_dijual ? 'Rp '.number_format($item->harga_jual, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-4 py-3.5 text-right font-semibold">
                                {{ rtrim(rtrim(number_format((float) $item->stok, 3, ',', '.'), '0'), ',') }}
                                <span class="font-normal text-[#70685F]">{{ $item->satuan }}</span>
                            </td>
                            <td class="px-4 py-3.5 text-right text-[#70685F]">
                                {{ rtrim(rtrim(number_format((float) $item->stok_minimum, 3, ',', '.'), '0'), ',') }}
                                {{ $item->satuan }}
                            </td>
                            <td class="px-4 py-3.5">
                                @include('stok.partials.stock-status', ['item' => $item])
                            </td>
                            <td class="px-4 py-3.5 text-xs text-[#70685F]">
                                @if ($item->stok_diperbarui_pada)
                                    <p>{{ $item->stok_diperbarui_pada->format('d/m/Y H:i') }}</p>
                                    <p class="text-[#70685F]">
                                        {{ $item->stokDiperbaruiOleh?->name ?? 'Sistem' }}
                                        @if ($item->alasan_perubahan_stok)
                                            &middot; {{ $item->alasan_perubahan_stok }}
                                        @endif
                                    </p>
                                @else
                                    <p>{{ $item->updated_at?->format('d/m/Y H:i') ?? '-' }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                <div class="inline-flex items-center justify-end gap-2">
                                    <a
                                        href="{{ route('stok.edit', $item) }}"
                                        class="inline-flex h-8 items-center rounded-lg border border-[#E4DED6] bg-white px-3 text-xs font-semibold text-[#6F4E37] transition hover:bg-[#EFE7DF]"
                                    >
                                        Kelola
                                    </a>

                                    <form method="POST" action="{{ route('stok.toggle-active', $item) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button
                                            type="submit"
                                            class="inline-flex h-8 items-center rounded-lg px-2.5 text-xs font-semibold {{ $item->is_active ? 'text-[#B42318] hover:bg-[#FDECEC]' : 'text-[#237A57] hover:bg-[#E8F4ED]' }}"
                                        >
                                            {{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center">
                                <p class="text-sm font-semibold text-[#26221E]">Barang tidak ditemukan</p>
                                <p class="mt-1 text-xs text-[#70685F]">
                                    Coba ubah kata kunci pencarian atau reset filter yang sedang aktif.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($items->hasPages())
            <div class="border-t border-[#E4DED6] bg-[#F7F5F1] px-4 py-3">
                {{ $items->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Modal Tambah Barang --}}
<div id="modal-tambah-barang" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 p-4 flex">
        <div class="w-full max-w-lg rounded-xl border border-[#E4DED6] bg-white p-6">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-[#26221E]">Tambah Barang Baru</h2>
                <button
                    type="button"
                    onclick="document.getElementById('modal-tambah-barang').classList.add('hidden')"
                    class="text-sm font-medium text-[#70685F] hover:text-[#26221E]"
                >
                    Tutup
                </button>
            </div>

            <form method="POST" action="{{ route('stok.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="create_nama" class="mb-1.5 block text-sm font-medium text-[#26221E]">Nama Barang</label>
                    <input id="create_nama" name="nama" type="text" required value="{{ old('nama') }}"
                           placeholder="Contoh: Milo Renteng, Ayam Potong"
                           class="min-h-10 w-full rounded-lg border border-[#E4DED6] bg-white px-3 text-sm text-[#26221E] focus:border-[#6F4E37] focus:outline-none">
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="create_kategori" class="mb-1.5 block text-sm font-medium text-[#26221E]">Kategori</label>
                        <select id="create_kategori" name="kategori" required
                                class="min-h-10 w-full rounded-lg border border-[#E4DED6] bg-white px-3 text-sm text-[#26221E] focus:border-[#6F4E37] focus:outline-none">
                            @foreach ($categories as $category)
                                <option value="{{ $category }}">{{ $category }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="create_harga_jual" class="mb-1.5 block text-sm font-medium text-[#26221E]">Harga Jual (Rp)</label>
                        <input id="create_harga_jual" name="harga_jual" type="number" min="0" value="{{ old('harga_jual', 0) }}"
                               class="min-h-10 w-full rounded-lg border border-[#E4DED6] bg-white px-3 text-sm text-[#26221E] focus:border-[#6F4E37] focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <label for="create_stok" class="mb-1.5 block text-sm font-medium text-[#26221E]">Stok Awal</label>
                        <input id="create_stok" name="stok" type="number" step="any" min="0" required value="{{ old('stok', 0) }}"
                               class="min-h-10 w-full rounded-lg border border-[#E4DED6] bg-white px-3 text-sm text-[#26221E] focus:border-[#6F4E37] focus:outline-none">
                    </div>
                    <div>
                        <label for="create_satuan" class="mb-1.5 block text-sm font-medium text-[#26221E]">Satuan</label>
                        <input id="create_satuan" name="satuan" type="text" required value="{{ old('satuan', 'pcs') }}"
                               placeholder="pcs / gelas / kg"
                               class="min-h-10 w-full rounded-lg border border-[#E4DED6] bg-white px-3 text-sm text-[#26221E] focus:border-[#6F4E37] focus:outline-none">
                    </div>
                    <div>
                        <label for="create_stok_minimum" class="mb-1.5 block text-sm font-medium text-[#26221E]">Stok Minimum</label>
                        <input id="create_stok_minimum" name="stok_minimum" type="number" step="any" min="0" required value="{{ old('stok_minimum', 5) }}"
                               class="min-h-10 w-full rounded-lg border border-[#E4DED6] bg-white px-3 text-sm text-[#26221E] focus:border-[#6F4E37] focus:outline-none">
                    </div>
                </div>

                <div class="space-y-2 pt-1">
                    <label class="flex items-center gap-2 text-sm text-[#26221E]">
                        <input type="checkbox" name="is_dijual" value="1" checked class="rounded border-[#E4DED6] text-[#6F4E37]">
                        Tampilkan di halaman kasir (barang dijual langsung)
                    </label>
                    <label class="flex items-center gap-2 text-sm text-[#26221E]">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded border-[#E4DED6] text-[#6F4E37]">
                        Status barang aktif
                    </label>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button
                        type="button"
                        onclick="document.getElementById('modal-tambah-barang').classList.add('hidden')"
                        class="inline-flex h-10 items-center rounded-lg border border-[#E4DED6] bg-white px-4 text-sm font-semibold text-[#26221E] hover:bg-[#F1EEE8]"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="inline-flex h-10 items-center rounded-lg bg-[#6F4E37] px-4 text-sm font-semibold text-white hover:bg-[#805B42]"
                    >
                        Simpan Barang
                    </button>
                </div>
            </form>
        </div>
</div>
@endsection