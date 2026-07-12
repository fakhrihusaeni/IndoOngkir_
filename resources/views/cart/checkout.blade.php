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
                    <div class="grid grid-cols-3 gap-3">

                        <!-- Provinsi -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Provinsi
                            </label>

                            <select id="province"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2">
                                <option value="">-- Pilih Provinsi --</option>
                            </select>

                            <input type="hidden" name="province_name" id="province_name">
                        </div>

                        <!-- Kota -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Kota
                            </label>

                            <select id="city" disabled
                                class="w-full border border-gray-300 rounded-lg px-4 py-2">
                                <option value="">-- Pilih Kota --</option>
                            </select>

                            <input type="hidden" name="city_name" id="city_name">
                        </div>

                        <!-- Kecamatan -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Kecamatan
                            </label>

                            <select id="district" disabled
                                class="w-full border border-gray-300 rounded-lg px-4 py-2">
                                <option value="">-- Pilih Kecamatan --</option>
                            </select>

                            <input type="hidden" name="district_id" id="district_id">
                            <input type="hidden" name="district_name" id="district_name">
                        </div>
                        
                       <!-- Desa / Kelurahan -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Desa
                            </label>

                            <select id="village" disabled
                                class="w-full border border-gray-300 rounded-lg px-4 py-2">
                                <option value="">-- Pilih Desa --</option>
                            </select>

                            <input type="hidden" name="village_id" id="village_id">
                            <input type="hidden" name="village_name" id="village_name">
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
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

const formatRp = (n) => 'Rp ' + Number(n).toLocaleString('id-ID');

// ====================
// LOAD PROVINCE
// ====================

fetch('/api/ongkir/provinces')
.then(r => r.json())
.then(res => {

    const sel = document.getElementById('province');

    res.data.forEach(p => {

        sel.innerHTML += `
            <option value="${p.id}">
                ${p.name}
            </option>
        `;

    });

});

// ====================
// LOAD CITY
// ====================

document.getElementById('province').addEventListener('change', function () {

    const id = this.value;

    const cityEl = document.getElementById('city');

    document.getElementById('province_name').value =
        this.options[this.selectedIndex].text;

    fetch('/api/ongkir/cities?province_id=' + id)
        .then(r => r.json())
        .then(res => {

            cityEl.innerHTML = '<option value="">-- Pilih Kota --</option>';

            res.data.forEach(c => {

                cityEl.innerHTML += `
                    <option value="${c.id}">
                        ${c.name}
                    </option>
                `;

            });

            cityEl.disabled = false;

        });

});



// ====================
// LOAD DISTRICT
// ====================

document.getElementById('city').addEventListener('change', function () {

    const cityId = this.value;

    const districtEl = document.getElementById('district');

    document.getElementById('city_name').value =
        this.options[this.selectedIndex].text;

    fetch('/api/ongkir/districts?city_id=' + cityId)

    .then(r => r.json())

    .then(res => {

        districtEl.innerHTML =
            '<option value="">-- Pilih Kecamatan --</option>';

        res.data.forEach(d => {

            districtEl.innerHTML += `
                <option value="${d.id}">
                    ${d.name}
                </option>
            `;

        });

        districtEl.disabled = false;

    });

});

    // ====================
    // LOAD VILLAGE
    // ====================

    document.getElementById('district').addEventListener('change', function () {

        const id = this.value;
        const village = document.getElementById('village');

        // reset dulu
        village.innerHTML = '<option value="">Loading...</option>';
        village.disabled = true;

        fetch('/api/ongkir/villages?district_id=' + id)
            .then(res => res.json())
            .then(res => {

                console.log(res);

                village.innerHTML = '<option value="">-- Pilih Desa/Kelurahan --</option>';

                // kalau datanya ada di res.data
                const data = res.data ?? res.body?.data ?? [];

                if (data.length === 0) {
                    village.innerHTML = '<option value="">Data desa tidak ditemukan</option>';
                    village.disabled = true;
                    return;
                }

                data.forEach(v => {
                    village.innerHTML += `
                        <option value="${v.id}">
                            ${v.name}
                        </option>
                    `;
                });

                village.disabled = false;
            })
            .catch(err => {
                console.error(err);
                village.innerHTML = '<option value="">Gagal memuat desa</option>';
                village.disabled = true;
            });

    });

