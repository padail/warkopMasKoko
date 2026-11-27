@extends('layouts.app')

@section('title', 'Masuk')

@section('content')
<div class="flex min-h-[calc(100dvh-2rem)] items-center justify-center px-4 py-8">
    <div class="w-full max-w-md rounded-xl border border-[#E4DED6] bg-white p-5 shadow-sm sm:p-6">
        <h1 class="text-2xl font-bold text-[#3B2A20]">Masuk Warkop</h1>
        <p class="mt-1 text-sm text-[#70685F]">Gunakan akun owner atau karyawan.</p>

        <form class="mt-6 space-y-4" method="POST" action="{{ route('login') }}">
            @csrf
            <div>
                <label class="mb-2 block text-sm font-medium" for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required
                       autocomplete="username"
                       class="min-h-12 w-full rounded-lg border border-[#E4DED6] px-3 text-base outline-none focus:border-[#805B42] sm:text-sm">
                @error('email') <p class="mt-1 text-sm text-[#B42318]">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium" for="password">Password</label>
                <input id="password" name="password" type="password" required
                       autocomplete="current-password"
                       class="min-h-12 w-full rounded-lg border border-[#E4DED6] px-3 text-base outline-none focus:border-[#805B42] sm:text-sm">
            </div>
            <button class="min-h-12 w-full rounded-lg bg-[#6F4E37] font-semibold text-white hover:bg-[#805B42]" type="submit">
                Masuk
            </button>
        </form>
    </div>
</div>
@endsection
