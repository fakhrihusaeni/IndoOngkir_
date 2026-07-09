<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log; // Ditambahkan untuk mencatat error jika terjadi kegagalan API

class RajaOngkirController extends Controller
{
    protected $api_key;
    protected $base_url;

    public function __construct()
    {
        // Mengambil data dari config/services.php > array 'komerce'
        $this->api_key = config('services.komerce.api_key');
        $this->base_url = config('services.komerce.base_url');
    }

    public function getProvinces()
    {
        if (empty($this->api_key) || empty($this->base_url)) {
            return $this->fallbackProvinces();
        }

        try {
            $response = Http::timeout(5)
                ->withOptions(['verify' => false]) // Solusi cURL error 60 (SSL issue) di localhost
                ->withHeaders(['key' => $this->api_key])
                ->get("{$this->base_url}/province");

            if ($response->successful()) {
                $data = $response->json();
                return response()->json($data['rajaongkir']['results']);
            }

            // Jika respons tidak sukses (misal: 400 Bad Request / 401 Unauthorized dari RajaOngkir)
            Log::error('RajaOngkir API Province Error: ' . $response->body());

        } catch (\Exception $e) {
            // Mencatat error ke dalam file storage/logs/laravel.log agar bisa ditelusuri
            Log::error('RajaOngkir Exception: ' . $e->getMessage());
        }

        return $this->fallbackProvinces();
    }

    public function getCities(Request $request)
    {
        $request->validate(['province_id' => 'required|integer']);

        if (empty($this->api_key) || empty($this->base_url)) {
            return $this->fallbackCities($request->province_id);
        }

        try {
            $response = Http::timeout(5)
                ->withOptions(['verify' => false]) // Solusi cURL error 60 (SSL issue) di localhost
                ->withHeaders(['key' => $this->api_key])
                ->get("{$this->base_url}/city", ['province' => $request->province_id]);

            if ($response->successful()) {
                $data = $response->json();
                return response()->json($data['rajaongkir']['results']);
            }

            Log::error('RajaOngkir API City Error: ' . $response->body());

        } catch (\Exception $e) {
            Log::error('RajaOngkir City Exception: ' . $e->getMessage());
        }

        return $this->fallbackCities($request->province_id);
    }

    // Hitung ongkos kirim
    public function calculateCost(Request $request)
    {
        $request->validate([
            'destination' => 'required|integer',
            'weight'      => 'required|integer|min:1',
            'courier'     => 'required|in:jne,pos,tiki',
        ]);

        if (empty($this->api_key) || empty($this->base_url)) {
            return $this->fallbackCosts();
        }

        try {
            $response = Http::timeout(5)
                ->withOptions(['verify' => false]) // Solusi cURL error 60 (SSL issue) di localhost
                ->withHeaders(['key' => $this->api_key])
                ->post("{$this->base_url}/cost", [
                    'origin'      => 151, // Default Jakarta Barat sesuai fallback Anda
                    'destination' => $request->destination,
                    'weight'      => $request->weight,
                    'courier'     => $request->courier,
                ]);

            if ($response->successful()) {
                $data    = $response->json();
                $results = $data['rajaongkir']['results'][0]['costs'] ?? [];
                return response()->json($results);
            }

            Log::error('RajaOngkir API Cost Error: ' . $response->body());

        } catch (\Exception $e) {
            Log::error('RajaOngkir Cost Exception: ' . $e->getMessage());
        }

        return $this->fallbackCosts();
    }
    
    private function fallbackProvinces() 
    {
        return response()->json([
            ['province_id' => '6', 'province' => 'DKI Jakarta'],
            ['province_id' => '9', 'province' => 'Jawa Barat'],
            ['province_id' => '10', 'province' => 'Jawa Tengah'],
            ['province_id' => '11', 'province' => 'Jawa Timur'],
            ['province_id' => '5', 'province' => 'DI Yogyakarta'],
            ['province_id' => '1', 'province' => 'Bali'],
            ['province_id' => '3', 'province' => 'Banten'],
            ['province_id' => '34', 'province' => 'Sumatera Utara'],
            ['province_id' => '33', 'province' => 'Sumatera Selatan'],
            ['province_id' => '23', 'province' => 'Sulawesi Selatan'],
            ['province_id' => '15', 'province' => 'Kalimantan Timur'],
        ]);
    }

