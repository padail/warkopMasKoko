@extends('layouts.app')

@section('title', 'Masuk')

@section('content')
<div class="flex min-h-screen items-center justify-center px-4">
    <div class="w-full max-w-md rounded-xl border border-[#E4DED6] bg-white p-6 shadow-sm">
        <h1 class="text-2xl font-bold text-[#3B2A20]">Masuk Warkop</h1>
        <p class="mt-1 text-sm text-[#70685F]">Gunakan akun owner atau karyawan.</p>

        <form class="mt-6 space-y-4" method="POST" action="{{ route('login') }}">
            @csrf
            <div>
                <label class="mb-2 block text-sm font-medium" for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required
                       class="h-11 w-full rounded-lg border border-[#E4DED6] px-3 outline-none focus:border-[#805B42]">
                @error('email') <p class="mt-1 text-sm text-[#B42318]">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium" for="password">Password</label>
                <input id="password" name="password" type="password" required
                       class="h-11 w-full rounded-lg border border-[#E4DED6] px-3 outline-none focus:border-[#805B42]">
            </div>
            <button class="h-11 w-full rounded-lg bg-[#6F4E37] font-semibold text-white hover:bg-[#805B42]" type="submit">
                Masuk
            </button>
        </form>
    </div>
</div>
@endsection
