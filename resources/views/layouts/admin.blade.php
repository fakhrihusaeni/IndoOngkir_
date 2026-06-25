<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - @yield('title', 'IndoOngkir')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen flex">

    {{-- Sidebar --}}
    <aside class="w-64 bg-gray-800 text-white min-h-screen p-4 flex-shrink-0">
        <div class="text-xl font-bold mb-8 text-orange-400">🛍️ IndoOngkir Admin</div>
        <nav class="space-y-2">
            <a href="{{ route('admin.products.index') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('admin.products.*') ? 'bg-gray-700' : '' }}">
                📦 Produk
            </a>
            <a href="{{ route('admin.transactions.index') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('admin.transactions.*') ? 'bg-gray-700' : '' }}">
                📋 Transaksi
            </a>
            <hr class="border-gray-600 my-4">
            <a href="{{ route('home') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-700">
                🏠 Lihat Toko
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="w-full text-left flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-700 text-red-400">
                    🚪 Keluar
                </button>
            </form>
        </nav>
    </aside>

    {{-- Content --}}
    <div class="flex-1 p-8">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">✅ {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">❌ {{ session('error') }}</div>
        @endif

        @yield('content')
    </div>
</body>
</html>