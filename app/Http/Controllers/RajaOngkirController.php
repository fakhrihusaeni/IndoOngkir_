<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RajaOngkirController extends Controller
{
    private string $base;
    private string $key;

    public function __construct()
    {
        $this->base = env('RAJAONGKIR_BASE_URL');
        $this->key  = env('RAJAONGKIR_API_KEY');
    }

    /**
     * ===========================
     * GET PROVINCES
     * ===========================
     */
    public function getProvinces()
    {
        try {

            $response = Http::withHeaders([
                'key' => $this->key
            ])->get($this->base . '/destination/province');

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengambil data provinsi',
                    'error'   => $response->body()
                ], $response->status());
            }

            return response()->json([
                'success' => true,
                'data' => $response->json()['data']
            ]);

        } catch (\Exception $e) {

            Log::error($e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ],500);

        }
    }

    /**
     * ===========================
     * GET CITY
     * ===========================
     */
    public function getCities(Request $request)
    {
        $request->validate([
            'province_id' => 'required'
        ]);

        try {

            $response = Http::withHeaders([
                'key' => $this->key
            ])->get(
                $this->base . '/destination/city/' . $request->province_id
            );

            if (!$response->successful()) {

                return response()->json([
                    'success'=>false,
                    'message'=>'Gagal mengambil kota'
                ],$response->status());

            }

            return response()->json([
                'success'=>true,
                'data'=>$response->json()['data']
            ]);

        } catch (\Exception $e){

            Log::error($e->getMessage());

            return response()->json([
                'success'=>false,
                'message'=>$e->getMessage()
            ],500);

        }

    }

    /**
     * ===========================
     * GET DISTRICT
     * ===========================
     */
    public function getDistricts(Request $request)
    {
        $request->validate([
            'city_id'=>'required'
        ]);

        try{

            $response = Http::withHeaders([
                'key'=>$this->key
            ])->get(
                $this->base.'/destination/district/'.$request->city_id
            );

            if(!$response->successful()){

                return response()->json([
                    'success'=>false,
                    'message'=>'Gagal mengambil kecamatan'
                ],$response->status());

            }

            return response()->json([
                'success'=>true,
                'data'=>$response->json()['data']
            ]);

        }catch(\Exception $e){

            Log::error($e->getMessage());

            return response()->json([
                'success'=>false,
                'message'=>$e->getMessage()
            ],500);

        }

    }

    /**
     * ===========================
     * HITUNG ONGKIR WITH FALLBACK
     * ===========================
     */
    public function calculateCost(Request $request)
    {
        $request->validate([
            'destination'=>'required',
            'weight'=>'required|numeric|min:1',
            'courier'=>'required'
        ]);

        // Siapkan struktur data fallback standar jika API bermasalah/tidak mengembalikan layanan
        $fallbackData = [
            [
                'code' => strtolower($request->courier),
                'name' => strtoupper($request->courier),
                'costs' => [
                    [
                        'service' => 'REG',
                        'description' => 'Layanan Reguler',
                        'cost' => [
                            [
                                'value' => 15000, // Nominal flat tarif fallback
                                'etd' => '2-4',
                                'note' => 'Sistem cadangan aktif'
                            ]
                        ]
                    ],
                    [
                        'service' => 'OKE',
                        'description' => 'Layanan Ekonomis',
                        'cost' => [
                            [
                                'value' => 10000,
                                'etd' => '4-7',
                                'note' => 'Sistem cadangan aktif'
                            ]
                        ]
                    ]
                ]
            ]
        ];

        try {
            $origin = 50198;

            $response = Http::withHeaders([
                'key'=>$this->key
            ])->post(
                $this->base.'/calculate/district/domestic-cost',
                [
                    'origin'=>$origin,
                    'destination'=>$request->destination,
                    'weight'=>$request->weight,
                    'courier'=>$request->courier
                ]
            );

            // JIKA API GAGAL / LIMIT ABIS (Tidak Berhasil)
            if (!$response->successful()) {
                Log::warning("RajaOngkir API gagal. Mengaktifkan fallback data. Error: " . $response->body());
                
                return response()->json([
                    'success' => true, 
                    'data' => $fallbackData,
                    'is_fallback' => true 
                ]);
            }

            $responseData = $response->json()['data'] ?? [];

            // JIKA API BERHASIL TAPI RESPONS DATA LAYANAN KOSONG
            if (empty($responseData)) {
                return response()->json([
                    'success' => true,
                    'data' => $fallbackData,
                    'is_fallback' => true
                ]);
            }

            // JIKA SEMUA BERJALAN NORMAL
            return response()->json([
                'success'=>true,
                'data'=>$responseData
            ]);

        } catch (\Exception $e) {
            Log::error("Exception pada RajaOngkir, mengaktifkan fallback: " . $e->getMessage());

            // JIKA TERJADI EXCEPTION (Misal Server RajaOngkir Down), TETAP KEMBALIKAN FALLBACK
            return response()->json([
                'success' => true,
                'data' => $fallbackData,
                'is_fallback' => true
            ]);
        }
    }
}