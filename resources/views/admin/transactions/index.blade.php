@extends('layouts.admin')
@section('title', 'Manajemen Transaksi')

@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-6">📋 Manajemen Transaksi</h1>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="text-left px-4 py-3 text-gray-600">Invoice</th>
                <th class="text-left px-4 py-3 text-gray-600">Pembeli</th>
                <th class="text-left px-4 py-3 text-gray-600">Tujuan</th>
                <th class="text-left px-4 py-3 text-gray-600">Total</th>
                <th class="text-left px-4 py-3 text-gray-600">Status</th>
                <th class="text-left px-4 py-3 text-gray-600">Ubah Status</th>
                <th class="text-left px-4 py-3 text-gray-600">Invoice</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($transactions as $trx)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3">
                    <p class="font-medium">{{ $trx->invoice_number }}</p>
                    <p class="text-gray-400 text-xs">{{ $trx->created_at->format('d M Y') }}</p>
                </td>
                <td class="px-4 py-3">{{ $trx->user->name }}</td>
                <td class="px-4 py-3">{{ $trx->recipient_city }}</td>
                <td class="px-4 py-3 font-bold text-orange-500">Rp {{ number_format($trx->total, 0, ',', '.') }}</td>
                <td class="px-4 py-3">
                    @php $colors = ['belum_bayar' => 'yellow', 'dikirim' => 'blue', 'selesai' => 'green']; @endphp
                    <span class="bg-{{ $colors[$trx->status] }}-100 text-{{ $colors[$trx->status] }}-700 px-2 py-0.5 rounded-full text-xs font-medium">
                        {{ $trx->status_label }}
                    </span>
                </td>
                <td class="px-4 py-3">
                    <form method="POST" action="{{ route('admin.transactions.updateStatus', $trx) }}" class="flex gap-1">
                        @csrf @method('PATCH')
                        <select name="status" class="text-xs border border-gray-300 rounded px-2 py-1 focus:ring-1 focus:ring-orange-400 outline-none">
                            <option value="belum_bayar" {{ $trx->status === 'belum_bayar' ? 'selected' : '' }}>Belum Bayar</option>
                            <option value="dikirim" {{ $trx->status === 'dikirim' ? 'selected' : '' }}>Dikirim</option>
                            <option value="selesai" {{ $trx->status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                        </select>
                        <button type="submit" class="bg-orange-500 text-white px-2 py-1 rounded text-xs hover:bg-orange-600">✓</button>
                    </form>
                </td>
                <td class="px-4 py-3">
                    <a href="{{ route('transactions.invoice', $trx) }}" target="_blank"
                        class="text-orange-500 hover:underline text-xs">🖨️ PDF</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center py-10 text-gray-400">Belum ada transaksi</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4">{{ $transactions->links() }}</div>
</div>
@endsection