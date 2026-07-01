
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

                <form action="{{ route('cart.add', $product->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 px-4 rounded-lg shadow-md transition duration-200 flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        + Keranjang
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection