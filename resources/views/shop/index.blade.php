@extends('layouts.app')
@section('title', 'Toko')

@section('content')
<div class="mb-6">
    <form method="GET" action="{{ route('home') }}" class="flex gap-2 max-w-md">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari produk..."
            class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-orange-400 outline-none">
        <button type="submit" class="bg-orange-500 text-white px-6 py-2 rounded-lg hover:bg-orange-600">🔍 Cari</button>
    </form>
</div>

@if($products->isEmpty())
    <div class="text-center py-20 text-gray-500">
        <p class="text-5xl mb-4">📦</p>
        <p class="text-xl">Produk tidak ditemukan</p>
    </div>
@else
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
        @foreach($products as $product)
        <div class="bg-white rounded-xl shadow hover:shadow-md transition overflow-hidden">
            <a href="{{ route('shop.show', $product) }}">
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                    class="w-full h-48 object-cover">
            </a>
            <div class="p-3">
                <a href="{{ route('shop.show', $product) }}" class="font-semibold text-gray-800 hover:text-orange-500 block truncate">
                    {{ $product->name }}
                </a>
                <p class="text-orange-500 font-bold mt-1">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-400">Stok: {{ $product->stock }} | {{ $product->weight }}g</p>
                @auth
                    @if(!auth()->user()->isAdmin())
                        <form method="POST" action="{{ route('cart.add', $product) }}" class="mt-2">
                            @csrf
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="w-full bg-orange-500 text-white text-sm py-1.5 rounded-lg hover:bg-orange-600">
                                + Keranjang
                            </button>
                        </form>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="block mt-2 text-center bg-gray-200 text-gray-700 text-sm py-1.5 rounded-lg hover:bg-gray-300">
                        Login untuk beli
                    </a>
                @endauth
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-6">{{ $products->links() }}</div>
@endif
@endsection