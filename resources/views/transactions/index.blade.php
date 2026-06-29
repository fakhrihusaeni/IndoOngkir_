@extends('layouts.app')
@section('title', 'Pesanan Saya')

@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-6">📋 Pesanan Saya</h1>

@if($transactions->isEmpty())
    <div class="text-center py-20 bg-white rounded-2xl shadow">
        <p class="text-5xl mb-4">📋</p>
        <p class="text-gray-500 text-lg">Belum ada pesanan</p>
        <a href="{{ route('home') }}" class="mt-4 inline-block bg-orange-500 text-white px-6 py-2 rounded-lg hover:bg-orange-600">Mulai Belanja</a>
    </div>
@else
    <div class="space-y-4">
        @foreach($transactions as $trx)
        <div class="bg-white rounded-xl shadow p-4">
            <div class="flex justify-between items-start">
                <div>
                    <p class="font-bold text-gray-800">{{ $trx->invoice_number }}</p>
                    <p class="text-sm text-gray-500">{{ $trx->created_at->format('d M Y, H:i') }}</p>
                    <p class="text-sm text-gray-600">{{ $trx->courier }} {{ $trx->courier_service }} → {{ $trx->recipient_city }}</p>
                </div>
                <div class="text-right">
                    @php
                        $colors = ['belum_bayar' => 'yellow', 'dikirim' => 'blue', 'selesai' => 'green'];
                        $color = $colors[$trx->status] ?? 'gray';
                    @endphp
                    <span class="bg-{{ $color }}-100 text-{{ $color }}-700 px-3 py-1 rounded-full text-sm font-medium">
                        {{ $trx->status_label }}
                    </span>
                    <p class="font-bold text-orange-500 mt-1">Rp {{ number_format($trx->total, 0, ',', '.') }}</p>
                </div>
            </div>
            <div class="flex gap-2 mt-3">
                <a href="{{ route('transactions.show', $trx) }}" class="text-sm bg-gray-100 text-gray-700 px-4 py-1.5 rounded-lg hover:bg-gray-200">
                    Lihat Detail
                </a>
                <a href="{{ route('transactions.invoice', $trx) }}" target="_blank"
                    class="text-sm bg-orange-100 text-orange-600 px-4 py-1.5 rounded-lg hover:bg-orange-200">
                    🖨️ Cetak Invoice
                </a>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-4">{{ $transactions->links() }}</div>
@endif
@endsection