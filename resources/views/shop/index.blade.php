@extends('layouts.app')
@section('title', 'Toko')

@section('content')

{{-- Hero Banner (Deep Navy Base + Gold Accent Gradient) --}}
<div class="relative bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-950 rounded-3xl overflow-hidden mb-8 p-8 md:p-12 border border-amber-500/10 shadow-xl">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-4 right-4 w-40 h-40 bg-white rounded-full blur-3xl"></div>
        <div class="absolute bottom-4 left-4 w-32 h-32 bg-white rounded-full blur-2xl"></div>
    </div>
    <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-6">
        <div>
            <p class="text-amber-400 text-sm font-semibold mb-2 uppercase tracking-wider">🌟 Produk UMKM Pilihan</p>
            <h1 class="font-display font-bold text-white text-3xl md:text-4xl leading-tight mb-3">
                Belanja Produk Lokal,<br>Kirim ke Seluruh Indonesia
            </h1>
            <p class="text-slate-300 text-sm">Gratis ongkos kirim untuk pembelian pertama • JNE • POS • TIKI</p>
        </div>
        <div class="flex-shrink-0">
            <div class="bg-white/5 border border-white/10 backdrop-blur-sm rounded-2xl p-4 text-center text-white min-w-[120px]">
                <p class="text-3xl font-black text-amber-400">{{ \App\Models\Product::where('stock', '>', 0)->count() }}</p>
                <p class="text-xs text-slate-300 mt-1">Produk Tersedia</p>
            </div>
        </div>
    </div>
</div>

{{-- Search & Filter --}}
<div class="flex flex-col sm:flex-row gap-3 mb-6">
    <form method="GET" action="{{ route('home') }}" class="flex gap-2 flex-1">
        <div class="relative flex-1">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Cari produk UMKM..."
                class="w-full pl-10 pr-4 py-3 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-amber-500 focus:border-transparent outline-none shadow-sm">
        </div>
        <button type="submit" class="bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950 px-6 py-3 rounded-xl font-bold text-sm hover:shadow-lg hover:shadow-amber-500/20 active:scale-95 transition-all">
            Cari
        </button>
    </form>
</div>

{{-- Products Grid --}}
@if($products->isEmpty())
    <div class="text-center py-24 bg-white rounded-3xl border border-gray-100 shadow-sm">
        <div class="w-20 h-20 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-amber-100">
            <span class="text-4xl">📦</span>
        </div>
        <h3 class="text-gray-800 font-semibold text-lg mb-1">Produk tidak ditemukan</h3>
        <p class="text-gray-400 text-sm">Coba kata kunci lain atau lihat semua produk</p>
        <a href="{{ route('home') }}" class="mt-4 inline-block text-amber-600 hover:underline text-sm font-semibold">Lihat semua produk →</a>
    </div>
@else
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
        @foreach($products as $product)
        <div class="bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group">
            <a href="{{ route('shop.show', $product) }}" class="block relative overflow-hidden bg-gray-50">
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                    class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-500">
                @if($product->stock < 10 && $product->stock > 0)
                    <span class="absolute top-2 left-2 bg-rose-500 text-white text-[10px] px-2 py-0.5 rounded-full font-bold uppercase tracking-wider shadow-sm">Stok Terbatas</span>
                @endif
            </a>
            <div class="p-4">
                <a href="{{ route('shop.show', $product) }}" class="block">
                    <h3 class="font-semibold text-gray-800 text-sm leading-tight hover:text-amber-600 transition-colors line-clamp-2 mb-1 min-h-[2.5rem]">{{ $product->name }}</h3>
                </a>
                <p class="font-extrabold text-amber-600 text-base">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                <div class="flex items-center justify-between mt-1 mb-3">
                    <span class="text-xs text-gray-400">Stok: {{ $product->stock }}</span>
                    <span class="text-xs text-gray-400">⚖️ {{ $product->weight }}g</span>
                </div>

                @auth
                    @if(!auth()->user()->isAdmin())
                        <form method="POST" action="{{ route('cart.add', $product) }}">
                            @csrf
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit"
                                class="w-full bg-amber-50 text-amber-700 border border-amber-200 text-sm py-2 rounded-xl font-bold hover:bg-slate-900 hover:text-amber-400 hover:border-slate-900 transition-all active:scale-95">
                                + Keranjang
                            </button>
                        </form>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="block text-center bg-gray-50 text-gray-500 border border-gray-200 text-sm py-2 rounded-xl hover:bg-gray-100 transition-colors font-medium">
                        Login untuk beli
                    </a>
                @endauth
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    <div class="mt-8 flex justify-center">
        {{ $products->links() }}
    </div>
@endif

@endsection