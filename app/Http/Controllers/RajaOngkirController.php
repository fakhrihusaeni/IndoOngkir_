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
        $response = Http::withHeaders(['key' => $this->apiKey])
            ->get("{$this->baseUrl}/province");

        if ($response->successful()) {
            $data = $response->json();
            return response()->json($data['rajaongkir']['results']);
        }

        return response()->json(['error' => 'Gagal mengambil data provinsi'], 500);
    }

    // Ambil daftar kota berdasarkan provinsi
    public function getCities(Request $request)
    {
        $request->validate(['province_id' => 'required|integer']);

        $response = Http::withHeaders(['key' => $this->apiKey])
            ->get("{$this->baseUrl}/city", ['province' => $request->province_id]);

        if ($response->successful()) {
            $data = $response->json();
            return response()->json($data['rajaongkir']['results']);
        }

        return response()->json(['error' => 'Gagal mengambil data kota'], 500);
    }

    // Hitung ongkos kirim
    public function calculateCost(Request $request)
    {
        $request->validate([
            'destination' => 'required|integer',   // city_id tujuan
            'weight'      => 'required|integer|min:1',
            'courier'     => 'required|in:jne,pos,tiki',
        ]);

        // Origin = Jakarta Pusat (city_id: 151 di RajaOngkir Starter)
        $response = Http::withHeaders(['key' => $this->apiKey])
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

        return response()->json(['error' => 'Gagal menghitung ongkos kirim'], 500);
    }
}