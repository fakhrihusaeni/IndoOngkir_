<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'IndoOngkir') - Toko Online UMKM</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen">

    {{-- Navbar --}}
    <nav class="bg-orange-500 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <a href="{{ route('home') }}" class="text-white font-bold text-xl flex items-center gap-2">
                    🛍️ IndoOngkir
                </a>

                <div class="flex items-center gap-4">
                    @guest
                        <a href="{{ route('login') }}" class="text-white hover:text-orange-200 text-sm">Masuk</a>
                        <a href="{{ route('register') }}" class="bg-white text-orange-500 px-4 py-1.5 rounded-full text-sm font-medium hover:bg-orange-50">Daftar</a>
                    @else
                        @if(!auth()->user()->isAdmin())
                            <a href="{{ route('cart.index') }}" class="text-white hover:text-orange-200 relative">
                                🛒
                                @php $cartCount = auth()->user()->cart?->items->count() ?? 0; @endphp
                                @if($cartCount > 0)
                                    <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">{{ $cartCount }}</span>
                                @endif
                            </a>
                            <a href="{{ route('transactions.index') }}" class="text-white hover:text-orange-200 text-sm">Pesanan Saya</a>
                        @else
                            <a href="{{ route('admin.products.index') }}" class="text-white hover:text-orange-200 text-sm">Admin Panel</a>
                        @endif

                        <div class="flex items-center gap-2">
                            <span class="text-orange-100 text-sm">{{ auth()->user()->name }}</span>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="bg-white text-orange-500 px-4 py-1.5 rounded-full text-sm font-medium hover:bg-orange-50">
                                    Keluar
                                </button>
                            </form>
                        </div>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    {{-- Flash Messages --}}
    <div class="max-w-7xl mx-auto px-4 mt-4">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                ✅ {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                ❌ {{ session('error') }}
            </div>
        @endif
    </div>

    {{-- Main Content --}}
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @yield('content')
    </main>

    <footer class="bg-orange-500 text-white text-center py-4 mt-12">
        <p class="text-sm">© 2024 IndoOngkir - Toko Online UMKM | Pengiriman ke seluruh Indonesia</p>
    </footer>

    @stack('scripts')
</body>
</html>