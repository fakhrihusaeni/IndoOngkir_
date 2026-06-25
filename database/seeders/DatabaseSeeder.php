<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Buat akun admin
        User::create([
            'name'     => 'Admin IndoOngkir',
            'email'    => 'admin@indo-ongkir.test',
            'password' => Hash::make('password123'),
            'role'     => 'admin',
        ]);

        // Buat akun pembeli contoh
        User::create([
            'name'     => 'Budi Santoso',
            'email'    => 'budi@example.com',
            'password' => Hash::make('password123'),
            'role'     => 'pembeli',
        ]);

        // Produk contoh
        $products = [
            ['name' => 'Batik Tulis Solo', 'description' => 'Batik tulis premium dari pengrajin Solo', 'price' => 250000, 'stock' => 50, 'weight' => 300],
            ['name' => 'Keripik Tempe Malang', 'description' => 'Keripik tempe gurih, kemasan 200gr', 'price' => 25000, 'stock' => 100, 'weight' => 250],
            ['name' => 'Kopi Arabika Aceh', 'description' => 'Kopi arabika single origin dari Gayo, 250gr', 'price' => 85000, 'stock' => 75, 'weight' => 300],
            ['name' => 'Tas Rotan Bali', 'description' => 'Tas rotan handmade khas Bali', 'price' => 185000, 'stock' => 30, 'weight' => 500],
            ['name' => 'Sambal Bu Rudy', 'description' => 'Sambal terasi asli Surabaya, 250gr', 'price' => 35000, 'stock' => 200, 'weight' => 300],
            ['name' => 'Tenun Ikat NTT', 'description' => 'Kain tenun ikat tradisional NTT', 'price' => 450000, 'stock' => 20, 'weight' => 400],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}