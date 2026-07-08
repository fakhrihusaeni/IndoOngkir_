@extends('layouts.app')
@section('title', $product->name)

@section('content')

{{-- Breadcrumb --}}
<nav class="flex items-center gap-2 text-sm text-gray-400 mb-6">
    <a href="{{ route('home') }}" class="hover:text-amber-600 transition-colors">Toko</a>
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="text-gray-700 font-medium truncate max-w-xs">{{ $product->name }}</span>
</nav>

<div class="grid lg:grid-cols-2 gap-8 mb-12">

    {{-- ═══ LEFT: Product Image ═══ --}}
    <div class="space-y-3">
        <div class="bg-white rounded-3xl overflow-hidden border border-gray-100 shadow-sm aspect-square flex items-center justify-center p-6">
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                class="w-full h-full object-contain hover:scale-105 transition-transform duration-500"
                id="mainImage">
        </div>

        {{-- Image Thumbnails --}}
        <div class="flex gap-2">
            <div class="w-16 h-16 rounded-xl border-2 border-amber-500 overflow-hidden cursor-pointer">
                <img src="{{ $product->image_url }}" class="w-full h-full object-cover">
            </div>
            <div class="w-16 h-16 rounded-xl border border-gray-200 overflow-hidden cursor-pointer opacity-50 flex items-center justify-center bg-gray-50">
                <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
        </div>
    </div>

    {{-- ═══ RIGHT: Product Info ═══ --}}
    <div class="space-y-5">

        {{-- Badge & Title --}}
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="bg-slate-900 text-amber-400 text-xs font-semibold px-3 py-1 rounded-full border border-amber-500/10">Produk UMKM</span>
                @if($product->stock < 10 && $product->stock > 0)
                    <span class="bg-rose-50 text-rose-600 text-xs font-semibold px-3 py-1 rounded-full border border-rose-100">⚡ Stok Terbatas!</span>
                @endif
            </div>
            <h1 class="font-display font-bold text-gray-900 text-2xl leading-snug">{{ $product->name }}</h1>
        </div>

        {{-- Rating --}}
        <div class="flex items-center gap-3">
            <div class="flex text-amber-500">
                @for($i = 0; $i < 5; $i++)
                    <svg class="w-4 h-4 {{ $i < 4 ? 'fill-current' : 'stroke-current fill-none' }}" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                @endfor
            </div>
            <span class="text-sm text-gray-500">4.8 <span class="text-gray-300">|</span> 128 ulasan <span class="text-gray-300">|</span> 340 terjual</span>
        </div>

        {{-- Price --}}
        <div class="bg-gradient-to-r from-slate-50 to-amber-50/40 rounded-2xl p-4 border border-amber-500/10">
            <p class="text-3xl font-extrabold text-amber-600 font-display">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">Harga belum termasuk ongkos kirim</p>
        </div>

        {{-- Info Pills --}}
        <div class="grid grid-cols-3 gap-3">
            <div class="bg-gray-50 rounded-xl p-3 text-center border border-gray-100">
                <p class="text-lg font-bold text-gray-800">{{ $product->stock }}</p>
                <p class="text-xs text-gray-400 mt-0.5">Stok</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-3 text-center border border-gray-100">
                <p class="text-lg font-bold text-gray-800">{{ $product->weight }}g</p>
                <p class="text-xs text-gray-400 mt-0.5">Berat</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-3 text-center border border-gray-100">
                <p class="text-lg font-bold text-amber-600">⚡</p>
                <p class="text-xs text-gray-400 mt-0.5">Kirim Cepat</p>
            </div>
        </div>

        {{-- Quantity & Add to Cart --}}
        @auth
            @if(!auth()->user()->isAdmin())
                @if($product->stock > 0)
                <form method="POST" action="{{ route('cart.add', $product) }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah</label>
                        <div class="flex items-center gap-3">
                            <div class="flex items-center border border-gray-200 rounded-xl overflow-hidden bg-white">
                                <button type="button" onclick="changeQty(-1)"
                                    class="px-4 py-3 bg-gray-50 hover:bg-gray-100 text-gray-600 font-bold text-lg transition-colors">−</button>
                                <input type="number" name="quantity" id="qty" value="1" min="1" max="{{ $product->stock }}"
                                    class="w-16 text-center py-3 border-0 text-sm font-semibold focus:ring-0 outline-none">
                                <button type="button" onclick="changeQty(1)"
                                    class="px-4 py-3 bg-gray-50 hover:bg-gray-100 text-gray-600 font-bold text-lg transition-colors">+</button>
                            </div>
                            <span class="text-xs text-gray-400">Maks. {{ $product->stock }} pcs</span>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <button type="submit"
                            class="flex-1 bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950 py-3.5 rounded-xl font-bold hover:shadow-lg hover:shadow-amber-500/20 active:scale-95 transition-all flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Tambah ke Keranjang
                        </button>
                        <button type="button"
                            class="w-14 h-14 border border-gray-200 rounded-xl flex items-center justify-center hover:bg-rose-50 hover:border-rose-200 transition-all group"
                            title="Simpan ke Wishlist">
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-rose-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </button>
                    </div>
                </form>
                @else
                    <div class="bg-rose-50 border border-rose-200 rounded-xl p-4 text-center">
                        <p class="text-rose-600 font-semibold">❌ Stok Habis</p>
                        <p class="text-rose-400 text-xs mt-1">Produk sedang tidak tersedia</p>
                    </div>
                @endif
            @endif
        @else
            <a href="{{ route('login') }}" class="block text-center bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950 py-3.5 rounded-xl font-bold hover:shadow-lg transition-all text-sm">
                Login untuk Membeli
            </a>
        @endauth

        {{-- Keunggulan --}}
        <div class="border border-gray-100 rounded-2xl p-4 space-y-3 bg-white">
            <div class="flex items-center gap-3 text-sm text-gray-600">
                <div class="w-8 h-8 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center flex-shrink-0">✅</div>
                <span>Produk asli 100% dari pengrajin UMKM lokal</span>
            </div>
            <div class="flex items-center gap-3 text-sm text-gray-600">
                <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center flex-shrink-0">🚚</div>
                <span>Pengiriman JNE, POS, TIKI ke seluruh Indonesia</span>
            </div>
            <div class="flex items-center gap-3 text-sm text-gray-600">
                <div class="w-8 h-8 bg-amber-50 text-amber-600 rounded-lg flex items-center justify-center flex-shrink-0">🔒</div>
                <span>Transaksi aman & terpercaya</span>
            </div>
        </div>
    </div>
