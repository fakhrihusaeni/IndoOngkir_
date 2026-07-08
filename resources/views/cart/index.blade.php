@extends('layouts.app')
@section('title', 'Keranjang Belanja')

@section('content')
<h1 class="font-display font-bold text-gray-900 text-2xl mb-6">🛒 Keranjang Belanja</h1>

@if(!$cart || $cart->items->isEmpty())
    <div class="text-center py-20 bg-white rounded-3xl border border-gray-100 shadow-sm">
        <div class="w-20 h-20 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-amber-100">
            <span class="text-4xl">🛒</span>
        </div>
        <p class="text-gray-800 font-semibold text-lg mb-1">Keranjang kamu masih kosong</p>
        <p class="text-gray-400 text-sm mb-6">Yuk, cari produk UMKM menarik lainnya di toko kami!</p>
        <a href="{{ route('home') }}" class="bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950 px-6 py-3 rounded-xl font-bold text-sm hover:shadow-lg shadow-sm">
            Mulai Belanja
        </a>
    </div>
@else
    <form method="GET" action="{{ route('cart.checkout') }}" id="checkoutForm">
        <div class="grid lg:grid-cols-3 gap-6">
            {{-- Daftar Item Keranjang --}}
            <div class="lg:col-span-2 space-y-4">
                @foreach($cart->items as $item)
                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-4 flex gap-4 items-center">
                    <input type="checkbox" name="items[]" value="{{ $item->id }}"
                        class="w-5 h-5 accent-amber-500 rounded cursor-pointer" checked>

                    <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}" class="w-20 h-20 object-cover rounded-2xl bg-gray-50 border border-gray-100 flex-shrink-0">

                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-900 text-sm md:text-base truncate">{{ $item->product->name }}</p>
                        <p class="text-amber-600 font-bold">Rp {{ number_format($item->product->price, 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">⚖️ {{ $item->product->weight }}g/item</p>

                        <div class="flex items-center gap-4 mt-2">
                            {{-- Quantity diupdate lewat JS fetch --}}
                            <input type="number" value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock }}"
                                onchange="updateQuantity({{ $item->id }}, this.value)"
                                class="w-16 border border-gray-200 bg-gray-50 rounded-xl px-2 py-1 text-center font-bold text-sm focus:ring-2 focus:ring-amber-500 focus:border-transparent outline-none">

                            {{-- Hapus tetap pakai form biasa --}}
                            <button type="button" onclick="removeItem({{ $item->id }})"
                                class="text-gray-400 hover:text-rose-600 text-xs font-medium transition-colors flex items-center gap-1 p-1 rounded-lg hover:bg-rose-50">
                                🗑️ Hapus
                            </button>
                        </div>
                    </div>

                    <div class="text-right flex-shrink-0 pl-2">
                        <p class="font-extrabold text-gray-900 text-sm md:text-base">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Ringkasan Belanja --}}
            <div class="bg-white rounded-3xl border border-gray-100 shadow-md p-6 h-fit">
                <h3 class="font-display font-bold text-gray-900 text-sm uppercase tracking-wider mb-4">Ringkasan</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between text-gray-600">
                        <span>Total Berat</span>
                        <span class="font-semibold text-gray-900">{{ $cart->total_weight }}g</span>
                    </div>
                    <div class="flex justify-between items-baseline font-bold text-base border-t border-gray-100 pt-3 mt-2">
                        <span class="text-gray-800">Subtotal</span>
                        <span class="text-xl font-extrabold text-amber-600">Rp {{ number_format($cart->subtotal, 0, ',', '.') }}</span>
                    </div>
                </div>

                <button type="submit"
                    class="block w-full text-center bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950 py-3.5 rounded-xl font-bold hover:shadow-lg hover:shadow-amber-500/20 active:scale-[0.98] transition-all text-sm mt-6">
                    Lanjut ke Checkout 💳
                </button>
            </div>
        </div>
    </form>
@endif
@endsection

@push('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// Update quantity tanpa reload form checkout (pakai fetch, bukan form nested)
function updateQuantity(itemId, quantity) {
    fetch(`/keranjang/update/${itemId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-HTTP-Method-Override': 'PATCH'
        },
        body: JSON.stringify({ quantity: quantity })
    })
    .then(response => {
        if (!response.ok) {
            alert('Gagal update quantity. Stok mungkin tidak cukup.');
        }
        location.reload();
    })
    .catch(() => {
        alert('Terjadi kesalahan, coba lagi.');
    });
}

// Hapus item dari keranjang
function removeItem(itemId) {
    if (!confirm('Hapus item ini dari keranjang?')) return;

    fetch(`/keranjang/hapus/${itemId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-HTTP-Method-Override': 'DELETE'
        }
    })
    .then(() => location.reload())
    .catch(() => alert('Gagal menghapus item.'));
}
</script>
@endpush