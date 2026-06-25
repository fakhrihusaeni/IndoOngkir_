@extends('layouts.app')
@section('title', 'Masuk')

@section('content')
<div class="max-w-md mx-auto mt-10">
    <div class="bg-white rounded-2xl shadow-md p-8">
        <h1 class="text-2xl font-bold text-center text-gray-800 mb-6">Masuk ke IndoOngkir</h1>

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-orange-400 focus:border-transparent outline-none">
                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-orange-400 focus:border-transparent outline-none">
            </div>
            <label class="flex items-center gap-2 text-sm text-gray-600">
                <input type="checkbox" name="remember"> Ingat saya
            </label>
            <button type="submit" class="w-full bg-orange-500 text-white py-2.5 rounded-lg font-semibold hover:bg-orange-600 transition">
                Masuk
            </button>
        </form>

        <p class="text-center text-sm text-gray-600 mt-4">
            Belum punya akun? <a href="{{ route('register') }}" class="text-orange-500 hover:underline font-medium">Daftar sekarang</a>
        </p>
    </div>
</div>
@endsection