<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'IndoOngkir') — Toko UMKM Indonesia</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { font-family: 'Inter', sans-serif; }
        .font-display { font-family: 'Plus Jakarta Sans', sans-serif; }

        /* Navbar glass effect */
        .navbar-glass {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0,0,0,0.06);
        }

        /* Cart badge pulse */
        .cart-badge {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        /* Smooth transitions */
        .nav-link {
            position: relative;
            transition: color 0.2s;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: #f97316;
            transition: width 0.3s;
        }
        .nav-link:hover::after { width: 100%; }

        /* Hero gradient text */
        .gradient-text {
            background: linear-gradient(135deg, #f97316, #ea580c);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Flash message styles */
        .flash-success {
            background: linear-gradient(135deg, #f0fdf4, #dcfce7);
            border-left: 4px solid #22c55e;
        }
        .flash-error {
            background: linear-gradient(135deg, #fef2f2, #fee2e2);
            border-left: 4px solid #ef4444;
        }

        /* Footer */
        .footer-bg {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">

    {{-- ═══ NAVBAR ═══ --}}
    <nav class="navbar-glass sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">

                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                    <div class="w-9 h-9 bg-gradient-to-br from-orange-400 to-orange-600 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-orange-200 transition-shadow">
                        <span class="text-white text-lg">🛍</span>
                    </div>
                    <div>
                        <span class="font-display font-bold text-gray-900 text-lg leading-none">IndoOngkir</span>
                        <p class="text-xs text-gray-400 leading-none">Toko UMKM Indonesia</p>
                    </div>
                </a>

                {{-- Nav Links --}}
                <div class="hidden md:flex items-center gap-6">
                    <a href="{{ route('home') }}" class="nav-link text-sm font-medium text-gray-600 hover:text-orange-500">Toko</a>
                    @auth
                        @if(!auth()->user()->isAdmin())
                            <a href="{{ route('transactions.index') }}" class="nav-link text-sm font-medium text-gray-600 hover:text-orange-500">Pesanan Saya</a>
                        @else
                            <a href="{{ route('admin.products.index') }}" class="nav-link text-sm font-medium text-gray-600 hover:text-orange-500">Admin Panel</a>
                        @endif
                    @endauth
                </div>

                {{-- Right Side --}}
                <div class="flex items-center gap-3">
                    @guest
                        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-orange-500 transition-colors">Masuk</a>
                        <a href="{{ route('register') }}" class="bg-gradient-to-r from-orange-500 to-orange-600 text-white px-4 py-2 rounded-xl text-sm font-semibold hover:shadow-lg hover:shadow-orange-200 transition-all">
                            Daftar Gratis
                        </a>
                    @else
                        @if(!auth()->user()->isAdmin())
                            <a href="{{ route('cart.index') }}" class="relative p-2 text-gray-600 hover:text-orange-500 hover:bg-orange-50 rounded-xl transition-all">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                @php $cartCount = auth()->user()->cart?->items->count() ?? 0; @endphp
                                @if($cartCount > 0)
                                    <span class="cart-badge absolute -top-1 -right-1 bg-orange-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold">{{ $cartCount }}</span>
                                @endif
                            </a>
                        @endif

                        <div class="flex items-center gap-2 pl-2 border-l border-gray-200">
                            <div class="w-8 h-8 bg-gradient-to-br from-orange-100 to-orange-200 rounded-full flex items-center justify-center">
                                <span class="text-orange-600 font-bold text-sm">{{ substr(auth()->user()->name, 0, 1) }}</span>
                            </div>
                            <span class="text-sm font-medium text-gray-700 hidden md:block">{{ auth()->user()->name }}</span>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-sm text-gray-400 hover:text-red-500 transition-colors ml-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    {{-- Flash Messages --}}
    @if(session('success') || session('error'))
    <div class="max-w-7xl mx-auto px-4 pt-4 w-full">
        @if(session('success'))
            <div class="flash-success px-4 py-3 rounded-xl mb-2 flex items-center gap-2 text-green-800 text-sm font-medium">
                <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="flash-error px-4 py-3 rounded-xl mb-2 flex items-center gap-2 text-red-800 text-sm font-medium">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                {{ session('error') }}
            </div>
        @endif
    </div>
    @endif

    {{-- Main Content --}}
    <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 w-full">
        @yield('content')
    </main>

    {{-- ═══ FOOTER ═══ --}}
    <footer class="footer-bg text-white mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">

                {{-- Brand --}}
                <div class="md:col-span-2">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-10 h-10 bg-gradient-to-br from-orange-400 to-orange-600 rounded-xl flex items-center justify-center">
                            <span class="text-white text-xl">🛍</span>
                        </div>
                        <div>
                            <span class="font-display font-bold text-white text-xl">IndoOngkir</span>
                            <p class="text-xs text-gray-400">Toko UMKM Indonesia</p>
                        </div>
                    </div>
                    <p class="text-gray-400 text-sm leading-relaxed max-w-sm">
                        Platform belanja produk UMKM lokal dengan pengiriman ke seluruh Indonesia. Dukung produk dalam negeri, belanja lebih mudah.
                    </p>
                    <div class="flex gap-3 mt-4">
                        <div class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center hover:bg-orange-500 transition-colors cursor-pointer">
                            <span class="text-sm">📘</span>
                        </div>
                        <div class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center hover:bg-orange-500 transition-colors cursor-pointer">
                            <span class="text-sm">📸</span>
                        </div>
                        <div class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center hover:bg-orange-500 transition-colors cursor-pointer">
                            <span class="text-sm">🐦</span>
                        </div>
                    </div>
                </div>

                {{-- Navigasi --}}
                <div>
                    <h4 class="font-semibold text-white mb-4 text-sm uppercase tracking-wider">Navigasi</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('home') }}" class="text-gray-400 hover:text-orange-400 text-sm transition-colors">Semua Produk</a></li>
                        @auth
                            @if(!auth()->user()->isAdmin())
                                <li><a href="{{ route('cart.index') }}" class="text-gray-400 hover:text-orange-400 text-sm transition-colors">Keranjang Belanja</a></li>
                                <li><a href="{{ route('transactions.index') }}" class="text-gray-400 hover:text-orange-400 text-sm transition-colors">Pesanan Saya</a></li>
                            @endif
                        @else
                            <li><a href="{{ route('login') }}" class="text-gray-400 hover:text-orange-400 text-sm transition-colors">Masuk</a></li>
                            <li><a href="{{ route('register') }}" class="text-gray-400 hover:text-orange-400 text-sm transition-colors">Daftar</a></li>
                        @endauth
                    </ul>
                </div>

                {{-- Layanan --}}
                <div>
                    <h4 class="font-semibold text-white mb-4 text-sm uppercase tracking-wider">Layanan Kirim</h4>
                    <ul class="space-y-2">
                        <li class="flex items-center gap-2 text-gray-400 text-sm">
                            <span class="w-6 h-6 bg-red-500/20 rounded-md flex items-center justify-center text-xs">🚚</span> JNE
                        </li>
                        <li class="flex items-center gap-2 text-gray-400 text-sm">
                            <span class="w-6 h-6 bg-blue-500/20 rounded-md flex items-center justify-center text-xs">📦</span> POS Indonesia
                        </li>
                        <li class="flex items-center gap-2 text-gray-400 text-sm">
                            <span class="w-6 h-6 bg-green-500/20 rounded-md flex items-center justify-center text-xs">🏍</span> TIKI
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Bottom Bar --}}
            <div class="border-t border-white/10 mt-10 pt-6 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-gray-500 text-xs">© {{ date('Y') }} IndoOngkir. Dibuat dengan ❤️ untuk UMKM Indonesia.</p>
                <div class="flex items-center gap-4">
                    <span class="text-gray-500 text-xs">Powered by RajaOngkir API</span>
                    <div class="flex gap-2">
                        <span class="bg-white/5 text-gray-400 text-xs px-2 py-1 rounded">Laravel 11</span>
                        <span class="bg-white/5 text-gray-400 text-xs px-2 py-1 rounded">Tailwind CSS</span>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>