<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\RajaOngkirController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\PaymentController;

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Toko
Route::get('/', [ProductController::class, 'shopIndex'])->name('home');
Route::get('/produk/{product}', [ProductController::class, 'shopShow'])->name('shop.show');

// Admin
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('products', ProductController::class)->except(['show']);
    Route::get('/transaksi', [TransactionController::class, 'adminIndex'])->name('transactions.index');
    Route::patch('/transaksi/{transaction}/status', [TransactionController::class, 'updateStatus'])->name('transactions.updateStatus');
});

// Keranjang
Route::middleware('auth')->group(function () {
    Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');
    Route::post('/keranjang/tambah/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/keranjang/update/{cartItem}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/keranjang/hapus/{cartItem}', [CartController::class, 'remove'])->name('cart.remove');
    Route::get('/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
    // Transaksi
    Route::post('/transaksi', [TransactionController::class, 'store'])->name('transactions.store');
    Route::get('/transaksi', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transaksi/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');
    Route::get('/transaksi/{transaction}/invoice', [TransactionController::class, 'printInvoice'])->name('transactions.invoice');

});
// RajaOngkir API
Route::middleware('auth')->prefix('api/ongkir')->group(function () {
    Route::get('/provinces', [RajaOngkirController::class, 'getProvinces'])->name('ongkir.provinces');
    Route::get('/cities', [RajaOngkirController::class, 'getCities'])->name('ongkir.cities');
    Route::post('/cost', [RajaOngkirController::class, 'calculateCost'])->name('ongkir.cost');
});
  Route::middleware('auth')->group(function () {
    Route::get('/transaksi/{transaction}/bayar', [PaymentController::class, 'show'])->name('payment.show');
    Route::post('/transaksi/{transaction}/bayar', [PaymentController::class, 'process'])->name('payment.process');
    Route::get('/transaksi/{transaction}/cek-status', [PaymentController::class, 'checkStatus'])->name('payment.check');
});
Route::post('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');  
