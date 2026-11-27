<form method="POST" action="{{ route('stok.update-stok', $item) }}" class="space-y-4">
    @csrf
    @method('PATCH')

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label for="mode" class="mb-1.5 block text-sm font-medium text-[#26221E]">
                Jenis Pembaruan
            </label>
            <select
                id="mode"
                name="mode"
                class="min-h-11 w-full rounded-lg border border-[#E4DED6] bg-white px-3 text-base sm:text-sm text-[#26221E] focus:border-[#6F4E37] focus:outline-none focus:ring-2 focus:ring-[#6F4E37]/20"
            >
                <option value="set" @selected(old('mode', 'set') === 'set')>Atur jumlah stok baru</option>
                <option value="tambah" @selected(old('mode') === 'tambah')>Tambah stok (+)</option>
                <option value="kurang" @selected(old('mode') === 'kurang')>Kurangi stok (-)</option>
            </select>
        </div>

        <div>
            <label for="nilai" class="mb-1.5 block text-sm font-medium text-[#26221E]">
                Jumlah ({{ $item->satuan }})
            </label>
            <input
                id="nilai"
                name="nilai"
                type="number"
                step="any"
                min="0"
                required
                value="{{ old('nilai', (float) $item->stok) }}"
                placeholder="Masukkan angka"
                class="min-h-11 w-full rounded-lg border border-[#E4DED6] bg-white px-3 text-base sm:text-sm text-[#26221E] placeholder-[#A8A29B] focus:border-[#6F4E37] focus:outline-none focus:ring-2 focus:ring-[#6F4E37]/20"
            >
            @error('nilai')
                <p class="mt-1 text-xs text-[#B42318]">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <label for="alasan" class="mb-1.5 block text-sm font-medium text-[#26221E]">
            Alasan Pembaruan
        </label>
        <select
            id="alasan"
            name="alasan"
            required
            class="min-h-11 w-full rounded-lg border border-[#E4DED6] bg-white px-3 text-base sm:text-sm text-[#26221E] focus:border-[#6F4E37] focus:outline-none focus:ring-2 focus:ring-[#6F4E37]/20"
        >
            <option value="">-- Pilih alasan pembaruan --</option>
            @foreach ($alasanOptions as $alasan)
                <option value="{{ $alasan }}" @selected(old('alasan') === $alasan)>
                    {{ $alasan }}
                </option>
            @endforeach
        </select>
        @error('alasan')
            <p class="mt-1 text-xs text-[#B42318]">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex flex-col-reverse gap-2 pt-2 sm:flex-row sm:flex-wrap sm:items-center sm:justify-end sm:gap-3">
        <a
            href="{{ route('stok.index') }}"
            class="inline-flex min-h-11 w-full items-center justify-center rounded-lg border border-[#E4DED6] bg-white px-4 text-sm font-semibold text-[#26221E] hover:bg-[#F1EEE8] sm:w-auto"
        >
            Kembali
        </a>
        <button
            type="submit"
            class="inline-flex min-h-12 w-full items-center justify-center rounded-lg bg-[#6F4E37] px-4 text-sm font-semibold text-white transition hover:bg-[#805B42] sm:w-auto"
        >
            Perbarui Stok
        </button>
    </div>
</form>