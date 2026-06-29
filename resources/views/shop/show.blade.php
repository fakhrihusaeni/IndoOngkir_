
@extends('layouts.app')
@section('title', $product->name)

@section('content')
<div class="max-w-4xl mx-auto">
    <a href="{{ route('home') }}" class="text-orange-500 hover:underline text-sm">← Kembali ke Toko</a>

    <div class="bg-white rounded-2xl shadow-md mt-4 overflow-hidden">
        <div class="md:flex">
            <div class="md:w-1/2">
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-72 md:h-full object-cover">
            </div>
            <div class="md:w-1/2 p-6">
                <h1 class="text-2xl font-bold text-gray-800">{{ $product->name }}</h1>
                <p class="text-3xl font-bold text-orange-500 mt-2">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                <div class="flex gap-4 text-sm text-gray-500 mt-2">
                    <span>📦 Stok: {{ $product->stock }}</span>
                    <span>⚖️ Berat: {{ $product->weight }}g</span>
                </div>
                <p class="text-gray-600 mt-4">{{ $product->description ?? 'Tidak ada deskripsi.' }}</p>

                @auth
                    @if(!auth()->user()->isAdmin() && $product->stock > 0)
                        <button disabled class="mt-6 w-full bg-gray-300 text-gray-500 py-2 rounded-lg cursor-not-allowed">
                            🛒 Segera Hadir (Mhs 2)
                        </button>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="block mt-6 text-center bg-orange-500 text-white py-2 rounded-lg font-semibold hover:bg-orange-600">
                        Login untuk Membeli
                    </a>
                @endauth
            </div>
        </div>
    </div>
</div>
@endsection