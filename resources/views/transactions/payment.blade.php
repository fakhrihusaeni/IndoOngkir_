@extends('layouts.app')
@section('title', 'Pilih Metode Pembayaran')

@section('content')
<div class="max-w-2xl mx-auto">

    {{-- Header --}}
    <div class="mb-6">
        <a href="{{ route('transactions.show', $transaction) }}" class="flex items-center gap-2 text-gray-400 hover:text-orange-500 transition-colors text-sm mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Detail Pesanan
        </a>
        <h1 class="font-display font-bold text-gray-900 text-2xl">Pilih Metode Pembayaran</h1>
        <p class="text-gray-500 text-sm mt-1">{{ $transaction->invoice_number }}</p>
    </div>

    {{-- Order Summary --}}
    <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-2xl p-5 mb-6 text-white">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-orange-100 text-sm mb-1">Total Pembayaran</p>
                <p class="text-3xl font-bold font-display">Rp {{ number_format($transaction->total, 0, ',', '.') }}</p>
            </div>
            <div class="text-right">
                <p class="text-orange-100 text-xs mb-1">{{ count($transaction->items) }} produk</p>
                <p class="text-sm font-medium">{{ $transaction->courier }} {{ $transaction->courier_service }}</p>
                <p class="text-orange-100 text-xs">→ {{ $transaction->recipient_city }}</p>
            </div>
        </div>
    </div>

    {{-- Payment Methods --}}
    <form method="POST" action="{{ route('payment.process', $transaction) }}" id="paymentForm">
        @csrf

        <div class="space-y-4 mb-6">
            @foreach($paymentMethods as $groupKey => $group)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-100 flex items-center gap-2">
                    <span class="text-lg">{{ $group['icon'] }}</span>
                    <span class="font-semibold text-gray-700 text-sm">{{ $group['label'] }}</span>
                </div>
                <div class="divide-y divide-gray-50">
                    @foreach($group['options'] as $option)
                    <label class="flex items-center gap-4 p-4 cursor-pointer hover:bg-orange-50 transition-colors group">
                        <input type="radio" name="payment_method" value="{{ $option['code'] }}"
                            class="w-4 h-4 text-orange-500 border-gray-300 focus:ring-orange-400"
                            {{ old('payment_method') === $option['code'] ? 'checked' : '' }}>
                        <div class="flex-1">
                            <p class="font-medium text-gray-800 text-sm group-hover:text-orange-600 transition-colors">
                                {{ $option['name'] }}
                            </p>
                        </div>
                        <svg class="w-4 h-4 text-gray-300 group-hover:text-orange-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </label>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        @error('payment_method')
            <p class="text-red-500 text-sm mb-4">{{ $message }}</p>
        @enderror

        {{-- Info --}}
        <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-6">
            <div class="flex gap-3">
                <span class="text-blue-500 flex-shrink-0">ℹ️</span>
                <div class="text-sm text-blue-700">
                    <p class="font-medium mb-1">Informasi Pembayaran</p>
                    <ul class="space-y-1 text-blue-600 text-xs">
                        <li>• Pembayaran diproses melalui Komerce Payment Gateway yang aman</li>
                        <li>• Setelah memilih metode, Anda akan diarahkan ke halaman pembayaran</li>
                        <li>• Status pesanan akan otomatis diperbarui setelah pembayaran berhasil</li>
                        <li>• Jika ada kendala, hubungi admin</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Submit Button --}}
        <button type="submit"
            class="w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white py-4 rounded-2xl font-bold text-lg hover:shadow-xl hover:shadow-orange-200 transition-all flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg>
            Lanjutkan Pembayaran
        </button>
    </form>

    {{-- Security Badge --}}
    <div class="flex items-center justify-center gap-3 mt-6 text-gray-400">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
        </svg>
        <span class="text-xs">Pembayaran aman & terenkripsi • Powered by Komerce</span>
    </div>
</div>
@endsection