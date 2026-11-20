<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Warkop Management System')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#F7F5F1] font-sans text-[#26221E]">
    @auth
        <header class="border-b border-[#E4DED6] bg-white">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 md:px-6">
                <div>
                    <p class="text-sm font-semibold text-[#3B2A20]">Warkop Mas Koko</p>
                    <p class="text-xs text-[#70685F]">{{ auth()->user()->name }}</p>
                </div>
                <nav class="flex items-center gap-3 text-sm">
                    <a class="rounded-lg px-3 py-2 hover:bg-[#F1EEE8]" href="{{ route('kasir.index') }}">Kasir</a>
                    @if(auth()->user()->isKaryawan())
                        <a class="rounded-lg px-3 py-2 hover:bg-[#F1EEE8]" href="{{ route('karyawan.laporan-harian') }}">Laporan</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="rounded-lg border border-[#E4DED6] px-3 py-2" type="submit">Keluar</button>
                    </form>
                </nav>
            </div>
        </header>
    @endauth
    <main>@yield('content')</main>
</body>
</html>
