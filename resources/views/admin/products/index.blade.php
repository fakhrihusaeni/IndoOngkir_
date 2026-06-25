
@extends('layouts.admin')
@section('title', 'Kelola Produk')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">📦 Kelola Produk</h1>
    <a href="{{ route('admin.products.create') }}" class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 font-medium">
        + Tambah Produk
    </a>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="text-left px-4 py-3 text-gray-600">Foto</th>
                <th class="text-left px-4 py-3 text-gray-600">Nama Produk</th>
                <th class="text-left px-4 py-3 text-gray-600">Harga</th>
                <th class="text-left px-4 py-3 text-gray-600">Stok</th>
                <th class="text-left px-4 py-3 text-gray-600">Berat</th>
                <th class="text-left px-4 py-3 text-gray-600">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($products as $product)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3">
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-12 h-12 object-cover rounded-lg">
                </td>
                <td class="px-4 py-3">
                    <p class="font-medium text-gray-800">{{ $product->name }}</p>
                    <p class="text-gray-400 truncate max-w-xs">{{ Str::limit($product->description, 50) }}</p>
                </td>
                <td class="px-4 py-3 font-medium text-orange-500">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                <td class="px-4 py-3">
                    <span class="{{ $product->stock < 5 ? 'text-red-500 font-bold' : 'text-gray-700' }}">{{ $product->stock }}</span>
                </td>
                <td class="px-4 py-3 text-gray-600">{{ $product->weight }}g</td>
                <td class="px-4 py-3">
                    <div class="flex gap-2">
                        <a href="{{ route('admin.products.edit', $product) }}" class="bg-blue-100 text-blue-700 px-3 py-1 rounded-lg hover:bg-blue-200 text-xs font-medium">Edit</a>
                        <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Hapus produk ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="bg-red-100 text-red-700 px-3 py-1 rounded-lg hover:bg-red-200 text-xs font-medium">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center py-10 text-gray-400">Belum ada produk</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4">{{ $products->links() }}</div>
</div>
@endsection