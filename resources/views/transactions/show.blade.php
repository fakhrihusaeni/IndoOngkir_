@extends('layouts.app')
@section('title', 'Detail Pesanan')

@section('content')
<div class="max-w-2xl mx-auto">
    <a href="{{ route('transactions.index') }}" class="text-orange-500 hover:underline text-sm">← Kembali</a>

    <div class="bg-white rounded-2xl shadow-md mt-4 p-6">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-xl font-bold text-gray-800">{{ $transaction->invoice_number }}</h1>
            @php
                $colors = ['belum_bayar' => 'yellow', 'dikirim' => 'blue', 'selesai' => 'green'];
                $color = $colors[$transaction->status] ?? 'gray';
            @endphp
            <span class="bg-{{ $color }}-100 text-{{ $color }}-700 px-3 py-1 rounded-full text-sm font-medium">
                {{ $transaction->status_label }}
            </span>
        </div>

        <div class="grid grid-cols-2 gap-4 text-sm mb-4">
            <div>
                <p class="text-gray-500">Penerima</p>
                <p class="font-medium">{{ $transaction->recipient_name }}</p>
            </div>
            <div>
                <p class="text-gray-500">Kurir</p>
                <p class="font-medium">{{ $transaction->courier }} - {{ $transaction->courier_service }}</p>
            </div>
            <div class="col-span-2">
                <p class="text-gray-500">Alamat</p>
                <p class="font-medium">{{ $transaction->recipient_address }}, {{ $transaction->recipient_city }}, {{ $transaction->recipient_province }}</p>
            </div>
        </div>

        <hr class="my-4">
        <h2 class="font-bold text-gray-800 mb-3">Item Pesanan</h2>
        @foreach($transaction->items as $item)
        <div class="flex justify-between text-sm py-2 border-b last:border-0">
            <div>
                <p class="font-medium">{{ $item['product_name'] }}</p>
                <p class="text-gray-500">{{ $item['quantity'] }}x @ Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
            </div>
            <p class="font-medium">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</p>
        </div>
        @endforeach

        <div class="mt-4 space-y-2 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-600">Subtotal</span>
                <span>Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Ongkos Kirim</span>
                <span>Rp {{ number_format($transaction->shipping_cost, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between font-bold text-lg border-t pt-2">
                <span>Total</span>
                <span class="text-orange-500">Rp {{ number_format($transaction->total, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="mt-4">
            <a href="{{ route('transactions.invoice', $transaction) }}" target="_blank"
                class="inline-block bg-orange-500 text-white px-6 py-2 rounded-lg hover:bg-orange-600 font-medium">
                🖨️ Cetak Invoice PDF
            </a>
        </div>
    </div>
</div>
@endsection