@extends('layouts.app')
@section('title', 'Keranjang Belanja')

@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-6">🛒 Keranjang Belanja</h1>

@if(!$cart || $cart->items->isEmpty())
    <div class="text-center py-20 bg-white rounded-2xl shadow">
        <p class="text-5xl mb-4">🛒</p>
        <p class="text-gray-500 text-lg">Keranjang kamu kosong</p>
        <a href="{{ route('home') }}" class="mt-4 inline-block bg-orange-500 text-white px-6 py-2 rounded-lg hover:bg-orange-600">
            Mulai Belanja
        </a>
    </div>
@else
    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-4">
            @foreach($cart->items as $item)
            <div class="bg-white rounded-xl shadow p-4 flex gap-4">
                <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}" class="w-20 h-20 object-cover rounded-lg">
                <div class="flex-1">
                    <p class="font-semibold text-gray-800">{{ $item->product->name }}</p>
                    <p class="text-orange-500">Rp {{ number_format($item->product->price, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-400">⚖️ {{ $item->product->weight }}g/item</p>
                    <div class="flex items-center gap-3 mt-2">
                        <form method="POST" action="{{ route('cart.update', $item) }}">
                            @csrf @method('PATCH')
                            <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock }}"
                                onchange="this.form.submit()"
                                class="w-16 border border-gray-300 rounded-lg px-2 py-1 text-center text-sm focus:ring-2 focus:ring-orange-400 outline-none">
                        </form>
                        <form method="POST" action="{{ route('cart.remove', $item) }}">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 text-sm" onclick="return confirm('Hapus item ini?')">🗑️ Hapus</button>
                        </form>
                    </div>
                </div>
                <div class="text-right">
                    <p class="font-bold text-gray-800">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                </div>
            </div>
            @endforeach
        </div>

        <div class="bg-white rounded-xl shadow p-6 h-fit">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Ringkasan</h2>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Berat</span>
                    <span>{{ $cart->total_weight }}g</span>
                </div>
                <div class="flex justify-between font-bold text-lg border-t pt-2">
                    <span>Subtotal</span>
                    <span class="text-orange-500">Rp {{ number_format($cart->subtotal, 0, ',', '.') }}</span>
                </div>
            </div>
            <a href="{{ route('cart.checkout') }}" class="block mt-4 text-center bg-orange-500 text-white py-3 rounded-lg font-semibold hover:bg-orange-600">
                Lanjut ke Checkout →
            </a>
        </div>
    </div>
@endif
@endsection