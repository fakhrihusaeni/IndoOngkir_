<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'IndoOngkir') — Premium Gold UMKM</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { font-family: 'Inter', sans-serif; }
        .font-display { font-family: 'Plus Jakarta Sans', sans-serif; }

        /* Navbar glass effect - Diubah ke Deep Navy Transparan */
        .navbar-glass {
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(245, 158, 11, 0.15);
        }

        /* Cart badge pulse */
        .cart-badge {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        /* Smooth transitions & Gold Hover Effect */
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
            background: #d97706; /* Gold Amber-600 */
            transition: width 0.3s;
        }
        .nav-link:hover::after { width: 100%; }

        /* Hero gradient text - Diubah ke Gold Premium Gradient */
        .gradient-text {
            background: linear-gradient(135deg, #fbbf24, #d97706);
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

        /* Footer - Tetap bernuansa dark gradient premium */
        .footer-bg {
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #020617 100%);
            border-top: 1px solid rgba(245, 158, 11, 0.1);
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">

    {{-- ═══ NAVBAR (Deep Navy Theme) ═══ --}}
    <nav class="navbar-glass sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">

                {{-- Logo dengan Aksen Gold --}}
                <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                    <div class="w-9 h-9 bg-gradient-to-br from-amber-400 to-amber-600 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-amber-500/20 transition-all">
                        <span class="text-slate-950 text-lg">🛍</span>
                    </div>
                    <div>
                        <span class="font-display font-bold text-white text-lg leading-none tracking-wide">Indo<span class="text-amber-400">Ongkir</span></span>
                        <p class="text-[10px] text-amber-500/80 leading-none tracking-wider uppercase font-semibold mt-0.5">Premium UMKM</p>
                    </div>
                </a>

                {{-- Nav Links --}}
                <div class="hidden md:flex items-center gap-6">
                    <a href="{{ route('home') }}" class="nav-link text-sm font-medium text-slate-300 hover:text-amber-400">Toko</a>
                    @auth
                        @if(!auth()->user()->isAdmin())
                            <a href="{{ route('transactions.index') }}" class="nav-link text-sm font-medium text-slate-300 hover:text-amber-400">Pesanan Saya</a>
                            {{-- Tombol Baru: Menu Profil Pengguna --}}
                            <a href="{{ route('profile.edit') }}" class="nav-link text-sm font-medium text-slate-300 hover:text-amber-400">
                                Profil Saya
                            </a>
                        @else
                            <a href="{{ route('admin.products.index') }}" class="nav-link text-sm font-semibold text-amber-400 hover:text-amber-300">Admin Panel</a>
                        @endif
                    @endauth
                </div>

                {{-- Right Side --}}
                <div class="flex items-center gap-3">
                    @guest
                        <a href="{{ route('login') }}" class="text-sm font-medium text-slate-300 hover:text-amber-400 transition-colors">Masuk</a>
                        <a href="{{ route('register') }}" class="bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950 px-4 py-2 rounded-xl text-sm font-bold hover:shadow-lg hover:shadow-amber-500/20 active:scale-95 transition-all">
                            Daftar Gratis
                        </a>
                    @else
                        @if(!auth()->user()->isAdmin())
                            {{-- Cart Icon --}}
                            <a href="{{ route('cart.index') }}" class="relative p-2 text-slate-300 hover:text-amber-400 hover:bg-white/5 rounded-xl transition-all">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                @php $cartCount = auth()->user()->cart?->items->count() ?? 0; @endphp
                                @if($cartCount > 0)
                                    <span class="cart-badge absolute -top-1 -right-1 bg-amber-500 text-slate-950 text-xs rounded-full w-5 h-5 flex items-center justify-center font-black border border-slate-900">{{ $cartCount }}</span>
                                @endif
                            </a>
                        @endif

                        {{-- User Avatar Info & Logout --}}
                        <div class="flex items-center gap-2 pl-2 border-l border-slate-700">
                            <div class="w-8 h-8 bg-gradient-to-br from-amber-400/20 to-amber-500/30 border border-amber-500/40 rounded-full flex items-center justify-center">
                                <span class="text-amber-400 font-bold text-sm">{{ substr(auth()->user()->name, 0, 1) }}</span>
                            </div>
                            <span class="text-sm font-medium text-slate-200 hidden md:block">{{ auth()->user()->name }}</span>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-sm text-slate-400 hover:text-rose-400 transition-colors ml-1">
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
            <div class="flash-success px-4 py-3 rounded-xl mb-2 flex items-center gap-2 text-green-800 text-sm font-medium shadow-sm">
                <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="flash-error px-4 py-3 rounded-xl mb-2 flex items-center gap-2 text-red-800 text-sm font-medium shadow-sm">
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

    {{-- ═══ FOOTER (Deep Premium Gradient) ═══ --}}
    <footer class="footer-bg text-white mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">

                {{-- Brand --}}
                <div class="md:col-span-2">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-10 h-10 bg-gradient-to-br from-amber-400 to-amber-600 rounded-xl flex items-center justify-center">
                            <span class="text-slate-950 text-xl">🛍</span>
                        </div>
                        <div>
                            <span class="font-display font-bold text-white text-xl tracking-wide">Indo<span class="text-amber-400">Ongkir</span></span>
                            <p class="text-xs text-amber-500/60">UMKM Store</p>
                        </div>
                    </div>
                    <p class="text-slate-400 text-sm leading-relaxed max-w-sm">
                        Platform belanja produk UMKM lokal premium dengan pengiriman terintegrasi ke seluruh Indonesia. Dukung karya anak bangsa dengan pelayanan terbaik.
                    </p>
                    <div class="flex gap-3 mt-4">
                        <div class="w-8 h-8 bg-white/5 border border-white/10 rounded-lg flex items-center justify-center hover:bg-amber-500 hover:text-slate-950 transition-all cursor-pointer">
                            <span class="text-sm">📘</span>
                        </div>
                        <div class="w-8 h-8 bg-white/5 border border-white/10 rounded-lg flex items-center justify-center hover:bg-amber-500 hover:text-slate-950 transition-all cursor-pointer">
                            <span class="text-sm">📸</span>
                        </div>
                        <div class="w-8 h-8 bg-white/5 border border-white/10 rounded-lg flex items-center justify-center hover:bg-amber-500 hover:text-slate-950 transition-all cursor-pointer">
                            <span class="text-sm">🐦</span>
                        </div>
                    </div>
                </div>

                {{-- Navigasi --}}
                <div>
                    <h4 class="font-semibold text-amber-400 mb-4 text-sm uppercase tracking-wider">Navigasi</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('home') }}" class="text-slate-400 hover:text-amber-400 text-sm transition-colors">Semua Produk</a></li>
                        @auth
                            @if(!auth()->user()->isAdmin())
                                <li><a href="{{ route('cart.index') }}" class="text-slate-400 hover:text-amber-400 text-sm transition-colors">Keranjang Belanja</a></li>
                                <li><a href="{{ route('transactions.index') }}" class="text-slate-400 hover:text-amber-400 text-sm transition-colors">Pesanan Saya</a></li>
                            @endif
                        @else
                            <li><a href="{{ route('login') }}" class="text-slate-400 hover:text-amber-400 text-sm transition-colors">Masuk</a></li>
                            <li><a href="{{ route('register') }}" class="text-slate-400 hover:text-amber-400 text-sm transition-colors">Daftar</a></li>
                        @endauth
                    </ul>
                </div>

                {{-- Layanan --}}
                <div>
                    <h4 class="font-semibold text-amber-400 mb-4 text-sm uppercase tracking-wider">Layanan Kirim</h4>
                    <ul class="space-y-2">
                        <li class="flex items-center gap-2 text-slate-400 text-sm">
                            <span class="w-6 h-6 bg-amber-500/10 border border-amber-500/20 rounded-md flex items-center justify-center text-xs">🚚</span> JNE Express
                        </li>
                        <li class="flex items-center gap-2 text-slate-400 text-sm">
                            <span class="w-6 h-6 bg-amber-500/10 border border-amber-500/20 rounded-md flex items-center justify-center text-xs">📦</span> POS Indonesia
                        </li>
                        <li class="flex items-center gap-2 text-slate-400 text-sm">
                            <span class="w-6 h-6 bg-amber-500/10 border border-amber-500/20 rounded-md flex items-center justify-center text-xs">🏍</span> TIKI Logistik
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Bottom Bar --}}
            <div class="border-t border-white/5 mt-10 pt-6 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-slate-500 text-xs">© {{ date('Y') }} IndoOngkir. Eksklusif untuk UMKM Indonesia.</p>
                <div class="flex items-center gap-4">
                    <span class="text-slate-500 text-xs">Powered by RajaOngkir API</span>
                    <div class="flex gap-2">
                        <span class="bg-white/5 text-slate-400 text-xs px-2 py-1 rounded border border-white/5">Laravel 11</span>
                        <span class="bg-white/5 text-slate-400 text-xs px-2 py-1 rounded border border-white/5">Tailwind CSS</span>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>