</div>

{{-- ═══ PRODUCT DESCRIPTION ═══ --}}
<div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 mb-8">
    <h2 class="font-display font-bold text-gray-900 text-lg mb-4">Detail Produk</h2>

    <div class="grid md:grid-cols-2 gap-6">
        <div>
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Deskripsi</h3>
            <p class="text-gray-600 text-sm leading-relaxed">
                {{ $product->description ?? 'Produk berkualitas tinggi dari pengrajin UMKM lokal Indonesia. Dibuat dengan bahan pilihan dan penuh perhatian terhadap kualitas.' }}
            </p>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Spesifikasi</h3>
            <table class="w-full text-sm">
                <tr class="border-b border-gray-50">
                    <td class="py-2 text-gray-400 pr-4">Nama</td>
                    <td class="py-2 font-medium text-gray-700">{{ $product->name }}</td>
                </tr>
                <tr class="border-b border-gray-50">
                    <td class="py-2 text-gray-400 pr-4">Berat</td>
                    <td class="py-2 font-medium text-gray-700">{{ $product->weight }} gram</td>
                </tr>
                <tr class="border-b border-gray-50">
                    <td class="py-2 text-gray-400 pr-4">Stok</td>
                    <td class="py-2 font-medium text-gray-700">{{ $product->stock }} pcs</td>
                </tr>
                <tr>
                    <td class="py-2 text-gray-400 pr-4">Kategori</td>
                    <td class="py-2 font-medium text-gray-700">Produk UMKM</td>
                </tr>
            </table>
        </div>
    </div>
</div>