    private function fallbackCities($provinceId) 
    {
        $cities = [
            '6' => [
                ['city_id' => '151', 'city_name' => 'Jakarta Barat', 'type' => 'Kota'],
                ['city_id' => '152', 'city_name' => 'Jakarta Pusat', 'type' => 'Kota'], 
                ['city_id' => '153', 'city_name' => 'Jakarta Selatan', 'type' => 'Kota'],
                ['city_id' => '154', 'city_name' => 'Jakarta Timur', 'type' => 'Kota'],
                ['city_id' => '155', 'city_name' => 'Jakarta Utara', 'type' => 'Kota']
            ],
            '9' => [
                ['city_id' => '23', 'city_name' => 'Bandung', 'type' => 'Kota'], 
                ['city_id' => '79', 'city_name' => 'Bekasi', 'type' => 'Kota'],
                ['city_id' => '108', 'city_name' => 'Bogor', 'type' => 'Kota'],
                ['city_id' => '115', 'city_name' => 'Ciamis', 'type' => 'Kabupaten'],
                ['city_id' => '126', 'city_name' => 'Cirebon', 'type' => 'Kota'],
                ['city_id' => '149', 'city_name' => 'Garut', 'type' => 'Kabupaten'],
                ['city_id' => '211', 'city_name' => 'Karawang', 'type' => 'Kabupaten'],
                ['city_id' => '358', 'city_name' => 'Purwakarta', 'type' => 'Kabupaten'],
            ],
            '10' => [
                ['city_id' => '399', 'city_name' => 'Semarang', 'type' => 'Kota'], 
                ['city_id' => '256', 'city_name' => 'Solo (Surakarta)', 'type' => 'Kota'],
                ['city_id' => '37', 'city_name' => 'Banyumas', 'type' => 'Kabupaten'],
                ['city_id' => '92', 'city_name' => 'Blora', 'type' => 'Kabupaten'],
                ['city_id' => '249', 'city_name' => 'Magelang', 'type' => 'Kota'],
                ['city_id' => '344', 'city_name' => 'Pekalongan', 'type' => 'Kota']
            ],
            '11' => [
                ['city_id' => '444', 'city_name' => 'Surabaya', 'type' => 'Kota'], 
                ['city_id' => '281', 'city_name' => 'Malang', 'type' => 'Kota'],
                ['city_id' => '42', 'city_name' => 'Banyuwangi', 'type' => 'Kabupaten'],
                ['city_id' => '178', 'city_name' => 'Jember', 'type' => 'Kabupaten'],
                ['city_id' => '222', 'city_name' => 'Kediri', 'type' => 'Kota'],
                ['city_id' => '247', 'city_name' => 'Madiun', 'type' => 'Kota'],
                ['city_id' => '418', 'city_name' => 'Sidoarjo', 'type' => 'Kabupaten']
            ],
            '5' => [
                ['city_id' => '501', 'city_name' => 'Yogyakarta', 'type' => 'Kota'],
                ['city_id' => '39', 'city_name' => 'Bantul', 'type' => 'Kabupaten'],
                ['city_id' => '135', 'city_name' => 'Sleman', 'type' => 'Kabupaten'],
                ['city_id' => '210', 'city_name' => 'Kulon Progo', 'type' => 'Kabupaten'],
                ['city_id' => '165', 'city_name' => 'Gunung Kidul', 'type' => 'Kabupaten']
            ],
            '1' => [
                ['city_id' => '17', 'city_name' => 'Denpasar', 'type' => 'Kota'],
                ['city_id' => '32', 'city_name' => 'Badung', 'type' => 'Kabupaten'],
                ['city_id' => '145', 'city_name' => 'Gianyar', 'type' => 'Kabupaten'],
                ['city_id' => '447', 'city_name' => 'Tabanan', 'type' => 'Kabupaten'],
                ['city_id' => '94', 'city_name' => 'Buleleng', 'type' => 'Kabupaten']
            ],
            '3' => [
                ['city_id' => '455', 'city_name' => 'Tangerang', 'type' => 'Kota'],
                ['city_id' => '457', 'city_name' => 'Tangerang Selatan', 'type' => 'Kota'],
                ['city_id' => '331', 'city_name' => 'Pandeglang', 'type' => 'Kabupaten'],
                ['city_id' => '402', 'city_name' => 'Serang', 'type' => 'Kota'],
                ['city_id' => '106', 'city_name' => 'Cilegon', 'type' => 'Kota']
            ],
            '34' => [
                ['city_id' => '278', 'city_name' => 'Medan', 'type' => 'Kota'],
                ['city_id' => '102', 'city_name' => 'Binjai', 'type' => 'Kota'],
                ['city_id' => '121', 'city_name' => 'Deli Serdang', 'type' => 'Kabupaten'],
                ['city_id' => '341', 'city_name' => 'Pematang Siantar', 'type' => 'Kota'],
                ['city_id' => '461', 'city_name' => 'Tapanuli Utara', 'type' => 'Kabupaten']
            ],
            '33' => [
                ['city_id' => '327', 'city_name' => 'Palembang', 'type' => 'Kota'],
                ['city_id' => '241', 'city_name' => 'Lubuk Linggau', 'type' => 'Kota'],
                ['city_id' => '311', 'city_name' => 'Ogan Komering Ilir', 'type' => 'Kabupaten'],
                ['city_id' => '351', 'city_name' => 'Prabumulih', 'type' => 'Kota'],
                ['city_id' => '68', 'city_name' => 'Banyuasin', 'type' => 'Kabupaten']
            ],
            '23' => [
                ['city_id' => '246', 'city_name' => 'Makassar', 'type' => 'Kota'],
                ['city_id' => '334', 'city_name' => 'Pare-Pare', 'type' => 'Kota'],
                ['city_id' => '156', 'city_name' => 'Gowa', 'type' => 'Kabupaten'],
                ['city_id' => '91', 'city_name' => 'Bone', 'type' => 'Kabupaten'],
                ['city_id' => '321', 'city_name' => 'Palopo', 'type' => 'Kota']
            ],
            '15' => [
                ['city_id' => '386', 'city_name' => 'Samarinda', 'type' => 'Kota'],
                ['city_id' => '48', 'city_name' => 'Balikpapan', 'type' => 'Kota'],
                ['city_id' => '219', 'city_name' => 'Kutai Kartanegara', 'type' => 'Kabupaten'],
                ['city_id' => '111', 'city_name' => 'Bontang', 'type' => 'Kota']
            ],
        ];

        return response()->json($cities[$provinceId] ?? []);
    }

    private function fallbackCosts() 
    {
        return response()->json([
            ['service' => 'REG', 'description' => 'Layanan Reguler', 'cost' => [['value' => 15000, 'etd' => '2-3']]],
            ['service' => 'YES', 'description' => 'Yakin Esok Sampai', 'cost' => [['value' => 25000, 'etd' => '1-1']]],
        ]);
    }
}