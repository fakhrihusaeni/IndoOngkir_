@extends('layouts.app')
@section('title', 'Checkout')

@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-6">✅ Checkout</h1>

<form method="POST" action="{{ route('transactions.store') }}" id="checkoutForm">
    @csrf
    <div class="grid lg:grid-cols-3 gap-6">

        {{-- Form Pengiriman --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">📍 Alamat Pengiriman</h2>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Penerima</label>
                        <input type="text" name="recipient_name" value="{{ auth()->user()->name }}" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-orange-400 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                        <textarea name="recipient_address" rows="3" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-orange-400 outline-none"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Provinsi</label>
                            <select id="province" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-orange-400 outline-none">
                                <option value="">-- Pilih Provinsi --</option>
                            </select>
                            <input type="hidden" name="province_name" id="province_name">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kota/Kabupaten</label>
                            <select id="city" disabled class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-orange-400 outline-none disabled:bg-gray-100">
                                <option value="">-- Pilih Kota --</option>
                            </select>
                            <input type="hidden" name="city_id" id="city_id">
                            <input type="hidden" name="city_name" id="city_name">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kurir</label>
                        <select id="courier" name="courier" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-orange-400 outline-none">
                            <option value="">-- Pilih Kurir --</option>
                            <option value="jne">JNE</option>
                            <option value="pos">POS Indonesia</option>
                            <option value="tiki">TIKI</option>
                        </select>
                    </div>
                    <div id="serviceContainer" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Layanan Pengiriman</label>
                        <div id="serviceList" class="space-y-2"></div>
                        <input type="hidden" name="courier_service" id="courier_service">
                        <input type="hidden" name="shipping_cost" id="shipping_cost">
                    </div>
                </div>
            </div>
        </div>

        {{-- Ringkasan Order --}}
        <div class="space-y-4">
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">📦 Pesanan</h2>
                @foreach($cart->items as $item)
                <div class="flex gap-2 mb-3">
                    <img src="{{ $item->product->image_url }}" class="w-12 h-12 object-cover rounded">
                    <div class="flex-1 text-sm">
                        <p class="font-medium">{{ $item->product->name }}</p>
                        <p class="text-gray-500">{{ $item->quantity }}x Rp {{ number_format($item->product->price, 0, ',', '.') }}</p>
                    </div>
                </div>
                @endforeach
                <hr class="my-3">
                <div class="space-y-1 text-sm">
                    <div class="flex justify-between">
                        <span>Subtotal</span>
                        <span>Rp {{ number_format($cart->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Ongkir</span>
                        <span id="ongkirDisplay">Rp 0</span>
                    </div>
                    <div class="flex justify-between font-bold text-lg border-t pt-2">
                        <span>Total</span>
                        <span class="text-orange-500" id="totalDisplay">Rp {{ number_format($cart->subtotal, 0, ',', '.') }}</span>
                    </div>
                </div>
                <p class="text-xs text-gray-400 mt-1">⚖️ Total berat: {{ $cart->total_weight }}g</p>
            </div>

            <button type="submit" id="submitBtn" disabled
                class="w-full bg-orange-500 text-white py-3 rounded-xl font-semibold hover:bg-orange-600 disabled:opacity-50 disabled:cursor-not-allowed transition">
                🛍️ Buat Pesanan
            </button>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
const totalWeight = {{ $cart->total_weight }};
const subtotal = {{ $cart->subtotal }};
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// Format rupiah
const formatRp = (n) => 'Rp ' + n.toLocaleString('id-ID');

// Load provinsi saat halaman dibuka
fetch('/api/ongkir/provinces')
    .then(r => r.json())
    .then(data => {
        const sel = document.getElementById('province');
        data.forEach(p => {
            sel.innerHTML += `<option value="${p.province_id}" data-name="${p.province}">${p.province}</option>`;
        });
    });

// Saat provinsi dipilih, load kota
document.getElementById('province').addEventListener('change', function () {
    const id = this.value;
    const name = this.options[this.selectedIndex].dataset.name;
    document.getElementById('province_name').value = name;

    const cityEl = document.getElementById('city');
    cityEl.disabled = true;
    cityEl.innerHTML = '<option value="">Loading...</option>';

    fetch(`/api/ongkir/cities?province_id=${id}`)
        .then(r => r.json())
        .then(data => {
            cityEl.innerHTML = '<option value="">-- Pilih Kota --</option>';
            data.forEach(c => {
                cityEl.innerHTML += `<option value="${c.city_id}" data-name="${c.type} ${c.city_name}">${c.type} ${c.city_name}</option>`;
            });
            cityEl.disabled = false;
        });
});

// Fungsi hitung ongkir
function hitungOngkir() {
    const cityId = document.getElementById('city').value;
    const courier = document.getElementById('courier').value;
    if (!cityId || !courier) return;

    const cityName = document.getElementById('city').options[document.getElementById('city').selectedIndex].dataset.name;
    document.getElementById('city_id').value = cityId;
    document.getElementById('city_name').value = cityName;

    fetch('/api/ongkir/cost', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ destination: cityId, weight: totalWeight, courier: courier })
    })
    .then(r => r.json())
    .then(services => {
        const container = document.getElementById('serviceContainer');
        const list = document.getElementById('serviceList');
        list.innerHTML = '';

        if (!services.length) {
            list.innerHTML = '<p class="text-red-500 text-sm">Layanan tidak tersedia untuk tujuan ini.</p>';
            container.classList.remove('hidden');
            return;
        }

        services.forEach((s, i) => {
            const cost = s.cost[0].value;
            list.innerHTML += `
                <label class="flex items-center gap-3 border rounded-lg p-3 cursor-pointer hover:border-orange-400">
                    <input type="radio" name="service_choice" value="${s.service}" data-cost="${cost}" ${i === 0 ? 'checked' : ''}
                        onchange="selectService('${s.service}', ${cost})">
                    <div>
                        <span class="font-medium">${courier.toUpperCase()} ${s.service}</span>
                        <span class="text-gray-500 text-sm"> - ${s.description}</span>
                        <span class="text-orange-500 font-bold ml-2">${formatRp(cost)}</span>
                        <span class="text-gray-400 text-xs ml-1">(Est. ${s.cost[0].etd} hari)</span>
                    </div>
                </label>`;
        });

        container.classList.remove('hidden');
        // Auto-pilih yang pertama
        const first = services[0];
        selectService(first.service, first.cost[0].value);
    });
}

function selectService(service, cost) {
    document.getElementById('courier_service').value = service;
    document.getElementById('shipping_cost').value = cost;
    const total = subtotal + cost;
    document.getElementById('ongkirDisplay').textContent = formatRp(cost);
    document.getElementById('totalDisplay').textContent = formatRp(total);
    document.getElementById('submitBtn').disabled = false;
}

document.getElementById('city').addEventListener('change', hitungOngkir);
document.getElementById('courier').addEventListener('change', hitungOngkir);
</script>
@endpush