function hitungOngkir(){
    const district = document.getElementById('district').value;
    const courier = document.getElementById('courier').value;

    if(!district || !courier) return;

    // Tampilkan pesan loading selagi memuat data
    const container = document.getElementById('serviceContainer');
    const list = document.getElementById('serviceList');
    list.innerHTML = '<p class="text-sm text-gray-500 animate-pulse">Sedang memuat layanan pengiriman...</p>';
    container.classList.remove('hidden');

    fetch('/api/ongkir/cost',{
        method:'POST',
        headers:{
            'Content-Type':'application/json',
            'X-CSRF-TOKEN':csrfToken
        },
        body:JSON.stringify({
            destination: district,
            weight: totalWeight,
            courier: courier
        })
    })
    .then(r => r.json())
    .then(res => {
        // Ambil data kurir pertama (index 0) dari hasil response backend
        const courierData = res.data && res.data[0] ? res.data[0] : null;
        // Ambil array 'costs' yang berisi daftar layanan (REG, OKE, dll)
        const services = courierData && courierData.costs ? courierData.costs : [];

        list.innerHTML = '';

        if(services.length === 0){
            list.innerHTML = '<p class="text-red-500 text-sm">Tidak ada layanan pengiriman yang tersedia saat ini.</p>';
            document.getElementById('submitBtn').disabled = true;
            return;
        }

        services.forEach((s, index) => {
            const cost = s.cost[0].value;
            const etd = s.cost[0].etd ? `Estimasi ${s.cost[0].etd} Hari` : '';
            const note = s.cost[0].note ? `<span class="text-xs text-orange-500 block">*${s.cost[0].note}</span>` : '';

            list.innerHTML += `
            <label class="flex items-center gap-3 border border-gray-200 rounded-lg p-3 hover:bg-gray-50 cursor-pointer transition">
                <input
                    type="radio"
                    name="service_choice"
                    value="${s.service}"
                    data-cost="${cost}"
                    ${index === 0 ? 'checked' : ''}
                    onchange="selectService('${s.service}', ${cost})"
                    class="text-orange-500 focus:ring-orange-400">
                <div class="text-sm text-gray-700">
                    <b class="text-gray-900">${courier.toUpperCase()} ${s.service}</b>
                    <p class="text-gray-500 text-xs">${s.description}</p>
                    <p class="font-semibold text-orange-500 mt-1">${formatRp(cost)} <span class="text-gray-400 font-normal text-xs">(${etd})</span></p>
                    ${note}
                </div>
            </label>
            `;
        });

        // Set secara default ke layanan pertama yang muncul
        selectService(
            services[0].service,
            services[0].cost[0].value
        );
    })
    .catch(err => {
        console.error("Error fetching cost:", err);
        list.innerHTML = '<p class="text-red-500 text-sm">Terjadi gangguan koneksi, silakan coba lagi.</p>';
        document.getElementById('submitBtn').disabled = true;
    });
}

function selectService(service,cost){

    document.getElementById('courier_service').value=service;
    document.getElementById('shipping_cost').value=cost;

    document.getElementById('ongkirDisplay').innerHTML=formatRp(cost);

    document.getElementById('totalDisplay').innerHTML=formatRp(subtotal+cost);

    document.getElementById('submitBtn').disabled=false;

}

document.getElementById('village').addEventListener('change', hitungOngkir);

document.getElementById('courier').addEventListener('change', hitungOngkir);
</script>
@endpush