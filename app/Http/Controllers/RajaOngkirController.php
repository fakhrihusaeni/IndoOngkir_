<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RajaOngkirController extends Controller
{
    private string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey  = config('services.rajaongkir.api_key');
        $this->baseUrl = config('services.rajaongkir.base_url');
    }

    // Ambil daftar provinsi
    public function getProvinces()
    {
        try {
            $response = Http::timeout(5)->withHeaders(['key' => $this->apiKey])
                ->get("{$this->baseUrl}/province");

            if ($response->successful()) {
                $data = $response->json();
                return response()->json($data['rajaongkir']['results']);
            }
        } catch (\Exception $e) {
            // lanjut ke fallback di bawah
        }

        // Fallback data statis kalau API tidak bisa diakses
        return response()->json([
            ['province_id' => '6', 'province' => 'DKI Jakarta'],
            ['province_id' => '9', 'province' => 'Jawa Barat'],
            ['province_id' => '10', 'province' => 'Jawa Tengah'],
            ['province_id' => '11', 'province' => 'Jawa Timur'],
            ['province_id' => '5', 'province' => 'DI Yogyakarta'],
            ['province_id' => '1', 'province' => 'Bali'],
            ['province_id' => '3', 'province' => 'Banten'],
        ]);
    }

    public function getCities(Request $request)
    {
        $request->validate(['province_id' => 'required|integer']);

        try {
            $response = Http::timeout(5)->withHeaders(['key' => $this->apiKey])
                ->get("{$this->baseUrl}/city", ['province' => $request->province_id]);

            if ($response->successful()) {
                $data = $response->json();
                return response()->json($data['rajaongkir']['results']);
            }
        } catch (\Exception $e) {
            // lanjut ke fallback di bawah
        }

        $cities = [
            '6' => [['city_id' => '152', 'city_name' => 'Jakarta Pusat', 'type' => 'Kota'], ['city_id' => '153', 'city_name' => 'Jakarta Selatan', 'type' => 'Kota']],
            '9' => [['city_id' => '23', 'city_name' => 'Bandung', 'type' => 'Kota'], ['city_id' => '79', 'city_name' => 'Bekasi', 'type' => 'Kota']],
            '10' => [['city_id' => '399', 'city_name' => 'Semarang', 'type' => 'Kota'], ['city_id' => '256', 'city_name' => 'Solo', 'type' => 'Kota']],
            '11' => [['city_id' => '444', 'city_name' => 'Surabaya', 'type' => 'Kota'], ['city_id' => '281', 'city_name' => 'Malang', 'type' => 'Kota']],
            '5' => [['city_id' => '501', 'city_name' => 'Yogyakarta', 'type' => 'Kota']],
            '1' => [['city_id' => '17', 'city_name' => 'Denpasar', 'type' => 'Kota']],
            '3' => [['city_id' => '455', 'city_name' => 'Tangerang', 'type' => 'Kota']],
        ];

        return response()->json($cities[$request->province_id] ?? []);
    }

    // Hitung ongkos kirim
    public function calculateCost(Request $request)
    {
        $request->validate([
            'destination' => 'required|integer',
            'weight'      => 'required|integer|min:1',
            'courier'     => 'required|in:jne,pos,tiki',
        ]);

        try {
            $response = Http::timeout(5)->withHeaders(['key' => $this->apiKey])
                ->post("{$this->baseUrl}/cost", [
                    'origin'      => 151,
                    'destination' => $request->destination,
                    'weight'      => $request->weight,
                    'courier'     => $request->courier,
                ]);

            if ($response->successful()) {
                $data    = $response->json();
                $results = $data['rajaongkir']['results'][0]['costs'] ?? [];
                return response()->json($results);
            }
        } catch (\Exception $e) {
            // lanjut ke fallback di bawah
        }

        return response()->json([
            ['service' => 'REG', 'description' => 'Layanan Reguler', 'cost' => [['value' => 15000, 'etd' => '2-3']]],
            ['service' => 'YES', 'description' => 'Yakin Esok Sampai', 'cost' => [['value' => 25000, 'etd' => '1-1']]],
        ]);
    }
}