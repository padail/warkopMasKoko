<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Warkop Management System')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-dvh bg-[#F7F5F1] font-sans text-[#26221E] antialiased">
    @auth
        <header class="sticky top-0 z-30 border-b border-[#E4DED6] bg-white/95 backdrop-blur">
            <div class="mx-auto flex min-h-16 max-w-7xl items-center justify-between gap-3 px-4 py-2 md:px-6">
                <div class="min-w-0">
                    <a href="{{ auth()->user()->isKaryawan() ? route('kasir.index') : url('/admin') }}"
                       class="block truncate text-sm font-bold text-[#3B2A20] sm:text-base">
                        Warkop Mas Koko
                    </a>
                    <p class="truncate text-xs text-[#70685F]">
                        {{ auth()->user()->name }}
                        <span class="hidden sm:inline">· {{ auth()->user()->isKaryawan() ? 'Karyawan' : 'Admin' }}</span>
                    </p>
                </div>

                <nav aria-label="Navigasi utama" class="hidden items-center gap-1 md:flex">
                    <a @class([
                        'rounded-lg px-3 py-2 text-sm font-medium transition',
                        'bg-[#EFE7DF] text-[#3B2A20]' => request()->routeIs('kasir.*'),
                        'text-[#70685F] hover:bg-[#F1EEE8]' => ! request()->routeIs('kasir.*'),
                    ]) href="{{ route('kasir.index') }}" @if(request()->routeIs('kasir.*')) aria-current="page" @endif>
                        Kasir
                    </a>
                    <a @class([
                        'rounded-lg px-3 py-2 text-sm font-medium transition',
                        'bg-[#EFE7DF] text-[#3B2A20]' => request()->routeIs('stok.*'),
                        'text-[#70685F] hover:bg-[#F1EEE8]' => ! request()->routeIs('stok.*'),
                    ]) href="{{ route('stok.index') }}" @if(request()->routeIs('stok.*')) aria-current="page" @endif>
                        Stok
                    </a>
                    @if(auth()->user()->isKaryawan())
                        <a @class([
                            'rounded-lg px-3 py-2 text-sm font-medium transition',
                            'bg-[#EFE7DF] text-[#3B2A20]' => request()->routeIs('karyawan.laporan-harian'),
                            'text-[#70685F] hover:bg-[#F1EEE8]' => ! request()->routeIs('karyawan.laporan-harian'),
                        ]) href="{{ route('karyawan.laporan-harian') }}" @if(request()->routeIs('karyawan.laporan-harian')) aria-current="page" @endif>
                            Laporan
                        </a>
                    @endif
                </nav>

                <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                    @csrf
                    <button type="submit"
                            class="inline-flex min-h-10 items-center justify-center rounded-lg border border-[#E4DED6] px-3 text-xs font-semibold text-[#70685F] transition hover:bg-[#F1EEE8] sm:text-sm">
                        Keluar
                    </button>
                </form>
            </div>
        </header>
    @endauth

    <main @if(auth()->check() && auth()->user()->isKaryawan()) class="pb-28 md:pb-0" @endif>
        @yield('content')
    </main>

    @auth
        @if(auth()->user()->isKaryawan())
            <nav id="employee-mobile-nav"
                 aria-label="Navigasi utama karyawan"
                 class="safe-bottom fixed inset-x-0 bottom-0 z-40 border-t border-[#E4DED6] bg-white/95 px-2 pt-2 shadow-[0_-4px_16px_rgba(38,34,30,0.06)] backdrop-blur md:hidden">
                <div class="mx-auto grid max-w-lg grid-cols-3 gap-1">
                    <a href="{{ route('kasir.index') }}"
                       @class([
                           'flex min-h-14 flex-col items-center justify-center gap-1 rounded-xl px-2 text-xs font-semibold transition',
                           'bg-[#EFE7DF] text-[#3B2A20]' => request()->routeIs('kasir.*'),
                           'text-[#70685F] active:bg-[#F1EEE8]' => ! request()->routeIs('kasir.*'),
                       ])
                       @if(request()->routeIs('kasir.*')) aria-current="page" @endif>
                        <svg aria-hidden="true" viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 3h2l2.2 11.2a2 2 0 0 0 2 1.6h8.9a2 2 0 0 0 1.9-1.4L22 7H6"/>
                            <circle cx="10" cy="20" r="1"/>
                            <circle cx="18" cy="20" r="1"/>
                        </svg>
                        <span>Kasir</span>
                    </a>
                    <a href="{{ route('stok.index') }}"
                       @class([
                           'flex min-h-14 flex-col items-center justify-center gap-1 rounded-xl px-2 text-xs font-semibold transition',
                           'bg-[#EFE7DF] text-[#3B2A20]' => request()->routeIs('stok.*'),
                           'text-[#70685F] active:bg-[#F1EEE8]' => ! request()->routeIs('stok.*'),
                       ])
                       @if(request()->routeIs('stok.*')) aria-current="page" @endif>
                        <svg aria-hidden="true" viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m12 3 9 4.5v9L12 21l-9-4.5v-9L12 3Z"/>
                            <path d="m3.5 7.7 8.5 4.4 8.5-4.4M12 12.1V21"/>
                        </svg>
                        <span>Stok</span>
                    </a>
                    <a href="{{ route('karyawan.laporan-harian') }}"
                       @class([
                           'flex min-h-14 flex-col items-center justify-center gap-1 rounded-xl px-2 text-xs font-semibold transition',
                           'bg-[#EFE7DF] text-[#3B2A20]' => request()->routeIs('karyawan.laporan-harian'),
                           'text-[#70685F] active:bg-[#F1EEE8]' => ! request()->routeIs('karyawan.laporan-harian'),
                       ])
                       @if(request()->routeIs('karyawan.laporan-harian')) aria-current="page" @endif>
                        <svg aria-hidden="true" viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M8 3h8l4 4v14H4V3h4Z"/>
                            <path d="M8 3v5h8V3M8 13h8M8 17h5"/>
                        </svg>
                        <span>Laporan</span>
                    </a>
                </div>
            </nav>
        @endif
    @endauth
</body>
</html>
