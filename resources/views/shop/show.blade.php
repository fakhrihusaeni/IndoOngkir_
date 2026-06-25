
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
                        <form method="POST" action="{{ route('cart.add', $product) }}" class="mt-6 flex gap-3">
                            @csrf
                            <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}"
                                class="w-20 border border-gray-300 rounded-lg px-3 py-2 text-center focus:ring-2 focus:ring-orange-400 outline-none">
                            <button type="submit" class="flex-1 bg-orange-500 text-white py-2 rounded-lg font-semibold hover:bg-orange-600">
                                🛒 Tambah ke Keranjang
                            </button>
                        </form>
                    @elseif($product->stock === 0)
                        <p class="mt-6 text-red-500 font-semibold">❌ Stok Habis</p>
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