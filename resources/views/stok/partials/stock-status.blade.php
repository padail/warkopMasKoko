@php
    $status = $item->status_stok;
@endphp

@if (! $item->is_active)
    <span class="inline-flex items-center rounded-full bg-[#F1EEE8] px-2.5 py-0.5 text-xs font-medium text-[#70685F]">
        Nonaktif
    </span>
@elseif ($status === \App\Models\MenuBarang::STATUS_STOK_HABIS)
    <span class="inline-flex items-center gap-1.5 rounded-full bg-[#FDECEC] px-2.5 py-0.5 text-xs font-medium text-[#B42318]">
        <span class="h-1.5 w-1.5 rounded-full bg-[#B42318]"></span>
        Habis
    </span>
@elseif ($status === \App\Models\MenuBarang::STATUS_STOK_MENIPIS)
    <span class="inline-flex items-center gap-1.5 rounded-full bg-[#FFF3DC] px-2.5 py-0.5 text-xs font-medium text-[#A86412]">
        <span class="h-1.5 w-1.5 rounded-full bg-[#A86412]"></span>
        Menipis
    </span>
@else
    <span class="inline-flex items-center gap-1.5 rounded-full bg-[#E8F4ED] px-2.5 py-0.5 text-xs font-medium text-[#237A57]">
        <span class="h-1.5 w-1.5 rounded-full bg-[#237A57]"></span>
        Normal
    </span>
@endif