{{-- ═══ REVIEWS SECTION ═══ --}}
<div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 mb-8">
    <div class="flex items-center justify-between mb-6">
        <h2 class="font-display font-bold text-gray-900 text-lg">Ulasan Pembeli</h2>
        <span class="text-sm text-gray-400">128 ulasan</span>
    </div>

    {{-- Rating Summary --}}
    <div class="flex gap-8 mb-6 p-4 bg-slate-900 rounded-2xl border border-amber-500/10 shadow-inner">
        <div class="text-center flex flex-col justify-center px-2">
            <p class="text-5xl font-black text-amber-400 font-display">4.8</p>
            <div class="flex text-amber-400 justify-center my-1">
                @for($i = 0; $i < 5; $i++)
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                @endfor
            </div>
            <p class="text-xs text-slate-400">dari 5</p>
        </div>
        <div class="flex-1 space-y-1.5">
            @foreach([5 => 75, 4 => 35, 3 => 12, 2 => 4, 1 => 2] as $star => $count)
            <div class="flex items-center gap-2">
                <span class="text-xs text-slate-400 w-4">{{ $star }}</span>
                <svg class="w-3 h-3 text-amber-400 fill-current flex-shrink-0" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                <div class="flex-1 bg-slate-800 rounded-full h-1.5">
                    <div class="bg-amber-400 h-1.5 rounded-full" style="width: {{ ($count/128)*100 }}%"></div>
                </div>
                <span class="text-xs text-slate-400 w-6">{{ $count }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Review Cards --}}
    <div class="space-y-4">
        @php
        $reviews = [
            ['name' => 'Budi S.', 'rating' => 5, 'date' => '2 hari lalu', 'comment' => 'Produk sangat bagus, sesuai deskripsi! Pengiriman cepat dan packaging rapi. Recommended banget!', 'avatar' => 'B'],
            ['name' => 'Siti A.', 'rating' => 5, 'date' => '1 minggu lalu', 'comment' => 'Kualitas premium dengan harga yang terjangkau. Penjual responsif dan ramah. Pasti beli lagi!', 'avatar' => 'S'],
            ['name' => 'Ahmad R.', 'rating' => 4, 'date' => '2 minggu lalu', 'comment' => 'Produk bagus, hanya perlu waktu sedikit lebih lama untuk sampai. Overall memuaskan!', 'avatar' => 'A'],
        ];
        @endphp

        @foreach($reviews as $review)
        <div class="border border-gray-100 rounded-2xl p-4">
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 bg-slate-900 rounded-full flex items-center justify-center flex-shrink-0 border border-amber-500/20">
                    <span class="text-amber-400 text-sm font-bold">{{ $review['avatar'] }}</span>
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <p class="font-semibold text-gray-800 text-sm">{{ $review['name'] }}</p>
                        <span class="text-xs text-gray-400">{{ $review['date'] }}</span>
                    </div>
                    <div class="flex text-amber-500 my-1">
                        @for($i = 0; $i < $review['rating']; $i++)
                            <svg class="w-3 h-3 fill-current" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                        @endfor
                    </div>
                    <p class="text-gray-600 text-sm">{{ $review['comment'] }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- ═══ REKOMENDASI PRODUK ═══ --}}
@php
$recommendations = \App\Models\Product::where('id', '!=', $product->id)
    ->where('stock', '>', 0)
    ->inRandomOrder()
    ->limit(4)
    ->get();
@endphp

@if($recommendations->count() > 0)
<div class="mb-8">
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="font-display font-bold text-gray-900 text-lg">Produk Lainnya</h2>
            <p class="text-sm text-gray-400">Produk pilihan yang mungkin kamu suka</p>
        </div>
        <a href="{{ route('home') }}" class="text-sm text-amber-600 hover:underline font-semibold">Lihat semua →</a>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        @foreach($recommendations as $rec)
        <div class="bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group">
            <a href="{{ route('shop.show', $rec) }}" class="block overflow-hidden">
                <img src="{{ $rec->image_url }}" alt="{{ $rec->name }}"
                    class="w-full h-36 object-cover group-hover:scale-105 transition-transform duration-500">
            </a>
            <div class="p-3">
                <a href="{{ route('shop.show', $rec) }}" class="block">
                    <h4 class="font-semibold text-gray-800 text-xs leading-tight hover:text-amber-600 transition-colors line-clamp-2 mb-1 min-h-[2rem]">{{ $rec->name }}</h4>
                </a>
                <p class="font-extrabold text-amber-600 text-sm">Rp {{ number_format($rec->price, 0, ',', '.') }}</p>
                @auth
                    @if(!auth()->user()->isAdmin())
                        <form method="POST" action="{{ route('cart.add', $rec) }}" class="mt-2">
                            @csrf
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="w-full bg-amber-50 text-amber-700 text-xs py-1.5 rounded-lg font-bold hover:bg-slate-900 hover:text-amber-400 transition-all">
                                + Keranjang
                            </button>
                        </form>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="block mt-2 text-center bg-gray-50 text-gray-500 text-xs py-1.5 rounded-lg">Login untuk beli</a>
                @endauth
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
function changeQty(delta) {
    const input = document.getElementById('qty');
    const max = parseInt(input.getAttribute('max'));
    let val = parseInt(input.value) + delta;
    if (val < 1) val = 1;
    if (val > max) val = max;
    input.value = val;
}
</script>
@endpush