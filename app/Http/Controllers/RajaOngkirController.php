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
     * HITUNG ONGKIR
     * ===========================
     */
    public function calculateCost(Request $request)
    {

        $request->validate([

            'destination'=>'required',

            'weight'=>'required|numeric|min:1',

            'courier'=>'required'

        ]);

        try{

            /**
             * ===================================================
             * GANTI ANGKA INI DENGAN ID KECAMATAN TOKO
             * ===================================================
             */
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

            if(!$response->successful()){

                return response()->json([

                    'success'=>false,

                    'message'=>'Gagal menghitung ongkir',

                    'error'=>$response->body()

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

}