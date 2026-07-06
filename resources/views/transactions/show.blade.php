@extends('layouts.app')
@section('title', 'Detail Pesanan')

@section('content')
<div class="max-w-2xl mx-auto">

    <a href="{{ route('transactions.index') }}" class="flex items-center gap-2 text-gray-400 hover:text-orange-500 text-sm mb-6 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali ke Pesanan Saya
    </a>

    {{-- Header Card --}}
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden mb-4">
        <div class="bg-gradient-to-r from-gray-900 to-gray-800 p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-400 text-xs mb-1">Nomor Invoice</p>
                    <p class="text-white font-bold font-display text-lg">{{ $transaction->invoice_number }}</p>
                    <p class="text-gray-400 text-xs mt-1">{{ $transaction->created_at->format('d F Y, H:i') }} WIB</p>
                </div>
                <div class="text-right space-y-2">
                    {{-- Status Pesanan --}}
                    @php $colors = ['belum_bayar' => 'yellow', 'dikirim' => 'blue', 'selesai' => 'green']; @endphp
                    <span class="inline-block bg-{{ $colors[$transaction->status] }}-500/20 text-{{ $colors[$transaction->status] }}-300 px-3 py-1 rounded-full text-xs font-medium border border-{{ $colors[$transaction->status] }}-500/30">
                        {{ $transaction->status_label }}
                    </span>

                    {{-- Status Pembayaran --}}
                    @php $pColors = ['unpaid' => 'red', 'pending' => 'yellow', 'paid' => 'green', 'failed' => 'red', 'expired' => 'gray']; @endphp
                    <span class="block bg-{{ $pColors[$transaction->payment_status ?? 'unpaid'] }}-500/20 text-{{ $pColors[$transaction->payment_status ?? 'unpaid'] }}-300 px-3 py-1 rounded-full text-xs font-medium border border-{{ $pColors[$transaction->payment_status ?? 'unpaid'] }}-500/30">
                        💳 {{ $transaction->payment_status_label ?? 'Belum Dibayar' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Payment Action --}}
        @if(($transaction->payment_status ?? 'unpaid') === 'unpaid' && $transaction->status === 'belum_bayar')
        <div class="bg-orange-50 border-b border-orange-100 p-4 flex items-center justify-between gap-4">
            <div>
                <p class="font-semibold text-orange-800 text-sm">⚡ Segera Lakukan Pembayaran</p>
                <p class="text-orange-600 text-xs">Pesanan akan dibatalkan jika tidak dibayar</p>
            </div>
            <a href="{{ route('payment.show', $transaction) }}"
                class="flex-shrink-0 bg-orange-500 text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-orange-600 transition-colors">
                Bayar Sekarang
            </a>
        </div>
        @elseif(($transaction->payment_status ?? '') === 'pending')
        <div class="bg-yellow-50 border-b border-yellow-100 p-4 flex items-center justify-between gap-4">
            <div>
                <p class="font-semibold text-yellow-800 text-sm">⏳ Menunggu Konfirmasi Pembayaran</p>
                <p class="text-yellow-600 text-xs">Pembayaran sedang diverifikasi</p>
            </div>
            <a href="{{ route('payment.check', $transaction) }}"
                class="flex-shrink-0 bg-yellow-500 text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-yellow-600 transition-colors">
                Cek Status
            </a>
        </div>
        @elseif(($transaction->payment_status ?? '') === 'paid')
        <div class="bg-green-50 border-b border-green-100 p-4 flex items-center gap-3">
            <span class="text-green-500 text-xl">✅</span>
            <div>
                <p class="font-semibold text-green-800 text-sm">Pembayaran Berhasil!</p>
                <p class="text-green-600 text-xs">Dibayar pada {{ $transaction->paid_at?->format('d M Y, H:i') }} WIB via {{ $transaction->payment_method }}</p>
            </div>
        </div>
        @endif

        <div class="p-5">
            {{-- Shipping Info --}}
            <div class="grid grid-cols-2 gap-4 text-sm mb-5">
                <div>
                    <p class="text-gray-400 text-xs mb-1">Penerima</p>
                    <p class="font-semibold text-gray-800">{{ $transaction->recipient_name }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs mb-1">Kurir</p>
                    <p class="font-semibold text-gray-800">{{ $transaction->courier }} — {{ $transaction->courier_service }}</p>
                </div>
                <div class="col-span-2">
                    <p class="text-gray-400 text-xs mb-1">Alamat Tujuan</p>
                    <p class="font-medium text-gray-700">{{ $transaction->recipient_address }}, {{ $transaction->recipient_city }}, {{ $transaction->recipient_province }}</p>
                </div>
            </div>

            {{-- Items --}}
            <div class="border-t border-gray-100 pt-4 mb-4">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Item Pesanan</p>
                @foreach($transaction->items as $item)
                <div class="flex justify-between items-center py-2.5 border-b border-gray-50 last:border-0">
                    <div>
                        <p class="font-medium text-gray-800 text-sm">{{ $item['product_name'] }}</p>
                        <p class="text-gray-400 text-xs">{{ $item['quantity'] }}x @ Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                    </div>
                    <p class="font-semibold text-gray-800 text-sm">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</p>
                </div>
                @endforeach
            </div>

            {{-- Totals --}}
            <div class="bg-gray-50 rounded-2xl p-4 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Subtotal Produk</span>
                    <span class="font-medium">Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Ongkos Kirim</span>
                    <span class="font-medium">Rp {{ number_format($transaction->shipping_cost, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between font-bold text-base border-t border-gray-200 pt-2 mt-2">
                    <span>Total Pembayaran</span>
                    <span class="text-orange-500">Rp {{ number_format($transaction->total, 0, ',', '.') }}</span>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex gap-3 mt-4">
                <a href="{{ route('transactions.invoice', $transaction) }}" target="_blank"
                    class="flex-1 text-center border border-orange-200 text-orange-500 py-2.5 rounded-xl text-sm font-semibold hover:bg-orange-50 transition-colors flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Cetak Invoice
                </a>
                @if(($transaction->payment_status ?? 'unpaid') === 'unpaid')
                <a href="{{ route('payment.show', $transaction) }}"
                    class="flex-1 text-center bg-orange-500 text-white py-2.5 rounded-xl text-sm font-semibold hover:bg-orange-600 transition-colors flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    Bayar Sekarang
                </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection